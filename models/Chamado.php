<?php
// models/Chamado.php
// Tabela: chamados. Coração do sistema: CRUD, filtros, status e estatísticas.

class Chamado
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Cria um chamado. Recebe um array associativo com os dados.
    // Retorna o id do chamado recém-criado (para usarmos logo em seguida na IA).
    public function criar($dados)
    {
        $sql = "INSERT INTO chamados (titulo, descricao, prioridade, usuario_id, categoria_id)
                VALUES (:titulo, :descricao, :prioridade, :usuario_id, :categoria_id)";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            ':titulo'       => $dados['titulo'],
            ':descricao'    => $dados['descricao'],
            ':prioridade'   => $dados['prioridade'] ?? 'media',
            ':usuario_id'   => $dados['usuario_id'],
            ':categoria_id' => $dados['categoria_id'] ?? null,
        ]);
        return $this->db->lastInsertId();
    }

    // Lista chamados com filtros opcionais. Monta o WHERE dinamicamente.
    // $filtros pode ter: status, prioridade, categoria_id, usuario_id, busca
    public function listar($filtros = [])
    {
        $sql = "SELECT c.*,
                       cat.nome  AS categoria_nome,
                       u.nome    AS usuario_nome,
                       t.nome    AS tecnico_nome
                FROM chamados c
                LEFT JOIN categorias cat ON c.categoria_id = cat.id
                LEFT JOIN usuarios   u   ON c.usuario_id   = u.id
                LEFT JOIN usuarios   t   ON c.tecnico_id   = t.id
                WHERE 1=1";

        $params = [];

        if (!empty($filtros['status'])) {
            $sql .= " AND c.status = :status";
            $params[':status'] = $filtros['status'];
        }
        if (!empty($filtros['prioridade'])) {
            $sql .= " AND c.prioridade = :prioridade";
            $params[':prioridade'] = $filtros['prioridade'];
        }
        if (!empty($filtros['categoria_id'])) {
            $sql .= " AND c.categoria_id = :categoria_id";
            $params[':categoria_id'] = $filtros['categoria_id'];
        }
        if (!empty($filtros['usuario_id'])) {
            $sql .= " AND c.usuario_id = :usuario_id";
            $params[':usuario_id'] = $filtros['usuario_id'];
        }
        if (!empty($filtros['busca'])) {
            $sql .= " AND (c.titulo LIKE :busca OR c.descricao LIKE :busca)";
            $params[':busca'] = '%' . $filtros['busca'] . '%';
        }

        $sql .= " ORDER BY c.criado_em DESC";

        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    // Busca um chamado completo (com nomes de categoria, autor e técnico).
    public function buscarPorId($id)
    {
        $sql = "SELECT c.*,
                       cat.nome AS categoria_nome,
                       u.nome   AS usuario_nome,
                       u.email  AS usuario_email,
                       t.nome   AS tecnico_nome
                FROM chamados c
                LEFT JOIN categorias cat ON c.categoria_id = cat.id
                LEFT JOIN usuarios   u   ON c.usuario_id   = u.id
                LEFT JOIN usuarios   t   ON c.tecnico_id   = t.id
                WHERE c.id = :id";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Atualiza os campos editáveis pelo usuário.
    public function atualizar($id, $dados)
    {
        $sql = "UPDATE chamados
                SET titulo = :titulo, descricao = :descricao,
                    prioridade = :prioridade, categoria_id = :categoria_id
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':titulo'       => $dados['titulo'],
            ':descricao'    => $dados['descricao'],
            ':prioridade'   => $dados['prioridade'],
            ':categoria_id' => $dados['categoria_id'] ?: null,
            ':id'           => $id,
        ]);
    }

    // Muda o status. Se for "resolvido", grava a data/hora da resolução.
    public function mudarStatus($id, $status)
    {
        if ($status === 'resolvido') {
            $sql = "UPDATE chamados SET status = :status, resolvido_em = NOW() WHERE id = :id";
        } else {
            $sql = "UPDATE chamados SET status = :status WHERE id = :id";
        }
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':status' => $status, ':id' => $id]);
    }

    // Atribui um técnico ao chamado (e já marca como em andamento).
    public function atribuirTecnico($id, $tecnico_id)
    {
        $sql = "UPDATE chamados SET tecnico_id = :tecnico_id, status = 'em_andamento' WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([':tecnico_id' => $tecnico_id, ':id' => $id]);
    }

    // Salva o resultado da IA (classificação em JSON + solução sugerida).
    public function salvarClassificacaoIA($id, $classificacaoJson, $solucao, $prioridade, $categoria_id)
    {
        $sql = "UPDATE chamados
                SET classificacao_ia = :classificacao,
                    solucao_sugerida_ia = :solucao,
                    prioridade = :prioridade,
                    categoria_id = :categoria_id
                WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':classificacao' => $classificacaoJson,
            ':solucao'       => $solucao,
            ':prioridade'    => $prioridade,
            ':categoria_id'  => $categoria_id,
            ':id'            => $id,
        ]);
    }

    public function deletar($id)
    {
        $stmt = $this->db->prepare("DELETE FROM chamados WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    // ---------- ESTATÍSTICAS (para o Dashboard) ----------

    // Total de chamados por status. Ex.: ['aberto' => 4, 'resolvido' => 10, ...]
    public function contarPorStatus()
    {
        $linhas = $this->db->query(
            "SELECT status, COUNT(*) AS total FROM chamados GROUP BY status"
        )->fetchAll();
        $resultado = [];
        foreach ($linhas as $l) {
            $resultado[$l['status']] = (int) $l['total'];
        }
        return $resultado;
    }

    // Total por prioridade.
    public function contarPorPrioridade()
    {
        $linhas = $this->db->query(
            "SELECT prioridade, COUNT(*) AS total FROM chamados GROUP BY prioridade"
        )->fetchAll();
        $resultado = [];
        foreach ($linhas as $l) {
            $resultado[$l['prioridade']] = (int) $l['total'];
        }
        return $resultado;
    }

    // Total por categoria (nome + quantidade), para o gráfico de pizza.
    public function contarPorCategoria()
    {
        $sql = "SELECT cat.nome AS categoria, COUNT(c.id) AS total
                FROM categorias cat
                LEFT JOIN chamados c ON c.categoria_id = cat.id
                GROUP BY cat.id, cat.nome
                ORDER BY total DESC";
        return $this->db->query($sql)->fetchAll();
    }

    // Total geral de chamados.
    public function contarTotal()
    {
        return (int) $this->db->query("SELECT COUNT(*) FROM chamados")->fetchColumn();
    }

    // Tempo médio de resolução (em horas) dos chamados já resolvidos.
    public function tempoMedioResolucaoHoras()
    {
        $sql = "SELECT AVG(TIMESTAMPDIFF(HOUR, criado_em, resolvido_em))
                FROM chamados WHERE resolvido_em IS NOT NULL";
        $media = $this->db->query($sql)->fetchColumn();
        return $media ? round($media, 1) : 0;
    }
}
