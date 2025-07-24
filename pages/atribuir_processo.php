<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Atribuição de Processos</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/lists.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>

  <?php include('header.php'); 
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
            if($status != 'Finalizado'){
        ?>

        <!-- Atribuição -->
        <div class="destiny-selection">
            <span><b>Categoria de Destino</b></span>
            <div class="radios">
                <label>
                    <input type="radio" name="destino" value="usuario" onclick="mostrarSelect('usuario')"> Usuário
                </label>
                <?php
                    if($_SESSION['categoria'] != 4){
                ?>
                    <label>
                        <input type="radio" name="destino" value="setor" onclick="mostrarSelect('setor')">
                        Setor
                    </label>
                    <label>
                        <input type="radio" name="destino" value="pasta" onclick="mostrarSelect('pasta')">
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
            
        </div>

        <form id="cadastroForm" style="display: none;">
            <!-- Divs com os selects (escondidas no começo) -->
            <div id="selects" class="destiny-selection">

                <div id="select-usuario" class="destiny-options" style="display:none;">
                    <label for="usuario"><b>Escolha o usuário:</b></label>
                    <select id="usuario" name="usuario">
                        <!-- Opções de usuários aqui -->
                        <?php

                            if($local_atual != '' && $tipo_atual == 'usuario'){
                                echo "<option value='$id_atual'>$local_atual</option>";
                            }else{
                                echo "<option value=''>Selecione um usuário</option>";
                            }

                            if($_SESSION['categoria'] == 3){
                                $condicao = 'WHERE categoria = 2 OR categoria = 4';
                            }else{
                                $condicao = 'WHERE categoria = 3';
                            }
                            
                            // Remover Quando estiver funcionando para todos
                            if($_SESSION['categoria'] != 3) {
                                $condicao = 'WHERE categoria > 0';
                            }

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

                <div id="select-setor" class="destiny-options" style="display:none;">
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

                <div id="select-pasta" class="destiny-options" style="display:none;">
                    <label for="pasta"><b>Escolha a pasta:</b></label>
                    <select id="pasta" name="pasta">
                        <!-- <option value="">Selecione uma pasta</option> -->
                        <!-- Opções de pastas aqui -->
                        <?php

                            if($local_atual != '' && $tipo_atual == 'pasta'){
                                echo "<option value='$id_atual'>$local_atual</option>";
                            }else{
                                echo "<option value=''>Selecione uma pasta</option>";
                            }

                            $sql = "SELECT id, nome
                            FROM pastas
                            WHERE ativo = 1
                            ORDER BY nome";
                            $result = mysqli_query($conexao, $sql);

                            if (mysqli_num_rows($result) > 0) {
                                while ($dados = mysqli_fetch_assoc($result)) {
                                    echo "<option value='{$dados['id']}'>{$dados['nome']}</option>";
                                }
                            } else {
                                echo "<option>Nenhuma pasta encontrada.</option>";
                            }
                        ?>
                    </select>
                </div>

                <!-- Status, discordo disso aqui, mas pediram assim -->
                <div id="select-status" class="form-group" style="width: 100%;">
                    <label for="status"><b>Status:</b></label>
                    <select id="status" name="status">
                        <?php 
                            if($status != ''){
                                echo "<option value=$status>$status</option>";
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

                <input type="hidden" name="destino" id="destino" value="">

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
        .then(window.location.href ="painel.php");
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
            window.location.href = "painel.php";
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
  </script>

</body>
</html>



  
