<?php
declare(strict_types=1);

// 1) Aceita apenas POST. Acesso direto pela barra de endereço (GET) é bloqueado.
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    header('Content-Type: text/plain; charset=UTF-8');
    exit('405 - Método não permitido. Preencha o formulário em index.html para enviar a inscrição.');
}

//Listas permitidas (whitelists) e rótulos para exibição
$rotulosNivel = [
    'iniciante'     => 'Iniciante',
    'intermediario' => 'Intermediário',
    'avancado'      => 'Avançado',
];
$rotulosTurno = [
    'manha' => 'Manhã',
    'noite' => 'Noite',
];
$rotulosOficina = [
    'html_css'    => 'HTML5 e CSS3',
    'js_dom'      => 'JavaScript e DOM',
    'php_backend' => 'PHP e Back-end',
];

$niveisPermitidos   = array_keys($rotulosNivel);
$turnosPermitidos   = array_keys($rotulosTurno);
$oficinasPermitidas = array_keys($rotulosOficina);

//Funções auxiliares

function e(string $texto): string
{
    return htmlspecialchars($texto, ENT_QUOTES, 'UTF-8');
}

function campo(string $nome): string
{
    $valor = $_POST[$nome] ?? '';
    return is_string($valor) ? trim($valor) : '';
}

//2) Leitura e normalização
$nome        = campo('nome');
$email       = campo('email');
$experiencia = campo('experiencia');
$turno       = campo('turno');
$mensagem    = str_replace(["\r\n", "\r"], "\n", campo('mensagem'));

$oficinas = $_POST['oficinas'] ?? [];
if (is_array($oficinas)) {
    $oficinas = array_map(static fn($item) => is_string($item) ? trim($item) : $item, $oficinas);
}

//3) Validação
$erros = [];

if (mb_strlen($nome, 'UTF-8') < 3) {
    $erros[] = 'Informe o nome completo com pelo menos 3 caracteres.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    $erros[] = 'Informe um e-mail em formato válido.';
}

if ($experiencia === '') {
    $erros[] = 'Selecione o nível de experiência.';
} elseif (!in_array($experiencia, $niveisPermitidos, true)) {
    $erros[] = 'O nível de experiência escolhido é inválido.';
}

if ($turno === '') {
    $erros[] = 'Selecione o turno de preferência.';
} elseif (!in_array($turno, $turnosPermitidos, true)) {
    $erros[] = 'O turno escolhido é inválido.';
}

if (!is_array($oficinas) || count($oficinas) === 0) {
    $erros[] = 'Selecione pelo menos uma oficina.';
} else {
    foreach ($oficinas as $item) {
        if (!in_array($item, $oficinasPermitidas, true)) {
            $erros[] = 'Uma das oficinas selecionadas é inválida.';
            break;
        }
    }
}

if (mb_strlen($mensagem, 'UTF-8') > 200) {
    $erros[] = 'A mensagem deve ter no máximo 200 caracteres.';
}

$valido = count($erros) === 0;

//4) Resposta em HTML (todo texto ecoado passa por e())
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= $valido ? 'Inscrição confirmada' : 'Corrija os dados' ?> — Tech Experience 2026</title>
  <link rel="icon" href="data:,">
  <link rel="stylesheet" href="style.css">
</head>
<body>

  <header class="site-header">
    <div class="container">
      <h1>Tech Experience 2026</h1>
      <p class="subtitle">Inscrição em oficinas técnicas</p>
    </div>
  </header>

  <main class="container">
<?php if (!$valido): ?>
    <section class="card card--error" aria-labelledby="titulo-resultado">
      <h2 id="titulo-resultado">Não foi possível concluir a inscrição</h2>
      <p class="hint">Corrija os pontos abaixo e envie o formulário novamente.</p>
      <ul class="error-list">
<?php foreach ($erros as $erro): ?>
        <li><?= e($erro) ?></li>
<?php endforeach; ?>
      </ul>
      <a class="btn" href="index.html">Voltar ao formulário</a>
    </section>
<?php else: ?>
    <section class="card card--success" aria-labelledby="titulo-resultado">
      <h2 id="titulo-resultado">Inscrição confirmada!</h2>
      <p class="hint">Confira os dados cadastrados.</p>
      <dl class="summary">
        <dt>Nome</dt>
        <dd><?= e($nome) ?></dd>

        <dt>E-mail</dt>
        <dd><?= e($email) ?></dd>

        <dt>Nível</dt>
        <dd><?= e($rotulosNivel[$experiencia]) ?></dd>

        <dt>Turno</dt>
        <dd><?= e($rotulosTurno[$turno]) ?></dd>

        <dt>Oficinas</dt>
        <dd>
          <ul>
<?php foreach ($oficinas as $codigo): ?>
            <li><?= e($rotulosOficina[$codigo]) ?></li>
<?php endforeach; ?>
          </ul>
        </dd>

        <dt>Mensagem</dt>
        <dd><p class="message-text"><?= e($mensagem !== '' ? $mensagem : 'Não informada.') ?></p></dd>
      </dl>
      <a class="btn" href="index.html">Nova inscrição</a>
    </section>
<?php endif; ?>
  </main>

  <footer class="site-footer">
    <div class="container">
      <p>&copy; 2026 Tech Experience — Projeto acadêmico</p>
    </div>
  </footer>

</body>
</html>
