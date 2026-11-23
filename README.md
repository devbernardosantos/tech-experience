# tech-experience
Mini portal web para inscrição em oficinas técnicas, com HTML5, CSS3, JavaScript e PHP (validação no servidor e proteção contra XSS).

## Funcionalidades

- Formulário semântico e acessível, com label ligado a cada campo e grupos em fieldset/legend
- Layout responsivo e limpo (Grid + Flexbox), em tons claros com destaque azul petróleo
- Contador de caracteres em tempo real na mensagem (limite de 200)
- Validação no navegador: impede o envio sem nenhuma oficina marcada
- Validação no servidor (PHP) com listas permitidas (whitelists), independente do JavaScript
- Saída protegida contra XSS com htmlspecialchars
- Resposta 405 Method Not Allowed para acesso direto ao processar.php

## Tecnologias

HTML5, no index.html para estruturar o formulário, cabeçalho e acessibilidade;
CSS3, no style.css para design responsivo, estados de foco e de erro;
JavaScript, no script.js para fazer o contador de caracteres e validação interativa;
PHP 8+, no processar.php para fazer a recepção via POST, validação e renderização segura.

## Como executar

Requisitos: PHP 8 ou superior, com a extensão mbstring ativa.

no bash/terminal:

cd tech-experience
php -S localhost:8000


Depois, abra <http://localhost:8000/index.html>.

> Windows: o PHP baixado do windows.php.net vem sem php.ini. Copie php.ini-development para php.ini e remova o ';' das linhas extension_dir = "ext" e extension=mbstring.

## Segurança

O servidor é a autoridade final: tudo o que o navegador valida é validado de novo no PHP.

- Métodos diferentes de POST recebem status 405
- Campos lidos com ?? e trim(), e valores que não sejam texto são descartados
- Nível, turno e oficinas conferidos com in_array(..., true) contra listas permitidas
- E-mail validado com filter_var(..., FILTER_VALIDATE_EMAIL)
- Todo texto exibido passa por htmlspecialchars($dado, ENT_QUOTES, UTF-8)

## Testes realizados

- Envio válido exibe o cartão de confirmação
- Nome <script>alert("XSS")</script> aparece como texto literal, sem executar
- Acesso direto a processar.php retorna 405
- DevTools → Network: método POST, status 200 e dados no Payload
- Envio sem oficina é bloqueado pelo JavaScript
- Console do navegador sem erros

## Estrutura

tech-experience/
├── index.html
├── style.css
├── script.js
└── processar.php
