<?php
// Fetch orders from database
$orders = getOrders($pdo);
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title h5 mb-0">Listagem de Pedidos</h2>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead>
                    <tr>
                        <th>Representante</th>
                        <th>Cliente</th>
                        <th>Modelo</th>
                        <th>Tipo de Metal</th>
                        <th>Data de Entrega</th>
                        <th>Imagem</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                        <tr>
                            <td colspan="7" class="text-center py-4">
                                <p class="text-muted mb-0">Nenhum pedido encontrado. Crie um novo pedido para começar.</p>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($orders as $order): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($order['sales_rep']); ?></td>
                                <td><?php echo htmlspecialchars($order['client']); ?></td>
                                <td><?php echo htmlspecialchars($order['model']); ?></td>
                                <td><?php echo htmlspecialchars($order['metal_type']); ?></td>
                                <td><?php echo formatDate($order['delivery_date']); ?></td>
                                <td>
                                    <?php if (isset($order['image_url']) && !empty($order['image_url'])): ?>
                                        <div class="hover-card">
                                            <button class="btn btn-sm btn-outline-secondary image-preview-link" data-image-url="<?php echo htmlspecialchars($order['image_url']); ?>">
                                                <i class="fas fa-image"></i>
                                            </button>
                                            <div class="hover-card-content">
                                                <img src="<?php echo htmlspecialchars($order['image_url']); ?>" alt="Order reference" class="img-fluid rounded">
                                            </div>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">Sem imagem</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <!-- View button with tooltip -->
                                        <div class="tooltip-wrapper">
                                            <a href="index.php?page=view-order&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-primary btn-icon">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <span class="tooltip-content">Ver Pedido</span>
                                        </div>
                                        
                                        <!-- Edit button with tooltip -->
                                        <div class="tooltip-wrapper">
                                            <a href="index.php?page=edit-order&id=<?php echo $order['id']; ?>" class="btn btn-sm btn-outline-secondary btn-icon">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <span class="tooltip-content">Editar Pedido</span>
                                        </div>
                                        
                                        <?php if (isset($order['image_url']) && !empty($order['image_url'])): ?>
                                            <!-- Download button with tooltip -->
                                            <div class="tooltip-wrapper">
                                                <a href="<?php echo htmlspecialchars($order['image_url']); ?>" download class="btn btn-sm btn-outline-info btn-icon">
                                                    <i class="fas fa-download"></i>
                                                </a>
                                                <span class="tooltip-content">Baixar Imagem</span>
                                            </div>
                                        <?php endif; ?>
                                        
                                        <!-- Delete button with tooltip -->
                                        <div class="tooltip-wrapper">
                                            <button class="btn btn-sm btn-outline-danger btn-icon delete-btn" data-id="<?php echo $order['id']; ?>">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                            <span class="tooltip-content">Excluir Pedido</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Image Preview Modal -->
<div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-labelledby="imagePreviewModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="imagePreviewModalLabel">Imagem de Referência</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <img id="previewImage" src="" alt="Preview" class="img-fluid rounded">
            </div>
            <div class="modal-footer">
                <a id="downloadImageLink" href="" download class="btn btn-primary">
                    <i class="fas fa-download me-1"></i> Baixar Imagem
                </a>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteConfirmModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este pedido? Esta ação não pode ser desfeita.</p>
                <form id="deleteForm" action="actions/delete_order.php" method="post">
                    <input type="hidden" name="id" value="">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="deleteForm" class="btn btn-danger">Excluir</button>
            </div>
        </div>
    </div>
</div>