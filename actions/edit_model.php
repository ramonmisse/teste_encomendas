<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $id = (int)$_POST['id'];
    $name = sanitizeInput($_POST['name']);
    $imageUrl = sanitizeInput($_POST['image_url']);
    $description = isset($_POST['description']) ? sanitizeInput($_POST['description']) : '';
    
    // Validate required fields
    if (empty($id) || empty($name) || empty($imageUrl)) {
        // Set error message and redirect back
        $_SESSION['error'] = 'ID, nome e URL da imagem são obrigatórios.';
        header('Location: ../index.php?page=home&tab=admin&admin_tab=models');
        exit;
    }
    
    try {
        // Update model in database
        $stmt = $pdo->prepare("UPDATE product_models SET name = ?, image_url = ?, description = ? WHERE id = ?");
        $stmt->execute([$name, $imageUrl, $description, $id]);
        
        // Set success message
        $_SESSION['success'] = 'Modelo atualizado com sucesso!';
    } catch (PDOException $e) {
        // Set error message
        $_SESSION['error'] = 'Erro ao atualizar modelo: ' . $e->getMessage();
    }
}

// Redirect back to admin panel
header('Location: ../index.php?page=home&tab=admin&admin_tab=models');
exit;
?>