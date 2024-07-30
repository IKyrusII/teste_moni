<!--Codigo com verivicação de ADM-->

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
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            width: 300px;
            text-align: center;
        }

        h2 {
            margin-bottom: 20px;
            color: #333;
        }

        label {
            display: block;
            margin-bottom: 5px;
            color: #666;
        }

        input[type="text"],
        input[type="password"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="submit"] {
            width: 100%;
            padding: 10px;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .message {
            color: green;
            margin-top: 10px;
        }

        .error-message {
            color: red;
            margin-top: 10px;
        }

    </style>
</head>
<body>
    <div class="container">
        <h2>Cadastro de Usuário</h2>
        <form method="post" action="register.php">
            <label for="username">Usuário:</label>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required><br>
            <label for="role">Role:</label>
            <select id="role" name="role" required>
                <option value="adm">Gestor</option>
                <option value="user">Monitor</option>
            </select><br>
            <input type="submit" value="Cadastrar">
        </form>
    </div>
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


