<?php
require_once __DIR__ . '/../includes/init.php';

function rowLabel($n){ return chr(ord('A') + $n); }

// NOTE: your DSN uses dbname=cinema (not cinema_db). Make sure your tables are in that DB.
$hall = $pdo->query("SELECT id, rows_count, cols_count FROM halls WHERE name = 'Hall 1'")
            ->fetch();
if (!$hall) {
    http_response_code(404);
    die('hall 1 not found');
}

$hid = (int)$hall['id'];
$R   = (int)$hall['rows_count'];
$C   = (int)$hall['cols_count'];

// INSERT IGNORE equivalent in PDO (works with MySQL)
$ins = $pdo->prepare("INSERT IGNORE INTO seats (halls_id, row_label, col_num) VALUES (:hid, :row, :col)");

$pdo->beginTransaction();
try {
    for ($r = 0; $r < $R; $r++) {
        $row = rowLabel($r);
        for ($c = 1; $c <= $C; $c++) {
            $ins->execute([
                ':hid' => $hid,
                ':row' => $row,
                ':col' => $c,
            ]);
        }
    }
    $pdo->commit();
    echo "Seeded seats for Hall 1: {$R}x{$C}\n";
} catch (Throwable $e) {
    $pdo->rollBack();
    http_response_code(500);
    echo "Seeding failed: " . htmlspecialchars($e->getMessage());
}
