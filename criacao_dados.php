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

// Verifique se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    // Capture os valores dos campos do formulário e trate para evitar injeção de SQL
    $nome_produto = $_POST['nome_produto'];
    $data_prevista = $_POST['data_prevista'];
    $lote = $_POST['lote'];
    $data_inicial = $_POST['data_inicial'];
    $data_final = $_POST['data_final'];

    // Formate as datas para o formato aceito pelo MySQL
    $data_inicial = date('Y-m-d', strtotime($data_inicial));
    $data_final = date('Y-m-d', strtotime($data_final));

    // Corrija a consulta SQL para usar parâmetros corretamente
    $sql = "SELECT * FROM lotes WHERE lote='$lote' AND data_cadastro >= '$data_inicial' AND data_cadastro <= '$data_final'";

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

        // Iterar sobre os dados por dados_t para calcular as médias e armazenar no banco de dados
        foreach ($dados_por_dados_t as $dados_t => $lotes) {
            foreach ($lotes as $lote => $linhas) {
                // Totais por lote
                $total_cig_lote = 0;
                $total_mb_lote = 0;
                $total_per_lote = 0;
                $total_pul_lote = 0;
                $total_ac_lote = 0;
                $total_tri_lote = 0;

                foreach ($linhas as $linha => $pontos) {
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

                    // Acumulando os totais por lote
                    $total_cig_lote += $total_cig;
                    $total_mb_lote += $total_mb;
                    $total_per_lote += $total_per;
                    $total_pul_lote += $total_pul;
                    $total_ac_lote += $total_ac;
                    $total_tri_lote += $total_tri;
                }

                // Cálculo das médias por lote
                $media_cig_total = $total_cig_lote / 3;
                $media_mb_total = $total_mb_lote / 3;
                $media_per_total = $total_per_lote / 3;
                $media_pul_total = $total_pul_lote / 3;
                $media_ac_total = $total_ac_lote / 3;
                $media_tri_total = $total_tri_lote / 3;

                $mediaCigarrinha = number_format($media_cig_total, 4);
                $mediaMoscaBranca = number_format($media_mb_total, 4);
                $mediaPercevejo = number_format($media_per_total, 4);
                $mediaPulgao = number_format($media_pul_total, 4);
                $mediaAcaro = number_format($media_ac_total, 4);
                $mediaTripes = number_format($media_tri_total, 4);

                // Inserir as médias no banco de dados
                $sql_insert_media = "INSERT INTO dados (nome_produto, data_prevista, lote, data_inicial, data_final, mediaCigarrinha, mediaMoscaBranca, mediaPercevejo, mediaPulgao, mediaAcaro, mediaTripes)
                                    VALUES ('$nome_produto', '$data_prevista', '$lote', '$data_inicial', '$data_final', $mediaCigarrinha, $mediaMoscaBranca, $mediaPercevejo, $mediaPulgao, $mediaAcaro, $mediaTripes)";

                if ($conn->query($sql_insert_media) === TRUE) {
                    header("Location: exibir_dados.php");
                    exit();
                } else {
                    echo "Erro ao inserir médias: " . $conn->error;
                }
            }
        }
    } else {
        echo "Nenhum dado encontrado para os critérios fornecidos.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criação de Dados</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            width: 50%;
            margin: 0 auto;
            background: #fff;
            padding: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            margin-top: 50px;
            border-radius: 10px;
        }
        h1 {
            text-align: center;
            color: #333;
        }
        form {
            display: flex;
            flex-direction: column;
        }
        label {
            margin-top: 10px;
            margin-bottom: 5px;
            color: #555;
        }
        input, select {
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
        }
        input[type="submit"], .button-link {
            background-color: #5cb85c;
            color: white;
            border: none;
            cursor: pointer;
            text-align: center;
            padding: 10px 20px;
            text-decoration: none;
            border-radius: 5px;
        }
        input[type="submit"]:hover, .button-link:hover {
            background-color: #4cae4c;
        }
        .button-link {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <?php include 'menu.php'; ?>
    <div class="container">
        

        <h1>Criação de Dados</h1>
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">

            <label for="data_prevista">Data de Criação:</label>
            <input type="date" id="data_prevista" name="data_prevista" required>

            <label for="lote">Lote:</label>
            <select id="lote" name="lote" required>
                <option value="lote_a">Lote A</option>
                <option value="lote_b">Lote B</option>
                <option value="lote_c">Lote C</option>
                <option value="lote_d">Lote D</option>
                <option value="lote_e">Lote E</option>
                <option value="lote_g">Lote G</option>
                <option value="lote_h">Lote H</option>
                <option value="lote_j">Lote J</option>
                <option value="lote_pern">Lote Pern</option>
            </select>

            <label for="data_inicial">Data Inicial:</label>
            <input type="date" id="data_inicial" name="data_inicial" required>

            <label for="data_final">Data Final:</label>
            <input type="date" id="data_final" name="data_final" required>

            <label for="nome_produto">Adicionar observação:</label>
            <input type="text" id="nome_produto" name="nome_produto" required>

            <input type="submit" value="Criar Tabela de Dados">
        </form>
        
    </div>
</body>
</html>
