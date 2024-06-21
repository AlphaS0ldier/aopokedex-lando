document.addEventListener('DOMContentLoaded', () => {

    let contenedor_imagenes = document.querySelector("#calculador-evs-imagen-contenedor");

    let contenedor_input = document.querySelector("#calculador-evs-nivel");

    let selectContenedor = document.querySelector(".contenedor-select");

    let selectPokemonAtacante = selectContenedor.querySelector("select");

    let movimiento_seleccionado = {};

    let boton = document.querySelector("#calculador-danos-boton-contenedor div");

    let nombre_defensor = "";


    let textoSeleccionar = document.querySelector("#texto-seleccionar").textContent;

    let textoEvSuperado = document.querySelector("#texto-ev-superado").textContent;

    let textoNivelValido = document.querySelector("#texto-nivel-valido").textContent;

    let textoSeleccionarMovimiento = document.querySelector("#texto-seleccionar-movimiento").textContent;


    jQuery(document).ready(function ($) {

        $.ajax({
            url: my_ajax_object.url,
            type: 'post',
            data: {
                action: 'mostrar_calculador_danos_inputs',
                nonce: my_ajax_object.nonce,

            },
            success: function (response) {
                contenedor_imagenes.insertAdjacentHTML('afterend', response.movimiento_datos);
                contenedor_imagenes.insertAdjacentHTML('beforeend', response.contenedor_imagenes);
                contenedor_input.insertAdjacentHTML('afterend', response.contenedor_inputs)
            }
        });
    });

    selectPokemonAtacante.addEventListener("change", () => {

        nombre = selectPokemonAtacante.value;

        jQuery(document).ready(function ($) {

            $.ajax({
                url: my_ajax_object.url,
                type: 'post',
                data: {
                    action: 'mostrar_movimiento_y_pokemon_defensor',
                    nonce: my_ajax_object.nonce,
                    name: nombre,

                },
                success: function (response) {

                    if (document.querySelector("#movimientos")) {
                        document.querySelector("#movimientos").remove();
                        document.querySelector("#movimiento_datos").style.display = "none"
                        movimiento_seleccionado = {};
                    }

                    if (document.querySelector("#select-pokemon-defensor")) {
                        document.querySelector("#select-pokemon-defensor").remove();
                        document.querySelector("#pokemon_defensor_datos_contenedor").style.display = "none";

                        document.querySelector("#pokemon_defensor_datos").style.display = "none";

                        document.querySelector("#pokemon_dano_batalla").style.display = "none";

                        document.querySelector("#pokemon_defensor_datos").src = null;

                        nombre_defensor = "";
                    }

                    selectContenedor.insertAdjacentHTML('beforeend', response.movimientos);

                    selectContenedor.insertAdjacentHTML('beforeend', response.select_pokemon_defensor);


                    let select_movimientos = document.querySelector("#movimientos");


                    select_movimientos.addEventListener("change", () => {

                        let movimiento_datos = document.querySelector("#movimiento_datos");

                        movimiento_datos.style.display = "flex";

                        let movimiento_id = movimiento_datos.querySelector("#movimiento_id");

                        movimiento_id.textContent = select_movimientos.value;

                        movimiento_seleccionado["nombre"] = select_movimientos.value;

                        let movimiento_poder = movimiento_datos.querySelector("#movimiento_poder");

                        movimiento_poder.textContent = select_movimientos[select_movimientos.selectedIndex].getAttribute("power");

                        movimiento_seleccionado["poder"] = select_movimientos[select_movimientos.selectedIndex].getAttribute("power");

                        let movimiento_tipo = movimiento_datos.querySelector("#movimiento_tipo");

                        movimiento_tipo.src = select_movimientos[select_movimientos.selectedIndex].getAttribute("type_img");

                        movimiento_seleccionado["tipo"] = select_movimientos[select_movimientos.selectedIndex].getAttribute("type");

                        let movimiento_categoria = movimiento_datos.querySelector("#movimiento_categoria");

                        movimiento_categoria.src = select_movimientos[select_movimientos.selectedIndex].getAttribute("damage_class_img");

                        movimiento_seleccionado["categoria"] = select_movimientos[select_movimientos.selectedIndex].getAttribute("damage_class");

                    });

                    let select_pokemon_defensor = document.querySelector("#select-pokemon-defensor");

                    select_pokemon_defensor.addEventListener("change", () => {

                        nombre_defensor = select_pokemon_defensor.value;

                        jQuery(document).ready(function ($) {
                            $.ajax({
                                url: my_ajax_object.url,
                                type: 'post',
                                data: {
                                    action: 'mostrar_pokemon',
                                    nonce: my_ajax_object.nonce,
                                    name: nombre_defensor,
                                },
                                success: function (response) {

                                    document.querySelector("#pokemon_defensor_datos_contenedor").style.display = "block";

                                    document.querySelector("#pokemon_defensor_datos").style.display = "block";

                                    document.querySelector("#pokemon_dano_batalla").style.display = "block";

                                    document.querySelector("#pokemon_defensor_datos").src = response.img;
                                }
                            });
                        });
                    });


                }
            });
        });
    });


    boton.addEventListener("click", () => {

        if (document.querySelector("#calculador-evs-pokemon-imagen").src != ""
            && document.querySelector("#pokemon_defensor_datos").src != ""
            && nombre_defensor) {

            if (document.querySelector("#movimiento_id").textContent
                && (
                    Object.keys(movimiento_seleccionado).length > 0
                )) {

                let evs = 0;

                let nivel = 0;

                let inputCalculadorEvs = document.querySelectorAll(".calculador-evs-inputs input[type=number]");

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

                    if (evs <= 510) {

                        let boosts_inputs = document.querySelectorAll("#calcular_dano_input_boost div");

                        let boosts_inputs_valor = {};

                        boosts_inputs.forEach((input) => {
                            if (input.querySelector("label input:checked")) {
                                boosts_inputs_valor[input.querySelector("label input").getAttribute("name")] =
                                    input.querySelector("label input:checked").getAttribute("id");
                            }
                        });

                        let loading = document.querySelector("#loading");

                        loading.style.display = "flex";

                        jQuery(document).ready(function ($) {
                            $.ajax({
                                url: my_ajax_object.url,
                                type: 'post',
                                data: {
                                    action: 'calcular_dano_entre_pokemon',
                                    nonce: my_ajax_object.nonce,
                                    pokemon_atq: nombre,
                                    evs: estadisticas,
                                    nivel: nivel,
                                    boost: boosts_inputs_valor,
                                    pokemon_def: nombre_defensor,
                                    movimiento: movimiento_seleccionado
                                },
                                success: function (response) {

                                    console.log(response);

                                    let loading_image = loading.querySelector("img");
                                    loading_image.style.display = "none";

                                    let resultado_contenedor = document.querySelector("#resultado_calculo_contenedor");
                                    resultado_contenedor.style.display = "flex";

                                    let resultado_contenedor_datos = document.querySelector("#resultado_calculo_datos");

                                    resultado_contenedor_datos.insertAdjacentHTML('afterend', response.result);

                                    let boton_resultado = document.querySelector("#resultado_calculo_boton");

                                    boton_resultado.addEventListener("click", () => {

                                        location.reload();

                                    });

                                }
                            });
                        });
                    } else {
                        window.alert(textoEvSuperado + " " + (evs - 510));
                    }

                } else {
                    window.alert(textoNivelValido);
                }

            } else {
                window.alert(textoSeleccionarMovimiento);
            }

        } else {
            window.alert(textoSeleccionar);
        }

    });

});