# HelpDesk Inteligente

Sistema de gerenciamento de chamados de suporte em **PHP (MVC puro)** com **MySQL**,
interface em **Bootstrap 5** e classificação automática de chamados via **IA (Google Gemini)**.

## Funcionalidades
- Autenticação com 3 perfis: usuário, técnico e administrador
- CRUD de chamados com filtros (status, prioridade, categoria, busca)
- Comentários e mudança de status pelos técnicos
- **IA**: classifica prioridade/categoria, sugere solução, extrai palavras-chave e detecta urgência emocional
- Dashboard com estatísticas e gráficos (Chart.js)
- Área administrativa: usuários e categorias

## Requisitos
- XAMPP (Apache + MySQL + PHP 7.4+)
- Extensões PHP: `pdo_mysql` e `curl` (já vêm no XAMPP)

## Instalação
1. Coloque o projeto em `htdocs/HelpDesk_inteligente`.
2. Inicie o **Apache** e o **MySQL** no XAMPP.
3. No phpMyAdmin (`http://localhost/phpmyadmin`), aba **Importar**, selecione
   `database/schema.sql` (cria o banco e os dados iniciais).
4. Configure a chave da IA:
   - Copie o arquivo `.env.example` para `.env`.
   - Gere uma chave gratuita em https://aistudio.google.com/app/apikey.
   - Cole a chave no `.env`, no campo `GEMINI_API_KEY`.
5. Acesse: `http://localhost/HelpDesk_inteligente/public/`

> A IA é opcional: sem a chave, o sistema funciona normalmente, apenas sem a
> análise automática dos chamados.

## Acesso inicial (admin)
- E-mail: `admin@helpdesk.com`
- Senha: `admin123`

## Estrutura
```
config/      credenciais do banco e da IA
core/        Database, Router e Controller base
controllers/ lógica das rotas
models/      acesso ao banco (uma classe por tabela)
views/       telas (Bootstrap)
public/      ponto de entrada, CSS, uploads
database/    schema.sql
```
