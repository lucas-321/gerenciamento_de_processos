<?php 
    include('../api/conexao.php');

    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ./index.php");
        exit;
    }

    $sql_vistoria = "SELECT vistorias.*, vistorias.id AS id_vistoria, agentes.nome AS n_fiscal
            FROM vistorias
            INNER JOIN usuarios ON fiscal = usuarios.id
            INNER JOIN agentes ON agente_id = agentes.id
            WHERE processo = $_POST[id]";

    $result_vistoria = mysqli_query($conexao, $sql_vistoria);

    if(mysqli_num_rows($result_vistoria) > 0) {

        while($dados_vistoria = mysqli_fetch_assoc($result_vistoria)){
            $id_vistoria = $dados_vistoria["id_vistoria"];
            $data_vistoria = $dados_vistoria["data_visita"];
            $id_fiscal = $dados_vistoria["fiscal"];
            $nome_fiscal = $dados_vistoria["n_fiscal"];
            $informacoes_adicionais = $dados_vistoria["informacoes_adicionais"];
        }
    }
?>