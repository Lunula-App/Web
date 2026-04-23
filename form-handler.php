<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

require_once __DIR__ . '/config.php';

// ── Verify hCaptcha ──────────────────────────────────────────────────────────
$token = trim($_POST['h-captcha-response'] ?? '');

if (empty($token)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please complete the captcha.']);
    exit;
}

$ch = curl_init('https://hcaptcha.com/siteverify');
curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST           => true,
    CURLOPT_POSTFIELDS     => http_build_query([
        'secret'   => HCAPTCHA_SECRET,
        'response' => $token,
        'remoteip' => $_SERVER['REMOTE_ADDR'] ?? '',
    ]),
    CURLOPT_TIMEOUT => 10,
]);
$verify = curl_exec($ch);
curl_close($ch);

$captcha = json_decode($verify);
if (!$captcha || !$captcha->success) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Captcha verification failed. Please try again.']);
    exit;
}

// ── Sanitise inputs ──────────────────────────────────────────────────────────
$first_name = trim($_POST['first_name'] ?? '');
$last_name  = trim($_POST['last_name']  ?? '');
$email      = trim($_POST['email']      ?? '');
$topic      = trim($_POST['topic']      ?? '');
$platform   = trim($_POST['platform']   ?? '');
$message    = trim($_POST['message']    ?? '');

// Validate required fields
if (empty($first_name) || empty($email) || empty($topic) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please fill in all required fields.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Please enter a valid email address.']);
    exit;
}

// Strip newlines from header-injectable fields
$first_name = preg_replace('/[\r\n]/', '', $first_name);
$last_name  = preg_replace('/[\r\n]/', '', $last_name);
$email      = preg_replace('/[\r\n]/', '', $email);
$topic      = preg_replace('/[\r\n]/', '', $topic);
$platform   = preg_replace('/[\r\n]/', '', $platform);

$name = trim("$first_name $last_name");

// ── Build and send email ─────────────────────────────────────────────────────
$subject = "Lunula Support: $topic";

$body  = "Name:     $name\n";
$body .= "Email:    $email\n";
$body .= "Topic:    $topic\n";
if (!empty($platform)) {
    $body .= "Platform: $platform\n";
}
$body .= "\nMessage:\n$message\n";

$headers  = "From: Lunula Support <noreply@lunula.me>\r\n";
$headers .= "Reply-To: $name <$email>\r\n";
$headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
$headers .= "X-Mailer: PHP/" . phpversion() . "\r\n";

if (mail(SUPPORT_EMAIL, $subject, $body, $headers)) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to send your message. Please try again later.']);
}
