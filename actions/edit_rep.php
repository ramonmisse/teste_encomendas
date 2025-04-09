<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize inputs
    $id = (int)$_POST['id'];
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
    if (empty($id) || empty($name) || empty($email)) {
        // Set error message and redirect back
        $_SESSION['error'] = 'ID, nome e email são obrigatórios.';
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
        // Check if email already exists for another representative
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM sales_representatives WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            // Email already exists for another representative
            $_SESSION['error'] = 'Este email já está cadastrado para outro representante.';
        } else {
            // Update sales representative in database
            $stmt = $pdo->prepare("UPDATE sales_representatives SET name = ?, email = ?, phone = ?, avatar_url = ? WHERE id = ?");
            $stmt->execute([$name, $email, $phone, $avatarUrl, $id]);
            
            // Set success message
            $_SESSION['success'] = 'Representante atualizado com sucesso!';
        }
    } catch (PDOException $e) {
        // Set error message
        $_SESSION['error'] = 'Erro ao atualizar representante: ' . $e->getMessage();
    }
}

// Redirect back to admin panel
header('Location: ../index.php?page=home&tab=admin&admin_tab=reps');
exit;
?>