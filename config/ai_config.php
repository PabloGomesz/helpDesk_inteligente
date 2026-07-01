<?php
// config/ai_config.php
// Lê a configuração da IA do .env. NÃO contém segredos -> pode ir para o Git.

return [
    'api_key'  => getenv('GEMINI_API_KEY') ?: '',
    'modelo'   => getenv('GEMINI_MODELO') ?: 'gemini-2.5-flash',
    'url_base' => 'https://generativelanguage.googleapis.com/v1beta/models/',
];
