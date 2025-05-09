<?php
// Get active admin tab from query string or set default to 'models'
$adminTab = isset($_GET['admin_tab']) ? $_GET['admin_tab'] : 'models';

//Check for admin permissions
if ($_SESSION['role'] !== 'admin') {
    header('Location: index.php');
    exit;
}

// Get product models from database
$models = getProductModels($pdo);

// Get companies and users
$companies = $pdo->query("SELECT * FROM companies ORDER BY name")->fetchAll();
$users = $pdo->query("SELECT u.*, c.name as company_name FROM users u LEFT JOIN companies c ON u.company_id = c.id ORDER BY username")->fetchAll();

// Add company
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_company'])) {
    $stmt = $pdo->prepare("INSERT INTO companies (name, email, phone) VALUES (?, ?, ?)");
    $stmt->execute([$_POST['name'], $_POST['email'], $_POST['phone']]);
    header('Location: index.php?page=admin_panel');
    exit;
}

// Add user
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add_user'])) {
    $hashedPassword = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO users (username, password, role, company_id) VALUES (?, ?, ?, ?)");
    $stmt->execute([$_POST['username'], $hashedPassword, $_POST['role'], $_POST['company_id']]);
    header('Location: index.php?page=admin_panel');
    exit;
}
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
                                            <div class="d-flex justify-content-end gap-2">
                                                <button class="btn btn-sm btn-outline-secondary add-variation-btn"
                                                        data-id="<?php echo $model['id']; ?>"
                                                        data-model-name="<?php echo htmlspecialchars($model['name']); ?>"
                                                        data-bs-toggle="modal"
                                                        data-bs-target="#variationsModal">
                                                    <i class="fas fa-layer-group"></i>
                                            </button>
                                                <button type="button" class="btn btn-sm btn-outline-primary edit-model-btn" 
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
        </div>
    </div>
</div>

