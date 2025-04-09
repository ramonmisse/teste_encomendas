<?php
// Start session for state management
session_start();

// Include necessary files
require_once 'includes/config.php';
require_once 'includes/functions.php';
require_once 'includes/header.php';

// Determine which page to display
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Load the appropriate page content
switch ($page) {
    case 'orders':
        include 'pages/order_listing.php';
        break;
    case 'new-order':
        include 'pages/order_form.php';
        break;
    case 'admin':
        include 'pages/admin_panel.php';
        break;
    default:
        include 'pages/home.php';
        break;
}

// Include footer
require_once 'includes/footer.php';
?>