<?php
/**
 * Theme functions and definitions.
 *
 * For additional information on potential customization options,
 * read the developers' documentation:
 *
 * https://developers.elementor.com/docs/hello-elementor-theme/
 *
 * @package HelloElementorChild
 */

require_once(__DIR__."/calculos.php");

global $post;

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

define('AOPOKEDEX', '1.0.0');

function poner_en_cola_mis_estilos()
{
    $id_padre_tema = 'hello-elementor-child-style';
    wp_enqueue_style(
        $id_padre_tema,
        get_stylesheet_directory_uri() . '/style.css',
        [
            'hello-elementor-theme-style',
        ],
        AOPOKEDEX
    );
}

add_action('wp_enqueue_scripts', 'poner_en_cola_mis_estilos', 20);

function configurar_traducciones()
{
    $path = get_stylesheet_directory() . '/languages';
    load_child_theme_textdomain('aopokedex', $path);
}
add_action('after_setup_theme', 'configurar_traducciones');

function incluir_js()
{
    if (str_contains(get_the_title(), "Pokedex")) {
        wp_enqueue_script('pokedex', get_stylesheet_directory_uri() . '/pokedex.js', array('jquery'));
        wp_localize_script(
            'pokedex',
            'my_ajax_object',
            array(
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ajax-nonce')
            )
        );
    }

    if (str_contains(get_the_title(), "EV Calculator") || str_contains(get_the_title(), "Damage Calculator")) {
        wp_enqueue_script('calculador_evs', get_stylesheet_directory_uri() . '/calculador_evs.js', array('jquery'));
        wp_localize_script(
            'calculador_evs',
            'my_ajax_object',
            array(
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ajax-nonce')
            )
        );
    }

    if (str_contains(get_the_title(), "Damage Calculator")) {
        wp_enqueue_script('calculador_danos', get_stylesheet_directory_uri() . '/calculador_danos.js', array('jquery'));
        wp_localize_script(
            'calculador_danos',
            'my_ajax_object',
            array(
                'url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('ajax-nonce')
            )
        );
    }
}

add_action('wp_enqueue_scripts', 'incluir_js');

function redireccionar_pagina_pokemon()
{
    if (!wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
        die('Pillín, no metas nada raro eh');
    }

    $pokemon = get_posts(
        array(
            'post_type' => 'pokemon',
            'post_status' => 'publish',
            'meta_key' => '_name',
            'meta_value' => $_POST['pokemon_nombre'],
            'order' => 'ASC',
            'posts_per_page' => 1,
        )
    );

    $result["url"] = $pokemon[0]->guid;

    wp_send_json($result);
    wp_die();
}

add_action('wp_ajax_redireccionar_pagina_pokemon', 'redireccionar_pagina_pokemon');
add_action('wp_ajax_nopriv_redireccionar_pagina_pokemon', 'redireccionar_pagina_pokemon');

function filtrar_pokemon_pokedex()
{
    if (!wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
        die('Pillín, no metas nada raro eh');
    }

    if ($_POST['region'] == __("National", "aopokedex")) {
        $_POST['region'] = "";
    }

    $result["tabla"] = do_shortcode('[filtroPokemon filtro="' . strtolower($_POST['region']) . '"]');

    wp_send_json($result);
    wp_die();
}

add_action('wp_ajax_filtrar_pokemon_pokedex', 'filtrar_pokemon_pokedex');
add_action('wp_ajax_nopriv_filtrar_pokemon_pokedex', 'filtrar_pokemon_pokedex');





function mostrar_pokemon()
{
    if (!wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
        die('Pillín, no metas nada raro eh');
    }

    $pokemon = get_posts(
        array(
            'post_type' => 'pokemon',
            'post_status' => 'publish',
            'meta_key' => '_name',
            'meta_value' => $_POST['name'],
            'order' => 'ASC',
            'posts_per_page' => -1,
        )
    );

    $img = get_post_meta($pokemon[0]->ID, '_sprite');

    $result["img"] = $img[0];

    wp_send_json($result);
    wp_die();
}

add_action('wp_ajax_mostrar_pokemon', 'mostrar_pokemon');
add_action('wp_ajax_nopriv_mostrar_pokemon', 'mostrar_pokemon');

