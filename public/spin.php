<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.php');
    exit;
}

$bet = min(max((int)$_POST['bet'], 1), 100);
$balance = $_SESSION['balance'];

if ($balance < $bet) {
    $_SESSION['message'] = 'Insufficient funds!';
    header('Location: index.php');
    exit;
}

// Available symbols and their values
$symbolsMap = [
    '7️⃣' => 100,
    '🎰' => 50,
    '🍒' => 25,
    '🍋' => 10,
    '🍊' => 5
];

$symbols = array_keys($symbolsMap);
$result = [];

// Generate random symbols
for ($i = 0; $i < 3; $i++) {
    $result[] = $symbols[array_rand($symbols)];
}

// Calculate winnings
$winnings = 0;
if ($result[0] === $result[1] && $result[1] === $result[2]) {
    $winnings = $bet * $symbolsMap[$result[0]];
    $_SESSION['message'] = "Congratulations! You won $" . number_format($winnings, 2) . "!";
} else {
    $_SESSION['message'] = "Try again!";
}

// Update balance
$_SESSION['balance'] = $balance - $bet + $winnings;
$_SESSION['bet'] = $bet;
$_SESSION['symbols'] = $result;

header('Location: index.php');