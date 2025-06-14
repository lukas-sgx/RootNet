<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RootNet - Administration Réseau</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            color: #333;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .header h1 {
            color: #4a5568;
            font-size: 2.5em;
            margin-bottom: 10px;
        }

        .status-bar {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        .status-item {
            display: flex;
            align-items: center;
            gap: 10px;
            background: #f7fafc;
            padding: 10px 15px;
            border-radius: 25px;
            border: 2px solid transparent;
            transition: all 0.3s ease;
        }

        .status-item.online {
            border-color: #48bb78;
            background: linear-gradient(135deg, #c6f6d5, #9ae6b4);
        }

        .status-item.offline {
            border-color: #f56565;
            background: linear-gradient(135deg, #fed7d7, #feb2b2);
        }

        .nav-tabs {
            display: flex;
            gap: 5px;
            margin-bottom: 20px;
            background: rgba(255, 255, 255, 0.1);
            padding: 5px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        .nav-tab {
            padding: 12px 24px;
            background: transparent;
            border: none;
            border-radius: 8px;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            font-size: 16px;
            font-weight: 500;
        }

        .nav-tab.active {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(20px);
            box-shadow: 0 4px 16px rgba(0, 0, 0, 0.1);
        }

        .nav-tab:hover:not(.active) {
            background: rgba(255, 255, 255, 0.1);
        }

        .tab-content {
            display: none;
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
        }

        .tab-content.active {
            display: block;
            animation: fadeIn 0.5s ease;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .card {
            background: #f8fafc;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #e2e8f0;
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card h3 {
            color: #2d3748;
            margin-bottom: 15px;
            font-size: 1.3em;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #4a5568;
        }

        .form-control {
            width: 100%;
            padding: 12px;
            border: 2px solid #e2e8f0;
            border-radius: 8px;
            font-size: 16px;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            outline: none;
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .btn {
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .btn-primary {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(102, 126, 234, 0.3);
        }

        .btn-success {
            background: linear-gradient(135deg, #48bb78, #38a169);
            color: white;
        }

        .btn-danger {
            background: linear-gradient(135deg, #f56565, #e53e3e);
            color: white;
        }

        .btn-warning {
            background: linear-gradient(135deg, #ed8936, #dd6b20);
            color: white;
        }

        .device-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }

        .device-table th,
        .device-table td {
            padding: 10px 8px;
            text-align: left;
        }

        .device-table th {
            background: #f7fafc;
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }

        .device-table tr:hover {
            background: #f0f4f8;
        }

        .device-table td {
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }

        .status-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            display: inline-block;
            margin-right: 8px;
        }

        .status-online { background: #48bb78; }
        .status-offline { background: #f56565; }

        .progress-bar {
            width: 100%;
            height: 20px;
            background: #e2e8f0;
            border-radius: 10px;
            overflow: hidden;
            margin: 10px 0;
        }

        .progress-fill {
            height: 100%;
            background: linear-gradient(90deg, #48bb78, #38a169);
            border-radius: 10px;
            transition: width 0.3s ease;
        }

        .alert {
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }

        .alert-success {
            background: #f0fff4;
            border-color: #48bb78;
            color: #22543d;
        }

        .alert-warning {
            background: #fffaf0;
            border-color: #ed8936;
            color: #7b341e;
        }

        .alert-danger {
            background: #fff5f5;
            border-color: #f56565;
            color: #742a2a;
        }

        .log-container {
            background: #1a202c;
            color: #e2e8f0;
            padding: 20px;
            border-radius: 8px;
            font-family: 'Courier New', monospace;
            max-height: 400px;
            overflow-y: auto;
            margin-top: 20px;
        }

        .log-entry {
            margin-bottom: 5px;
            padding: 5px;
            border-radius: 4px;
        }

        .log-info { background: rgba(49, 130, 206, 0.1); }
        .log-warning { background: rgba(237, 137, 54, 0.1); }
        .log-error { background: rgba(245, 101, 101, 0.1); }

        @media (max-width: 768px) {
            .container { padding: 10px; }
            .header h1 { font-size: 2em; }
            .status-bar { flex-direction: column; }
            .nav-tabs { flex-wrap: wrap; }
            .grid { grid-template-columns: 1fr; }
        }

        /* Styles pour le modal */
        .modal {
            display: none;
            position: fixed;
            left: 0;
            top: 0;
            width: 100vw;
            height: 100vh;
            background: rgba(0, 0, 0, 0.3);
            z-index: 1000;
            align-items: center;
            justify-content: center;
        }

        #device-modal .modal-content {
  max-width: 370px;
  width: 95vw;
  margin: auto;
  border-radius: 18px;
  background: #f9fafb;
  box-shadow: 0 8px 32px #0002;
  padding: 32px 24px 24px 24px;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: stretch;
}

#device-modal .modal-title {
  text-align: center;
  font-size: 1.25em;
  font-weight: 700;
  margin-bottom: 18px;
  color: #2d3748;
  letter-spacing: 0.5px;
}

#device-modal .modal-body {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 18px;
}

#device-modal .modal-body button {
  width: 100%;
  border-radius: 12px;
  font-size: 1em;
  font-weight: 600;
  letter-spacing: 0.5px;
  border: none;
  padding: 13px 0;
  transition: background 0.18s, color 0.18s;
  margin-bottom: 0;
}

#device-modal .modal-body .btn-primary { background: #6c47c7; color: #fff; }
#device-modal .modal-body .btn-primary:hover { background: #4b2997; }
#device-modal .modal-body .btn-warning { background: #f2994a; color: #fff; }
#device-modal .modal-body .btn-warning:hover { background: #c97a2b; }
#device-modal .modal-body .btn-danger { background: #eb5757; color: #fff; }
#device-modal .modal-body .btn-danger:hover { background: #b92d2d; }
#device-modal .modal-body .btn-success { background: #27ae60; color: #fff; }
#device-modal .modal-body .btn-success:hover { background: #168f4e; }
#device-modal .modal-body .btn-secondary { background: #e2e8f0; color: #222; }
#device-modal .modal-body .btn-secondary:hover { background: #cbd5e1; }

        .close-modal {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 22px;
            cursor: pointer;
            color: #888;
            background: none;
            border: none;
            transition: color 0.18s;
        }

        .close-modal:hover {
            color: #e53e3e;
        }

        #device-modal .modal-content .btn.btn-secondary.close-modal {
  position: static;
  float: left;
  margin-right: 10px;
  font-size: 1.1em;
  font-weight: 700;
  background: #fff;
  color: #222;
  border-radius: 10px;
  border: 2px solid #e2e8f0;
  padding: 8px 18px;
  box-shadow: none;
}

#device-modal .modal-content .btn.btn-secondary.close-modal:hover {
  background: #f0f4f8;
  color: #b92d2d;
}

#device-modal .modal-content .btn.modal-confirm {
  font-size: 1.1em;
  font-weight: 700;
  border-radius: 10px;
  padding: 8px 18px;
  background: #eb5757;
  color: #fff;
  border: none;
  margin-left: 10px;
}

#device-modal .modal-content .btn.modal-confirm:hover {
  background: #b92d2d;
}

.device-table th, .device-table td {
  padding: 10px 8px;
  text-align: left;
}

.device-table th {
  background: #f7fafc;
  font-size: 15px;
  font-weight: 600;
  letter-spacing: 0.5px;
}

.device-table tr:hover {
  background: #f0f4f8;
}

.btn-action-menu {
  background: none;
  border: none;
  color: #4a5568;
  font-size: 22px;
  cursor: pointer;
  border-radius: 50%;
  width: 36px;
  height: 36px;
  display: flex;
  align-items: center;
  justify-content: center;
  transition: background 0.2s, color 0.2s;
  margin: 0 auto;
}

.btn-action-menu:hover {
  background: #e2e8f0;
  color: #6c47c7;
  box-shadow: 0 2px 8px #6c47c71a;
}

/* Styles pour le modal */
.modal {
  display: none;
  position: fixed;
  left: 0;
  top: 0;
  width: 100vw;
  height: 100vh;
  background: rgba(0, 0, 0, 0.25);
  z-index: 1000;
  align-items: center;
  justify-content: center;
}

#device-modal.active { display: flex; }

#device-modal .modal-content {
  max-width: 370px;
  width: 95vw;
  margin: auto;
  border-radius: 18px;
  background: #f9fafb;
  box-shadow: 0 8px 32px #0002;
  padding: 32px 24px 24px 24px;
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: stretch;
}

#device-modal .modal-title {
  text-align: center;
  font-size: 1.25em;
  font-weight: 700;
  margin-bottom: 18px;
  color: #2d3748;
  letter-spacing: 0.5px;
}

#device-modal .modal-body {
  display: flex;
  flex-direction: column;
  gap: 12px;
  margin-bottom: 18px;
}

#device-modal .modal-body button {
  width: 100%;
  border-radius: 12px;
  font-size: 1em;
  font-weight: 600;
  letter-spacing: 0.5px;
  border: none;
  padding: 13px 0;
  transition: background 0.18s, color 0.18s;
  margin-bottom: 0;
}

