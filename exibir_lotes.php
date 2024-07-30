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

session_start();

// Verifique se o usuário está logado
if (!isset($_SESSION['user'])) {
    $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
    header("Location: login.php");
    exit();
}

// Verifique se o usuário é um administrador
if ($_SESSION['role'] != 'adm') {
    echo "Você não tem acesso a essa pagina!";
    header("Location: monitoramento_praga.php");
    exit();
}

// Inicialize as variáveis de data
$data_inicial = "";
$data_final = "";

// Verifique se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $data_inicial = $_POST['data_inicial'];
    $data_final = $_POST['data_final'];
    if (isset($_POST['delete_id'])) {
        $delete_id = $_POST['delete_id'];
        $delete_sql = "DELETE FROM lotes WHERE id = $delete_id";
        $conn->query($delete_sql);
    }
}

// Condição de data para a consulta SQL
$condicao_data = "";
if (!empty($data_inicial) && !empty($data_final)) {
    $condicao_data = "WHERE data_cadastro BETWEEN '$data_inicial' AND '$data_final'";
}

// Consulta SQL para selecionar todos os lotes agrupados por lote, linha e ponto, somando as colunas especificadas
$sql = "SELECT id, lote, linha, ponto, observacao,
               SUM(somaCigarrinha) AS somaCigarrinha, 
               SUM(somaMoscaBranca) AS somaMoscaBranca, 
               SUM(somaPercevejo) AS somaPercevejo, 
               SUM(somaPulgao) AS somaPulgao, 
               SUM(somaAcaro) AS somaAcaro, 
               SUM(somaTripes) AS somaTripes 
        FROM lotes 
        $condicao_data
        GROUP BY lote, linha, ponto, id";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="telas_exibi.css">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="teste.js" defer></script>
    <title>Exibir Lotes</title>
</head>
<body>
    <?php include 'menu.php'; ?>

    <h1>Exibir Entrada de dados</h1>
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

            echo "<h2>Lote: " . $lote_nome . "</h2>";
            echo "<table>";
                echo "<tr class='header'>";
                echo "<th>Linha</th>";
                echo "<th>Ponto</th>";
                echo "<th>CG</th>";
                echo "<th>MB</th>";
                echo "<th>PC</th>";
                echo "<th>PG</th>";
                echo "<th>AC</th>";
                echo "<th>TRP</th>";
                echo "<th>Ações</th>";
                echo "</tr>";

                //total de insetos por ponto
                $total_cig = 0;
                $total_mb = 0;
                $total_per = 0;
                $total_pul = 0;
                $total_ac = 0;
                $total_tri = 0;

                foreach ($linhas as $linha => $pontos) {
                    $linha_fomat = "";

                    if ($linha == "linha_3") {
                        $linha_format = "Linha 3";
                    } elseif ($linha == "linha_10") {
                        $linha_format = "Linha 10";
                    } elseif ($linha == "linha_30") {
                        $linha_format = "Linha 30";
                    } elseif ($linha == "linha_46") {
                        $linha_format = "Linha 46 ";
                    } elseif ($linha == "linha_50") {
                        $linha_format = "Linha 50";
                    } else {
                        $lote_format = "";
                    }
                    $soma_cig = 0;
                    $soma_mb = 0;
                    $soma_per = 0;
                    $soma_pul = 0;
                    $soma_ac = 0;
                    $soma_tri = 0;

                    foreach ($pontos as $dados) {
                        $p = $dados['ponto'];
                        $ponto_format = "";

                        if ($p == "ponto_1") {
                            $ponto_format = "Ponto 1";
                        } elseif ($p == "ponto_2") {
                            $ponto_format = "Ponto 2";
                        } elseif ($p == "ponto_3") {
                            $ponto_format = "Ponto 3";
                        } elseif ($p == "ponto_4") {
                            $ponto_format = "Ponto 4 ";
                        } elseif ($p == "ponto_5") {
                            $ponto_format = "Ponto 5";
                        } elseif ($p == "ponto_6") {
                            $ponto_format = "Ponto 6";
                        } else {
                            $ponto_format = "Ponto invalido!!";
                        }

                        echo "<tr>";
                        echo '<td>' . $linha_format . '</td>';
                        echo '<td class="pon">' . $ponto_format . '</td>';
                        echo '<td class="CG">' . $dados['somaCigarrinha'] . '</td>';
                        echo '<td class="MB">' . $dados['somaMoscaBranca'] . '</td>';
                        echo '<td class="PERC">' . $dados['somaPercevejo'] . '</td>';
                        echo '<td class="PUL">' . $dados['somaPulgao'] . '</td>';
                        echo '<td class="AC">' . $dados['somaAcaro'] . '</td>';
                        echo '<td class="TRI">' . $dados['somaTripes'] . '</td>';
                        echo '<td><form method="post" action="'.htmlspecialchars($_SERVER["PHP_SELF"]).'">
                                <input type="hidden" name="delete_id" value="'.$dados['id'].'">
                                <input type="submit" value="Excluir">
                            </form></td>';
                        echo "</tr>";

                        $soma_cig = $soma_cig + $dados['somaCigarrinha'];
                        $soma_mb = $soma_mb + $dados['somaMoscaBranca'];
                        $soma_per = $soma_per + $dados['somaPercevejo'];
                        $soma_pul = $soma_pul + $dados['somaPulgao'];
                        $soma_ac = $soma_ac + $dados['somaAcaro'];
                        $soma_tri = $soma_tri + $dados['somaTripes'];
                    }
                    $total_cig = $total_cig + $soma_cig;
                    $total_mb = $total_mb + $soma_mb;
                    $total_per = $total_per + $soma_per;
                    $total_pul = $total_pul + $soma_pul;
                    $total_ac = $total_ac + $soma_ac;
                    $total_tri = $total_tri + $soma_tri;

                    $observacao = $dados['observacao'];
                    echo"<tr>";
                        echo '<td>Observação</td>';
                        echo "<td id='obs'>$observacao</td>";
                    echo "</tr>";

                    echo"<tr class='total'>";
                        echo "<td >Total</td>";
                        echo "<td></td>";
                        echo "<td>$soma_cig</td>";
                        echo "<td>$soma_mb</td>";
                        echo "<td>$soma_per</td>";
                        echo "<td>$soma_pul</td>";
                        echo "<td>$soma_ac</td>";
                        echo "<td>$soma_tri</td>";
                        echo "<td></td>";
                    echo "</tr>";
  
                    
                }

            
            echo "</table>";
        }
    } else {
        echo "<p>Nenhum lote cadastrado.</p>";
    }
    // Fechar a conexão
    $conn->close();
    ?>
</body>
</html>
