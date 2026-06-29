<?php
// models/LogIA.php
// Tabela: historico_ia. Guarda cada interação com a IA (auditoria e insights).

class LogIA
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Registra uma chamada à IA. $tipo: 'classificacao', 'sugestao', 'analise'
    public function registrar($chamado_id, $tipo, $prompt, $resposta)
    {
        $sql = "INSERT INTO historico_ia (chamado_id, tipo, prompt, resposta)
                VALUES (:chamado_id, :tipo, :prompt, :resposta)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':chamado_id' => $chamado_id,
            ':tipo'       => $tipo,
            ':prompt'     => $prompt,
            ':resposta'   => $resposta,
        ]);
    }

    public function listarPorChamado($chamado_id)
    {
        $stmt = $this->db->prepare(
            "SELECT * FROM historico_ia WHERE chamado_id = :id ORDER BY criado_em DESC"
        );
        $stmt->execute([':id' => $chamado_id]);
        return $stmt->fetchAll();
    }
}