#device-modal .modal-body .btn-primary { background: #6c47c7; color: #fff; }
#device-modal .modal-body .btn-primary:hover { background: #4b2997; }
#device-modal .modal-body .btn-warning { background: #f2994a; color: #fff; }
#device-modal .modal-body .btn-warning:hover { background: #c97a2b; }
#device-modal .modal-body .btn-danger { background: #eb5757; color: #fff; }
#device-modal .modal-body .btn-danger:hover { background: #b92d2d; }
#device-modal .modal-body .btn-success { background: #27ae60; color: #fff; }
#device-modal .modal-body .btn-success:hover { background: #168f4e; }
#device-modal .modal-body .btn-secondary { background: #e2e8f0; color: #222; }
#device-modal .modal-body .btn-secondary:hover { background: #cbd5e1; }

        .close-modal {
            position: absolute;
            right: 15px;
            top: 10px;
            font-size: 22px;
            cursor: pointer;
            color: #888;
            background: none;
            border: none;
            transition: color 0.18s;
        }

        .close-modal:hover {
            color: #e53e3e;
        }

        #device-modal .modal-content .btn.btn-secondary.close-modal {
  position: static;
  float: left;
  margin-right: 10px;
  font-size: 1.1em;
  font-weight: 700;
  background: #fff;
  color: #222;
  border-radius: 10px;
  border: 2px solid #e2e8f0;
  padding: 8px 18px;
  box-shadow: none;
}

