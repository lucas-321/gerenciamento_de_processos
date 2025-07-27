<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Dados do Processo</title>

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
        include('utils/history_list.php');
      ?>
    </div>
  </div>

</body>
</html>



  
