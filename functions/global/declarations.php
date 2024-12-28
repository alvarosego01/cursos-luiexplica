<?php

function init_scripts_styles()
{

    wp_enqueue_style('Main.Css', ROOT_PATH . '/dist/styles/main.css', false, THEME_VERSION);

    // wp_enqueue_script('Main.js', get_stylesheet_directory_uri() . '/dist/scripts/main.js', ['jquery'], wp_get_theme()->get('Version'), true);

}
add_action('wp_enqueue_scripts', 'init_scripts_styles');


/* function redirect_to_login_if_not_logged_in() {
    if (!is_user_logged_in() && !is_page(array('wp-login.php', 'registro'))) {
        wp_redirect('/cursos-lui/wp-login.php');
        exit();
    }
}
add_action('template_redirect', 'redirect_to_login_if_not_logged_in');

 */

function setTypeUrl()
{
    if ($_SERVER['SERVER_NAME'] == 'localhost') {
        return '/cursos-lui';
    } else {
        return '';
    }
}

function redirect_to_login_if_not_logged_in()
{
    global $pagenow;

    if (!is_user_logged_in()) {

        $requested_url = $_SERVER['REQUEST_URI'];
        $redirect = setTypeUrl() . '/wp-login.php';

        $slug = trim(parse_url($requested_url, PHP_URL_PATH), '/');
        $slug = strtolower($slug);


        if (strpos($slug, 'public/') !== false || strpos($slug, 'landing/') !== false || strpos($slug, 'lost-password/') === false) {
            return;
            exit();
        }
        // if (strpos($slug, 'public/') !== false) {
        //     // wp_redirect($redirect);
        //     return;
        // exit();
        // }

        // if (($pagenow != 'wp-login.php') && !is_page('registro') && strpos($slug, '/activate') === false) {
        //     wp_redirect($redirect);
        //     exit();
        // }

        if ($pagenow != 'wp-login.php' && $slug !== 'registro' && strpos($slug, 'activate') === false) {
            wp_redirect($redirect);
            exit();
        }
    }
}
add_action('template_redirect', 'redirect_to_login_if_not_logged_in');


function render_custom_lost_password_form()
{
    ob_start();

    $key = isset($_GET['key']) ? sanitize_text_field($_GET['key']) : '';
    $login = isset($_GET['login']) ? sanitize_text_field($_GET['login']) : '';
    $action = isset($_GET['action']) ? sanitize_text_field($_GET['action']) : '';

    if ($action === 'newaccount') {

        echo '<h1>Nueva cuenta</h1>';

    }

    if (!empty($key) && !empty($login)) {
        $user = check_password_reset_key($key, $login);

        if (is_wp_error($user)) {
            echo '<p>' . __('El enlace no es válido o ha caducado. Por favor, solicita un nuevo enlace.', 'woocommerce') . '</p>';
        } else {
            echo '<div class="woocommerce-reset-password">';
            wc_get_template(
                'myaccount/form-reset-password.php',
                array(
                    'key'   => $key,
                    'login' => $login,
                )
            );
            echo '</div>';
        }
    } else {
        echo '<p>' . __('Parámetros inválidos. Por favor, revisa tu enlace.', 'woocommerce') . '</p>';
    }
    return ob_get_clean();
}
add_shortcode('custom_lost_password', 'render_custom_lost_password_form');
