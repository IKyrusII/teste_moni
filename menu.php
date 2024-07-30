<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu de Navegação</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .navbar {
            display: flex;
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .botao_sair {
            color: #d92525 !important;
            margin-left: auto;
            margin-right: 2%;
        }
    </style>
</head>
<body>

<div class="navbar">
    <a href="monitoramento_praga.php">Monitoramento</a>
    <a href="exibir_lotes.php">Exibir Lotes</a>
    <a href="criacao_dados.php">Criação de Dados</a>
    <a href="exibir_dados.php">Exibir Dados</a>
    <a href="comparacao.php">Comparação</a>

    <a href="logout.php" class="botao_sair">Sair</a>
    
</div>

</body>
</html>
