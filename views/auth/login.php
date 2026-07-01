<?php $titulo = 'Login'; $bodyClass = 'auth-page'; require __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow border-0">
                <div class="card-body p-4">

                    <div class="text-center mb-4">
                        <span class="auth-logo mb-3"><i class="bi bi-headset"></i></span>
                        <h4 class="fw-bold mb-1">HelpDesk<span class="text-primary">IA</span></h4>
                        <p class="text-muted small mb-0">Acesse sua conta</p>
                    </div>

                    <?php if (!empty($_SESSION['erro'])): ?>
                        <div class="alert alert-danger py-2"><?= e($_SESSION['erro']); unset($_SESSION['erro']); ?></div>
                    <?php endif; ?>
                    <?php if (!empty($_SESSION['sucesso'])): ?>
                        <div class="alert alert-success py-2"><?= e($_SESSION['sucesso']); unset($_SESSION['sucesso']); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>auth/processarLogin">
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-envelope"></i></span>
                                <input type="email" name="email" class="form-control" required autofocus>
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="bi bi-lock"></i></span>
                                <input type="password" name="senha" class="form-control" required>
                            </div>
                        </div>
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right"></i> Entrar
                        </button>
                    </form>

                    <p class="text-center mt-3 mb-0 small">
                        Não tem conta?
                        <a href="<?= BASE_URL ?>auth/register">Cadastre-se</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
