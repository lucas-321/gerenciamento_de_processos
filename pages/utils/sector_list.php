<div class="form-model search">

    <ul class="list-title">
        <li>Busca de Setor</li>
    </ul>

    <form method="GET" action="" style="margin-bottom: 20px;">

        <div class="form-group">
            <label for="busca">Nome ou Sigla</label>
            <input type="text" name="busca" placeholder="Pesquisar por nome ou sigla" value="<?= isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : '' ?>">
        </div>

        <div class="form-group button">
            <button type="submit" class="form-btn blue-btn">Buscar</button>
        </div>

    </form>

</div>

<div class="list-model">

    <ul class="list-title">
        <li>Sigla</li>
        <li>Nome</li>
        <li></li>
        <li></li>
    </ul>

    <?php 
    include('../api/conexao.php');

    $limite = 10; // Quantos usuários por página
    $pagina = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    if ($pagina < 1) $pagina = 1;
    $inicio = ($pagina - 1) * $limite;

    $busca = isset($_GET['busca']) ? trim($_GET['busca']) : '';

    $condicoes = "WHERE ativo = 1";

    if (!empty($busca)) {
        $buscaSegura = mysqli_real_escape_string($conexao, $busca);
        $condicoes .= " AND (setores.nome LIKE '%$buscaSegura%') OR (setores.sigla LIKE '%$buscaSegura%')";
    }

    // Consulta principal
    $sql = "SELECT *
    FROM setores
    $condicoes
    ORDER BY nome
    LIMIT $inicio, $limite";
    $result = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($dados = mysqli_fetch_assoc($result)) {

            echo "<ul class='list-items'>
                    <li>{$dados['sigla']}</li>
                    <li>{$dados['nome']}</li>
                    <li>
                        <form method='POST' action='editar_setor.php'>
                            <input type='hidden' name='id' value='{$dados['id']}'>
                            <button class='list-btn blue-btn' type='submit'>Editar</button>
                        </form>
                    </li>
                    <li>
                        <form id='deleteForm{$dados['id']}'>
                            <input type='hidden' name='id' value='{$dados['id']}'>
                            <button class='list-btn red-btn' type='submit' onclick='deletar(this)'>Excluir</button>
                        </form>
                    </li>
                </ul>";
        }
    } else {
        echo "<div class='list-items'>Nenhum setor encontrado.</div>";
    }

    if ($_SESSION['categoria'] == 1 || $_SESSION['categoria'] == 2) {
        echo "<div class='list-items'>
                <a href='cadastro_setor.php'>
                    <button class='list-btn blue-btn'>Novo Setor</button>
                </a>
            </div>";
    }

    // Paginação
    $sqlTotal = "SELECT COUNT(*) as total 
    FROM setores
    $condicoes";
    $resultTotal = mysqli_query($conexao, $sqlTotal);
    $total = mysqli_fetch_assoc($resultTotal)['total'];

    $totalPaginas = ceil($total / $limite);

    if ($totalPaginas > 1) {
        echo "<div class='pagination'>";

        $arquivoAtual = basename($_SERVER['PHP_SELF']);
        $queryString = $_GET;
        unset($queryString['page']); // remove page pra não duplicar

        $parametros = !empty($queryString) ? '&' . http_build_query($queryString) : '';

        // Botão Anterior
        if ($pagina > 1) {
            $paginaAnterior = $pagina - 1;
            echo "<a href='{$arquivoAtual}?page=$paginaAnterior$parametros'>&laquo; Anterior</a>";
        }

        // Botões numerados
        for ($i = 1; $i <= $totalPaginas; $i++) {
            if ($i == $pagina) {
                echo "<strong style='margin: 0 5px;'>$i</strong>";
            } else {
                echo "<a href='{$arquivoAtual}?page=$i$parametros' style='margin: 0 5px;'>$i</a>";
            }
        }

        // Botão Próximo
        if ($pagina < $totalPaginas) {
            $paginaProxima = $pagina + 1;
            echo "<a href='{$arquivoAtual}?page=$paginaProxima$parametros'>Próximo &raquo;</a>";
        }

        echo "</div>";
    }
    ?>
</div>

<script>
    function deletar(botao) {
        if (confirm('Você tem certeza que deseja excluir permanentemente este setor?')) {
            const form = botao.closest("form");
            const formData = new FormData(form);

            fetch("../api/excluir_setor.php", {
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
<script src="/modal.js"></script>