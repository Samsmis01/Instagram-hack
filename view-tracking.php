<?php
$logFile = '/data/tracking.txt';

if (file_exists($logFile)) {
    header('Content-Type: text/plain');
    echo file_get_contents($logFile);
} else {
    echo "Aucun tracking pour le moment.\n";
    echo "Le fichier /data/tracking.txt n'existe pas encore.\n";
}
?>
