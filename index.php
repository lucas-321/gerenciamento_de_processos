<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Login</title>

  <link rel="stylesheet" href="css/style.css">
  <link rel="stylesheet" href="css/header.css">
  <link rel="stylesheet" href="css/forms.css">
  <link rel="stylesheet" href="css/lists.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

</head>
<body>

  <nav>
      <div class="logo">
          <img src="img/logo-no-bg.png" alt="img">
          <span>Projeto Preliminar</span>
      </div>
  </nav>

  <div class="content login-box">

    <div class="form-model">
        <ul class="list-title">
          <li>Login</li>
        </ul>

        <form id="loginForm">

          <div class="form-group">
              <label>Usuário</label>
              <input type="text" id="login" placeholder="Seu usuário" required>
          </div>

          <div class="form-group">
              <label>Senha</label>
              <input type="password" id="senha" placeholder="Senha" required>
          </div>

          <div class="form-group button">
            <button type="submit" class="form-btn blue-btn">Acessar</button>
          </div>

        </form>

    </div>

  </div>

  <script>
    document.getElementById("loginForm").addEventListener("submit", function(e) {
      e.preventDefault();

      fetch("api/autenticar.php", {
        method: "POST",
        headers: { "Content-Type": "application/json" },
        body: JSON.stringify({
          login: document.getElementById("login").value,
          senha: document.getElementById("senha").value
        })
      })
      .then(res => res.json())
      .then(data => {
        if (data.sucesso) {
          window.location.href = "pages/painel.php";
        } else {
          alert(data.mensagem);
        }
      });
    });
  </script>
</body>
</html>