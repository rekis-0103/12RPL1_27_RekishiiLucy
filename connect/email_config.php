<?php
/**
 * Email Configuration for PT Waindo Specterra
 * 
 * This file contains email settings for the application.
 * Update the SMTP settings according to your email provider.
 */

// Email Configuration
define('EMAIL_HOST', 'smtp.gmail.com');
define('EMAIL_PORT', 587);
define('EMAIL_USERNAME', 'rekishiilucy658@gmail.com');
define('EMAIL_PASSWORD', 'censored'); // Gmail App Password
define('EMAIL_FROM_NAME', 'PT Waindo Specterra HRD');
define('EMAIL_FROM_ADDRESS', 'rekishiilucy658@gmail.com');

// Alternative configurations for different email providers
// For Outlook/Hotmail:
// define('EMAIL_HOST', 'smtp-mail.outlook.com');
// define('EMAIL_PORT', 587);

// For Yahoo:
// define('EMAIL_HOST', 'smtp.mail.yahoo.com');
// define('EMAIL_PORT', 587);

// For custom SMTP server:
// define('EMAIL_HOST', 'your-smtp-server.com');
// define('EMAIL_PORT', 587);

/**
 * Send email using PHPMailer
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Email message body
 * @param bool $isHTML Whether the message is HTML format
 * @return bool True if email sent successfully, false otherwise
 */
function sendEmail($to, $subject, $message, $isHTML = false) {
    try {
        require_once __DIR__ . '/../vendor/autoload.php';
        
        $mail = new \PHPMailer\PHPMailer\PHPMailer(true);
        
        // Server settings
        $mail->isSMTP();
        $mail->Host = EMAIL_HOST;
        $mail->SMTPAuth = true;
        $mail->Username = EMAIL_USERNAME;
        $mail->Password = EMAIL_PASSWORD;
        $mail->SMTPSecure = \PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port = EMAIL_PORT;
        
        // Enable debug output (set to 0 for production)
        $mail->SMTPDebug = 0;
        
        // Recipients
        $mail->setFrom(EMAIL_FROM_ADDRESS, EMAIL_FROM_NAME);
        $mail->addAddress($to);
        
        // Content
        $mail->isHTML($isHTML);
        $mail->Subject = $subject;
        $mail->Body = $message;
        
        if ($isHTML) {
            $mail->AltBody = strip_tags($message);
        }
        
        $mail->send();
        return true;
    } catch (Exception $e) {
        error_log("Email sending failed: " . $e->getMessage());
        return false;
    }
}

/**
 * Send HTML email
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $htmlMessage HTML email message
 * @return bool True if email sent successfully, false otherwise
 */
function sendHTMLEmail($to, $subject, $htmlMessage) {
    return sendEmail($to, $subject, $htmlMessage, true);
}

/**
 * Send plain text email
 * 
 * @param string $to Recipient email address
 * @param string $subject Email subject
 * @param string $message Plain text email message
 * @return bool True if email sent successfully, false otherwise
 */
function sendTextEmail($to, $subject, $message) {
    return sendEmail($to, $subject, $message, false);
}
?>