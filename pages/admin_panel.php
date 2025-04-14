<?php
// Get active admin tab from query string or set default to 'models'
$adminTab = isset($_GET['admin_tab']) ? $_GET['admin_tab'] : 'models';

// Get product models from database
$models = getProductModels($pdo);

// Get sales representatives from database
$salesReps = getSalesReps($pdo);
?>

<div class="card">
    <div class="card-header">
        <h2 class="card-title h5 mb-0">Painel de Administração</h2>
    </div>
    <div class="card-body">
        <!-- Admin Tabs -->
        <ul class="nav nav-tabs mb-4" id="adminTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <a class="nav-link <?php echo $adminTab == 'models' ? 'active' : ''; ?>" 
                   href="index.php?page=home&tab=admin&admin_tab=models" role="tab">Modelos de Produtos</a>
            </li>
            <li class="nav-item" role="presentation">
                <a class="nav-link <?php echo $adminTab == 'reps' ? 'active' : ''; ?>" 
                   href="index.php?page=home&tab=admin&admin_tab=reps" role="tab">Representantes de Vendas</a>
            </li>
        </ul>
        
        <!-- Tab Content -->
        <div class="tab-content" id="adminTabsContent">
            <!-- Models Tab -->
            <div class="tab-pane fade <?php echo $adminTab == 'models' ? 'show active' : ''; ?>" id="models" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h5 mb-0">Modelos de Produtos</h3>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addModelModal">
                        <i class="fas fa-plus-circle me-1"></i> Adicionar Modelo
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Visualização</th>
                                <th>Nome</th>
                                <th>Descrição</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($models)): ?>
                                <tr>
                                    <td colspan="4" class="text-center py-4">
                                        <p class="text-muted mb-0">Nenhum modelo cadastrado. Adicione um modelo para começar.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($models as $model): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <img src="<?php echo htmlspecialchars($model['image_url']); ?>" 
                                                     alt="<?php echo htmlspecialchars($model['name']); ?>" 
                                                     class="image-thumbnail me-2">
                                            </div>
                                        </td>
                                        <td class="fw-medium"><?php echo htmlspecialchars($model['name']); ?></td>
                                        <td><?php echo htmlspecialchars($model['description']); ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-icon edit-model-btn" 
                                                        data-id="<?php echo $model['id']; ?>" 
                                                        data-name="<?php echo htmlspecialchars($model['name']); ?>" 
                                                        data-image-url="<?php echo htmlspecialchars($model['image_url']); ?>" 
                                                        data-description="<?php echo htmlspecialchars($model['description']); ?>" 
                                                        data-bs-toggle="modal" data-bs-target="#editModelModal">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-icon delete-model-btn" 
                                                        data-id="<?php echo $model['id']; ?>" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteModelModal">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
            
            <!-- Sales Representatives Tab -->
            <div class="tab-pane fade <?php echo $adminTab == 'reps' ? 'show active' : ''; ?>" id="reps" role="tabpanel">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h3 class="h5 mb-0">Representantes de Vendas</h3>
                    <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#addRepModal">
                        <i class="fas fa-plus-circle me-1"></i> Adicionar Representante
                    </button>
                </div>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Avatar</th>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                                <th class="text-end">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($salesReps)): ?>
                                <tr>
                                    <td colspan="5" class="text-center py-4">
                                        <p class="text-muted mb-0">Nenhum representante cadastrado. Adicione um representante para começar.</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($salesReps as $rep): ?>
                                    <tr>
                                        <td>
                                            <img src="<?php echo htmlspecialchars($rep['avatar_url']); ?>" 
                                                 alt="<?php echo htmlspecialchars($rep['name']); ?>" 
                                                 class="avatar">
                                        </td>
                                        <td class="fw-medium"><?php echo htmlspecialchars($rep['name']); ?></td>
                                        <td><?php echo htmlspecialchars($rep['email']); ?></td>
                                        <td><?php echo htmlspecialchars($rep['phone']); ?></td>
                                        <td class="text-end">
                                            <div class="d-flex justify-content-end gap-1">
                                                <button type="button" class="btn btn-sm btn-outline-secondary btn-icon edit-rep-btn" 
                                                        data-id="<?php echo $rep['id']; ?>" 
                                                        data-name="<?php echo htmlspecialchars($rep['name']); ?>" 
                                                        data-email="<?php echo htmlspecialchars($rep['email']); ?>" 
                                                        data-phone="<?php echo htmlspecialchars($rep['phone']); ?>" 
                                                        data-avatar-url="<?php echo htmlspecialchars($rep['avatar_url']); ?>" 
                                                        data-bs-toggle="modal" data-bs-target="#editRepModal">
                                                    <i class="fas fa-pencil-alt"></i>
                                                </button>
                                                <button type="button" class="btn btn-sm btn-outline-danger btn-icon delete-rep-btn" 
                                                        data-id="<?php echo $rep['id']; ?>" 
                                                        data-bs-toggle="modal" data-bs-target="#deleteRepModal">
                                                    <i class="fas fa-trash"></i>
                                                </button>
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
    </div>
</div>

