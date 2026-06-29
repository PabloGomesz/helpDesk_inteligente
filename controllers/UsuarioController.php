<?php
// controllers/UsuarioController.php
// Área administrativa: gestão de usuários (somente admin).

class UsuarioController extends Controller
{
    public function __construct()
    {
        // Todas as ações aqui exigem perfil admin.
        $this->exigirPerfil(['admin']);
    }

    public function index()
    {
        $usuarios = $this->model('Usuario')->listarTodos();
        $this->view('usuarios/index', ['usuarios' => $usuarios]);
    }

    public function criar()
    {
        $this->view('usuarios/criar');
    }

    public function salvar()
    {
        $nome  = trim($_POST['nome'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $senha = $_POST['senha'] ?? '';
        $tipo  = $_POST['tipo'] ?? 'usuario';

        if ($nome === '' || $email === '' || strlen($senha) < 4) {
            $_SESSION['erro'] = 'Preencha todos os campos (senha mínima de 4 caracteres).';
            $this->redirect('usuario/criar');
        }

        $usuarioModel = $this->model('Usuario');
        if ($usuarioModel->buscarPorEmail($email)) {
            $_SESSION['erro'] = 'Este e-mail já está cadastrado.';
            $this->redirect('usuario/criar');
        }

        $usuarioModel->criar($nome, $email, $senha, $tipo);
        $_SESSION['sucesso'] = 'Usuário criado.';
        $this->redirect('usuario/index');
    }

    public function editar($id)
    {
        $usuario = $this->model('Usuario')->buscarPorId($id);
        if (!$usuario) {
            http_response_code(404);
            die('Usuário não encontrado.');
        }
        $this->view('usuarios/editar', ['usuario' => $usuario]);
    }

    public function atualizar($id)
    {
        $this->model('Usuario')->atualizar(
            $id,
            trim($_POST['nome']),
            trim($_POST['email']),
            $_POST['tipo']
        );
        $_SESSION['sucesso'] = 'Usuário atualizado.';
        $this->redirect('usuario/index');
    }

    public function deletar($id)
    {
        // Evita o admin se autoexcluir e ficar sem acesso.
        if ($id == $this->usuarioLogado()['id']) {
            $_SESSION['erro'] = 'Você não pode excluir a si mesmo.';
            $this->redirect('usuario/index');
        }
        $this->model('Usuario')->deletar($id);
        $_SESSION['sucesso'] = 'Usuário excluído.';
        $this->redirect('usuario/index');
    }
}
