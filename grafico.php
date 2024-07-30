<?php
// Importar o módulo
require("phplot-6.1.0/phplot.php");

// Instanciar o gráfico com tamanho pré-definido
$grafico = new PHPlot(800, 600);

// Definindo o formato final da imagem
$grafico->SetFileFormat("png");

// Definindo o título do gráfico
$grafico->SetTitle("Gráfico Exemplo\nseucurso.com.br");

// Tipo do gráfico (pode ser: lines, bars, pie, etc.)
$grafico->SetPlotType("lines");

// Título dos dados no eixo Y
$grafico->SetYTitle("Vezes");

// Título dos dados no eixo X
$grafico->SetXTitle("Dias");

// Dados do gráfico
$dados = array(
    array('Dom', 12),
    array('Seg', 20),
    array('Ter', 7),
    array('Qua', 2),
    array('Qui', 6),
    array('Sex', 4),
    array('Sáb', 1)
);

$grafico->SetDataValues($dados);

// Exibimos o gráfico
$grafico->DrawGraph();
?>
