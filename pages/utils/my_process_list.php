<div class="list-model">
    <ul class="list-title">
        <li>Nº Protocolo</li>
        <li>Assunto</li>
        <li>Interessado</li>
        <li>Status</li>
        <li></li>
        <li></li>
    </ul>

    <?php
        $meuId = $_SESSION['agente_id']; 

        $porPagina = 10;
        $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        if($pagina < 1) $pagina = 1;
        $offset = ($pagina - 1) * $porPagina;

        // BUSCA apenas processos que estão destinados ao usuário logado e ainda não foram recebidos
        // $sql = "SELECT processos.*, processos.id AS id_processo, assuntos.nome AS n_assunto, localizacoes.id AS id_localizacao, localizacoes.localizado_em, localizacoes.recebido_em
        // FROM processos
        // INNER JOIN assuntos ON processos.assunto = assuntos.id
        // INNER JOIN localizacoes ON localizacoes.id_processo = processos.id
        // WHERE processos.ativo = 1
        // AND localizacoes.destino_tipo = 'usuario'
        // AND localizacoes.destino_id = ?
        // AND localizacoes.ativo = 1
        // ORDER BY localizacoes.localizado_em DESC
        // LIMIT ? OFFSET ?";

        $sql = "SELECT processos.*, 
        processos.id AS id_processo, 
        assuntos.nome AS n_assunto, 
        localizacoes.id AS id_localizacao, 
        localizacoes.localizado_em, 
        localizacoes.recebido_em
        FROM processos
        INNER JOIN assuntos ON processos.assunto = assuntos.id
        INNER JOIN localizacoes ON localizacoes.id_processo = processos.id
        WHERE processos.ativo = 1
        AND localizacoes.ativo = 1
        AND localizacoes.id IN (
            SELECT MAX(l2.id)
            FROM localizacoes l2
            WHERE l2.ativo = 1
            GROUP BY l2.id_processo
            HAVING MAX(l2.destino_tipo = 'usuario' AND l2.destino_id = ?) = 1
        )
        ORDER BY localizacoes.localizado_em DESC
        LIMIT ? OFFSET ?";

        $stmt = $conexao->prepare($sql);
        $stmt->bind_param("iii", $meuId, $porPagina, $offset);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($dados = $result->fetch_assoc()) {
                $id_processo = $dados['id_processo'];

                echo "<ul class='list-items'>
                        <li>{$dados['n_protocolo']}/".date('Y', strtotime($dados['data_processo']))."</li>
                        <li>{$dados['n_assunto']}</li>
                        <li>{$dados['nome_interessado']}</li>
                        <li>{$dados['status']}</li>
                        <li>";

                // Se ainda não recebeu, mostrar botão "Receber"
                if($dados['status'] != 'Finalizado'){
                    
                    if (empty($dados['recebido_em'])) {
                        echo "<form method='POST' action='analisar_processo.php'>
                                <input type='hidden' name='id' value='{$dados['id_processo']}'>
                                <button type='submit' class='list-btn green-btn'>Analisar</button>
                            </form>
                            </li>
                            <li>
                                <form method='POST' action='atribuir_processo.php'>
                                    <input type='hidden' name='id' value='{$dados['id_processo']}'>
                                    <button type='submit' class='list-btn gray-btn'>Atribur</button>
                                </form>";
                    } else {
                        echo "<form method='POST' action='analisar_processo.php'>
                                <input type='hidden' name='id' value='{$dados['id_processo']}'>
                                <button type='submit' class='list-btn blue-btn'>Recebido</button>
                            </form>
                            </li>
                            <li>
                                <form method='POST' action='atribuir_processo.php'>
                                    <input type='hidden' name='id' value='{$dados['id_processo']}'>
                                    <button type='submit' class='list-btn gray-btn'>Atribur</button>
                                </form>";
                    }
                }else{
                    echo "<li>
                            <form method='POST' action='analisar_processo.php'>
                                <input type='hidden' name='id' value='{$dados['id_processo']}'>
                                <button type='submit' class='list-btn gray-btn'>Acessar</button>
                            </form></li><li>";
                }

                echo "</li>
                    </ul>";
            }
        } else {
            echo "<div class='list-items'><span>Não há processos atribuídos a você</span></div>";
        }
    ?>
</div>