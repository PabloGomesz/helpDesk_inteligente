<?php
// core/Env.php
// Lê o arquivo .env e joga cada "CHAVE=valor" para dentro de getenv()/$_ENV.

class Env
{
    public static function carregar($caminho)
    {
        if (!file_exists($caminho)) {
            return;
        }

        foreach (file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
            $linha = trim($linha);

            // Ignora linhas vazias e comentários (#).
            if ($linha === '' || $linha[0] === '#' || strpos($linha, '=') === false) {
                continue;
            }

            list($chave, $valor) = explode('=', $linha, 2);
            $chave = trim($chave);
            $valor = trim(trim($valor), "\"'"); // remove espaços e aspas

            putenv("{$chave}={$valor}");
            $_ENV[$chave] = $valor;
        }
    }
}
