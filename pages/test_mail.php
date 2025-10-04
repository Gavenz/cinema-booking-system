<?php
require_once __DIR__ . '/../includes/mailer.php';

$html = "<h2>Test Email</h2><p>This is a test booking confirmation.</p>";
if (sendBookingEmail("user@example.test", "Cinema Booking Test", $html)) {
    echo "✅ Email sent successfully (check MailHog at http://localhost:8025)";
} else {
    echo "❌ Failed to send email, check logs";
}
