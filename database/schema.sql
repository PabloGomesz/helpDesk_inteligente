-- ============================================================
-- HelpDesk Inteligente - Schema do Banco de Dados
-- Rode este arquivo UMA vez no phpMyAdmin ou via linha de comando:
--   mysql -u root -p < database/schema.sql
-- ============================================================

CREATE DATABASE IF NOT EXISTS helpdesk_ai_db
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE helpdesk_ai_db;

-- ------------------------------------------------------------
-- Ordem importa por causa das FOREIGN KEYS:
-- usuarios e categorias primeiro; chamados depois; por fim
-- comentarios e historico_ia (que referenciam chamados).
-- ------------------------------------------------------------

CREATE TABLE usuarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('usuario', 'tecnico', 'admin') DEFAULT 'usuario',
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE categorias (
    id INT PRIMARY KEY AUTO_INCREMENT,
    nome VARCHAR(50) NOT NULL,
    descricao TEXT
);

CREATE TABLE chamados (
    id INT PRIMARY KEY AUTO_INCREMENT,
    titulo VARCHAR(100) NOT NULL,
    descricao TEXT NOT NULL,
    prioridade ENUM('baixa', 'media', 'alta', 'critica') DEFAULT 'media',
    status ENUM('aberto', 'em_andamento', 'aguardando', 'resolvido', 'fechado') DEFAULT 'aberto',
    usuario_id INT,
    tecnico_id INT NULL,
    categoria_id INT NULL,
    classificacao_ia JSON,              -- Armazena classificacao da IA
    solucao_sugerida_ia TEXT,
    resolvido_em DATETIME,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    atualizado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id),
    FOREIGN KEY (tecnico_id) REFERENCES usuarios(id),
    FOREIGN KEY (categoria_id) REFERENCES categorias(id)
);

CREATE TABLE comentarios (
    id INT PRIMARY KEY AUTO_INCREMENT,
    chamado_id INT NOT NULL,
    usuario_id INT NOT NULL,
    mensagem TEXT NOT NULL,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chamado_id) REFERENCES chamados(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE historico_ia (
    id INT PRIMARY KEY AUTO_INCREMENT,
    chamado_id INT,
    tipo VARCHAR(20),                   -- 'classificacao', 'sugestao', 'analise'
    prompt TEXT,
    resposta TEXT,
    criado_em TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (chamado_id) REFERENCES chamados(id) ON DELETE CASCADE
);
