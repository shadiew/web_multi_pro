<?php
/** "AMP Header */
	$header_nav_search_section    = 'yes';
   $header_top_info_show         = 'no';
   $amp_header_menu = '';
 
	if ( defined( 'FW' ) ) {
      $header_top_info_show = digiqole_option('amp_header_top_info_show');
      $amp_header_menu = digiqole_option( 'amp_header_menu' );
      $header_nav_search_section = digiqole_option( 'amp_header_nav_search_section' );
	}

 ?>

<div class="header-main-container">
   <?php if(defined( 'FW' ) && $header_top_info_show=='yes' ): ?>
      <div class="topbar topbar-gray">
         <ul class="top-info"><li><?php echo date_i18n(get_option('date_format')); ?></li></ul>
      <!-- end container -->
      </div>
   <?php endif; ?>

   <div class="header-middle-area">
      <div class="container">
         <div class="logo-area">
            <a class="logo" href="<?php echo esc_url(home_url('/')); ?>">
               <img  class="img-fluid" src="<?php 
                  echo esc_url(
                     digiqole_src(
                        'amp_header_logo',
                        DIGIQOLE_IMG . '/logo/logo-dark.png'
                     )
                  );
               ?>" alt="<?php bloginfo('name'); ?>">
            </a>   
         </div>
      </div>                     
   </div>

   <header id="header" class="header header-gradient">
      <div class="header-wrapper">
         <div class="container">
            <nav class="navbar navbar-expand-lg navbar-light">
            <div role="button" on="tap:sidebar.toggle" tabindex="0" class="hamburger">
            <button class="elementskit-menu-hamburger elementskit-menu-toggler" type="button" data-toggle="collapse" data-target="#primary-nav" aria-controls="primary-nav" aria-expanded="false"
                  aria-label="Toggle navigation">
                  <span class="elementskit-menu-hamburger-icon"></span>
                  <span class="elementskit-menu-hamburger-icon"></span>
                  <span class="elementskit-menu-hamburger-icon"></span>
            </button>
            </div>                   
            <?php if(defined( 'FW' )): ?>                        
               <div class="nav-search-area">
                  <?php if($header_nav_search_section=='yes'): ?>
                     <div class="header-search-icon">
                     <?php get_search_form(); ?>
                     </div>
                  <?php endif; ?>
                  <!-- xs modal -->                           
               </div>                        
            <?php endif; ?>
            <!-- Site search end-->             
            </nav>
         </div><!-- container end-->
      </div>
   </header>
</div>

<amp-sidebar class="amp-menu-slide" id="sidebar" layout="nodisplay" side="left">
   <div on="tap:sidebar.close" class="menu-close" role="button" tabindex="0"><span class="icon-close">&times;</span></div>
      <div class="logo-area">
         <a class="logo" href="<?php echo esc_url(home_url('/')); ?>">
            <img  class="img-fluid" src="<?php 
               echo esc_url(
                  digiqole_src(
                     'amp_header_logo',
                     DIGIQOLE_IMG . '/logo/logo-dark.png'
                  )
               );
            ?>" alt="<?php bloginfo('name'); ?>">
         </a>
      </div>
      <?php 
      wp_nav_menu([
         'menu'            => $amp_header_menu,
         'menu_id'         => 'amp-menu',				  
         'container_id'    => 'amp-nav',
         'container'       => 'div',
         'container_class' => 'sidebar',
         'menu_class'      => 'amp-nav',
         'depth'           => 3,
         'walker'          => new digiqole_navwalker(),
         'fallback_cb'     => 'digiqole_navwalker::fallback',
      ]);
      ?>
   </amp-sidebar>