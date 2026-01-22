function limparEditorVazio(id) {
    const textarea = document.getElementById(id);
    if (!textarea) return;

    const conteudo = textarea.value
        .replace(/<br\s*\/?>/gi, '')
        .replace(/&nbsp;/gi, '')
        .trim();

    textarea.value = conteudo === '' ? '' : textarea.value;
}