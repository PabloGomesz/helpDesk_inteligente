<?php
// core/Controller.php
// Classe BASE de todos os controllers. Dá: render de views, atalho de model,
// e os helpers de autenticação/redirecionamento.

class Controller
{
    // Carrega uma VIEW e injeta os dados nela.
    // Ex.: $this->view('chamados/index', ['chamados' => $lista]);
    protected function view($caminho, $dados = [])
    {
        extract($dados);
        require __DIR__ . '/../views/' . $caminho . '.php';
    }

    // Atalho para instanciar uma model. Ex.: $this->model('Usuario')
    protected function model($nome)
    {
        return new $nome();
    }

    // Redireciona para uma rota interna. Ex.: $this->redirect('dashboard/index')
    protected function redirect($rota)
    {
        header('Location: ' . BASE_URL . $rota);
        exit;
    }

    // Devolve os dados do usuário logado (ou null se ninguém estiver logado).
    protected function usuarioLogado()
    {
        return $_SESSION['usuario'] ?? null;
    }

    // Exige que haja um usuário logado; se não, manda para o login.
    protected function exigirLogin()
    {
        if (!isset($_SESSION['usuario'])) {
            $this->redirect('auth/login');
        }
    }

    // Exige que o usuário logado tenha um dos perfis informados.
    // Ex.: $this->exigirPerfil(['admin']) ou $this->exigirPerfil(['admin', 'tecnico'])
    protected function exigirPerfil($perfis)
    {
        $this->exigirLogin();
        if (!in_array($_SESSION['usuario']['tipo'], (array) $perfis)) {
            http_response_code(403);
            die('Acesso negado: você não tem permissão para esta ação.');
        }
    }
}
