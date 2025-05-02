
<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

if(isset($_GET['model_id'])) {
    $modelId = filter_input(INPUT_GET, 'model_id', FILTER_VALIDATE_INT);
    
    if (!$modelId) {
        http_response_code(400);
        echo json_encode(['error' => 'ID do modelo inválido']);
        exit;
    }

    try {
        // First check if model exists
        $stmt = $pdo->prepare("SELECT id FROM product_models WHERE id = ?");
        $stmt->execute([$modelId]);
        if (!$stmt->fetch()) {
            http_response_code(404);
            echo json_encode(['error' => 'Modelo não encontrado']);
            exit;
        }

        // Get active variations
        $stmt = $pdo->prepare("SELECT id, name, image_url, description FROM model_variations WHERE model_id = ? AND status = 'active' ORDER BY name");
        $stmt->execute([$modelId]);
        $variations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Always return array, even if empty
        echo json_encode($variations ?: [], JSON_UNESCAPED_UNICODE);
    } catch(PDOException $e) {
        error_log('Database error: ' . $e->getMessage());
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao buscar variações']);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'ID do modelo é obrigatório']);
}
