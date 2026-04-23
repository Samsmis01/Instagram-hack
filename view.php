<?php
// Protéger par mot de passe simple
$auth_password = 'admin123';
if (!isset($_SERVER['PHP_AUTH_USER']) || $_SERVER['PHP_AUTH_PW'] !== $auth_password) {
    header('WWW-Authenticate: Basic realm="Logs"');
    header('HTTP/1.0 401 Unauthorized');
    die('Accès non autorisé');
}

require_once 'config.php';

if (file_exists(LOG_FILE)) {
    $logs = file_get_contents(LOG_FILE);
    echo "<pre>" . htmlspecialchars($logs) . "</pre>";
} else {
    echo "Aucun log pour le moment.";
}
?>
