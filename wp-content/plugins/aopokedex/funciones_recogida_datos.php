<?php 

function llamar_api($url_api)
{

    $response = wp_remote_get($url_api);
    if (is_wp_error($response)) {
        return 'Error al obtener los datos';
    }

    // Decodificar JSON
    $body = wp_remote_retrieve_body($response);
    $data = json_decode($body, true);

    if (json_last_error() !== JSON_ERROR_NONE) {
        echo 'Error al decodificar los datos JSON: ' . json_last_error_msg();
    }

    return $data;
}

function coger_nombre_pokemon($datos)
{

    $nombre = "";

    if (!str_contains($datos, "deoxys")) {
        $nombre = ucwords($datos);
    } else {
        $nombre = ucwords(explode("-", $datos)[0]);
    }

    return $nombre;
}

function coger_pokedex_region_y_generacion($datos)
{

    $result = [];

    $pokedex_regional = [];

    $pokedex_generacion = [];


    foreach ($datos as $numero_pokedex) {

        if ($numero_pokedex["pokedex"]["name"] == "hoenn") {

            $pokedex_regional[$numero_pokedex["pokedex"]["name"]] = $numero_pokedex["entry_number"];

            if (!term_exists($numero_pokedex["pokedex"]["name"], 'pokedex_region')) {

                $pokedex = end(llamar_api($numero_pokedex["pokedex"]["url"])["pokemon_entries"]);

                wp_insert_term(
                    $numero_pokedex["pokedex"]["name"],
                    'pokedex_region',
                    ['description' => $pokedex['entry_number']]
                );

            }

            $pokedex_generacion[] = get_term_by('name', $numero_pokedex["pokedex"]["name"], 'pokedex_region')->term_id;

        } else if ($numero_pokedex["pokedex"]["name"] == "original-johto") {

            $nombre_generacion = explode('-', $numero_pokedex["pokedex"]["name"])[1];

            $pokedex_regional[$nombre_generacion] = $numero_pokedex["entry_number"];

            if (!term_exists($nombre_generacion, 'pokedex_region')) {

                $pokedex = end(llamar_api($numero_pokedex["pokedex"]["url"])["pokemon_entries"]);

                wp_insert_term(
                    $nombre_generacion,
                    'pokedex_region',
                    ['description' => $pokedex['entry_number']]
                );
            }

            $pokedex_generacion[] = get_term_by('name', $nombre_generacion, 'pokedex_region')->term_id;

        } else if ($numero_pokedex["pokedex"]["name"] == "kanto") {

            $pokedex_regional[$numero_pokedex["pokedex"]["name"]] = $numero_pokedex["entry_number"];

            if (!term_exists($numero_pokedex["pokedex"]["name"], 'pokedex_region')) {

                $pokedex = end(llamar_api($numero_pokedex["pokedex"]["url"])["pokemon_entries"]);


                wp_insert_term(
                    $numero_pokedex["pokedex"]["name"],
                    'pokedex_region',
                    ['description' => $pokedex['entry_number']]
                );

            }

            $pokedex_generacion[] = get_term_by('name', $numero_pokedex["pokedex"]["name"], 'pokedex_region')->term_id;

        }
    }

    if (empty($pokedex_regional)) {
        $pokedex_regional = "-";
    }

    $result["regional"] = $pokedex_regional;

    $result["generacion"] = $pokedex_generacion;

    return $result;
}


function coger_descripcion($datos)
{
    $pokemon_descripcion = "";

    $pokemon_descripcion_es = "";

    foreach ($datos as $text) {
        if ($text["version"]["name"] == "emerald") {
            $pokemon_descripcion = $text['flavor_text'];
        }
        if ($text["version"]["name"] == "omega-ruby" && $text["language"]["name"] == "es") {
            $pokemon_descripcion_es = $text['flavor_text'];
        }
    }

    return ["en" => $pokemon_descripcion, "es" => $pokemon_descripcion_es];
}


function coger_especie($datos)
{
    $pokemon_especie = "";

    $pokemon_especie_es = "";

    foreach ($datos as $text) {
        if ($text["language"]["name"] == "en") {
            $pokemon_especie = $text['genus'];
        } else if ($text["language"]["name"] == "es") {
            $pokemon_especie_es = $text['genus'];
        }
    }

    return ["en" => $pokemon_especie, "es" => $pokemon_especie_es];
}


