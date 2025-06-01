<?php
// session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Criação de Despacho</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/modal.css">
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

    fieldset {
      margin: .625rem;
      border-radius: 5px;
    }
  </style>
</head>
<body>

  <?php 
    include('header.php');
  ?>

  <div class="content">

    <div class="form-model">

      <?php 
        include('utils/process_data.php');
      ?>

      <form id="despachoForm">

        <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">
        
        <div class="form-group">
            <label for="tipo">Tipo de Documento:</label>
            <select name="tipo" id="tipo"  onchange="exibeCampos()" required>
                <option value="" disabled selected>Escolha o tipo de documento</option>
                <option value="simples">Simples</option>
                <option value="transferencia_titularidade">Transferência de Titularidade</option>
                <option value="revisao_area">Revisão de Área</option>
            </select>
        </div>

        <div class="form-group">
          <label for="nome_proprietario">Nome do Proprietário</label>
          <input type="text" id="nome_proprietario" name="nome_proprietario" placeholder="Nome do Proprietário">
        </div>

        <div class="form-group">
          <label for="cpf_cnpj">CPF ou CNPJ do Proprietário</label>
          <input type="text" id="cpf_cnpj" name="cpf_cnpj" placeholder="CPF ou CNPJ"  oninput="mascaraCpfCnpj(this)" maxlength="18">
        </div>

        <!-- Teste -->

        <div class="form-group">
          <label for="endereco_cadastrado">Endereço Cadastrado:</label>
          <textarea name="endereco_cadastrado" id="endereco_cadastrado" rows="3" required></textarea>
        </div>   

        <fieldset>
          <legend>Transferência de Titularidade</legend>

          <div class="form-group campo_oculto" id="transferencia_titularidade">
              <label for="trecho_documento">Trecho do documento</label>
              <textarea name="trecho_documento" id="trecho_documento"></textarea>
          </div>
        </fieldset>

        <fieldset>

          <legend>Relatório de Visita</legend>

          <div class="form-group campo_oculto" id="dados_visita">
              <label for="data_visita">Data da visita:</label>
              <input type="date" name="data_visita" id="data_visita" required>

              <label for="nome_fiscal">Nome do Fiscal:</label>
              <input type="text" name="nome_fiscal" id="nome_fiscal" required>

              <label for="matriculafiscal">Nome do Fiscal:</label>
              <input type="text" name="matricula_fiscal" id="matricula_fiscal" required>

              <label for="relatorio_visita">Relatório da Visita</label>
              <textarea name="relatorio_visita" id="relatorio_visita"></textarea>
          </div>

        </fieldset>

        <div class="form-group">
          <label for="destaque">Observações</label>
          <textarea name="destaque" id="destaque" rows="3" required></textarea>
        </div>

        <div class="form-group">
          <label for="setor">Encaminhar para:</label>
          <select name="setor" id="setor" required>
              <option value="" disabled selected>Escolha o destino do documento</option>
              <option value="gabinete_sefaz">Gabinete/SEFAZ</option>
              <option value="proju">PROJU</option>
              <option value="proju">PROGER</option>
          </select>
        </div>

        <div class="form-group">
          <label for="coordenador">Coordenador</label>

          <select id="coordenador" name="coordenador" required>
            <option value=""></option>
            <?php 
                include('../api/conexao.php'); 

                $sql = "SELECT nome, agentes.id AS a_id 
                FROM agentes
                INNER JOIN usuarios ON agente_id = agentes.id
                WHERE usuarios.categoria = 2
                AND agentes.ativo = 1
                ORDER BY nome";

                $result = mysqli_query($conexao, $sql);

                if(mysqli_num_rows($result) > 0) {

                    while($dados = mysqli_fetch_assoc($result)){
                        echo "<option value='$dados[a_id]'>$dados[nome]</option>";
                    }

                }else{
                    echo "<option value=''>Não há assuntos cadastrados</option>";
                }   

            ?>
          </select>

        </div>
            
        <!-- Fim -->

        <div class="form-group button">
            <button class="form-btn blue-btn" type="submit">Salvar</button>
        </div>

      </form>

    </div>

  </div>

  <script>
    document.getElementById("cadastroForm").addEventListener("submit", function(e) {
      e.preventDefault();

      const form = document.getElementById("cadastroForm");
      const formData = new FormData(form);
      fetch("../api/criar_despacho.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => alert(data.mensagem))
      .then(window.location.href ="lista_processos.php");
    });

    function exibeCampos() {
        let campos = document.querySelectorAll('.campo_oculto');
        let tipoCertidao = document.getElementById('tipo').value;

        campos.forEach(campo => {
            campo.style.display = 'none';
        });

        if(tipoCertidao == 'simples'){
            document.getElementById('simples').style = 'display: flex';
            document.getElementById('simples-btn').style = 'display: flex';
        }else if(tipoCertidao == 'transferencia_titularidade'){
            document.getElementById('transferencia_titularidade').style = 'display: flex';
        }else if(tipoCertidao == 'revisao_area'){
            document.getElementById('dados_visita').style = 'display: flex';
        }else if(tipoCertidao == 'comp_endereco'){
            document.getElementById('comprovacao_endereco').style = 'display: flex';
        }
    }
    
  </script>
  <script src="../js/modal.js"></script>

</body>
</html>



  
