<?php
function trechoCpfCnpj($cpf_cnpj)
{
    $complemento = "";
    $apenasDigitos = preg_replace('/\D/', '', $cpf_cnpj);

    if (strlen($apenasDigitos) === 11) {
        $complemento = ", <b>CPF nº $cpf_cnpj</b>";
    } elseif (strlen($apenasDigitos) === 14) {
        $complemento = ", <b>CNPJ nº $cpf_cnpj</b>";
    } else {
        $complemento = "";
    }

    return ucfirst($complemento);
}

// Testes
// echo numeroPorExtenso(100000);      
// echo "\n";
// echo numeroPorExtenso(1250.75);    
// echo "\n";
// echo numeroPorExtenso(1000000);
