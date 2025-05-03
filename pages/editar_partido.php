<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ./index.php");
    exit;
}
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

        $sql = "SELECT *
        FROM partidos
        WHERE id = $_POST[id]";

        $result = mysqli_query($conexao, $sql);

        if(mysqli_num_rows($result) > 0) {

            while($dados = mysqli_fetch_assoc($result)){
              $sigla = $dados['sigla'];
              $nome = $dados['nome'];
              $numero = $dados['numero'];
              $foto = $dados['logo'];
            }

        }

  ?>

  <div class="content">

    <div class="form-model">

          <form id="cadastroForm" enctype="multipart/form-data">

            <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">

            <div class="form-group">
              <label>Sigla</label>
              <input type="text" id="sigla" name="sigla" placeholder="Sigla" value="<?php echo "$sigla";?>" required>
            </div>

            <div class="form-group">
              <label>Nome</label>
              <input type="text" id="nome" name="nome" placeholder="Nome" value="<?php echo "$nome";?>" required>
            </div>

            <div class="form-group">
              <label>Legenda</label>
              <input type="number" id="numero" name="numero" placeholder="Número" value="<?php echo "$numero";?>" required>
            </div>

            <div class="form-group">
              <div class='logo-partido'>
                  <img src='../fotos_partido/<?php echo "$foto"; ?>' alt='img-partido'>
              </div>
              <label for="picture">Logotipo</label>
              <input type="file" name="foto" id="foto">
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
      fetch("../api/editar_partido.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => alert(data.mensagem));
    });
  </script>

</body>
</html>



  
