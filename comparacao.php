<?php
// Conexão com o banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "monitoramento_praga";

// Crie a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifique a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

// Verifique se o usuário é um administrador
if ($_SESSION['role'] != 'adm') {
    header("Location: monitoramento_praga.php");
    exit();
}

// Inicialize as variáveis
$ident1 = $nome_lote1 = $data1 = $meidaCigarrinha1 = $mediaMoscaBranca1 = $mediaPercevejo1 = $mediaPulgao1 = $mediaAcaro1 = $mediaTripes1 = "";
$ident2 = $nome_lote2 = $data2 = $meidaCigarrinha2 = $mediaMoscaBranca2 = $mediaPercevejo2 = $mediaPulgao2 = $mediaAcaro2 = $mediaTripes2 = "";
$resul_cg = $resul_mb = $resul_pc = $resul_pg = $resul_ac = $resul_tri = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $id1 = $_POST['id1'];
    $id2 = $_POST['id2'];

    if (is_numeric($id1) && is_numeric($id2)) {
        // Consulta para o primeiro ID
        $query1 = "SELECT * FROM dados WHERE id = $id1";
        $result1 = mysqli_query($conn, $query1);

        while ($row = $result1->fetch_assoc()) {
            $ident1 = $row['id'];
            $nome_lote1 = $row['lote'];
            $data1 = $row['data_prevista'];
            $meidaCigarrinha1 = $row['mediaCigarrinha'];
            $mediaMoscaBranca1 = $row['mediaMoscaBranca'];
            $mediaPercevejo1 = $row['mediaPercevejo'];
            $mediaPulgao1 = $row['mediaPulgao'];
            $mediaAcaro1 = $row['mediaAcaro'];
            $mediaTripes1 = $row['mediaTripes'];
        }

        // Consulta para o segundo ID
        $query2 = "SELECT * FROM dados WHERE id = $id2";
        $result2 = mysqli_query($conn, $query2);

        while ($row = $result2->fetch_assoc()) {
            $ident2 = $row['id'];
            $nome_lote2 = $row['lote'];
            $data2 = $row['data_prevista'];
            $meidaCigarrinha2 = $row['mediaCigarrinha'];
            $mediaMoscaBranca2 = $row['mediaMoscaBranca'];
            $mediaPercevejo2 = $row['mediaPercevejo'];
            $mediaPulgao2 = $row['mediaPulgao'];
            $mediaAcaro2 = $row['mediaAcaro'];
            $mediaTripes2 = $row['mediaTripes'];
        }

        // Calculando resultados
        $resul_cg = $meidaCigarrinha1 - $meidaCigarrinha2;
        $resul_mb = $mediaMoscaBranca1 - $mediaMoscaBranca2;
        $resul_pc = $mediaPercevejo1 - $mediaPercevejo2;
        $resul_pg = $mediaPulgao1 - $mediaPulgao2;
        $resul_ac = $mediaAcaro1 - $mediaAcaro2;
        $resul_tri = $mediaTripes1 - $mediaTripes2;

    } else {
        $erro = "Por favor, insira números válidos para os IDs.";
    }
    
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Comparar Dados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f5f5f5;
            margin: 0;
            padding: 0;
        }

        h1, h2, h3 {
            color: #333;
            text-align: center;
        }

        form {
            text-align: center;
            margin: 20px;
        }

        label {
            margin: 0 10px;
            font-weight: bold;
        }

        input[type="date"],
        input[type="submit"] {
            padding: 5px;
            margin: 0 10px;
        }

        table {
            width: 80%;
            margin: 20px auto;
            border-collapse: collapse;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        th, td {
            padding: 10px;
            border: 1px solid #ddd;
            text-align: center;
        }

        th {
            background-color: #4CAF50;
            color: white;
        }

        tr.header {
            background-color: #f2f2f2;
        }

        tr.total {
            font-weight: bold;
            background-color: #f9f9f9;
        }

        tr.total td {
            color: #333;
        }

        table.media {
            margin-top: 10px;
        }

        table.media th {
            background-color: #2196F3;
        }

        table.media td {
            background-color: #E3F2FD;
        }
        hr {
            width: 80%;
        }
        .th_crono{
            background: #c84d4d;
        }
        #tb_cig {
            background: #c6efce;
        }
        #tb_mb {
            background: #ececec;
        }
        #tb_per {
            background: #fbe4d5;
        }
        #tb_pul {
            background: #c6e9ef;
        }
        #tb_ac {
            background: #ffc7ce;
        }
        #tb_tri {
            background: #ffeb9c;
        }
        .th_obser {
            background: #c84d4d;

        }
        hr {
            width: 80%;
        }
    </style>
