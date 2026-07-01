<?php
$titulo = 'Novo Usuário';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <h3 class="mb-3"><i class="bi bi-person-plus"></i> Novo usuário</h3>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>usuario/salvar">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="senha" class="form-control" minlength="4" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Perfil</label>
                            <select name="tipo" class="form-select">
                                <option value="usuario">Usuário</option>
                                <option value="tecnico">Técnico</option>
                                <option value="admin">Administrador</option>
                            </select>
                        </div>
                        <button class="btn btn-primary"><i class="bi bi-check-circle"></i> Criar</button>
                        <a href="<?= BASE_URL ?>usuario/index" class="btn btn-outline-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