function calcular_evs_calculador_evs()
{
    if (!wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
        die('Pillín, no metas nada raro eh');
    }

    $estadisticas = $_POST["evs"];

    $pokemon = get_posts(
        array(
            'post_type' => 'pokemon',
            'post_status' => 'publish',
            'meta_key' => '_name',
            'meta_value' => $_POST['name'],
            'order' => 'ASC',
            'posts_per_page' => -1,
        )
    );

    $estadisticas_base = get_post_meta($pokemon[0]->ID, "_stats")[0];

    $resultado = [];


    foreach ($estadisticas_base as $nombre => $valor) {
        $evs_resultado = calcular_ev($nombre, $valor, $estadisticas[$nombre], $_POST["nivel"]);
        $resultado[$nombre] = round($evs_resultado);
    }

    $resultado_html = "";

    foreach ($resultado as $key => $value) {
        $resultado_html .= "<div><p>" . strtoupper($key) . "</p><p>" . $value . "</p></div>";
    }

    $result["result"] = $resultado_html;


    wp_send_json($result);
    wp_die();
}

add_action('wp_ajax_calcular_evs_calculador_evs', 'calcular_evs_calculador_evs');
add_action('wp_ajax_nopriv_calcular_evs_calculador_evs', 'calcular_evs_calculador_evs');



function mostrar_calculador_danos_inputs()
{
    if (!wp_verify_nonce($_POST['nonce'], 'ajax-nonce')) {
        die('Pillín, no metas nada raro eh');
    }

    $datos_movimiento = '<div id="movimiento_datos" class="contenedor-datos">
      <p class="fuente-texto fuente-tamano-texto"><span>' . __("Name", "aopokedex") . '</span><span id="movimiento_id"></span></p>
      <p class="fuente-texto fuente-tamano-texto"><span>' . __("Power", "aopokedex") . '</span><span id="movimiento_poder"></span></p>
      <p class="fuente-texto fuente-tamano-texto"><span>' . __("Type", "aopokedex") . '</span><img id="movimiento_tipo"></p>
      <p class="fuente-texto fuente-tamano-texto"><span>' . __("Category", "aopokedex") . '</span><img id="movimiento_categoria"></p>
    </div>
  </div>';

    $cont_imp = '<div id="pokemon_dano_batalla"><img width="250" height="auto" src="' . get_bloginfo('wpurl') . '/wp-content/plugins/aopokedex/sword.svg"></div>
  <div id="pokemon_defensor_datos_contenedor">
    <img class="estilo-pokemon-imagen contenedor-datos" id="pokemon_defensor_datos">
  </div>';


    $pokemon = get_posts(
        array(
            'post_type' => 'pokemon',
            'post_status' => 'publish',
            'meta_key' => '_name',
            'meta_value' => "bulbasaur",
            'order' => 'ASC',
            'posts_per_page' => 1,
        )
    );

    $estadisticas = get_post_meta($pokemon[0]->ID, "_stats")[0];

    $cont_inp = '<div id="calcular_dano_input_boost">';
    foreach ($estadisticas as $nombre => $valor) {

        if ($nombre == "hp" || $nombre == "PS") {
            continue;
        }

        $cont_inp .= '<div class="fuente-texto fuente-tamano-texto"><h3 class="fuente-tamano-texto">Boost ' . strtoupper($nombre) . '</h3>';

        for ($i = 1; $i <= 6; $i++) {
            $cont_inp .= '<label class="fuente-texto fuente-tamano-texto"
        >' . $i . '<input type="radio" id="' . $i . '" name="' . $nombre . '" /><span
          class="checkmark"
        ></span></label
      >';
        }

        $cont_inp .= '</div>';
    }
    $cont_inp .= "</div>";


    $result["movimiento_datos"] = $datos_movimiento;

    $result["contenedor_imagenes"] = $cont_imp;

    $result["contenedor_inputs"] = $cont_inp;

    wp_send_json($result);
    wp_die();
}

add_action('wp_ajax_mostrar_calculador_danos_inputs', 'mostrar_calculador_danos_inputs');
add_action('wp_ajax_nopriv_mostrar_calculador_danos_inputs', 'mostrar_calculador_danos_inputs');

