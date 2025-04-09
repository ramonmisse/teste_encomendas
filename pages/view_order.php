<?php
// Check if order ID is provided
if (!isset($_GET['id'])) {
    // Redirect to order listing if no ID provided
    header('Location: index.php?page=home&tab=orders');
    exit;
}

$orderId = (int)$_GET['id'];
$order = getOrderById($pdo, $orderId);

// If order not found, redirect to order listing
if (!$order) {
    $_SESSION['error'] = 'Pedido não encontrado.';
    header('Location: index.php?page=home&tab=orders');
    exit;
}

// Get sales representative details
$salesRep = null;
if ($order['sales_representative_id']) {
    $stmt = $pdo->prepare("SELECT * FROM sales_representatives WHERE id = ?");
    $stmt->execute([$order['sales_representative_id']]);
    $salesRep = $stmt->fetch();
}

// Get model details
$model = null;
if ($order['model_id']) {
    $stmt = $pdo->prepare("SELECT * FROM product_models WHERE id = ?");
    $stmt->execute([$order['model_id']]);
    $model = $stmt->fetch();
}

// Parse image URLs from JSON
$imageUrls = [];
if (!empty($order['image_urls'])) {
    $imageUrls = json_decode($order['image_urls'], true);
}
?>

<div class="container py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Detalhes do Pedido</h1>
        <a href="index.php?page=home&tab=orders" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-left me-2"></i> Voltar para Listagem
        </a>
    </div>
    
    <div class="card shadow-sm mb-4">
        <div class="card-header bg-white">
            <h2 class="card-title h5 mb-0">Pedido #<?php echo $order['id']; ?></h2>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <h3 class="h6 fw-bold mb-3">Informações do Pedido</h3>
                    <table class="table table-borderless">
                        <tr>
                            <th class="ps-0" style="width: 40%;">Representante:</th>
                            <td>
                                <?php if ($salesRep): ?>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($salesRep['avatar_url'])): ?>
                                            <img src="<?php echo htmlspecialchars($salesRep['avatar_url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($salesRep['name']); ?>" 
                                                 class="avatar me-2">
                                        <?php endif; ?>
                                        <?php echo htmlspecialchars($salesRep['name']); ?>
                                    </div>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($order['sales_rep']); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="ps-0">Cliente:</th>
                            <td><?php echo htmlspecialchars($order['client_name']); ?></td>
                        </tr>
                        <tr>
                            <th class="ps-0">Data do Pedido:</th>
                            <td><?php echo formatDate($order['created_at']); ?></td>
                        </tr>
                        <tr>
                            <th class="ps-0">Data de Entrega:</th>
                            <td><?php echo formatDate($order['delivery_date']); ?></td>
                        </tr>
                    </table>
                </div>
                
                <div class="col-md-6">
                    <h3 class="h6 fw-bold mb-3">Detalhes do Produto</h3>
                    <table class="table table-borderless">
                        <tr>
                            <th class="ps-0" style="width: 40%;">Modelo:</th>
                            <td>
                                <?php if ($model): ?>
                                    <?php echo htmlspecialchars($model['name']); ?>
                                <?php else: ?>
                                    <?php echo htmlspecialchars($order['model']); ?>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <tr>
                            <th class="ps-0">Tipo de Metal:</th>
                            <td>
                                <?php 
                                $metalTypeDisplay = '';
                                switch ($order['metal_type']) {
                                    case 'gold':
                                        $metalTypeDisplay = 'Ouro';
                                        break;
                                    case 'silver':
                                        $metalTypeDisplay = 'Prata';
                                        break;
                                    case 'not_applicable':
