<?php

session_start();

// Constantes de caminho usadas em links, redirecionamentos e assets.
// Se você mudar o nome da pasta do projeto, ajuste só aqui.
define('BASE_URL', '/HelpDesk_inteligente/public/?url=');  // base para rotas: BASE_URL.'chamado/index'
define('ASSETS',   '/HelpDesk_inteligente/public/');       // base para css/js: ASSETS.'css/style.css'

spl_autoload_register(function ($classe) {
    $pastas = ['core', 'controllers', 'models'];
    foreach ($pastas as $pasta) {
        $arquivo = __DIR__ . '/../' . $pasta . '/' . $classe . '.php';
        if (file_exists($arquivo)) {
            require_once $arquivo;
            return;
        }
    }
});

// 3) Entrega o controle para o Router, que vai ler a URL e chamar quem deve.
$router = new Router();
$router->run();
