<?php
// controllers/CategoriaController.php
// Área administrativa: gestão de categorias (somente admin).
// Rota: ?url=categoria/index

class CategoriaController extends Controller
{
    public function __construct()
    {
        $this->exigirPerfil(['admin']);
    }

    public function index()
    {
        $categorias = $this->model('Categoria')->listarTodos();
        $this->view('categorias/index', ['categorias' => $categorias]);
    }

    public function salvar()
    {
        $nome      = trim($_POST['nome'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');

        if ($nome === '') {
            $_SESSION['erro'] = 'Informe o nome da categoria.';
            $this->redirect('categoria/index');
        }

        $this->model('Categoria')->criar($nome, $descricao);
        $_SESSION['sucesso'] = 'Categoria criada.';
        $this->redirect('categoria/index');
    }

    public function atualizar($id)
    {
        $this->model('Categoria')->atualizar(
            $id,
            trim($_POST['nome']),
            trim($_POST['descricao'])
        );
        $_SESSION['sucesso'] = 'Categoria atualizada.';
        $this->redirect('categoria/index');
    }

    public function deletar($id)
    {
        $this->model('Categoria')->deletar($id);
        $_SESSION['sucesso'] = 'Categoria excluída.';
        $this->redirect('categoria/index');
    }
}
