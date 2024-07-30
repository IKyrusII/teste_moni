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
    <!-- Adicione o Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Estilos CSS -->
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

        .th_crono {
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
                       l.somaCigarrinha, 
                       l.somaMoscaBranca, 
                       l.somaPercevejo, 
                       l.somaPulgao, 
                       l.somaAcaro, 
                       l.somaTripes
                FROM dados d
                LEFT JOIN lotes l ON d.lote = l.lote
                WHERE l.data_cadastro BETWEEN d.data_inicial AND d.data_final
                AND d.lote IN ($lotes_string)
                ORDER BY d.id DESC";
        $result = $conn->query($sql);

        // Verifique se há resultados
        if ($result->num_rows > 0) {
            // Arrays para armazenar os dados dos gráficos
            $graficos = [];

            while ($row = $result->fetch_assoc()) {
                $id = $row['id'];
                if (!isset($graficos[$id])) {
                    $graficos[$id] = [
                        'linhas' => [],
                        'pontos' => [],
                        'lote' => $row['lote'],
                        'data_prevista' => $row['data_prevista'],
                        'somaCigarrinha' => [],
                        'somaMoscaBranca' => [],
                        'somaPercevejo' => [],
                        'somaPulgao' => [],
                        'somaAcaro' => [],
                        'somaTripes' => [],
                    ];
                }
                $graficos[$id]['linhas'][] = $row['linha'];
                $ponto = $row['ponto'];
                $graficos[$id]['pontos'][] = $row['linha'] . " - " . $row['ponto']; // Combine linha e ponto
                $graficos[$id]['somaCigarrinha'][] = $row['somaCigarrinha'];
                $graficos[$id]['somaMoscaBranca'][] = $row['somaMoscaBranca'];
                $graficos[$id]['somaPercevejo'][] = $row['somaPercevejo'];
                $graficos[$id]['somaPulgao'][] = $row['somaPulgao'];
                $graficos[$id]['somaAcaro'][] = $row['somaAcaro'];
                $graficos[$id]['somaTripes'][] = $row['somaTripes'];
            }

            // Calcular somas por linha para cada ID
            $somas_por_linha = [];
            foreach ($graficos as $id => $dados) {

                foreach ($dados['linhas'] as $index => $linha) {

                    if (!isset($somas_por_linha[$id][$linha])) {
                        $somas_por_linha[$id][$linha] = [
                            'somaCigarrinha' => 0,
                            'somaMoscaBranca' => 0,
                            'somaPercevejo' => 0,
                            'somaPulgao' => 0,
                            'somaAcaro' => 0,
                            'somaTripes' => 0,
                        ];
                    }
                    $somas_por_linha[$id][$linha]['somaCigarrinha'] += $dados['somaCigarrinha'][$index];
                    $somas_por_linha[$id][$linha]['somaMoscaBranca'] += $dados['somaMoscaBranca'][$index];
                    $somas_por_linha[$id][$linha]['somaPercevejo'] += $dados['somaPercevejo'][$index];
                    $somas_por_linha[$id][$linha]['somaPulgao'] += $dados['somaPulgao'][$index];
                    $somas_por_linha[$id][$linha]['somaAcaro'] += $dados['somaAcaro'][$index];
                    $somas_por_linha[$id][$linha]['somaTripes'] += $dados['somaTripes'][$index];
                }
                $media_cigarrinha = $somas_por_linha[$id][$linha]['somaCigarrinha']/3;
                $media_moscaBranca = $somas_por_linha[$id][$linha]['somaMoscaBranca']/3;
                $media_percevejo = $somas_por_linha[$id][$linha]['somaPercevejo']/3;
                $media_pulgao = $somas_por_linha[$id][$linha]['somaPulgao']/3;
                $media_acaro = $somas_por_linha[$id][$linha]['somaAcaro']/3;
                $media_tripes = $somas_por_linha[$id][$linha]['somaTripes']/3;



            }
            
        }
    }
    ?>

    <?php if (!empty($graficos)): ?>
        <?php foreach ($graficos as $id => $dados): ?>
            
            <table>
                <tr>
                    <th>ID: <?= $id ?> </th>
                    <th><?= $dados['data_prevista'] ?></th>
                </tr>
            </table>

            <table>
                <thead>
                    <tr class="header">
                        <th>Linha</th>
                        <th>Total Cigarrinha</th>
                        <th>Total Mosca Branca</th>
                        <th>Total Percevejo</th>
                        <th>Total Pulgão</th>
                        <th>Total Ácaro</th>
                        <th>Total Tripes</th>
                    </tr>
                </thead>
                
                <tbody>
                    <?php foreach ($somas_por_linha[$id] as $linha => $somas): ?>
                        <tr>
                            <td><?= $linha ?></td>
                            <td><?= $somas['somaCigarrinha'] ?></td>
                            <td><?= $somas['somaMoscaBranca'] ?></td>
                            <td><?= $somas['somaPercevejo'] ?></td>
                            <td><?= $somas['somaPulgao'] ?></td>
                            <td><?= $somas['somaAcaro'] ?></td>
                            <td><?= $somas['somaTripes'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                        <tr>
                            <th>Media</th>
                            <th>Cigarrinha</th>
                            <th>Mosca Branca</th>
                            <th>Percevejo</th>
                            <th>Pulgão</th>
                            <th>Ácaro</th>
                            <th>Tripes</th>
                        </tr>
                        <tr>
                            <td>/3</td>
                            <td><?= number_format($media_cigarrinha, 4)?></td>
                            <td><?= number_format($media_moscaBranca, 4)?></td>
                            <td><?= number_format($media_percevejo, 4)?></td>
                            <td><?= number_format($media_pulgao, 4)?></td>
                            <td><?= number_format($media_acaro, 4)?></td>
                            <td><?= number_format($media_tripes, 4)?>
                        </tr>
                </table>
   
                <canvas id="grafico-<?= $id ?>" width="400" height="200"></canvas>
           
            <script>
                var ctx = document.getElementById('grafico-<?= $id ?>').getContext('2d');
                var myChart = new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: <?= json_encode($dados['pontos']) ?>,
                        datasets: [
                            {
                                label: 'Cigarrinha',
                                data: <?= json_encode($dados['somaCigarrinha']) ?>,
                                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                                borderColor: 'rgba(75, 192, 192, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Mosca Branca',
                                data: <?= json_encode($dados['somaMoscaBranca']) ?>,
                                backgroundColor: 'rgba(54, 162, 235, 0.2)',
                                borderColor: 'rgba(54, 162, 235, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Percevejo',
                                data: <?= json_encode($dados['somaPercevejo']) ?>,
                                backgroundColor: 'rgba(255, 206, 86, 0.2)',
                                borderColor: 'rgba(255, 206, 86, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Pulgão',
                                data: <?= json_encode($dados['somaPulgao']) ?>,
                                backgroundColor: 'rgba(153, 102, 255, 0.2)',
                                borderColor: 'rgba(153, 102, 255, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Ácaro',
                                data: <?= json_encode($dados['somaAcaro']) ?>,
                                backgroundColor: 'rgba(255, 159, 64, 0.2)',
                                borderColor: 'rgba(255, 159, 64, 1)',
                                borderWidth: 1
                            },
                            {
                                label: 'Tripes',
                                data: <?= json_encode($dados['somaTripes']) ?>,
                                backgroundColor: 'rgba(255, 99, 132, 0.2)',
                                borderColor: 'rgba(255, 99, 132, 1)',
                                borderWidth: 1
                            }
                        ]
                    },
                    options: {
                        scales: {
                            y: {
                                beginAtZero: true
                            }
                        }
                    }
                });
            </script>
            <hr>
        <?php endforeach; ?>
    <?php endif; ?>

    <?php $conn->close(); ?>
</body>
</html>