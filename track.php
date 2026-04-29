<?php
// track.php - Version complète IP publique + privée + User Agent

// 📁 Stockage : utilise le dossier courant si /data/ n'existe pas
$logDir = is_dir('/data') ? '/data/' : __DIR__ . '/';
$logFile = $logDir . 'tracking.txt';

// IP publique
$publicIP = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

// User Agent
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';

// IP privée (envoyée par JavaScript)
$privateIP = $_POST['local_ip'] ?? 'non détectée';

// Referer
$referer = $_SERVER['HTTP_REFERER'] ?? 'direct';

// Date et heure
$date = date('Y-m-d H:i:s');

// Construction de la ligne de log
$entry = "=============================================\n";
$entry .= "Date: $date\n";
$entry .= "IP Publique: $publicIP\n";
$entry .= "IP Privée: $privateIP\n";
$entry .= "User Agent: $userAgent\n";
$entry .= "Referer: $referer\n";
$entry .= "=============================================\n\n";

// Écriture dans le fichier (sans erreur)
file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);

// Retourne une image invisible (pixel GIF)
ob_clean(); // Nettoie tout buffer sortie avant l'image
header('Content-Type: image/gif');
echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
?>
