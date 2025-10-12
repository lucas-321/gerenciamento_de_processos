<?php
    include('process_filter.php');
?>
<link rel="stylesheet" href="../css/report.css">

<div class="list-model">

<?php
    $periodo = 'em período não especificado';
    $analista = '';
    $condicoes = "WHERE processos.ativo = 1";

    $n_protocolo = isset($_GET['n_protocolo']) ? trim($_GET['n_protocolo']) : '';
    $data_inicial = isset($_GET['data_inicial']) ? trim($_GET['data_inicial']) : '';
    $data_final = isset($_GET['data_final']) ? trim($_GET['data_final']) : '';
    $interessado = isset($_GET['interessado']) ? trim($_GET['interessado']) : '';
    $cpf_cnpj = isset($_GET['cpf_cnpj']) ? trim($_GET['cpf_cnpj']) : '';
    $assunto = isset($_GET['assunto']) ? trim($_GET['assunto']) : '';
    $usuario_localizado = isset($_GET['usuario_localizado']) ? trim($_GET['usuario_localizado']) : '';

    $setor_localizado = isset($_GET['setor_localizado']) ? trim($_GET['setor_localizado']) : '';

    $pasta_localizada = isset($_GET['pasta_localizada']) ? trim($_GET['pasta_localizada']) : '';

    if (!empty($n_protocolo)) {
        $condicoes .= " AND (n_protocolo = $n_protocolo)";
    }

    if (!empty($data_inicial)) {
        $buscaSegura = mysqli_real_escape_string($conexao, $data_inicial);
        $condicoes .= " AND (data_processo >= '$buscaSegura')";
    }
    
    if (!empty($data_final)) {
        $buscaSegura = mysqli_real_escape_string($conexao, $data_final);
        $condicoes .= " AND (data_processo <= '$buscaSegura')";
    }

    if (!empty($interessado)) {
        $buscaSegura = mysqli_real_escape_string($conexao, $interessado);
        $condicoes .= " AND (nome_interessado LIKE '%$buscaSegura%')";
    }

    if (!empty($cpf_cnpj)) {
        $buscaSegura = mysqli_real_escape_string($conexao, $cpf_cnpj);
        $condicoes .= " AND (cpf_cnpj LIKE '%$cpf_cnpj%')";
    }

    if (!empty($assunto)) {
        $condicoes .= " AND (processos.assunto = $assunto)";
    }

    if (!empty($usuario_localizado)) {
        $buscaSegura = mysqli_real_escape_string($conexao, $usuario_localizado);
        $condicoes .= " AND (agentes.nome LIKE '%$buscaSegura%')";
    }

    if (!empty($pasta_localizada)) {
        $buscaSegura = mysqli_real_escape_string($conexao, $pasta_localizada);
        $condicoes .= " AND (pastas.nome LIKE '%$buscaSegura%')";
    }

    if (!empty($setor_localizado)) {
        $buscaSegura = mysqli_real_escape_string($conexao, $setor_localizado);
        $condicoes .= " AND (setores.nome LIKE '%$buscaSegura%')";
    }

    if (!empty($data_inicial) && !empty($data_final)) {
        $periodo = "Entre ".date('d/m/Y', strtotime($data_inicial))." e ".date('d/m/Y', strtotime($data_final))."";
    } else if (!empty($data_inicial)) {
        $periodo = "A partir de ".date('d/m/Y', strtotime($data_inicial))."";
    } else if (!empty($data_final)) {
        $periodo = "Até ".date('d/m/Y', strtotime($data_final))."";
    }

    echo "<div class='list-title'>
            <span>Relatório de Processos $periodo</span>
        </div>";

    $sql = "SELECT processos.*, 
                   assuntos.nome AS n_assunto,
                   data_processo,
                   localizacoes.destino_id, 
                   localizacoes.destino_tipo,
                   agentes.nome AS nome_usuario_destino
            FROM processos
            INNER JOIN assuntos ON processos.assunto = assuntos.id
            LEFT JOIN (
                SELECT l1.*
                FROM localizacoes l1
                INNER JOIN (
                    SELECT id_processo, MAX(localizado_em) AS max_localizado
                    FROM localizacoes
                    WHERE ativo = 1
                    GROUP BY id_processo
                ) l2 ON l1.id_processo = l2.id_processo AND l1.localizado_em = l2.max_localizado
            ) AS localizacoes ON localizacoes.id_processo = processos.id
            LEFT JOIN agentes ON localizacoes.destino_tipo = 'usuario' AND localizacoes.destino_id = agentes.id
            LEFT JOIN setores ON localizacoes.destino_tipo = 'setor' AND localizacoes.destino_id = setores.id
            LEFT JOIN pastas ON localizacoes.destino_tipo = 'pasta' AND localizacoes.destino_id = pastas.id
            $condicoes
            ORDER BY assuntos.nome ASC, processos.created_at DESC";

    // echo "$sql";

    $result = mysqli_query($conexao, $sql);

    $processosPorAssunto = [];
    $totalGeral = 0;

    while ($dados = mysqli_fetch_assoc($result)) {
        $assunto = $dados['n_assunto'];
        $processosPorAssunto[$assunto][] = $dados;
        $totalGeral++;
    }

    if (count($processosPorAssunto) > 0) {
        foreach ($processosPorAssunto as $assunto => $processos) {
            echo "<div id='assunto-bloco' class='assunto-bloco'>";
            echo "<div class='assunto-titulo' onclick='toggleBloco(this)'>
                    <span>$assunto (" . count($processos) . " processos)</span>
                    <span class='icone'>[+]</span>
                  </div>";
            echo "<div class='assunto-conteudo'>";

            echo "<ul class='list-items list-header' style='font-weight: bold;'>
                    <li>Nº Protocolo</li>
                    <li>Interessado</li>
                    <li>Data de Entrada</li>
                    <li>Localização</li>
                  </ul>";

            foreach ($processos as $dados) {
                $destino_id = $dados['destino_id'];
                $destino_tipo = $dados['destino_tipo'];
                $destino_nome = "Não localizado";
                $data_entrada = date('d/m/Y', strtotime($dados['data_processo']));

                if (!empty($destino_id) && !empty($destino_tipo)) {
                    if ($destino_tipo == 'usuario') {
                        $consulta = mysqli_query($conexao, "SELECT nome FROM agentes WHERE id = $destino_id");
                    } elseif ($destino_tipo == 'setor') {
                        $consulta = mysqli_query($conexao, "SELECT nome FROM setores WHERE id = $destino_id");
                    } elseif ($destino_tipo == 'pasta') {
                        $consulta = mysqli_query($conexao, "SELECT nome FROM pastas WHERE id = $destino_id");
                    }

                    if (!empty($consulta) && mysqli_num_rows($consulta) > 0) {
                        $linha = mysqli_fetch_assoc($consulta);
                        $destino_nome = $linha['nome'];
                    }
                }

                echo "<ul class='list-items'>
                        <li>{$dados['n_protocolo']}/" . date('Y', strtotime($dados['data_processo'])) . "</li>
                        <li>{$dados['nome_interessado']}</li>
                        <li>{$data_entrada}</li>
                        <li>{$destino_nome}</li>
                      </ul>";
            }

            echo "</div></div>";
        }

        echo "<div class='total-geral'>Total geral: $totalGeral processo(s)</div>";

        echo "<!-- Botão de impressão -->
            <!--<div style='text-align: right; margin-bottom: 15px;'>
                <a href='report_print.php'>
                    <button onclick='window.print()' class='form-btn blue-btn'>Imprimir</button>
                </a>
            </div>-->
            <div style='text-align: right; margin-bottom: 15px;'>
                <button onclick='imprimirRelatorio()' class='form-btn blue-btn'>Imprimir</button>
            </div>";
            
    } else {
        echo "<div class='list-items'><span>Não há processos cadastrados</span></div>";
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
    janela.document.write('<html><head><title>Relatório de Processos</title>');
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