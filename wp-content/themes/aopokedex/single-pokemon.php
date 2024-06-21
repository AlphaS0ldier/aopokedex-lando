<?php
/*
 * Template Post Pokemon
 */

if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly.
}

get_header();

global $post;
$custom = get_post_custom($post->ID);

$nombre = $custom["_name"][0];
$sprite = $custom["_sprite"][0];
$pokedex_nacional = $custom["_pokedex_national"][0];
$pokedex_regional = get_post_meta($post->ID, "_pokedex_regional")[0];
$descripcion = $custom["_description"][0];
$especie = $custom["_genus"][0];
//$evolucion = $custom["evolucion"][0];//array
$habilidades = get_post_meta($post->ID, "_abilities")[0];
$estadisticas = get_post_meta($post->ID, "_stats")[0];
//$ventajas_desventajas = $custom["ventajas_desventajas"][0];//array
$movimientos = get_post_meta($post->ID, "_moves")[0];
$tipos = get_post_meta($post->ID, "_types")[0];

$pokemon_antes = get_posts(
    array(
        'post_type' => 'pokemon',
        'meta_key' => '_pokedex_national',
        'meta_value' => $pokedex_nacional - 1,
        'post_status' => 'publish',
        'posts_per_page' => 1,
    )
);
if (!empty($pokemon_antes)) {
    $custom = get_post_custom($pokemon_antes[0]->ID);

    $pokemon_antes_nombre = $custom["_name"][0];

    $pokemon_antes_num_nacional = $custom["_pokedex_national"][0];
}

$pokemon_despues = get_posts(
    array(
        'post_type' => 'pokemon',
        'meta_key' => '_pokedex_national',
        'meta_value' => $pokedex_nacional + 1,
        'post_status' => 'publish',
        'posts_per_page' => 1,
    )
);
if (!empty($pokemon_despues)) {
    $custom = get_post_custom($pokemon_despues[0]->ID);

    $pokemon_despues_nombre = $custom["_name"][0];

    $pokemon_despues_num_nacional = $custom["_pokedex_national"][0];
}

