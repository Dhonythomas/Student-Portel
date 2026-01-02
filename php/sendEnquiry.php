<?php
header('Content-Type: application/json');
require 'vendor/autoload.php';
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// ✅ Gmail SMTP credentials (replace with your real ones)
const SMTP_HOST = 'smtp.gmail.com';
const SMTP_USERNAME = 'supp0rtucc0llege@gmail.com';
const SMTP_PASSWORD = 'jxji cpuc cdst znte';

// --- Read JSON request ---
$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    echo json_encode(['success' => false, 'message' => 'Invalid request format.']);
    exit;
}

$departmentEmail = trim($data['departmentEmail'] ?? '');
$studentEmail    = trim($data['studentEmail'] ?? '');
$subject         = trim($data['subject'] ?? '');
$message         = trim($data['message'] ?? '');

// --- Basic validation ---
if (empty($departmentEmail) || empty($studentEmail) || empty($subject) || empty($message)) {
    echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    exit;
}

if (!filter_var($departmentEmail, FILTER_VALIDATE_EMAIL) || !filter_var($studentEmail, FILTER_VALIDATE_EMAIL)) {
    echo json_encode(['success' => false, 'message' => 'Invalid email format.']);
    exit;
}

// --- Compose and send email ---
$mail = new PHPMailer(true);

try {
    $mail->isSMTP();
    $mail->Host = SMTP_HOST;
    $mail->SMTPAuth = true;
    $mail->Username = SMTP_USERNAME;
    $mail->Password = SMTP_PASSWORD;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    // Optional TLS configuration (disable verification if using localhost)
    $mail->SMTPOptions = [
        'ssl' => [
            'verify_peer' => false,
            'verify_peer_name' => false,
            'allow_self_signed' => true,
        ]
    ];

    // --- Email content ---
    $mail->setFrom(SMTP_USERNAME, 'UCCOLLEGE Support');
    $mail->addAddress($departmentEmail);
    $mail->addReplyTo($studentEmail, 'Student');
    $mail->addCC($studentEmail); // optional: send a copy to the student

    $mail->isHTML(true);
    $mail->Subject = "Enquiry: " . htmlspecialchars($subject);
    $mail->Body = "
        <h2>New Enquiry from Student</h2>
        <p><strong>From:</strong> {$studentEmail}</p>
        <p><strong>Subject:</strong> " . nl2br(htmlspecialchars($subject)) . "</p>
        <p><strong>Message:</strong><br>" . nl2br(htmlspecialchars($message)) . "</p>
        <hr>
        <p style='font-size:12px;color:#777;'>Sent automatically via UCCOLLEGE Portal</p>
    ";

    $mail->send();

    echo json_encode(['success' => true, 'message' => 'Email sent successfully.']);
} catch (Exception $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Mailer Error: ' . $mail->ErrorInfo
    ]);
}
?>