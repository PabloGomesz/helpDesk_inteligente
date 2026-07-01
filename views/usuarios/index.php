<?php
$titulo = 'Usuários';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-people"></i> Usuários</h3>
        <a href="<?= BASE_URL ?>usuario/criar" class="btn btn-primary"><i class="bi bi-person-plus"></i> Novo usuário</a>
    </div>

    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr><th>#</th><th>Nome</th><th>E-mail</th><th>Perfil</th><th>Criado em</th><th></th></tr>
                </thead>
                <tbody>
                    <?php foreach ($usuarios as $usr): ?>
                        <tr>
                            <td><?= $usr['id'] ?></td>
                            <td><?= e($usr['nome']) ?></td>
                            <td><?= e($usr['email']) ?></td>
                            <td><span class="badge bg-secondary"><?= e($usr['tipo']) ?></span></td>
                            <td><small><?= date('d/m/Y', strtotime($usr['criado_em'])) ?></small></td>
                            <td>
                                <a href="<?= BASE_URL ?>usuario/editar/<?= $usr['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i></a>
                                <a href="<?= BASE_URL ?>usuario/deletar/<?= $usr['id'] ?>" class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Excluir este usuário?')"><i class="bi bi-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
