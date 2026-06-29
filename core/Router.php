<?php

class Router
{
    public function run()
    {
    
        $url = $_GET['url'] ?? 'auth/login';
        $url = trim($url, '/');

        $partes = explode('/', $url);

        $nomeController = ucfirst($partes[0]) . 'Controller';

        $metodo = $partes[1] ?? 'index';

        $parametros = array_slice($partes, 2);

        if (!class_exists($nomeController)) {
            http_response_code(404);
            die("Controller '{$nomeController}' não encontrado.");
        }

        $controller = new $nomeController();

        if (!method_exists($controller, $metodo)) {
            http_response_code(404);
            die("Método '{$metodo}' não encontrado em {$nomeController}.");
        }

        call_user_func_array([$controller, $metodo], $parametros);
    }
}
