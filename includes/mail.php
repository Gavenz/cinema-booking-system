<?php
/**
 * mail.php
 *
 * Defines email-sending logic for booking and payment confirmations.
 *
 * Responsibilities:
 * - Composes email subject and body (movie title, showtime, hall, seats, total).
 * - Uses the configured PHPMailer instance from mailer.php to send emails.
 *
 * Supports Functional Requirement F14 (Payment Page) by sending a confirmation
 * email to the user after successful booking/payment.
 */

// includes/mail.php
require_once __DIR__ . '/mailer.php'; // central SMTP config lives here

/**
 * Thin wrapper used across the app.
 */
function send_email(string $to, string $subject, string $html, string $toName='Guest'): bool {
  // single source of truth: mailer.php
  return send_via_mailer($to, $subject, $html, $toName); 
}

/**
 * Build & send booking confirmation for a booking id.
 * (unchanged logic; only the final send uses send_email())
 */
function send_booking_email(PDO $pdo, int $bookingId, string $toEmail, string $toName='Guest'): void {
  if (!$toEmail) return;

  // ... (your existing query and HTML building code) ...
  // Pull booking + items + movie + showtime 
  $sql = " SELECT b.id, b.qty, b.total_amount, b.booking_status, b.created_at, b.paid_at, s.starts_at, h.name AS hall_name, m.title, m.rating, m.runtime_min 
  FROM booking b 
  JOIN showtimes s ON s.id=b.showtime_id 
  JOIN halls h ON h.id=s.hall_id 
  JOIN movies m ON m.id=s.movies_id 
  WHERE b.id=:bid 
  "; 
  $st = $pdo->prepare($sql); 
  $st->execute([':bid'=>$bookingId]); 
  $b = $st->fetch(PDO::FETCH_ASSOC); 
  if (!$b) return; 

  $it = $pdo->prepare(" SELECT bi.seat_id, bi.price_id, bi.line_amount, p.ticket_type, CONCAT(se.row_label, se.col_num) AS seat_label 
  FROM booking_items bi 
  JOIN pricing p ON p.id = bi.price_id 
  JOIN seats se ON se.id = bi.seat_id 
  WHERE bi.booking_id = :bid 
  ORDER BY se.row_label, se.col_num "); 
  $it->execute([':bid'=>$bookingId]); 
  $items = $it->fetchAll(PDO::FETCH_ASSOC); 

  $seatList = array_column($items, 'seat_label'); 
  $lines = ''; foreach ($items as $r) { 
    $lines .= '<tr> 
    <td style="padding:6px 10px;border-bottom:1px solid #eee;">'.$r['seat_label'].' </td> 
    <td style="padding:6px 10px;border-bottom:1px solid #eee;">'.htmlspecialchars($r['ticket_type']).' </td> 
    <td style="padding:6px 10px;border-bottom:1px solid #eee;">$'.number_format((float)$r['line_amount'],2).' </td> 
    </tr>'; 
} 
// --- Compose booking/payment confirmation email content ---
$html = '
<div style="font-family:system-ui,-apple-system,Segoe UI,Roboto,Helvetica,Arial;max-width:640px;margin:0 auto;color:#111"> 
<h2 style="margin:0 0 10px">Your booking is confirmed 🎬</h2> 
<p>Hi '.htmlspecialchars($toName).',</p>
 <p>Thanks for booking with <strong>Big Premiere Point</strong>.
 Here are your details:</p> 
 
 <div style="background:#fafafa;border:1px solid #eee;border-radius:10px;padding:12px 14px;margin:10px 0"> 
 <div><strong>Movie:</strong> '.htmlspecialchars($b['title']).'</div>
  <div><strong>Hall:</strong> '.htmlspecialchars($b['hall_name']).'</div> 
  <div><strong>Showtime:</strong> '.htmlspecialchars(date('D, j M Y • g:i A', strtotime($b['starts_at']))).'</div> 
  <div><strong>Seats:</strong> '.htmlspecialchars(implode(', ', $seatList)).'</div> 
  </div> 
  
  <table width="100%" cellspacing="0" cellpadding="0" style="border-collapse:collapse;margin:10px 0"> 
  <thead> 
  <tr> 
    <th align="left" style="padding:6px 10px;border-bottom:2px solid #ddd">Seat</th> 
    <th align="left" style="padding:6px 10px;border-bottom:2px solid #ddd">Type</th> 
    <th align="left" style="padding:6px 10px;border-bottom:2px solid #ddd">Amount</th> </tr> 
  </thead> 
  <tbody>'.$lines.'</tbody> 
  </table> 
  
  <p><strong>Total Paid:</strong> 
  $'.number_format((float)$b['total_amount'],2).'</p> <p style="color:#666">Booking ID: #'.$b['id'].' • Paid at: '.($b['paid_at'] ? date('D, j M Y • g:i A', strtotime($b['paid_at'])) : 'just now').'</p> 
  <p>If you need to make changes, visit <a href="'.htmlspecialchars(url('pages/bookings.php')).'">My Bookings</a>.</p> <p style="color:#666">— Big Premiere Point</p> </div>';

 // --- Send email using PHPMailer and handle potential failures ---
  send_email($toEmail, 'Your booking is confirmed — Big Premiere Point', $html, $toName);
}
