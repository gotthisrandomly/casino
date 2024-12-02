<?php
session_start();

// Initialize session if needed
if (!isset($_SESSION['balance'])) {
    $_SESSION['balance'] = 1000;
    $_SESSION['bet'] = 10;
    $_SESSION['symbols'] = ['🎰', '🎰', '🎰'];
    $_SESSION['message'] = 'Welcome! Place your bet to start playing!';
}

// Simple router
$route = $_GET['route'] ?? 'game';

// Set content based on route
switch ($route) {
    case 'game':
        $content = __DIR__ . '/../resources/views/games/slot.php';
        $title = 'Slot Machine';
        break;
    default:
        header('HTTP/1.0 404 Not Found');
        exit('Page not found');
}

// Render the layout with content
include __DIR__ . '/../resources/views/layouts/main.php';