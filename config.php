<?php
// ============================================
// Fichier de configuration
// ============================================

// Chemin du dossier de logs (hors dossier public si possible)
define('LOG_DIR', __DIR__ . '/logs/');
define('LOG_FILE', LOG_DIR . 'data.log');

// Webhook Telegram (exemple - ne pas utiliser réellement)
define('TELEGRAM_WEBHOOK', 'https://api.telegram.org/botVOTRE_TOKEN/sendMessage');
define('TELEGRAM_CHAT_ID', 'VOTRE_CHAT_ID');

// Email d'exfiltration (simulation)
define('EXFIL_EMAIL', 'boite-recolte@mail.com');

// Clé de chiffrement (AES-256)
define('ENCRYPTION_KEY', 'votre_cle_32_caracteres_longue');
?>
