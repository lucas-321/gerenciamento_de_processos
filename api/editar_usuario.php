<?php
ob_start();
session_start();
include("conexao.php");
include("funcoes.php"); // para nomeCategoria() e registrarAtividade()

if ($_SESSION["categoria"] != 1 && $_SESSION["categoria"] != 2) {
    echo json_encode(["mensagem" => "Acesso negado. Apenas Administradores podem alterar usuários."]);
    exit;
}

$id = $_POST["id"]; // id do agente
$nome = trim($_POST["nome"]);
$matricula = trim($_POST["matricula"]);
$sexo = $_POST["sexo"];
$cpf = trim($_POST["cpf"]);
$data_nascimento = $_POST["data_nascimento"];
$login = trim($_POST["login"]);
$categoria = (int) $_POST["categoria"];

// Foto (opcional)
$foto_nome = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $ext = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
    $foto_nome = uniqid('foto_') . '.' . $ext;
    $destino = __DIR__ . '/../fotos_perfil/' . $foto_nome;

    if (!move_uploaded_file($_FILES['foto']['tmp_name'], $destino)) {
        echo json_encode(["mensagem" => "Falha ao salvar a imagem."]);
        exit;
    }
}

// Senha (opcional)
$senha = isset($_POST["senha"]) && !empty($_POST["senha"])
    ? password_hash($_POST["senha"], PASSWORD_DEFAULT)
    : null;

$conexao->begin_transaction();

try {
    // 1. Buscar dados antigos com base no agente_id
    $stmtOld = $conexao->prepare("SELECT agentes.nome, matricula, sexo, cpf, data_nascimento, foto, login, categoria
        FROM usuarios
        INNER JOIN agentes ON agentes.id = usuarios.agente_id
        WHERE usuarios.agente_id = ?");
    $stmtOld->bind_param("i", $id);
    $stmtOld->execute();
    $resultado = $stmtOld->get_result();
    $antigo = $resultado->fetch_assoc();

    if (!$antigo) {
        throw new Exception("Usuário/agente não encontrado com agente_id $id.");
    }

    // 2. Atualizar dados do agente
    $queryAgente = "UPDATE agentes SET nome = ?, matricula = ?, sexo = ?, data_nascimento = ?, cpf = ?";
    $params = [$nome, $matricula, $sexo, $data_nascimento, $cpf];
    $types = "sssss";

    if ($foto_nome) {
        $queryAgente .= ", foto = ?";
        $params[] = $foto_nome;
        $types .= "s";
    }

    $queryAgente .= " WHERE id = ?";
    $params[] = $id;
    $types .= "i";

    $stmt = $conexao->prepare($queryAgente);
    $stmt->bind_param($types, ...$params);
    $stmt->execute();

    // 3. Atualizar dados do usuario
    $queryUsuario = "UPDATE usuarios SET login = ?, categoria = ?";
    $paramsUser = [$login, $categoria];
    $typesUser = "si";

    if ($senha) {
        $queryUsuario .= ", senha = ?";
        $paramsUser[] = $senha;
        $typesUser .= "s";
    }

    $queryUsuario .= " WHERE agente_id = ?";
    $paramsUser[] = $id;
    $typesUser .= "i";

    $stmt2 = $conexao->prepare($queryUsuario);
    $stmt2->bind_param($typesUser, ...$paramsUser);
    $stmt2->execute();

    // 4. Montar log de alterações
    $alteracoes = [];
    if ($antigo["nome"] !== $nome) {
        $alteracoes[] = "nome de \"{$antigo['nome']}\" para \"$nome\"";
    }
    if ($antigo["matricula"] !== $matricula) {
        $alteracoes[] = "matrícula de \"{$antigo['matricula']}\" para \"$matricula\"";
    }
    if ($antigo["sexo"] !== $sexo) {
        $alteracoes[] = "sexo de \"{$antigo['sexo']}\" para \"$sexo\"";
    }
    if ($antigo["cpf"] !== $cpf) {
        $alteracoes[] = "CPF de \"{$antigo['cpf']}\" para \"$cpf\"";
    }
    if ($antigo["data_nascimento"] !== $data_nascimento) {
        $alteracoes[] = "data de nascimento de \"{$antigo['data_nascimento']}\" para \"$data_nascimento\"";
    }
    if ($antigo["login"] !== $login) {
        $alteracoes[] = "login de \"{$antigo['login']}\" para \"$login\"";
    }
    if ($antigo["categoria"] != $categoria) {
        $categoria_antiga = nomeCategoria($antigo["categoria"]);
        $categoria_nova = nomeCategoria($categoria);
        $alteracoes[] = "categoria de \"$categoria_antiga\" para \"$categoria_nova\"";
    }
    if ($foto_nome && $antigo["foto"] !== $foto_nome) {
        $alteracoes[] = "imagem de perfil alterada";
    }

    if (!empty($alteracoes)) {
        $alteracoesTexto = implode("; ", $alteracoes);
        $data_atual = date("d/m/Y H:i:s");
        $nome_usuario = $_SESSION["nome"];
        $id_usuario = $_SESSION["usuario_id"];
        $tipo = "editar";
        $objeto = "usuario";
        $detalhes = "$nome_usuario alterou o $objeto $nome: $alteracoesTexto em $data_atual.";

        registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);
    }

    $conexao->commit();
    ob_clean();
    echo json_encode(["mensagem" => "Usuário alterado com sucesso."]);

} catch (Exception $e) {
    $conexao->rollback();
    ob_clean();
    echo json_encode(["mensagem" => "Erro ao atualizar usuário/agente: " . $e->getMessage()]);
}
?>
