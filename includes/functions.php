<?php
/**
 * Get all orders from the database
 * 
 * @param PDO $pdo Database connection
 * @return array Array of orders
 */
function getOrders($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM orders ORDER BY created_at DESC");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        // For development, show error. For production, log error and show generic message
        return [];
    }
}

/**
 * Get a single order by ID
 * 
 * @param PDO $pdo Database connection
 * @param int $id Order ID
 * @return array|false Order data or false if not found
 */
function getOrderById($pdo, $id) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    } catch(PDOException $e) {
        return false;
    }
}

/**
 * Get all sales representatives
 * 
 * @param PDO $pdo Database connection
 * @return array Array of sales representatives
 */
function getSalesReps($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM sales_representatives ORDER BY name");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Get all product models
 * 
 * @param PDO $pdo Database connection
 * @return array Array of product models
 */
function getProductModels($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM product_models ORDER BY name");
        return $stmt->fetchAll();
    } catch(PDOException $e) {
        return [];
    }
}

/**
 * Format date for display
 * 
 * @param string $date Date string
 * @return string Formatted date
 */
function formatDate($date) {
    return date("d/m/Y", strtotime($date));
}

/**
 * Sanitize input data
 * 
 * @param string $data Data to sanitize
 * @return string Sanitized data
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}
?>