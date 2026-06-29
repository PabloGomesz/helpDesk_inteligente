<?php
$titulo = 'Chamados';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>

<div class="container">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3 class="mb-0"><i class="bi bi-ticket-detailed"></i> Chamados</h3>
        <a href="<?= BASE_URL ?>chamado/criar" class="btn btn-primary">
            <i class="bi bi-plus-circle"></i> Novo Chamado
        </a>
    </div>

    <!-- Filtros -->
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <form method="GET" class="row g-2 align-items-end">
                <!-- O Router lê ?url=... então precisamos reenviá-lo como campo oculto -->
                <input type="hidden" name="url" value="chamado/index">

                <div class="col-md-3">
                    <label class="form-label small mb-1">Buscar</label>
                    <input type="text" name="busca" value="<?= e($filtros['busca']) ?>" class="form-control form-control-sm" placeholder="título ou descrição">
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Status</label>
                    <select name="status" class="form-select form-select-sm">
                        <option value="">Todos</option>
                        <?php foreach (['aberto','em_andamento','aguardando','resolvido','fechado'] as $s): ?>
                            <option value="<?= $s ?>" <?= $filtros['status']===$s?'selected':'' ?>><?= rotuloStatus($s) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label small mb-1">Prioridade</label>
                    <select name="prioridade" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <?php foreach (['baixa','media','alta','critica'] as $p): ?>
                            <option value="<?= $p ?>" <?= $filtros['prioridade']===$p?'selected':'' ?>><?= ucfirst($p) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label small mb-1">Categoria</label>
                    <select name="categoria" class="form-select form-select-sm">
                        <option value="">Todas</option>
                        <?php foreach ($categorias as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= (string)$filtros['categoria_id']===(string)$cat['id']?'selected':'' ?>><?= e($cat['nome']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2 d-grid">
                    <button class="btn btn-sm btn-outline-primary"><i class="bi bi-funnel"></i> Filtrar</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista -->
    <div class="card shadow-sm">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th>#</th><th>Título</th><th>Categoria</th><th>Prioridade</th>
                        <th>Status</th><th>Solicitante</th><th>Data</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($chamados)): ?>
                        <tr><td colspan="8" class="text-center text-muted py-4">
                            Nenhum chamado encontrado.
                        </td></tr>
                    <?php else: foreach ($chamados as $c): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= e($c['titulo']) ?></td>
                            <td><small><?= e($c['categoria_nome'] ?? '—') ?></small></td>
                            <td><span class="badge bg-<?= corPrioridade($c['prioridade']) ?>"><?= e($c['prioridade']) ?></span></td>
                            <td><span class="badge bg-<?= corStatus($c['status']) ?>"><?= rotuloStatus($c['status']) ?></span></td>
                            <td><small><?= e($c['usuario_nome'] ?? '—') ?></small></td>
                            <td><small><?= date('d/m/Y', strtotime($c['criado_em'])) ?></small></td>
                            <td>
                                <a href="<?= BASE_URL ?>chamado/visualizar/<?= $c['id'] ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
