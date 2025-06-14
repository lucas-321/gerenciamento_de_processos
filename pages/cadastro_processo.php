<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Processo</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/modal.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>

  <?php include('header.php'); 
        if (!isset($_SESSION['usuario_id'])) {
          header("Location: ./index.php");
          exit;
        }
  ?>

  <div class="content">

    <div class="form-model">
        
          <ul class="list-title">
              <li>Cadastro de Processo</li>
          </ul>

          <form id="cadastroForm" enctype="multipart/form-data">

            <div class="form-group">
              <label>Nº de Protocolo</label>
              <input type="text" id="n_protocolo" name="n_protocolo" placeholder="Nº de Protocolo" required>
            </div>

            <div class="form-group">
              <label>Data do Processo</label>
              <input type="date" id="data_processo" name="data_processo" required>
            </div>

            <div class="form-group">
              <label>Assunto</label>

              <div class="flex-row">
                  <select id="assunto" name="assunto">
                    <option value=""></option>
                    <?php 
                        include('../api/conexao.php'); 

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

                  <button type="button" class="form-btn green-btn" id="btnNovoAssunto">Novo Assunto</button>
              </div>
              
            </div>

            <div class="form-group" id="inscricoes-container">
              <label>Nº de Inscrição</label>
              <div class="flex-row">
                <input type="text" name="inscricao[]" placeholder="Nº de Inscrição" oninput="mascaraInscricao(this)" maxlength="18">
                <button type="button"  class="form-btn green-btn" onclick="adicionarInscricao()">Adicionar</button>
              </div>
            </div>

            <div class="form-group">
              <label>Nome do Interessado</label>
              <input type="text" id="nome_interessado" name="nome_interessado" placeholder="Nome do Interessado" required>
            </div>

            <div class="form-group">
              <label for="cpf">CPF</label>
              <input type="text" id="cpf" name="cpf" placeholder="CPF"  oninput="mascaraCpfCnpj(this)" maxlength="18" required>
            </div>

            <div class="form-group">
              <label for="email">E-mail</label>
              <input type="email" id="email" name="email" placeholder="example@mail.com">
            </div>

            <div class="form-group">
              <label for="telefone">Telefone</label>
              <input type="tel" name="telefone" placeholder="(xx) xxxx-xxxx" oninput="mascaraTelefone(this)" maxlength="15">
            </div>

            <div class="form-group">
              <label for="observacoes">Observações</label>
              <textarea name="observacoes" id="observacoes"></textarea>
            </div>

            <!-- <div class="form-group">
              <label for="picture">Logotipo</label>
              <input type="file" name="foto" id="foto">
            </div> -->

            <div class="form-group button">
              <button class="form-btn red-btn" type="submit">Cadastrar</button>
            </div>

          </form>

    </div>

  </div>

  <?php include('utils/subject_modal.php'); ?>

  <script>
    document.getElementById("cadastroForm").addEventListener("submit", function(e) {
      e.preventDefault();

      const form = document.getElementById("cadastroForm");
      const formData = new FormData(form);
      fetch("../api/criar_processo.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => alert(data.mensagem))
      .then(window.location.href ="lista_processos.php");
    });

    function adicionarInscricao() {
    const container = document.getElementById('inscricoes-container');

    const novaInscricao = document.createElement('div');
    novaInscricao.classList.add('flex-row');

    novaInscricao.innerHTML = `
        <input type="text" name="inscricao[]" placeholder="Nº de Inscrição" oninput="mascaraInscricao(this)" maxlength="18">
        <button type="button"  class="form-btn red-btn" onclick="removerInscricao(this)">Remover</button>
    `;

    container.appendChild(novaInscricao);
  }

  function removerInscricao(botao) {
    const formGroup = botao.parentElement;
    formGroup.remove();
  }

  //Verificar Duplicidade de Processos
  const nProtocoloInput = document.getElementById("n_protocolo");
  const dataProcessoInput = document.getElementById("data_processo");
  const submitButton = document.querySelector("button[type='submit']");

  function verificarDuplicidade() {
    const n_protocolo = nProtocoloInput.value.trim();
    const data_processo = dataProcessoInput.value;

    // Só verifica se os dois campos estiverem preenchidos
    if (n_protocolo && data_processo) {
      fetch("../api/verifica_processo.php", {
        method: "POST",
        body: new URLSearchParams({ n_protocolo, data_processo })
      })
      .then(res => res.json())
      .then(data => {
        if (data.existe) {
          alert("Já existe um processo com este número de protocolo e data.");
          dataProcessoInput.value = "";
          submitButton.disabled = true; // Impede envio
        } else {
          submitButton.disabled = false; // Permite envio
        }
      })
      .catch(err => {
        console.error("Erro na verificação:", err);
        submitButton.disabled = false;
      });
    }
  }

  // Aciona quando sair do campo (ou pode usar 'input' se quiser mais instantâneo)
  nProtocoloInput.addEventListener("blur", verificarDuplicidade);
  dataProcessoInput.addEventListener("blur", verificarDuplicidade);
  //Fim
  </script>
  <script src="../js/modal.js"></script>
  <script src="../js/masks.js"></script>

</body>
</html>



  
