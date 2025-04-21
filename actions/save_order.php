<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

// Enable error reporting for debugging
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Check if form was submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Debug: Print POST data
    error_log('POST data: ' . print_r($_POST, true));
    
    // Start transaction for data integrity
    $pdo->beginTransaction();
    // Validate and sanitize inputs
    $userId = (int)$_SESSION['user_id'];
    $companyId = (int)$_SESSION['company_id'];
    $clientName = sanitizeInput($_POST['client_name']);
    
    // Combine date and time for delivery date
    $deliveryDate = sanitizeInput($_POST['delivery_date']);
    $deliveryTime = isset($_POST['delivery_time']) ? sanitizeInput($_POST['delivery_time']) : '00:00';
    $deliveryDateTime = $deliveryDate . ' ' . $deliveryTime;
    
    $modelId = (int)$_POST['model_id'];
    $metalType = sanitizeInput($_POST['metal_type']);
    $status = sanitizeInput($_POST['status']);
    $notes = isset($_POST['notes']) ? sanitizeInput($_POST['notes']) : '';
    
    // Validate required fields
    if (empty($userId) || empty($clientName) || empty($deliveryDate) || empty($modelId) || empty($metalType) || empty($companyId)) {
        // Set error message and redirect back
        $_SESSION['error'] = 'Todos os campos obrigatórios devem ser preenchidos.';
        header('Location: ../index.php?page=home&tab=new-order');
        exit;
    }
    
    // Process uploaded images
    $imageUrls = [];
    if (isset($_FILES['images']) && !empty($_FILES['images']['name'][0])) {
        $uploadDir = '../uploads/';
        
        // Create uploads directory if it doesn't exist
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
            // Set proper permissions for the uploads directory
            chmod($uploadDir, 0777);
        }
        
        // Process each uploaded file
        foreach ($_FILES['images']['tmp_name'] as $key => $tmp_name) {
            if ($_FILES['images']['error'][$key] === 0) {
                $fileName = time() . '_' . basename($_FILES['images']['name'][$key]);
                $filePath = $uploadDir . $fileName;
                
                // Move uploaded file to destination
                if (move_uploaded_file($tmp_name, $filePath)) {
                    $imageUrls[] = 'uploads/' . $fileName;
                    // Set proper permissions for the uploaded file
                    chmod($filePath, 0644);
                }
            }
        }
    }
    
    // Convert image URLs array to JSON for storage
    $imageUrlsJson = !empty($imageUrls) ? json_encode($imageUrls) : null;
    
    try {
        // Check if editing existing order or creating new one
        if (isset($_POST['id']) && !empty($_POST['id'])) {
            // Update existing order
            $id = (int)$_POST['id'];
            
            // Get existing image URLs if no new images uploaded
            if (empty($imageUrls)) {
                $stmt = $pdo->prepare("SELECT image_urls FROM orders WHERE id = ?");
                $stmt->execute([$id]);
                $imageUrlsJson = $stmt->fetchColumn();
            }
            
            $stmt = $pdo->prepare("UPDATE orders SET 
                user_id = ?, 
                client_name = ?, 
                delivery_date = ?, 
                model_id = ?, 
                metal_type = ?,
                status = ?,
                notes = ?, 
                image_urls = ?
                WHERE id = ?");
            $stmt->execute([$salesRepId, $clientName, $deliveryDateTime, $modelId, $metalType, $status, $notes, $imageUrlsJson, $id]);
            
            // Commit transaction
            $pdo->commit();
            $_SESSION['success'] = 'Pedido atualizado com sucesso!';
        } else {
            // Insert new order
            $stmt = $pdo->prepare("INSERT INTO orders 
                (sales_representative_id, client_name, delivery_date, model_id, metal_type, status, notes, image_urls, created_at) 
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
            $stmt->execute([$salesRepId, $clientName, $deliveryDateTime, $modelId, $metalType, $status, $notes, $imageUrlsJson]);
            
            // Commit transaction
            $pdo->commit();
            $_SESSION['success'] = 'Pedido criado com sucesso!';
        }
    } catch (PDOException $e) {
        // Rollback transaction
        $pdo->rollBack();
        // Set error message
        $_SESSION['error'] = 'Erro ao salvar pedido: ' . $e->getMessage();
    }
}

// Redirect back to order listing
header('Location: ../index.php?page=home&tab=orders');
exit;
?>