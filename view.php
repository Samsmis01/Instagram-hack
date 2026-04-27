<?php
// ============================================
// view.php - Version avec migration automatique
// ============================================

$oldLogFile = __DIR__ . '/login.txt';
$newLogFile = '/data/login.txt';

// 🔄 MIGRATION AUTOMATIQUE : si ancien fichier existe et nouveau n'existe pas
if (file_exists($oldLogFile) && !file_exists($newLogFile)) {
    // Copier l'ancien fichier vers le nouveau
    copy($oldLogFile, $newLogFile);
    
    // Optionnel : renommer l'ancien pour éviter double lecture
    rename($oldLogFile, $oldLogFile . '.migrated');
}

// 🔥 Lecture depuis le disque persistant Render
if (file_exists($newLogFile)) {
    // Affiche en texte brut
    header('Content-Type: text/plain');
    echo file_get_contents($newLogFile);
} else {
    // Si aucun log n'existe encore
    echo "Aucun log pour le moment.\n";
    echo "Le fichier /data/login.txt n'existe pas encore.\n";
    echo "Connectez-vous via login.html pour créer les logs.";
}
?>
