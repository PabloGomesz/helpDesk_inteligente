<?php
// controllers/AIController.php
// Integração com a IA (Google Gemini). NÃO é chamado pela URL: é usado por
// dentro do ChamadoController para analisar o texto de um chamado.
//
// Faz tudo em UMA chamada à API: classificação + prioridade + sugestão de
// solução + palavras-chave + análise de sentimento.

class AIController
{
    private $config;

    public function __construct()
    {
        $this->config = require __DIR__ . '/../config/ai_config.php';
    }

    // A chave foi configurada? (se não, o sistema segue funcionando sem IA)
    public function estaConfigurada()
    {
        return !empty($this->config['api_key'])
            && $this->config['api_key'] !== 'COLE_SUA_CHAVE_AQUI';
    }

    // Analisa um chamado e devolve um array estruturado, ou null se falhar.
    // Retorno: [prioridade, categoria, sugestao_solucao, palavras_chave[], sentimento, urgencia_emocional, _raw]
    public function analisarChamado($titulo, $descricao)
    {
        if (!$this->estaConfigurada()) {
            return null;
        }

        $prompt = $this->montarPrompt($titulo, $descricao);
        $resposta = $this->chamarGemini($prompt);
        if ($resposta === null) {
            return null;
        }

        // A IA pode devolver o JSON cercado por ```json ... ```. Limpamos isso.
        $limpo = trim($resposta);
        $limpo = preg_replace('/^```(json)?|```$/m', '', $limpo);
        $dados = json_decode(trim($limpo), true);

        if (!is_array($dados)) {
            return null; // veio algo que não é JSON válido
        }

        // Guardamos o texto cru também (para salvar no log e no campo JSON).
        $dados['_raw'] = $resposta;
        return $dados;
    }

    // Monta as instruções para a IA, pedindo resposta SOMENTE em JSON.
    private function montarPrompt($titulo, $descricao)
    {
        return <<<PROMPT
Você é um assistente de suporte técnico (Help Desk). Analise o chamado abaixo e
responda APENAS com um objeto JSON válido, sem texto antes ou depois, sem markdown.

Chamado:
Título: {$titulo}
Descrição: {$descricao}

Responda exatamente neste formato:
{
  "prioridade": "baixa|media|alta|critica",
  "categoria": "hardware|software|rede|acesso|outro",
  "sugestao_solucao": "Passo a passo curto e prático para resolver o problema.",
  "palavras_chave": ["palavra1", "palavra2", "palavra3"],
  "sentimento": "neutro|frustrado|irritado|urgente",
  "urgencia_emocional": true
}

Regras:
- "prioridade": use "critica" só se o problema parar o trabalho da pessoa.
- "categoria": escolha exatamente uma das opções listadas.
- "urgencia_emocional": true se o texto demonstra estresse/urgência emocional, senão false.
PROMPT;
    }

    // Faz a requisição HTTP para a API do Gemini usando cURL.
    private function chamarGemini($prompt)
    {
        $url = $this->config['url_base']
             . $this->config['modelo']
             . ':generateContent?key=' . $this->config['api_key'];

        $payload = [
            'contents' => [
                ['parts' => [['text' => $prompt]]]
            ],
            // Temperatura baixa = respostas mais consistentes/objetivas.
            'generationConfig' => ['temperature' => 0.3],
        ];

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 30,
        ]);

        $resposta = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        if ($resposta === false || $httpCode !== 200) {
            return null; // falha de rede ou erro da API -> sistema segue sem IA
        }

        $json = json_decode($resposta, true);
        // Caminho do texto na resposta do Gemini:
        return $json['candidates'][0]['content']['parts'][0]['text'] ?? null;
    }
}
