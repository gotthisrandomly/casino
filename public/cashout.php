<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$finalBalance = $_SESSION['balance'];
session_destroy();

$_SESSION['message'] = "Successfully cashed out $" . number_format($finalBalance, 2) . "!";
$_SESSION['balance'] = 1000;
$_SESSION['bet'] = 10;
$_SESSION['symbols'] = ['🎰', '🎰', '🎰'];

header('Location: index.php');