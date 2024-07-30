<?php
session_start();

// Verifique se o usuário já está logado
if (isset($_SESSION['user'])) {
    if (isset($_SESSION['redirect_to'])) {
        $redirect_to = $_SESSION['redirect_to'];
        unset($_SESSION['redirect_to']);
        header("Location: $redirect_to");
    } else {
        header("Location: login.php");
    }
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
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
        <h2>Login</h2>
        <form method="post" action="login.php">
            <label for="username">Usuário:</label>
            <input type="text" id="username" name="username" required><br>
            <label for="password">Senha:</label>
            <input type="password" id="password" name="password" required><br>
            <input type="submit" value="Login">
        </form>
    </div>
</body>
</html>

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $db_username = "root";
    $db_password = "";
    $dbname = "monitoramento_praga";

    // Crie a conexão com o banco de dados
    $conn = new mysqli($servername, $db_username, $db_password, $dbname);

    // Verifique a conexão
    if ($conn->connect_error) {
        die("Falha na conexão: " . $conn->connect_error);
    }

    $user = $_POST['username'];
    $pass = $_POST['password'];

    // Prepare e execute a consulta
    $sql = "SELECT * FROM usuario WHERE username = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $user);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Verifique a senha usando password_verify
        if (password_verify($pass, $row['password'])) {
            // Senha correta
            $_SESSION['user'] = $user;
            $_SESSION['role'] = $row['role'];
            if (isset($_SESSION['redirect_to'])) {
                $redirect_to = $_SESSION['redirect_to'];
                unset($_SESSION['redirect_to']);
                header("Location: $redirect_to");
            } else {
                echo "Erro!!";
            }
            exit();
        } else {
            // Senha incorreta
            echo "Senha incorreta.";
        }
    } else {
        // Usuário não encontrado
        echo "Usuário não encontrado.";
    }

    $stmt->close();
    $conn->close();
}
?>
