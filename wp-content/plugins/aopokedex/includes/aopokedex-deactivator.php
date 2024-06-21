<?php

global $aopokedex_version;
$aopokedex_version = '1.0';

/**
 * Fired during plugin deactivation.
 *
 * This class defines all code necessary to run during the plugin's deactivation.
 *
 * @since      1.0.0
 * @package    Aopokedex
 * @subpackage aopokedex/includes
 * @author     m1chaelD
 */

class Aopokedex_Deactivator
{

    static function deactivate()
    {
        delete_option('aopokedex_version');
    }
}