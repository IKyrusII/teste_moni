<?php
// Define as credenciais do banco de dados
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "monitoramento_praga";

// Cria a conexão com o banco de dados
$conn = new mysqli($servername, $username, $password, $dbname);

// Verifica a conexão
if ($conn->connect_error) {
    die("Falha na conexão: " . $conn->connect_error);
}

// Verifica se os dados foram enviados via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['dadosAnalise'])) {
    // Obtenha os dados enviados
    $dadosAnalise = json_decode($_POST['dadosAnalise'], true);

    // Verifica se a decodificação foi bem-sucedida
    if (is_array($dadosAnalise)) {
        // Percorra cada análise e insira no banco de dados
        foreach ($dadosAnalise as $analise) {
            $lote = isset($analise['lote']) ? $analise['lote'] : '';
            $linha = isset($analise['linha']) ? $analise['linha'] : '';
            $ponto = isset($analise['ponto']) ? $analise['ponto'] : '';

            $cigarrinha = isset($analise['cigarrinha']) ? implode(',', $analise['cigarrinha']) : '';
            $moscaBranca = isset($analise['moscaBranca']) ? implode(',', $analise['moscaBranca']) : '';
            $percevejo = isset($analise['percevejo']) ? implode(',', $analise['percevejo']) : '';
            $pulgao = isset($analise['pulgao']) ? implode(',', $analise['pulgao']) : '';
            $acaro = isset($analise['acaro']) ? implode(',', $analise['acaro']) : '';
            $tripes = isset($analise['tripes']) ? implode(',', $analise['tripes']) : '';

            $somaCigarrinha = isset($analise['somaCigarrinha']) ? $analise['somaCigarrinha'] : 0;
            $somaMoscaBranca = isset($analise['somaMoscaBranca']) ? $analise['somaMoscaBranca'] : 0;
            $somaPercevejo = isset($analise['somaPercevejo']) ? $analise['somaPercevejo'] : 0;
            $somaPulgao = isset($analise['somaPulgao']) ? $analise['somaPulgao'] : 0;
            $somaAcaro = isset($analise['somaAcaro']) ? $analise['somaAcaro'] : 0;
            $somaTripes = isset($analise['somaTripes']) ? $analise['somaTripes'] : 0;

            $observacao = isset($analise['observacao']) ? $analise['observacao'] : '';
        }


    
         // SQL para inserir os dados
         $sql = "INSERT INTO lotes (lote, linha, ponto, cigarrinha, moscaBranca, percevejo, pulgao, acaro, tripes, somaCigarrinha, somaMoscaBranca, somaPercevejo, somaPulgao, somaAcaro, somaTripes, observacao)
         VALUES ('$lote', '$linha', '$ponto', '$cigarrinha', '$moscaBranca', '$percevejo', '$pulgao', '$acaro', '$tripes', '$somaCigarrinha', '$somaMoscaBranca', '$somaPercevejo', '$somaPulgao', '$somaAcaro', '$somaTripes', '$observacao')";

         if ($conn->query($sql) === TRUE) {
             echo "Nova análise inserida com sucesso!";
         } else {
             echo "Erro: " . $sql . "<br>" . $conn->error;
         }

    } else {
        echo "Erro ao decodificar os dados JSON.";
    }
} else {
    echo "";
}

// Feche a conexão
$conn->close();
?>