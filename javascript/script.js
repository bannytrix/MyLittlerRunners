// Pede confirmação antes de excluir uma run.
let formulariosExcluir = document.querySelectorAll('.form-excluir');

formulariosExcluir.forEach(function(formulario) {
    formulario.addEventListener('submit', function(evento) {
        let mensagem = formulario.getAttribute('data-confirmar');
        if (!confirm(mensagem)) {
            evento.preventDefault();
        }
    });
});