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

        if ($_SESSION['categoria'] != 1 && $_SESSION['categoria'] != 2 && $_SESSION['categoria'] != 3) {
            echo "<div style='padding: 30px; font-family: sans-serif; text-align: center;'>
                    <h2>⚠️ Acesso Negado</h2>
                    <p>Você não tem acesso a esta funcionalidade.</p>
                    <a href='../index.php' style='color: blue; text-decoration: underline;'>Voltar para o início</a>
                </div>";
            exit;
        } 

        $sql = "SELECT pastas.*, assuntos.nome AS nome_assunto
        FROM pastas
        LEFT JOIN assuntos ON assuntos.id = pastas.assunto
        WHERE pastas.id = $_POST[id]";

        $result = mysqli_query($conexao, $sql);

        if(mysqli_num_rows($result) > 0) {

            while($dados = mysqli_fetch_assoc($result)){
              $cor = $dados['cor'];
              $nome = $dados['nome'];
              $nome_assunto = $dados['nome_assunto'];
              $letra = $dados['letra'];
              $numero = $dados['numero'];
              $assunto = $dados['assunto'];
              $status = $dados['status'];
            }

        }

        // echo "$sql";

  ?>

  <div class="content">

    <div class="form-model">

          <form id="cadastroForm">

          <ul class="list-title">
            <li>Edição de Pasta</li>
          </ul>

            <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">

            <div class="form-group">
              <label>Letra da Pasta</label>
              <input type="text" id="letra" name="letra" maxlength="2" style="text-transform: uppercase;" value="<?php echo "$letra";?>">
            </div>

            <div class="form-group">
              <label>Número da Pasta</label>
              <input type="number" id="numero" name="numero" value="<?php echo "$numero";?>">
            </div>

            <div class="form-group">
              <label>Assunto</label>
              <select id="assunto" name="assunto">
                <option value="<?php echo "$assunto";?>"><?php echo "$nome_assunto";?></option>
                <?php 
                    include('../api/conexao.php'); 

                    $sql = "SELECT *
                    FROM assuntos 
                    WHERE ativo = 1
                    ORDER BY nome";

                    $result = mysqli_query($conexao, $sql);

                    if(mysqli_num_rows($result) > 0) {

                        while($dados = mysqli_fetch_assoc($result)){
                            echo "<option value='$dados[id]'>$dados[nome]</option>";
                        }

                    }else{
                        echo "<option value=''>Não há assuntos cadastrados</option>";
                    }   
                ?>
              </select>
              
            </div>

            <!-- <div class="form-group">
              <label>Cor</label>
              <input type="text" id="cor" name="cor" placeholder="cor" value="<?php echo "$cor";?>" required>
            </div> -->

            <!-- Status, discordo disso aqui, mas pediram assim -->
            <div id="select-status" class="form-group" style="width: 100%;">
                <label for="status"><b>Status:</b></label>
                <select id="status" name="status">
                    <?php 
                        if($status != ''){
                            echo "<option value='$status'>$status</option>";
                        }else{
                            echo "<option value=''>Selecione Status</option>";
                        }
                    ?>
                    <!-- <option value="">Selecione Status</option> -->
                    <option value="<?php echo "$status"; ?>"><?php echo "$status"; ?></option>

                    <option value="Decisão Exarada">Decisão Exarada</option>
                    <option value="Sem Decisão">Sem Decisão</option>
                    <option value="com Relatório de Visita">com Relatório de Visita</option>
                    <option value="Aguardando Vistoria">Aguardando Vistoria</option>
                    <option value="Sem Análise do DRI">Sem Análise do DRI</option>
                    <option value="Com Análise do DRI">Com Análise do DRI</option>
                    <option value="Aguardando Contribuinte">Aguardando Contribuinte</option>

                    <option value="Sob Análise">Sob Análise</option>
                    <option value="Despacho Elaborado">Despacho Elaborado</option>
                    <option value="Certidão Elaborada">Certidão Elaborada</option>
                    <option value="Pendência">Pendência</option>
                    <option value="Encaminhado">Encaminhado</option>
                    <option value="Finalizado sem Arquivar">Finalizado sem Arquivar</option>
                    <option value="Finalizado  e Arquivado">Finalizado e Arquivado</option>
                    <option value="Finalizado e Arquivado sem Digitalização">Finalizado e Arquivado sem Digitalização</option>
                    <option value="Finalizado, Digitalizado e Arquivado">Finalizado, Digitalizado e Arquivado</option>
                </select>
            </div>
            <!-- Fim de status -->

            <div class="form-group">
              <label>Nome do Pasta</label>
              <input type="text" id="nome" name="nome" placeholder="Nome"  value="<?php echo "$nome";?>" required>
            </div>

            <div class="form-group button">
              <button class="form-btn blue-btn" type="submit">Salvar</button>
            </div>

          </form>

    </div>

  </div>

  <script>
    document.addEventListener('DOMContentLoaded', function () {
        const letra   = document.getElementById('letra');
        const numero  = document.getElementById('numero');
        const assunto = document.getElementById('assunto');
        const status  = document.getElementById('status');
        const nome    = document.getElementById('nome');

        function atualizarNome() {
            const txtLetra   = letra.value.trim().toUpperCase();
            const txtNumero  = numero.value.trim();
            const txtAssunto = assunto.options[assunto.selectedIndex]?.text || '';
            const txtStatus  = status.value;

            // Monta: Caixa + Letra + Número + Assunto + Status
            // Ajuste o separador conforme preferir
            nome.value = `Caixa ${txtLetra}${txtNumero ? '-' + txtNumero : ''} - ${txtAssunto} - ${txtStatus}`;
        }

        // Aciona a atualização quando qualquer campo mudar
        [letra, numero, assunto, status].forEach(el => {
            el.addEventListener('input', atualizarNome);
            el.addEventListener('change', atualizarNome);
        });
    });
    
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



  
