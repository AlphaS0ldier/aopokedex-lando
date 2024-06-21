document.addEventListener('DOMContentLoaded', () => {

    let selectsCalculadoEvsContenedor = document.querySelector(".contenedor-select");

    let selectsCalculadorEvs = selectsCalculadoEvsContenedor.querySelector("select");

    let imgCalculadorEvs = document.querySelector("#calculador-evs-pokemon-imagen");

    let inputCalculadorEvs = document.querySelectorAll(".calculador-evs-inputs div input[type=number]");

    let nombre = "";

    let boton = document.querySelector("#calculador-evs-boton-contenedor .estilo-boton");

    let textoSeleccionar = document.querySelector("#texto-seleccionar").textContent;

    let textoEvSuperado = document.querySelector("#texto-ev-superado").textContent;

    let textoEvMinimo = document.querySelector("#texto-ev-minimo").textContent;

    let textoNivelValido = document.querySelector("#texto-nivel-valido").textContent;

    inputCalculadorEvs.forEach((input) => {

        const max = +input.getAttribute("max");

        input.addEventListener("keydown", function (e) {

            const typed = Number(e.key);

            if (!isNaN(typed)) {
                e.preventDefault();
            }

            if ((e.target.value + typed) <= max) {
                input.value += typed
            }
        })
    });

    selectsCalculadorEvs.addEventListener("change", () => {
        let loading = document.querySelector("#loading");

        loading.style.display = "flex";

        nombre = selectsCalculadorEvs.value;

        jQuery(document).ready(function ($) {

            $.ajax({
                url: my_ajax_object.url,
                type: 'post',
                data: {
                    action: 'mostrar_pokemon',
                    nonce: my_ajax_object.nonce,
                    name: nombre,

                },
                success: function (response) {

                    imgCalculadorEvs.style.display = "flex";

                    imgCalculadorEvs.src = response.img;

                    loading.style.display = "none";
                }
            });
        });
    });

    if (boton) {
        boton.addEventListener(("click"), () => {

            if (imgCalculadorEvs.src == "") {
                window.alert(textoSeleccionar);
            } else {
                let loading = document.querySelector("#loading");

                let evs = 0;

                let nivel = 0;

                let estadisticas = {};

                inputCalculadorEvs.forEach((input) => {

                    if (input.getAttribute("max") != 100) {

                        evs += Number(input.value);

                        estadisticas[input.getAttribute("id")] = input.value;

                    } else {

                        nivel = Number(input.value);

                    }

                });
                if (nivel > 0) {

                    if (evs > 0 && evs <= 510) {
                        loading.style.display = "flex";

                        jQuery(document).ready(function ($) {
                            $.ajax({
                                url: my_ajax_object.url,
                                type: 'post',
                                data: {
                                    action: 'calcular_evs_calculador_evs',
                                    nonce: my_ajax_object.nonce,
                                    name: nombre,
                                    evs: estadisticas,
                                    nivel: nivel,
                                },
                                success: function (response) {

                                    console.log(response);

                                    let loading_image = loading.querySelector("img");
                                    loading_image.style.display = "none";

                                    let resultado_contenedor = document.querySelector("#resultado_calculo_contenedor");
                                    resultado_contenedor.style.display = "flex";

                                    if (document.querySelector("#resultado_calculo_contenedor h1")) {

                                        let nivel_texto = document.querySelector("#resultado_calculo_contenedor h1");

                                        nivel_texto.textContent += nivel;
                                    }

                                    let resultado_contenedor_datos = document.querySelector("#resultado_calculo_datos");

                                    resultado_contenedor_datos.insertAdjacentHTML('beforeend', response.result);

                                    let boton_resultado = document.querySelector("#resultado_calculo_boton");

                                    boton_resultado.addEventListener("click", () => {

                                        location.reload();

                                    });

                                }
                            });
                        });
                    } else {
                        if (evs > 510) {
                            window.alert(textoEvSuperado + " " + (evs - 510));
                        } else {
                            window.alert(textoEvMinimo);
                        }
                    }
                } else {
                    window.alert(textoNivelValido);
                }
            }
        });
    }
});
