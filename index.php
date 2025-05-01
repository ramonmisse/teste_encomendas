
<?php
session_start();
require_once 'includes/config.php';
require_once 'includes/functions.php';

// Check authentication
if (!isset($_SESSION['user_id']) && $_GET['page'] != 'login') {
    header('Location: index.php?page=login');
    exit;
}

// Handle page routing
$page = isset($_GET['page']) ? $_GET['page'] : 'home';
$validPages = ['home', 'login', 'orders', 'new-order', 'admin', 'view-order'];

if (!in_array($page, $validPages)) {
    $page = 'home';
}

// Include header
include 'includes/header.php';

// Load page content based on route
switch ($page) {
    case 'login':
        include 'pages/login.php';
        break;
    case 'orders':
        include 'pages/order_listing.php';
        break;
    case 'new-order':
        include 'pages/order_form.php';
        break;
    case 'admin':
        if ($_SESSION['role'] === 'admin') {
            include 'pages/admin_panel.php';
        } else {
            header('Location: index.php');
        }
        break;
    case 'view-order':
        include 'pages/view_order.php';
        break;
    default:
        include 'pages/home.php';
        break;
}

// Include footer
include 'includes/footer.php';
?>
