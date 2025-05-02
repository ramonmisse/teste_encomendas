
<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/functions.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $modelId = filter_input(INPUT_POST, 'model_id', FILTER_VALIDATE_INT);
    $name = trim(filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING));
    $imageUrl = trim(filter_input(INPUT_POST, 'image_url', FILTER_SANITIZE_URL));
    $description = trim(filter_input(INPUT_POST, 'description', FILTER_SANITIZE_STRING));

    // Validate required fields
    if (!$modelId || empty($name) || empty($imageUrl)) {
        $_SESSION['error'] = 'Todos os campos obrigatórios devem ser preenchidos.';
        header('Location: ../index.php?page=home&tab=admin&admin_tab=models');
        exit;
    }

    try {
        // Verify if model exists
        $stmt = $pdo->prepare("SELECT id FROM product_models WHERE id = ?");
        $stmt->execute([$modelId]);
        if (!$stmt->fetch()) {
            throw new Exception('Modelo não encontrado.');
        }

        // Insert variation
        $stmt = $pdo->prepare("INSERT INTO model_variations (model_id, name, image_url, description, status) VALUES (?, ?, ?, ?, 'active')");
        $stmt->execute([$modelId, $name, $imageUrl, $description]);
        
        $_SESSION['success'] = 'Variação adicionada com sucesso!';
        echo json_encode(['status' => 'success']);
    } catch (PDOException $e) {
        $_SESSION['error'] = 'Erro ao adicionar variação: ' . $e->getMessage();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
    exit;
}
