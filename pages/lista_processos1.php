<?php
// session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Processos</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/lists.css">
  <link rel="stylesheet" href="../css/pagination.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>

  <?php include('header.php');
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ./index.php");
            exit;
        }
  ?>

  <div class="content list-box">

        <div class="list-model">

            <ul class="list-title">
                <li>Nº Protocolo</li>
                <li>Assunto</li>
                <li>Interessado</li>
                <!-- <li>Inscrição</li> -->
                <!-- <li>Data de Entrada</li> -->
                <li>Localização</li>
                <li>Pendência</li>
                <li></li>
                <li></li>
                <li></li>
            </ul>

            <?php 
                include('../api/conexao.php');

                $porPagina = 10;
                $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
                if ($pagina < 1) $pagina = 1;
                $offset = ($pagina - 1) * $porPagina;
                
                // Agora vamos buscar a última localização também
                $sql = "SELECT processos.*, assuntos.nome AS n_assunto, localizacoes.destino_id, localizacoes.destino_tipo
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
                WHERE processos.ativo = 1
                ORDER BY processos.created_at DESC
                LIMIT $porPagina OFFSET $offset";

            $result = mysqli_query($conexao, $sql);

            if (mysqli_num_rows($result) > 0) {

                while ($dados = mysqli_fetch_assoc($result)) {

                    $destino_id = $dados['destino_id'];
                    $destino_tipo = $dados['destino_tipo'];
                    $destino_nome = "Não localizado";
                    $pendencia = $dados['pendencia'];

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

                    echo "<ul class='list-items'>
                            <li>{$dados['n_protocolo']}/".date('Y', strtotime($dados['data_processo']))."</li>
                            <li>{$dados['n_assunto']}</li>
                            <li>{$dados['nome_interessado']}</li>
                            <li>{$destino_nome}</li>
                            <li>{$pendencia}</li>";

                    if($dados['status'] != 'Finalizado'){
                        echo "<li>
                                    <form id='cadastroForm' method='POST' action='atribuir_processo.php'>
                                        <input type='hidden' name='id' value='$dados[id]'>
                                        <div class='form-group button'>
                                            <button class='list-btn green-btn' type='submit'>Atribuir</button>
                                        </div>
                                    </form>
                                </li>

                                <li>
                                    <form id='cadastroForm' method='POST' action='editar_processo.php'>
                                        <input type='hidden' name='id' value='$dados[id]'>
                                        <div class='form-group button'>
                                            <button class='list-btn blue-btn' type='submit'>Editar</button>
                                        </div>
                                    </form>
                                </li>
                                <li>
                                    <button class='list-btn red-btn'>Excluir</button>
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
                    echo "<div class='list-items'><span>Não há processos cadastrados</span></div>";
                }
                
                if ($_SESSION['categoria'] == 1 || $_SESSION['categoria'] == 2 || $_SESSION['categoria'] == 3) {

                    echo "<div class='list-items'>
                            <a href='cadastro_processo.php'>
                                <button class='list-btn blue-btn'>Novo Processo</button>
                            </a>
                        </div>";
                }

                $sqlTotal = "SELECT COUNT(*) as total 
                FROM processos 
                WHERE ativo = 1 AND id <> 1";
                $resultTotal = mysqli_query($conexao, $sqlTotal);
                $total = mysqli_fetch_assoc($resultTotal)['total'];

                $totalPaginas = ceil($total / $porPagina);

                // Botões de página
                if ($totalPaginas > 1) {
                    echo "<div class='pagination'>";
                    
                    for ($i = 1; $i <= $totalPaginas; $i++) {
                        if ($i == $pagina) {
                            echo "<strong style='margin: 0 5px;'>$i</strong>"; // página atual destacada
                        } else {
                            echo "<a href='lista_processos.php?page=$i' style='margin: 0 5px;'>$i</a>"; // link para outras páginas
                        }
                    }

                    echo "</div>";
                }

            ?>

        </div>

  </div>

</body>
</html>
