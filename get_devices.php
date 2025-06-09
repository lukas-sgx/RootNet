<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

function get_all_devices($subnet) {
    $devices = [];
    $seen = [];
    $all_ips = [];
    // 1. Ping broadcast pour réveiller les appareils
    $broadcast = preg_replace('/\d+$/', '255', preg_replace('/\/(\d+)$/', '', $subnet));
    exec("ping -b -c 2 $broadcast > /dev/null 2>&1");
    // 2. fping pour détecter tous les hôtes actifs
    $fping = trim(shell_exec('which fping'));
    if ($fping) {
        $fping_out = [];
        exec("$fping -a -q -g $subnet 2>/dev/null", $fping_out);
        foreach ($fping_out as $ip) {
            $ip = trim($ip);
            if ($ip) {
                $all_ips[$ip] = 'fping';
                if (!isset($devices[$ip])) {
                    $devices[$ip] = [
                        'ip' => $ip,
                        'hostname' => null,
                        'mac' => null,
                        'vendor' => null,
                        'type' => '',
                        'action' => '',
                        'status' => 'online',
                        'source' => 'fping'
                    ];
                }
            }
        }
    }
    // 3. nmap pour enrichir (MAC, vendor, hostname)
    $output = [];
    exec("sudo nmap -sn -n -oG - $subnet 2>/dev/null", $output);
    $last_ip = null;
    foreach ($output as $line) {
        if (preg_match('/Host: ([0-9.]+) \(([^)]*)\).*Status: Up.*MAC: ([0-9A-F:]+) \(([^)]+)\)/i', $line, $matches)) {
            $ip = $matches[1];
            $hostname = $matches[2] !== '' ? $matches[2] : null;
            $mac = strtoupper($matches[3]);
            $vendor = $matches[4];
            $devices[$ip] = [
                'ip' => $ip,
                'hostname' => $hostname,
                'mac' => $mac,
                'vendor' => $vendor,
                'type' => $vendor,
                'action' => '',
                'status' => 'online',
                'source' => 'nmap'
            ];
            $seen[$ip] = true;
            $all_ips[$ip] = 'nmap';
            $last_ip = $ip;
        } elseif (preg_match('/Host: ([0-9.]+)\s+Status: Up/', $line, $matches)) {
            $ip = $matches[1];
            $devices[$ip] = [
                'ip' => $ip,
                'hostname' => null,
                'mac' => null,
                'vendor' => null,
                'type' => '',
                'action' => '',
                'status' => 'online',
                'source' => 'nmap'
            ];
            $seen[$ip] = true;
            $all_ips[$ip] = 'nmap';
            $last_ip = $ip;
        } elseif (preg_match('/Host: ([0-9.]+) \(([^)]*)\)\s+Status: Up/', $line, $matches)) {
            $ip = $matches[1];
            $hostname = $matches[2] !== '' ? $matches[2] : null;
            $devices[$ip] = [
                'ip' => $ip,
                'hostname' => $hostname,
                'mac' => null,
                'vendor' => null,
                'type' => '',
                'action' => '',
                'status' => 'online',
                'source' => 'nmap'
            ];
            $seen[$ip] = true;
            $all_ips[$ip] = 'nmap';
            $last_ip = $ip;
        } elseif (preg_match('/MAC: ([0-9A-F:]+) \(([^)]+)\)/i', $line, $matches) && $last_ip) {
            $mac = strtoupper($matches[1]);
            $vendor = $matches[2];
            $devices[$last_ip]['mac'] = $mac;
            $devices[$last_ip]['vendor'] = $vendor;
            $devices[$last_ip]['type'] = $vendor;
        }
    }
    // 4. Compléter avec la table ARP
    $arp = [];
    exec('arp -a', $arp);
    foreach ($arp as $line) {
        if (preg_match('/\(([^)]+)\) at ([0-9a-f:]+) /i', $line, $matches)) {
            $ip = $matches[1];
            $mac = strtoupper($matches[2]);
            $hostname = null;
            if (preg_match('/^([^ ]+) /', $line, $hostMatch) && $hostMatch[1] !== '?') {
                $hostname = $hostMatch[1];
            }
            if (isset($devices[$ip])) {
                if (!$devices[$ip]['mac']) $devices[$ip]['mac'] = $mac;
                if (!$devices[$ip]['hostname']) $devices[$ip]['hostname'] = $hostname;
                if (!isset($devices[$ip]['source']) || $devices[$ip]['source'] === 'fping') $devices[$ip]['source'] = 'arp';
            } else {
                $devices[$ip] = [
                    'ip' => $ip,
                    'hostname' => $hostname,
                    'mac' => $mac,
                    'vendor' => null,
                    'type' => '',
                    'action' => '',
                    'status' => 'online',
                    'source' => 'arp'
                ];
            }
            $seen[$ip] = true;
            $all_ips[$ip] = 'arp';
        }
    }
    // 5. Enrichissement DNS pour chaque IP trouvée
    foreach (array_keys($all_ips) as $ip) {
        if (!isset($devices[$ip])) {
            $devices[$ip] = [
                'ip' => $ip,
                'hostname' => null,
                'mac' => null,
                'vendor' => null,
                'type' => '',
                'action' => '',
                'status' => 'online',
                'source' => 'dns'
            ];
        }
        if (!$devices[$ip]['hostname']) {
            $host = gethostbyaddr($ip);
            if ($host && $host !== $ip) {
                $devices[$ip]['hostname'] = $host;
            }
        }
    }
    // Après avoir construit chaque $devices[$ip]...
    foreach ($devices as &$dev) {
        // Toujours calculer le type
        $dev['type'] = guess_device_type($dev['vendor'] ?? '', $dev['hostname'] ?? '', $dev['mac'] ?? '');
        // Toujours forcer la présence de la source
        if (!isset($dev['source']) || !$dev['source']) {
            if (!empty($dev['mac']) && !empty($dev['vendor'])) {
                $dev['source'] = 'nmap';
            } elseif (!empty($dev['mac'])) {
                $dev['source'] = 'arp';
            } elseif (!empty($dev['ip'])) {
                $dev['source'] = 'fping';
            } else {
                $dev['source'] = 'dns';
            }
        }
    }
    unset($dev);
    return array_values($devices);
}

