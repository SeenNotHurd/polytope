<?php
/**
 * The template for displaying the shop loop slider's pager.
 *
 *
 * @author    Thomas Hurd
 * @package   Polytope
 * @version     0.1
 */
global $product;
?>

<section class="product-shortcuts">
  <h4 class="more-photos"><?php _e('More seats', 'wpsc'); ?></h4>
  <div class="thumbnail-container pager">
    <?php 
      $count = 1; 
      while ( have_posts() ) : the_post();
    ?>
      <a href="#<?php echo $count; $count = $count + 1; ?>" class="product-shortcut">
        <!-- <img class="product-thumbnail" id="product_image_<?php //echo wpsc_the_product_id(); ?>" alt="<?php //echo wpsc_the_product_title(); ?>" title="<?php //echo wpsc_the_product_title(); ?>" src="<?php //echo esc_url( wpsc_the_product_image() ); ?>"/> -->
        <?php echo woocommerce_template_loop_product_thumbnail(); ?>
      </a>
    <?php endwhile; ?>
  </div>
</section>