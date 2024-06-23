<?php


function init_scripts_styles()
{
    wp_enqueue_style('Main.Css', ROOT_PATH . '/dist/styles/main.css', false, wp_get_theme()->get('Version'));

    // wp_enqueue_script('Main.js', get_stylesheet_directory_uri() . '/dist/scripts/main.js', ['jquery'], wp_get_theme()->get('Version'), true);

}
add_action('wp_enqueue_scripts', 'init_scripts_styles');
