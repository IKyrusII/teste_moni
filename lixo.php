<?php
// Defina as credenciais do banco de dados
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

// Inicialize as variáveis de data
$data_inicial = "";
$data_final = "";

// Verifique se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data_inicial = $_POST['data_inicial'];
    $data_final = $_POST['data_final'];
}

// Condição de data para a consulta SQL
$condicao_data = "";
if (!empty($data_inicial) && !empty($data_final)) {
    $condicao_data = "WHERE data_cadastro BETWEEN '$data_inicial' AND '$data_final'";
}

// Consulta SQL para selecionar todos os lotes agrupados por lote, linha e ponto, somando as colunas especificadas
$sql = "SELECT lote, linha, ponto, 
               SUM(somaCigarrinha) AS somaCigarrinha, 
               SUM(somaMoscaBranca) AS somaMoscaBranca, 
               SUM(somaPercevejo) AS somaPercevejo, 
               SUM(somaPulgao) AS somaPulgao, 
               SUM(somaAcaro) AS somaAcaro, 
               SUM(somaTripes) AS somaTripes 
        FROM lotes 
        $condicao_data
        GROUP BY lote, linha, ponto";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="teste.js" defer></script>
    <title>Entrada de Dados</title>
    <!--Revalocar os comandos css para o arquivo exibir_telas-->
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

        #borda_lote {
            border: 3px;
        }

        hr {
            width: 80%;
        }
        .th_crono{
            background: #c84d4d;
        }
        .th_linha {
            background: #c1b85d;
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

    </style>
</head>

<body>
    <h1>Exibir Entrada de Dados</h1>
    <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
        <label for="data_inicial">Data Inicial:</label>
        <input type="date" id="data_inicial" name="data_inicial" value="<?php echo $data_inicial; ?>">
        <label for="data_final">Data Final:</label>
        <input type="date" id="data_final" name="data_final" value="<?php echo $data_final; ?>">
        <input type="submit" value="Filtrar">
    </form>
    <br>
    <?php
    // Verifique se há resultados
    if ($result->num_rows > 0) {
        // Variável para armazenar o lote atual
        $lote_atual = null;
        
        // Inicialize uma matriz para armazenar os dados por lote
        $dados_por_lote = [];

        echo '<div id="borda_lote">';

        // Iterar sobre os resultados e agrupar os dados por lote e linha
        while ($row = $result->fetch_assoc()) {
            $lote = $row['lote'];
            $linha = $row['linha'];
            $ponto = $row['ponto'];
            
            // Crie a estrutura do lote se não existir
            if (!isset($dados_por_lote[$lote])) {
                $dados_por_lote[$lote] = [];
            }

            // Adicione os dados do ponto à linha do lote
            $dados_por_lote[$lote][$linha][] = $row;
        }
         
        // Iterar sobre os dados por lote para exibir as tabelas
        foreach ($dados_por_lote as $lote => $linhas) {

            $lote_nome = "";

            if ($lote == "lote_a") {
                $lote_nome = "A";
            } elseif ($lote == "lote_b") {
                $lote_nome = "B";
            } elseif ($lote == "lote_c") {
                $lote_nome = "C";
            } elseif ($lote == "lote_d") {
                $lote_nome = "D";
            } elseif ($lote == "lote_e") {
                $lote_nome = "E";
            } elseif ($lote == "lote_g") {
                $lote_nome = "G";
            } elseif ($lote == "lote_h") {
                $lote_nome = "H";
            } elseif ($lote == "lote_j") {
                $lote_nome = "J";
            } else {
                $lote_nome = "PERN";
            }

             echo "<br>";
            echo "<div>";
            echo '<table>'; 
                echo "<table>";
                echo "<tr>";
                echo "<th class='th_crono'>Lote: {$lote_nome} </th>";
                echo "</tr>";
                echo "</table>";
      
            foreach ($linhas as $linha => $pontos) {
                    echo "<table>";
                    echo "<tr>";
                    echo "<th class='th_linha'>$linha</th>";
                    echo "</tr>";
                    echo "</table>";

                
                echo "<table>";
                echo "<tr class='header'>";
                echo "<th>Ponto</th>";
                echo "<th>CG</th>";
                echo "<th>MB</th>";
                echo "<th>PC</th>";
                echo "<th>PG</th>";
                echo "<th>AC</th>";
                echo "<th>TRP</th>";
                echo "</tr>";

                // Totais por linha
                $total_cig = 0;
                $total_mb = 0;
                $total_per = 0;
                $total_pul = 0;
                $total_ac = 0;
                $total_tri = 0;

                foreach ($pontos as $dados) {
                    echo "<tr>";
                    echo "<td class='pon'>" . $dados['ponto'] . "</td>";
                    echo "<td class='CG'>" . $dados['somaCigarrinha'] . "</td>";
                    echo "<td class='MB'>" . $dados['somaMoscaBranca'] . "</td>";
                    echo "<td class='PERC'>" . $dados['somaPercevejo'] . "</td>";
                    echo "<td class='PUL'>" . $dados['somaPulgao'] . "</td>";
                    echo "<td class='AC'>" . $dados['somaAcaro'] . "</td>";
                    echo "<td class='TRI'>" . $dados['somaTripes'] . "</td>";
                    echo "</tr>";

                    // Acumulando os totais por linha
                    $total_cig += $dados['somaCigarrinha'];
                    $total_mb += $dados['somaMoscaBranca'];
                    $total_per += $dados['somaPercevejo'];
                    $total_pul += $dados['somaPulgao'];
                    $total_ac += $dados['somaAcaro'];
                    $total_tri += $dados['somaTripes'];
                }

                echo "<tr class='total'>";
                echo "<td>Total {$linha}</td>";
                echo "<td>$total_cig</td>";
                echo "<td>$total_mb</td>";
                echo "<td>$total_per</td>";
                echo "<td>$total_pul</td>";
                echo "<td>$total_ac</td>";
                echo "<td>$total_tri</td>";
                echo "</tr>";
                echo "</table>";

            }

            // Fecha os ciclos dos pontos dentro das linha
            echo '</table>';
            echo "<hr>";
        }
        echo "</div>";
    } else {
        echo "<p>Nenhum lote cadastrado.</p>";
    }
    // Fechar a conexão
    $conn->close();
    ?>
</body>
</html>
