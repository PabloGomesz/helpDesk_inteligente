<?php $titulo = 'Cadastro'; $bodyClass = 'auth-page'; require __DIR__ . '/../partials/header.php'; ?>

<div class="container">
    <div class="row justify-content-center mt-5">
        <div class="col-md-5 col-lg-4">
            <div class="card shadow border-0">
                <div class="card-body p-4">

                    <div class="text-center mb-4">
                        <span class="auth-logo mb-3"><i class="bi bi-person-plus"></i></span>
                        <h4 class="fw-bold mb-1">Criar conta</h4>
                        <p class="text-muted small mb-0">Cadastre-se para abrir chamados</p>
                    </div>

                    <?php if (!empty($_SESSION['erro'])): ?>
                        <div class="alert alert-danger py-2"><?= e($_SESSION['erro']); unset($_SESSION['erro']); ?></div>
                    <?php endif; ?>

                    <form method="POST" action="<?= BASE_URL ?>auth/processarRegistro">
                        <div class="mb-3">
                            <label class="form-label">Nome</label>
                            <input type="text" name="nome" class="form-control" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">E-mail</label>
                            <input type="email" name="email" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Senha</label>
                            <input type="password" name="senha" class="form-control" minlength="4" required>
                            <div class="form-text">Mínimo de 4 caracteres.</div>
                        </div>
                        <button class="btn btn-primary w-100">
                            <i class="bi bi-check-circle"></i> Cadastrar
                        </button>
                    </form>

                    <p class="text-center mt-3 mb-0 small">
                        Já tem conta?
                        <a href="<?= BASE_URL ?>auth/login">Faça login</a>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../partials/footer.php'; ?>
