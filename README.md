# RootNet

RootNet est une interface web d'administration réseau permettant de surveiller, diagnostiquer et gérer les appareils connectés à votre réseau local.

## Fonctionnalités

- **Tableau de bord** : Visualisation en temps réel de l'utilisation CPU, mémoire, température, et bande passante.
- **Configuration réseau** : Affichage de la configuration LAN (interface, IP, masque, passerelle, DNS, SSID…).
- **Liste des appareils** : Scan automatique du réseau pour détecter tous les appareils connectés (affichage IP, MAC, type, etc.).
- **Monitoring** : Suivi du trafic réseau, surveillance des ports, tests de performance, top connexions.
- **Logs** : Affichage des journaux système récents.
- **Actions rapides** : Scan de ports, ARP spoof, deauth WiFi, Wake-on-LAN, ping sur chaque appareil (actions à implémenter côté backend).

## Installation

1. **Prérequis** :
   - Serveur web avec PHP (Apache, Nginx…)
   - Accès root ou sudo pour certaines commandes réseau (nmap, arp, fping…)
   - Outils nécessaires : `nmap`, `fping`, `arp`, `iwgetid`

2. **Déploiement** :
   - Clonez ce dépôt ou copiez les fichiers sur votre serveur web :
     ```
     git clone <repo-url> rootnet
     ```
   - Placez les fichiers dans le dossier web (ex : `/var/www/html/rootnet`).

3. **Droits sudo** :
   - Pour permettre à PHP d'utiliser `nmap` sans mot de passe, ajoutez dans `/etc/sudoers` :
     ```
     www-data ALL=(ALL) NOPASSWD: /usr/bin/nmap
     ```
   - Adaptez l'utilisateur (`www-data`) et le chemin selon votre configuration.

## Utilisation

- Accédez à l'interface via : `http://<adresse-ip-de-votre-serveur>/rootnet/index.php`
- Le scan réseau et les statistiques système se mettent à jour automatiquement toutes les 2 secondes.
- Cliquez sur les onglets pour naviguer entre les différentes sections.

## Sécurité

- **Attention** : Certaines fonctionnalités nécessitent des droits élevés (sudo). Ne jamais exposer cette interface sur Internet sans authentification ni restriction d'accès.
- Pour usage sur réseau local uniquement.

## Structure des fichiers

- `index.php` : Interface principale (frontend)
- `system_stats.php` : API PHP pour les statistiques système et réseau
- `get_devices.php` : API PHP pour le scan et la liste des appareils connectés
- `style.css` : Styles additionnels (optionnel)
- `README.md` : Ce fichier

## Auteurs

- [Votre nom ou pseudo]

## Licence

Ce projet est distribué sous licence MIT.