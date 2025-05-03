<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Processo</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/modal.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>

  <?php include('header.php'); 
        if (!isset($_SESSION['usuario_id'])) {
          header("Location: ./index.php");
          exit;
        }
  ?>

  <div class="content">

    <div class="form-model">
        
          <ul class="list-title">
              <li>Cadastro de Setor</li>
          </ul>

          <form id="cadastroForm" enctype="multipart/form-data">

            <div class="form-group">
              <label>Sigla do Setor</label>
              <input type="text" id="sigla" name="sigla" placeholder="Sigla" required>
            </div>

            <div class="form-group">
              <label>Nome do Setor</label>
              <input type="text" id="nome" name="nome" placeholder="Nome" required>
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
      fetch("../api/criar_setor.php", {
        method: "POST",
        body: formData
      })
      .then(res => res.json())
      .then(data => alert(data.mensagem))
      .then(window.location.href ="lista_setores.php");
    });

    function adicionarInscricao() {
    const container = document.getElementById('inscricoes-container');

    const novaInscricao = document.createElement('div');
    novaInscricao.classList.add('flex-row');

    novaInscricao.innerHTML = `
        <input type="text" name="inscricao[]" placeholder="Nº de Inscrição" oninput="mascaraInscricao(this)" maxlength="18">
        <button type="button"  class="form-btn blue-btn" onclick="removerInscricao(this)">Remover</button>
    `;

    container.appendChild(novaInscricao);
  }

  function removerInscricao(botao) {
    const formGroup = botao.parentElement;
    formGroup.remove();
  }

  </script>
  <script src="../js/masks.js"></script>

</body>
</html>



  
