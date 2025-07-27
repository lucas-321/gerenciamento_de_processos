<?php 
    include('../api/conexao.php');

    if (!isset($_SESSION['usuario_id'])) {
        header("Location: ./index.php");
        exit;
    }

    $sql = "SELECT processos.*, assuntos.nome AS n_assunto
            FROM processos
            INNER JOIN assuntos ON processos.assunto = assuntos.id
            WHERE processos.id = $_POST[id]";

    $result = mysqli_query($conexao, $sql);

    if(mysqli_num_rows($result) > 0) {

        while($dados = mysqli_fetch_assoc($result)){
            $n_protocolo = $dados["n_protocolo"];
            $data_processo = date('d/m/Y', strtotime($dados["data_processo"]));
            $nome_assunto = $dados["n_assunto"];
            $assunto = $dados["assunto"];
            $inscricao = $dados["inscricao"];
            $nome_interessado = mb_strtolower($dados["nome_interessado"], 'UTF-8');
            $cpf_cnpj = $dados["cpf_cnpj"];
            $email = $dados["email"];
            $telefone = $dados["telefone"];
            $observacoes = $dados["observacoes"];
            $pendencia = $dados["pendencia"];
            $ano = date('Y', strtotime($dados['data_processo']));
            $status = $dados["status"];
        }

    }

?>

<ul class="list-title">
    <li>Informações</li>
</ul>

<div class="order-info">
    <span><b>Nº Protocolo:</b> <?php echo "$n_protocolo/$ano"; ?></span>
    <span><b>Assunto:</b> <?php echo "$nome_assunto"; ?></span>
</div>

<div class="order-info">
    <span><b>Data de Entrada:</b> <?php echo "$data_processo"; ?></span>
    <span><b>Inscrições:</b> <?php echo "$inscricao"; ?></span>
</div>

<div class="order-info">
    <span style="text-transform: capitalize"><b>Interessado:</b> <?php echo "$nome_interessado"; ?></span>
    <span><b>CPF/CNPJ:</b> <?php echo "$cpf_cnpj"; ?></span>
</div>

<div class="order-info">
    <span><b>E-mail:</b> <?php echo "$email"; ?></span>
    <span><b>Telefone:</b> <?php echo "$telefone"; ?></span>
</div>

<div class="process-info">
    <span><b>Observações:</b></span>
    <span><?php echo "$observacoes"; ?></span>
</div>

<?php if($pendencia){ ?>
    <div class="process-info">
        <span><b>Pendência:</b></span>
        <span><?php echo "$pendencia"; ?></span>
    </div>
<?php
    }

    if($status != 'Finalizado'){
?>

<form id="finalizarForm" style="display: none;">
    <input type="hidden" name="id" value="<?php echo $_POST['id']; ?>">
    <input type="hidden" id='status' name='status' value='Finalizado'>
    <div class="form-group button">
        <button class="form-btn red-btn" type="submit">Finalizar</button>
    </div>
</form>

<?php
    }
?>