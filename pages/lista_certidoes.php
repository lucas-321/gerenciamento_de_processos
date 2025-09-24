<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Certidões</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/lists.css">
  <link rel="stylesheet" href="../css/pagination.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>

  <?php include('header.php');
        if (!isset($_SESSION['usuario_id'])) {
            header("Location: ./index.php");
            exit;
        }
        include('../api/conexao.php');

        $referencia = 'geral';
  ?>

  <div class="content list-box">

    <?php
        include('utils/certificate_list.php');
    ?>

  </div>
</body>
</html>
