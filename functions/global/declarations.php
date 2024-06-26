<?php


function init_scripts_styles()
{
    wp_enqueue_style('Main.Css', ROOT_PATH . '/dist/styles/main.css', false, wp_get_theme()->get('Version'));

    // wp_enqueue_script('Main.js', get_stylesheet_directory_uri() . '/dist/scripts/main.js', ['jquery'], wp_get_theme()->get('Version'), true);

}
add_action('wp_enqueue_scripts', 'init_scripts_styles');


function redirect_to_login_if_not_logged_in() {
    if (!is_user_logged_in() && !is_page(array('wp-login.php', 'registro'))) {
        wp_redirect('/cursos-lui/wp-login.php');
        exit();
    }
}
add_action('template_redirect', 'redirect_to_login_if_not_logged_in');
