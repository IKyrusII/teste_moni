<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'adm') {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Página Restrita</title>
</head>
<body>
    <h1>Bem-vindo, <?php echo $_SESSION['user']; ?></h1>
    <!-- O conteúdo da página restrita vai aqui -->
</body>
</html>

<!--Esta parte do codigo deve ser adicionada em cada pagina que possuir restrição de usuario-->
<?php
session_start();

if (!isset($_SESSION['user']) || $_SESSION['role'] !== 'adm') {
    header("Location: login.php");
    exit();
}

// O código original da página continua aqui...
?>
