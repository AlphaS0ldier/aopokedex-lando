<?php

global $aopokedex_version;
$aopokedex_version = '1.0';

/**
 * Fired during plugin activation.
 *
 * This class defines all code necessary to run during the plugin's activation.
 *
 * @since      1.0.0
 * @package    Aopokedex
 * @subpackage aopokedex/includes
 * @author     m1chaelD
 */

class Aopokedex_Activator
{

    static function activate()
    {

        global $aopokedex_version;

        add_option('aopokedex_version', $aopokedex_version);

        //if (!post_type_exists("pokemon")) {

        add_action('init', 'crear_tipo_post_pokemon');

        add_meta_box("pokemon_metabox_id", __('Pokemon Data', 'aopokedex'), "pokemon_metabox", "pokemon", "normal", "low");

        add_action('save_post', 'pokemon_guardar_datos_post_meta');
        //}

    }

    function crear_tipo_post_pokemon()
    {

        $labels = array(
            'name' => __('Pokemon', 'aopokedex'),
            'singular_name' => __('pokemon', 'aopokedex'),
            'add_new' => __('Add New', 'aopokedex'),
            'add_new_item' => __('Add New Pokemon', 'aopokedex'),
            'edit_item' => __('Edit Pokemon', 'aopokedex'),
            'new_item' => __('New Pokemon', 'aopokedex'),
            'view_item' => __('View Pokemon', 'aopokedex'),
            'search_items' => __('Search Pokemon', 'aopokedex'),
        );

        $args = array(
            'labels' => $labels,
            'has_archive'   => true,
            'public' => true,
            'publicly_queryable' => true,
            'show_ui' => true,
            'show_in_menu' => true,
            'show_in_rest' => true,
            'menu_icon' => __DIR__ . "../../../uploads/2024/04/logo-rotated-1.png",
            'supports' => array('title', 'editor', 'thumbnail', 'revisions', 'custom-fields', 'revisions'),
            'can_export' => true
        );

        register_post_type('pokemon', $args);

    }

    function pokemon_metabox()
    {
        global $post;
        $custom = get_post_custom($post->ID);
        $nombre = $custom["nombre"][0];//string
        $pokedex_nacional = $custom["pokedex_nacional"][0];//int
        $pokedex_regional = $custom["pokedex_regional"][0];//int
        $evolucion = $custom["evolucion"][0];//array
        $habilidades = $custom["habilidades"][0];//array
        $estadisticas = $custom["estadisticas"][0];//array
        $ventajas_desventajas = $custom["ventajas_desventajas"][0];//array
        $movimientos = $custom["movimientos"][0];//array
        $sprite = $custom["sprite"][0];//string
        $tipos = $custom["tipos"][0];//array
        ?>
        <p><label>Designed By:</label><br />
            <textarea cols="50" rows="5" name="designers"><?php echo $nombre; ?></textarea>
        </p>
        <?php
    }


    function pokemon_guardar_datos_post_meta()
    {
        global $post;
        if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
            return;
        }
        update_post_meta($post->ID, "nombre", sanitize_text_field($_POST["nombre"]));
        update_post_meta($post->ID, "pokedex_nacional", sanitize_text_field($_POST["pokedex_nacional"]));
        update_post_meta($post->ID, "pokedex_regional", sanitize_text_field($_POST["pokedex_regional"]));
        update_post_meta($post->ID, "evolucion", sanitize_text_field($_POST["evolucion"]));
        update_post_meta($post->ID, "habilidades", sanitize_text_field($_POST["habilidades"]));
        update_post_meta($post->ID, "estadisticas", sanitize_text_field($_POST["estadisticas"]));
        update_post_meta($post->ID, "ventajas_desventajas", sanitize_text_field($_POST["ventajas_desventajas"]));
        update_post_meta($post->ID, "movimientos", sanitize_text_field($_POST["movimientos"]));
        update_post_meta($post->ID, "sprite", sanitize_text_field($_POST["sprite"]));
        update_post_meta($post->ID, "tipos", sanitize_text_field($_POST["tipos"]));
    }

}