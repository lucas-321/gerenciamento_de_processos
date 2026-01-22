<div class="filter-model search">

    <!-- <ul id="filter-title" class="filter-title rounded-border-bottom"> -->
    <ul id="filter-title" class="filter-title">
        <li>Filtro de Busca</li>
        <!-- <li onclick="exibeFiltro(this)">Exibir</li> -->
         <li onclick="exibeFiltro(this)">Ocultar</li>
    </ul>

    <form id="form-filter" method="GET" action="" style="display: block; margin-bottom: 20px;">
    <!-- <form id="form-filter" method="GET" action="" style="display: none; margin-bottom: 20px;"> -->

        <div class="filter-row">
            <div class="filter-group">
                <label for="nome_usuario">Nome de Usuário</label>
                <input type="text" name="nome_usuario" placeholder="Nome do usuário" value="<?= isset($_GET['nome_usuario']) ? htmlspecialchars($_GET['nome_usuario']) : '' ?>">
            </div>

            <div class="filter-group">
                <label for="objeto">Item</label>
                <input type="text" name="objeto" placeholder="Pesquisar por nome" value="<?= isset($_GET['objeto']) ? htmlspecialchars($_GET['objeto']) : '' ?>">
            </div>
        </div>

        <div class="filter-row">
            <div class="filter-group">
                <label for="data_inicial">Data Inicial</label>
                <input type="date" name="data_inicial" value="<?= isset($_GET['data_inicial']) ? htmlspecialchars($_GET['data_inicial']) : '' ?>">
            </div>

            <div class="filter-group">
                <label for="data_final">Data Final</label>
                <input type="date" name="data_final" value="<?= isset($_GET['data_final']) ? htmlspecialchars($_GET['data_final']) : '' ?>">
            </div>
        </div>

        <div class="filter-row">

            <div class="filter-group">
                <label for="n_protocolo">Nº de Protocolo</label>
                <input type="text" name="n_protocolo" placeholder="" value="<?= isset($_GET['n_protocolo']) ? htmlspecialchars($_GET['n_protocolo']) : '' ?>">
            </div>

            <div class="filter-group">
                <label for="tipo">Tipo</label>
                <select id="tipo" name="tipo">
                    <option value=""></option>
                    <option value="criar">Criação</option>
                    <!-- <option value="alterar">Alteração</option> -->
                    <option value="editar">Alteração</option>
                    <option value="deletar">Exclusão</option>
                    <option value="analisar">Análise</option>
                    <option value="atribuir">Atribuição</option>
                    <option value="finalizar">Finalização</option>
                </select>
            </div>
        </div>

        <div class="form-group button">
            <button type="submit" class="form-btn blue-btn">Buscar</button>
        </div>

    </form>

</div>

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