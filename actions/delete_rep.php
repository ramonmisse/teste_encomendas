<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate and sanitize input
    $id = (int)$_POST['id'];
    
    // Validate required field
    if (empty($id)) {
        // Set error message and redirect back
        $_SESSION['error'] = 'ID do representante é obrigatório.';
        header('Location: ../index.php?page=home&tab=admin&admin_tab=reps');
        exit;
    }
    
    try {
        // Check if representative is assigned to any orders
        $stmt = $pdo->prepare("SELECT COUNT(*) FROM orders WHERE sales_representative_id = ?");
        $stmt->execute([$id]);
        $count = $stmt->fetchColumn();
        
        if ($count > 0) {
            // Representative is assigned to orders, cannot delete
            $_SESSION['error'] = 'Este representante não pode ser excluído porque está associado a pedidos.';
        } else {
            // Delete representative from database
            $stmt = $pdo->prepare("DELETE FROM sales_representatives WHERE id = ?");
            $stmt->execute([$id]);
            
            // Set success message
            $_SESSION['success'] = 'Representante excluído com sucesso!';
        }
    } catch (PDOException $e) {
        // Set error message
        $_SESSION['error'] = 'Erro ao excluir representante: ' . $e->getMessage();
    }
}

// Redirect back to admin panel
header('Location: ../index.php?page=home&tab=admin&admin_tab=reps');
exit;
?>