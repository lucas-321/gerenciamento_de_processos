<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Recebimento de Processos</title>

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

        // Verificar se existem certidões
        $sql_certidoes = "SELECT *
                FROM certidoes
                WHERE processo = $_POST[id]
                AND ativo = 1";
        $result_certidoes = mysqli_query($conexao, $sql_certidoes);

        if(mysqli_num_rows($result_certidoes) > 0) {

            while($dados = mysqli_fetch_assoc($result_certidoes)){
                $certidao_id = $dados["id"];

                $btn_certidao = "<span id='certidaoBtn' style='display: none;'>Processo já possui certidão</span>";
            }
        }else{
            $btn_certidao = "<button id='certidaoBtn' class='form-btn green-btn' style='display: none;' type='button' onclick='criarCertidao()'>Criar Certidão</button>";
        }
        // echo "$sql_certidoes";
        //Fim

  ?>

  <div class="content">

    <div class="form-model">

        <ul class="list-title">
            <li>Análise</li>
        </ul>

          <form id="cadastroForm">

            <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">

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

            <?php if($pendencia){ ?>
            <div class="process-info">
                <span><b>Pendência</b></span>
                <span><?php echo "$pendencia"; ?></span>
            </div>

            <?php    
                
                }

                if($status != 'Finalizado'){
            
            ?>

            <!-- Status -->
            <div class="destiny-selection">
                <span><b>Status</b></span>
                <div class="radios">
                    <label>
                        <input type="radio" name="status" value="Sob análise" onclick="mostrarSelect('sob-analise')">Sob Análise
                    </label>
                    <label>
                        <input type="radio" name="status" value="Vistoria" onclick="mostrarSelect('vistoria')">Vistoria
                    </label>
                    <label>
                        <input type="radio" name="status" value="Pendência" onclick="mostrarSelect('pendencia')">
                        Pendência
                    </label>
                </div>
            </div>

            <!-- Atribuição -->
            <!-- Divs com os selects (escondidas no começo) -->
            <div id="selects" class="destiny-selection">

                <div id="sob-analise" class="destiny-options" style="display:none;">
                    <label for="usuario"><b>Documento Elaborado:</b></label>
                    <div class="radios">

                        <label>
                            <input type="radio" name="status" value="Certidão elaborada" onclick="mostrarArquivo('arquivos'), showBtn('certidao')">Certidão

                            <?php echo $btn_certidao; ?>

                            <!-- <button id="certidaoBtn" class="form-btn green-btn" style="display: none;" type="button" onclick="criarCertidao()">Criar Certidão</button> -->
                        </label>

                        <label>
                            <input type="radio" name="status" value="Despacho elaborado" onclick="mostrarArquivo('arquivos'), showBtn('despacho')">Despacho

                            <button id="despachoBtn" class="form-btn green-btn" style="display: none;" type="button" onclick="criarDespacho()">Criar Despacho</button>

                        </label>
                        
                    </div>

                    <!--<div id="arquivos">
                        <input type="file" name="arquivo" id="arquivo">
                    </div>-->
                </div>

                <div id="vistoria" class="destiny-options" style="display:none;">
                    <label for="usuario"><b>Ação:</b></label>
                    <div class="radios">

                        <label>
                            <button id="certidaoBtn" class="form-btn green-btn" type="button" onclick="agendarVistoria()">Agendar</button>
                        </label>

                        <!-- <label>
                            <input type="radio" name="status" value="Despacho elaborado" onclick="mostrarArquivo('arquivos'), showBtn('despacho')">Despacho

                            <button id="despachoBtn" class="form-btn green-btn" style="display: none;" type="button" onclick="criarDespacho()">Criar Despacho</button>

                        </label> -->
                        
                    </div>

                    <!--<div id="arquivos">
                        <input type="file" name="arquivo" id="arquivo">
                    </div>-->
                </div>

                <div id="pendencia-box" class="destiny-options" style="display:none;">
                    <div class="form-group">
                        <label for="pendencia"><b>Informe a Pendência:</b></label>
                        <textarea name="pendencia" id="pendencia"><?php echo "$pendencia"; ?></textarea>
                    </div>
                </div>

            </div>

            <div class="form-group button">
              <button class="form-btn blue-btn" type="submit">Salvar</button>
            </div>

        <?php
            }
        ?>

          </form>

    </div>

  </div>

  <script>
    document.getElementById("cadastroForm").addEventListener("submit", function(e) {
      e.preventDefault();

      const form = document.getElementById("cadastroForm");
      const formData = new FormData(form);
      fetch("../api/analisar_processo.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => alert(data.mensagem))
      .then(window.location.href ="painel_analista.php");
    });

    function mostrarSelect(tipo) {
        // Esconde todos primeiro
        document.getElementById('sob-analise').style.display = 'none';
        document.getElementById('vistoria').style.display = 'none';
        document.getElementById('pendencia-box').style.display = 'none';

        // Mostra apenas o que foi selecionado
        if (tipo === 'sob-analise') {
            document.getElementById('sob-analise').style.display = 'flex';
        }else if (tipo === 'vistoria') {
            document.getElementById('vistoria').style.display = 'flex';
        } else if (tipo === 'pendencia') {
            document.getElementById('pendencia-box').style.display = 'flex';
        }
    }

    function mostrarArquivo(tipo) {
        // Mostra apenas o que foi selecionado
        if (tipo === 'despacho') {
            document.getElementById('arquivo').style.display = 'flex';
        } else if (tipo === 'certidao') {
            document.getElementById('arquivo').style.display = 'flex';
        }
    }

    //Teste de Elaboração de Despacho

    function showBtn(documento){
        // Esconde todos primeiro
        document.getElementById('despachoBtn').style.display = 'none';
        document.getElementById('certidaoBtn').style.display = 'none';

        // Mostra apenas o que foi selecionado
        if (documento === 'despacho') {
            // document.getElementById('despachoBtn').style.display = 'flex';
        } else if (documento === 'certidao') {
            document.getElementById('certidaoBtn').style.display = 'flex';
        }
    }

    function criarDespacho() {
        const id = document.querySelector("input[name='id']").value;

        // Cria um form temporário para enviar os dados via POST
        const form = document.createElement("form");
        form.method = "POST";
        form.action = "elaborar_despacho.php";

        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "id";
        input.value = id;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }

    function criarCertidao() {
        const id = document.querySelector("input[name='id']").value;

        // Cria um form temporário para enviar os dados via POST
        const form = document.createElement("form");
        form.method = "POST";
        form.action = "elaborar_certidao.php";

        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "id";
        input.value = id;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }

    function agendarVistoria() {
        const id = document.querySelector("input[name='id']").value;

        // Cria um form temporário para enviar os dados via POST
        const form = document.createElement("form");
        form.method = "POST";
        form.action = "agendar_vistoria.php";

        const input = document.createElement("input");
        input.type = "hidden";
        input.name = "id";
        input.value = id;

        form.appendChild(input);
        document.body.appendChild(form);
        form.submit();
    }

    //Fim

  </script>

</body>
</html>



  
