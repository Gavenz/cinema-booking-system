<?php
require_once __DIR__ . '/../includes/init.php';
session_unset();
session_destroy();
header('Location: /cinema-booking-system/pages/login.php');
exit;
