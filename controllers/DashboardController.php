<?php
// controllers/DashboardController.php
// Página inicial pós-login: estatísticas e gráficos (Chart.js).

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->exigirLogin();
    }

    public function index()
    {
        $usuario  = $this->usuarioLogado();
        $chamado  = $this->model('Chamado');

        // Monta o conjunto de estatísticas para os cards e gráficos.
        $stats = [
            'total'          => $chamado->contarTotal(),
            'por_status'     => $chamado->contarPorStatus(),
            'por_prioridade' => $chamado->contarPorPrioridade(),
            'por_categoria'  => $chamado->contarPorCategoria(),
            'tempo_medio'    => $chamado->tempoMedioResolucaoHoras(),
        ];

        // Lista dos últimos chamados (técnico/admin: todos; usuário: os seus).
        $filtros = ($usuario['tipo'] === 'usuario') ? ['usuario_id' => $usuario['id']] : [];
        $recentes = array_slice($chamado->listar($filtros), 0, 5);

        $this->view('dashboard/index', [
            'stats'    => $stats,
            'recentes' => $recentes,
        ]);
    }
}