function guess_device_type($vendor, $hostname, $mac) {
    $vendor = strtolower($vendor ?? '');
    $hostname = strtolower($hostname ?? '');
    $mac = strtolower($mac ?? '');
    if (strpos($vendor, 'apple') !== false || strpos($hostname, 'iphone') !== false || strpos($hostname, 'ipad') !== false) return 'phone/tablette Apple';
    if (strpos($vendor, 'samsung') !== false || strpos($hostname, 'android') !== false) return 'phone/tablette Android';
    if (strpos($vendor, 'intel') !== false || strpos($vendor, 'lenovo') !== false || strpos($vendor, 'dell') !== false || strpos($vendor, 'hp') !== false || strpos($vendor, 'asus') !== false || strpos($vendor, 'acer') !== false || strpos($vendor, 'msi') !== false || strpos($hostname, 'pc') !== false || strpos($hostname, 'laptop') !== false) return 'PC/Ordinateur';
    if (strpos($vendor, 'raspberry') !== false) return 'Raspberry Pi';
    if (strpos($vendor, 'printer') !== false || strpos($hostname, 'printer') !== false) return 'Imprimante';
    if (strpos($vendor, 'lg') !== false || strpos($vendor, 'sony') !== false || strpos($vendor, 'philips') !== false || strpos($hostname, 'tv') !== false) return 'TV';
    if (strpos($vendor, 'cisco') !== false || strpos($vendor, 'ubiquiti') !== false || strpos($vendor, 'tp-link') !== false || strpos($vendor, 'netgear') !== false) return 'Routeur/Switch';
    if (strpos($vendor, 'google') !== false && strpos($hostname, 'cast') !== false) return 'Google Cast';
    if (strpos($vendor, 'amazon') !== false && strpos($hostname, 'echo') !== false) return 'Amazon Echo';
    if (strpos($vendor, 'nintendo') !== false || strpos($vendor, 'playstation') !== false || (strpos($vendor, 'sony') !== false && strpos($hostname, 'ps') !== false)) return 'Console de jeu';
    if (strpos($vendor, 'camera') !== false || strpos($hostname, 'cam') !== false || strpos($hostname, 'ipcam') !== false) return 'Caméra IP';
    if (strpos($vendor, 'sonos') !== false || strpos($vendor, 'bose') !== false || strpos($hostname, 'speaker') !== false) return 'Enceinte connectée';
    if (strpos($vendor, 'yeelight') !== false || strpos($vendor, 'hue') !== false || strpos($vendor, 'tuya') !== false || strpos($hostname, 'light') !== false) return 'Éclairage connecté';
    if (strpos($vendor, 'synology') !== false || strpos($vendor, 'qnap') !== false || strpos($hostname, 'nas') !== false) return 'NAS';
    if (strpos($vendor, 'grandstream') !== false || strpos($vendor, 'yealink') !== false || strpos($hostname, 'voip') !== false) return 'Téléphone VoIP';
    if (strpos($vendor, 'somfy') !== false || strpos($vendor, 'legrand') !== false || strpos($vendor, 'delta dore') !== false) return 'Domotique';
    return '';
}

$subnet = null;
if (isset($_GET['subnet'])) {
    $subnet = $_GET['subnet'];
} else {
    // Détecte le subnet de wlan0
    $ifconfig = shell_exec("ip -4 addr show wlan0");
    if (preg_match('/inet ([0-9.]+)\/(\d+)/', $ifconfig, $matches)) {
        $ip = $matches[1];
        $mask = $matches[2];
        $subnet = preg_replace('/\d+$/', '0', $ip) . "/$mask";
    } else {
        $subnet = '192.168.86.0/24'; // fallback
    }
}
$devices = $subnet ? get_all_devices($subnet) : [];
echo json_encode(['devices' => $devices]);
