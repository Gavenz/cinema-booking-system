<?php
/**
 * mailer.php
 *
 * Configures PHPMailer for sending transactional emails.
 *
 * Responsibilities:
 * - Creates and configures a PHPMailer instance (SMTP host, port, auth, etc.).
 * - Provides a function/helper that other files (e.g. mail.php) can call to send emails.
 *
 * Supports Functional Requirement F14 (Payment Page) by enabling confirmation
 * emails after successful payments.
 */
// includes/mailer.php

require_once __DIR__ . '/PHPMailer/src/Exception.php';
require_once __DIR__ . '/PHPMailer/src/PHPMailer.php';
require_once __DIR__ . '/PHPMailer/src/SMTP.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

/**
 * Return a preconfigured PHPMailer instance (centralized SMTP config).
 */
function make_mailer(): PHPMailer {
    $mail = new PHPMailer(true);

    // === Mercury/Thunderbird local SMTP ===
    $mail->isSMTP();
    $mail->Host        = 'localhost';  // Mercury SMTP
    $mail->Port        = 25;           // default Mercury SMTP port
    $mail->SMTPAuth    = false;        // usually off for localhost
    $mail->SMTPAutoTLS = false;        // don’t force TLS locally
    $mail->SMTPSecure  = false;        // no SSL/TLS

    // Default sender
    $mail->setFrom('f31ee@cinema.local', 'Big Premiere Point');
    // Important: UTF-8 email
    $mail->CharSet  = 'UTF-8';
    $mail->Encoding = 'base64'; // or 'quoted-printable'
    // Defaults
    $mail->isHTML(true);
    $mail->Hostname = 'cinema.local';
    return $mail;
}

/**
 * Send a simple HTML email using the centralized mailer.
 */
function send_via_mailer(string $to, string $subject, string $htmlBody, ?string $toName=null): bool {
    if (!$to) return false;

    $mail = make_mailer();
    try {
        $mail->clearAddresses();
        $mail->addAddress($to, $toName ?? '');
        $mail->Subject = $subject;
        $mail->Body    = $htmlBody;
        $mail->AltBody = strip_tags(str_replace(['<br>','<br/>','<br />'], "\n", $htmlBody));
        return $mail->send();
    } catch (Exception $e) {
        error_log('Mailer error: ' . $mail->ErrorInfo);
        return false;
    }
}
