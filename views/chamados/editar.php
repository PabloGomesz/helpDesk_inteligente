<?php
$titulo = 'Editar Chamado';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h3 class="mb-3"><i class="bi bi-pencil"></i> Editar chamado #<?= $chamado['id'] ?></h3>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>chamado/atualizar/<?= $chamado['id'] ?>">
                        <div class="mb-3">
                            <label class="form-label">Título</label>
                            <input type="text" name="titulo" class="form-control" value="<?= e($chamado['titulo']) ?>" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Descrição</label>
                            <textarea name="descricao" rows="5" class="form-control" required><?= e($chamado['descricao']) ?></textarea>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Categoria</label>
                                <select name="categoria_id" class="form-select">
                                    <option value="">—</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>" <?= $chamado['categoria_id']==$cat['id']?'selected':'' ?>><?= e($cat['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prioridade</label>
                                <select name="prioridade" class="form-select">
                                    <?php foreach (['baixa','media','alta','critica'] as $p): ?>
                                        <option value="<?= $p ?>" <?= $chamado['prioridade']===$p?'selected':'' ?>><?= ucfirst($p) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>
                        <button class="btn btn-primary"><i class="bi bi-save"></i> Salvar</button>
                        <a href="<?= BASE_URL ?>chamado/visualizar/<?= $chamado['id'] ?>" class="btn btn-outline-secondary">Cancelar</a>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
