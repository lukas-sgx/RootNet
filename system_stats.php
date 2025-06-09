<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Cache-Control: no-cache, no-store, must-revalidate');

function get_cpu_temp() {
    $temp = 0;
    $count = 0;
    
    // Parcourir toutes les zones thermiques
    for ($i = 0; $i < 4; $i++) {
        $path = "/sys/class/thermal/thermal_zone{$i}/temp";
        if (file_exists($path)) {
            $current_temp = intval(file_get_contents($path));
            if ($current_temp > 0) {
                $temp += $current_temp;
                $count++;
            }
        }
    }
    
    return $count > 0 ? round($temp / $count / 1000, 1) : null;
}

function get_cpu_usage() {
    $prev = [];
    // Première lecture
    $cpu_info = shell_exec('cat /proc/stat');
    $lines = explode("\n", $cpu_info);
    foreach ($lines as $line) {
        if (strpos($line, 'cpu') !== 0) continue;
        $data = preg_split('/\s+/', trim($line));
        if (count($data) < 5) continue;
        $cpu = $data[0];
        $user = intval($data[1]);
        $nice = intval($data[2]);
        $system = intval($data[3]);
        $idle = intval($data[4]);
        $total = $user + $nice + $system + $idle;
        $prev[$cpu] = ['total' => $total, 'idle' => $idle];
    }
    // Attendre un peu
    usleep(100000);
    // Deuxième lecture
    $cpu_info = shell_exec('cat /proc/stat');
    $lines = explode("\n", $cpu_info);
    $cpus = [];
    $global = null;
    foreach ($lines as $line) {
        if (strpos($line, 'cpu') !== 0) continue;
        $data = preg_split('/\s+/', trim($line));
        if (count($data) < 5) continue;
        $cpu = $data[0];
        if (!isset($prev[$cpu])) continue;
        $user = intval($data[1]);
        $nice = intval($data[2]);
        $system = intval($data[3]);
        $idle = intval($data[4]);
        $total = $user + $nice + $system + $idle;
        $diff_total = $total - $prev[$cpu]['total'];
        $diff_idle = $idle - $prev[$cpu]['idle'];
        if ($diff_total == 0) {
            $usage = 0;
        } else {
            $usage = ($diff_total - $diff_idle) / $diff_total * 100;
        }
        if ($cpu === 'cpu') {
            $global = round($usage, 1);
        } else {
            $cpus[$cpu] = round($usage, 1);
        }
    }
    return ['global' => $global, 'cores' => $cpus];
}

function get_memory_info() {
    $free = shell_exec('free -m');
    if ($free === null) return null;
    
    $lines = explode("\n", $free);
    $memory = explode(" ", trim(preg_replace('/\s+/', ' ', $lines[1])));
    
    return [
        'total' => round($memory[1] / 1024, 1),
        'used' => round(($memory[1] - $memory[6]) / 1024, 1),
        'usage' => round((($memory[1] - $memory[6]) / $memory[1]) * 100, 1)
    ];
}

function get_network_speed() {
    $interfaces = ['wlan0', 'eth0', 'enp0s3', 'wlp2s0'];
    $active_interface = null;
    foreach ($interfaces as $iface) {
        if (
            file_exists("/sys/class/net/$iface/statistics/rx_bytes") &&
            file_exists("/sys/class/net/$iface/operstate") &&
            trim(file_get_contents("/sys/class/net/$iface/operstate")) === 'up'
        ) {
            $active_interface = $iface;
            break;
        }
    }
    if (!$active_interface) {
        return [
            'download' => 0,
            'upload' => 0,
            'iface' => null
        ];
    }
    $rx_initial = (float)file_get_contents("/sys/class/net/$active_interface/statistics/rx_bytes");
    $tx_initial = (float)file_get_contents("/sys/class/net/$active_interface/statistics/tx_bytes");
    usleep(500000); // 0.5s
    $rx_final = (float)file_get_contents("/sys/class/net/$active_interface/statistics/rx_bytes");
    $tx_final = (float)file_get_contents("/sys/class/net/$active_interface/statistics/tx_bytes");
    $rx_diff = $rx_final - $rx_initial;
    $tx_diff = $tx_final - $tx_initial;
    $rx_speed = $rx_diff > 0 ? ($rx_diff * 2 / 1024 / 1024) : 0;
    $tx_speed = $tx_diff > 0 ? ($tx_diff * 2 / 1024 / 1024) : 0;
    return [
        'download' => round($rx_speed, 4),
        'upload' => round($tx_speed, 4),
        'iface' => $active_interface,
        'rx_initial' => $rx_initial,
        'rx_final' => $rx_final,
        'tx_initial' => $tx_initial,
        'tx_final' => $tx_final
    ];
}

function get_network_config() {
    $iface = null;
    $interfaces = ['wlan0', 'eth0', 'enp0s3', 'wlp2s0'];
    foreach ($interfaces as $i) {
        if (
            file_exists("/sys/class/net/$i/statistics/rx_bytes") &&
            file_exists("/sys/class/net/$i/operstate") &&
            trim(file_get_contents("/sys/class/net/$i/operstate")) === 'up'
        ) {
            $iface = $i;
            break;
        }
    }
    $ip = null;
    $mask = null;
    $gateway = null;
    $dns = null;
    $ssid = null;
    $hostname = gethostname();
    if ($iface) {
        $ifconfig = shell_exec("ip -4 addr show $iface");
        if (preg_match('/inet ([0-9.]+)\/(\d+)/', $ifconfig, $matches)) {
            $ip = $matches[1];
            $mask = $matches[2];
        }
        $route = shell_exec('ip route show default');
        if (preg_match('/default via ([0-9.]+)/', $route, $matches)) {
            $gateway = $matches[1];
        }
        $resolv = @file_get_contents('/etc/resolv.conf');
        if ($resolv && preg_match('/nameserver ([0-9.]+)/', $resolv, $matches)) {
            $dns = $matches[1];
        }
        // Récupérer le SSID si interface WiFi
        if (strpos($iface, 'wl') === 0 || strpos($iface, 'wlan') === 0) {
            $iw = shell_exec("iwgetid -r");
            $ssid = trim($iw);
        }
    }
    return [
        'interface' => $iface,
        'ip' => $ip,
        'mask' => $mask,
        'gateway' => $gateway,
        'dns' => $dns,
        'hostname' => $hostname,
        'ssid' => $ssid
    ];
}

$cpu_data = get_cpu_usage();
$net = get_network_speed();
$netconf = get_network_config();
$data = [
    'cpu_temp' => get_cpu_temp(),
    'cpu_usage' => $cpu_data['global'],
    'cpu_cores' => $cpu_data['cores'],
    'memory' => get_memory_info(),
    'network' => [
        'download' => $net['download'],
        'upload' => $net['upload'],
        'iface' => $net['iface']
    ],
    'network_config' => $netconf,
    'timestamp' => time()
];

echo json_encode($data);