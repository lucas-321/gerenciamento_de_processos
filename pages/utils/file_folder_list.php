<div class="form-model search">

    <ul class="list-title">
        <li>Busca de Pastas</li>
    </ul>

    <form method="GET" action="" style="margin-bottom: 20px;">

        <div class="form-group">
            <label for="busca">Nome ou Cor</label>
            <input type="text" name="busca" placeholder="Pesquisar por nome ou cor" value="<?= isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : '' ?>">
        </div>

        <div class="form-group button">
            <button type="submit" class="form-btn blue-btn">Buscar</button>
        </div>

    </form>

</div>

<div class="list-model">

    <ul class="list-title">
        <li>Nome</li>
        <!-- <li>Cor</li> -->
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
        $condicoes .= " AND (pastas.nome LIKE '%$buscaSegura%') OR (pastas.cor LIKE '%$buscaSegura%')";
    }

    // Consulta principal
    $sql = "SELECT *
    FROM pastas
    $condicoes
    ORDER BY nome
    LIMIT $inicio, $limite";
    $result = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($dados = mysqli_fetch_assoc($result)) {

            echo "<ul class='list-items'>
                    <li title='{$dados['nome']}'>{$dados['nome']}</li>
                    <!--<li>{$dados['cor']}</li>-->
                    <li>
                        <form method='POST' action='editar_pasta.php'>
                            <input type='hidden' name='id' value='{$dados['id']}'>
                            <button class='list-btn blue-btn' type='submit'>Editar</button>
                        </form>
                    </li>
                    <li>
                        <form id='deleteForm{$dados['id']}'>
                            <input type='hidden' name='nome' value='{$dados['nome']}'>
                            <input type='hidden' name='id' value='{$dados['id']}'>
                            <button class='list-btn red-btn' type='submit' onclick='deletar(this)'>Excluir</button>
                        </form>
                    </li>
                </ul>";
        }
    } else {
        echo "<div class='list-items'>Nenhuma pasta encontrado.</div>";
    }

    // if ($_SESSION['categoria'] == 1 || $_SESSION['categoria'] == 2) {
    if ($_SESSION['categoria'] < 4) {
        echo "<div class='list-items'>
                <a href='cadastro_pasta.php'>
                    <button class='list-btn blue-btn'>Nova Pasta</button>
                </a>
            </div>";
    }

    // Paginação
    $sqlTotal = "SELECT COUNT(*) as total 
    FROM pastas
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
        if (confirm('Você tem certeza que deseja excluir permanentemente esta pasta?')) {
            const form = botao.closest("form");
            const formData = new FormData(form);

            fetch("../api/excluir_pasta.php", {
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