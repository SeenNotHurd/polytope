<?php
/************* WOOCOMMERCE CHANGES *****************/

// Remove things we don't want.

// Breadcrumb
remove_action( 'woocommerce_before_main_content', 'woocommerce_breadcrumb', 20, 0 );
// Result Count
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_result_count', 20 );
// Catalog sorting
remove_action( 'woocommerce_before_shop_loop', 'woocommerce_catalog_ordering', 30);

/* Changes to shop loop product layout. */

/* Single products */

// Remove add button from current location
remove_action( 'woocommerce_after_shop_loop_item', 'woocommerce_template_loop_add_to_cart', 10 );
// Remove rating
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_rating', 5 );
// Remove price 
remove_action( 'woocommerce_after_shop_loop_item_title', 'woocommerce_template_loop_price', 10 );
// Remove thumbnail
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_template_loop_product_thumbnail', 10 );
// Remove sale flash
remove_action( 'woocommerce_before_shop_loop_item_title', 'woocommerce_show_product_loop_sale_flash', 10 );

/* Single product pages */

//remove sales flash
remove_action( 'woocommerce_before_single_product_summary', 'woocommerce_show_product_sale_flash', 10 );
// REMOVE TITLE
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_title', 5 );
//remove price
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_price', 10 );
// add to cart button
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_add_to_cart', 30 );
// remove_action( 'woocommerce_simple_add_to_cart', 'woocommerce_simple_add_to_cart', 30 );
// remove_action( 'woocommerce_grouped_add_to_cart', 'woocommerce_grouped_add_to_cart', 30 );
// remove_action( 'woocommerce_variable_add_to_cart', 'woocommerce_variable_add_to_cart', 30 );
// remove_action( 'woocommerce_external_add_to_cart', 'woocommerce_external_add_to_cart', 30 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_excerpt', 20 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_meta', 40 );
remove_action( 'woocommerce_single_product_summary', 'woocommerce_template_single_sharing', 50 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_product_data_tabs', 10 );
remove_action( 'woocommerce_after_single_product_summary', 'woocommerce_output_related_products', 20 );




function polytope_wc_add_slider_pager() {
  get_template_part( 'woocommerce/shop-loop-image', 'pager' );
}
add_action( 'woocommerce_after_shop_loop', 'polytope_wc_add_slider_pager', 10 );

function polytope_hide_shop_title() {
  if(is_shop()) {
    return false;
  } else {
  return true;
  }
}
add_filter( 'woocommerce_show_page_title', 'polytope_hide_shop_title', 10, 1 );

// Change add to cart button text
add_filter( 'add_to_cart_text', 'polytope_custom_cart_button_text' ); 
add_filter( 'single_add_to_cart_text', 'polytope_custom_cart_button_text' );   
// add_filter( 'woocommerce_product_single_add_to_cart_text', 'polytope_custom_cart_button_text' );    // 2.1 +
// add_filter( 'woocommerce_product_add_to_cart_text', 'polytope_custom_cart_button_text' );

function polytope_custom_cart_button_text() {
  return __( 'Add To Stack', 'woocommerce' );
}