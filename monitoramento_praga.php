<?php
    include('conexao.php');

    session_start();

    // Verifique se o usuário está logado
    if (!isset($_SESSION['user'])) {
        $_SESSION['redirect_to'] = $_SERVER['REQUEST_URI'];
        header("Location: login.php");
        exit();
    }
    
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="comandos.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="script.js" defer></script>
    <title>Monitoramento de Praga</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .navbar {
            display: flex;
            background-color: #333;
            overflow: hidden;
        }
        .navbar a {
            float: left;
            display: block;
            color: #f2f2f2;
            text-align: center;
            padding: 14px 20px;
            text-decoration: none;
        }
        .navbar a:hover {
            background-color: #ddd;
            color: black;
        }
        .botao_sair {
            color: #d92525 !important;
            margin-left: auto;
            margin-right: 2%;
        }
    </style>
    
</head>
<body>
    <div class="container">
        <a href="logout.php" class="botao_sair">Logout</a>
        <div id="menu">
            <h2><a href="#">Selecionar Lotes</a></h2>
            <div id="labelconfig">
                <br>
                <label>
                    <input type="radio" name="lote" value="lote_a" required>Lote A
                </label>
                <label>
                    <input type="radio" name="lote" value="lote_b">Lote B
                </label>
                <label>
                    <input type="radio" name="lote" value="lote_c">Lote C
                </label>
                <label>
                    <input type="radio" name="lote" value="lote_d">Lote D
                </label>
                <label>
                    <input type="radio" name="lote" value="lote_e">Lote E
                </label>
                 <br><br>
                <label>
                    <input type="radio" name="lote" value="lote_g">Lote G
                </label>
                <label>
                    <input type="radio" name="lote" value="lote_h">Lote H
                </label>
                <label>
                    <input type="radio" name="lote" value="lote_j">Lote J
                </label>
                <label>
                    <input type="radio" name="lote" value="lote_pern">Lote PERN
                </label>
            </div>
            
        </div>

        <div id="menu">
            <h2><a href="#">Selecionar Linha</a></h2>
            <div id="labelconfig">
                <br>
                <label>
                    <input type="radio" name="linha"s value="linha_3" required>Linha 3
                </label>
                <label>
                    <input type="radio" name="linha"s value="linha_10">Linha 10
                </label>
                <label>
                    <input type="radio" name="linha"s value="linha_30">Linha 30
                </label>
                <label>
                    <input type="radio" name="linha"s value="linha_46">Linha 46
                </label>
                <label>
                    <input type="radio" name="linha"s value="linha_50">Linha 50
                </label>
            </div>
        
        </div>

        <div id="menu">
            <h2><a href="#">Selecionar Ponto</a></h2>
            <div id="labelconfig">
                <br>
                <label>
                    <input type="radio" name="ponto" value="ponto_1" required>1° - 4 a 6
                </label>
                <label>
                    <input type="radio" name="ponto" value="ponto_2">2° - 13 a 15
                </label>
                <label>
                    <input type="radio" name="ponto" value="ponto_3">3° - 22 a 24
                </label>
                
                <label>
                    <input type="radio" name="ponto" value="ponto_4">4° - 31 a 33
                </label>
                <br><br>
                <label>
                    <input type="radio" name="ponto" value="ponto_5">5 °- 40 a 42
                </label>
                <label>
                    <input type="radio" name="ponto" value="ponto_6">6° - 49 a 51
                </label>
            </div>
            
        </div>

        <div id="menu">
            <button class="accordion"><h2 id="cig"> Cigarrinha</h2></button>  
            <div id="contagem_cigarrinha" class="painel">
                <table>
                    <tr>
                        <td >1°</td>
                        <td >2°</td>
                        <td >3°</td>
                        <td >4°</td>
                        <td >5°</td>
                        <td >6°</td>
                        <td >7°</td>
                        <td >8°</td>
                        <td >9°</td>
                        <td>10°</td>
                    </tr>
                    
                    <tr>
                        <td><input type="text" class="inputconf" id="ciga1" value="0"></td>
                        <td><input type="text" class="inputconf" id="ciga2" value="0"></td>
                        <td><input type="text" class="inputconf" id="ciga3" value="0"></td>
                        <td><input type="text" class="inputconf" id="ciga4" value="0"></td>
                        <td><input type="text" class="inputconf" id="ciga5" value="0"></td>
                        <td><input type="text" class="inputconf" id="ciga6" value="0"></td>
                        <td><input type="text" class="inputconf" id="ciga7" value="0"></td>
                        <td><input type="text" class="inputconf" id="ciga8" value="0"></td>
                        <td><input type="text" class="inputconf" id="ciga9" value="0"></td>
                        <td><input type="text" class="inputconf" id="ciga10" value="0"></td>
                    </tr>
                </table>
            </div> 
        </div> 
        
        <div id="menu">
            <button class="accordion"><h2 id="mb"> Mosca Branca</h2></button> 
            <div id="contagem_mosca_branca" class="painel"> 
                <table>
                    <tr>
                        <td >1°</td>
                        <td >2°</td>
                        <td >3°</td>
                        <td >4°</td>
                        <td >5°</td>
                        <td >6°</td>
                        <td >7°</td>
                        <td >8°</td>
                        <td >9°</td>
                        <td>10°</td>
                    </tr>
                    
                    <tr>
                        <td><input type="text" class="inputconf" id="mosca1" value="0"></td>
                        <td><input type="text" class="inputconf" id="mosca2" value="0"></td>
                        <td><input type="text" class="inputconf" id="mosca3" value="0"></td>
                        <td><input type="text" class="inputconf" id="mosca4" value="0"></td>
                        <td><input type="text" class="inputconf" id="mosca5" value="0"></td>
                        <td><input type="text" class="inputconf" id="mosca6" value="0"></td>
                        <td><input type="text" class="inputconf" id="mosca7" value="0"></td>
                        <td><input type="text" class="inputconf" id="mosca8" value="0"></td>
                        <td><input type="text" class="inputconf" id="mosca9" value="0"></td>
                        <td><input type="text" class="inputconf" id="mosca10" value="0"></td>
                    </tr>
                </table>
            </div>
        </div>
        <div id="menu">
            <button class="accordion"><h2 id="per"> Percevejo</h2></button> 
            <div id="contagem_percevejo" class="painel">
                <table>
                    <tr>
                        <td >1°</td>
                        <td >2°</td>
                        <td >3°</td>
                        <td >4°</td>
                        <td >5°</td>
                        <td >6°</td>
                        <td >7°</td>
                        <td >8°</td>
                        <td >9°</td>
                        <td>10°</td>
                    </tr>
                    
                    <tr>
                        <td><input type="text" class="inputconf" id="percevejo1" value="0"></td>
                        <td><input type="text" class="inputconf" id="percevejo2" value="0"></td>
                        <td><input type="text" class="inputconf" id="percevejo3" value="0"></td>
                        <td><input type="text" class="inputconf" id="percevejo4" value="0"></td>
                        <td><input type="text" class="inputconf" id="percevejo5" value="0"></td>
                        <td><input type="text" class="inputconf" id="percevejo6" value="0"></td>
                        <td><input type="text" class="inputconf" id="percevejo7" value="0"></td>
                        <td><input type="text" class="inputconf" id="percevejo8" value="0"></td>
                        <td><input type="text" class="inputconf" id="percevejo9" value="0"></td>
                        <td><input type="text" class="inputconf" id="percevejo10" value="0"></td>
                    </tr>
                </table>
            </div>
        </div>   
        <div id="menu">      
            <button class="accordion"><h2 id="pul"> Pulgão</h2></button> 
            <div id="contagem_pulgao" class="painel">
                <table>
                    <tr>
                        <td >1°</td>
                        <td >2°</td>
                        <td >3°</td>
                        <td >4°</td>
                        <td >5°</td>
                        <td >6°</td>
                        <td >7°</td>
                        <td >8°</td>
                        <td >9°</td>
                        <td>10°</td>
                    </tr>
                    
                    <tr>
                        <td><input type="text" class="inputconf" id="pulgao1" value="0" ></td>
                        <td><input type="text" class="inputconf" id="pulgao2" value="0"></td>
                        <td><input type="text" class="inputconf" id="pulgao3" value="0"></td>
                        <td><input type="text" class="inputconf" id="pulgao4" value="0"></td>
                        <td><input type="text" class="inputconf" id="pulgao5" value="0"></td>
                        <td><input type="text" class="inputconf" id="pulgao6" value="0"></td>
                        <td><input type="text" class="inputconf" id="pulgao7" value="0"></td>
                        <td><input type="text" class="inputconf" id="pulgao8" value="0"></td>
                        <td><input type="text" class="inputconf" id="pulgao9" value="0"></td>
                        <td><input type="text" class="inputconf" id="pulgao10" value="0"></td>
                    </tr>
                </table>
            </div>
        </div>   
        <div id="menu">
            <button class="accordion"><h2 id="ac"> Ácaro</h2></button> 
            <div id="contagem_acaro" class="painel">
                <table>
                    <tr>
                        <td >1°</td>
                        <td >2°</td>
                        <td >3°</td>
                        <td >4°</td>
                        <td >5°</td>
                        <td >6°</td>
                        <td >7°</td>
                        <td >8°</td>
                        <td >9°</td>
                        <td>10°</td>
                    </tr>
                    
                    <tr>
                        <td><input type="text" class="inputconf" id="acaro1" value="0"></td>
                        <td><input type="text" class="inputconf" id="acaro2" value="0"></td>
                        <td><input type="text" class="inputconf" id="acaro3" value="0"></td>
                        <td><input type="text" class="inputconf" id="acaro4" value="0"></td>
                        <td><input type="text" class="inputconf" id="acaro5" value="0"></td>
                        <td><input type="text" class="inputconf" id="acaro6" value="0"></td>
                        <td><input type="text" class="inputconf" id="acaro7" value="0"></td>
                        <td><input type="text" class="inputconf" id="acaro8" value="0"></td>
                        <td><input type="text" class="inputconf" id="acaro9" value="0"></td>
                        <td><input type="text" class="inputconf" id="acaro10" value="0"></td>
                    </tr>
                </table>
            </div>  
        </div>
        <div id="menu">
            <button class="accordion"><h2 id="ac"> Tripes</h2></button> 
            <div id="contagem_tripes" class="painel">
                <table>
                    <tr>
                        <td >1°</td>
                        <td >2°</td>
                        <td >3°</td>
                        <td >4°</td>
                        <td >5°</td>
                        <td >6°</td>
                        <td >7°</td>
                        <td >8°</td>
                        <td >9°</td>
                        <td>10°</td>
                    </tr>
                    
                    <tr>
                        <td><input type="text" class="inputconf" id="tripes1" value="0"></td>
                        <td><input type="text" class="inputconf" id="tripes2" value="0"></td>
                        <td><input type="text" class="inputconf" id="tripes3" value="0"></td>
                        <td><input type="text" class="inputconf" id="tripes4" value="0"></td>
                        <td><input type="text" class="inputconf" id="tripes5" value="0"></td>
                        <td><input type="text" class="inputconf" id="tripes6" value="0"></td>
                        <td><input type="text" class="inputconf" id="tripes7" value="0"></td>
                        <td><input type="text" class="inputconf" id="tripes8" value="0"></td>
                        <td><input type="text" class="inputconf" id="tripes9" value="0"></td>
                        <td><input type="text" class="inputconf" id="tripes10" value="0"></td>
                    </tr>
                </table>
            </div>  
        </div> 

        <div id="menu">
            <form id="observacaoForm">
                <input type="text" name="observacao" id="observacao" placeholder="Insira uma observação">
            </form>
        </div>
        
        <div id="menu">
            <div id="enviar">
                <button onclick="salvarAnalise()" id="btenviar">Salvar</button>

                <button onclick="enviarDadosParaBancoDeDados()" type="submit" id="btenviar">Enviar</button>
            </div>
                <div>
                    <p id="mensagem"></p>
                </div>
        </div>
    </div>
</body>
</html>