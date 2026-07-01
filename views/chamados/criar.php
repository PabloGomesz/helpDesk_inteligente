<?php
$titulo = 'Novo Chamado';
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
?>

<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <h3 class="mb-3"><i class="bi bi-plus-circle"></i> Abrir novo chamado</h3>

            <div class="alert alert-info py-2 small">
                <i class="bi bi-robot"></i> Ao enviar, a <strong>IA analisa</strong> automaticamente
                seu chamado e sugere prioridade, categoria e uma possível solução.
            </div>

            <div class="card shadow-sm">
                <div class="card-body">
                    <form method="POST" action="<?= BASE_URL ?>chamado/salvar" id="formChamado">
                        <div class="mb-3">
                            <label class="form-label">Título *</label>
                            <input type="text" name="titulo" class="form-control" required
                                   placeholder="Resuma o problema em uma frase">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Descrição *</label>
                            <textarea name="descricao" rows="5" class="form-control" required
                                      placeholder="Descreva o problema com o máximo de detalhes..."></textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Categoria (opcional)</label>
                                <select name="categoria_id" class="form-select">
                                    <option value="">— deixar a IA decidir —</option>
                                    <?php foreach ($categorias as $cat): ?>
                                        <option value="<?= $cat['id'] ?>"><?= e($cat['nome']) ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Prioridade (opcional)</label>
                                <select name="prioridade" class="form-select">
                                    <option value="media">Média</option>
                                    <option value="baixa">Baixa</option>
                                    <option value="alta">Alta</option>
                                    <option value="critica">Crítica</option>
                                </select>
                                <div class="form-text">A IA pode ajustar a prioridade.</div>
                            </div>
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" id="btnEnviar">
                                <span id="btnTexto"><i class="bi bi-send"></i> Abrir chamado</span>
                                <span id="btnCarregando" class="d-none">
                                    <span class="spinner-border spinner-border-sm me-1"></span> Analisando com IA...
                                </span>
                            </button>
                            <a href="<?= BASE_URL ?>chamado/index" class="btn btn-outline-secondary">Cancelar</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Ao enviar, troca o botão por um spinner e bloqueia novos cliques.
document.getElementById('formChamado').addEventListener('submit', function () {
    const btn = document.getElementById('btnEnviar');
    btn.disabled = true;
    document.getElementById('btnTexto').classList.add('d-none');
    document.getElementById('btnCarregando').classList.remove('d-none');
});
</script>

<?php require __DIR__ . '/../partials/footer.php'; ?>
