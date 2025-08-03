<?php
    include('register_filter.php');
?>
<link rel="stylesheet" href="../css/report.css">

<div class="list-model register-box">

<?php

    $porPagina = 20;
    $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($pagina < 1) $pagina = 1;
    $offset = ($pagina - 1) * $porPagina;

    $periodo = 'em período não especificado';
    $analista = '';
    $condicoes = "WHERE 1";

    $nome_usuario = isset($_GET['nome_usuario']) ? trim($_GET['nome_usuario']) : '';
    $data_inicial = isset($_GET['data_inicial']) ? trim($_GET['data_inicial']) : '';
    $data_final = isset($_GET['data_final']) ? trim($_GET['data_final']) : '';
    $objeto = isset($_GET['objeto']) ? trim($_GET['objeto']) : '';
    $tipo = isset($_GET['tipo']) ? trim($_GET['tipo']) : '';

    if (!empty($nome_usuario)) {
        $condicoes .= " AND (nome_usuario LIKE '%$nome_usuario%')";
    }

    if (!empty($data_inicial)) {
        $buscaSegura = mysqli_real_escape_string($conexao, $data_inicial);
        $condicoes .= " AND (data_registro >= '$buscaSegura')";
    }
    
    if (!empty($data_final)) {
        $buscaSegura = mysqli_real_escape_string($conexao, $data_final);
        $condicoes .= " AND (data_registro <= '$buscaSegura')";
    }

    if (!empty($objeto)) {
        $buscaSegura = mysqli_real_escape_string($conexao, $objeto);
        $condicoes .= " AND (objeto LIKE '%$buscaSegura%')";
    }

    if (!empty($tipo)) {
        $buscaSegura = mysqli_real_escape_string($conexao, $tipo);
        $condicoes .= " AND (tipo LIKE '%$tipo%')";
    }

    if (!empty($data_inicial) && !empty($data_final)) {
        $periodo = "Entre ".date('d/m/Y', strtotime($data_inicial))." e ".date('d/m/Y', strtotime($data_final))."";
    } else if (!empty($data_inicial)) {
        $periodo = "A partir de ".date('d/m/Y', strtotime($data_inicial))."";
    } else if (!empty($data_final)) {
        $periodo = "Até ".date('d/m/Y', strtotime($data_final))."";
    }

    echo "<div class='list-title'>
            <span>Registro de Atividades $periodo</span>
        </div>";

    $sql = "SELECT *
            FROM registro_atividades
            $condicoes
            ORDER BY data_registro DESC
            LIMIT $porPagina OFFSET $offset";

    $result = mysqli_query($conexao, $sql);
    $totalGeral = 0;

    if (mysqli_num_rows($result) > 0) {

        while ($dados = mysqli_fetch_assoc($result)) {
            echo "<div class='register-list'><span>{$dados['detalhes']}</span></div>";
            $totalGeral++;
        }
            
    } else {
        echo "<div class='list-items'><span>Não há registros cadastrados</span></div>";
    }

    $sqlTotal = "SELECT COUNT(*) as total 
             FROM registro_atividades
             $condicoes";
             
            $resultTotal = mysqli_query($conexao, $sqlTotal);
            $total = mysqli_fetch_assoc($resultTotal)['total'];

            $totalPaginas = ceil($total / $porPagina);


            $queryString = $_GET;
            unset($queryString['page']); // Evita conflito
            $filtrosURL = http_build_query($queryString);

            // Botões de página
            if ($totalPaginas > 1) {
                echo "<div class='pagination'>";

                $maxLinks = 2;

                if ($pagina > 1) {
                    echo "<a href='?page=1&$filtrosURL'>1</a>";
                    if ($pagina > $maxLinks + 2) {
                        echo "<span>...</span>";
                    }
                }

                $start = max(2, $pagina - $maxLinks);
                $end = min($totalPaginas - 1, $pagina + $maxLinks);
                
                for ($i = $start; $i <= $end; $i++) {
                    if ($i == $pagina) {
                        echo "<strong style='margin: 0 5px;'>$i</strong>"; // página atual destacada
                    } else {
                        echo "<a href='registros.php?page=$i&$filtrosURL' style='margin: 0 5px;'>$i</a>"; // link para outras páginas
                    }
                }

                if ($pagina < $totalPaginas - $maxLinks - 1) {
                    echo "<span>...</span>";
                }

                if ($pagina < $totalPaginas) {
                    echo "<a href='?page=$totalPaginas&$filtrosURL'>$totalPaginas</a>";
                }

                echo "</div>";
            }
?>
</div>

<script>
function toggleBloco(titulo) {
    const conteudo = titulo.nextElementSibling;
    const icone = titulo.querySelector('.icone');

    if (conteudo.style.display === 'block') {
        conteudo.style.display = 'none';
        icone.textContent = '[+]';
    } else {
        conteudo.style.display = 'block';
        icone.textContent = '[-]';
    }
}

function mostrarSelect(tipo) {
    const elementos = ['usuario', 'pasta', 'setor'];

    elementos.forEach(id => {
        const el = document.getElementById(id);
        el.style.display = (id === tipo) ? 'flex' : 'none';
    });
}





function imprimirRelatorio() {
    const conteudo = document.querySelector('.list-model').innerHTML;
    const estilo = `
      <style>
        body { font-family: Arial, sans-serif; padding: 20px; }
        li { width: 9rem;}
        .icone { display: none; }
        .list-model { width: 100%; }
        .assunto-bloco {margin-top: 2rem;}
        .list-items, .assunto-bloco { margin-bottom: 10px; }
        .list-title li { width: 100%; text-align: center; font-size: 20px; font-weight: bold; list-style: none; }
        .list-items { display: flex; gap: 10px; list-style: none; padding: 5px 0; border-bottom: 1px solid #ccc; }
        .total-geral { margin-top: 20px; font-weight: bold; }
        .form-btn { display: none; }
      </style>
    `;

    const janela = window.open('', '', 'width=900,height=600');
    janela.document.write('<html><head><title>Relatório de registros</title>');
    janela.document.write(estilo);
    janela.document.write('</head><body>');
    janela.document.write(conteudo);
    janela.document.write('</body></html>');
    janela.document.close();

    janela.onload = () => {
        janela.focus();
        janela.print();
        janela.close();
    };
}
</script>