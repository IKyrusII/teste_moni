// Função para salvar os dados no localStorage
function salvarAnalise() {
    // Objeto para armazenar os dados
    var dadosAnalise = {};

    // Função para pegar valor de input do tipo radio
    function getRadioValue(name) {
        var radios = document.getElementsByName(name);
        for (var i = 0; i < radios.length; i++) {
            if (radios[i].checked) {
                return radios[i].value;
            }
        }
        return null;
    }

    // Função para pegar valor de inputs de texto
    function getInputValuesById(id) {
        var inputs = document.querySelectorAll(`#${id} input[type="text"]`);
        var values = [];
        inputs.forEach(input => values.push(parseFloat(input.value) || 0));
        return values;
    }

    // Função para calcular a soma dos valores de um array
    function calcularSoma(values) {
        return values.reduce((total, num) => total + num, 0);
    }

    // Armazenar valores dos lotes
    dadosAnalise.lote = getRadioValue('lote');

    // Armazenar valores das linhas
    dadosAnalise.linha = getRadioValue('linha');

    // Armazenar valores das ponto
    dadosAnalise.ponto = getRadioValue('ponto');

    // Armazenar valores das pragas com soma
    dadosAnalise.cigarrinha = getInputValuesById('contagem_cigarrinha');
    dadosAnalise.somaCigarrinha = calcularSoma(dadosAnalise.cigarrinha);

    dadosAnalise.moscaBranca = getInputValuesById('contagem_mosca_branca');
    dadosAnalise.somaMoscaBranca = calcularSoma(dadosAnalise.moscaBranca);

    dadosAnalise.percevejo = getInputValuesById('contagem_percevejo');
    dadosAnalise.somaPercevejo = calcularSoma(dadosAnalise.percevejo);

    dadosAnalise.pulgao = getInputValuesById('contagem_pulgao');
    dadosAnalise.somaPulgao = calcularSoma(dadosAnalise.pulgao);

    dadosAnalise.acaro = getInputValuesById('contagem_acaro');
    dadosAnalise.somaAcaro = calcularSoma(dadosAnalise.acaro);

    dadosAnalise.tripes = getInputValuesById('contagem_tripes');
    dadosAnalise.somaTripes = calcularSoma(dadosAnalise.tripes);

    // Obter o array atual do localStorage, ou criar um novo se não existir
    var analisesExistentes = JSON.parse(localStorage.getItem('dadosAnalise')) || [];

    // Adicionar a nova análise ao array
    analisesExistentes.push(dadosAnalise);

    // Converter o array atualizado para JSON
    var dadosJson = JSON.stringify(analisesExistentes);

    // Armazenar os dados no localStorage
    localStorage.setItem('dadosAnalise', dadosJson);

    // Exibir uma mensagem de confirmação
    document.getElementById('mensagem').textContent = 'Dados salvos com sucesso!';
}

// Função para enviar os dados do localStorage para o banco de dados
function enviarDadosParaBancoDeDados() {
    var analisesExistentes = JSON.parse(localStorage.getItem('dadosAnalise')) || [];

    if (analisesExistentes.length === 0) {
        document.getElementById('mensagem').textContent = 'Não há dados para enviar!';
        return;
    }

    // URL da página PHP onde os dados serão enviados
    var url = 'conexao.php'; // substitua pelo URL da sua página PHP

    // Preparar os dados para envio
    var formData = new FormData();
    formData.append('dadosAnalise', JSON.stringify(analisesExistentes));

    // Enviar os dados para a página PHP
    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => {
        console.log('Sucesso:', data);
        document.getElementById('mensagem').textContent = 'Dados enviados com sucesso!';

        // Limpar o localStorage após o envio bem-sucedido
        localStorage.removeItem('dadosAnalise');
    })
    .catch((error) => {
        console.error('Erro:', error);
        document.getElementById('mensagem').textContent = 'Erro ao enviar os dados!';
    });
}

/* Função para adicionar comportamento de accordion*/function menuAccordion() {var accordions = document.getElementsByClassName("accordion");for (var i = 0; i < accordions.length; i++) {accordions[i].addEventListener("click", function() {this.classList.toggle("active");var painel = this.nextElementSibling;if (painel.style.display === "block") {painel.style.display = "none";} else {painel.style.display = "block";}});}}/* Chamar a função de inicialização dos accordions*/document.addEventListener("DOMContentLoaded", menuAccordion);function media_inseto_lote_a() {var media_l_a = Float;var media_l_b = Float;var media_l_c = Float;var media_l_d = Float;var media_l_e = Float;var media_l_g = Float;var media_l_h = Float;var media_l_j = Float;var media_l_pern = Float;var ponto_3 = int;var ponto_10 = int;var ponto_30 = int;var ponto_46 = int;var ponto_49 = int;}


function media_praga($media) {
    
    $soma_cig = $soma_cig + $dados['somaCigarrinha'];

}