<!-- Histórico -->
 <!-- O css está em lists -->
<?php
    $id_processo = (int)$_POST['id']; // segurança com cast

    $sql = "SELECT l.*, 
                CASE 
                    WHEN l.destino_tipo = 'usuario' THEN (SELECT a.nome FROM agentes a WHERE a.id = l.destino_id)
                    WHEN l.destino_tipo = 'pasta' THEN (SELECT p.nome FROM pastas p WHERE p.id = l.destino_id)
                    WHEN l.destino_tipo = 'setor' THEN (SELECT s.sigla FROM setores s WHERE s.id = l.destino_id)
                    ELSE 'Desconhecido'
                END AS nome_destino
            FROM localizacoes l
            WHERE l.id_processo = ?
            ORDER BY l.localizado_em ASC";

    $stmt = $conexao->prepare($sql);
    $stmt->bind_param("i", $id_processo);
    $stmt->execute();
    $result = $stmt->get_result();
?>
<div class="process-info history">
    <span class="history-title"><b>Histórico</b></span>

    <ul class='list-items'>
        <li><b>Local</b></li>
        <li><b>Atribuído</b></li>
        <li><b>Recebido</b></li>
    </ul>

    <?php
        if ($result->num_rows > 0) {
            while ($dados = $result->fetch_assoc()) {

                if($dados['atual'] == 1){
                    $local_atual = $dados['nome_destino'];
                    $id_atual = $dados['destino_id'];
                    $tipo_atual = $dados['destino_tipo'];
                }

                $local = $dados['nome_destino'] ?? '---';
                $atribuido = $dados['localizado_em'] ? date('d/m/Y', strtotime($dados['localizado_em'])) : '-';
                $recebido = $dados['recebido_em'] ? date('d/m/Y', strtotime($dados['recebido_em'])) : '-';

                echo "<ul class='list-items'>
                        <li>$local</li>
                        <li>$atribuido</li>
                        <li>$recebido</li>
                    </ul>";
            }
        } else {
            echo "<ul class='list-items'><li colspan='3'>Nenhum histórico encontrado</li></ul>";
        }
    ?>
</div>
<!-- Fim -->