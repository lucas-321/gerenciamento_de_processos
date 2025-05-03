// Obter o modal
var modal = document.getElementById("myModal");

// Obter o botão que abre o modal
var btn = document.getElementById("btnNovoAssunto");

// Obter o elemento <span> (que fecha o modal)
var span = document.getElementsByClassName("close")[0];

// Quando o usuário clica no botão, abre o modal
btn.onclick = function() {
    modal.style.display = "block";
}

// Quando o usuário clica no "x" (fechar o modal), fecha o modal
span.onclick = function() {
    modal.style.display = "none";
}

// Quando o usuário clica fora do modal, o modal também é fechado
window.onclick = function(event) {
    if (event.target == modal) {
        modal.style.display = "none";
    }
}

document.getElementById("assuntoForm").addEventListener("submit", function(e) {
    e.preventDefault();

    const form = document.getElementById("assuntoForm");
    const formData = new FormData(form);
    fetch("../api/criar_assunto.php", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(data => {
        alert(data.mensagem);
        atualizarAssuntos(data.novo_id);
        modal.style.display = "none";
    });
});

function atualizarAssuntos() {
    window.location.reload();
}