</head>
<body>
    <div class="container">
        <?php include 'menu.php'; ?>
        <h1>Comparar Dados</h1>
        <form method="post" action="">
            <label for="id1">ID 1:</label>
            <input type="text" name="id1" id="id1" required>
            <label for="id2">ID 2:</label>
            <input type="text" name="id2" id="id2" required>
            <button type="submit">Comparar</button>
        </form>
    </div>

<?php

//Renomeando as variaveis nome_lote1 
$lote_nome1 = "";

if ($nome_lote1 == "lote_a") {
    $lote_nome1 = "A";
} elseif ($nome_lote1 == "lote_b") {
    $lote_nome1 = "B";
} elseif ($nome_lote1 == "lote_c") {
    $lote_nome1 = "C";
} elseif ($nome_lote1 == "lote_d") {
    $lote_nome1 = "D";
} elseif ($nome_lote1 == "lote_e") {
    $lote_nome1 = "E";
} elseif ($nome_lote1 == "lote_g") {
    $lote_nome1 = "G";
} elseif ($nome_lote1 == "lote_h") {
    $lote_nome1 = "H";
} elseif ($nome_lote1 == "lote_j") {
    $lote_nome1 = "J";
} else {
    $lote_nome1 = "PERN";
}

//Renomeando as variaveis nome_lote12
$lote_nome2 = "";

if ($nome_lote2 == "lote_a") {
    $lote_nome2 = "A";
} elseif ($nome_lote2 == "lote_b") {
    $lote_nome2 = "B";
} elseif ($nome_lote2 == "lote_c") {
    $lote_nome2 = "C";
} elseif ($nome_lote2 == "lote_d") {
    $lote_nome2 = "D";
} elseif ($nome_lote2 == "lote_e") {
    $lote_nome2 = "E";
} elseif ($nome_lote2 == "lote_g") {
    $lote_nome2 = "G";
} elseif ($nome_lote2 == "lote_h") {
    $lote_nome2 = "H";
} elseif ($nome_lote2 == "lote_j") {
    $lote_nome2 = "J";
} else {
    $lote_nome2 = "PERN";
}


    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        echo '<div class="container">';
        echo "<table>";
        echo "<thead>";
        echo "<tr>";
        echo "<th>id</th>";
        echo "<th>Lote</th>";
        echo "<th>Data</th>";
        echo "<th>CG</th>";
        echo "<th>MB</th>";
        echo "<th>PC</th>";
        echo "<th>PG</th>";
        echo "<th>AC</th>";
        echo "<th>TRI</th>";
        echo "</tr>";
        echo "</thead>";
        echo "<tbody>";
        // Informações da Primeira Tabela
        echo "<tr>";
        echo "<td>$ident1</td>";
        echo "<td>$lote_nome1</td>";
        echo "<td>$data1</td>";
        echo "<td id='tb_cig'>$meidaCigarrinha1</td>";
        echo "<td id='tb_mb'>$mediaMoscaBranca1</td>";
        echo "<td id='tb_per'>$mediaPercevejo1</td>";
        echo "<td id='tb_pul'>$mediaPulgao1</td>";
        echo "<td id='tb_ac'>$mediaAcaro1</td>";
        echo "<td id='tb_tri'>$mediaTripes1</td>";
        echo "</tr>";
        // Informações da Segunda Tabela
        echo "<tr>";
        echo "<td>$ident2</td>";
        echo "<td>$lote_nome2</td>";
        echo "<td>$data2</td>";
        echo "<td id='tb_cig'>$meidaCigarrinha2</td>";
        echo "<td id='tb_mb'>$mediaMoscaBranca2</td>";
        echo "<td id='tb_per'>$mediaPercevejo2</td>";
        echo "<td id='tb_pul'>$mediaPulgao2</td>";
        echo "<td id='tb_ac'>$mediaAcaro2</td>";
        echo "<td id='tb_tri'>$mediaTripes2</td>";
        echo "</tr>";
        // Resultados
        echo "<tr>";
        echo "<td>Resultado</td>";
        echo "<td> || </td>";
        echo "<td> || </td>";
        echo "<td id='tb_cig'>$resul_cg</td>";
        echo "<td id='tb_mb'>$resul_mb</td>";
        echo "<td id='tb_per'>$resul_pc</td>";
        echo "<td id='tb_pul'>$resul_pg</td>";
        echo "<td id='tb_ac'>$resul_ac</td>";
        echo "<td id='tb_tri'>$resul_tri</td>";
        echo "</tr>";

        echo "</tbody>";
        echo "</table>";

        echo "</div>";
    }

    // Fechar a conexão
    $conn->close();
    ?>
</body>
</html>
