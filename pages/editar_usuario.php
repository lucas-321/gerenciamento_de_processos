<?php
// session_start();
?>

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
        include("../api/funcoes.php"); // para nomeCategoria() e registrarAtividade()

        if (!isset($_SESSION['usuario_id'])) {
          header("Location: ../index.php");
          exit;
        }

        if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
            header("Location: ../index.php");
            exit;
        }

        $sql = "SELECT agentes.nome AS a_nome, usuarios.id AS u_id, matricula, sexo, cpf, data_nascimento, categoria, login
        FROM agentes
        INNER JOIN usuarios ON agentes.id = agente_id
        WHERE agentes.id = $_POST[id]";

        $result = mysqli_query($conexao, $sql);

        if(mysqli_num_rows($result) > 0) {

            while($dados = mysqli_fetch_assoc($result)){
              $nome = $dados['a_nome'];
              $matricula = $dados['matricula'];
              $sexo = $dados['sexo'];
              $cpf = $dados['cpf'];
              $data_nascimento = $dados['data_nascimento'];
              $categoria = $dados['categoria'];
              $login = $dados['login'];
              $id_usuario = $dados['u_id'];

              $n_categoria = nomeCategoria($categoria);

              // switch ($categoria) {
              //     case 1:
              //         $n_categoria = "Administrador";
              //         break;
              //     case 2:
              //         $n_categoria = "Coordenador";
              //         break;
              //     case 3:
              //         $n_categoria = "Protocolo";
              //         break;
              //     case 4:
              //         $n_categoria = "Analista";
              //         break;
              //     case 5:
              //         $n_categoria = "Externo";
              //         break;
              // }
            }

        }

  ?>

  <div class="content">

    <div class="form-model">

          <ul class="list-title">
            <li>Edição de Usuário</li>
          </ul>

          <form id="cadastroForm" enctype="multipart/form-data">

            <input type="hidden" id="usuario_id" name="id" value="<?php echo $_POST['id']; ?>">

            <div class="form-group">
              <label>Nome</label>
              <input type="text" id="nome" name="nome" placeholder="Nome" value="<?php echo "$nome";?>" required>
            </div>

            <div class="form-group">
              <label>Matrícula</label>
              <input type="text" id="matricula" name="matricula" placeholder="Matrícula" value="<?php echo "$matricula";?>" required>
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
                <option value="2">Coordenador</option>
                <option value="3">Protocolo</option>
                <option value="4">Analista</option>
                <option value="5">Externo</option>
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
    const loginInput = document.getElementById("login");
    const usuarioId = <?php echo $id_usuario; ?>; // agora vai funcionar

    loginInput.addEventListener("blur", () => {
      const login = loginInput.value.trim();
      if (login !== "") {
        fetch(`../api/verifica_login.php?login=${encodeURIComponent(login)}&id=${usuarioId}`)
          .then(res => res.json())
          .then(data => {
            if (!data.disponivel) {
              alert(data.mensagem);
              loginInput.value = "";
              loginInput.style.borderColor = "red";
            } else {
              loginInput.style.borderColor = "green";
            }
          });
      }
    });

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
        .then(window.location.href ="lista_usuarios.php");
      }else{
        alert('A senha e a confirmação da senha não coincidem, por favor digite novamente!');
        confirmaSenha = '';
      }
      
    });
  </script>

</body>
</html>



  
