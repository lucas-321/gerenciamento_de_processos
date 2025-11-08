<?php
function numeroPorExtenso($valor)
{
    // Garante que o valor é numérico e converte para float
    if (is_string($valor)) {
        $valor = trim($valor);
        $valor = str_replace(['R$', '.', ','], ['', '', '.'], $valor);
    }

    if (!is_numeric($valor)) {
        $valor = 0;
    }

    $valor = (float)$valor;
    //Fim

    $valor = number_format($valor, 2, '.', '');
    list($inteiro, $centavos) = explode('.', $valor);
    $inteiro = (int)$inteiro;
    $centavos = (int)$centavos;

    $unidades = ['', 'um', 'dois', 'três', 'quatro', 'cinco',
        'seis', 'sete', 'oito', 'nove', 'dez', 'onze',
        'doze', 'treze', 'quatorze', 'quinze', 'dezesseis',
        'dezessete', 'dezoito', 'dezenove'];
    $dezenas = ['', '', 'vinte', 'trinta', 'quarenta', 'cinquenta',
        'sessenta', 'setenta', 'oitenta', 'noventa'];
    $centenas = ['', 'cento', 'duzentos', 'trezentos', 'quatrocentos',
        'quinhentos', 'seiscentos', 'setecentos', 'oitocentos', 'novecentos'];

    $trio = function($n) use ($unidades, $dezenas, $centenas) {
        $n = (int)$n;
        if ($n == 0) return '';
        if ($n == 100) return 'cem';

        $c = floor($n / 100);
        $d = floor(($n % 100) / 10);
        $u = $n % 10;

        $txt = '';
        if ($c) $txt .= $centenas[$c];
        if ($d < 2 && ($d * 10 + $u) > 0) {
            $txt .= ($txt ? ' e ' : '') . $unidades[$d * 10 + $u];
        } else {
            if ($d) $txt .= ($txt ? ' e ' : '') . $dezenas[$d];
            if ($u) $txt .= ($txt ? ' e ' : '') . $unidades[$u];
        }
        return $txt;
    };

    // Converte por blocos de milhar
    $grupos = [
        1000000000 => 'bilhão',
        1000000    => 'milhão',
        1000       => 'mil',
        1          => ''
    ];

    $partes = [];
    foreach ($grupos as $valorGrupo => $nomeGrupo) {
        if ($inteiro >= $valorGrupo) {
            $qtd = floor($inteiro / $valorGrupo);
            $inteiro %= $valorGrupo;

            $texto = $trio($qtd);
            if ($nomeGrupo) {
                $texto .= ' ' . ($qtd > 1 && $nomeGrupo !== 'mil' ? $nomeGrupo . 'es' : $nomeGrupo);
            }
            $partes[] = $texto;
        }
    }

    if (empty($partes)) {
        $extenso = 'zero';
    } else {
        $extenso = implode(' e ', $partes);
    }

    $extenso .= (array_sum($partes) == 1) ? ' real' : ' reais';

    if ($centavos > 0) {
        $extenso .= ' e ' . $trio($centavos) . ' ' . ($centavos == 1 ? 'centavo' : 'centavos');
    }

    return ucfirst($extenso);
}

// Testes
// echo numeroPorExtenso(100000);      
// echo "\n";
// echo numeroPorExtenso(1250.75);    
// echo "\n";
// echo numeroPorExtenso(1000000);
