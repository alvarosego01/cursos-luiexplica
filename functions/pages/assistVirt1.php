<?php


function force_product_on_custom_checkout_page()
{

    $product_id = 11899;

    if (!is_page('asistente-pro-cart')) {

    WC()->cart->empty_cart();
    WC()->cart->add_to_cart($product_id);

    }

}
add_action('template_redirect', 'force_product_on_custom_checkout_page');

?>
