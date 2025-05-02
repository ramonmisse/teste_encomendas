
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
        $stmt = $pdo->prepare("SELECT id, name, image_url, description FROM model_variations WHERE model_id = ? AND status = 'active' ORDER BY name");
        $stmt->execute([$modelId]);
        $variations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        if (empty($variations)) {
            echo json_encode([]);
        } else {
            array_walk_recursive($variations, function(&$item) {
                if (is_string($item)) {
                    $item = htmlspecialchars_decode($item, ENT_QUOTES);
                }
            });
            echo json_encode($variations, JSON_UNESCAPED_UNICODE);
        }
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => 'Erro ao buscar variações: ' . $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'ID do modelo é obrigatório']);
}
