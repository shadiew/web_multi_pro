<?php
 $class = '';
 $title = '';
 $header_nav_search_section    = 'yes';
 $header_nav_sticky            = 'no';
 $header_top_info_show         = 'no';
 $header_social_share         = 'no';

 if ( defined( 'FW' ) ) {

    $header_settings = digiqole_option('theme_header_default_settings');

    if(isset($header_settings['no'])) {
    
      $header_top_info_show = $header_settings['no']['header_top_info_show']; 
      $header_nav_search_section = $header_settings['no']['header_nav_search_section']; 
      $header_nav_sticky = $header_settings['no']['header_nav_sticky']; 
    
      $header_social_share = $header_settings['no']['header_social_share']; 
      
     
    } 
 
 } 
   
  

?>


<div class="header-middle-area header-middle-gradent">
   <div class="container">
      <div class="row">
         <div class="col-md-3 align-self-center">
             <?php if(defined( 'FW' )): ?>
               <?php
                  if($header_social_share=='yes'):
                     $social_links = digiqole_option('general_social_links',[]);  ?>
                     <ul class="social-links xs-center">
                     <?php 
                        $class1 = '';
                        if(count($social_links)):   
                           foreach($social_links as $sl):
                              if( isset( $sl['icon_class']) && isset($sl['title']) ) :
                                 $explode_value = explode(" ", $sl['icon_class']);
                                 if($explode_value[0] == 'fab' || $explode_value[0] == 'fa'){
                                    $first_class = str_replace($explode_value[0], 'ts-icon', $explode_value[0]);
                                    $second_class = str_replace('fa-', 'ts-icon-', $explode_value[1]);
                                    $class = str_replace('fa-', '', $sl['icon_class']);
                                 } else {
                                    $first_class = $explode_value[0];
                                    $second_class =$explode_value[1];
                                    $class = str_replace('ts-icon-', '', $sl['icon_class']);
                                 }
                                 
                                 $class1 = $first_class.' '.$second_class;
                                 $title = $sl["title"];
                              endif; ?>
                              <li class="<?php echo esc_attr($class); ?>">
                                 <a target="_blank" title="<?php echo esc_attr( $title = $sl["title"]); ?>" href="<?php echo esc_url($sl['url']); ?>">
                                 <span class="social-icon">  <i class="<?php echo esc_attr($class1); ?>"></i> </span>
                                 </a>
                              </li>
                           <?php endforeach; ?>
                        <?php endif; ?>
                     </ul>
                     <?php endif; ?>
                  <!-- end social links -->
                  
               <?php endif; ?>
         <!-- Site search end-->
         </div>
          <div class="col-md-6 align-self-center">
              <div class="logo-area text-center">
               <?php
                  
                  $digiqole_logo_url= esc_url(
                        digiqole_src(
                           'general_light_logo',
                           DIGIQOLE_IMG . '/logo/logo-light.png'
                        )
                     );
              

               ?> 
                  <?php echo  digiqole_text_logo()?'<h1 class="logo-title">':''; ?> 
                     <a class="logo" href="<?php echo esc_url(home_url('/')); ?>">
                        
                        <?php if(digiqole_text_logo()): ?> 
                           <?php echo esc_html(digiqole_text_logo()); ?>
                           <?php else: ?>
                              <img width="220" height="33" class="img-fluid" src="<?php echo esc_url($digiqole_logo_url); ?>" alt="<?php echo get_bloginfo('name') ?>">
                           <?php endif; ?>
                           
                     </a>
                  <?php echo  digiqole_text_logo()?'</h1>':''; ?>
              </div>
          </div>    
         <!-- col end  -->
         <div class="col-md-3 align-self-center">
            
            <?php if ( defined( 'FW' ) ): ?>
                  <?php if($header_nav_search_section=='yes'): ?>
                  <div class="header-search text-right">
                      <?php get_search_form(); ?>
                  </div>
                  <?php endif; ?>
            <?php endif; ?>

         </div>
         <!-- col end  -->
      </div>
   </div>                     
</div>
<header id="header" class="header navbar-solid <?php echo esc_attr($header_nav_sticky=='yes'?'navbar-sticky':''); ?> ">
      <div class="container">
         <nav class="navbar navbar-expand-lg navbar-light m-auto">
              <?php echo  digiqole_text_logo()?'<h1 class="logo-title">':''; ?> 
                  <a class="logo d-none" href="<?php echo esc_url(home_url('/')); ?>">
                           <?php if(digiqole_text_logo()): ?> 
                                 <?php echo esc_html(digiqole_text_logo()); ?>
                              <?php else: ?>
                                 <img  class="img-fluid" src="<?php echo esc_url($digiqole_logo_url); ?>" alt="<?php echo get_bloginfo('name') ?>">
                              <?php endif; ?>
                  </a>
               <?php echo  digiqole_text_logo()?'</h1>':''; ?>   
               <button class="navbar-toggler" type="button" data-toggle="collapse"
                     data-target="#primary-nav" aria-controls="primary-nav" aria-expanded="false"
                     aria-label="Toggle navigation">
                     <span class="navbar-toggler-icon"><i class="ts-icon ts-icon-menu"></i></span>
               </button>
               
                  <?php get_template_part( 'template-parts/navigations/nav', 'primary' ); ?>
              
            <!-- Site search end-->
                                          
                        
         </nav>
      </div><!-- container end-->
</header>