#device-modal .modal-content .btn.btn-secondary.close-modal:hover {
  background: #f0f4f8;
  color: #b92d2d;
}

#device-modal .modal-content .btn.modal-confirm {
  font-size: 1.1em;
  font-weight: 700;
  border-radius: 10px;
  padding: 8px 18px;
  background: #eb5757;
  color: #fff;
  border: none;
  margin-left: 10px;
}

#device-modal .modal-content .btn.modal-confirm:hover {
  background: #b92d2d;
}
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1>🌐 RootNet Admin Panel</h1>
            <div class="status-bar">
                <div class="status-item online">
                    <span class="status-indicator status-online"></span>
                    <span>Routeur Principal: En ligne</span>
                </div>
                <div class="status-item online">
                    <span class="status-indicator status-online"></span>
                    <span>Switch Core: Actif</span>
                </div>
                <div class="status-item offline">
                    <span class="status-indicator status-offline"></span>
                    <span>Backup WAN: Déconnecté</span>
                </div>
                <div class="status-item online">
                    <span class="status-indicator status-online"></span>
                    <span>WiFi: 24 clients</span>
                </div>
            </div>
        </div>

        <div class="nav-tabs">
            <button class="nav-tab active" onclick="showTab('dashboard')">📊 Tableau de bord</button>
            <button class="nav-tab" onclick="showTab('network')">🌐 Configuration Réseau</button>
            <button class="nav-tab" onclick="showTab('devices')">📱 Appareils</button>
            <button class="nav-tab" onclick="showTab('monitoring')">📈 Monitoring</button>
            <button class="nav-tab" onclick="showTab('logs')">📋 Logs</button>
        </div>

        <!-- Dashboard Tab -->
        <div id="dashboard" class="tab-content active">
            <div class="alert alert-success">
                ✅ Système fonctionnel - Dernière vérification: il y a 2 minutes
            </div>
            
            <div class="grid">
                <div class="card bandwidth-card">
                    <h3>📊 Utilisation Bande Passante</h3>
                    <p class="download-speed">Téléchargement: 0 MB/s</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 0%"></div>
                    </div>
                    <p class="upload-speed">Upload: 0 MB/s</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 0%"></div>
                    </div>
                </div>

                <div class="card memory-card">
                    <h3>💾 Utilisation Mémoire</h3>
                    <p class="ram-usage">RAM: 0 GB / 0 GB</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 0%"></div>
                    </div>
                    <p class="flash-usage">Flash: 0 MB / 0 GB</p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 0%"></div>
                    </div>
                </div>

                <div class="card cpu-card">
                    <h3>🌡️ Température CPU</h3>
                    <p><strong class="cpu-temp">0°C (0% CPU)</strong></p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 0%"></div>
                    </div>
                    <p class="cpu-status">Température optimale</p>
                </div>
            </div>
        </div>

        <!-- Network Configuration Tab -->
        <div id="network" class="tab-content">
            <div class="grid" style="justify-content: center;">
                <div class="card" style="max-width: 500px; margin: auto; background: #fff; box-shadow: 0 4px 24px rgba(102,126,234,0.08); border: 1.5px solid #e2e8f0;">
                    <h3 style="margin-bottom: 25px; text-align:center; color:#4a5568;">📡 Configuration LAN</h3>
                    <table style="width:100%;  border-spacing:0 10px;">
                        <tr>
                            <td style="font-weight:600; color:#4a5568; padding:6px 0;">Interface active</td>
                            <td style="text-align:right;"><span class="lan-iface" style="font-weight:bold; color:#2d3748;"></span></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:#4a5568; padding:6px 0;">Adresse IP LAN</td>
                            <td style="text-align:right;"><span class="lan-ip" style="font-weight:bold; color:#2d3748;"></span></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:#4a5568; padding:6px 0;">Masque de sous-réseau</td>
                            <td style="text-align:right;"><span class="lan-mask" style="font-weight:bold; color:#2d3748;"></span></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:#4a5568; padding:6px 0;">Passerelle</td>
                            <td style="text-align:right;"><span class="lan-gateway" style="font-weight:bold; color:#2d3748;"></span></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:#4a5568; padding:6px 0;">DNS</td>
                            <td style="text-align:right;"><span class="lan-dns" style="font-weight:bold; color:#2d3748;"></span></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:#4a5568; padding:6px 0;">Nom d'hôte</td>
                            <td style="text-align:right;"><span class="lan-hostname" style="font-weight:bold; color:#2d3748;"></span></td>
                        </tr>
                        <tr>
                            <td style="font-weight:600; color:#4a5568; padding:6px 0;">Nom du WiFi (SSID)</td>
                            <td style="text-align:right;"><span class="lan-ssid" style="font-weight:bold; color:#2d3748;"></span></td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Devices Tab -->
        <div id="devices" class="tab-content">
            <div class="card">
                <h3>📱 Appareils connectés</h3>
                <table class="device-table" style="width:100%; border-spacing:0 8px; background:#fff;">
                    <thead>
                        <tr>
                            <th>Statut</th>
                            <th>Nom</th>
                            <th>Adresse IP</th>
                            <th>Adresse MAC</th>
                            <th>Type</th>
                            <th style="text-align:center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>

        <!-- Monitoring Tab -->
        <div id="monitoring" class="tab-content">
            <div class="grid">
                <div class="card">
                    <h3>📈 Trafic réseau (Temps réel)</h3>
                    <p>Trafic entrant: <strong>1.2 MB/s</strong></p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 60%"></div>
                    </div>
                    <p>Trafic sortant: <strong>0.8 MB/s</strong></p>
                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 40%"></div>
                    </div>
                </div>

                <div class="card">
                    <h3>🔍 Surveillance des ports</h3>
                    <p>Port 80 (HTTP): ✅ Ouvert</p>
                    <p>Port 443 (HTTPS): ✅ Ouvert</p>
                    <p>Port 22 (SSH): ❌ Fermé</p>
                    <p>Port 21 (FTP): ❌ Fermé</p>
                    <button class="btn btn-primary">Scanner les ports</button>
                </div>

                <div class="card">
                    <h3>⚡ Performance</h3>
                    <p>Latence moyenne: <strong>2ms</strong></p>
                    <p>Perte de paquets: <strong>0.01%</strong></p>
                    <p>Débit maximum: <strong>100 Mbps</strong></p>
                    <button class="btn btn-primary">Test de vitesse</button>
                </div>

                <div class="card">
                    <h3>🎯 Top connexions</h3>
                    <p>1. 192.168.0.105 - 45.2 MB</p>
                    <p>2. 192.168.0.110 - 32.1 MB</p>
                    <p>3. 192.168.0.120 - 28.7 MB</p>
                    <p>4. 192.168.0.115 - 15.3 MB</p>
                </div>
            </div>
        </div>

        <!-- Logs Tab -->
        <div id="logs" class="tab-content">
            <div class="card">
                <h3>📋 Journaux système</h3>
                <div style="display: flex; gap: 10px; margin-bottom: 20px;">
                    <button class="btn btn-primary">Actualiser</button>
                    <button class="btn btn-warning">Vider les logs</button>
                    <button class="btn btn-success">Exporter</button>
                </div>
                
                <div class="log-container">
                    <div class="log-entry log-info">
                        [2025-06-09 14:32:15] INFO: Connexion utilisateur depuis 192.168.0.105
                    </div>
                    <div class="log-entry log-warning">
                        [2025-06-09 14:30:42] WARN: Tentative de connexion échouée depuis 203.142.75.22
                    </div>
                    <div class="log-entry log-info">
                        [2025-06-09 14:28:33] INFO: DHCP: Nouvelle attribution IP 192.168.0.125 pour MAC bb:cc:dd:ee:ff:05
                    </div>
                    <div class="log-entry log-error">
                        [2025-06-09 14:25:18] ERROR: Interface WAN2 déconnectée
                    </div>
                    <div class="log-entry log-info">
                        [2025-06-09 14:22:07] INFO: Mise à jour firmware terminée avec succès
                    </div>
                    <div class="log-entry log-warning">
                        [2025-06-09 14:20:33] WARN: Utilisation CPU élevée: 85%
                    </div>
                    <div class="log-entry log-info">
                        [2025-06-09 14:18:45] INFO: Sauvegarde configuration automatique effectuée
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal générique -->
        <div id="device-modal" class="modal" style="display:none; position:fixed; left:0; top:0; width:100vw; height:100vh; background:rgba(0,0,0,0.3); z-index:1000; align-items:center; justify-content:center;">
          <div class="modal-content" style="background:#fff; padding:30px; border-radius:8px; min-width:300px; max-width:90vw; box-shadow:0 8px 32px #0002; position:relative;">
            <span class="close-modal" style="position:absolute; right:15px; top:10px; font-size:22px; cursor:pointer;">&times;</span>
            <h4 class="modal-title"></h4>
            <div class="modal-body" style="margin:20px 0;"></div>
            <div style="text-align:right;">
              <button class="btn btn-secondary close-modal">Annuler</button>
              <button class="btn btn-danger modal-confirm">Confirmer</button>
            </div>
          </div>
        </div>
    </div>
