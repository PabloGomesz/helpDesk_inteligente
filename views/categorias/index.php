<?php
$titulo = 'Categorias';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>

<div class="container">
    <h3 class="mb-3"><i class="bi bi-tags"></i> Categorias</h3>

    <div class="row g-3">
        <div class="col-lg-5">
            <div class="card shadow-sm">
                <div class="card-header bg-white fw-bold">Nova categoria</div>
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>categoria/salvar">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <input type="text" name="descricao" class="form-control">
                        </div>
                        <button class="btn btn-primary"><i class="bi bi-plus-circle"></i> Adicionar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-7">
            <div class="card shadow-sm">
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead class="table-light"><tr><th>Editar categoria</th><th class="text-end">Excluir</th></tr></thead>
                        <tbody>
                            <?php foreach ($categorias as $cat): ?>
                                <tr>
                                    <td>
                                        <form method="POST" action="<?= BASE_URL ?>categoria/atualizar/<?= $cat['id'] ?>" class="d-flex gap-1">
                                            <input type="text" name="nome" value="<?= e($cat['nome']) ?>" class="form-control form-control-sm" style="max-width:140px">
                                            <input type="text" name="descricao" value="<?= e($cat['descricao']) ?>" class="form-control form-control-sm" placeholder="descrição">
                                            <button class="btn btn-sm btn-outline-success"><i class="bi bi-save"></i></button>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <a href="<?= BASE_URL ?>categoria/deletar/<?= $cat['id'] ?>" class="btn btn-sm btn-outline-danger"
                                           onclick="return confirm('Excluir esta categoria?')"><i class="bi bi-trash"></i></a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
