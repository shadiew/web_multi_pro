<?php
$copyright_text = digiqole_option('amp_footer_copyright', 'Copyright &copy; '.date('Y').' Digiqole. All Right Reserved.'); 
$back_to_top = digiqole_option('amp_back_to_top');
$amp_footer_menu    = digiqole_option( 'amp_footer_menu' );
$footer_top_show_hide =    digiqole_option("amp_footer_newsletter_section");
$mailchimp_short_code =    digiqole_option("amp_footer_mailchimp");

?>
<?php if(defined( 'FW' )){ ?>
	<?php if($footer_top_show_hide == 'yes'){ ?>
		<div class="newsletter-area">
			<div class="container">
				<div class="row align-items-center">
					<div class="col-md-6">
						<div class="footer-logo">
							<a class="logo" href="<?php echo esc_url(home_url('/')); ?>">
								<img  class="img-fluid" src="<?php 
									echo esc_url(
										digiqole_src(
											'amp_footer_logo',
											DIGIQOLE_IMG . '/logo/logo-light.png'
										)
									);
								?>" alt="<?php bloginfo('name'); ?>">
							</a>
						</div>
					</div>
					<?php if( shortcode_exists('mc4wp_form') && $mailchimp_short_code!='' ): ?>  
						<div class="col-md-6">
							<div class="ts-subscribe">
								<?php  echo do_shortcode($mailchimp_short_code);  ?>  
							</div>  					
						</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
	<?php } ?>
<?php } ?>

<?php if(defined( 'FW' )): ?>
	<?php if( is_active_sidebar('footer-bottom-center') || is_active_sidebar('footer-left') || is_active_sidebar('footer-center') || is_active_sidebar('footer-right') || is_active_sidebar('footer-bottom-left') ): ?> 
		<footer class="ts-footer">
			<div class="container">
				<?php if( is_active_sidebar('footer-left') || is_active_sidebar('footer-center') || is_active_sidebar('footer-right') ): ?>
					<div class="col-4">
						<?php  dynamic_sidebar( 'footer-left' ); ?>
					</div>
					<div class="col-4">
						<?php  dynamic_sidebar( 'footer-center' ); ?>
					</div>
					<div class="col-4">
						<?php  dynamic_sidebar( 'footer-right' ); ?>
					</div>				
				<?php endif; ?>  
			</div>				
		</footer>
	<?php endif; ?>   
<?php endif; ?>

<footer class="amp-wp-footer">
	<div class="amp-footer-container">
		<div class="copyright-text">
			<?php		
			echo digiqole_kses($copyright_text);  
			?>
		</div>
		<div class="top-up-btn">
			<!-- end footer -->
			<?php if($back_to_top=="yes"): ?>
				<a href="#" aria-hidden="true">&uarr;</a>
			<?php endif; ?>
		</div>
	</div>	
</footer>
