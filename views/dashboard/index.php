<?php
$titulo = 'Dashboard';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';

// Prepara dados para os gráficos (status e categoria).
$statusLabels = ['aberto','em_andamento','aguardando','resolvido','fechado'];
$statusValores = [];
foreach ($statusLabels as $s) {
    $statusValores[] = $stats['por_status'][$s] ?? 0;
}

$catLabels = [];
$catValores = [];
foreach ($stats['por_categoria'] as $c) {
    $catLabels[]  = $c['categoria'];
    $catValores[] = (int) $c['total'];
}
?>

<div class="container">
    <h3 class="mb-4"><i class="bi bi-speedometer2"></i> Dashboard</h3>

    <!-- Cards de resumo -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-white bg-primary shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small">Total de chamados</div>
                            <div class="fs-3 fw-bold"><?= $stats['total'] ?></div>
                        </div>
                        <i class="bi bi-ticket-detailed fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white bg-warning shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small">Abertos</div>
                            <div class="fs-3 fw-bold"><?= $stats['por_status']['aberto'] ?? 0 ?></div>
                        </div>
                        <i class="bi bi-folder2-open fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white bg-success shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small">Resolvidos</div>
                            <div class="fs-3 fw-bold"><?= $stats['por_status']['resolvido'] ?? 0 ?></div>
                        </div>
                        <i class="bi bi-check2-circle fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-white bg-info shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <div class="small">Tempo médio (h)</div>
                            <div class="fs-3 fw-bold"><?= $stats['tempo_medio'] ?></div>
                        </div>
                        <i class="bi bi-clock-history fs-1 opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row g-3 mb-4">
        <div class="col-md-7">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">Chamados por status</div>
                <div class="card-body"><canvas id="graficoStatus"></canvas></div>
            </div>
        </div>
        <div class="col-md-5">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">Chamados por categoria</div>
                <div class="card-body"><canvas id="graficoCategoria"></canvas></div>
            </div>
        </div>
    </div>

    <!-- Últimos chamados -->
    <div class="card shadow-sm">
        <div class="card-header bg-white fw-bold d-flex justify-content-between align-items-center">
            <span>Últimos chamados</span>
            <a href="<?= BASE_URL ?>chamado/index" class="btn btn-sm btn-outline-primary">Ver todos</a>
        </div>
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead class="table-light">
                    <tr>
                        <th>#</th><th>Título</th><th>Prioridade</th><th>Status</th><th>Data</th><th></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentes)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-3">Nenhum chamado ainda.</td></tr>
                    <?php else: foreach ($recentes as $c): ?>
                        <tr>
                            <td><?= $c['id'] ?></td>
                            <td><?= e($c['titulo']) ?></td>
                            <td><span class="badge bg-<?= corPrioridade($c['prioridade']) ?>"><?= e($c['prioridade']) ?></span></td>
                            <td><span class="badge bg-<?= corStatus($c['status']) ?>"><?= rotuloStatus($c['status']) ?></span></td>
                            <td><small><?= date('d/m/Y H:i', strtotime($c['criado_em'])) ?></small></td>
                            <td><a href="<?= BASE_URL ?>chamado/visualizar/<?= $c['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-eye"></i></a></td>
                        </tr>
                    <?php endforeach; endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js só nesta página -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.3/dist/chart.umd.min.js"></script>
<script>
// Dados vindos do PHP, convertidos para JavaScript com json_encode.
const statusLabels = ['Aberto','Em andamento','Aguardando','Resolvido','Fechado'];
const statusValores = <?= json_encode($statusValores) ?>;
const catLabels = <?= json_encode($catLabels) ?>;
const catValores = <?= json_encode($catValores) ?>;

new Chart(document.getElementById('graficoStatus'), {
    type: 'bar',
    data: {
        labels: statusLabels,
        datasets: [{
            label: 'Chamados',
            data: statusValores,
            backgroundColor: ['#0d6efd','#0dcaf0','#ffc107','#198754','#6c757d']
        }]
    },
    options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, ticks: { precision: 0 } } } }
});

new Chart(document.getElementById('graficoCategoria'), {
    type: 'doughnut',
    data: {
        labels: catLabels,
        datasets: [{ data: catValores,
            backgroundColor: ['#0d6efd','#198754','#ffc107','#dc3545','#6c757d','#6610f2'] }]
    }
});
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
