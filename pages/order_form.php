<?php
// Check if we're editing an existing order
$isEditing = false;
$order = null;

if (isset($_GET['id'])) {
    $orderId = (int)$_GET['id'];
    $order = getOrderById($pdo, $orderId);
    $isEditing = ($order !== false);
}

// Get product models and sales representatives from database
$models = getProductModels($pdo);
$salesReps = $pdo->query("SELECT * FROM users WHERE role = 'user' ORDER BY username")->fetchAll();
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title h5 mb-0"><?php echo $isEditing ? 'Editar Pedido' : 'Criar Novo Pedido'; ?></h2>
    </div>
    <div class="card-body">
        <?php if (empty($salesReps)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Nenhum representante de vendas cadastrado. Por favor, <a href="index.php?page=home&tab=admin&admin_tab=reps" class="alert-link">adicione um representante</a> antes de criar um pedido.
            </div>
        <?php elseif (empty($models)): ?>
            <div class="alert alert-warning">
                <i class="fas fa-exclamation-triangle me-2"></i>
                Nenhum modelo de produto cadastrado. Por favor, <a href="index.php?page=home&tab=admin&admin_tab=models" class="alert-link">adicione um modelo</a> antes de criar um pedido.
            </div>
        <?php else: ?>
            <form action="actions/save_order.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
                <?php if ($isEditing): ?>
                    <input type="hidden" name="id" value="<?php echo $order['id']; ?>">
                <?php endif; ?>
                
                <div class="row mb-4">
                    <input type="hidden" name="user_id" value="<?php echo $_SESSION['user_id']; ?>">
                    <input type="hidden" name="company_id" value="<?php echo $_SESSION['company_id']; ?>">
                    
                    <!-- Client Name -->
                    <div class="col-md-6 mb-3">
                        <label for="clientName" class="form-label">Nome do Cliente</label>
                        <input type="text" class="form-control" id="clientName" name="client_name" 
                               value="<?php echo $isEditing ? htmlspecialchars($order['client_name']) : ''; ?>" 
                               placeholder="Digite o nome do cliente" required>
                        <div class="invalid-feedback">Por favor, informe o nome do cliente.</div>
                    </div>
                    
                    <!-- Order Date (Read-only) -->
                    <div class="col-md-6 mb-3">
                        <label for="orderDate" class="form-label">Data do Pedido</label>
                        <input type="text" class="form-control" id="orderDate" 
                               value="<?php echo date('d/m/Y H:i'); ?>" readonly>
                        <small class="text-muted">Data e hora atuais (definidas automaticamente)</small>
                    </div>
                    
                    <!-- Delivery Date -->
                    <div class="col-md-6 mb-3">
                        <label for="deliveryDate" class="form-label">Data e Hora de Entrega</label>
                        <div class="row">
                            <div class="col-md-8">
                                <input type="date" class="form-control" id="deliveryDate" name="delivery_date" 
                                       value="<?php echo $isEditing ? date('Y-m-d', strtotime($order['delivery_date'])) : ''; ?>" 
                                       min="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <input type="time" class="form-control" id="deliveryTime" name="delivery_time" 
                                       value="<?php echo $isEditing ? date('H:i', strtotime($order['delivery_date'])) : ''; ?>" required>
                            </div>
                        </div>
                        <div class="invalid-feedback">Por favor, selecione uma data e hora de entrega válida.</div>
                    </div>
                </div>
                
                <!-- Product Model Selection -->
                <div class="mb-4">
                    <label class="form-label">Modelo do Produto</label>
                    <input type="hidden" id="modelInput" name="model_id" 
                           value="<?php echo $isEditing ? $order['model_id'] : ''; ?>" required>

                <!-- Status -->
                <div class="mb-4">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status" required>
                        <option value="Em produção" <?php echo ($isEditing && $order['status'] == 'Em produção') ? 'selected' : ''; ?>>Em produção</option>
                        <option value="Gravado" <?php echo ($isEditing && $order['status'] == 'Gravado') ? 'selected' : ''; ?>>Gravado</option>
                        <option value="Separado" <?php echo ($isEditing && $order['status'] == 'Separado') ? 'selected' : ''; ?>>Separado</option>
                        <option value="Enviado" <?php echo ($isEditing && $order['status'] == 'Enviado') ? 'selected' : ''; ?>>Enviado</option>
                        <option value="Entregue" <?php echo ($isEditing && $order['status'] == 'Entregue') ? 'selected' : ''; ?>>Entregue</option>
                    </select>
                </div>

                    
                    <div class="mb-3" id="variationSelect" style="display: none;">
                        <label class="form-label">Variação do Modelo</label>
                        <select class="form-select" name="variation_id">
                            <option value="">Selecione uma variação</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <input type="text" class="form-control" id="modelSearch" placeholder="Buscar modelo...">
                    </div>
                    <div class="row row-cols-1 row-cols-sm-2 row-cols-md-4 g-3" id="modelGrid">
                        <?php foreach ($models as $model): ?>
                            <div class="col model-item" data-model-name="<?php echo strtolower(htmlspecialchars($model['name'])); ?>">
                                <div class="card model-card h-100 <?php echo ($isEditing && $order['model_id'] == $model['id']) ? 'selected' : ''; ?>" 
                                     data-model-id="<?php echo $model['id']; ?>">
                                    <div class="card-body p-2 text-center">
                                        <img src="<?php echo htmlspecialchars($model['image_url']); ?>" 
                                             alt="<?php echo htmlspecialchars($model['name']); ?>" 
                                             class="img-fluid model-image">
                                        <p class="card-text fw-medium"><?php echo htmlspecialchars($model['name']); ?></p>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <div class="mb-3 mt-3" id="variationSelect" style="display: none;">
                        <label class="form-label required">Variação do Modelo</label>
                        <select class="form-select" name="variation_id" required>
                            <option value="">Selecione uma variação</option>
                        </select>
                    </div>
                    <div class="invalid-feedback">Por favor, selecione um modelo.</div>
                </div>
                
                <!-- Metal Type -->
                <div class="mb-4">
                    <label for="metalType" class="form-label">Tipo de Metal</label>
                    <select class="form-select" id="metalType" name="metal_type" required>
                        <option value="" selected disabled>Selecione o tipo de metal</option>
                        <option value="gold" <?php echo ($isEditing && $order['metal_type'] == 'gold') ? 'selected' : ''; ?>>Ouro</option>
                        <option value="silver" <?php echo ($isEditing && $order['metal_type'] == 'silver') ? 'selected' : ''; ?>>Prata</option>
                        <option value="not_applicable" <?php echo ($isEditing && $order['metal_type'] == 'not_applicable') ? 'selected' : ''; ?>>Não Aplicável</option>
                    </select>
                    <div class="invalid-feedback">Por favor, selecione um tipo de metal.</div>
                </div>
                
                <!-- Image Upload -->
                <div class="mb-4">
                    <label class="form-label">Fotos de Personalização</label>
                    <div class="input-group mb-3">
                        <label class="input-group-text" for="image-upload">
                            <i class="fas fa-upload me-2"></i> Enviar Imagens
                        </label>
                        <input type="file" class="form-control" id="image-upload" name="images[]" multiple accept="image/*">
                    </div>
                    <small class="text-muted">Você pode selecionar várias imagens.</small>
                    
                    <!-- Image preview container -->
                    <div class="row mt-3" id="image-preview-container">
                        <?php if ($isEditing && isset($order['image_urls'])): ?>
                            <?php 
                                $imageUrlsArray = json_decode($order['image_urls'], true);
                                if (is_array($imageUrlsArray)) {
                                    foreach ($imageUrlsArray as $index => $image): 
                            ?>
                                <div class="col-6 col-md-3 mb-3 position-relative">
                                    <div class="card h-100">
                                        <img src="<?php echo htmlspecialchars($image); ?>" class="card-img-top" alt="Preview">
                                        <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-1 remove-image" data-index="<?php echo $index; ?>">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                </div>
                            <?php 
                                    endforeach;
                                }
                            ?>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Notes -->
                <div class="mb-4">
                    <label for="notes" class="form-label">Observações</label>
                    <textarea class="form-control" id="notes" name="notes" rows="4" 
                              placeholder="Adicione informações adicionais sobre o pedido"><?php echo $isEditing ? htmlspecialchars($order['notes']) : ''; ?></textarea>
                </div>
                
                <!-- Submit Button -->
                <div class="text-end">
                    <button type="submit" class="btn btn-primary">
                        <?php echo $isEditing ? 'Atualizar Pedido' : 'Salvar Pedido'; ?>
                    </button>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<script>
// Form validation script
(function() {
    'use strict';
    
    // Model search functionality
    const modelSearch = document.getElementById('modelSearch');
    const modelItems = document.querySelectorAll('.model-item');
    
    modelSearch.addEventListener('input', function(e) {
        const searchTerm = e.target.value.toLowerCase();
        
        modelItems.forEach(item => {
            const modelName = item.dataset.modelName;
            if (modelName.includes(searchTerm)) {
                item.style.display = '';
            } else {
                item.style.display = 'none';
            }
        });
    });
    
    // Fetch all forms we want to apply validation to
    var forms = document.querySelectorAll('.needs-validation');
    
    // Model card selection
    var modelCards = document.querySelectorAll('.model-card');
    var modelInput = document.getElementById('modelInput');
    
    // Add click event to model cards
    modelCards.forEach(function(card) {
        card.addEventListener('click', function() {
            // Remove selected class from all cards
            modelCards.forEach(function(c) {
                c.classList.remove('selected');
            });
            // Add selected class to clicked card
            this.classList.add('selected');
            // Set model ID in hidden input
            const modelId = this.getAttribute('data-model-id');
            modelInput.value = modelId;
            
            // Load variations for selected model
            const variationSelect = document.getElementById('variationSelect');
            const variationSelectElement = variationSelect.querySelector('select');
            
            // Load variations for selected model
            fetch(`actions/get_variations.php?model_id=${modelId}`)
                .then(response => response.json())
                .then(variations => {
                    const variationSelect = document.getElementById('variationSelect');
                    const variationSelectElement = variationSelect.querySelector('select');
                    
                    if (variations.length > 0) {
                        variationSelectElement.innerHTML = '<option value="">Selecione uma variação</option>' +
                            variations.map(v => `<option value="${v.id}">${v.name}</option>`).join('');
                        variationSelect.style.display = 'block';
                        variationSelectElement.required = true;
                    } else {
                        variationSelect.style.display = 'none';
                        variationSelectElement.required = false;
                        variationSelectElement.innerHTML = '<option value="">Selecione uma variação</option>';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    variationSelect.style.display = 'none';
                });
        });
    });
    
    // Loop over forms and prevent submission
    Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
            // Check if model is selected
            if (!modelInput.value) {
                event.preventDefault();
                event.stopPropagation();
                // Show validation message
                modelInput.parentElement.querySelector('.invalid-feedback').style.display = 'block';
            }
            
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            form.classList.add('was-validated');
        }, false);
    });
})();
</script>