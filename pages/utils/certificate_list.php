<?php include('utils/process_filter.php'); ?>

<div class="list-model">
    <ul class="list-title">
        <li>Nº Protocolo</li>
        <li>Assunto</li>
        <li>Interessado</li>
        <li>Localização</li>
        <li></li>
        <li></li>
        <li></li>
    </ul>

    <?php

        $meuId = $_SESSION["usuario_id"];

        $porPagina = 10;
        $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if ($pagina < 1) $pagina = 1;
        $offset = ($pagina - 1) * $porPagina;

        $n_protocolo = isset($_GET['n_protocolo']) ? trim($_GET['n_protocolo']) : '';
        $data_inicial = isset($_GET['data_inicial']) ? trim($_GET['data_inicial']) : '';
        $data_final = isset($_GET['data_final']) ? trim($_GET['data_final']) : '';
        $interessado = isset($_GET['interessado']) ? trim($_GET['interessado']) : '';
        $cpf_cnpj = isset($_GET['cpf_cnpj']) ? trim($_GET['cpf_cnpj']) : '';
        $assunto = isset($_GET['assunto']) ? trim($_GET['assunto']) : '';
        $inscricao = isset($_GET['inscricao']) ? trim($_GET['inscricao']) : '';
        $usuario_localizado = isset($_GET['usuario_localizado']) ? trim($_GET['usuario_localizado']) : '';
        $pasta_localizada = isset($_GET['pasta_localizada']) ? trim($_GET['pasta_localizada']) : '';
        $setor_localizado = isset($_GET['setor_localizado']) ? trim($_GET['setor_localizado']) : '';

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

        if (!empty($inscricao)) {
            $buscaSegura = mysqli_real_escape_string($conexao, $inscricao);
            $condicoes .= " AND (inscricao LIKE '%$inscricao%')";
        }

        if (!empty($usuario_localizado)) {
            $buscaSegura = mysqli_real_escape_string($conexao, $usuario_localizado);
            $condicoes .= " AND (agentes.nome LIKE '%$buscaSegura%')";
            echo "<script></script>";
        }

        if (!empty($pasta_localizada)) {
            $buscaSegura = mysqli_real_escape_string($conexao, $pasta_localizada);
            $condicoes .= " AND (pastas.nome LIKE '%$buscaSegura%') AND (destino_tipo = 'pasta')";
            echo "<script></script>";
        }

        if (!empty($setor_localizado)) {
            $buscaSegura = mysqli_real_escape_string($conexao, $setor_localizado);
            $condicoes .= " AND (setores.nome LIKE '%$buscaSegura%') OR (setores.sigla LIKE '%$buscaSegura%')";
        }


        //Acho que isso pode ser enxuto se jogar o cargo de coordenador no primeiro if
        if ($_SESSION["categoria"] > 3) {
            $condicoes .= " AND certidoes.criado_por = $meuId";
        }else if($_SESSION["categoria"] <= 3 && isset($_GET['cargo']) && ($_GET['cargo']=== 'coordenador')) {
            $condicoes .= "  AND certidoes.criado_por = $meuId";
        }else {
            $condicoes .= "";
        }

        $sql = "SELECT processos.*, 
               assuntos.nome AS n_assunto, 
               localizacoes.destino_id, 
               localizacoes.destino_tipo,
               agentes.nome AS nome_usuario_destino,
               pastas.nome,
               certidoes.id AS certidao_id
        FROM processos
        INNER JOIN assuntos ON processos.assunto = assuntos.id
        INNER JOIN certidoes ON processos.id = certidoes.processo
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
        LEFT JOIN pastas ON localizacoes.destino_tipo = 'pasta' AND localizacoes.destino_id = pastas.id
        LEFT JOIN setores ON localizacoes.destino_tipo = 'setor' AND localizacoes.destino_id = setores.id
        $condicoes
        AND certidoes.ativo = 1
        ORDER BY certidoes.created_at DESC
        LIMIT $porPagina OFFSET $offset";

        // echo $sql;

    $result = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($result) > 0) {

        while ($dados = mysqli_fetch_assoc($result)) {

            $destino_id = $dados['destino_id'];
            $destino_tipo = $dados['destino_tipo'];
            $destino_nome = "Não localizado";
            $pendencia = $dados['pendencia'];
            $certidao_id = $dados['certidao_id'];

            if (!empty($destino_id) && !empty($destino_tipo)) {
                // Consultar o nome correto conforme o tipo
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

            if($dados['status'] == 'Pendência'){
                $btn = "<button class='list-btn yellow-btn' type='submit'>Pendência</button>";
            }else{
                $btn = "<button class='list-btn green-btn' type='submit'>Certidão</button>";
            }

            $form = "<form id='cadastroForm' method='POST' action='certidao.php'>
                                <input type='hidden' name='id' value='$certidao_id'>
                                <div class='form-group button'>
                                    $btn
                                </div>
                            </form>";
            
            $status = mb_strtolower($dados['status'], 'UTF-8');

            echo "<ul class='list-items' tabindex=0 >
                    <li>

                        <form 
                            method='POST' 
                            action='dados_processo.php'>
                            <input type='hidden' name='id' value='{$dados['id']}'>
                            <input class='link-data-process' type='submit' value={$dados['n_protocolo']}/".date('Y', strtotime($dados['data_processo'])).">
                        </form>

                    </li>
                    <li>{$dados['n_assunto']}</li>
                    <li>{$dados['nome_interessado']}</li>
                    <li>
                        {$destino_nome}<br>
                        <span style='text-transform: capitalize;'>
                            {$status}
                        </span>
                    </li>";

            if($dados['status'] != 'Finalizado'){
                echo "<li>$form</li>

                        <li>
                            <form id='cadastroForm' method='POST' action='editar_certidao.php'>
                                <input type='hidden' name='id' value='$certidao_id'>
                                <div class='form-group button'>
                                    <button class='list-btn blue-btn' type='submit'>Editar</button>
                                </div>
                            </form>
                        </li>
                        <li>
                            <form id='deleteForm$certidao_id'>
                                <input type='hidden' name='n_protocolo' value='{$dados['n_protocolo']}/".date('Y', strtotime($dados['data_processo']))."'>
                                <input type='hidden' name='id' value='$certidao_id'>
                                <input type='hidden' name='processo' value='{$dados['id']}'>
                                <button class='list-btn red-btn' type='submit' onclick='deletar(this)'>Excluir</button>
                            </form>
                        </li>";
            }else{
                echo "<li>
                    <form id='cadastroForm' method='POST' action='atribuir_processo.php'>
                        <input type='hidden' name='id' value='$dados[id]'>
                        <div class='form-group button'>
                            <button class='list-btn gray-btn' type='submit'>Acessar</button>
                        </div>
                    </form>
                </li>
                <li></li>
                <li></li>";
            }        
                echo "</ul>";
            }

    }else{
        echo "<div class='list-items'><span>Não há certidões cadastradas</span></div>";
    }

            $sqlTotal = "SELECT COUNT(*) as total 
             FROM processos
             INNER JOIN assuntos ON processos.assunto = assuntos.id
             INNER JOIN certidoes ON processos.id = certidoes.processo
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
             LEFT JOIN pastas ON localizacoes.destino_tipo = 'pasta' AND localizacoes.destino_id = pastas.id
             LEFT JOIN setores ON localizacoes.destino_tipo = 'setor' AND localizacoes.destino_id = setores.id
             $condicoes
             AND certidoes.ativo = 1";

            //  echo "$sqlTotal";
             
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
                        echo "<a href='lista_processos.php?page=$i&$filtrosURL' style='margin: 0 5px;'>$i</a>"; // link para outras páginas
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
    function mostrarSelect(tipo) {
        const elementos = ['usuario', 'pasta', 'setor'];

        elementos.forEach(id => {
            const el = document.getElementById(id);
            el.style.display = (id === tipo) ? 'flex' : 'none';
        });
    }

    function exibeFiltro(e) {
        const titulo = document.getElementById('filter-title');
        const form = document.getElementById('form-filter');

        if(form.style.display === 'none') {
            titulo.classList.remove('rounded-border-bottom');
            form.style.display = 'block';
            e.innerText = 'Ocultar';
        }else{
            titulo.classList.add('rounded-border-bottom');
            form.style.display = 'none';
            e.innerText = 'Exibir';
        }
    }

    function deletar(botao) {
        if (confirm('Você tem certeza que deseja excluir permanentemente esta certidão?')) {
            const form = botao.closest("form");
            const formData = new FormData(form);

            fetch("../api/excluir_certidao.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.mensagem);
                location.reload();
            });
        }
    }

</script>
<script src="../js/geral.js"></script>