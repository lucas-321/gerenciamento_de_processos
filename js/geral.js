document.addEventListener('DOMContentLoaded', function() {
    document.querySelector('.list-model').scrollIntoView({
        behavior: 'smooth', // rolagem suave
        block: 'center'     // centraliza o elemento na tela
    });
});