?>
<main class="contenedor fuente-texto fuente-tamano-texto">
    <div class="pokemon-pagina-botones">
        <div>
            <?php
            if (!empty($pokemon_antes)) {
                echo '<a class="contenedor-datos" href="';
                echo $pokemon_antes[0]->guid . '"> &lt;&lt; ';
                echo $pokemon_antes_num_nacional . "  " . $pokemon_antes_nombre;
                echo '</a>';
            }
            ?>
        </div>
        <div>
            <?php
            if (!empty($pokemon_despues)) {
                echo '<a class="contenedor-datos" href="';
                echo $pokemon_despues[0]->guid . '">';
                echo $pokemon_despues_num_nacional . "  " . $pokemon_despues_nombre;
                echo ' &gt;&gt;</a>';
            }
            ?>
        </div>
    </div>
    <div class="pokemon-pagina-descripcion pokemon-pagina-contenedor-datos">
        <h2 class="anchura-header contenedor-datos fuente-header pokemon-header">
            <?php echo $nombre ?>
        </h2>
        <div>
            <div>
                <img class="contenedor-datos pokemon-pagina-imagen  " alt="Imagen de <?php echo $nombre ?>"
                    src="<?php echo $sprite ?>" />
            </div>
            <div class="contenedor-datos" id="pokemon-pagina-descripcion-datos">
                <h3 class="fuente-titulo-pokemon-datos"><?php _e("POKÉMON DATA", "aopokedex") ?></h3>
                <div class="contenedor-pokemon-datos">
                    <div>
                        <h4 class="fuente-header-pokemon"><b><?php _e("NATIONAL POKÉDEX", "aopokedex") ?></b></h4>
                        <p class="fuente-header-pokemon"><?php echo $pokedex_nacional ?></p>
                    </div>
                    <div id="pokemon-num-pokedex-regional">
                        <?php
                        if (is_array($pokedex_regional)) {
                            foreach ($pokedex_regional as $nombre_region => $num_region) {
                                echo '<div>
                            <h4 class="fuente-header-pokemon"><b>' . strtoupper($nombre_region) . ' POKÉDEX</b></h4>
                            <p class="fuente-header-pokemon">' . $num_region . '</p>
                        </div>';
                            }
                        }
                        ?>
                    </div>
                </div>
                <div>
                    <h4 class="fuente-header-pokemon"><b><?php _e("TYPES", "aopokedex") ?></b></h4>
                    <div id="pokemon-tipos">
                        <?php
                        foreach ($tipos as $index => $tipo) {
                            echo "<img class='tabla-imagen-tipo' src='" . get_site_url() . "/wp-content/plugins/aopokedex/pokemon_types/" . $tipo . ".svg'>";
                        }
                        ?>
                    </div>
                </div>
                <div class="contenedor-pokemon-datos">
                    <div>
                        <h4 class="fuente-header-pokemon"><b><?php _e("SPECIE", "aopokedex") ?></b></h4>
                        <p class="fuente-header-pokemon"><?php echo $especie ?></p>
                    </div>
                    <div>
                        <h4 class="fuente-header-pokemon"><b><?php _e("ABILITIES", "aopokedex") ?></b></h4>
                        <?php
                        echo "<p class='fuente-header-pokemon'>";
                        foreach ($habilidades as $habilidad => $value) {
                            echo $habilidad . "<br>";
                        }
                        echo "</p>";
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="pokemon-pagina-contenedor-datos" id="pokemon-texto-pokedex">
        <h2 class="anchura-header contenedor-datos fuente-header pokemon-header">
            <?php _e("POKÉDEX ENTRY", "aopokedex") ?>
        </h2>
        <div class="contenedor-datos">
            <p><?php
            echo $descripcion
                ?></p>
        </div>
    </div>
    <div class="pokemon-pagina-contenedor-datos" id="pokemon-estadisticas">
        <h2 class="anchura-header contenedor-datos fuente-header pokemon-header">
            <?php _e("BASE STATS", "aopokedex") ?>
        </h2>
        <div class="contenedor-datos">
            <?php
            $total = 0;
            $i = 1;
            foreach ($estadisticas as $key => $value) {
                $i == 4 ? $i = 1 : "";

                echo $i == 1 ? "<div class='pokemon-dividir-estadistica'>" : "";

                echo "<div class='metro fuente-header-pokemon' >";
                echo "<p><b>" . strtoupper($key) . "</b></p>";
                echo "<p>" . $value . "</p>";
                echo "<div>";
                if ($value / 200 <= 0.2) {
                    echo '<div class="low" style="width:' . (($value / 200) * 100) . '%">';
                } else if ($value / 200 <= 0.40) {
                    echo '<div class="low-medium" style="width:' . (($value / 200) * 100) . '%">';
                } else if ($value / 200 <= 0.60) {
                    echo '<div class="medium" style="width:' . (($value / 200) * 100) . '%">';
                } else if ($value / 200 <= 0.80) {
                    echo '<div class="medium-high" style="width:' . (($value / 200) * 100) . '%">';
                } else {
                    echo '<div class="high" style="width:' . (($value / 200) * 100) . '%">';
                }
                echo "</div>";
                echo "</div></div>";
                echo $i == 3 ? "</div>" : "";


                $i++;

                $total += $value;
            }
            echo "<div class='fuente-header-pokemon'><p><b>TOTAL</b></p><p>" . $total . "</p></div>";
            ?>
        </div>
    </div>
    <div class="pokemon-pagina-contenedor-datos" id="pokemon-movimientos">
        <h2 class="anchura-header contenedor-datos fuente-header pokemon-header"><?php _e("MOVES", "aopokedex") ?>
        </h2>
        <table class="estilo-tabla tabla-anchura">
            <thead>
                <tr>
                    <th>LEVEL</th>
                    <th>NAME</th>
                    <th>POWER</th>
                    <th>TYPE</th>
                </tr>
            </thead>
            <tbody>
                <?php
                foreach ($movimientos as $movimientos => $valor) {
                    echo "<tr>";
                    echo "<td>" . $valor["level"] . "</td>";
                    echo "<td>" . $movimientos . "</td>";
                    echo "<td>" . $valor["power"] . "</td>";
                    echo "<td><img class='tabla-imagen-tipo' src='" . get_site_url() . "/wp-content/plugins/aopokedex/pokemon_types/" . $valor["type"] . ".svg'>";
                    echo "<img class='tabla-imagen-tipo categoria-movimiento' src='" . get_site_url() . "/wp-content/plugins/aopokedex/pokemon_move_categories/" . $valor["damage_class"] . ".png'></td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
    <div class="pokemon-pagina-contenedor-datos" id="pokemon-ventajas">
        <h2 class="anchura-header contenedor-datos fuente-header pokemon-header">
            <?php _e("WEAKNESS BY TYPE FOR", "aopokedex") ?> <?php echo strtoupper($nombre) ?>
        </h2>
        <table class="estilo-tabla tabla-anchura">
            <?php
            $tipos_multiplicador = get_posts(
                array(
                    'post_type' => 'pokemon_type',
                    'post_status' => 'publish',
                    'posts_per_page' => -1,
                )
            );

            $debilidad_1 = get_post_meta(
                get_posts(
                    array(
                        'post_type' => 'pokemon_type',
                        'meta_key' => '_name',
                        'meta_value' => $tipos[0],
                        'post_status' => 'publish',
                        'posts_per_page' => 1,
                    )
                )[0]->ID,
                "_type_weakness"
            )[0];

            $debilidad_2 = [];

            if (!empty($tipos[1])) {
                $debilidad_2 = get_post_meta(
                    get_posts(
                        array(
                            'post_type' => 'pokemon_type',
                            "meta_key" => "_name",
                            "meta_value" => $tipos[1],
                            'post_status' => 'publish',
                            'posts_per_page' => 1,
                        )
                    )[0]->ID,
                    "_type_weakness"
                )[0];
            }

            $tr_tipos_1 = "<tr class='tipos'>";
            $tr_multiplicadores_1 = '<tr class="multiplicadores">';

            $tr_tipos_2 = "<tr class='tipos'>";
            $tr_multiplicadores_2 = '<tr class="multiplicadores">';

            foreach ($tipos_multiplicador as $index => $tipo) {

                $nombre_tipo = get_post_meta($tipo->ID, "_name")[0];

                if ($index + 1 <= 9) {
                    $tr_tipos_1 .= "<td><img class='tabla-imagen-tipo' src='" . get_site_url() .
                        "/wp-content/plugins/aopokedex/pokemon_types/" . $nombre_tipo . ".svg'></td>";

                    $tr_multiplicadores_1 .= "<td><p>";

                    $multiplicador_1 = 1;

                    $multiplicador_2 = 1;

                    foreach ($debilidad_1 as $mult => $value) {
                        $key = array_search($nombre_tipo, $value);
                        if (is_int($key)) {
                            $multiplicador_1 = str_replace("x", "", $mult);
                        }
                    }

                    if (!empty($debilidad_2)) {
                        foreach ($debilidad_2 as $mult => $value) {
                            $key = array_search($nombre_tipo, $value);
                            if (is_int($key)) {
                                $multiplicador_2 = str_replace("x", "", $mult);
                            }
                        }
                    }

                    $tr_multiplicadores_1 .= "x" . $multiplicador_1 * $multiplicador_2 . "</p></td>";


                } else {
                    $tr_tipos_2 .= "<td><img class='tabla-imagen-tipo' src='" . get_site_url() .
                        "/wp-content/plugins/aopokedex/pokemon_types/" . $nombre_tipo . ".svg'></td>";

                    $tr_multiplicadores_2 .= "<td><p>";

                    $multiplicador_1 = 1;

                    $multiplicador_2 = 1;

                    foreach ($debilidad_1 as $mult => $value) {
                        $key = array_search($nombre_tipo, $value);
                        if (is_int($key)) {
                            $multiplicador_1 = str_replace("x", "", $mult);
                        }
                    }

                    if (!empty($debilidad_2)) {
                        foreach ($debilidad_2 as $mult => $value) {
                            $key = array_search($nombre_tipo, $value);
                            if (is_int($key)) {
                                $multiplicador_2 = str_replace("x", "", $mult);
                            }
                        }
                    }

                    $tr_multiplicadores_2 .= "x" . $multiplicador_1 * $multiplicador_2 . "</p></td>";
                }
            }

            $tr_tipos_1 .= "</tr>";
            $tr_multiplicadores_1 .= '</tr>';

            $tr_tipos_2 .= "</tr>";
            $tr_multiplicadores_2 .= '</tr>';

            echo $tr_tipos_1;
            echo $tr_multiplicadores_1;


            echo $tr_tipos_2;
            echo $tr_multiplicadores_2;
            ?>
        </table>
    </div>

</main>
<?php

get_footer();
?>