<?php

/*
Plugin Name: Lista de Pokémon
Description: Plugin para el listado de Pokémon con filtrado
Version: 1.0
Author: m1chaelD
*/

function lista_pokemon()
{
  $output = '<div class="contenedor">
  <div class="contenedor-select">
    <div class="select-estilo-pokedex">
      <select class="select-estilo fuente-texto fuente-tamano-texto">
        <option hidden>'.__("Select the generation","lista-pokemon").'</option>
    ';

  $regiones = get_terms([
    'taxonomy' => 'pokedex_region',
    'hide_empty' => false,
    'order' => 'DESC',

  ]);

  foreach ($regiones as $region) {
    $output .= "<option>" . ucwords($region->name) . "</option>";
  }

  $output .= "<option>".__("National","lista-pokemon")."</option>";

  $output .= '
        </select>
      </div>
    </div>
    <div id="tabla-pokedex" class="contenedor-tabla">
        <h2 id="pokedex-titulo-generacion" class="contenedor-datos fuente-header-dos-tamano fuente-header">'.
        __("Generation","lista-pokemon").'</h2>
        <div id="loading">
          <img src="' .
          get_bloginfo('wpurl') .
          '/wp-content/plugins/aopokedex/loading.svg">
        </div>';

  //$output .= do_shortcode('[filtroPokemon]');

  $output .= '</div>
  </div>';

  return $output;
}

add_shortcode("listaPokemon", "lista_pokemon");

?>