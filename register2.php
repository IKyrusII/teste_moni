<!--Codigo com a verificação ADM-->

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
    <title>Cadastro de Usuário</title>
</head>
<body>
    <h2>Cadastro de Usuário</h2>
    <form method="post" action="register.php">
        <label for="username">Usuário:</label>
        <input type="text" id="username" name="username" required><br>
        <label for="password">Senha:</label>
        <input type="password" id="password" name="password" required><br>
        <label for="role">Role:</label>
        <select id="role" name="role" required>
            <option value="adm">Administrador</option>
            <option value="user">Usuário</option>
        </select><br>
        <input type="submit" value="Cadastrar">
    </form>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "monitoramento_praga";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    $user = $_POST['username'];
    $pass = $_POST['password'];
    $role = $_POST['role'];

    // Hash da senha
    $hashed_password = password_hash($pass, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuario (username, password, role) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $user, $hashed_password, $role);

    if ($stmt->execute()) {
        echo "Usuário cadastrado com sucesso.";
    } else {
        echo "Erro ao cadastrar o usuário: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
?>
