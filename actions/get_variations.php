
<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

if(isset($_GET['model_id'])) {
    $modelId = (int)$_GET['model_id'];
    try {
        $stmt = $pdo->prepare("SELECT id, name, image_url, description FROM model_variations WHERE model_id = ? AND status = 'active' ORDER BY name");
        $stmt->execute([$modelId]);
        $variations = $stmt->fetchAll(PDO::FETCH_ASSOC);
        
        // Ensure proper encoding of special characters
        array_walk_recursive($variations, function(&$item) {
            $item = htmlspecialchars_decode($item);
        });
        
        echo json_encode($variations, JSON_UNESCAPED_UNICODE);
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Model ID is required']);
}
