<?php
/**
 * admin_showtimes.php
 *
 * Admin interface for managing showtimes.
 *
 * Responsibilities:
 * - Restricts access to admin users only.
 * - Lists existing showtimes with movie, hall and timing information.
 * - Handles form submissions to create/update/delete showtimes.
 *
 * Supports Functional Requirement F18 (Admin Manage Showtimes Page).
 */

require_once __DIR__ . '/../includes/init.php';
require_admin();
// --- Run aggregate queries to compute report metrics (e.g., revenue by movie) ---
$sql = "
SELECT
  s.id                                      AS showtime_id,
  m.title                                   AS movie_title,
  h.name                                    AS hall_name,
  s.starts_at,

  -- total seats in the hall (rows*cols preferred; fallback to seats count)
  COALESCE(h.rows_count * h.cols_count, cap.seat_capacity) AS capacity_total,

  -- seats actually booked (confirmed only)
  COALESCE(sold.seats_booked, 0)            AS seats_booked,

  -- revenue = sum of confirmed booking totals for this showtime
  COALESCE(rev.revenue_total, 0.00)         AS revenue_total,

  -- helpful extra: occupancy %
  ROUND(
    100.0 * COALESCE(sold.seats_booked,0)
    / NULLIF(COALESCE(h.rows_count*h.cols_count, cap.seat_capacity), 0), 1
  ) AS occupancy_pct

FROM showtimes s
JOIN movies m ON m.id = s.movies_id
JOIN halls  h ON h.id = s.hall_id

-- capacity per hall (pre-aggregated)
LEFT JOIN (
  SELECT halls_id, COUNT(*) AS seat_capacity
  FROM seats
  GROUP BY halls_id
) cap ON cap.halls_id = h.id

-- seats booked per showtime (confirmed only, pre-aggregated)
LEFT JOIN (
  SELECT bi.showtime_id, COUNT(*) AS seats_booked
  FROM booking_items bi
  JOIN booking b ON b.id = bi.booking_id
  WHERE b.booking_status = 'confirmed'
  GROUP BY bi.showtime_id
) sold ON sold.showtime_id = s.id

-- revenue per showtime (confirmed only, pre-aggregated)
LEFT JOIN (
  SELECT showtime_id, ROUND(SUM(total_amount), 2) AS revenue_total
  FROM booking
  WHERE booking_status = 'confirmed'
  GROUP BY showtime_id
) rev ON rev.showtime_id = s.id

-- optional filter:
-- WHERE DATE(s.starts_at) BETWEEN :from AND :to

ORDER BY s.starts_at DESC;

";
$rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Admin · Revenue by Showtime</title>
  <link rel="stylesheet" href="<?= url('assets/styles.css') ?>">
</head>
<body>
<?php include __DIR__ . '/../includes/header.php'; ?>
<main style="max-width:1000px;margin:2rem auto;padding:0 20px">
  <h1>Showtime Revenue & Capacity</h1>
  <div class="admin-toolbar" style="margin: .5rem 0 1.25rem; display:flex; gap:.8rem; align-items:center;">
    <a class="btn ghost" href="<?= url('pages/admin.php') ?>">← Back to Dashboard</a>
    </div>
  <table border="1" cellpadding="6" cellspacing="0">
    <thead>
      <tr>
        <th>ID</th>
        <th>Movie</th>
        <th>Hall</th>
        <th>Starts</th>
        <th>Capacity (total)</th>
        <th>Booked</th>
        <th>Remaining</th>
        <th>Occupancy</th>
        <th>Revenue</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($rows as $r):
        $cap       = (int)($r['capacity_total'] ?? 0);
        $booked    = (int)($r['seats_booked']   ?? 0);
        $remaining = max(0, $cap - $booked);
        $occ       = isset($r['occupancy_pct']) ? (float)$r['occupancy_pct'] : (
                       $cap ? round(100 * $booked / $cap, 1) : 0
                     );
        $revenue   = (float)($r['revenue_total'] ?? 0);
      ?>
      <tr>
        <td><?= (int)$r['showtime_id'] ?></td>
        <td><?= htmlspecialchars($r['movie_title']) ?></td>
        <td><?= htmlspecialchars($r['hall_name']) ?></td>
        <td><?= htmlspecialchars($r['starts_at']) ?></td>
        <td><?= $cap ?></td>
        <td><?= $booked ?></td>
        <td><?= $remaining ?></td>
        <td><?= number_format($occ, 1) ?>%</td>
        <td>$<?= number_format($revenue, 2) ?></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</main>

