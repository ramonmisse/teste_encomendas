<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $name = sanitizeInput($_POST['name']);
    $imageUrl = sanitizeInput($_POST['image_url']);
    $description = isset($_POST['description']) ? sanitizeInput($_POST['description']) : '';
    
    // Validate required fields
    if (empty($name) || empty($imageUrl)) {
        // Set error message and redirect back
        $_SESSION['error'] = 'Nome e URL da imagem são obrigatórios.';
        header('Location: ../index.php?page=home&tab=admin&admin_tab=models');
        exit;
    }
    
    try {
        // Insert new model into database
        $stmt = $pdo->prepare("INSERT INTO product_models (name, image_url, description) VALUES (?, ?, ?)");
        $stmt->execute([$name, $imageUrl, $description]);
        
        // Set success message
        $_SESSION['success'] = 'Modelo adicionado com sucesso!';
    } catch (PDOException $e) {
        // Set error message
        $_SESSION['error'] = 'Erro ao adicionar modelo: ' . $e->getMessage();
    }
}

// Redirect back to admin panel
header('Location: ../index.php?page=home&tab=admin&admin_tab=models');
exit;
?>