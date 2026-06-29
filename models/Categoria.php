<?php
// models/Categoria.php
// Tabela: categorias. CRUD simples usado no cadastro de chamados e na área admin.

class Categoria
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    public function listarTodos()
    {
        return $this->db->query("SELECT * FROM categorias ORDER BY nome")->fetchAll();
    }

    public function buscarPorId($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM categorias WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Busca uma categoria pelo nome (usado para mapear a classificação da IA -> id).
    public function buscarPorNome($nome)
    {
        $stmt = $this->db->prepare("SELECT * FROM categorias WHERE nome = :nome LIMIT 1");
        $stmt->execute([':nome' => $nome]);
        return $stmt->fetch();
    }

    public function criar($nome, $descricao)
    {
        $stmt = $this->db->prepare("INSERT INTO categorias (nome, descricao) VALUES (:nome, :descricao)");
        return $stmt->execute([':nome' => $nome, ':descricao' => $descricao]);
    }

    public function atualizar($id, $nome, $descricao)
    {
        $stmt = $this->db->prepare("UPDATE categorias SET nome = :nome, descricao = :descricao WHERE id = :id");
        return $stmt->execute([':nome' => $nome, ':descricao' => $descricao, ':id' => $id]);
    }

    public function deletar($id)
    {
        $stmt = $this->db->prepare("DELETE FROM categorias WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
