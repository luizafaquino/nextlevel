document.addEventListener("DOMContentLoaded", function () {

    // Ordenação dos produtos

    const ordenar = document.getElementById("ordenar-produtos");

    if (ordenar) {

        ordenar.addEventListener("change", function () {

            ordenar.form.submit();

        });

    }

    // Validação da quantidade

    const campos = document.querySelectorAll(
        'input[name="quantidade"]'
    );

    campos.forEach(function (campo) {

        campo.addEventListener("input", function () {

            const quantidade = Number(campo.value);

            if (quantidade < 1) {

                campo.setCustomValidity(
                    "Informe uma quantidade válida."
                );

            } else if (campo.max && quantidade > Number(campo.max)) {

                campo.setCustomValidity(
                    "Quantidade superior ao estoque."
                );

            } else {

                campo.setCustomValidity("");

            }

        });

    });

    // Confirmação de senha

    const senha = document.querySelector(
        'input[name="senha"]'
    );

    const confirmar = document.querySelector(
        'input[name="confirmar"]'
    );

    if (senha && confirmar) {

        function validarSenha() {

            if (senha.value !== confirmar.value) {

                confirmar.setCustomValidity(
                    "As senhas não coincidem."
                );

            } else {

                confirmar.setCustomValidity("");

            }

        }

        senha.addEventListener("input", validarSenha);

        confirmar.addEventListener("input", validarSenha);

    }

    // Confirmação de exclusão

    const formularios = document.querySelectorAll(
        ".form-excluir"
    );

    formularios.forEach(function (formulario) {

        formulario.addEventListener("submit", function (evento) {

            if (!confirm("Deseja realmente excluir este produto?")) {

                evento.preventDefault();

            }

        });

    });

});