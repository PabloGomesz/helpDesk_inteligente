<?php
// controllers/ChamadoController.php
// CRUD de chamados + acionamento da IA + comentários.
// ATENÇÃO: a rota usa o nome no singular -> ?url=chamado/index

class ChamadoController extends Controller
{
    public function __construct()
    {
        // Toda ação de chamados exige estar logado.
        $this->exigirLogin();
    }

    // Lista de chamados (com filtros vindos da query string).
    public function index()
    {
        $usuario = $this->usuarioLogado();

        $filtros = [
            'status'       => $_GET['status']     ?? '',
            'prioridade'   => $_GET['prioridade'] ?? '',
            'categoria_id' => $_GET['categoria']  ?? '',
            'busca'        => $_GET['busca']      ?? '',
        ];

        // Usuário comum só vê os PRÓPRIOS chamados. Técnico/admin veem todos.
        if ($usuario['tipo'] === 'usuario') {
            $filtros['usuario_id'] = $usuario['id'];
        }

        $chamados   = $this->model('Chamado')->listar($filtros);
        $categorias = $this->model('Categoria')->listarTodos();

        $this->view('chamados/index', [
            'chamados'   => $chamados,
            'categorias' => $categorias,
            'filtros'    => $filtros,
        ]);
    }

    // Formulário de abertura de chamado.
    public function criar()
    {
        $categorias = $this->model('Categoria')->listarTodos();
        $this->view('chamados/criar', ['categorias' => $categorias]);
    }

    // Salva o chamado novo e dispara a análise da IA.
    public function salvar()
    {
        $usuario = $this->usuarioLogado();

        $titulo    = trim($_POST['titulo'] ?? '');
        $descricao = trim($_POST['descricao'] ?? '');

        if ($titulo === '' || $descricao === '') {
            $_SESSION['erro'] = 'Preencha título e descrição.';
            $this->redirect('chamado/criar');
        }

        $chamadoModel = $this->model('Chamado');

        // 1) Cria o chamado com os dados básicos.
        $chamadoId = $chamadoModel->criar([
            'titulo'       => $titulo,
            'descricao'    => $descricao,
            'prioridade'   => $_POST['prioridade'] ?? 'media',
            'categoria_id' => $_POST['categoria_id'] ?? null,
            'usuario_id'   => $usuario['id'],
        ]);

        // 2) Aciona a IA (se estiver configurada) para classificar.
        $ia = new AIController();
        $analise = $ia->analisarChamado($titulo, $descricao);

        if ($analise) {
            // Mapeia a categoria que a IA sugeriu (nome) para o id no banco.
            $categoriaId = $_POST['categoria_id'] ?? null;
            if (!empty($analise['categoria'])) {
                $cat = $this->model('Categoria')->buscarPorNome($analise['categoria']);
                if ($cat) {
                    $categoriaId = $cat['id'];
                }
            }

            // Salva classificação (JSON), solução sugerida e prioridade no chamado.
            $chamadoModel->salvarClassificacaoIA(
                $chamadoId,
                json_encode($analise, JSON_UNESCAPED_UNICODE),
                $analise['sugestao_solucao'] ?? '',
                $analise['prioridade'] ?? ($_POST['prioridade'] ?? 'media'),
                $categoriaId
            );

            // Registra no histórico de IA.
            $this->model('LogIA')->registrar(
                $chamadoId,
                'classificacao',
                "Título: {$titulo}\nDescrição: {$descricao}",
                $analise['_raw'] ?? json_encode($analise, JSON_UNESCAPED_UNICODE)
            );

            $_SESSION['sucesso'] = 'Chamado aberto e analisado pela IA com sucesso!';
        } else {
            $_SESSION['sucesso'] = 'Chamado aberto com sucesso! (IA indisponível no momento)';
        }

        $this->redirect('chamado/visualizar/' . $chamadoId);
    }

