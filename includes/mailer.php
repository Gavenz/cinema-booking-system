<?php
// includes/mailer.php
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/mail_config.php';

function sendBookingEmail(string $to, string $subject, string $htmlBody, ?string $attachmentPath=null): bool {
    $mail = new PHPMailer(true);

    // Debug logging to PHP error log
    $mail->SMTPDebug  = 2; 
    $mail->Debugoutput = function ($str) {
        error_log('PHPMailer: ' . trim($str));
    };

    try {
        // === MailHog-friendly SMTP settings ===
        $mail->isSMTP();
        $mail->Host        = SMTP_HOST;    // defined in mail_config.php
        $mail->Port        = SMTP_PORT;    // 1025 for MailHog
        $mail->SMTPAuth    = false;        // no auth for MailHog
        $mail->SMTPAutoTLS = false;        // don’t force TLS
        $mail->SMTPSecure  = false;        // no SSL/TLS

        // From/To
        $mail->setFrom(SMTP_FROM, SMTP_FROM_NAME);
        $mail->addAddress($to);

        // Content
        $mail->isHTML(true);
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;

        if ($attachmentPath && file_exists($attachmentPath)) {
            $mail->addAttachment($attachmentPath);
        }

        $mail->send();
        return true;

    } catch (Exception $e) {
        error_log("Mailer Error: " . $mail->ErrorInfo);
        return false;
    }
}
