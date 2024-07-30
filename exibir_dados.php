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

// Função para obter todos os lotes disponíveis
function obterLotes($conn) {
    $sql = "SELECT DISTINCT lote FROM dados ORDER BY id DESC";
    $result = $conn->query($sql);
    $lotes = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $lotes[] = $row['lote'];
        }
    }
    return $lotes;
}

// Obter a lista de lotes
$lotes_disponiveis = obterLotes($conn);

// Obter lotes selecionados pelo usuário
$lotes_selecionados = isset($_POST['lotes']) ? $_POST['lotes'] : [];

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exibir Dados</title>
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
    <?php include 'menu.php'; ?>

    <h1>Exibir Dados</h1>
    <form method="POST">
        <label for="lotes">Selecione o lote:</label>
        <select name="lotes[]" id="lotes">
            <?php foreach ($lotes_disponiveis as $lote): ?>
                <option value="<?= $lote ?>" <?= in_array($lote, $lotes_selecionados) ? 'selected' : '' ?>><?= $lote ?></option>
            <?php endforeach; ?>
        </select>
        <input type="submit" value="Filtrar">
    </form>

    <?php
    if (!empty($lotes_selecionados)) {
        // Crie uma string de lotes selecionados para a consulta SQL
        $lotes_string = "'" . implode("','", $lotes_selecionados) . "'";

        // Consulta SQL para unir dados e lotes
        $sql = "SELECT d.id, d.nome_produto, d.data_prevista, d.lote, d.data_inicial, d.data_final,
                       l.lote, l.linha, l.ponto, 
                       SUM(l.somaCigarrinha) AS somaCigarrinha, 
                       SUM(l.somaMoscaBranca) AS somaMoscaBranca, 
                       SUM(l.somaPercevejo) AS somaPercevejo, 
                       SUM(l.somaPulgao) AS somaPulgao, 
                       SUM(l.somaAcaro) AS somaAcaro, 
                       SUM(l.somaTripes) AS somaTripes
                FROM dados d
                LEFT JOIN lotes l ON d.lote = l.lote
                WHERE l.data_cadastro BETWEEN d.data_inicial AND d.data_final
                AND d.lote IN ($lotes_string)
                GROUP BY d.id, d.nome_produto, d.data_prevista, d.lote, d.data_inicial, d.data_final, l.lote, l.linha, l.ponto
                ORDER BY d.id DESC";
        $result = $conn->query($sql);

        // Verifique se há resultados
        if ($result->num_rows > 0) {
            // Variável para armazenar o dados_t atual
            $dados_t_atual = null;
            
            // Inicialize uma matriz para armazenar os dados por dados_t
            $dados_por_dados_t = [];

            // Iterar sobre os resultados e agrupar os dados por dados_t e lote
            while ($row = $result->fetch_assoc()) {
                $dados_t = $row['id'] . " - " . $row['data_prevista'];
                $lote = $row['lote'];
                $linha = $row['linha'];
                $ponto = $row['ponto'];
                $obser = $row['nome_produto'];
                
                // Crie a estrutura do dados_t se não existir
                if (!isset($dados_por_dados_t[$dados_t])) {
                    $dados_por_dados_t[$dados_t] = [];
                }

                // Adicione os dados do lote ao dados_t
                $dados_por_dados_t[$dados_t][$lote][$linha][] = $row;
            }

            // Iterar sobre os dados por dados_t para exibir as tabelas
            foreach ($dados_por_dados_t as $dados_t => $lotes) {
                echo "<br>";
                echo "<table>";
                echo "<tr>";
                echo "<th class='th_crono'>$dados_t</th>";

                foreach ($lotes as $lote => $linhas) {
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

                    echo "<th class='th_crono'>Lote: " . $lote_nome . "</th>";
                    echo "</tr>";
                    echo "</table>";

                    echo "<div>";
                    echo '<table>';

                    // Totais por lote
                    $total_cig_lote = 0;
                    $total_mb_lote = 0;
                    $total_per_lote = 0;
                    $total_pul_lote = 0;
                    $total_ac_lote = 0;
                    $total_tri_lote = 0;

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
                        echo "<table>";
                        echo "<tr class='header'>";
                        echo '<th>Total</th>';
                        echo '<th>CG</th>';
                        echo '<th>MB</th>';
                        echo '<th>PC</th>';
                        echo '<th>PG</th>';
                        echo '<th>AC</th>';
                        echo '<th>TRP</th>';
                        echo "</tr>";

                        // Totais por linha
                        $total_cig = 0;
                        $total_mb = 0;
                        $total_per = 0;
                        $total_pul = 0;
                        $total_ac = 0;
                        $total_tri = 0;

                        foreach ($pontos as $dados) {
                            // Acumulando os totais por linha
                            $total_cig += $dados['somaCigarrinha'];
                            $total_mb += $dados['somaMoscaBranca'];
                            $total_per += $dados['somaPercevejo'];
                            $total_pul += $dados['somaPulgao'];
                            $total_ac += $dados['somaAcaro'];
                            $total_tri += $dados['somaTripes'];
                        }

                        echo "<tr class='total'>";
                        echo "<td>$linha_format</td>";
                        echo "<td id='tb_cig'>$total_cig</td>";
                        echo "<td id='tb_mb'>$total_mb</td>";
                        echo "<td id='tb_per'>$total_per</td>";
                        echo "<td id='tb_pul'>$total_pul</td>";
                        echo "<td id='tb_ac'>$total_ac</td>";
                        echo "<td id='tb_tri'>$total_tri</td>";
                        echo "</tr>";
                        echo "</table>";

                        // Acumulando os totais por lote
                        $total_cig_lote += $total_cig;
                        $total_mb_lote += $total_mb;
                        $total_per_lote += $total_per;
                        $total_pul_lote += $total_pul;
                        $total_ac_lote += $total_ac;
                        $total_tri_lote += $total_tri;
                    }

                    // Cálculo das médias por lote
                    $media_cig_lote = $total_cig_lote / 3;
                    $media_mb_lote = $total_mb_lote / 3;
                    $media_per_lote = $total_per_lote / 3;
                    $media_pul_lote = $total_pul_lote / 3;
                    $media_ac_lote = $total_ac_lote / 3;
                    $media_tri_lote = $total_tri_lote / 3;

                    echo "<table class='media'>";
                    echo "<tr class='header'>";
                    echo "<th>$lote_nome</th>";
                    echo "<th>CG</th>";
                    echo "<th>MB</th>";
                    echo "<th>PC</th>";
                    echo "<th>PG</th>";
                    echo "<th>AC</th>";
                    echo "<th>TRP</th>";
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td>Media</td>";
                    echo "<td>" . number_format($media_cig_lote, 4) . "</td>";
                    echo "<td>" . number_format($media_mb_lote, 4) . "</td>";
                    echo "<td>" . number_format($media_per_lote, 4) . "</td>";
                    echo "<td>" . number_format($media_pul_lote, 4) . "</td>";
                    echo "<td>" . number_format($media_ac_lote, 4) . "</td>";
                    echo "<td>" . number_format($media_tri_lote, 4) . "</td>";
                    echo "</tr>";
                    echo "</table>";

                    //Campo Observacao
                    echo '<table>';
                    echo '<tr>';
                    echo '<th class="th_obser">Observações</th>';
                    echo "</tr>";
                    echo "<tr>";
                    echo "<td>$obser</td>";
                    echo '</tr>';
                    echo '</table>';

                    // Fecha os ciclos dos pontos dentro das linha
                    echo '</table>';
                    echo "<th>";
                    echo "</table>";
                    echo "<hr>";
                }
            }
        } else {
            echo "<p>Não há nenhum registro de dados relacionado a este lote.</p>";
        }
    } else {
        
    }

    // Fechar a conexão
    $conn->close();
    ?>
</body>
</html>
