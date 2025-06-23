<?php
function registrarAtividade($conexao, $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes) {
    // Prepara o statement de log
    $query = "INSERT INTO registro_atividades (id_usuario, nome_usuario, tipo, objeto, detalhes, data_registro)
              VALUES (?, ?, ?, ?, ?, NOW())";

    $stmt = $conexao->prepare($query);
    if (!$stmt) {
        throw new Exception("Erro ao preparar log de atividade: " . $conexao->error);
    }

    $stmt->bind_param("issss", $id_usuario, $nome_usuario, $tipo, $objeto, $detalhes);

    if (!$stmt->execute()) {
        throw new Exception("Erro ao executar log de atividade: " . $stmt->error);
    }

    // Não retorna nada: log é silencioso
}

function nomeCategoria($id_categoria) {
    $categorias = [
        1 => "Administrador",
        2 => "Coordenador",
        3 => "Protocolo",
        4 => "Analista",
        5 => "Externo"
    ];

    return $categorias[$id_categoria] ?? "Desconhecido";
}