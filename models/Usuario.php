<?php
// models/Usuario.php
// Tabela: usuarios. Cuida de cadastro, login e gestão de usuários.

class Usuario
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getConnection();
    }

    // Cria um usuário novo. A senha é criptografada com password_hash (bcrypt).
    public function criar($nome, $email, $senha, $tipo = 'usuario')
    {
        $sql = "INSERT INTO usuarios (nome, email, senha, tipo)
                VALUES (:nome, :email, :senha, :tipo)";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome'  => $nome,
            ':email' => $email,
            ':senha' => password_hash($senha, PASSWORD_DEFAULT),
            ':tipo'  => $tipo,
        ]);
    }

    // Busca um usuário pelo email (usado no login e para evitar email duplicado).
    public function buscarPorEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE email = :email");
        $stmt->execute([':email' => $email]);
        return $stmt->fetch();   // devolve o usuário ou false
    }

    // Busca por id.
    public function buscarPorId($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM usuarios WHERE id = :id");
        $stmt->execute([':id' => $id]);
        return $stmt->fetch();
    }

    // Verifica email + senha. Retorna o usuário se as credenciais baterem, senão false.
    public function verificarLogin($email, $senha)
    {
        $usuario = $this->buscarPorEmail($email);
        // password_verify compara a senha digitada com o hash salvo no banco.
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            return $usuario;
        }
        return false;
    }

    // Lista todos (área do admin).
    public function listarTodos()
    {
        return $this->db->query("SELECT * FROM usuarios ORDER BY criado_em DESC")->fetchAll();
    }

    // Lista apenas técnicos (para atribuir chamados).
    public function listarTecnicos()
    {
        return $this->db->query(
            "SELECT id, nome FROM usuarios WHERE tipo IN ('tecnico','admin') ORDER BY nome"
        )->fetchAll();
    }

    // Atualiza dados básicos (sem mexer na senha).
    public function atualizar($id, $nome, $email, $tipo)
    {
        $sql = "UPDATE usuarios SET nome = :nome, email = :email, tipo = :tipo WHERE id = :id";
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            ':nome'  => $nome,
            ':email' => $email,
            ':tipo'  => $tipo,
            ':id'    => $id,
        ]);
    }

    public function deletar($id)
    {
        $stmt = $this->db->prepare("DELETE FROM usuarios WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }
}
