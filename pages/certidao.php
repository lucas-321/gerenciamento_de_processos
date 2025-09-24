<!DOCTYPE html>
<html lang="pt-br">
<head>
  <meta charset="UTF-8">
  <title>Criação de Certidão</title>

  <link rel="stylesheet" href="../css/style.css">
  <link rel="stylesheet" href="../css/forms.css">
  <link rel="stylesheet" href="../css/modal.css">
  <link rel="stylesheet" href="../css/documentos.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
</head>
<body>

  <?php 
    include('header.php');
  ?>

  <div class="content">

    <button id="btnImprimir" style="margin-bottom:1rem;">Imprimir Certidão</button>

      <?php 
        include('../api/conexao.php');
        include('../api/numero_extenso.php');
        include('../api/cpf_cnpj_registro.php');

        $sql = "SELECT certidoes.*,
        certidoes.cpf_cnpj AS cpf_cnpj_proprietario, 
        processos.*, 
        assuntos.nome AS n_assunto, 
        agentes.nome AS n_agente
            FROM certidoes
            INNER JOIN processos ON processos.id = certidoes.processo
            INNER JOIN assuntos ON processos.assunto = assuntos.id
            INNER JOIN usuarios ON usuarios.id = certidoes.criado_por
            INNER JOIN agentes ON usuarios.agente_id = agentes.id
            WHERE certidoes.id = $_POST[id]";

        $result = mysqli_query($conexao, $sql);

        if(mysqli_num_rows($result) > 0) {

            while($dados = mysqli_fetch_assoc($result)){
                $n_protocolo = $dados["n_protocolo"];
                // $data_processo = date('d/m/Y', strtotime($dados["data_processo"]));
                $data_processo = $dados["data_processo"];
                $nome_assunto = $dados["n_assunto"];
                $assunto = $dados["assunto"];
                $inscricao = $dados["inscricao"];
                // $nome_interessado = mb_strtolower($dados["nome_interessado"], 'UTF-8');
                $nome_interessado = $dados["nome_interessado"];
                $cpf_cnpj = $dados["cpf_cnpj"];
                $email = $dados["email"];
                $telefone = $dados["telefone"];
                $observacoes = $dados["observacoes"];
                $pendencia = $dados["pendencia"];
                $ano = date('Y', strtotime($dados['data_processo']));
                $status = $dados["status"];

                $tipo = $dados["tipo"];
                $data_certidao = date('d/m/Y', strtotime($dados["data_certidao"]));
                $nome_proprietario = $dados["nome_proprietario"];
                $cpf_cnpj_proprietario = $dados["cpf_cnpj_proprietario"];
                $endereco = $dados["endereco"];
                $numero_porta = $dados["numero_porta"];
                $valor_venal = number_format($dados["valor_venal"], 2, ',', '.');
                $trecho_documento = $dados["trecho_documento"];
                $endereco_atual = $dados["endereco_atual"];

                $data_itiv = $dados["data_itiv"];
                $valor_itiv = $dados["valor_itiv"];
                $valor_itiv_extenso = numeroPorExtenso($valor_itiv);
                $aliquota_itiv = $dados["aliquota_itiv"];
                $valor_transacao = $dados["valor_transacao"];
                $valor_transacao_extenso = numeroPorExtenso($valor_transacao);
                $dam = $dados["numero_dam"];
                $nome_transmitente = $dados["nome_transmitente"];
                $cpf_cnpj_transmitente = $dados["cpf_cnpj_transmitente"];
                $nome_adquirente = $dados["nome_adquirente"];
                $cpf_cnpj_adquirente = $dados["cpf_cnpj_adquirente"];
                $data_pagamento_itiv = $dados["data_pagamento_itiv"];
                $descricao_metragem = $dados["descricao_metragem"];

                $informacoes_adicionais = $dados["informacoes_adicionais"];
                $agente = $dados["n_agente"];

                $meses = [
                    1 => 'janeiro', 'fevereiro', 'março', 'abril', 'maio', 'junho',
                    'julho', 'agosto', 'setembro', 'outubro', 'novembro', 'dezembro'
                ];

                $timestamp = strtotime($data_processo);

                $dia = date('d', $timestamp);
                $mes = $meses[(int)date('m', $timestamp)];
                $ano = date('Y', $timestamp);

                $data_processo = "$dia de $mes de $ano";
              }

        }

        $complemento = "";
        $apenasDigitos = preg_replace('/\D/', '', $cpf_cnpj_proprietario);

        if (strlen($apenasDigitos) === 11) {
            $complemento = ", <b>CPF nº $cpf_cnpj_proprietario</b>";
        } elseif (strlen($apenasDigitos) === 14) {
            $complemento = ", <b>CNPJ nº $cpf_cnpj_proprietario</b>";
        } else {
            $complemento = "";
        }

        if($tipo == 'lancamento_numero'){

          $titulo = "DE LANÇAMENTO DE NÚMERO";

          $p1 = "<p class='bloco'>Tendo em vista o que consta no Processo Administrativo nº <b>$n_protocolo</b>, datado de <b>$data_processo</b> e revendo os arquivos deste setor, certificamos que o imóvel situado na <b>$endereco</b>, encontra-se lançado em nome de <b>$nome_proprietario</b>$complemento. Inscrito no Cadastro Imobiliário sob nº <b>$inscricao</b>, fora atribuído o número <b><u>$numero_porta</u></b>.</p>";

        }else if($tipo == 'valor_venal'){

          $titulo = "DE VALOR VENAL";

          $p1 = "<p class='bloco'>Tendo em vista o que consta no Processo Administrativo nº <b>$n_protocolo</b>, datado de <b>$data_processo</b> e revendo os arquivos deste setor, certificamos que o imóvel situado na <b>$endereco</b>, encontra-se lançado em nome de <b>$nome_proprietario</b>$complemento. Cadastro Imobiliário sob nº <b>$inscricao</b>.</p>
          
          <p class='bloco'>
              Possui valor venal total de R$ <b>$valor_venal</b>. Vale ressaltar que o referido valor consiste em base de cálculo do Imposto sobre a Propriedade Predial e Territorial Urbana – IPTU.
          </p>";
        }else if($tipo == 'comprovacao_endereco'){

          $titulo = "DE COMPROVAÇÃO DE ENDEREÇO";

          $p1 = "<p class='bloco'>Tendo em vista o que consta no Processo Administrativo nº <b>$n_protocolo</b>, datado de <b>$data_processo</b> e revendo os arquivos deste setor, certificamos que o imóvel situado na <b>$endereco</b>, encontra-se lançado em nome de <b>$nome_proprietario</b>$complemento. Cadastro Imobiliário sob nº <b>$inscricao</b>.</p>
          
          <p class='bloco'>
              $trecho_documento
          </p>
          
          <p class='bloco'>De acordo com a Lei Nº 2.774, de 26 de junho de 2024, que “DISPÕE SOBRE O REORDENAMENTO DOS LOGRADOUROS PÚBLICOS DO MUNICÍPIO DE
          ALAGOINHAS-BAHIA E DÁ OUTRAS PROVIDÊNCIAS.”, o referido fora alterado para:  <b>$endereco_atual.</b></p>";
        }else if($tipo == 'comprovacao_pagamento_itiv'){

          $titulo = "DE COMPROVAÇÃO DE PAGAMENTO DE ITIV";

          $p1 = "<p class='bloco'>Tendo em vista o que consta no Processo Administrativo nº <b>$n_protocolo</b>, datado de <b>$data_processo</b> e revendo os arquivos deste setor, declaramos que fora lançado ITIV na data de ".date('d/m/Y', strtotime($data_itiv)).", valor de <b>R$ ".number_format($valor_itiv, 2, ',', '.')." ($valor_itiv_extenso)</b>, alíquota de <b>".number_format($aliquota_itiv, 2, ',', '.')." %</b>, valor da transação de <b>R$ ".number_format($valor_transacao, 2, ',', '.')." ($valor_transacao_extenso)</b>, referente ao imóvel situado na <b>$endereco</b>, lançado em nome de <b>$nome_proprietario</b>$complemento. Cadastro Imobiliário sob nº <b>$inscricao</b>.</p>
          
          <p class='bloco'>
              DAM nº <b>$dam</b>, data de lançamento: ".date('d/m/Y', strtotime($data_itiv)).", tendo como Transmitente: <b>$nome_transmitente</b>".trechoCpfCnpj($cpf_cnpj_transmitente)." e Adquirente: <b>$nome_adquirente</b>".trechoCpfCnpj($cpf_cnpj_adquirente).", data de pagamento: ".date('d/m/Y', strtotime($data_pagamento_itiv))."
          </p>";
        }else if($tipo == 'inexistencia_imoveis'){

          $titulo = "DE INEXISTÊNCIA DE IMÓVEIS";

          $p1 = "<p class='bloco'>Tendo em vista o que consta no Processo Administrativo nº <b>$n_protocolo</b>, datado de <b>$data_processo</b> e revendo os arquivos deste setor, certificamos que <b>$nome_proprietario</b>$complemento, não possui imóveis lançados no Cadastro Imobiliário do Município de Alagoinhas.</p>";

        }else if($tipo == 'lancamento'){

          $titulo = "DE LANÇAMENTO DE IMÓVEL";

          $p1 = "<p class='bloco'>Tendo em vista o que consta no Processo Administrativo nº <b>$n_protocolo</b>, datado de <b>$data_processo</b>, revendo os arquivos deste setor e em atenção ao § 1º, art. 96 da Lei Municipal nº 144/2020¹, certificamos que o imóvel situado na <b>$endereco</b>, nesta, encontra-se lançado no Cadastro Imobiliário Municipal em nome de <b>$nome_proprietario</b>$complemento. Cadastro Imobiliário nº <b>$inscricao</b>.</p>
          ";

        }else if($tipo == 'metragem'){

          $titulo = "DE METRAGEM";

          $p1 = "<p class='bloco'>Tendo em vista o que consta no Processo Administrativo nº <b>$n_protocolo</b>, datado de <b>$data_processo</b> e revendo os arquivos deste setor, certificamos que o imóvel situado na <b>$endereco</b>, nesta, encontra-se lançado no Cadastro Imobiliário Municipal em nome de <b>$nome_proprietario</b>$complemento. Cadastro Imobiliário nº <b>$inscricao</b>.</p>
          <p>$descricao_metragem</p>
          ";

        }

        $destaque = "<p class='bloco'>$informacoes_adicionais</p>";

        $p2 = "<p class='bloco'>
          E, para constar, eu, $agente, passei a presente Certidão aos $data_certidao, a qual foi conferida por mim, emitida e subscrita pela Coordenação, após confirmação da situação do imóvel em nosso cadastro.
        </p>";

        // echo "$sql";
      ?>

    <div class="certidao">

      <div class='cabecalho'>

          <div class='logo-docs'>
              <img src='../img/logo-no-bg.png' alt='logo'>
          </div>

          <div class='dados_prefeitura'>
              <span>PREFEITURA MUNICIPAL DE ALAGOINHAS</span>
              <span class='subtitle'>PRAÇA GRACILIANO DE FREITAS, S/N, CENTRO<br>
              ALAGOINHAS-BA</span>
          </div>

      </div>

      <div class="titulo">
        <h3>
          <?php
            echo "CERTIDÃO $titulo";
          ?>
        </h3>
      </div>

      <div class="conteudo">
        
        <p class="bloco">
          <strong>Passada a pedido de:</strong> 
          <?php echo "$nome_interessado"; ?>
        </p>

        <?php
          echo "$p1";
          echo "$destaque";
          echo "$p2";
        ?>

        <p class="validacao">
            Validade da Certidão de 90 dias, a contar da data de sua emissão. 
        </p>

        <p class="validacao">
            As Certidões fornecidas não excluem o direito da Fazenda Pública Municipal cobrar, em qualquer tempo, os débitos que venham a ser posteriormente apurados pela autoridade administrativa competente.
        </p>

        <p class="validacao">
            Obs: Qualquer rasura tornará nulo este documento.
        </p>

        <div class="assinatura">
            <span>Coordenação/DRI</span>
        </div>

      </div>

    </div>

  </div>

  <!-- <script src="../js/masks.js"></script> -->
  <script>
    document.getElementById('btnImprimir').addEventListener('click', function () {
        const conteudo = document.querySelector('.certidao').innerHTML;
        const novaJanela = window.open('', '', 'width=800,height=600');

        novaJanela.document.write(`
            <html>
                <head>
                    <title>Impressão de Certidão</title>
                    <style>
                        .certidao {
                          width: 90%;
                          background: whitesmoke;
                          color: black;
                          font-family: 'Times New Roman', Times, serif;
                          display: flex;
                          flex-direction: column;
                          align-items: center;
                          justify-content: center;
                        }

                        .conteudo {
                          margin: 0 3.5rem;
                        }

                        .cabecalho {
                          display: flex;
                          justify-content: center;
                          align-items: center;
                          font-weight: bold;
                        }

                        .logo-docs {
                          margin: .625rem;
                        }

                        .logo-docs img {
                          width: 5rem;
                        }

                        .subtitle {
                          font-size: 12px;
                        }
                        
                        .dados_prefeitura {
                          display: flex;
                          flex-direction: column;
                        }

                        .titulo {
                          margin: 2rem 0;
                          text-align: center;
                        }

                        .bloco {
                          text-align: justify;
                          font-size: 12pt;
                          line-height: 1.5;
                        }

                        .validacao {
                          text-align: justify;
                          font-size: 10pt;
                          font-weight: bold;
                          margin: .625rem 0;
                        }

                        .assinatura{
                          margin-top: 4rem;
                          display: flex;
                          justify-content: center;
                        }

                        .assinatura span{
                          width: 33%;
                          text-align: center;
                          border-top: 1px solid black;
                        }
                    </style>
                </head>
                <body>
                    ${conteudo}
                </body>
            </html>
        `);

        novaJanela.document.close();
        novaJanela.focus();
        novaJanela.print();
        novaJanela.close();
    });
  </script>

</body>
</html>



  
