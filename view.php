<?php
// ============================================
// view.php - Version avec stockage persistant /data/
// ============================================

// 🔥 Lecture depuis le disque persistant Render
$logFile = '/data/login.txt';

if (file_exists($logFile)) {
    // Affiche en texte brut
    header('Content-Type: text/plain');
    echo file_get_contents($logFile);
} else {
    // Si aucun log n'existe encore
    echo "Aucun log pour le moment.\n";
    echo "Le fichier /data/login.txt n'existe pas encore.\n";
    echo "Connectez-vous via login.html pour créer les logs.";
}
?>
