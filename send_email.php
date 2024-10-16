<?php
// Using PHPMailer for better SMTP handling
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $smtp = $_POST['smtp'];
    $port = $_POST['port'];
    $app_password = $_POST['app_password'];
    $to_email = $_POST['to_email'];
    $subject = $_POST['subject'];
    $message = $_POST['message'];
    $security = $_POST['security']; // Get selected security option

    $mail = new PHPMailer(true);

    try {
        // Server settings
        $mail->isSMTP();
        $mail->Host = $smtp;
        $mail->SMTPAuth = true;
        $mail->Username = $email;
        $mail->Password = $app_password;

        // Set encryption based on user's choice
        if ($security === 'ssl') {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS; // SSL
            $mail->Port = 465; // SSL Port
        } else {
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // TLS
            $mail->Port = $port; // Use the provided port for TLS
        }

        // Recipients
        $mail->setFrom($email);
        $mail->addAddress($to_email);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = nl2br($message);

        // Send email
        $mail->send();
        // Return success response with 200 status
        http_response_code(200);
        echo json_encode(['status' => 'success', 'message' => 'Message has been sent.']);
    } catch (Exception $e) {
        // Return error response with 500 status
        http_response_code(500);
        echo json_encode(['status' => 'error', 'message' => "Message could not be sent. Mailer Error: {$mail->ErrorInfo}"]);
    }
} else {
    // Return a 405 Method Not Allowed status if not a POST request
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed.']);
}
