<div class="filter-model search">

    <!-- <ul id="filter-title" class="filter-title rounded-border-bottom"> -->
    <ul id="filter-title" class="filter-title">
        <li>Filtro de Busca</li>
        <!-- <li onclick="exibeFiltro(this)">Exibir</li> -->
         <li onclick="exibeFiltro(this)">Ocultar</li>
    </ul>

    <!-- <form id="form-filter" method="GET" action="" style="display: none; margin-bottom: 20px;"> -->
    <form id="form-filter" method="GET" action="" style="display: block; margin-bottom: 20px;">

        <div class="filter-row">
            <div class="filter-group">
                <label for="n_protocolo">Nº de Protocolo</label>
                <input type="text" name="n_protocolo" placeholder="Nº de Protocolo" value="<?= isset($_GET['n_protocolo']) ? htmlspecialchars($_GET['n_protocolo']) : '' ?>">
            </div>

            <div class="filter-group">
                <label for="interessado">Nome do Interessado</label>
                <input type="text" name="interessado" placeholder="Pesquisar por nome" value="<?= isset($_GET['interessado']) ? htmlspecialchars($_GET['interessado']) : '' ?>">
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
                <label for="cpf_cnpj">CPF ou CNPJ</label>
                <input type="text" name="cpf_cnpj" placeholder="Pesquisar por CPF ou CNPJ" value="<?= isset($_GET['cpf_cnpj']) ? htmlspecialchars($_GET['cpf_cnpj']) : '' ?>">
            </div>

            <div class="filter-group">
                <label for="assunto">Assunto</label>
                <select id="assunto" name="assunto">
                    <option value=""></option>
                    <?php 
                        $sql = "SELECT *
                        FROM assuntos 
                        WHERE ativo = 1
                        ORDER BY nome";
                        $result = mysqli_query($conexao, $sql);
                        if(mysqli_num_rows($result) > 0) {
                            while($dados = mysqli_fetch_assoc($result)){
                                echo "<option value='$dados[id]'>$dados[nome]</option>";
                            }
                        }else{
                            echo "<option value=''>Não há assuntos cadastrados</option>";
                        }   
                    ?>
                </select>
            </div>
        </div>

        <?php
            if($referencia == 'painel'){

            }else{        
        ?>

        <div class="destiny-selection">
            <span><b>Destino</b></span>
            <div class="radios">
                <label>
                    <input 
                    type="radio" 
                    name="destino" 
                    value="usuario" 
                    onclick="mostrarSelect('usuario'), removerValores('usuario_localizado')"
                    <?php
                        if(isset($_GET['usuario_localizado']) && $_GET['usuario_localizado'] != ''){
                            echo "checked";
                        }
                    ?>
                    > Usuário
                </label>
                <label>
                    <input 
                    type="radio" 
                    name="destino" 
                    value="setor" 
                    onclick="mostrarSelect('setor'), removerValores('setor_localizado')"
                    <?php
                        if(isset($_GET['setor_localizado']) && $_GET['setor_localizado'] != ''){
                            echo "checked";
                        }
                    ?>
                    >
                    Setor
                </label>
                <label>
                    <input 
                    type="radio" 
                    name="destino" 
                    value="pasta" 
                    onclick="mostrarSelect('pasta'), removerValores('pasta_localizada')"
                    <?php
                        if(isset($_GET['pasta_localizada']) && $_GET['pasta_localizada'] != ''){
                            echo "checked";
                        }
                    ?>
                    >
                    Pasta
                </label>
            </div>
        </div>

        <div id="usuario" class="form-group destino" style="display: none;">
            <label for="usuario_localizado">Usuário Responsável</label>
            <input type="text" id="usuario_localizado"  name="usuario_localizado" placeholder="Buscar por usuário" value="<?= isset($_GET['usuario_localizado']) ? htmlspecialchars($_GET['usuario_localizado']) : '' ?>">
        </div>

        <div id="pasta" class="form-group destino" style="display: none;">
            <label for="pasta_localizada">Pasta</label>
            <input type="text" id="pasta_localizada"  name="pasta_localizada" placeholder="Buscar por pasta" value="<?= isset($_GET['pasta_localizada']) ? htmlspecialchars($_GET['pasta_localizada']) : '' ?>">
        </div>

        <div id="setor" class="form-group destino" style="display: none;">
            <label for="setor_localizado">Setor Encaminhado</label>
            <input type="text" id="setor_localizado"  name="setor_localizado" placeholder="Buscar por setor" value="<?= isset($_GET['setor_localizado']) ? htmlspecialchars($_GET['setor_localizado']) : '' ?>">
        </div>

        <?php
            }
        ?>

        <div class="form-group button">
            <button type="submit" class="form-btn blue-btn">Buscar</button>
        </div>

    </form>

</div>

<script>
    let eUser = document.getElementById("usuario_localizado").value;
    let eSector = document.getElementById("setor_localizado").value;
    let eFile = document.getElementById("pasta_localizada").value;

    let idUser = document.getElementById("usuario");
    let idSector = document.getElementById("setor");
    let idFile = document.getElementById("pasta");

    if(eUser != ''){
        idUser.style.display = 'flex';
    }else if(eSector != ''){
        idSector.style.display = 'flex';
    }else if(eFile != ''){
        idFile.style.display = 'flex';
    }

    function removerValores(tipo) {
        const elementos = ['usuario_localizado', 'pasta_localizada', 'setor_localizado'];

        elementos.forEach(id => {
            const el = document.getElementById(id);
            el.value = '';
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
</script>