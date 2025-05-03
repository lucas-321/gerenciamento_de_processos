<?php
// session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Usuário</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>

  <?php include('header.php'); 
  if (!isset($_SESSION['usuario_id'])) {
      header("Location: ../index.php");
      exit;
  }
  
  ?>

  <div class="content">

    <div class="form-model">

          <form id="cadastroForm" enctype="multipart/form-data">

            <div class="form-group">
              <label>Nome</label>
              <input type="text" id="nome" name="nome" placeholder="Nome" required>
            </div>

            <div class="form-group">

              <label for="sexo">Sexo</label>
              <select id="sexo" name="sexo">
                <option value=""></option>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
              </select>

            </div>

            <div class="form-group">
              <label for="cpf">CPF</label>
              <input type="text" id="cpf" name="cpf" placeholder="CPF" oninput="mascaraCpfCnpj(this)" required>
            </div>

            <div class="form-group">
              <label for="data_nascimento">Data de Nascimento</label>
              <input type="date" id="data_nascimento" name="data_nascimento" required>
            </div>

            <div class="form-group">
              <label for="picture">Foto de Perfil</label>
              <input type="file" name="foto" id="foto">
            </div>

            <div class="form-group">
              <label for="categoria">Categoria</label>
              <select id="categoria" name="categoria">
                <option value="4">Analista</option>
                <option value="2">Coordenador</option>
                <option value="3">Protocolo</option>
                <option value="4">Analista</option>
                <option value="5">Externo</option>
              </select>
            </div>

            <div class="form-group">
              <label for="login">Login</label>
              <input type="text" id="login" name="login" placeholder="Login" required>
            </div>

            <div class="form-group">
              <label for="senha">Senha</label>
              <input type="password" id="senha" name="senha" placeholder="Senha" required>
            </div>

            <div class="form-group">
              <label for="confirma_senha">Confirme a Senha</label>
              <input type="password" id="confirma_senha" name="confirma_senha" placeholder="Confirme sua senha" required>
            </div>

            <div class="form-group button">
              <button class="form-btn red-btn" type="submit">Cadastrar</button>
            </div>

          </form>

    </div>

  </div>

  <script>
    document.getElementById("cadastroForm").addEventListener("submit", function(e) {
      e.preventDefault();

      const form = document.getElementById("cadastroForm");
      const formData = new FormData(form);

      const senha = document.querySelector('#senha').value;
      const confirmaSenha = document.querySelector("#confirma_senha").value;

      if(senha == confirmaSenha){
        fetch("../api/criar_usuario.php", {
          method: "POST",
          body: formData
        })
        .then(res => res.json())
        .then(data => alert(data.mensagem))
        .then(window.location.href ="lista_usuarios.php");
      }else{
        alert('A senha e a confirmação da senha não coincidem, por favor digite novamente!');
        confirmaSenha = '';
      }
    });
  </script>
  <script src="../js/masks.js"></script>

</body>
</html>



  
