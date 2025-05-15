<?php

function force_product_on_custom_checkout_page() {

    // produccion
    $product_id = 11899;
    // local
    // $product_id = 11603;

    // Verificar si estamos en la página correcta
    if (is_page('asistente-pro-cart')) {

        // Verificar si el producto ya está en el carrito
        $product_in_cart = false;
        foreach (WC()->cart->get_cart() as $cart_item) {
            if ($cart_item['product_id'] == $product_id) {
                $product_in_cart = true;
                break;
            }
        }

        // Si el producto no está en el carrito, lo añadimos
        if (!$product_in_cart) {
            WC()->cart->empty_cart(); // Limpiar el carrito
            WC()->cart->add_to_cart($product_id);

            // Redirigir para evitar problemas de caché
            wp_safe_redirect(get_permalink());
            exit();
        }

    }
}
add_action('template_redirect', 'force_product_on_custom_checkout_page');
