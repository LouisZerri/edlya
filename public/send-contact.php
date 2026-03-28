<?php
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Methode non autorisee']);
    exit;
}

$name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$phone = filter_input(INPUT_POST, 'phone', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
$message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_FULL_SPECIAL_CHARS);

if (!$name || !$email || !$message) {
    http_response_code(400);
    echo json_encode(['error' => 'Champs obligatoires manquants']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['error' => 'Email invalide']);
    exit;
}

// Config SMTP Ionos
$smtpHost = 'smtp.ionos.fr';
$smtpPort = 465;
$smtpUser = 'contact@edlya.fr';
$smtpPass = 'ByroN.GESTIMMO2005';
$fromEmail = 'contact@edlya.fr';
$fromName = 'Edlya';
$toEmail = 'contact@edlya.fr';

$subject = 'Nouveau message depuis edlya.fr - ' . $name;

$body = "Nouveau message depuis le site edlya.fr\r\n";
$body .= "========================================\r\n\r\n";
$body .= "Nom : $name\r\n";
$body .= "Email : $email\r\n";
$body .= "Telephone : " . ($phone ?: 'Non renseigne') . "\r\n\r\n";
$body .= "Message :\r\n$message\r\n";

// Envoi via SMTP avec fsockopen
$sent = sendSmtp($smtpHost, $smtpPort, $smtpUser, $smtpPass, $fromEmail, $fromName, $toEmail, $email, $subject, $body);

if ($sent) {
    echo json_encode(['success' => true]);
} else {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors de l\'envoi']);
}

function sendSmtp($host, $port, $user, $pass, $from, $fromName, $to, $replyTo, $subject, $body) {
    $socket = @fsockopen("ssl://$host", $port, $errno, $errstr, 10);
    if (!$socket) return false;

    $response = fgets($socket, 512);
    if (substr($response, 0, 3) !== '220') { fclose($socket); return false; }

    $commands = [
        "EHLO edlya.fr",
        "AUTH LOGIN",
        base64_encode($user),
        base64_encode($pass),
        "MAIL FROM:<$from>",
        "RCPT TO:<$to>",
        "DATA",
    ];

    $expectedCodes = ['250', '334', '334', '235', '250', '250', '354'];

    foreach ($commands as $i => $cmd) {
        fputs($socket, "$cmd\r\n");
        $response = fgets($socket, 512);
        if (substr($response, 0, 3) !== $expectedCodes[$i]) {
            fputs($socket, "QUIT\r\n");
            fclose($socket);
            return false;
        }
    }

    $headers = "From: $fromName <$from>\r\n";
    $headers .= "Reply-To: $replyTo\r\n";
    $headers .= "To: $to\r\n";
    $headers .= "Subject: $subject\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/plain; charset=UTF-8\r\n";
    $headers .= "Date: " . date('r') . "\r\n";

    $data = $headers . "\r\n" . $body . "\r\n.\r\n";
    fputs($socket, $data);
    $response = fgets($socket, 512);
    $success = (substr($response, 0, 3) === '250');

    fputs($socket, "QUIT\r\n");
    fclose($socket);

    return $success;
}
