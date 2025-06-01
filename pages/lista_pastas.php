<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Pastas</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/lists.css">
  <link rel="stylesheet" href="../css/modal.css">
  <link rel="stylesheet" href="../css/pagination.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>

  <?php include('header.php');
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ./index.php");
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
        
        include('../api/conexao.php');
  ?>

  <div class="content list-box">

    <?php
        include('utils/file_folder_list.php');
    ?>

  </div>

</body>
</html>
