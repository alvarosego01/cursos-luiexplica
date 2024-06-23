<?php

define('THEME_VERSION', wp_get_theme()->get('Version'));

define('ROOT_PATH', get_stylesheet_directory_uri());

require get_stylesheet_directory() . '/functions/index.php';

function child_theme_enqueue_styles()
{

    $parent_style = 'BuddyBoss Theme'; // Este es el nombre del estilo del tema padre.

    wp_enqueue_style($parent_style, get_template_directory_uri() . '/style.css');
    wp_enqueue_style(
        'child-style',
        ROOT_PATH . '/style.css',
        array($parent_style),
        wp_get_theme()->get('Version')
    );
}
add_action('wp_enqueue_scripts', 'child_theme_enqueue_styles');
