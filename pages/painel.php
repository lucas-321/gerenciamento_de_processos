<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Painel</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/lists.css">
  <link rel="stylesheet" href="../css/pagination.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>

    <?php include('header.php'); 
        include('../api/conexao.php');

    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ./index.php");
        exit;
    }

    ?>

    <div class="content list-box">

        <?php

            $referencia = 'geral';

            if($_SESSION['categoria'] == 1 || $_SESSION['categoria'] == 2) {
                include('utils/users_list.php');
            }else if($_SESSION['categoria'] == 3){
                include('utils/process_list.php');
            }else if($_SESSION['categoria'] == 4){
                $referencia = 'painel';
                include('utils/my_process_list.php');
            }
        ?>
    
    </div>

</body>

<script>

    // const excluir = document.querySelector("#excluir");
    const texto = 'Você tem certeza que deseja excluir permanentemente este usuário?';

    function deletar(botao) {
        if (confirm(texto)) {
            const form = botao.closest("form");
            const formData = new FormData(form);

            fetch("../api/excluir_usuario.php", {
                method: "POST",
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                alert(data.mensagem);
                location.reload();
            });
        }
    }

</script>
</html>