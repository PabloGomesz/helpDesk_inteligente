<?php
// views/partials/navbar.php
// Barra de navegação superior. Mostra links conforme o perfil do usuário logado.
$u = $_SESSION['usuario'] ?? null;
?>
<nav class="navbar navbar-expand-lg navbar-dark bg-primary shadow-sm mb-4">
    <div class="container">
        <a class="navbar-brand fw-bold" href="<?= BASE_URL ?>dashboard/index">
            <i class="bi bi-headset"></i> HelpDesk<span class="text-warning">IA</span>
        </a>

        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse" id="menu">
            <ul class="navbar-nav me-auto">
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>dashboard/index">
                        <i class="bi bi-speedometer2"></i> Dashboard
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>chamado/index">
                        <i class="bi bi-ticket-detailed"></i> Chamados
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="<?= BASE_URL ?>chamado/criar">
                        <i class="bi bi-plus-circle"></i> Novo Chamado
                    </a>
                </li>

                <?php if ($u && $u['tipo'] === 'admin'): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>usuario/index">
                            <i class="bi bi-people"></i> Usuários
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="<?= BASE_URL ?>categoria/index">
                            <i class="bi bi-tags"></i> Categorias
                        </a>
                    </li>
                <?php endif; ?>
            </ul>

            <ul class="navbar-nav">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                        <i class="bi bi-person-circle"></i>
                        <?= e($u['nome'] ?? '') ?>
                        <span class="badge bg-warning text-dark ms-1"><?= e($u['tipo'] ?? '') ?></span>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li>
                            <a class="dropdown-item text-danger" href="<?= BASE_URL ?>auth/logout">
                                <i class="bi bi-box-arrow-right"></i> Sair
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<?php
// Mensagens de feedback (sucesso/erro) guardadas na sessão.
if (!empty($_SESSION['sucesso'])): ?>
    <div class="container">
        <div class="alert alert-success alert-dismissible fade show">
            <i class="bi bi-check-circle"></i> <?= e($_SESSION['sucesso']) ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php unset($_SESSION['sucesso']); endif; ?>

<?php if (!empty($_SESSION['erro'])): ?>
    <div class="container">
        <div class="alert alert-danger alert-dismissible fade show">
            <i class="bi bi-exclamation-triangle"></i> <?= e($_SESSION['erro']) ?>
            <button class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    </div>
<?php unset($_SESSION['erro']); endif; ?>
