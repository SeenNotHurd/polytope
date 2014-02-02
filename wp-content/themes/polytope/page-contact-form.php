<?php
/*
Template Name: Contact form
*/
?>

<?php get_header(); ?>

			<div id="content">

				<div id="inner-content" class="wrap clearfix">

						<div id="main" class="eightcol first clearfix" role="main">

							<?php if (have_posts()) : while (have_posts()) : the_post(); ?>

							<article id="post-<?php the_ID(); ?>" <?php post_class( 'clearfix' ); ?> role="article" itemscope itemtype="http://schema.org/BlogPosting">

								<header class="article-header">

									<h1 class="page-title"><?php the_title(); ?></h1>
								


								</header>

								<section class="entry-content clearfix" itemprop="articleBody">
									<?php the_content(); ?>
								</section>
								
								<footer class="article-footer">
									<p class="clearfix"><?php the_tags( '<span class="tags">' . __( 'Tags:', 'bonestheme' ) . '</span> ', ', ', '' ); ?></p>

								</footer>

								<?php //comments_template(); ?>

							</article>

							<?php 
								$contact_form_id = get_post_meta( get_the_ID(),'_polytope_contact_form',TRUE);
								$contact_form_title = get_post_meta( get_the_ID(),'_polytope_contact_form_title',TRUE );
								?>
							<section class="contact-form<?php if(!$contact_form_title) { echo ' no-title'; } ?>">
								<?php
									if($contact_form_title) { 
									  echo '<h1><' . __($contact_form_title, 'polytope') . '</h1>';
									}
									echo do_shortcode( $contact_form_id ); 
			 					?>
							</section>

							<?php endwhile; else : ?>

									<article id="post-not-found" class="hentry clearfix">
											<header class="article-header">
												<h1><?php _e( 'Oops, Post Not Found!', 'bonestheme' ); ?></h1>
										</header>
											<section class="entry-content">
												<p><?php _e( 'Uh Oh. Something is missing. Try double checking things.', 'bonestheme' ); ?></p>
										</section>
										<footer class="article-footer">
												<p><?php _e( 'This is the error message in the page-custom.php template.', 'bonestheme' ); ?></p>
										</footer>
									</article>

							<?php endif; ?>

						</div>

						<?php //get_sidebar(); ?>

				</div>

			</div>

<?php get_footer(); ?>
