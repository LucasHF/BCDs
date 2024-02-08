<?php

//Função para verificar se a linha é uma metropolitana que precisa de substituição de cartão
function linhaMetropolitana(string $line){
    $linhasMetro = array("    43M", "    44M", "    67M", "   570M", "   122M", " 10211M", " 10325M", " 20211M", " 20325M", "10199M", "30199M"); //linhas que precisam de alteração
    $linha = substr($line, 1, 7); // retorna a linha aberta na viagem
    
    return in_array($linha, $linhasMetro); //retorna true caso a linha da viagem seja uma das que precisam de alteração do cartão
}

//Função para verificar se foi paga uma tarifa inteira
function cartaoInteira(string $line){
    $cartoesInteira = array ("34", "52", "18", "19", "17", "37", "38", "24"); // Array de codigos de cartoes de passagem inteira
    $cartao = substr($line, 65, 2); //obtem o tipo de cartao utilizado para pagar a passagem
    
    return in_array($cartao, $cartoesInteira); // retorna true se for um cartao de passagem inteira
}

//Função para verificar se foi paga uma meia papssagem
function cartaoMeia(string $line){
    $cartoesMeia = array ("23", "39"); // Array de codigos de cartoes de meia passagem com credito
    $cartao = substr($line, 65, 2); //obtem o tipo de cartao utilizado para pagar a passagem
    
    return in_array($cartao, $cartoesMeia); // retorna true se for um cartao de meia passagem com credito
}

//Funçao para modificar o código do cartão caso seja um valor antigo
function modificaCartao(string $line){
	
    //Array com os valor de passagens inteiras antigas
	$valoresInteira = array("01,90", "04,30", "05,30", "06,30", "07,30", "08,00", "09,60", "11,15", "15,35", "01,60", "03,70", "04,70", "05,70", "06,70", "07,40", "10,55", "14,75", "01,00", "03,20", "06,85", "11,05", "00,50");
    
    //Array com os valor de meias passagens antigas
	$valoresMeia = array("00,95", "02,15", "02,65", "03,65", "04,00", "04,80", "05,60", "07,70", "01,65", "02,10", "03,00", "03,45", "04,00", "04,70", "06,60", "00,50", "01,85", "02,65", "03,45", "05,55");
    
	$valor = substr($line, 71, 5); //retorna o valor da passagem
    
    if(linhaMetropolitana($line)){
        if(in_array($valor, $valoresInteira) && cartaoInteira($line)){ //verifica se o tarifa descontada foi reamente uma inteira antiga

           $line = substr_replace($line,"54",65, 2); //Modifica codigo para auxiliar de Inteiras antigas

        }elseif(in_array($valor, $valoresMeia) && cartaoMeia($line)) { //verifica se o tarifa descontada foi reamente uma inteira antiga

            $line = substr_replace($line,"55",65, 2); //Modifica codigo para auxiliar de Meias antigas

        }
    }
    return $line;
}



//Processo pra abrir o arquivo e fazer as modificações
foreach(glob('*.txt') as $arquivo){
    echo $dir;
    echo "<br>";
    $arq = fopen($arquivo, "r+");
    if ($arq) { 

        $string = fgets($arq); ///Recebe a primeira linha do arquivo, que contem a data do movimento das viagens

        while(true) { 
            $linha = fgets($arq);

            if ($linha[0]=="C") break; //Caso a letra inicial seja C, chegou no fim do arquivo

            $string.= modificaCartao($linha);
        }

        $string.= fgets($arq); //pega a última linha do arquivo, que contem a letra C
        
        // move o ponteiro para o inicio pois o ftruncate() nao fara isso 
        rewind($arq); 
        // truca o arquivo apagando tudo dentro dele 
        ftruncate($arq, 0); 
        // reescreve o conteudo dentro do arquivo 
        if (!fwrite($arq, $string)) die('Não foi possível atualizar o arquivo.'); 
        echo 'Arquivo atualizado com sucesso'; 
        fclose($arq);
    } 
}
?>