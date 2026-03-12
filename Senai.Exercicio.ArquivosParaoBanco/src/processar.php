<?php

include 'conexao.php';

if(isset($_POST['salvar'])){
    $arquivo = $_FILES['arquivo']['tmp_name'];

    if($arquivo){
        $conteudo = file_get_contents($arquivo);
        $linhas = explode("\n", $conteudo);

        foreach($linhas as $linha){
            if(!empty(trim($linha))){
                list($nome, $telefone, $email, $cpf) = explode(";", $linha);

                $sql = "INSERT INTO clientes (nome, telefone, email, cpf) 
                VALUES ('$nome', '$telefone', '$email', '$cpf')";

                if(!mysqli_query($con, $sql)){
                    echo "Erro ao inserir dados: ". mysqli_error($con);
                }
            }
        }

        echo "Dados inseridos com sucesso!<br>";
        echo "<a href=\"/projects/ArquivosParaBanco/Senai.Exercicio.ArquivosParaoBanco/src/\">Voltar para a página inicial</a>";
    }else{
        echo "Nenhum arquivo inserido";
    }
}

?>