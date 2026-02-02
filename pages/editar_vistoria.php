<?php
// session_start();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Agendamento de Vistoria</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/modal.css">
  <link rel="stylesheet" href="../css/documentos.css">
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

    .campo_exposto {
      display: flex;
    }

    fieldset {
      margin: .625rem;
      border-radius: 5px;

      flex-direction: column;
    }

    /* Container do NicEdit */
    .editor {
      display: flex;
      flex-direction: column;
      padding: .775rem;
    }

    .form-row{
      display: flex;
      justify-content: space-between;
    }
  </style>

  <script type="text/javascript" src="https://js.nicedit.com/nicEdit-latest.js"></script>

</head>
<body>

  <?php 
    include('header.php');
  ?>

  <div class="content">

    <div class="form-model">

      <?php 
        include('utils/process_data.php');
        include('utils/survey_data.php');
      ?>

      <form id="certidaoForm">

        <span style="width: 100%; text-align: center; margin: 1rem; border-bottom: 1px solid #000;"><h3>Editar Visita</h3></span>

        <input type="hidden" name="processo" value="<?php echo $_POST['id']; ?>">
        <input type="hidden" name="id" value="<?php echo $id_vistoria; ?>">

        <div class="form-group">
          <label for="data_visita">Informe a Data:</label>
          <input type="date" name="data_visita" id="data_visita" value="<?php echo "$data_vistoria"; ?>"></input>
        </div>

        

        <div 
            id="select-usuario" 
            class="form-group" 
        >
            <label for="usuario"><b>Escolha o Fiscal Responsável:</b></label>
            <input 
              type="text" 
              id="buscaUsuario"
              value="<?php echo "$nome_fiscal"; ?>"
              <?php 
                echo "placeholder='Digite para buscar usuários...'";
              ?>
              onchange="removerValor()"
            >
            <input 
              type="hidden" 
              id="usuario"
              name="usuario"
              value="<?php
               echo "$id_fiscal";
              ?>"
              required
            >

            <ul id="resultados" style="border:1px solid #ccc; max-height:150px; overflow-y:auto; list-style:none;">
            </ul>
        </div>

        <div class="form-group">
          <label for="informacoes_adicionais">Informações Adicionais</label>
          <div class="editor"><textarea name="informacoes_adicionais" id="informacoes_adicionais" rows="3"><?php echo "$informacoes_adicionais"; ?></textarea></div>
        </div>

        <input type="hidden" name="data_processo" value="<?php echo $data_processo; ?>">
        <input type="hidden" name="n_protocolo" value="<?php echo $n_protocolo; ?>">
            
        <!-- Fim -->

        <div class="form-group button">
            <button class="form-btn blue-btn" type="submit">Salvar</button>
        </div>

      </form>

    </div>

  </div>

  <script>

    document.getElementById("certidaoForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const usuario = document.getElementById('usuario').value;

    if (!usuario) {
        alert("Selecione um fiscal responsável antes de salvar.");
        return;
    }

    // === SINCRONIZA TODAS AS INSTÂNCIAS nicEdit COM OS TEXTAREAS ===
    nicEditors.findEditor('informacoes_adicionais').saveContent();
    // Adicione aqui outras instâncias se criar mais

    limparEditorVazio('informacoes_adicionais');

    const formData = new FormData(this);

    fetch("../api/editar_vistoria.php", {
        method: "POST",
        body: formData
    })
    .then(async res => {
        const text = await res.text();
        try {
        return JSON.parse(text);
        } catch {
        console.error("Resposta não é JSON:", text);
        throw new Error("Resposta do servidor inválida");
        }
    })
    .then(data => {
        alert(data.mensagem);
        window.location.href = "lista_visitas.php";
    })
    .catch(err => alert("Erro: " + err.message));
    });

    function filtrarNumeros(campo) {
      campo.value = campo.value
        .replace(/[^0-9.,]/g, '')   // mantém apenas números, ponto e vírgula
        .replace(/(,.*),/g, '$1');  // impede mais de uma vírgula
    }

    let nicTrechoInit = false;

    bkLib.onDomLoaded(function () {
        new nicEditor({fullPanel:true}).panelInstance('informacoes_adicionais');
    });

    function limparEditorVazio(id) {
        const textarea = document.getElementById(id);
        if (!textarea) return;

        const conteudo = textarea.value
            .replace(/<br\s*\/?>/gi, '')
            .replace(/&nbsp;/gi, '')
            .trim();

        textarea.value = conteudo === '' ? '' : textarea.value;
    }



    //Busca de usuario por termo
    document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('buscaUsuario');
    const usuario = document.getElementById('usuario');
    const resultados = document.getElementById('resultados');
    let timeout;

    input.addEventListener('input', () => {
        clearTimeout(timeout);
        const termo = input.value.trim();
        if (termo.length < 1) {
        resultados.innerHTML = '';
        return;
        }

        timeout = setTimeout(() => {
        fetch(`utils/buscar_usuario.php?q=${encodeURIComponent(termo)}`)
            .then(resp => resp.json())
            .then(data => {
            resultados.innerHTML = '';
            if (!data.length) {
                resultados.innerHTML = '<li style="padding:4px">Nenhum usuário encontrada</li>';
                return;
            }

            data.forEach(item => {
                const li = document.createElement('li');
                li.textContent = item.nome;  // usa o campo "nome" retornado
                li.style.padding = '4px';
                li.style.cursor = 'pointer';
                li.addEventListener('click', () => {
                input.value = item.nome;
                resultados.innerHTML = '';
                // Se precisar guardar o ID, você pode criar um input hidden:
                document.getElementById('usuario').value = item.id;
                });
                resultados.appendChild(li);
            });
            })
            .catch(err => {
            console.error('Erro ao buscar usuários:', err);
            resultados.innerHTML = '<li style="padding:4px">Erro na busca</li>';
            });
        }, 300); // delay para evitar muitas requisições
    });
    });
    //Fim

    function removerValor() {
      let idFiscal = document.getElementById("usuario");
      idFiscal.value = "";
    }
    
  </script>
  <script src="../js/masks.js"></script>

</body>
</html>



  
