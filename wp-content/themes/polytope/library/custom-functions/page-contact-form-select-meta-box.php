<?php


/**
 * Adds a box to the main column on the Post and Page edit screens.
 */
function polytope_add_contact_form_select() {

  $post_id = $_GET['post'] ? $_GET['post'] : $_POST['post_ID'] ;
  $template_file = get_post_meta($post_id,'_wp_page_template',TRUE);
  // check for a template type
  if($template_file == 'page-contact-form.php') {
    add_meta_box(
        'polytope-contact-form-add',
        __( 'Contact Form', 'polytope' ),
        'polytope_contact_form_select_inner',
        'page',
        'side'
    );
  }
}
add_action( 'add_meta_boxes', 'polytope_add_contact_form_select' );

/**
 * Prints the box content.
 * 
 * @param WP_Post $post The object for the current post/page.
 */
function polytope_contact_form_select_inner( $post ) {

  // Add an nonce field so we can check for it later.
  wp_nonce_field( 'polytope_contact_form_select_inner', 'polytope_contact_form_select_nonce' );

  /*
   * Use get_post_meta() to retrieve an existing value
   * from the database and use the value for the form.
   */
  $form_title_val = get_post_meta( $post->ID, '_polytope_contact_form_title', true );
  $form_id_val = get_post_meta( $post->ID, '_polytope_contact_form', true );

  echo '<label for="contact_form_title">';
       _e( "Form title", 'polytope' ); // This should be automatic
  echo '</label> ';
  echo '<input type="text" id="contact_form_title" name="contact_form_title" value="' . esc_attr( $form_title_val ) . '" size="25" />';
  echo '<label for="contact_form_id">';
       _e( "Description for this field", 'polytope' );
  echo '</label> ';
  echo '<input type="text" id="contact_form_id" name="contact_form_id" value="' . esc_attr( $form_id_val ) . '" size="25" />';

}

/**
 * When the post is saved, saves our custom data.
 *
 * @param int $post_id The ID of the post being saved.
 */
function polytope_save_contact_form_postdata( $post_id ) {

  /*
   * We need to verify this came from the our screen and with proper authorization,
   * because save_post can be triggered at other times.
   */

  // Check if our nonce is set.
  if ( ! isset( $_POST['polytope_contact_form_select_nonce'] ) )
    return $post_id;

  $nonce = $_POST['polytope_contact_form_select_nonce'];

  // Verify that the nonce is valid.
  if ( ! wp_verify_nonce( $nonce, 'polytope_contact_form_select_inner' ) )
      return $post_id;

  // If this is an autosave, our form has not been submitted, so we don't want to do anything.
  if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) 
      return $post_id;

  // Check the user's permissions.
  if ( 'page' == $_POST['post_type'] ) {

    if ( ! current_user_can( 'edit_page', $post_id ) )
        return $post_id;
  
  } else {

    if ( ! current_user_can( 'edit_post', $post_id ) )
        return $post_id;
  }

  /* OK, its safe for us to save the data now. */

  // Sanitize user input.
  $form_id = sanitize_text_field( $_POST['contact_form_id'] );
  $form_title = sanitize_text_field( $_POST['contact_form_title'] );

  // Update the meta field in the database.
  update_post_meta( $post_id, '_polytope_contact_form', $form_id );
  update_post_meta( $post_id, '_polytope_contact_form_title', $form_title );
}
add_action( 'save_post', 'polytope_save_contact_form_postdata' );