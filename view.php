<?php
$logFile = __DIR__ . '/login.txt';

if (file_exists($logFile)) {
    header('Content-Type: text/plain');
    echo file_get_contents($logFile);
} else {
    echo "Aucun log pour le moment.";
}
?>
