<?php
/**
 * The template for displaying product content within loops.
 *
 * Override this template by copying it to yourtheme/woocommerce/content-product.php
 *
 * @author 		WooThemes
 * @package 	WooCommerce/Templates
 * @version     1.6.4
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

global $product, $woocommerce_loop;

// Store loop count we're currently on
if ( empty( $woocommerce_loop['loop'] ) )
	$woocommerce_loop['loop'] = 0;

// Store column count for displaying the grid
if ( empty( $woocommerce_loop['columns'] ) )
	$woocommerce_loop['columns'] = apply_filters( 'loop_shop_columns', 4 );

// Ensure visibility
if ( ! $product || ! $product->is_visible() )
	return;

// Increase loop count
$woocommerce_loop['loop']++;

// Extra post classes
$classes = array();
if ( 0 == ( $woocommerce_loop['loop'] - 1 ) % $woocommerce_loop['columns'] || 1 == $woocommerce_loop['columns'] )
	$classes[] = 'first';
if ( 0 == $woocommerce_loop['loop'] % $woocommerce_loop['columns'] )
	$classes[] = 'last';
?>
<li <?php post_class( $classes ); ?>>

	<?php do_action( 'woocommerce_before_shop_loop_item' ); ?>

	<header class="product-header">
		<a href="<?php the_permalink(); ?>" class="product-title">

			<?php
				/**
				 * woocommerce_before_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_show_product_loop_sale_flash - 10
				 * @hooked woocommerce_template_loop_product_thumbnail - 10
				 */
				do_action( 'woocommerce_before_shop_loop_item_title' );
			?>

			<h2><?php the_title(); ?></h2>

			<?php
				/**
				 * woocommerce_after_shop_loop_item_title hook
				 *
				 * @hooked woocommerce_template_loop_price - 10
				 */
				do_action( 'woocommerce_after_shop_loop_item_title' );
			?>

		</a>
		<?php echo woocommerce_template_loop_add_to_cart(); ?>
	</header>
	<div class="product-basics">
		<?php
			echo woocommerce_template_loop_price();
			echo woocommerce_show_product_loop_sale_flash();
			echo '<span class="out-of-stock">' . __('Out of Stock', 'woocommerce') . '</span>';
		?>
	</div>
	<div class="product-content">
		<section class="product-image">
			<?php echo woocommerce_template_loop_product_thumbnail(); //Need to get the first large size image?>
		</section>
		<section class="product-information">
			<?php echo apply_filters( 'woocommerce_short_description', $post->post_excerpt ); ?>
			<a href="<?php echo the_permalink(); ?>" class="more-information"><?php _e("More information", "woocommerce"); ?></a>
		</section>
	</div>
	<?php do_action( 'woocommerce_after_shop_loop_item' ); ?>

</li>