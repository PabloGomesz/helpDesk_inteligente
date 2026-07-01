<?php
$titulo = 'Chamado #' . $chamado['id'];
require __DIR__ . '/../partials/header.php';
require __DIR__ . '/../partials/navbar.php';
$u = $_SESSION['usuario'];
$ehTecnico = in_array($u['tipo'], ['tecnico', 'admin']);
$ehDono = ($chamado['usuario_id'] == $u['id']) || $u['tipo'] === 'admin';
?>

<div class="container">
    <a href="<?= BASE_URL ?>chamado/index" class="btn btn-sm btn-link mb-2"><i class="bi bi-arrow-left"></i> Voltar</a>

    <div class="row g-3">
        <div class="col-lg-8">
            <div class="card shadow-sm mb-3">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-start">
                        <h4 class="mb-1"><?= e($chamado['titulo']) ?></h4>
                        <div class="text-end">
                            <span class="badge bg-<?= corPrioridade($chamado['prioridade']) ?>"><?= e($chamado['prioridade']) ?></span>
                            <span class="badge bg-<?= corStatus($chamado['status']) ?>"><?= rotuloStatus($chamado['status']) ?></span>
                        </div>
                    </div>
                    <p class="text-muted small mb-3">
                        #<?= $chamado['id'] ?> &middot; <?= e($chamado['categoria_nome'] ?? 'sem categoria') ?>
                        &middot; aberto por <?= e($chamado['usuario_nome']) ?>
                        em <?= date('d/m/Y H:i', strtotime($chamado['criado_em'])) ?>
                    </p>
                    <p style="white-space:pre-line"><?= e($chamado['descricao']) ?></p>

                    <?php if ($ehDono): ?>
                        <hr>
                        <a href="<?= BASE_URL ?>chamado/editar/<?= $chamado['id'] ?>" class="btn btn-sm btn-outline-secondary"><i class="bi bi-pencil"></i> Editar</a>
                        <a href="<?= BASE_URL ?>chamado/deletar/<?= $chamado['id'] ?>" class="btn btn-sm btn-outline-danger"
                           onclick="return confirm('Excluir este chamado?')"><i class="bi bi-trash"></i> Excluir</a>
                    <?php endif; ?>
                </div>
            </div>

            <div class="card shadow-sm mb-3">
                <div class="card-header bg-white fw-bold"><i class="bi bi-chat-dots"></i> Comentários</div>
                <div class="card-body">
                    <?php if (empty($comentarios)): ?>
                        <p class="text-muted small mb-3">Nenhum comentário ainda.</p>
                    <?php else: foreach ($comentarios as $cm): ?>
                        <div class="border-start border-3 ps-3 mb-3 <?= $cm['autor_tipo']==='usuario'?'border-secondary':'border-primary' ?>">
                            <div class="small fw-bold">
                                <?= e($cm['autor_nome']) ?>
                                <span class="badge bg-light text-dark"><?= e($cm['autor_tipo']) ?></span>
                                <span class="text-muted fw-normal">· <?= date('d/m/Y H:i', strtotime($cm['criado_em'])) ?></span>
                            </div>
                            <div style="white-space:pre-line"><?= e($cm['mensagem']) ?></div>
                        </div>
                    <?php endforeach; endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>chamado/responder/<?= $chamado['id'] ?>">
                        <textarea name="mensagem" class="form-control mb-2" rows="3" placeholder="Escreva uma resposta..."></textarea>
                        <button class="btn btn-primary btn-sm"><i class="bi bi-send"></i> Comentar</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card shadow-sm mb-3 border-warning">
                <div class="card-header bg-warning-subtle fw-bold"><i class="bi bi-robot"></i> Análise da IA</div>
                <div class="card-body">
                    <?php if (!$analiseIA): ?>
                        <p class="text-muted small mb-0">Sem análise de IA para este chamado.</p>
                    <?php else: ?>
                        <?php if (!empty($analiseIA['urgencia_emocional'])): ?>
                            <div class="alert alert-danger py-2 small"><i class="bi bi-exclamation-triangle"></i> Possível urgência/estresse detectado.</div>
                        <?php endif; ?>

                        <p class="mb-1"><strong>Sentimento:</strong> <?= e($analiseIA['sentimento'] ?? '—') ?></p>
                        <p class="mb-2"><strong>Categoria sugerida:</strong> <?= e($analiseIA['categoria'] ?? '—') ?></p>

                        <?php if (!empty($analiseIA['palavras_chave'])): ?>
                            <div class="mb-2">
                                <strong class="d-block mb-1">Palavras-chave:</strong>
                                <?php foreach ($analiseIA['palavras_chave'] as $kw): ?>
                                    <span class="badge bg-secondary"><?= e($kw) ?></span>
                                <?php endforeach; ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($chamado['solucao_sugerida_ia'])): ?>
                            <strong class="d-block mb-1">Solução sugerida:</strong>
                            <div class="bg-light p-2 rounded small" style="white-space:pre-line"><?= e($chamado['solucao_sugerida_ia']) ?></div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>

            <?php if ($ehTecnico): ?>
                <div class="card shadow-sm">
                    <div class="card-header bg-white fw-bold"><i class="bi bi-gear"></i> Ações do técnico</div>
                    <div class="card-body">
                        <form method="POST" action="<?= BASE_URL ?>chamado/responder/<?= $chamado['id'] ?>">
                            <label class="form-label small mb-1">Alterar status</label>
                            <select name="status" class="form-select form-select-sm mb-2">
                                <?php foreach (['aberto','em_andamento','aguardando','resolvido','fechado'] as $s): ?>
                                    <option value="<?= $s ?>" <?= $chamado['status']===$s?'selected':'' ?>><?= rotuloStatus($s) ?></option>
                                <?php endforeach; ?>
                            </select>

                            <label class="form-label small mb-1">Atribuir a</label>
                            <select name="tecnico_id" class="form-select form-select-sm mb-3">
                                <option value="">— ninguém —</option>
                                <?php foreach ($tecnicos as $t): ?>
                                    <option value="<?= $t['id'] ?>" <?= $chamado['tecnico_id']==$t['id']?'selected':'' ?>><?= e($t['nome']) ?></option>
                                <?php endforeach; ?>
                            </select>

                            <button class="btn btn-success btn-sm w-100"><i class="bi bi-check2"></i> Aplicar</button>
                        </form>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
