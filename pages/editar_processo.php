<?php
// session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Edição de Sessão</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/modal.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>

  <?php include('header.php'); 
        include('../api/conexao.php');

        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ./index.php");
            exit;
        }

        if ($_SESSION['categoria'] != 1 && $_SESSION['categoria'] != 2 && $_SESSION['categoria'] != 3) {
            echo "<div style='padding: 30px; font-family: sans-serif; text-align: center;'>
                    <h2>⚠️ Acesso Negado</h2>
                    <p>Você não tem acesso a esta funcionalidade.</p>
                    <a href='../index.php' style='color: blue; text-decoration: underline;'>Voltar para o início</a>
                </div>";
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
                $data_processo = $dados["data_processo"];
                $nome_assunto = $dados["n_assunto"];
                $assunto = $dados["assunto"];
                $inscricao = $dados["inscricao"];
                $nome_interessado = $dados["nome_interessado"];
                $cpf_cnpj = $dados["cpf_cnpj"];
                $email = $dados["email"];
                $telefone = $dados["telefone"];
                $observacoes = $dados["observacoes"];
            }

        }

  ?>

  <div class="content">

    <div class="form-model">

          <ul class="list-title">
              <li>Edição de Processo</li>
          </ul>

          <form id="cadastroForm">

            <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">
            <input type="hidden" name="nome_assunto" value="<?php echo $nome_assunto; ?>">

            <div class="form-group">
              <label>Nº Protocolo</label>
              <input type="text" id="n_protocolo" name="n_protocolo" placeholder="Nº de Protocolo" value="<?php echo "$n_protocolo";?>" required>
            </div>

            <div class="form-group">
              <label>Data do Processo</label>
              <input type="date" id="data_processo" name="data_processo" value="<?php echo "$data_processo";?>" required>
            </div>

            <div class="form-group">
              <label>Assunto</label>

              <div class="flex-row">
                  <select id="assunto" name="assunto">
                    <option value="<?php echo "$assunto";?>"><?php echo "$nome_assunto";?></option>
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

            <div class="form-group">
              <label for="inscricao">Inscrições</label>
              <input type="text" id="inscricao" name="inscricao[]" placeholder="inscricao" value="<?php echo "$inscricao";?>">
            </div>

            <div class="form-group">
              <label for="nome_interessado">Nome do Interessado</label>
              <input type="text" id="nome_interessado" name="nome_interessado" placeholder="nome_interessado" value="<?php echo "$nome_interessado";?>" required>
            </div>

            <div class="form-group">
              <label for="cpf_cnpj">CPF/CNPJ</label>
              <input type="text" id="cpf_cnpj" name="cpf_cnpj" placeholder="CPF CNPJ"
              oninput="mascaraCpfCnpj(this)"
              value="<?php echo "$cpf_cnpj";?>"
               maxlength="18">
            </div>

            <div class="form-group">
              <label for="email">E-mail</label>
              <input type="text" id="email" name="email" placeholder="example@email.com" value="<?php echo "$email";?>">
            </div>

            <div class="form-group">
              <label for="telefone">Telefone</label>
              <input 
              type="text" 
              id="telefone" 
              name="telefone"  
              placeholder="(xx) xxxx-xxxx" 
              oninput="mascaraTelefone(this)" 
              maxlength="15" 
              value="<?php echo "$telefone";?>">
            </div>

            <div class="form-group">
              <label for="observacoes">Observações</label>
              <textarea id="observacoes" name="observacoes" placeholder="Observações" required><?php echo "$observacoes";?></textarea>
            </div>

            <div class="form-group button">
              <button class="form-btn red-btn" type="submit">Salvar</button>
            </div>

          </form>

    </div>

  </div>

  <!-- O Modal -->
  <div id="myModal" class="modal">

    <!-- Modal Conteúdo -->
    <div class="modal-content">
        <span class="close">&times;</span>

        <div class="modal-title">
            <h3>Novo Assunto</h3>
        </div>

        <form id="assuntoForm">
            <div class="form-group">
                <label for="novo_assunto">Assunto:</label>
                <input type="text" id="novo_assunto" name="novo_assunto" required>
            </div>

            <div class="form-group">
                <input type="submit" value="Adicionar" class="form-btn green-btn">
            </div>
        </form>
    </div>

  </div>

  <script>
    document.getElementById("cadastroForm").addEventListener("submit", function(e) {
      e.preventDefault();

      const form = document.getElementById("cadastroForm");
      const formData = new FormData(form);
      fetch("../api/editar_processo.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => alert(data.mensagem))
      .then(window.location.href ="lista_processos.php");
    });



    document.getElementById("assuntoForm").addEventListener("submit", function(e) {
      e.preventDefault();

      const form = document.getElementById("assuntoForm");
      const formData = new FormData(form);
      fetch("../api/criar_assunto.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        alert(data.mensagem);
        atualizarAssuntos(data.novo_id);
      });
    });

    function atualizarAssuntos(selecionarId = null) {

      const assunto_input = document.getElementById("#novo_assunto");
      fetch("../api/listar_assuntos.php")
      .then(res => res.json())
      .then(assuntos => {
        // const select = document.getElementById("assunto");
        select.innerHTML = "<option value=''></option>"; // limpa o select

        assuntos.forEach(assunto => {
          const option = document.createElement("option");
          option.value = assunto.id;
          option.textContent = assunto.nome;

          if (selecionarId && assunto.id == selecionarId) {
            option.selected = true;
          }

          select.appendChild(option);
        });
      });
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



  