function mostrar_movimiento_y_pokemon_defensor()
{

    $pokemon = get_posts(
        array(
            'post_type' => 'pokemon',
            'post_status' => 'publish',
            'meta_key' => '_name',
            'meta_value' => $_POST['name'],
            'order' => 'ASC',
            'posts_per_page' => -1,
        )
    );


    $pokemon_movimientos = get_post_meta($pokemon[0]->ID, "_moves")[0];


    $movimientos = '<select id="movimientos" class="select-estilo fuente-texto fuente-tamano-texto">
<option hidden>' . __("Select a Move", "aopokedex") . '</option>';

    foreach ($pokemon_movimientos as $movimiento => $valor) {

        if ($valor['damage_class'] != "status" && is_integer($valor["power"])) {

            $movimientos .= '<option power="' . $valor['power'] . '"
             damage_class="' . $valor["damage_class"] . '" type="' . $valor['type'] . '" damage_class_img="' .
                get_site_url() . "/wp-content/plugins/aopokedex/pokemon_move_categories/" . $valor["damage_class"] . '.png' .
                '" type_img="' . get_site_url() . "/wp-content/plugins/aopokedex/pokemon_types/" . $valor['type'] . '.svg"' .
                ' >' . $movimiento . "</option>";

        }
    }
    $movimientos .= "</select>";

    $result["movimientos"] = $movimientos;

    $select_pokemon_defensor = '<select class="select-estilo fuente-texto fuente-tamano-texto" id="select-pokemon-defensor">
        <option hidden>' . __("Defending Pokémon", "aopokedex") . '</option>';

    $pokemons = get_posts(
        array(
            'post_type' => 'pokemon',
            'post_status' => 'publish',
            'order' => 'ASC',
            'posts_per_page' => -1,
        )
    );


    foreach ($pokemons as $pokemon_defensor) {
        $pokemon_datos = get_post_custom($pokemon_defensor->ID);

        $nombre = $pokemon_datos['_name'][0];

        $select_pokemon_defensor .= "<option>" . $nombre . "</option>";
    }

    $select_pokemon_defensor .= '</select>';

    $result["select_pokemon_defensor"] = $select_pokemon_defensor;

    wp_send_json($result);
    wp_die();
}

add_action('wp_ajax_mostrar_movimiento_y_pokemon_defensor', 'mostrar_movimiento_y_pokemon_defensor');
add_action('wp_ajax_nopriv_mostrar_movimiento_y_pokemon_defensor', 'mostrar_movimiento_y_pokemon_defensor');


