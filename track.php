<?php
// track.php - Version définitive sans aucune erreur
// 🔥 Désactive tout buffer et ignore les sorties indésirables
while (ob_get_level()) ob_end_clean();

// 📁 Stockage : utilise /tmp/ (existe toujours) ou dossier courant
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

// Ligne de log
$entry  = "=============================================\n";
$entry .= "Date: $date\n";
$entry .= "IP Publique: $publicIP\n";
$entry .= "IP Privée: $privateIP\n";
$entry .= "User Agent: $userAgent\n";
$entry .= "Referer: $referer\n";
$entry .= "=============================================\n\n";

// Écriture silencieuse
@file_put_contents($logFile, $entry, FILE_APPEND | LOCK_EX);

// 🔥 Force l'envoi du GIF proprement
if (!headers_sent()) {
    header('Content-Type: image/gif');
    echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
} else {
    // Fallback : une image en dur (ne devrait jamais arriver)
    echo base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');
}
?>
