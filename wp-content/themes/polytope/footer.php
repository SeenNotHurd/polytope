			<footer class="footer" role="contentinfo">

				<div id="inner-footer" class="wrap clearfix">

					<div class="footer-info">
						<p class="contact-info">
							info@polytope-seating.com<br>
							+358 466 30 9520
						</p>

						<p class="source-org copyright">&copy; <?php echo date('Y'); ?> <?php bloginfo( 'name' ); ?></p>

					</div>
						
					<nav role="navigation" id="footer-menu">
							<?php bones_footer_links(); ?>
					</nav>

				</div>

			</footer>

		</div>

		<?php // all js scripts are loaded in library/bones.php ?>
		<?php wp_footer(); ?>

	</body>

</html>
