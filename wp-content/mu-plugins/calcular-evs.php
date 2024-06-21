<?php

function calcular_evs($atts)
{

  $default_atts = array(
    "calculador" => '',
  );

  $params = shortcode_atts($default_atts, $atts);


  $output = '
  <div id="loading">
    <img src="' .
    get_bloginfo('wpurl') .
    '/wp-content/plugins/aopokedex/loading.svg">
    <div id="resultado_calculo_contenedor">
      <h1 class="contenedor-datos fuente-texto fuente-header-dos-tamano" >' .
    __("STATS CALCULATED FROM LEVEL", "calcular-evs")
    . ' </h1>
      <div class="contenedor-datos fuente-texto fuente-tamano-texto" id="resultado_calculo_datos">
      </div>
      <div id="resultado_calculo_boton_contenedor">
          <div id="resultado_calculo_boton" class="estilo-boton fuente-tamano-texto fuente-texto">' .
    __("Calculate Again", "calcular-evs")
    . '</div>
      </div>
    </div>
  </div>
  <div class="contenedor">';

  $output .= '
  <p class="contenedor-datos fuente-texto fuente-tamano-texto" id="calculador-evs-instrucciones">' .
    __("Every Pokémon can have a total of 510 EVs and every stat can have up to 252 EVs", "calcular-evs")
    . '</p>
  <div class="contenedor-select">';

  $output .= '<select class="select-estilo fuente-texto fuente-tamano-texto';

  if ($params["calculador"] == "dano") {
    $output .= '';
  } else {
    $output .= ' select-estilo-calculador-evs ';
  }

  $output .= '">';


  if ($params["calculador"] == "dano") {
    $output .= '<option hidden>' . __("Attacking Pokémon", "calcular-evs") . '</option>';
  } else {
    $output .= '<option hidden>' . __("Select a Pokémon", "calcular-evs") . '</option>';
  }

  $pokemons = get_posts(
    array(
      'post_type' => 'pokemon',
      'post_status' => 'publish',
      'order' => 'ASC',
      'posts_per_page' => -1,
    )
  );

  foreach ($pokemons as $pokemon) {
    $pokemon_datos = get_post_custom($pokemon->ID);

    $nombre = $pokemon_datos['_name'][0];

    $output .= "<option>" . $nombre . "</option>";
  }

  $output .= '</select>
  </div>
  <div id="calculador-evs-imagen-contenedor">
    <div>
      <img class="estilo-pokemon-imagen contenedor-datos" id="calculador-evs-pokemon-imagen">
    </div>
  </div>
  <div class="calculador-evs-inputs">';

  $estadisticas = get_post_meta($pokemons[0]->ID, "_stats")[0];

  foreach ($estadisticas as $nombre => $valor) {
    $output .= '<div class="fuente-tamano-texto fuente-texto">
<label>EV ' . strtoupper($nombre) . '</label>
<input id="' . $nombre . '" class="contenedor-datos fuente-tamano-texto fuente-texto" type="number" min="1" max="252">
<p>' . __("Value between 1 to 252", "calcular-evs") . '</p>
</div>';
  }


  $output .= '<div class="fuente-tamano-texto fuente-texto" id="calculador-evs-nivel">
<label>' . __("LEVEL", "calcular-evs") . '</label>
<input class="contenedor-datos fuente-tamano-texto fuente-texto" type="number" min="1" max="100">
<p>' . __("Value between 1 to 100", "calcular-evs") . '</p>
</div>';

  if ($params["calculador"] == "dano") {
    $output .= '<div id="calculador-danos-boton-contenedor">
  <div class="estilo-boton fuente-texto fuente-tamano-texto">' . __("Calculate", "calcular-evs") . '</div>
</div>
</div>
';


  } else {
    $output .= '<div id="calculador-evs-boton-contenedor">
  <div class="estilo-boton fuente-texto fuente-tamano-texto">' . __("Calculate", "calcular-evs") . '</div>
</div>
</div>
';

  }

  $output .= "<p id='texto-seleccionar' style='display:none'>" . __("Select a Pokémon", "calcular-evs") . "</p>";

  $output .= "<p id='texto-ev-superado' style='display:none'>" . __("EVs exceded max value by", "calcular-evs") . "</p>";

  $output .= "<p id='texto-nivel-valido' style='display:none'>" . __("Level is not valid", "calcular-evs") . "</p>";

  $output .= "<p id='texto-ev-minimo' style='display:none'>" . __("Introduce at least 1 EV", "calcular-evs") . "</p>";

  $output .= "<p id='texto-seleccionar-movimiento' style='display:none'>" . __("Select a Move", "calcular-evs") . "</p>";

  return $output . "</div>";
}

add_shortcode("calcularEvs", "calcular_evs");