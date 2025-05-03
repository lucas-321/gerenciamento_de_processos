<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Edição de Pasta</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>

  <?php include('header.php'); 
        include('../api/conexao.php');
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ./index.php");
            exit;
        }

        $sql = "SELECT *
        FROM pastas
        WHERE id = $_POST[id]";

        $result = mysqli_query($conexao, $sql);

        if(mysqli_num_rows($result) > 0) {

            while($dados = mysqli_fetch_assoc($result)){
              $cor = $dados['cor'];
              $nome = $dados['nome'];
            }

        }

  ?>

  <div class="content">

    <div class="form-model">

          <form id="cadastroForm">

          <ul class="list-title">
            <li>Edição de Pasta</li>
          </ul>

            <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">

            <div class="form-group">
              <label>Nome</label>
              <input type="text" id="nome" name="nome" placeholder="Nome" value="<?php echo "$nome";?>" required>
            </div>

            <div class="form-group">
              <label>Cor</label>
              <input type="text" id="cor" name="cor" placeholder="cor" value="<?php echo "$cor";?>" required>
            </div>

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
      fetch("../api/editar_pasta.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => alert(data.mensagem))
      .then(window.location.href ="lista_pastas.php");
    });
  </script>

</body>
</html>



  
