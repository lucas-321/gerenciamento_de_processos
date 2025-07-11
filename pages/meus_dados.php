<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Edição de Usuário</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>

  <?php include('header.php'); 
        include('../api/conexao.php');

        if (!isset($_SESSION['usuario_id'])) {
          header("Location: ../index.php");
          exit;
        }

        if ($_SESSION['categoria'] != 1 && $_SESSION['categoria'] != 2) {
            echo "<div style='padding: 30px; font-family: sans-serif; text-align: center;'>
                    <h2>⚠️ Acesso Negado</h2>
                    <p>Você não tem acesso a esta funcionalidade.</p>
                    <a href='../index.php' style='color: blue; text-decoration: underline;'>Voltar para o início</a>
                </div>";
            exit;
        }

        $sql = "SELECT agentes.nome AS a_nome, sexo, cpf, data_nascimento, categoria, login
        FROM agentes
        INNER JOIN usuarios ON agentes.id = agente_id
        WHERE agentes.id = $_SESSION[agente_id]";

        $result = mysqli_query($conexao, $sql);

        if(mysqli_num_rows($result) > 0) {

            while($dados = mysqli_fetch_assoc($result)){
              $nome = $dados['a_nome'];
              $sexo = $dados['sexo'];
              $cpf = $dados['cpf'];
              $data_nascimento = $dados['data_nascimento'];
              $categoria = $dados['categoria'];
              $login = $dados['login'];

              switch ($categoria) {
                  case 1:
                      $n_categoria = "Administrador";
                      break;
                  case 2:
                      $n_categoria = "Coordenador";
                      break;
                  case 3:
                      $n_categoria = "Protocolo";
                      break;
                  case 4:
                      $n_categoria = "Analista";
                      break;
                  case 5:
                      $n_categoria = "Externo";
                      break;
              }
            }

        }

  ?>

  <div class="content">

    <div class="form-model">

          <ul class="list-title">
            <li>Edição de Usuário</li>
          </ul>

          <form id="cadastroForm" enctype="multipart/form-data">

            <input type="hidden" name="id" value="<?php echo $_SESSION['usuario_id']; ?>">

            <div class="form-group">
              <label>Nome</label>
              <input type="text" id="nome" name="nome" placeholder="Nome" value="<?php echo "$nome";?>" required>
            </div>

            <div class="form-group">

              <label for="sexo">Sexo</label>
              <select id="sexo" name="sexo">
                <option value="<?php echo "$sexo";?>"><?php echo "$sexo";?></option>
                <option value="Masculino">Masculino</option>
                <option value="Feminino">Feminino</option>
              </select>

            </div>

            <div class="form-group">
              <label for="cpf">CPF</label>
              <input type="text" id="cpf" name="cpf" placeholder="CPF" value="<?php echo "$cpf";?>" required>
            </div>

            <div class="form-group">
              <label for="data_nascimento">Data de Nascimento</label>
              <input type="date" id="data_nascimento" name="data_nascimento" value="<?php echo "$data_nascimento";?>" required>
            </div>

            <div class="form-group">
              <label for="picture">Foto de Perfil</label>
              <input type="file" name="foto" id="foto">
            </div>

            <div class="form-group">
              <label for="categoria">Categoria</label>
              <select id="categoria" name="categoria" onchange="verificaCategoria()">
                <option value="<?php echo "$categoria";?>"><?php echo "$n_categoria";?></option>
                <option value="1">Administrador</option>
                <option value="2">Presidente</option>
                <option value="3">Vereador</option>
                <option value="4">Usuário Comum</option>
              </select>
            </div>

            <div class="form-group">
              <label for="login">Login</label>
              <input type="text" id="login" name="login" placeholder="Login" value="<?php echo "$login";?>" required>
            </div>

            <div class="form-group">
              <label for="senha">Senha</label>
              <input type="password" id="senha" name="senha" placeholder="Senha">
            </div>

            <div class="form-group">
              <label for="confirma_senha">Confirme a Senha</label>
              <input type="password" id="confirma_senha" name="confirma_senha" placeholder="Confirme sua senha">
            </div>

            <div class="form-group button">
              <button class="form-btn red-btn" type="submit">Salvar</button>
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
        fetch("../api/editar_usuario.php", {
          method: "POST",
          body: formData
        })
        .then(res => res.json())
        .then(data => alert(data.mensagem))
        .then(window.location.href ="painel.php");
      }else{
        alert('A senha e a confirmação da senha não coincidem, por favor digite novamente!');
        confirmaSenha = '';
      }
      
    });
  </script>

</body>
</html>



  
