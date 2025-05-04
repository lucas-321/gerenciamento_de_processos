<?php
    include('process_filter.php');
?>

<style>
    .assunto-bloco {
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .assunto-titulo {
        background-color: #f0f0f0;
        padding: 10px;
        font-weight: bold;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .assunto-conteudo {
        display: none;
        padding: 10px;
    }

    .list-items {
        list-style: none;
        padding-left: 0;
        border-bottom: 1px solid #ddd;
        padding: 5px 0;
    }

    .list-items li {
        display: inline-block;
        width: 24%;
    }

    .total-geral {
        margin-top: 20px;
        font-weight: bold;
        font-size: 1.1em;
        padding: 10px;
    }

    @media print {
        #assunto_bloco {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
    }
</style>

<div class="list-model">

<ul class="list-title">
    <li>Relatório de Processos</li>
</ul>

<?php
    $condicoes = "WHERE processos.ativo = 1";

    $n_protocolo = isset($_GET['n_protocolo']) ? trim($_GET['n_protocolo']) : '';
    $data_inicial = isset($_GET['data_inicial']) ? trim($_GET['data_inicial']) : '';
    $data_final = isset($_GET['data_final']) ? trim($_GET['data_final']) : '';
    $interessado = isset($_GET['interessado']) ? trim($_GET['interessado']) : '';
    $cpf_cnpj = isset($_GET['cpf_cnpj']) ? trim($_GET['cpf_cnpj']) : '';
    $assunto = isset($_GET['assunto']) ? trim($_GET['assunto']) : '';
    $usuario_localizado = isset($_GET['usuario_localizado']) ? trim($_GET['usuario_localizado']) : '';

    $condicoes = "WHERE processos.ativo = 1";

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
        $condicoes .= " AND (assunto = $assunto)";
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

    $sql = "SELECT processos.*, 
                   assuntos.nome AS n_assunto,
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
            $condicoes
            ORDER BY assuntos.nome ASC, processos.created_at DESC";

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

            echo "<ul class='list-items' style='font-weight: bold;'>
                    <li>Nº Protocolo</li>
                    <li>Interessado</li>
                    <li>Assunto</li>
                    <li>Localização</li>
                  </ul>";

            foreach ($processos as $dados) {
                $destino_id = $dados['destino_id'];
                $destino_tipo = $dados['destino_tipo'];
                $destino_nome = "Não localizado";

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
                        <li>{$dados['n_assunto']}</li>
                        <li>{$destino_nome}</li>
                      </ul>";
            }

            echo "</div></div>";
        }

        echo "<div class='total-geral'>Total geral: $totalGeral processo(s)</div>";

        echo "<!-- Botão de impressão -->
            <div style='text-align: right; margin-bottom: 15px;'>
                <a href='report_print.php'>
                    <button onclick='window.print()' class='form-btn blue-btn'>Imprimir</button>
                </a>
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
</script>