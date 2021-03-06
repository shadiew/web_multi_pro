<?php
/**
 * The header template for the theme
 */
?>
<!DOCTYPE html>
  <html <?php language_attributes(); ?>> 
    <head>
        <meta charset="<?php bloginfo( 'charset' ); ?>">
		<?php wp_head(); ?>
    </head>
    <body <?php body_class(); ?> >

    
     
      <?php wp_body_open(); ?>

 
      <div class="body-inner-content">
      
      <?php 
     
      $header_style = 'standard';
      $header_settings = digiqole_option('theme_header_default_settings');
      $header_builder_enable = digiqole_option('header_builder_enable','no');

      
      if($header_builder_enable=='yes' && class_exists('ElementsKit')){
         if(class_exists('\ElementsKit\Utils::render_elementor_content')){
            echo \ElementsKit\Utils::render_elementor_content($header_settings['yes']['digiqole_header_builder_select']); 
         }else{
            $elementor = \Elementor\Plugin::instance();
            echo \ElementsKit\Utils::render($elementor->frontend->get_builder_content_for_display( $header_settings['yes']['digiqole_header_builder_select'] )); 
         }
      }else{

         if(isset($header_settings['no'])) {
            $header_style = $header_settings['no']['header_layout_style']; 
         } 

         $page_override_header     = digiqole_meta_option(get_the_ID(),'page_header_override');
         $page_header_layout_style = digiqole_meta_option(get_the_ID(),'page_header_layout_style','standard');
      
         // if($page_override_header=='yes'):
         //    $header_style = $page_header_layout_style;
         // endif;
         get_template_part( 'template-parts/headers/header', $header_style );
      }
      ?>