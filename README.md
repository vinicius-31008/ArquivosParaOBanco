# ArquivosParaBanco

Exercicio do senai

## Melhorias aplicadas nesta versão

- **Segurança:** substituição de SQL concatenado por *prepared statements* (`mysqli_prepare` / `bind_param`), eliminando risco de SQL Injection em `processar.php`.
- **Integridade dos dados:** `UNIQUE KEY` em `cpf` e `email` na tabela `clientes`, evitando duplicidade ao reenviar o mesmo arquivo.
- **Codificação:** conexão e tabela configuradas em `utf8mb4`, evitando corrupção de nomes/e-mails com acentuação (ex.: "João", "Araújo").
- **Validação de dados:** cada linha do TXT é validada (quantidade de campos, campos vazios, formato de e-mail, CPF com 11 dígitos) antes de tentar inserir; linhas inválidas são reportadas, não travam o processo.
- **Tratamento de erros:** `mysqli_report` habilitado para lançar exceções em erros do banco (ex.: duplicidade), tratadas com `try/catch` em vez de falhar silenciosamente.
- **Robustez de upload:** validação de erro de upload (`$_FILES['arquivo']['error']`) e da extensão do arquivo no servidor (não confia apenas no `accept=".txt"` do HTML).
- **Usabilidade:** ao final da importação, o usuário vê um relatório com quantidade de registros inseridos e a lista de linhas ignoradas com o motivo (antes só aparecia um erro genérico e a importação parava no meio se houvesse uma linha mal formatada).
- **Performance:** inserções em lote dentro de uma transação (`begin_transaction` / `commit`), em vez de uma transação implícita por linha.
