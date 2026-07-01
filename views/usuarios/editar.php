<?php
$titulo = 'Editar Usuário';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <h3 class="mb-3"><i class="bi bi-pencil"></i> Editar usuário</h3>
            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>usuario/atualizar/<?= $usuario['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" value="<?= e($usuario['nome']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" value="<?= e($usuario['email']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Perfil</label>
                            <select name="tipo" class="form-select">
                                <?php foreach (['usuario'=>'Usuário','tecnico'=>'Técnico','admin'=>'Administrador'] as $valor=>$rotulo): ?>
                                    <option value="<?= $valor ?>" <?= $usuario['tipo']===$valor?'selected':'' ?>><?= $rotulo ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <p class="text-muted small">A senha não é alterada por aqui.</p>
                        <button class="btn btn-primary"><i class="bi bi-save"></i> Salvar</button>
                        <a href="<?= BASE_URL ?>usuario/index" class="btn btn-outline-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