<!-- Add Model Modal -->
<div class="modal fade" id="addModelModal" tabindex="-1" aria-labelledby="addModelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addModelModalLabel">Adicionar Novo Modelo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="actions/add_model.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="modelName" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="modelName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="modelImageUrl" class="form-label">URL da Imagem</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="modelImageUrl" name="image_url" required>
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-upload"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="modelDescription" class="form-label">Descrição</label>
                        <textarea class="form-control" id="modelDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar Modelo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Model Modal -->
<div class="modal fade" id="editModelModal" tabindex="-1" aria-labelledby="editModelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editModelModalLabel">Editar Modelo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="actions/edit_model.php" method="post">
                <input type="hidden" name="id" id="editModelId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editModelName" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="editModelName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editModelImageUrl" class="form-label">URL da Imagem</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="editModelImageUrl" name="image_url" required>
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-upload"></i>
                            </button>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="editModelDescription" class="form-label">Descrição</label>
                        <textarea class="form-control" id="editModelDescription" name="description" rows="3"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar Modelo</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Model Modal -->
<div class="modal fade" id="deleteModelModal" tabindex="-1" aria-labelledby="deleteModelModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModelModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este modelo? Esta ação não pode ser desfeita.</p>
                <form id="deleteModelForm" action="actions/delete_model.php" method="post">
                    <input type="hidden" name="id" id="deleteModelId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="deleteModelForm" class="btn btn-danger">Excluir</button>
            </div>
        </div>
    </div>
</div>

<!-- Add Rep Modal -->
<div class="modal fade" id="addRepModal" tabindex="-1" aria-labelledby="addRepModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addRepModalLabel">Adicionar Novo Representante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="actions/add_rep.php" method="post">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="repName" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="repName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="repEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="repEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="repPhone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="repPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="repAvatarUrl" class="form-label">URL do Avatar</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="repAvatarUrl" name="avatar_url">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-upload"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Adicionar Representante</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Rep Modal -->
<div class="modal fade" id="editRepModal" tabindex="-1" aria-labelledby="editRepModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editRepModalLabel">Editar Representante</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form action="actions/edit_rep.php" method="post">
                <input type="hidden" name="id" id="editRepId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="editRepName" class="form-label">Nome</label>
                        <input type="text" class="form-control" id="editRepName" name="name" required>
                    </div>
                    <div class="mb-3">
                        <label for="editRepEmail" class="form-label">Email</label>
                        <input type="email" class="form-control" id="editRepEmail" name="email" required>
                    </div>
                    <div class="mb-3">
                        <label for="editRepPhone" class="form-label">Telefone</label>
                        <input type="text" class="form-control" id="editRepPhone" name="phone">
                    </div>
                    <div class="mb-3">
                        <label for="editRepAvatarUrl" class="form-label">URL do Avatar</label>
                        <div class="input-group">
                            <input type="text" class="form-control" id="editRepAvatarUrl" name="avatar_url">
                            <button class="btn btn-outline-secondary" type="button">
                                <i class="fas fa-upload"></i>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Atualizar Representante</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Rep Modal -->
<div class="modal fade" id="deleteRepModal" tabindex="-1" aria-labelledby="deleteRepModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteRepModalLabel">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir este representante? Esta ação não pode ser desfeita.</p>
                <form id="deleteRepForm" action="actions/delete_rep.php" method="post">
                    <input type="hidden" name="id" id="deleteRepId">
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="submit" form="deleteRepForm" class="btn btn-danger">Excluir</button>
            </div>
        </div>
    </div>
</div>

<script>
// Script to handle modal data for editing models and reps
document.addEventListener('DOMContentLoaded', function() {
    // Edit model button click
    const editModelBtns = document.querySelectorAll('.edit-model-btn');
    editModelBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const imageUrl = this.getAttribute('data-image-url');
            const description = this.getAttribute('data-description');
            
            document.getElementById('editModelId').value = id;
            document.getElementById('editModelName').value = name;
            document.getElementById('editModelImageUrl').value = imageUrl;
            document.getElementById('editModelDescription').value = description;
        });
    });
    
    // Delete model button click
    const deleteModelBtns = document.querySelectorAll('.delete-model-btn');
    deleteModelBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('deleteModelId').value = id;
        });
    });
    
    // Edit rep button click
    const editRepBtns = document.querySelectorAll('.edit-rep-btn');
    editRepBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            const name = this.getAttribute('data-name');
            const email = this.getAttribute('data-email');
            const phone = this.getAttribute('data-phone');
            const avatarUrl = this.getAttribute('data-avatar-url');
            
            document.getElementById('editRepId').value = id;
            document.getElementById('editRepName').value = name;
            document.getElementById('editRepEmail').value = email;
            document.getElementById('editRepPhone').value = phone;
            document.getElementById('editRepAvatarUrl').value = avatarUrl;
        });
    });
    
    // Delete rep button click
    const deleteRepBtns = document.querySelectorAll('.delete-rep-btn');
    deleteRepBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            const id = this.getAttribute('data-id');
            document.getElementById('deleteRepId').value = id;
        });
    });
});
</script>