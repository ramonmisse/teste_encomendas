<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start transaction for data integrity
    $pdo->beginTransaction();
    // Validate and sanitize inputs
    $name = sanitizeInput($_POST['name']);
    $email = sanitizeInput($_POST['email']);
    $phone = isset($_POST['phone']) ? sanitizeInput($_POST['phone']) : '';
    $avatarUrl = isset($_POST['avatar_url']) ? sanitizeInput($_POST['avatar_url']) : '';
    
    // If no avatar URL provided, generate one using DiceBear API
    if (empty($avatarUrl)) {
        $seed = strtolower(str_replace(' ', '', $name));
        $avatarUrl = "https://api.dicebear.com/7.x/avataaars/svg?seed=$seed";
    }
    
    // Validate required fields
    if (empty($name) || empty($email)) {
        // Set error message and redirect back
        $_SESSION['error'] = 'Nome e email são obrigatórios.';
        header('Location: ../index.php?page=home&tab=admin&admin_tab=reps');
        exit;
    }
    
    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = 'Formato de email inválido.';
        header('Location: ../index.php?page=home&tab=admin&admin_tab=reps');
        exit;
    }
    
    try {
        // Check if email already exists
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sales_representatives WHERE email = ?");
        $stmt->execute([$email]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            // Email already exists
            $_SESSION['error'] = 'Este email já está cadastrado para outro representante.';
        } else {
            // Insert new sales representative into database
            $stmt = $pdo->prepare("INSERT INTO sales_representatives (name, email, phone, avatar_url) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $phone, $avatarUrl]);
            
            // Commit transaction
            $pdo->commit();
            // Set success message
            $_SESSION['success'] = 'Representante adicionado com sucesso!';
        }
    } catch (PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();
        // Set error message
        $_SESSION['error'] = 'Erro ao adicionar representante: ' . $e->getMessage();
    }
}

// Redirect back to admin panel
header('Location: ../index.php?page=home&tab=admin&admin_tab=reps');
exit;
?>