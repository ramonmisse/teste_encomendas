<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Start transaction for data integrity
    $pdo->beginTransaction();
    
    try {
        // Validate and sanitize inputs
        $repData = [
            'name' => sanitizeInput($_POST['name'] ?? ''),
            'email' => sanitizeInput($_POST['email'] ?? ''),
            'phone' => sanitizeInput($_POST['phone'] ?? ''),
            'avatar_url' => sanitizeInput($_POST['avatar_url'] ?? '')
        ];
        
        // Validate phone format if provided
        if (!empty($repData['phone']) && !validatePhone($repData['phone'])) {
            throw new Exception('Formato de telefone inválido. Use apenas números, parênteses, espaços e hífens.');
        }
        
        // Validate avatar URL format if provided
        if (!empty($repData['avatar_url']) && !validateUrl($repData['avatar_url'])) {
            throw new Exception('URL do avatar inválida. Por favor, forneça uma URL válida.');
        }
        
        // Add sales rep using the function
        $result = addSalesRep($pdo, $repData);
        
        if ($result['status'] === 'success') {
            // Commit transaction
            $pdo->commit();
            $_SESSION['success'] = $result['message'];
        } else {
            // Rollback transaction
            $pdo->rollBack();
            $_SESSION['error'] = $result['message'];
        }
    } catch (Exception $e) {
        // Rollback transaction
        $pdo->rollBack();
        // Set error message
        $_SESSION['error'] = 'Erro ao adicionar representante: ' . $e->getMessage();
        error_log('Error in add_rep.php: ' . $e->getMessage());
    }
}

// Redirect back to admin panel
header('Location: ../index.php?page=home&tab=admin&admin_tab=reps');
exit;
?>