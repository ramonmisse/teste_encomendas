
<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelId = (int)$_POST['model_id'];
    $name = sanitizeInput($_POST['name']);
    $imageUrl = sanitizeInput($_POST['image_url']);
    $description = sanitizeInput($_POST['description'] ?? '');

    try {
        $stmt = $pdo->prepare("INSERT INTO model_variations (model_id, name, image_url, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$modelId, $name, $imageUrl, $description]);
        
        $_SESSION['success'] = 'Variação adicionada com sucesso!';
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Erro ao adicionar variação: ' . $e->getMessage();
    }
}

header('Location: ../index.php?page=home&tab=admin&admin_tab=models');
exit;
?>
