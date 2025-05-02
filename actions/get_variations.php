
<?php
require_once '../includes/config.php';
header('Content-Type: application/json');

if(isset($_GET['model_id'])) {
    $modelId = (int)$_GET['model_id'];
    try {
        $stmt = $pdo->prepare("SELECT * FROM model_variations WHERE model_id = ? AND status = 'active'");
        $stmt->execute([$modelId]);
        echo json_encode($stmt->fetchAll());
    } catch(PDOException $e) {
        http_response_code(500);
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    http_response_code(400);
    echo json_encode(['error' => 'Model ID is required']);
}