    // Página de detalhes de um chamado.
    public function visualizar($id)
    {
        $chamado = $this->model('Chamado')->buscarPorId($id);
        if (!$chamado) {
            http_response_code(404);
            die('Chamado não encontrado.');
        }
        $this->verificarAcesso($chamado);

        $comentarios = $this->model('Comentario')->listarPorChamado($id);
        $tecnicos    = $this->model('Usuario')->listarTecnicos();

        // Decodifica o JSON da IA (se houver) para mostrar bonito na tela.
        $analiseIA = $chamado['classificacao_ia']
            ? json_decode($chamado['classificacao_ia'], true)
            : null;

        $this->view('chamados/visualizar', [
            'chamado'     => $chamado,
            'comentarios' => $comentarios,
            'tecnicos'    => $tecnicos,
            'analiseIA'   => $analiseIA,
        ]);
    }

    // Formulário de edição.
    public function editar($id)
    {
        $chamado = $this->model('Chamado')->buscarPorId($id);
        if (!$chamado) {
            http_response_code(404);
            die('Chamado não encontrado.');
        }
        $this->verificarDono($chamado);

        $categorias = $this->model('Categoria')->listarTodos();
        $this->view('chamados/editar', ['chamado' => $chamado, 'categorias' => $categorias]);
    }

    // Salva a edição.
    public function atualizar($id)
    {
        $chamado = $this->model('Chamado')->buscarPorId($id);
        if (!$chamado) {
            http_response_code(404);
            die('Chamado não encontrado.');
        }
        $this->verificarDono($chamado);

        $this->model('Chamado')->atualizar($id, [
            'titulo'       => trim($_POST['titulo']),
            'descricao'    => trim($_POST['descricao']),
            'prioridade'   => $_POST['prioridade'],
            'categoria_id' => $_POST['categoria_id'] ?? null,
        ]);

        $_SESSION['sucesso'] = 'Chamado atualizado.';
        $this->redirect('chamado/visualizar/' . $id);
    }

    // Exclui um chamado.
    public function deletar($id)
    {
        $chamado = $this->model('Chamado')->buscarPorId($id);
        if (!$chamado) {
            http_response_code(404);
            die('Chamado não encontrado.');
        }
        $this->verificarDono($chamado);

        $this->model('Chamado')->deletar($id);
        $_SESSION['sucesso'] = 'Chamado excluído.';
        $this->redirect('chamado/index');
    }

    // Adiciona comentário e/ou muda status (usado na página de detalhes).
    public function responder($id)
    {
        $chamado = $this->model('Chamado')->buscarPorId($id);
        if (!$chamado) {
            http_response_code(404);
            die('Chamado não encontrado.');
        }
        $this->verificarAcesso($chamado);

        $usuario = $this->usuarioLogado();

        // Comentário (qualquer um com acesso pode comentar).
        $mensagem = trim($_POST['mensagem'] ?? '');
        if ($mensagem !== '') {
            $this->model('Comentario')->criar($id, $usuario['id'], $mensagem);
        }

        // Mudança de status e atribuição: só técnico/admin.
        if (in_array($usuario['tipo'], ['tecnico', 'admin'])) {
            if (!empty($_POST['status'])) {
                $this->model('Chamado')->mudarStatus($id, $_POST['status']);
            }
            if (!empty($_POST['tecnico_id'])) {
                $this->model('Chamado')->atribuirTecnico($id, $_POST['tecnico_id']);
            }
        }

        $_SESSION['sucesso'] = 'Resposta registrada.';
        $this->redirect('chamado/visualizar/' . $id);
    }

    // ---------- Helpers de permissão ----------

    // Pode VER: o dono do chamado, técnicos e admins.
    private function verificarAcesso($chamado)
    {
        $u = $this->usuarioLogado();
        if ($u['tipo'] === 'usuario' && $chamado['usuario_id'] != $u['id']) {
            http_response_code(403);
            die('Acesso negado: este chamado não é seu.');
        }
    }

    // Pode EDITAR/EXCLUIR: o dono ou um admin.
    private function verificarDono($chamado)
    {
        $u = $this->usuarioLogado();
        if ($u['tipo'] !== 'admin' && $chamado['usuario_id'] != $u['id']) {
            http_response_code(403);
            die('Acesso negado: você não pode alterar este chamado.');
        }
    }
}
