<?php
require 'vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$dotenv = Dotenv\Dotenv::createImmutable(__DIR__);
$dotenv->load();

header('Access-Control-Allow-Origin: *'); // Allow calls from your script tags
header('Content-Type: application/json');

// 1. SECURITY CHECK
$providedKey = $_POST['api_key'] ?? '';
if ($providedKey !== $_ENV['API_SECRET']) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Invalid API Key']);
    exit;
}

// 2. GET DATA FROM CLIENT
$recipientsInput = $_POST['recipients'] ?? ''; // Expecting a JSON string or comma-separated list
$subject = $_POST['subject'] ?? 'New Notification';
$messageBody = $_POST['message'] ?? 'No content';

// Parse recipients (handles both JSON arrays and comma-separated strings)
$recipients = json_decode($recipientsInput);
if (!is_array($recipients)) {
    // Fallback: try comma separation if not JSON
    $recipients = array_map('trim', explode(',', $recipientsInput));
}

// Remove empty entries
$recipients = array_filter($recipients);

if (empty($recipients)) {
    echo json_encode(['status' => 'error', 'message' => 'No recipients provided']);
    exit;
}

$mail = new PHPMailer(true);

try {
    // Server Settings
    $mail->isSMTP();
    $mail->Host       = $_ENV['SMTP_HOST'];
    $mail->SMTPAuth   = true;
    $mail->Username   = $_ENV['SMTP_USER'];
    $mail->Password   = $_ENV['SMTP_PASS'];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = $_ENV['SMTP_PORT'];

    // Sender
    $mail->setFrom($_ENV['SMTP_USER'], 'My App System');

    // Add All Recipients
    foreach ($recipients as $email) {
        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $mail->addAddress($email);
        }
    }

    // Content
    $mail->isHTML(true);
    $mail->Subject = htmlspecialchars($subject);
    $mail->Body    = nl2br(htmlspecialchars($messageBody)); // Convert newlines to <br>

    $mail->send();
    echo json_encode(['status' => 'success', 'message' => 'Emails sent']);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['status' => 'error', 'message' => $mail->ErrorInfo]);
}
?>