function coger_habilidades($datos)
{

    $habilidades = [];

    $habilidades_es = [];

    foreach ($datos as $habilidad) {


        $nombre_habilidades = llamar_api($habilidad["ability"]["url"])["names"];

        $nombre_habilidad = "";

        foreach ($nombre_habilidades as $nom) {
            if ($nom["language"]["name"] == "es") {
                $nombre_habilidad = $nom["name"];
                break;
            }
        }



        $habilidades_es[ucwords($nombre_habilidad)] = $habilidad["is_hidden"] ? true : false;


        $habilidades[ucwords($habilidad["ability"]["name"])] = $habilidad["is_hidden"] ? true : false;

    }

    return ["en" => $habilidades, "es" => $habilidades_es];
}


function coger_estadisticas($datos)
{
    $estadisticas = [];

    $estadisticas_es = [];

    foreach ($datos as $estadistica) {
        $estadisticas[$estadistica["stat"]["name"]] = $estadistica["base_stat"];

        $nombre_estadisticas = llamar_api($estadistica["stat"]["url"])["names"];

        $nombre_estadistica = "";


        foreach ($nombre_estadisticas as $nom) {
            if ($nom["language"]["name"] == "es") {
                $nombre_estadistica = $nom["name"];
                break;
            }
        }

        $estadisticas_es[$nombre_estadistica] = $estadistica["base_stat"];
    }

    return ["en" => $estadisticas, "es" => $estadisticas_es];
}


function coger_movimientos($datos)
{
    $movimientos = [];

    $movimientos_es = [];

    foreach ($datos as $movimiento) {
        foreach ($movimiento["version_group_details"] as $version) {
            if ($version["version_group"]["name"] == "emerald" || $version["version_group"]["name"] == "ruby-sapphire") {
                if ($version["move_learn_method"]["name"] == "level-up") {
                    $mov_desc = llamar_api($movimiento["move"]["url"]);

                    $nombre_movimiento = "";

                    foreach ($mov_desc["names"] as $mov) {
                        if ($mov["language"]["name"] == "es") {
                            $nombre_movimiento = $mov["name"];
                        }
                    }

                    $movimientos[ucwords($movimiento["move"]["name"])] = [
                        "level" => $version["level_learned_at"],
                        "damage_class" => $mov_desc["damage_class"]["name"],
                        "type" => $mov_desc["type"]["name"],
                        "power" => !empty($mov_desc["power"]) ? $mov_desc["power"] : "-",
                    ];


                    $movimientos_es[ucwords($nombre_movimiento)] = [
                        "level" => $version["level_learned_at"],
                        "damage_class" => $mov_desc["damage_class"]["name"],
                        "type" => $mov_desc["type"]["name"],
                        "power" => !empty($mov_desc["power"]) ? $mov_desc["power"] : "-",
                    ];
                }
            }
        }

    }

    return ["en" => $movimientos, "es" => $movimientos_es];
}

function coger_tipos($datos)
{
    $tipos = [];

    foreach ($datos as $tipo) {
        $tipos[] = $tipo["type"]["name"];

        $tipo_existe = get_posts(
            array(
                'post_type' => 'pokemon_type',
                'meta_key' => '_name',
                'meta_value' => $tipo["type"]["name"],
                'post_status' => 'publish',
                'posts_per_page' => 1,
            )
        );

        if (empty($tipo_existe)) {
            $new_post = array(
                'post_title' => $tipo["type"]["name"],
                'post_content' => "",
                'post_status' => 'publish',
                'post_author' => 1,
                'post_type' => 'pokemon_type',
            );

            $post_id = wp_insert_post($new_post);



            $debilidades_api = llamar_api($tipo["type"]["url"])["damage_relations"];

            $debilidades = [];

            foreach ($debilidades_api["double_damage_from"] as $multiplicador) {
                $debilidades["2x"][] = $multiplicador["name"];
            }

            foreach ($debilidades_api["half_damage_from"] as $multiplicador) {
                $debilidades["0.5x"][] = $multiplicador["name"];
            }

            foreach ($debilidades_api["no_damage_from"] as $multiplicador) {
                $debilidades["0x"][] = $multiplicador["name"];
            }

            add_post_meta($post_id, "_name", $tipo["type"]["name"]);

            add_post_meta($post_id, "_type_weakness", $debilidades);

        }
    }

    return $tipos;
}