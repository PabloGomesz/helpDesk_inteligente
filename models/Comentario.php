<?php
// models/Comentario.php
// Tabela: comentarios. Conversa entre usuário e técnico dentro de um chamado.

class Comentario
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function criar($chamado_id, $usuario_id, $mensagem)
    {
        $sql = "INSERT INTO comentarios (chamado_id, usuario_id, mensagem)
                VALUES (:chamado_id, :usuario_id, :mensagem)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':chamado_id' => $chamado_id,
            ':usuario_id' => $usuario_id,
            ':mensagem'   => $mensagem,
        ]);
    }

    // Lista os comentários de um chamado, já com o nome e o tipo de quem escreveu.
    public function listarPorChamado($chamado_id)
    {
        $sql = "SELECT cm.*, u.nome AS autor_nome, u.tipo AS autor_tipo
                FROM comentarios cm
                JOIN usuarios u ON cm.usuario_id = u.id
                WHERE cm.chamado_id = :chamado_id
                ORDER BY cm.criado_em ASC";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([':chamado_id' => $chamado_id]);
        return $stmt->fetchAll();
    }
}
