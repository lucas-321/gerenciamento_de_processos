<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Edição de Certidão</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/modal.css">
  <link rel="stylesheet" href="../css/documentos.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

  <style>
    .order-info {
      display: flex;
      align-items: center;
      justify-content: space-between;
      margin: .625rem;
      padding-bottom: .125rem;
      border-bottom: 1px solid #ccc;
    }

    .order-info span {
      width: 50%;
    }

    #despachoForm {
      margin-top: 2rem;
    }

    .campo_oculto {
      display: none;
    }

    .campo_exposto {
      display: flex;
    }

    fieldset {
      margin: .625rem;
      border-radius: 5px;

      flex-direction: column;
    }

    /* Container do NicEdit */
    .editor {
      display: flex;
      flex-direction: column;
      padding: .775rem;
    }

    .form-row{
      display: flex;
      justify-content: space-between;
    }

  </style>

  <script type="text/javascript" src="https://js.nicedit.com/nicEdit-latest.js"></script>

</head>
<body>

  <?php 
    include('header.php');
  ?>

  <div class="content">

    <div class="form-model">

      <?php 
        include('utils/process_data.php');

        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ./index.php");
            exit;
        }   

        $sql = "SELECT certidoes.*,
            certidoes.cpf_cnpj AS cpf_cnpj_proprietario, 
            processos.*,
            agentes.nome AS n_agente
            FROM certidoes
            INNER JOIN processos ON processos.id = certidoes.processo
            INNER JOIN assuntos ON processos.assunto = assuntos.id
            INNER JOIN usuarios ON usuarios.id = certidoes.criado_por
            INNER JOIN agentes ON usuarios.agente_id = agentes.id
            WHERE certidoes.id = $_POST[id]";

        $result = mysqli_query($conexao, $sql);

        if(mysqli_num_rows($result) > 0) {

            while($dados = mysqli_fetch_assoc($result)){

                $tipo = $dados["tipo"];
                $data_certidao = $dados["data_certidao"];
                $nome_proprietario = $dados["nome_proprietario"];
                $cpf_cnpj_proprietario = $dados["cpf_cnpj_proprietario"];
                $endereco = $dados["endereco"];
                $numero_porta = $dados["numero_porta"];
                // $valor_venal = number_format($dados["valor_venal"], 2, ',', '.');
                $valor_venal = $dados["valor_venal"];
                $trecho_documento = $dados["trecho_documento"];
                $endereco_atual = $dados["endereco_atual"];

                $data_itiv = $dados["data_itiv"];
                $valor_itiv = $dados["valor_itiv"];
                $aliquota_itiv = $dados["aliquota_itiv"];
                $valor_transacao = $dados["valor_transacao"];
                $dam = $dados["numero_dam"];
                $nome_transmitente = $dados["nome_transmitente"];
                $cpf_cnpj_transmitente = $dados["cpf_cnpj_transmitente"];
                $nome_adquirente = $dados["nome_adquirente"];
                $cpf_cnpj_adquirente = $dados["cpf_cnpj_adquirente"];
                $data_pagamento_itiv = $dados["data_pagamento_itiv"];
                $descricao_metragem = $dados["descricao_metragem"];

                $informacoes_adicionais = $dados["informacoes_adicionais"];
                $agente = $dados["n_agente"];
              }

              $display_ce = 'campo_oculto';
              $display_cpi = 'campo_oculto';
              $display_im = 'campo_oculto';
              $display_ln = 'campo_oculto';
              $display_l = 'campo_oculto';
              $display_m = 'campo_oculto';
              $display_vv = 'campo_oculto';

              if($tipo == 'comprovacao_endereco'){
                $display_ce = 'campo_exposto';
                $option = "Comprovação de Endereço";
              }else if($tipo == 'comprovacao_pagamento_itiv'){
                $display_cpi = 'campo_exposto';
                $option = "Comprovação de Pagamento de ITIV";
              }else if($tipo == 'inexistencia_imoveis'){
                $display_im = 'campo_exposto';
                $option = "Inexistência de Imóveis";
              }else if($tipo == 'lancamento_numero'){
                $display_ln = 'campo_exposto';
                $option = "Lançamento de Número";
              }else if($tipo == 'lancamento'){
                $display_l = 'campo_exposto';
                $option = "Lançamento";
              }else if($tipo == 'metragem'){
                $display_m = 'campo_exposto';
                $option = "Metragem";
              }else if($tipo == 'valor_venal'){
                $display_vv = 'campo_exposto';
                $option = "Valor Venal";
              }

        }
      ?>

      <form id="certidaoForm">

        <span style="width: 100%; text-align: center; margin: 1rem; border-bottom: 1px solid #000;"><h3>Certidão</h3></span>

        <input type="hidden" name="certidao" value="<?php echo $_POST['id']; ?>">
        
        <div class="form-group">
            <label for="tipo">Tipo de Certidão:</label>
            <select name="tipo" id="tipo"  onchange="exibeCampos()" required>
                <option value="<?php echo "$tipo"; ?>" selected><?php echo $option; ?></option>
                <option value="comprovacao_endereco">Comprovação de Endereço</option>
                <option value="comprovacao_pagamento_itiv">Comprovação de Pagamento de ITIV</option>
                <option value="inexistencia_imoveis">Inexistência de Imóveis</option>
                <option value="lancamento_numero">Lançamento de Nº</option>
                <option value="lancamento">Lançamento de Imóvel</option>
                <option value="metragem">Metragem</option>
                <option value="valor_venal">Valor Venal</option>
            </select>
        </div>

        <div class="form-group">
          <label for="nome_proprietario">Nome do Proprietário</label>
          <input type="text" id="nome_proprietario" name="nome_proprietario" placeholder="Nome do Proprietário" value="<?php echo $nome_proprietario; ?>">
        </div>

        <div class="form-group">
          <label for="cpf_cnpj">CPF ou CNPJ do Proprietário</label>
          <input type="text" id="cpf_cnpj" name="cpf_cnpj" placeholder="CPF ou CNPJ"  oninput="mascaraCpfCnpj(this)" maxlength="18" value="<?php echo $cpf_cnpj_proprietario; ?>">
        </div>

        <div class="form-group">
          <label for="endereco">Endereço Cadastrado:</label>
          <div class="editor">
            <textarea name="endereco" id="endereco" rows="3"><?php echo htmlspecialchars(strip_tags($endereco), ENT_QUOTES, 'UTF-8'); ?></textarea>
          </div>
        </div>

        <fieldset class="<?php echo $display_ce; ?>" id="comprovacao_endereco">
          <legend><b>Comprovação de Endereço</b></legend>

          <div class="form-group">
              <label for="trecho_documento">Trecho do Documento:</label>
              <div class="editor">
                <textarea name="trecho_documento" id="trecho_documento"><?php echo $trecho_documento; ?></textarea>
              </div>
          </div>

          <div class="form-group">
              <label for="endereco_atual">Endereço Atual:</label>
              <div class="editor">
                <textarea name="endereco_atual" id="endereco_atual"><?php echo $endereco_atual; ?></textarea>
              </div>
          </div>

        </fieldset>

        <fieldset class="<?php echo $display_cpi; ?>" id="comprovacao_pagamento_itiv">
          <legend><b>Comprovação de Pagamento de ITIV</b></legend>

          <div class="form-row">
          
            <div class="form-group">
                <label for="data_lancamento_itiv">Data de Lançamento do ITIV:</label>
                <input type="date" name="data_lancamento_itiv" id="data_lancamento_itiv" value="<?php echo $data_itiv; ?>">
            </div>

            <div class="form-group">
                <label for="valor_itiv">Valor do ITIV:</label>
                <!-- <input type="number" name="valor_itiv" id="valor_itiv" step="0.01"  value="<?php echo $valor_itiv; ?>"> -->
                <input type="text" name="valor_itiv" id="valor_itiv" oninput="filtrarNumeros(this)" value="<?php echo $valor_itiv; ?>">
            </div>

          </div>

          <div class="form-row">

            <div class="form-group">
                <label for="aliquota">Alíquota:</label>
                <input type="number" name="aliquota" id="aliquota" step="0.01"  value="<?php echo $aliquota_itiv; ?>">
            </div>

            <div class="form-group">
                <label for="valor_transacao">Valor da Transação:</label>
                <!-- <input type="number" name="valor_transacao" id="valor_transacao" step="0.01" value="<?php echo $valor_transacao; ?>"> -->
                <input type="text" name="valor_transacao" id="valor_transacao" oninput="filtrarNumeros(this)" value="<?php echo $valor_transacao; ?>">
            </div>

          </div>

          <div class="form-row">

            <div class="form-group">
                <label for="dam">Nº do DAM:</label>
                <input type="number" name="dam" id="dam"  value="<?php echo $dam; ?>">
            </div>

            <div class="form-group">
                <label for="data_pagamento_itiv">Data de Pagamento do ITIV:</label>
                <input type="date" name="data_pagamento_itiv" id="data_pagamento_itiv"  value="<?php echo $data_pagamento_itiv; ?>">
            </div>

          </div>

          <div class="form-group">
              <label for="nome_transmitente">Nome do Transmitente:</label>
              <input type="text" name="nome_transmitente" id="nome_transmitente"  value="<?php echo $nome_transmitente; ?>">
          </div>

          <div class="form-group">
              <label for="cpf_cnpj_transmitente">CPF ou CNPJ do Transmitente:</label>
              <input 
              type="text" 
              name="cpf_cnpj_transmitente" 
              id="cpf_cnpj_transmitente"
              oninput="mascaraCpfCnpj(this)" 
              maxlength="18"
               value="<?php echo $cpf_cnpj_transmitente; ?>">
          </div>

          <div class="form-group">
              <label for="nome_adquirente">Nome do Adquirente:</label>
              <input type="text" name="nome_adquirente" id="nome_adquirente" value="<?php echo $nome_adquirente; ?>">
          </div>

          <div class="form-group">
              <label for="cpf_cnpj_adquirente">CPF ou CNPJ do Adquirente:</label>
              <input 
              type="text" 
              name="cpf_cnpj_adquirente" 
              id="cpf_cnpj_adquirente"
              oninput="mascaraCpfCnpj(this)" 
              maxlength="18"
              value="<?php echo $cpf_cnpj_adquirente; ?>">
          </div>

        </fieldset>

        <fieldset class="<?php echo $display_ln; ?>" id="numero_porta">
          <legend><b>Lançamento de nº</b></legend>

          <div class="form-group">
              <label for="numero">Nº da Porta:</label>
              <input type="number" name="numero" id="numero" value="<?php echo $numero_porta; ?>">
          </div>

        </fieldset>

        <fieldset class="<?php echo $display_m; ?>" id="metragem">
          <legend><b>Metragem</b></legend>

          <div class="form-group">
              <label for="descricao_metragem">Descrição da Metragem:</label>
              <div class="editor">
                <textarea name="descricao_metragem" id="descricao_metragem"><?php echo htmlspecialchars(strip_tags($descricao_metragem), ENT_QUOTES, 'UTF-8'); ?></textarea>
              </div>
          </div>

        </fieldset>

        <fieldset class="<?php echo $display_vv; ?>" id="valor_venal">
          <legend><b>Valor Venal</b></legend>

          <div class="form-group">
              <label for="valor_venda">Valor do Imóvel:</label>
              <!-- <input type="number" name="valor_venda" id="valor_venda" step="0.01" value="<?php echo htmlspecialchars($valor_venal); ?>"> -->
              <input type="text" name="valor_venda" id="valor_venda" oninput="filtrarNumeros(this)" value="<?php echo htmlspecialchars($valor_venal); ?>">
          </div>

        </fieldset>

        <div class="form-group">
          <label for="data_certidao">Data da Certidão: </label>
          <input type="date" name="data_certidao" id="data_certidao" value="<?php echo $data_certidao; ?>">
        </div>

        <div class="form-group">
          <label for="informacoes_adicionais">Informações Adicionais</label>
          <div class="editor">
            <textarea 
              name="informacoes_adicionais" 
              id="informacoes_adicionais" 
              rows="3"><?php echo htmlspecialchars(strip_tags($informacoes_adicionais), ENT_QUOTES, 'UTF-8'); ?></textarea>
          </div>
        </div>
            
        <!-- Fim -->

        <div class="form-group button">
            <button class="form-btn blue-btn" type="submit">Salvar</button>
        </div>

      </form>

    </div>

  </div>

  <script>
    document.getElementById("certidaoForm").addEventListener("submit", function(e) {
      e.preventDefault();

      // === SINCRONIZA TODAS AS INSTÂNCIAS nicEdit COM OS TEXTAREAS ===
      nicEditors.findEditor('endereco').saveContent();
      nicEditors.findEditor('trecho_documento')?.saveContent();
      nicEditors.findEditor('endereco_atual')?.saveContent();
      nicEditors.findEditor('informacoes_adicionais').saveContent();
      nicEditors.findEditor('descricao_metragem')?.saveContent();
      // Adicione aqui outras instâncias se criar mais

      // === TRATAMENTO DE CAMPOS MONETÁRIOS ===
      const camposMonetarios = ["valor_itiv", "valor_venda", "valor_transacao"]; 
      camposMonetarios.forEach(id => {
          const campo = document.getElementById(id);
          if (campo && campo.value) {
              campo.value = campo.value.replace(/\./g, '').replace(',', '.');
          }
      });
      // === FIM ===

      const formData = new FormData(this);
      fetch("../api/editar_certidao.php", {
        method: "POST",
        body: formData
      })
      .then(async res => {
        const text = await res.text();
        try {
        return JSON.parse(text);
        } catch {
        console.error("Resposta não é JSON:", text);
        throw new Error("Resposta do servidor inválida");
        }
      })
      .then(data => {
          alert(data.mensagem);
          window.location.href = "lista_certidoes.php";
      })
      .catch(err => alert("Erro: " + err.message));
    });

    function filtrarNumeros(campo) {
      campo.value = campo.value
        .replace(/[^0-9.,]/g, '')   // mantém apenas números, ponto e vírgula
        .replace(/(,.*),/g, '$1');  // impede mais de uma vírgula
    }

    let nicTrechoInit = false;

    function exibeCampos() {
      document.querySelectorAll('.campo_oculto').forEach(c => c.style.display = 'none');
      const tipo = document.getElementById('tipo').value;

      if (tipo === 'lancamento_numero') {
          document.getElementById('numero_porta').style.display = 'flex';

      } else if (tipo === 'valor_venal') {
          document.getElementById('valor_venal').style.display = 'flex';

      } else if (tipo === 'comprovacao_endereco') {
          document.getElementById('comprovacao_endereco').style.display = 'flex';

          if (!nicTrechoInit) {                    // só inicializa uma vez
              new nicEditor({fullPanel:true}).panelInstance('trecho_documento');
              new nicEditor({fullPanel:true}).panelInstance('endereco_atual');
              nicTrechoInit = true;
          }
      } else if (tipo === 'comprovacao_pagamento_itiv') {
          document.getElementById('comprovacao_pagamento_itiv').style.display = 'flex';
      } else if (tipo === 'metragem') {
          document.getElementById('metragem').style.display = 'flex';
          if (!nicTrechoInit) {                    // só inicializa uma vez
              new nicEditor({fullPanel:true}).panelInstance('descricao_metragem');
              nicTrechoInit = true;
          }
      }
    }

    bkLib.onDomLoaded(function () {
        new nicEditor({fullPanel:true}).panelInstance('informacoes_adicionais');
        new nicEditor({fullPanel:true}).panelInstance('endereco');
    });

  </script>
  <script src="../js/masks.js"></script>

</body>
</html>



  