function calcular_dano_entre_pokemon()
{

    $pokemon_atacante = get_posts(
        array(
            'post_type' => 'pokemon',
            'post_status' => 'publish',
            'meta_key' => '_name',
            'meta_value' => $_POST["pokemon_atq"],
            'order' => 'ASC',
            'posts_per_page' => 1,
        )
    );

    $estadisticas_base_atacante = get_post_meta($pokemon_atacante[0]->ID, "_stats")[0];


    $pokemon_defensor = get_posts(
        array(
            'post_type' => 'pokemon',
            'post_status' => 'publish',
            'meta_key' => '_name',
            'meta_value' => $_POST['pokemon_def'],
            'order' => 'ASC',
            'posts_per_page' => -1,
        )
    );

    $estadisticas_base_defensor = get_post_meta($pokemon_defensor[0]->ID, "_stats")[0];

    $ataque = 0;

    $defensa = 0;

    $boost = 1;

    if ($_POST["movimiento"]["categoria"] == "physical") {
        if ($_POST["boost"]["attack"]) {
            $boost = ((int) ($_POST["boost"]["attack"])) / 2;
        }
        $ataque = calcular_ev("", $estadisticas_base_atacante["attack"], $_POST["evs"]["attack"], (int) $_POST["nivel"]) * $boost;
        $defensa = calcular_ev("", $estadisticas_base_defensor["defense"], 0, 100);
    } else {
        if ($_POST["boost"]["special-attack"]) {
            $boost += ((int) ($_POST["boost"]["special-attack"])) / 2;
        }
        $ataque = calcular_ev("", $estadisticas_base_atacante["special-attack"], $_POST["evs"]["special-attack"], (int) $_POST["nivel"]) * $boost;
        $defensa = calcular_ev("", $estadisticas_base_defensor["special-defense"], 0, 100);
    }

    $stab = 1;

    $pokemon_atacante_tipos = get_post_meta($pokemon_atacante[0]->ID, "_types")[0];

    $tipo_movimiento = $_POST["movimiento"]["tipo"];

    foreach ($pokemon_atacante_tipos as $pokemon_atacante_tipo) {
        if ($tipo_movimiento == $pokemon_atacante_tipo) {
            $stab = 1.5;
        }
    }

    $pokemon_defensor_tipos = get_post_meta($pokemon_defensor[0]->ID, "_types")[0];

    $debilidad = 1;

    foreach ($pokemon_defensor_tipos as $pokemon_defensor_tipo) {
        $consulta_tipo = get_posts(
            array(
                'post_type' => 'pokemon_type',
                'post_status' => 'publish',
                'meta_key' => '_name',
                'meta_value' => $pokemon_defensor_tipo,
                'order' => 'ASC',
                'posts_per_page' => -1,
            )
        );

        $tipo_defensor = get_post_meta($consulta_tipo[0]->ID, "_type_weakness")[0];

        foreach ($tipo_defensor as $mult => $value) {
            $key = array_search($tipo_movimiento, $value);

            if (is_int($key)) {
                $debilidad *= (float) str_replace("x", "", $mult);
            }
        }
    }

    $result["calculo"] = ["atq" => $ataque, "def" => $defensa, "lvl" => (int) $_POST["nivel"], "weak" => $debilidad, "move_power" => (int) $_POST["movimiento"]["poder"], "stab" => $stab, "boosts" => $boost];

    $result["post"] = $_POST;

    $dano_final = calcular_dano($ataque, $defensa, (int) $_POST["nivel"], $debilidad, (int) $_POST["movimiento"]["poder"], $stab);

    if (intval($dano_final) == 0 && $debilidad != 0) {
        $dano_final = 1;
    }


    if (get_bloginfo("language") == "en-GB") {
        $texto_resultado = sprintf("A level %s %s will do %s damage to a level 100 %s", $_POST["nivel"], $_POST["pokemon_atq"], round($dano_final), $_POST["pokemon_def"]);
    } else if (get_bloginfo("language") == "es") {
        $texto_resultado = sprintf("%s de nivel %s hará %s de daño a %s de nivel 100", $_POST["pokemon_atq"], $_POST["nivel"], round($dano_final), $_POST["pokemon_def"]);
    }


    $result["result"] = "<div id='resultado_dano' class='contenedor-datos fuente-texto fuente-tamano-texto'><p>
    " . $texto_resultado . "</p></div>";

    wp_send_json($result);
    wp_die();
}

add_action('wp_ajax_calcular_dano_entre_pokemon', 'calcular_dano_entre_pokemon');
add_action('wp_ajax_nopriv_calcular_dano_entre_pokemon', 'calcular_dano_entre_pokemon');

function region_select_dropdown($tag, $unused)
{
    if ($tag['name'] != 'region') {
        return $tag;
    }

    $tag['raw_values'] = [];
    $tag['labels'] = [];

    $regiones = get_terms([
        'taxonomy' => 'pokedex_region',
        'hide_empty' => false,
        'order' => 'DESC',

    ]);

    foreach ($regiones as $region) {
        $tag['raw_values'][] = ucwords($region->name);
        $tag['labels'][] = ucwords($region->name);
    }

    $pipes = new WPCF7_Pipes($tag['raw_values']);
    $tag['values'] = $pipes->collect_befores();
    $tag['pipes'] = $pipes;

    return $tag;
}

add_filter('wpcf7_form_tag', 'region_select_dropdown', 10, 2);


function pokemon_select_dropdown($tag, $unused)
{
    if ($tag['name'] != 'pokemon') {
        return $tag;
    }



    $tag['raw_values'] = [];
    $tag['labels'] = [];

    $pokemons = get_posts(
        array(
            'post_type' => 'pokemon',
            'post_status' => 'publish',
            'order' => 'ASC',
            'posts_per_page' => -1,
        )
    );

    foreach ($pokemons as $pokemon) {
        $nombre = get_post_meta($pokemon->ID, "_name")[0];
        $tag['raw_values'][] = $nombre;
        $tag['labels'][] = $nombre;
    }

    $pipes = new WPCF7_Pipes($tag['raw_values']);
    $tag['values'] = $pipes->collect_befores();
    $tag['pipes'] = $pipes;

    return $tag;
}

add_filter('wpcf7_form_tag', 'pokemon_select_dropdown', 10, 2);
