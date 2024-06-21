<?php

/*
Plugin Name: Noticias Pokémon
Description: Plugin para el listado de noticias de Pokémon
Version: 1.0
Author: m1chaelD
*/

function noticias_pokemon()
{

    $news = get_posts(
        array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'category_name' => 'news',
            'posts_per_page' => 3,
        )
    );

    $output .= "<div class='contenedor'>";

    foreach ($news as $new) {

        $imagen = wp_get_attachment_image_src(get_post_thumbnail_id($new->ID), 'single-post-thumbnail')[0];

        $imagen ? $imagen : $imagen = "http://aopokedex.site/wp-content/uploads/2024/05/pokeball-figma.svg";

        $output .= "<div class='contenedor-noticia'>";

        $output .= "<div><img src='" . $imagen . "'></div>";

        $output .= '<div class="contenedor-datos contenedor-noticia-datos fuente-texto">' . $new->post_content;

        $output .= "<a href='" . get_permalink($new->ID) . "'>".__("Read More","noticias-pokemon")."</a>";

        $output .= '</div>';

        $output .= "</div>";


    }

    $output .= "</div>";

    return $output;
}

add_shortcode("noticiasPokemon", "noticias_pokemon");

?>