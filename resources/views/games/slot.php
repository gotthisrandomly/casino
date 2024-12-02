<?php
$balance = $_SESSION['balance'] ?? 1000;
$bet = $_SESSION['bet'] ?? 10;
$symbols = $_SESSION['symbols'] ?? ['🎰', '🎰', '🎰'];
$message = $_SESSION['message'] ?? 'Place your bet!';
?>

<div class="game-info">
    <h2>Balance: $<?php echo number_format($balance, 2); ?></h2>
    <p><?php echo htmlspecialchars($message); ?></p>
</div>

<div class="slot-machine">
    <?php foreach ($symbols as $symbol): ?>
        <div class="slot"><?php echo $symbol; ?></div>
    <?php endforeach; ?>
</div>

<form class="controls" method="POST" action="spin.php">
    <input type="number" name="bet" value="<?php echo $bet; ?>" min="1" max="100">
    <button type="submit" class="btn">SPIN</button>
</form>

<div class="controls">
    <form method="POST" action="cashout.php" style="display: inline-block; margin: 10px;">
        <button type="submit" class="btn">CASH OUT</button>
    </form>
</div>