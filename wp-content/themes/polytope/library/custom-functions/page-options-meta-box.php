<?php

/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function polytope_add_page_settings_box() {

  add_meta_box(
      'polytope-page-options',
      __( 'Page Options', 'polytope' ),
      'polytope_inner_page_settings_box',
      'page',
      'side'
  );
}
add_action( 'add_meta_boxes', 'polytope_add_page_settings_box' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function polytope_inner_page_settings_box( $post ) {

  // Add an nonce field so we can check for it later.
  wp_nonce_field( 'polytope_inner_page_settings_box', 'polytope_inner_page_settings_box_nonce' );

  /*
   * Use get_post_meta() to retrieve an existing value
   * from the database and use the value for the form.
   */
  $page_colouring_value = get_post_meta( $post->ID, '_polytope_page_colouring', true );
  if( esc_attr( $page_colouring_value ) == 'light' ) { 
    $page_light = 'selected'; 
  } else if( esc_attr( $page_colouring_value ) == 'dark' ) {
    $page_dark = 'selected';
  }

  $page_footer_location = get_post_meta( $post->ID, '_polytope_footer_positioning', true );
  if( esc_attr( $page_footer_location ) == 'normal' ) { 
    $footer_normal = 'selected'; 
  } else if( esc_attr( $page_footer_location ) == 'bottom' ) {
    $footer_bottom = 'selected';
  } else if( esc_attr( $page_footer_location ) == 'slimline' ) {
    $footer_slimline = 'selected';
  }
  // Page colouring
  echo '<p><strong>' . __('Page Colouring', 'polytope') . '</strong></p><label class="screen-reader-text" for="polytope_page_colouring">' . __('Page Colouring', 'polytope') . '</label>';
  echo '<select name="polytope_page_colouring">';
  echo '<option value="light" ' . $page_light . '>' . __('Light', 'polytope') . '</option>';
  echo '<option value="dark" ' . $page_dark . '>' . __('Dark', 'polytope') . '</option>';
  echo '</select>';

  // Footer position
  echo '<p><strong>' . __('Footer Positioning', 'polytope') . '</strong></p><label class="screen-reader-text" for="polytope_footer_positioning">' . __('Footer Positioning', 'polytope') . '</label>';
  echo '<select name="polytope_footer_positioning">';
  echo '<option value="normal" ' . $footer_normal . '>' . __('Normal', 'polytope') . '</option>';
  echo '<option value="bottom" ' . $footer_bottom . '>' . __('Page bottom (full)', 'polytope') . '</option>';
  echo '<option value="slimline" ' . $footer_slimline . '>' . __('Page bottom (slimline)', 'polytope') . '</option>';
  echo '</select>';
}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function polytope_save_options_post_data( $post_id ) {

  /*
   * We need to verify this came from the our screen and with proper authorization,
   * because save_post can be triggered at other times.
   */

  // Check if our nonce is set.
  if ( ! isset( $_POST['polytope_inner_page_settings_box_nonce'] ) )
    return $post_id;

  $nonce = $_POST['polytope_inner_page_settings_box_nonce'];

  // Verify that the nonce is valid.
  if ( ! wp_verify_nonce( $nonce, 'polytope_inner_page_settings_box' ) )
      return $post_id;

  // If this is an autosave, our form has not been submitted, so we don't want to do anything.
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return $post_id;

  // Check the user's permissions.
  if ( 'page' == $_POST['post_type'] ) {

    if ( ! current_user_can( 'edit_page', $post_id ) )
        return $post_id;
  
  }

  /* OK, its safe for us to save the data now. */

  // Sanitize user input.
  $page_colouring_data = esc_attr( $_POST['polytope_page_colouring'] );
  $page_footer_location = esc_attr( $_POST['polytope_footer_positioning'] );

  // Update the meta field in the database.
  update_post_meta( $post_id, '_polytope_page_colouring', $page_colouring_data );
  update_post_meta( $post_id, '_polytope_footer_positioning', $page_footer_location );
}
add_action( 'save_post', 'polytope_save_options_post_data' );