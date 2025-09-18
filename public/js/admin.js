// public/js/admin.js

document.addEventListener("DOMContentLoaded", function () {
    /**
     * Lógica para os inputs de arquivo customizados.
     * Procura por todos os inputs com a classe 'custom-file-input'
     * e adiciona um event listener para atualizar o texto do label.
     */
    const customFileInputs = document.querySelectorAll(".custom-file-input");

    customFileInputs.forEach((input) => {
        input.addEventListener("change", function (e) {
            // Encontra o span de texto mais próximo dentro do mesmo wrapper
            const wrapper = e.target.closest(".file-input-wrapper");
            if (wrapper) {
                const textSpan = wrapper.querySelector(".file-input-text");
                if (textSpan) {
                    if (e.target.files && e.target.files.length > 0) {
                        textSpan.textContent = e.target.files[0].name;
                    } else {
                        textSpan.textContent =
                            "Clique para escolher um arquivo...";
                    }
                }
            }
        });
    });

    // Você pode adicionar outros scripts globais do admin aqui no futuro.
});