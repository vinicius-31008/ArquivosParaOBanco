<?php

header('Content-Type: text/html; charset=utf-8');

require 'conexao.php';

if (!isset($_POST['salvar'])) {
    exit;
}

// --- Validação do upload -------------------------------------------------

if (!isset($_FILES['arquivo']) || $_FILES['arquivo']['error'] !== UPLOAD_ERR_OK) {
    die("Nenhum arquivo válido foi enviado. <br><a href='index.php'>Voltar</a>");
}

$nomeOriginal = $_FILES['arquivo']['name'];
$extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

if ($extensao !== 'txt') {
    die("Apenas arquivos .txt são aceitos. <br><a href='index.php'>Voltar</a>");
}

$conteudo = file_get_contents($_FILES['arquivo']['tmp_name']);

// Normaliza quebras de linha (Windows \r\n, Mac \r, Linux \n)
$linhas = preg_split('/\r\n|\r|\n/', $conteudo);

// --- Processamento linha a linha -----------------------------------------

$sql = "INSERT INTO clientes (nome, telefone, email, cpf) VALUES (?, ?, ?, ?)";
$stmt = mysqli_prepare($con, $sql);

$inseridos = 0;
$erros = [];

mysqli_begin_transaction($con);

foreach ($linhas as $numero => $linha) {
    $linha = trim($linha);
    $numeroLinha = $numero + 1;

    if ($linha === '') {
        continue; // ignora linhas em branco silenciosamente
    }

    $campos = explode(';', $linha);

    if (count($campos) !== 4) {
        $erros[] = "Linha {$numeroLinha}: formato inválido (esperado 4 campos separados por ';', encontrado " . count($campos) . ").";
        continue;
    }

    [$nome, $telefone, $email, $cpf] = array_map('trim', $campos);

    if ($nome === '' || $telefone === '' || $email === '' || $cpf === '') {
        $erros[] = "Linha {$numeroLinha}: há campo(s) vazio(s).";
        continue;
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erros[] = "Linha {$numeroLinha}: e-mail inválido ({$email}).";
        continue;
    }

    // Mantém apenas dígitos no CPF para validar o tamanho (11 dígitos)
    $cpfDigitos = preg_replace('/\D/', '', $cpf);
    if (strlen($cpfDigitos) !== 11) {
        $erros[] = "Linha {$numeroLinha}: CPF inválido ({$cpf}).";
        continue;
    }

    mysqli_stmt_bind_param($stmt, 'ssss', $nome, $telefone, $email, $cpf);

    try {
        mysqli_stmt_execute($stmt);
        $inseridos++;
    } catch (mysqli_sql_exception $e) {
        // Ex.: violação da constraint UNIQUE (cpf/email já cadastrado)
        $erros[] = "Linha {$numeroLinha}: não foi possível inserir '{$nome}' — já existe um cadastro com este CPF ou e-mail.";
    }
}

mysqli_commit($con);
mysqli_stmt_close($stmt);
mysqli_close($con);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Resultado da importação</title>
</head>

<body>

    <h1>Resultado da importação</h1>

    <p>
        <strong><?php echo $inseridos; ?></strong> cliente(s) inserido(s) com sucesso.<br>
        <strong><?php echo count($erros); ?></strong> linha(s) ignorada(s) por erro.
    </p>

    <?php if (!empty($erros)): ?>
        <h2>Detalhes das linhas ignoradas</h2>
        <ul>
            <?php foreach ($erros as $erro): ?>
                <li><?php echo htmlspecialchars($erro, ENT_QUOTES, 'UTF-8'); ?></li>
            <?php endforeach; ?>
        </ul>
    <?php endif; ?>

    <br>
    <a href="index.php">Voltar para a página inicial</a>

</body>

</html>
