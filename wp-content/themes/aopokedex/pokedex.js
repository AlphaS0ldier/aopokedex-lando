
function redireccionar_pagina_pokemon(response) {

    let contenedor_tabla = document.querySelector('.contenedor-tabla');

    let lista = document.querySelector('.contenedor-tabla table');

    if (!lista) {
        contenedor_tabla.insertAdjacentHTML('beforeend', response.tabla);
    } else {
        lista.remove();
        contenedor_tabla.insertAdjacentHTML('beforeend', response.tabla);
    }

    lista = document.querySelector('.contenedor-tabla table');

    loading.style.display = "none";

    let pokemons = lista.querySelectorAll('tr');

    pokemons.forEach((pokemon) => {
        if (pokemon.querySelectorAll("td")[2]) {
            pokemon.addEventListener("click", () => {
                loading.style.display = "flex";
                jQuery(document).ready(function ($) {
                    $.ajax({
                        url: my_ajax_object.url,
                        type: 'post',
                        data: {
                            action: 'redireccionar_pagina_pokemon',
                            nonce: my_ajax_object.nonce,
                            pokemon_nombre: pokemon.querySelectorAll("td")[2].getAttribute("nombre"),
                        },
                        success: function (response) {
                            window.location.href = response.url;
                        }
                    });
                });
            });
        }
    });

}


document.addEventListener('DOMContentLoaded', () => {

    let selectsPokedex = document.querySelector(".select-estilo-pokedex");

    let textGeneracion = document.querySelector("#pokedex-titulo-generacion");

    selectsPokedex.addEventListener("change", () => {
        textGeneracion.textContent = selectsPokedex.querySelector("select").value;

        let loading = document.querySelector("#loading");

        loading.style.display = "flex";

        jQuery(document).ready(function ($) {
            $.ajax({
                url: my_ajax_object.url,
                type: 'post',
                data: {
                    action: 'filtrar_pokemon_pokedex',
                    nonce: my_ajax_object.nonce,
                    region: selectsPokedex.querySelector("select").value,
                },
                success: function (response) {
                    redireccionar_pagina_pokemon(response);
                }
            });
        });

    });

    //redireccionar_pagina_pokemon();

});
