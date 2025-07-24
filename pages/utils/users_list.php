<div class="form-model search">

    <!-- <ul id="filter-title" class="filter-title rounded-border-bottom"> -->
    <ul id="filter-title" class="filter-title">
        <li>Busca de usuário</li>
        <li onclick="exibeFiltro(this)">Ocultar</li>
    </ul>

    <form id="form-filter" method="GET" action="" style="display: block; margin-bottom: 20px;">
    <!-- <form id="form-filter" method="GET" action="" style="display: none; margin-bottom: 20px;"> -->

        <div class="form-group">
            <label for="busca">Nome ou CPF</label>
            <input type="text" name="busca" placeholder="Pesquisar por nome ou CPF" value="<?= isset($_GET['busca']) ? htmlspecialchars($_GET['busca']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="matricula">Matrícula</label>
            <input type="text" name="matricula" placeholder="Pesquisar por Matrícula" value="<?= isset($_GET['matricula']) ? htmlspecialchars($_GET['matricula']) : '' ?>">
        </div>

        <div class="form-group">
            <label for="categoria">Categoria</label>
            <select name="categoria">
                <option value="">Todas</option>
                <!--<option value="1" <?= (isset($_GET['categoria']) && $_GET['categoria'] == '1') ? 'selected' : '' ?>>Administrador</option>-->
                <option value="2" <?= (isset($_GET['categoria']) && $_GET['categoria'] == '2') ? 'selected' : '' ?>>Coordenador</option>
                <option value="3" <?= (isset($_GET['categoria']) && $_GET['categoria'] == '3') ? 'selected' : '' ?>>Protocolo</option>
                <option value="4" <?= (isset($_GET['categoria']) && $_GET['categoria'] == '4') ? 'selected' : '' ?>>Analista</option>
                <option value="5" <?= (isset($_GET['categoria']) && $_GET['categoria'] == '5') ? 'selected' : '' ?>>Externo</option>
            </select>
        </div>

        <div class="form-group button">
            <button type="submit" class="form-btn blue-btn">Buscar</button>
        </div>

    </form>

</div>

<div class="list-model">

    <ul class="list-title">
        <li>Usuário</li>
        <li>Nome</li>
        <li>Matrícula</li>
        <li>Categoria</li>
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
    $categoriaFiltro = isset($_GET['categoria']) ? trim($_GET['categoria']) : '';
    $matriculaFiltro = isset($_GET['matricula']) ? trim($_GET['matricula']) : '';

    $condicoes = "WHERE agentes.ativo = 1 AND agentes.id <> 1";

    if (!empty($busca)) {
        $buscaSegura = mysqli_real_escape_string($conexao, $busca);
        $condicoes .= " AND (agentes.nome LIKE '%$buscaSegura%' OR agentes.cpf LIKE '%$buscaSegura%')";
    }

    if (!empty($categoriaFiltro)) {
        $categoriaSegura = (int)$categoriaFiltro;
        $condicoes .= " AND usuarios.categoria = $categoriaSegura";
    }

    if (!empty($matriculaFiltro)) {
        $matriculaSegura = (int)$matriculaFiltro;
        $condicoes .= " AND agentes.matricula LIKE '%$matriculaSegura%'";
    }

    // Consulta principal
    $sql = "SELECT agentes.id, nome, matricula, categoria, foto
    FROM agentes
    INNER JOIN usuarios ON agentes.id = usuarios.agente_id
    $condicoes
    ORDER BY agentes.created_at DESC 
    LIMIT $inicio, $limite";
    $result = mysqli_query($conexao, $sql);

    if (mysqli_num_rows($result) > 0) {
        while ($dados = mysqli_fetch_assoc($result)) {

            $categoria = $dados['categoria'];

            switch ($categoria) {
                case 1:
                    $n_categoria = "Administrador";
                    break;
                case 2:
                    $n_categoria = "Coordenador";
                    break;
                case 3:
                    $n_categoria = "Protocolo";
                    break;
                case 4:
                    $n_categoria = "Analista";
                    break;
                case 5:
                    $n_categoria = "Externo";
                    break;
            }

            echo "<ul class='list-items'>
                    <li>
                        <div class='item-list-painel'>
                            <img src='../fotos_perfil/{$dados['foto']}' alt='img-perfil'>
                        </div>
                    </li>
                    <li>{$dados['nome']}</li>
                    <li>{$dados['matricula']}</li>
                    <li>{$n_categoria}</li>
                    <li>
                        <form method='POST' action='editar_usuario.php'>
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
        echo "<div class='list-items'>Nenhum usuário encontrado.</div>";
    }

    if ($_SESSION['categoria'] == 1 || $_SESSION['categoria'] == 2) {
        echo "<div class='list-items'>
                <a href='cadastro_usuario.php'>
                    <button class='list-btn blue-btn'>Novo Usuário</button>
                </a>
            </div>";
    }

    // Paginação
    $sqlTotal = "SELECT COUNT(*) as total 
    FROM agentes
    INNER JOIN usuarios ON agentes.id = usuarios.agente_id 
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

<script>
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
</script>
</div>