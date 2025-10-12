<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Atribuição de Processos</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/lists.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <style>
    .autocomplete-list {
        border: 1px solid #ccc;
        max-height: 200px;
        overflow-y: auto;
        position: absolute;
        background: #fff;
        width: 300px;
        z-index: 1000;
    }
    .autocomplete-item {
        padding: 6px;
        cursor: pointer;
    }
    .autocomplete-item:hover {
        background: #eee;
    }

    #resultados{
        display: block;
        margin: .68rem;
        padding: .125rem;
    }

    #resultados li{
        min-width: 100%;
        text-align: start;
    }

    #resultados li:hover{
        background: #ccc;
    }
    </style>

</head>
<body>

  <?php 
        include('header.php'); 
        include('../api/conexao.php');

        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ./index.php");
            exit;
        }

        $sql = "SELECT processos.*, assuntos.nome AS n_assunto
                FROM processos
                INNER JOIN assuntos ON processos.assunto = assuntos.id
                WHERE processos.id = $_POST[id]";

        $result = mysqli_query($conexao, $sql);

        if(mysqli_num_rows($result) > 0) {

            while($dados = mysqli_fetch_assoc($result)){
                $n_protocolo = $dados["n_protocolo"];
                $data_processo = date('d/m/Y', strtotime($dados["data_processo"]));
                $nome_assunto = $dados["n_assunto"];
                $assunto = $dados["assunto"];
                $inscricao = $dados["inscricao"];
                $nome_interessado = $dados["nome_interessado"];
                $cpf_cnpj = $dados["cpf_cnpj"];
                $email = $dados["email"];
                $telefone = $dados["telefone"];
                $observacoes = $dados["observacoes"];
                $pendencia = $dados["pendencia"];
                $ano = date('Y', strtotime($dados['data_processo']));
                $status = $dados["status"];
            }

        }

  ?>

  <div class="content">

    <div class="form-model">

        <ul class="list-title">
            <li>Atribuição</li>
        </ul>

        <!-- <form id="cadastroForm">

        <input type="hidden" name="id" value="<?php //echo $_POST['id']; ?>"> -->

        <div class="process-info">
            <span><b>Nº Protocolo</b></span>
            <span><?php echo "$n_protocolo/$ano"; ?></span>
        </div>

        <div class="process-info">
            <span><b>Assunto</b></span>
            <span><?php echo "$nome_assunto"; ?></span>
        </div>

        <div class="process-info">
            <span><b>Data Processo</b></span>
            <span><?php echo "$data_processo"; ?></span>
        </div>

        <div class="process-info">
            <span><b>Inscrição</b></span>
            <span><?php echo "$inscricao"; ?></span>
        </div>

        <div class="process-info">
            <span><b>Interessado</b></span>
            <span><?php echo "$nome_interessado"; ?></span>
        </div>

        <div class="process-info">
            <span><b>CPF/CNPJ</b></span>
            <span><?php echo "$cpf_cnpj"; ?></span>
        </div>

        <div class="process-info">
            <span><b>E-mail</b></span>
            <span><?php echo "$email"; ?></span>
        </div>

        <div class="process-info">
            <span><b>Telefone</b></span>
            <span><?php echo "$telefone"; ?></span>
        </div>

        <div class="process-info">
            <span><b>Observações</b></span>
            <span><?php echo "$observacoes"; ?></span>
        </div>

        <div class="process-info">
            <span><b>Status</b></span>
            <span><?php echo "$status"; ?></span>
        </div>

        <?php if($pendencia){ ?>
            <div class="process-info">
                <span><b>Pendência</b></span>
                <span><?php echo "$pendencia"; ?></span>
            </div>
        <?php
            }

            include('utils/history_list.php');
            
            if(isset($tipo_atual)) {

                switch($tipo_atual){
                    case 'usuario':
                        $check_user = 'checked';
                        $check_sector = '';
                        $check_folder = '';
                        $valor_destino = 'usuario';
                        break;
                    case 'setor':
                        $check_user = '';
                        $check_sector = 'checked';
                        $check_folder = '';
                        $valor_destino = 'setor';
                        break;
                    case 'pasta':
                        $check_user = '';
                        $check_sector = '';
                        $check_folder = 'checked';
                        $valor_destino = 'pasta';
                        break;
                    default:
                        $check_user = '';
                        $check_sector = '';
                        $check_folder = '';
                        $valor_destino = '';
                        break;
                }
            }else{
                $check_user = '';
                $check_sector = '';
                $check_folder = '';
                $valor_destino = '';
            }

            if($status != 'Finalizado'){
        ?>

        <!-- Atribuição -->
        <div class="destiny-selection">
            <span><b>Categoria de Destino</b></span>
            <div class="radios">
                <label>
                    <input type="radio" name="destino" value="usuario" onclick="mostrarSelect('usuario'), removerValores('usuario')"
                    <?php echo "$check_user"; ?>
                    > Usuário
                </label>
                <?php
                    if($_SESSION['categoria'] != 4){
                ?>
                    <label>
                        <input type="radio" name="destino" value="setor" onclick="mostrarSelect('setor'), removerValores('setor')"
                        <?php echo "$check_sector"; ?>
                        >
                        Setor
                    </label>
                    <label>
                        <input type="radio" name="destino" value="pasta" onclick="mostrarSelect('pasta'), removerValores('pasta')"
                        <?php echo "$check_folder"; ?>
                        >
                        Pasta
                    </label>
                    <!-- <label>
                        <input type="radio" name="destino" value="finalizar" onclick="mostrarFinalizar()">
                        Finalizar
                    </label> -->
                <?php
                    }
                ?>
            </div>
            
            <?php
                // if(isset($local_atual) && $local_atual != ''){
                //     $display_local = "";
                // }else{
                //     $display_local = "style='display: none'";
                // }

                // if(isset($tipo_atual) && $tipo_atual == 'usuario'){
                //     $display_usuario = "style='display: flex'";
                //     $display_setor = "style='display: none'";
                //     $display_pasta = "style='display: none'";
                // }else if(isset($tipo_atual) && $tipo_atual == 'setor'){
                //     $display_usuario = "style='display: none'";
                //     $display_setor = "style='display: flex'";
                //     $display_pasta = "style='display: none'";
                // }else if(isset($tipo_atual) && $tipo_atual == 'pasta'){
                //     $display_usuario = "style='display: none'";
                //     $display_setor = "style='display: none'";
                //     $display_pasta = "style='display: flex'";
                // }else{
                //     $display_usuario = "style='display: none'";
                //     $display_setor = "style='display: none'";
                //     $display_pasta = "style='display: none'";
                // }

                $display_local = (!empty($local_atual)) ? "" : "style='display: none'";

                // Define os tipos possíveis
                $tipos = ['usuario', 'setor', 'pasta'];
                $display_usuario = $display_setor = $display_pasta = "style='display: none'";

                // Se $tipo_atual for válido, ativa apenas o correspondente
                if (in_array($tipo_atual ?? '', $tipos)) {
                    ${"display_" . $tipo_atual} = "style='display: flex'";
                }

            ?>

        </div>

        <form 
            id="cadastroForm" 
            <?php echo $display_local; ?>
        >
            <!-- Divs com os selects (escondidas no começo) -->
            <div id="selects" class="destiny-selection">

                <div 
                    id="select-usuario" 
                    class="destiny-options"
                    <?php echo $display_usuario; ?>
                >
                    <label for="usuario"><b>Escolha o usuário:</b></label>
                    <select id="usuario" name="usuario">
                        <!-- Opções de usuários aqui -->
                        <?php

                            if($local_atual != '' && $tipo_atual == 'usuario'){
                                echo "<option value='$id_atual'>$local_atual</option>";
                            }else{
                                echo "<option value=''>Selecione um usuário</option>";
                            }

                            // Descomentar quando estiver funcionando para todos
                            // if($_SESSION['categoria'] == 3){
                            //     $condicao = 'WHERE categoria = 2 OR categoria = 4';
                            // }else{
                            //     $condicao = 'WHERE categoria = 3';
                            // }
                            
                            // if($_SESSION['categoria'] != 3) {
                            //     $condicao = 'WHERE categoria > 0';
                            // }
                            //Fim

                            //Comentar quando estiver na versão final
                            $condicao = 'WHERE agentes.ativo = 1 AND categoria <> 1';
                            //Fim

                            $sql = "SELECT agentes.id AS agente_id, nome
                            FROM agentes
                            INNER JOIN usuarios ON agentes.id = usuarios.agente_id
                            $condicao
                            AND agentes.ativo = 1
                            ORDER BY nome ";
                            $result = mysqli_query($conexao, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                while ($dados = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$dados['agente_id']}'>{$dados['nome']}</option>";
                                }
                            } else {
                                echo "<option>Nenhum usuário encontrado.</option>";
                            }
                        ?>
                    </select>
                </div>

                <div 
                    id="select-setor" 
                    class="destiny-options" 
                    <?php echo $display_setor; ?>
                >
                    <label for="setor"><b>Escolha o setor:</b></label>
                    <select id="setor" name="setor">
                        <!-- <option value="">Selecione um setor</option> -->
                        <!-- Opções de setores aqui -->
                        <?php

                            if($local_atual != '' && $tipo_atual == 'setor'){
                                echo "<option value='$id_atual'>$local_atual</option>";
                            }else{
                                echo "<option value=''>Selecione um setor</option>";
                            }

                            $sql = "SELECT id, nome
                            FROM setores
                            WHERE ativo = 1
                            ORDER BY nome ";
                            $result = mysqli_query($conexao, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                while ($dados = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$dados['id']}'>{$dados['nome']}</option>";
                                }
                            } else {
                                echo "<option>Nenhum setor encontrado.</option>";
                            }
                        ?>
                    </select>
                </div>

                <div 
                    id="select-pasta" 
                    class="destiny-options" 
                    <?php echo $display_pasta; ?>
                >
                    <label for="pasta"><b>Escolha a pasta:</b></label>
                    <input 
                        type="text" 
                        id="buscaPasta"
                        <?php 
                            if($local_atual != '' && $tipo_atual == 'pasta'){
                                echo "value = '$local_atual'";
                            }else{
                                echo "placeholder='Digite para buscar pastas...'";
                            } 
                        ?>>
                    <input 
                        type="hidden" 
                        id="pasta"
                        name="pasta"
                        value="<?php 
                                if($local_atual != '' && $tipo_atual == 'pasta'){
                                    echo "$id_atual";
                                } 
                        ?>">

                    <ul id="resultados" style="border:1px solid #ccc; max-height:150px; overflow-y:auto; list-style:none;">
                    </ul>

                    <!-- <select id="pasta" name="pasta">
                        <?php

                            // if($local_atual != '' && $tipo_atual == 'pasta'){
                            //     echo "<option value='$id_atual'>$local_atual</option>";
                            // }else{
                            //     echo "<option value=''>Selecione uma pasta</option>";
                            // }

                            // $sql = "SELECT id, nome
                            // FROM pastas
                            // WHERE ativo = 1
                            // ORDER BY nome";
                            // $result = mysqli_query($conexao, $sql);

                            // if (mysqli_num_rows($result) > 0) {
                            //     while ($dados = mysqli_fetch_assoc($result)) {
                            //         echo "<option value='{$dados['id']}'>{$dados['nome']}</option>";
                            //     }
                            // } else {
                            //     echo "<option>Nenhuma pasta encontrada.</option>";
                            // }
                        ?>
                    </select>-->
                </div>

                <!-- Status, discordo disso aqui, mas pediram assim -->
                <div id="select-status" class="form-group" style="width: 100%;">
                    <label for="status"><b>Status:</b></label>
                    <select id="status" name="status">
                        <?php 
                            if($status != ''){
                                echo "<option value='$status'>$status</option>";
                            }else{
                                echo "<option value=''>Selecione Status</option>";
                            }
                        ?>
                        <!-- <option value="">Selecione Status</option> -->
                        <!-- <option value="<?php echo "$status"; ?>"><?php echo "$status"; ?></option> -->
                        <option value="Sob Análise">Sob Análise</option>
                        <option value="Despacho Elaborado">Despacho Elaborado</option>
                        <option value="Certidão Elaborada">Certidão Elaborada</option>
                        <option value="Pendência">Pendência</option>
                        <option value="Aguardando Contribuinte">Aguardando Contribuinte</option>
                        <option value="Encaminhado">Encaminhado</option>
                        <option value="Finalizado sem Arquivar">Finalizado sem Arquivar</option>
                        <option value="Finalizado  e Arquivado">Finalizado e Arquivado</option>
                        <option value="Finalizado e Arquivado sem Digitalização">Finalizado e Arquivado sem Digitalização</option>
                        <option value="Finalizado, Digitalizado e Arquivado">Finalizado, Digitalizado e Arquivado</option>
                    </select>
                </div>
                <!-- Fim de status -->

                <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">

                <input type="hidden" name="destino" id="destino" value="<?php echo "$valor_destino"; ?>">

                <div class="form-group button">
                    <button class="form-btn blue-btn" type="submit">Atribuir</button>
                </div>

            </div>

        </form>

        <form id="finalizarForm" style="display: none;">
            <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">
            <input type="hidden" id='status' name='status' value='Finalizado'>
            <div class="form-group button">
                <button class="form-btn red-btn" type="submit">Finalizar</button>
            </div>
        </form>
    
    <?php
            }
    ?>

    </div>

  </div>

  <script>
    let categoria = <?php echo $_SESSION['categoria']; ?>
    /*document.getElementById("cadastroForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const form = document.getElementById("cadastroForm");
        const formData = new FormData(form);
        fetch("../api/atribuir_processo.php", {
        method: "POST",
        body: formData
        })
        .then(res => res.json())
        .then(data => alert(data.mensagem))
        .then(
            if(categoria > 2){
                window.location.href = "painel.php";
            }else{
                window.location.href = "lista_processos.php";
            }
        );
    });*/
    
    //Deixar aqui pra ver o erro, depois comentar e utilizar o código acima
    document.getElementById("cadastroForm").addEventListener("submit", function(e) {
        e.preventDefault();

        const form = document.getElementById("cadastroForm");
        const formData = new FormData(form);
        fetch("../api/atribuir_processo.php", {
        method: "POST",
        body: formData
        })
        .then(res => res.text()) // <- muda para .text() para inspecionar
        .then(text => {
        console.log("Resposta bruta:", text); // veja o erro real no console
        try {
            const data = JSON.parse(text);
            alert(data.mensagem);
            if(categoria > 2){
                window.location.href = "painel.php";
            }else{
                window.location.href = "lista_processos.php";
            }
        } catch (e) {
            console.error("Erro ao interpretar JSON:", e);
        }
        });
    });

    document.getElementById("finalizarForm").addEventListener("submit", function(e) {
      e.preventDefault();

      if (confirm("Tem certeza que deseja finalizar permanentemente este processo? Após o encerramento não poderão ser feitas alterações ou atualizações no processo!")) {
        const form = document.getElementById("finalizarForm");
        const formData = new FormData(form);
        fetch("../api/finalizar_processo.php", {
            method: "POST",
            body: formData
        })
        .then(res => res.json())
        .then(data => alert(data.mensagem))
        .then(window.location.href ="lista_processos.php");
        }
    });

    function mostrarSelect(tipo) {
        // Esconde todos primeiro
        document.getElementById('finalizarForm').style.display = 'none';
        document.getElementById('select-usuario').style.display = 'none';
        document.getElementById('select-setor').style.display = 'none';
        document.getElementById('select-pasta').style.display = 'none';

        // Mostra apenas o que foi selecionado
        document.getElementById('cadastroForm').style.display = 'block';
        if (tipo === 'usuario') {
            document.getElementById('select-usuario').style.display = 'flex';
        } else if (tipo === 'setor') {
            document.getElementById('select-setor').style.display = 'flex';
        } else if (tipo === 'pasta') {
            document.getElementById('select-pasta').style.display = 'flex';
        }

        document.getElementById('destino').value = tipo;
    }

    function mostrarFinalizar() {
        // Esconde todos primeiro
        document.getElementById('finalizarForm').style.display = 'block';

        document.getElementById('cadastroForm').style.display = 'none';
    }

    function removerValores(tipo) {
        const elementos = ['usuario', 'pasta', 'setor'];

        elementos.forEach(id => {
            const el = document.getElementById(id);
            el.value = '';
            // el.selectedIndex = 0;
        });
    }

    //Busca de pasta por termo
    document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('buscaPasta');
    // const pasta = document.getElementById('pasta');
    const resultados = document.getElementById('resultados');
    let timeout;

    input.addEventListener('input', () => {
        clearTimeout(timeout);
        const termo = input.value.trim();
        if (termo.length < 1) {
        resultados.innerHTML = '';
        return;
        }

        timeout = setTimeout(() => {
        fetch(`utils/buscar_pasta.php?q=${encodeURIComponent(termo)}`)
            .then(resp => resp.json())
            .then(data => {
            resultados.innerHTML = '';
            if (!data.length) {
                resultados.innerHTML = '<li style="padding:4px">Nenhuma pasta encontrada</li>';
                return;
            }

            data.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item.nome;  // usa o campo "nome" retornado
                li.style.padding = '4px';
                li.style.cursor = 'pointer';
                li.addEventListener('click', () => {
                input.value = item.nome;
                resultados.innerHTML = '';
                // Se precisar guardar o ID, você pode criar um input hidden:
                document.getElementById('pasta').value = item.id;
                });
                resultados.appendChild(li);
            });
            })
            .catch(err => {
            console.error('Erro ao buscar pastas:', err);
            resultados.innerHTML = '<li style="padding:4px">Erro na busca</li>';
            });
        }, 300); // delay para evitar muitas requisições
    });
    });
    //Fim
  </script>

</body>
</html>



  
