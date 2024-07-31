<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redirecionar</title>
    <!-- Meta tag para redirecionamento -->
    <meta http-equiv="refresh" content="3;url=exibir_lotes.php">
    <!-- Estilos CSS -->
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }
        .container {
            text-align: center;
        }
        h1 {
            color: #333;
        }
        p {
            color: #666;
        }
    </style>
    <script>
        // Redirecionamento usando JavaScript como alternativa
        setTimeout(function() {
            window.location.href = 'exibir_lotes.php';
        }, 3000);
    </script>
</head>
<body>
    <div class="container">
        <h1>Redirecionando...</h1>
        <p>Você será redirecionado para a página de lotes em breve.</p>
        <p>Se o redirecionamento não funcionar, <a href="exibir_lotes.php">clique aqui</a>.</p>
    </div>
</body>
</html>