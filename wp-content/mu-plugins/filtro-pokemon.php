<?php

/*
Plugin Name: Filtro Pokémon
Description: Plugin para el filtro de Pokémon
Version: 1.0
Author: m1chaelD
*/
function filtro_pokemon($atts)
{
    $default_atts = array(
        "filtro" => '',
    );

    $params = shortcode_atts($default_atts, $atts);

    $output = '
    <table class="estilo-tabla estilo-tabla-separar fuente-texto fuente-tamano-texto">
        <thead>
        <tr>
            <th>'.__("SPRITE","filtro-pokemon").'</th>
            <th>'.__("POKÉDEX NUM.","filtro-pokemon").'</th>
            <th>'.__("NAME","filtro-pokemon").'</th>
            <th>'.__("TYPES","filtro-pokemon").'</th>
        </tr>
        </thead>
        <tbody>';

    $pokemons = [];

    if (empty($params['filtro'])) {
        $pokemons = get_posts(
            array(
                'post_type' => 'pokemon',
                'post_status' => 'publish',
                'order' => 'ASC',
                'posts_per_page' => -1,
            )
        );

    } else {
        $pokemons = get_posts(
            array(
                'post_type' => 'pokemon',
                'post_status' => 'publish',
                'tax_query' => array(
                    array(
                        'taxonomy' => 'pokedex_region',
                        'field' => 'slug',
                        'terms' => $params['filtro'], /// Where term_id of Term 1 is "1".
                        'include_children' => false
                    )
                ),
                'order' => 'ASC',
                'posts_per_page' => -1,
            )
        );
    }

    $pokemon_ordenado = [];

    foreach ($pokemons as $pokemon) {
        $pokemon_datos = get_post_custom($pokemon->ID);
        if (!empty($params['filtro'])) {
            $pokedex_regional = get_post_meta($pokemon->ID, "_pokedex_regional")[0];
            foreach ($pokedex_regional as $generacion => $num) {
                if ($generacion == $params['filtro']) {
                    $pokemon_ordenado[$num] = ["nombre" => $pokemon_datos['_name'][0], "sprite" => $pokemon_datos["_sprite"][0], "tipos" => get_post_meta($pokemon->ID, "_types")[0]];
                }
            }
        } else {
            $pokemon_ordenado[get_post_meta($pokemon->ID, "_pokedex_national")[0]] = ["nombre" => $pokemon_datos['_name'][0], "sprite" => $pokemon_datos["_sprite"][0], "tipos" => get_post_meta($pokemon->ID, "_types")[0]];
        }
    }

    ksort($pokemon_ordenado);

    foreach ($pokemon_ordenado as $num => $valor) {
        $output .= '<tr>
        <td><img loading="lazy" class="tabla-imagen-pokemon" alt="Imagen de ' . $valor['nombre'] . '" src="' .
            $valor['sprite'] . '" /></td>
        <td>' . $num . '</td>
        <td nombre="' . $valor['nombre'] . '">' . $valor['nombre'] . '</td>
        <td>';
        foreach ($valor['tipos'] as $tipo) {
            $output .= '<img loading="lazy" class="tabla-imagen-tipo" src="' . get_bloginfo('wpurl') . "/wp-content/plugins/aopokedex/pokemon_types/" . $tipo . '.svg" />';
        }
        $output .= '</td></tr>';
    }

    $output .= '
            </tbody>
        </table>';
    return $output;
}

add_shortcode("filtroPokemon", "filtro_pokemon");