<script>
    // Fonction pour afficher un onglet donné, en gérant les classes actives
    function showTab(tabName, event) {
        // Cacher tous les contenus de tab
        const tabContents = document.querySelectorAll('.tab-content');
        tabContents.forEach(content => {
            content.classList.remove('active');
        });

        // Retirer la classe active de tous les onglets de navigation
        const navTabs = document.querySelectorAll('.nav-tab');
        navTabs.forEach(tab => {
            tab.classList.remove('active');
        });

        // Afficher le contenu de l'onglet sélectionné
        const selectedTabContent = document.getElementById(tabName);
        if (selectedTabContent) {
            selectedTabContent.classList.add('active');
        }

        // Ajouter la classe active à l'onglet cliqué (si event présent)
        if (event && event.target) {
            event.target.classList.add('active');
        }

        // Charger dynamiquement les appareils connectés si l'onglet 'devices' est sélectionné
        if (tabName === 'devices') {
            updateDevicesTable();
        }
    }

    // Fonction pour récupérer et mettre à jour les stats système
    async function updateSystemStats() {
        const alertElement = document.querySelector('.alert');
        
        try {
            const response = await fetch('system_stats.php', {
                method: 'GET',
                headers: {
                    'Accept': 'application/json',
                    'Cache-Control': 'no-cache, no-store, must-revalidate'
                },
                cache: 'no-store'
            });
            
            if (!response.ok) {
                throw new Error(`Erreur HTTP: ${response.status}`);
            }
            
            const data = await response.json();

            // Vérification des données reçues
            if (!data) {
                throw new Error('Aucune donnée reçue');
            }

            // Mise à jour de l'alerte en succès
            alertElement.className = 'alert alert-success';
            alertElement.innerHTML = '✅ Système fonctionnel - Dernière vérification: il y a quelques secondes';

            // Mise à jour de la bande passante
            const dashboard = document.getElementById('dashboard');
            if (dashboard) {
                // Sélecteurs pour la bande passante
                const downloadElement = dashboard.querySelector('.bandwidth-card .download-speed');
                const uploadElement = dashboard.querySelector('.bandwidth-card .upload-speed');
                const downloadBar = dashboard.querySelector('.bandwidth-card .progress-fill:first-of-type');
                const uploadBar = dashboard.querySelector('.bandwidth-card .progress-fill:last-of-type');

                if (downloadElement && uploadElement && downloadBar && uploadBar) {
                    const downloadSpeed = parseFloat(data.network.download).toFixed(1);
                    const uploadSpeed = parseFloat(data.network.upload).toFixed(1);
                    downloadElement.textContent = `Téléchargement: ${downloadSpeed} MB/s`;
                    uploadElement.textContent = `Upload: ${uploadSpeed} MB/s`;

                    const downloadPercent = Math.min(parseFloat(data.network.download) * 10, 100);
                    const uploadPercent = Math.min(parseFloat(data.network.upload) * 10, 100);
                    downloadBar.style.width = `${downloadPercent}%`;
                    uploadBar.style.width = `${uploadPercent}%`;
                }

                // Mise à jour de la température CPU et utilisation
                const cpuTempElement = dashboard.querySelector('.cpu-card .cpu-temp');
                const cpuStatusText = dashboard.querySelector('.cpu-card .cpu-status');
                const cpuBar = dashboard.querySelector('.cpu-card .progress-fill');

                if (cpuTempElement && cpuBar && cpuStatusText) {
                    const cpuTemp = parseFloat(data.cpu_temp);
                    const cpuUsage = parseFloat(data.cpu_usage);

                    cpuTempElement.textContent = `${cpuTemp}°C (${cpuUsage}% CPU)`;
                    cpuBar.style.width = `${cpuUsage}%`;

                    if (cpuTemp > 65) {
                        cpuBar.style.background = 'linear-gradient(90deg, #f56565, #e53e3e)';
                        cpuStatusText.textContent = 'Température élevée';
                    } else if (cpuTemp > 50) {
                        cpuBar.style.background = 'linear-gradient(90deg, #ed8936, #dd6b20)';
                        cpuStatusText.textContent = 'Température normale';
                    } else {
                        cpuBar.style.background = 'linear-gradient(90deg, #48bb78, #38a169)';
                        cpuStatusText.textContent = 'Température optimale';
                    }
                }

                // Mise à jour de l'utilisation de la RAM
                const ramTextElement = dashboard.querySelector('.memory-card .ram-usage');
                const ramBar = dashboard.querySelector('.memory-card .progress-fill:first-of-type');

                if (ramBar && ramTextElement) {
                    ramBar.style.width = `${data.memory.usage}%`;
                    ramTextElement.textContent = `RAM: ${data.memory.used} GB / ${data.memory.total} GB`;
                }
            }

            // Mise à jour de la configuration réseau
            const netconf = data.network_config;
            window.lastNetconf = netconf;
            if (netconf) {
                const ifaceSpan = document.querySelector('.lan-iface');
                if (ifaceSpan) ifaceSpan.textContent = netconf.interface || '';
                const lanIpSpan = document.querySelector('.lan-ip');
                if (lanIpSpan) lanIpSpan.textContent = netconf.ip || '';
                const maskSpan = document.querySelector('.lan-mask');
                if (maskSpan) maskSpan.textContent = netconf.mask || '';
                const gwSpan = document.querySelector('.lan-gateway');
                if (gwSpan) gwSpan.textContent = netconf.gateway || '';
                const dnsSpan = document.querySelector('.lan-dns');
                if (dnsSpan) dnsSpan.textContent = netconf.dns || '';
                const hostnameSpan = document.querySelector('.lan-hostname');
                if (hostnameSpan) hostnameSpan.textContent = netconf.hostname || '';
                const ssidSpan = document.querySelector('.lan-ssid');
                if (ssidSpan) ssidSpan.textContent = netconf.ssid || '';
            }

        } catch (error) {
            console.error('Erreur:', error);
            alertElement.className = 'alert alert-danger';
            alertElement.innerHTML = `❌ Erreur de connexion au serveur - ${error.message}`;
        }
    }

    // Fonction pour charger dynamiquement les appareils connectés
    async function updateDevicesTable() {
        const tbody = document.querySelector('#devices .device-table tbody');
        try {
            const response = await fetch('get_devices.php');
            if (!response.ok) return;
            const data = await response.json();
            const devices = data.devices || [];
            let html = '';
            devices.forEach(device => {
                html += `<tr>`;
                html += `<td style='text-align:center;'><span class="status-indicator status-online"></span>En ligne</td>`;
                html += `<td style='font-weight:bold;'>${device.hostname ? device.hostname : ''}</td>`;
                html += `<td style='font-family:monospace;'>${device.ip || ''}</td>`;
                html += `<td style='font-family:monospace;'>${device.mac || ''}</td>`;
                html += `<td>${device.type || ''}</td>`;
                html += `<td style='text-align:center;'>`;
                html += `<button class="btn-action-menu" data-ip="${device.ip}" data-mac="${device.mac}" title="Actions"><span style='font-size:22px;'>⋮</span></button>`;
                html += `</td>`;
                html += `</tr>`;
            });
            tbody.innerHTML = html;
            setTimeout(() => {
              document.querySelectorAll('.btn-action-menu').forEach(btn => {
                btn.onclick = function(e) {
                  e.preventDefault();
                  const ip = this.getAttribute('data-ip');
                  const mac = this.getAttribute('data-mac');
                  showDeviceActionModal(ip, mac);
                };
              });
            }, 10);
        } catch (e) {
            tbody.innerHTML = '<tr><td colspan="6">Erreur lors du scan réseau</td></tr>';
        }
    }

    // Nouveau modal d'action
    function showDeviceActionModal(ip, mac) {
    const modal = document.getElementById('device-modal');
    const title = modal.querySelector('.modal-title');
    const body = modal.querySelector('.modal-body');
    const confirmBtn = modal.querySelector('.modal-confirm');
    title.innerHTML = `<span style='font-size:1.1em;'>Actions pour <span style='color:#5a4ee6'>${ip}</span></span>`;
    body.innerHTML = `
      <button class='btn btn-primary' onclick=\"confirmDeviceAction('scan','${ip}','${mac}')\"><span>🔎</span>Scan ports</button>
      <button class='btn btn-warning' onclick=\"confirmDeviceAction('arp','${ip}','${mac}')\"><span>🛡️</span>ARP spoof</button>
      <button class='btn btn-danger' onclick=\"confirmDeviceAction('deauth','${ip}','${mac}')\"><span>🚫</span>Deauth WiFi</button>
      <button class='btn btn-success' onclick=\"confirmDeviceAction('wol','${ip}','${mac}')\"><span>⚡</span>Wake-on-LAN</button>
      <button class='btn btn-secondary' onclick=\"confirmDeviceAction('ping','${ip}','${mac}')\"><span>📶</span>Ping</button>
    `;
    confirmBtn.style.display = 'none';
    modal.classList.add('active');
    modal.style.display = 'flex';
    modal.querySelectorAll('.close-modal').forEach(btn => btn.onclick = () => { modal.classList.remove('active'); modal.style.display = 'none'; });
}

    // Confirmation d'action
    function confirmDeviceAction(action, ip, mac) {
    const modal = document.getElementById('device-modal');
    const title = modal.querySelector('.modal-title');
    const body = modal.querySelector('.modal-body');
    const confirmBtn = modal.querySelector('.modal-confirm');
    let actionText = '';
    if (action === 'scan') actionText = `Scanner les ports de <b>${ip}</b> ?`;
    if (action === 'arp') actionText = `Lancer un ARP spoof sur <b>${ip}</b> ?`;
    if (action === 'deauth') actionText = `Déconnecter (deauth) <b>${mac || ip}</b> du WiFi ?`;
    if (action === 'wol') actionText = `Envoyer un Wake-on-LAN à <b>${mac || ip}</b> ?`;
    if (action === 'ping') actionText = `Pinger <b>${ip}</b> ?`;
    title.innerHTML = 'Confirmation action';
    body.innerHTML = actionText;
    confirmBtn.style.display = '';
    confirmBtn.onclick = function() {
        alert('Action non implémentée côté backend.');
        modal.style.display = 'none';
    };
}

    // Fonction d'initialisation au chargement DOM
    document.addEventListener('DOMContentLoaded', function() {
        // Afficher un onglet par défaut, par exemple 'dashboard'
        showTab('dashboard');

        // Démarrer la mise à jour périodique des stats système
        updateSystemStats();
        setInterval(updateSystemStats, 2000);

        // Ajout d'une animation simple sur les boutons
        const buttons = document.querySelectorAll('.btn');
        buttons.forEach(button => {
            if (!button.onclick) {
                button.addEventListener('click', function() {
                    this.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        this.style.transform = '';
                    }, 150);
                });
            }
        });

        // Gestion simple des focus/blur sur les inputs
        const inputs = document.querySelectorAll('.form-control');
        inputs.forEach(input => {
            input.addEventListener('focus', function() {
                this.style.borderColor = '#667eea';
            });
            input.addEventListener('blur', function() {
                this.style.borderColor = '#e2e8f0';
            });
        });
    });
</script>
</body>
</html>