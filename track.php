<?php
// track.php - Affiche les données du visiteur dans le navigateur

// Désactive tout buffer
while (ob_get_level()) ob_end_clean();

// Stockage fichier (optionnel)
$logFile = '/tmp/tracking.txt';
if (!is_dir('/tmp')) {
    $logFile = __DIR__ . '/tracking.txt';
}

// Données
$publicIP  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
$privateIP = $_POST['local_ip'] ?? 'non détectée';
$referer   = $_SERVER['HTTP_REFERER'] ?? 'direct';
$date      = date('Y-m-d H:i:s');

// Construction de l'affichage
$output  = "=============================================\n";
$output .= "Date: $date\n";
$output .= "IP Publique: $publicIP\n";
$output .= "IP Privée: $privateIP\n";
$output .= "User Agent: $userAgent\n";
$output .= "Referer: $referer\n";
$output .= "=============================================\n\n";

// Enregistrement dans le fichier
@file_put_contents($logFile, $output, FILE_APPEND | LOCK_EX);

// 🔥 AFFICHAGE DANS LE NAVIGATEUR
header('Content-Type: text/plain; charset=utf-8');
echo $output;
?>