<div class="container mt-4">
    <div class="row">
        <!-- Companies Section -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Gerenciar Empresas</h5>
                </div>
                <div class="card-body">
                    <form method="post" class="mb-4">
                        <input type="hidden" name="add_company" value="1">
                        <div class="mb-3">
                            <label class="form-label">Nome da Empresa</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Telefone</label>
                            <input type="text" name="phone" class="form-control">
                        </div>
                        <button type="submit" class="btn btn-primary">Adicionar Empresa</button>
                    </form>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Nome</th>
                                <th>Email</th>
                                <th>Telefone</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($companies as $company): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($company['name']); ?></td>
                                <td><?php echo htmlspecialchars($company['email']); ?></td>
                                <td><?php echo htmlspecialchars($company['phone']); ?></td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Users Section -->
        <div class="col-md-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Gerenciar Usuários</h5>
                </div>
                <div class="card-body">
                    <form method="post" class="mb-4">
                        <input type="hidden" name="add_user" value="1">
                        <div class="mb-3">
                            <label class="form-label">Usuário</label>
                            <input type="text" name="username" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Tipo de Usuário</label>
                            <select name="role" class="form-select" required>
                                <option value="user">Usuário</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Empresa</label>
                            <select name="company_id" class="form-select">
                                <option value="">Nenhuma</option>
                                <?php foreach ($companies as $company): ?>
                                <option value="<?php echo $company['id']; ?>"><?php echo htmlspecialchars($company['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Adicionar Usuário</button>
                    </form>

                    <table class="table">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Tipo</th>
                                <th>Empresa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $user): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($user['username']); ?></td>
                                <td><?php echo $user['role'] == 'admin' ? 'Administrador' : 'Usuário'; ?></td>
                                <td><?php echo htmlspecialchars($user['company_name'] ?? 'Nenhuma'); ?></td>
                            </tr>
                            <?php endforeach; ?>
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

<!-- Variations Modal -->
<div class="modal fade" id="variationsModal" tabindex="-1" aria-labelledby="variationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="variationsModalLabel">Gerenciar Variações</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="variationForm" class="mb-4">
                    <input type="hidden" name="model_id" id="variationModelId">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="variationName" class="form-label">Nome da Variação</label>
                                <input type="text" class="form-control" id="variationName" name="name" required>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="variationImageUrl" class="form-label">URL da Imagem</label>
                                <input type="text" class="form-control" id="variationImageUrl" name="image_url" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="variationDescription" class="form-label">Descrição</label>
                        <textarea class="form-control" id="variationDescription" name="description" rows="3"></textarea>
                    </div>
                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">Adicionar Variação</button>
                    </div>
                </form>

                <hr>

                <div class="mt-4">
                    <h6 class="mb-3">Variações Existentes</h6>
                    <div id="variationsList" class="row">
                        <!-- Variations will be loaded here -->
                    </div>
                </div>

                <script>
                    // Handle variation form submission
                    document.getElementById('variationForm').addEventListener('submit', async function(e) {
                        e.preventDefault();

                        const formData = new FormData(this);
                        const modelId = document.getElementById('variationModelId').value;
                        const nameInput = this.querySelector('input[name="name"]');
                        const imageUrlInput = this.querySelector('input[name="image_url"]');

                        // Validate required fields
                        if (!nameInput.value.trim() || !imageUrlInput.value.trim()) {
                            alert('Por favor, preencha todos os campos obrigatórios.');
                            return;
                        }

                        try {
                            const response = await fetch('actions/add_variation.php', {
                                method: 'POST',
                                body: formData
                            });

                            if(response.ok) {
                                // Clear form
                                this.reset();

                                // Reload variations list
                                const variationsResponse = await fetch(`actions/get_variations.php?model_id=${modelId}`);
                                const variations = await variationsResponse.json();
                                const variationsList = document.getElementById('variationsList');

                                if(variations && variations.length > 0) {
                                    variationsList.innerHTML = variations.map(variation => `
                                        <div class="col-md-4 mb-3">
                                            <div class="card h-100">
                                                <img src="${variation.image_url || 'https://via.placeholder.com/150'}" 
                                                     class="card-img-top" alt="${variation.name}"
                                                     onerror="this.src='https://via.placeholder.com/150'">
                                                <div class="card-body">
                                                    <h6 class="card-title">${variation.name}</h6>
                                                    <p class="card-text small">${variation.description || ''}</p>
                                                    <div class="btn-group btn-group-sm">
                                                        <button class="btn btn-outline-danger delete-variation" data-id="${variation.id}">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    `).join('');

                                    // Add delete handlers to new variation cards
                                    addDeleteHandlers();
                                } else {
                                    variationsList.innerHTML = '<p class="text-muted">Nenhuma variação encontrada.</p>';
                                }

                                // Show success message
                                alert('Variação adicionada com sucesso!');
                            }
                        } catch(error) {
                            console.error('Error:', error);
                            alert('Erro ao adicionar variação');
                        }
                    });

                    // Function to add delete handlers
                    function addDeleteHandlers() {
                        document.querySelectorAll('.delete-variation').forEach(deleteBtn => {
                            deleteBtn.addEventListener('click', async function() {
                                if(confirm('Tem certeza que deseja excluir esta variação?')) {
                                    const variationId = this.getAttribute('data-id');
                                    try {
                                        const response = await fetch('actions/delete_variation.php', {
                                            method: 'POST',
                                            headers: {
                                                'Content-Type': 'application/x-www-form-urlencoded',
                                            },
                                            body: `id=${variationId}`
                                        });
                                        if(response.ok) {
                                            this.closest('.col-md-4').remove();
                                            if(document.querySelectorAll('.col-md-4').length === 0) {
                                                document.getElementById('variationsList').innerHTML = 
                                                    '<p class="text-muted">Nenhuma variação encontrada.</p>';
                                            }
                                        }
                                    } catch(error) {
                                        console.error('Error:', error);
                                        alert('Erro ao excluir variação');
                                    }
                                }
                            });
                        });
                    }
                </script>
            </div>
        </div>
    </div>
</div>

<script>
// Script to handle modal data for editing models
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

    // Handle variations modal
    const variationButtons = document.querySelectorAll('.add-variation-btn');
    variationButtons.forEach(btn => {
        btn.addEventListener('click', async function() {
            const modelId = this.getAttribute('data-id');
            const modelName = this.getAttribute('data-model-name');
            document.getElementById('variationModelId').value = modelId;
            document.getElementById('variationsModalLabel').textContent = `Gerenciar Variações - ${modelName}`;
            const variationsList = document.getElementById('variationsList');

            // Load existing variations
            try {
                const response = await fetch(`actions/get_variations.php?model_id=${modelId}`);
                const variations = await response.json();

                // Clear existing variations
                variationsList.innerHTML = '';

                if(variations && variations.length > 0) {
                    variations.forEach(variation => {
                        const card = document.createElement('div');
                        card.className = 'col-md-4 mb-3';
                        card.innerHTML = `
                            <div class="card h-100">
                                <img src="${variation.image_url || 'https://via.placeholder.com/150'}" 
                                     class="card-img-top" alt="${variation.name}"
                                     onerror="this.src='https://via.placeholder.com/150'">
                                <div class="card-body">
                                    <h6 class="card-title">${variation.name}</h6>
                                    <p class="card-text small">${variation.description || ''}</p>
                                    <div class="btn-group btn-group-sm">
                                        <button class="btn btn-outline-danger delete-variation" data-id="${variation.id}">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                        variationsList.appendChild(card);
                    });
                } else {
                    variationsList.innerHTML = '<p class="text-muted">Nenhuma variação encontrada.</p>';
                }

                // Add delete handlers
                document.querySelectorAll('.delete-variation').forEach(deleteBtn => {
                    deleteBtn.addEventListener('click', async function() {
                        if(confirm('Tem certeza que deseja excluir esta variação?')) {
                            const variationId = this.getAttribute('data-id');
                            try {
                                const response = await fetch('actions/delete_variation.php', {
                                    method: 'POST',
                                    headers: {
                                        'Content-Type': 'application/x-www-form-urlencoded',
                                    },
                                    body: `id=${variationId}`
                                });
                                if(response.ok) {
                                    this.closest('.col-md-4').remove();
                                }
                            } catch(error) {
                                console.error('Error:', error);
                                alert('Erro ao excluir variação');
                            }
                        }
                    });
                });
            } catch(error) {
                console.error('Error:', error);
                alert('Erro ao carregar variações');
            }
        });
    });
});
</script>
<!-- View Variations Modal -->
<div class="modal fade" id="viewVariationsModal" tabindex="-1" aria-labelledby="viewVariationsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="viewVariationsModalLabel">Variações do Modelo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="variationsList" class="row">
                    <!-- Variations will be loaded here -->
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.querySelectorAll('.view-variations-btn').forEach(button => {
    button.addEventListener('click', async function() {
        const modelId = this.getAttribute('data-id');
        try {
            const response = await fetch(`actions/get_variations.php?model_id=${modelId}`);
            const variations = await response.json();
            const variationsList = document.getElementById('variationsList');
            variationsList.innerHTML = '';

            variations.forEach(variation => {
                const card = document.createElement('div');
                card.className = 'col-md-4 mb-3';
                card.innerHTML = `
                    <div class="card">
                        <img src="${variation.image_url}" class="card-img-top" alt="${variation.name}">
                        <div class="card-body">
                            <h5 class="card-title">${variation.name}</h5>
                            <p class="card-text">${variation.description || ''}</p>
                            <button class="btn btn-sm btn-danger delete-variation" data-id="${variation.id}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                `;
                variationsList.appendChild(card);
            });

            // Add delete variation handlers
            document.querySelectorAll('.delete-variation').forEach(btn => {
                btn.addEventListener('click', async function() {
                    if(confirm('Tem certeza que deseja excluir esta variação?')) {
                        const variationId = this.getAttribute('data-id');
                        try {
                            const response = await fetch('actions/delete_variation.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/x-www-form-urlencoded',
                                },
                                body: `id=${variationId}`
                            });
                            if(response.ok) {
                                this.closest('.col-md-4').remove();
                            }
                        } catch(error) {
                            console.error('Error:', error);
                        }
                    }
                });
            });
        } catch(error) {
            console.error('Error:', error);
        }
    });
});
</script>