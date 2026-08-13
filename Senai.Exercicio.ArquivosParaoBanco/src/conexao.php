<?php

// Faz o mysqli lançar exceções em vez de gerar apenas warnings silenciosos.
// Isso permite tratar erros (ex.: cpf/email duplicado) com try/catch em processar.php
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "sistema";

try {
    $con = mysqli_connect($servername, $username, $password, $dbname);
    mysqli_set_charset($con, "utf8mb4");
} catch (mysqli_sql_exception $e) {
    die("Falha na conexão com o banco de dados: " . $e->getMessage());
}

?>
