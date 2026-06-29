<?php
// controllers/AuthController.php
// Cuida de login, cadastro e logout.

class AuthController extends Controller
{
    // Tela de login (GET).
    public function login()
    {
        // Se já estiver logado, vai direto pro dashboard.
        if ($this->usuarioLogado()) {
            $this->redirect('dashboard/index');
        }
        $this->view('auth/login');
    }

    // Tela de cadastro (GET).
    public function register()
    {
        if ($this->usuarioLogado()) {
            $this->redirect('dashboard/index');
        }
        $this->view('auth/register');
    }

    // Processa o formulário de login (POST).
    public function processarLogin()
    {
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        $usuario = $this->model('Usuario')->verificarLogin($email, $senha);

        if (!$usuario) {
            // Guarda mensagem de erro na sessão e volta pro login.
            $_SESSION['erro'] = 'E-mail ou senha incorretos.';
            $this->redirect('auth/login');
        }

        // Login OK: guardamos os dados essenciais na sessão (sem a senha!).
        $_SESSION['usuario'] = [
            'id'    => $usuario['id'],
            'nome'  => $usuario['nome'],
            'email' => $usuario['email'],
            'tipo'  => $usuario['tipo'],
        ];
        $this->redirect('dashboard/index');
    }

    // Processa o formulário de cadastro (POST).
    public function processarRegistro()
    {
        $nome  = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';

        // Validações simples.
        if ($nome === '' || $email === '' || strlen($senha) < 4) {
            $_SESSION['erro'] = 'Preencha todos os campos (senha com no mínimo 4 caracteres).';
            $this->redirect('auth/register');
        }

        $usuarioModel = $this->model('Usuario');

        // Email já existe?
        if ($usuarioModel->buscarPorEmail($email)) {
            $_SESSION['erro'] = 'Este e-mail já está cadastrado.';
            $this->redirect('auth/register');
        }

        // Cria sempre como 'usuario' comum (técnicos/admins são criados pelo admin).
        $usuarioModel->criar($nome, $email, $senha, 'usuario');

        $_SESSION['sucesso'] = 'Conta criada com sucesso! Faça login.';
        $this->redirect('auth/login');
    }

    // Sai do sistema.
    public function logout()
    {
        session_destroy();
        $this->redirect('auth/login');
    }
}
