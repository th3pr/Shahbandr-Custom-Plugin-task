<?php
/*
Plugin Name: Shahbandr Custom Plugin
Description: ⁠add a new custom field to the woo product named (main_price), replace the woocommerce price coming from the api with the newly added custom field (main_price)
Version: 1.0
Author: Mohamed A. Bahnsawy
Author URI: https://www.linkedin.com/in/bahnsawy/
*/

// ⁠add a new custom field to the woo product named (main_price)
function add_main_price_to_products() {
    woocommerce_wp_text_input(
        array(
            'id' => 'main_price',
            'label' => __('Main Price', 'woocommerce')  . ' (' . get_woocommerce_currency_symbol() . ')',
            'placeholder' => '',
            // I set it here to Number istead of text because I don't add any validation for this field.
            'type' => 'number'
        )
    );
}
add_action('woocommerce_product_options_general_product_data', 'add_main_price_to_products');


// Save the New main_price
function save_main_price($post_id) {
    // ternary operator to check if the main_price isset or not
    $main_price_value = isset($_POST['main_price']) ? $_POST['main_price'] : ''; 
    update_post_meta($post_id, 'main_price', esc_attr($main_price_value));
}
add_action('woocommerce_process_product_meta', 'save_main_price');


// replace the woocommerce price coming from the api with the newly added custom field (main_price)
function replace_woo_api_price($response, $object, $request) {
    $main_price = get_post_meta($object->get_id(), 'main_price', true);
    // return $main_price;
    $response->data['price'] = $main_price ? $main_price : $response->data['price'];
    return $response;
}
add_filter('woocommerce_rest_prepare_product_object', 'replace_woo_api_price', 10, 3);
