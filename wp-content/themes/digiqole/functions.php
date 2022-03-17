<?php

/**
 * theme's main functions and globally usable variables, contants etc
 * added: v1.0 
 * textdomain: digiqole, class: DIGIQOLE, var: $digiqole_, constants: DIGIQOLE_, function: digiqole_
 */

// shorthand contants
// ------------------------------------------------------------------------
define('DIGIQOLE_THEME', 'DIGIQOLE Newspaper and Magazine WordPress Theme');
define('DIGIQOLE_VERSION', '2.0.2');
define('DIGIQOLE_MINWP_VERSION', '5.2');
define('DIGIQOLE_DEMO',true); 

// shorthand contants for theme assets url
// ------------------------------------------------------------------------
define('DIGIQOLE_THEME_URI', get_template_directory_uri());
define('DIGIQOLE_IMG', DIGIQOLE_THEME_URI . '/assets/images');
define('DIGIQOLE_CSS', DIGIQOLE_THEME_URI . '/assets/css');
define('DIGIQOLE_JS', DIGIQOLE_THEME_URI . '/assets/js');



// shorthand contants for theme assets directory path
// ----------------------------------------------------------------------------------------
define('DIGIQOLE_THEME_DIR', get_template_directory());
define('DIGIQOLE_IMG_DIR', DIGIQOLE_THEME_DIR . '/assets/images');
define('DIGIQOLE_CSS_DIR', DIGIQOLE_THEME_DIR . '/assets/css');
define('DIGIQOLE_JS_DIR', DIGIQOLE_THEME_DIR . '/assets/js');

define('DIGIQOLE_CORE', DIGIQOLE_THEME_DIR . '/core');
define('DIGIQOLE_COMPONENTS', DIGIQOLE_THEME_DIR . '/components');
define('DIGIQOLE_EDITOR', DIGIQOLE_COMPONENTS . '/editor');
define('DIGIQOLE_EDITOR_ELEMENTOR', DIGIQOLE_EDITOR . '/elementor');
define('DIGIQOLE_EDITOR_GUTENBERG', DIGIQOLE_EDITOR . '/gutenberg');
define('DIGIQOLE_INSTALLATION', DIGIQOLE_CORE . '/installation-fragments');
define('DIGIQOLE_REMOTE_CONTENT', esc_url('http://demo.themewinter.com/demo-content/digiqole'));


// set up the content width value based on the theme's design
// ----------------------------------------------------------------------------------------
if (!isset($content_width)) {
    $content_width = 800;
}

// set up theme default and register various supported features.
// ----------------------------------------------------------------------------------------

function digiqole_setup() {

    // make the theme available for translation
    $lang_dir = DIGIQOLE_THEME_DIR . '/languages';
    load_theme_textdomain('digiqole', $lang_dir);

    // add support for post formats
    add_theme_support('post-formats', [
        'standard', 'image', 'video', 'audio','gallery'
    ]);

    // add support for automatic feed links
    add_theme_support('automatic-feed-links');

    // let WordPress manage the document title
    add_theme_support('title-tag');

    // add support for post thumbnails
    add_theme_support('post-thumbnails');

    // woocommerce support
    add_theme_support( 'woocommerce' );
    add_theme_support( 'wc-product-gallery-slider' );



    // hard crop center center
    set_post_thumbnail_size(850, 560, ['center', 'center']);
    add_image_size( 'digiqole-medium', 600, 398, array( 'center', 'center' ) );
    add_image_size( 'digiqole-small', 455, 300, array( 'center', 'center' ) ); 
 
    // register navigation menus
    register_nav_menus(
        [
            'primary' => esc_html__('Primary Menu', 'digiqole'),
            'topbarmenu' => esc_html__('TopBar Menu', 'digiqole'),
            'footermenu' => esc_html__('Footer Menu', 'digiqole'),
        ]
    );
  

    // HTML5 markup support for search form, comment form, and comments
    add_theme_support('html5', array(
        'search-form', 'comment-form', 'comment-list', 'gallery', 'caption'
    ));
    /*
     * Enable support for wide alignment class for Gutenberg blocks.
     */
    add_theme_support( 'align-wide' );
    add_theme_support( 'editor-styles' );
    add_theme_support( 'wp-block-styles' );

    /**
     * Converting FA4 icon to FA5 icon
     *
     * */

	    $converter = new \Digiqole\Converter();
        $converter->init();
        

}
add_action('after_setup_theme', 'digiqole_setup');


add_action('enqueue_block_editor_assets', 'digiqole_action_enqueue_block_editor_assets' );
function digiqole_action_enqueue_block_editor_assets() {
    wp_enqueue_style( 'digiqole-fonts', digiqole_google_fonts_url(['Barlow:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i', 'Roboto:300,300i,400,400i,500,500i,700,700i,900,900i']), null,  DIGIQOLE_VERSION );

    wp_enqueue_style( 'digiqole-gutenberg-editor-font-awesome-styles', DIGIQOLE_CSS . '/font-awesome.css', null, DIGIQOLE_VERSION );
    wp_enqueue_style( 'digiqole-gutenberg-editor-customizer-styles', DIGIQOLE_CSS . '/gutenberg-editor-custom.css', null, DIGIQOLE_VERSION );
    wp_enqueue_style( 'digiqole-gutenberg-editor-styles', DIGIQOLE_CSS . '/gutenberg-custom.css', null, DIGIQOLE_VERSION );
    wp_enqueue_style( 'digiqole-gutenberg-blog-styles', DIGIQOLE_CSS . '/blog.css', null, DIGIQOLE_VERSION );
}

// hooks for unyson framework
// ----------------------------------------------------------------------------------------
function digiqole_framework_customizations_path($rel_path) {
    return '/components';
}
add_filter('fw_framework_customizations_dir_rel_path', 'digiqole_framework_customizations_path');

function digiqole_remove_fw_settings() {
    remove_submenu_page( 'themes.php', 'fw-settings' );
}
add_action( 'admin_menu', 'digiqole_remove_fw_settings', 999 );


// include the init.php
// ----------------------------------------------------------------------------------------
require_once( DIGIQOLE_CORE . '/init.php');
require_once( DIGIQOLE_COMPONENTS . '/editor/elementor/elementor.php');


/*************************************
/*******  Load More  ********
**************************************/

function digiqole_post_ajax_loading_cb()
{   
    $settings =  $_POST['ajax_json_data'];
    $show_gradient = (($settings['show_gradient']== 'yes') ? 'gradient-post' : '');
   $arg = [
      'post_type'   =>  'post',
      'post_status' => 'publish',
      'order'       => $settings['order'],
      'posts_per_page' => $settings['posts_per_page'],
      'paged'             => $_POST['paged'],
      'tag__in'           => $settings['tags'],
      'suppress_filters' => false,
    
  ];

  if(count($settings['terms'])){
   $arg['tax_query'] = array(
      array(
                'taxonomy' => 'category',
                'terms'    => $settings['terms'],
                'field' => 'id',
                'include_children' => true,
                'operator' => 'IN'
        ),
    );
  }

  switch($settings['post_sortby']){
      case 'popularposts':
          $arg['meta_key'] = 'newszone_post_views_count';
          $arg['orderby'] = 'meta_value_num';
      break;
      case 'mostdiscussed':
          $arg['orderby'] = 'comment_count';
      break;
      default:
          $arg['orderby'] = 'date';
      break;
  }
   
  $allpostloding = new WP_Query($arg);
  $index = 0;

  while($allpostloding->have_posts()){ $allpostloding->the_post(); ?>
     
            <?php if($settings['grid_style']=='style1'): ?>
                <?php echo "<div class='col-md-6 grid-item $show_gradient' >"; ?>
                     <?php require( DIGIQOLE_COMPONENTS . '/editor/elementor/widgets/style/post-grid/content-style1.php');  ?>
               <?php echo "</div>"; ?> 

            <?php elseif($settings['grid_style']=='style2'): ?>
            <?php echo "<div class='col-md-6 grid-item $show_gradient' >"; ?>
                    <?php require( DIGIQOLE_COMPONENTS . '/editor/elementor/widgets/style/post-grid/content-style-2-a.php');  ?>
            <?php echo "</div>"; ?> 

             <?php elseif($settings['grid_style']=='style3'): ?>

             <?php echo "<div class='grid-item col-md-12 $show_gradient' >"; ?>
                  <?php require( DIGIQOLE_COMPONENTS . '/editor/elementor/widgets/style/post-list/content-style4-a.php');  ?>
             <?php echo "</div>"; ?> 
            <?php endif ?>

      
        <?php
     $index ++;
  }
  wp_reset_postdata();
  wp_die();
  
}

add_action( 'wp_ajax_nopriv_digiqole_post_ajax_loading', 'digiqole_post_ajax_loading_cb' );
add_action( 'wp_ajax_digiqole_post_ajax_loading', 'digiqole_post_ajax_loading_cb' );

// preloader function
// ----------------------------------------------------------------------------------------            

function preloader_function(){
    $preloader_show = digiqole_option('preloader_show');
        if($preloader_show == 'yes'){
            $digiqole_preloader_logo_url= esc_url(digiqole_src('preloader_logo'));
        ?>
        <div id="preloader">
            <?php if($digiqole_preloader_logo_url !=''): ?>
            
            <div class="preloader-logo">
                <img  class="img-fluid" src="<?php echo esc_url($digiqole_preloader_logo_url); ?>" alt="<?php echo get_bloginfo('name') ?>">
            </div>
            <?php else: ?>
            <div class="spinner">
                <div class="double-bounce1"></div>
                <div class="double-bounce2"></div>
            </div>
            <?php endif; ?>
            <div class="preloader-cancel-btn-wraper"> 
                <span class="btn btn-primary preloader-cancel-btn">
                  <?php esc_html_e('Cancel Preloader', 'digiqole'); ?></span>
            </div>
        </div>
    <?php
    }
}
add_action('wp_body_open', 'preloader_function');

// dark light mode
function digiqole_darklight_mode(){
    $style_darklight_mode = digiqole_option('style_darklight_mode');
    if($style_darklight_mode == 'yes'){
    ?>
        <div class="color_swicher change-mode">
            <div class="switch_container">
                <i class="ts-icon ts-icon-sun"></i>
                <i class="ts-icon ts-icon-moon"></i>
            </div>
        </div>

    <?php
    }
}
add_action('wp_body_open', 'digiqole_darklight_mode');

// for optimization dequeue styles
add_action( 'wp_enqueue_scripts', 'digiqole_remove_unused_css_files', 9999 );
function digiqole_remove_unused_css_files() {
    $fontawesome = digiqole_option('optimization_fontawesome_enable', 'yes');
    $blocklibrary = digiqole_option('optimization_blocklibrary_enable', 'yes');
    $elementoricons = digiqole_option('optimization_elementoricons_enable', 'yes');
    $elementkitsicons = digiqole_option('optimization_elementkitsicons_enable', 'yes');
    $socialicons = digiqole_option('optimization_socialicons_enable', 'yes');
    $dashicons = digiqole_option('optimization_dashicons_enable', 'yes');

    // dequeue wp-review styles file
    wp_dequeue_style( 'wur_content_css' );
    wp_deregister_style( 'wur_content_css' );

    // dequeue fontawesome icons file
    if($fontawesome == 'no'){
        wp_dequeue_style( 'font-awesome' );
	    wp_deregister_style( 'font-awesome' );
        wp_dequeue_style( 'font-awesome-5-all' );
        wp_deregister_style( 'font-awesome-5-all' );
        wp_dequeue_style( 'font-awesome-4-shim' );
        wp_deregister_style( 'font-awesome-4-shim' );
        wp_dequeue_style( 'fontawesome-five-css' );
    }

    // dequeue block-library file
    if($blocklibrary == 'no'){
        wp_dequeue_style( 'wp-block-library' );
        wp_dequeue_style( 'wp-block-library-theme' );
        wp_dequeue_style( 'wc-block-style' );
		wp_dequeue_style( 'wc-block-style' );
    }

    if($elementkitsicons == 'no'){		
		wp_dequeue_style( 'elementor-icons-ekiticons' );
		wp_deregister_style( 'elementor-icons-ekiticons' );
        wp_dequeue_script( 'elementskit-framework-js-frontend' );
        wp_dequeue_script( 'ekit-widget-scripts' );       
        wp_dequeue_style( 'ekit-widget-styles' );
        wp_dequeue_style( 'ekit-responsive' );          
    }

    if($socialicons == 'no'){		
        wp_dequeue_style( 'apsc-frontend-css' );
    }

    if($elementoricons == 'no'){
        // Don't remove it in the backend
        if ( is_admin() || current_user_can( 'manage_options' ) ) {
            return;
        }
        wp_dequeue_style( 'elementor-animations' );
        wp_dequeue_style( 'elementor-icons' );
        wp_deregister_style( 'elementor-icons' );        
    }

    if($dashicons == 'no'){
        // Don't remove it in the backend
        if ( is_admin() || current_user_can( 'manage_options' ) ) {
            return;
        }
        wp_dequeue_style( 'dashicons' );
    }
	
}

/* disable option for elementskit icons */
add_action('elementskit_lite/after_loaded', function(){
    add_filter('elementor/icons_manager/additional_tabs', function($icons){
        $elementkitsicons = digiqole_option('optimization_elementkitsicons_enable', 'yes');
    
        if($elementkitsicons == 'no'){
            unset($icons['ekiticons']);      
        }
    
        return $icons;
    });

});

/* disable option for font awesome icons from elementor editor */
add_action( 'elementor/frontend/after_register_styles',function() {
    $fontawesome = digiqole_option('optimization_fontawesome_enable', 'yes');
    if($fontawesome == 'no'){
        foreach( [ 'solid', 'regular', 'brands' ] as $style ) {
            wp_deregister_style( 'elementor-icons-fa-' . $style );
        }
    }

    $elementkitsicons = digiqole_option('optimization_elementkitsicons_enable', 'yes');
    if($elementkitsicons == 'no'){
        wp_deregister_script( 'animate-circle' );
        wp_dequeue_script( 'animate-circle' );
        wp_deregister_script( 'elementskit-elementor' );    
        wp_dequeue_script( 'elementskit-elementor' );
        wp_dequeue_style( 'e-animations' );
        wp_deregister_style( 'e-animations' );
    }
    
}, 20 );

/* disable option for font awesome icons from elementor editor */
add_filter('elementor/icons_manager/native', function($icons){
    $fontawesome = digiqole_option('optimization_fontawesome_enable', 'yes');
    if($fontawesome == 'no'){
        unset($icons['fa-regular']);
        unset($icons['fa-solid']);
        unset($icons['fa-brands']);        
    }

    return $icons;
});

// add rel for accesspress social links
add_action('apsc_facebook_link', 'digiqole_accesspress_social_rel_link', 5);
add_action('apsc_twitter_link', 'digiqole_accesspress_social_rel_link', 5);
add_action('apsc_youtube_link', 'digiqole_accesspress_social_rel_link', 5);
add_action('apsc_soundcloud_link', 'digiqole_accesspress_social_rel_link', 5);
add_action('apsc_dribbble_link', 'digiqole_accesspress_social_rel_link', 5);
add_action('apsc_instagram_link', 'digiqole_accesspress_social_rel_link', 5);
function digiqole_accesspress_social_rel_link(){
    echo 'rel="noreferrer"';
}

// content security policy(CSP)
header("Content-Security-Policy: script-src 'self' 'unsafe-inline' 'unsafe-eval' https: data:");

/* Push google analytics code in head area */
function digiqole_meta_des_viewport(){
    $meta_viewport = digiqole_option('optimization_meta_viewport', 'yes');
    if($meta_viewport == 'yes'){
        ?>
        <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=5">
        <meta name="description" content="<?php if ( is_single() ) {
        single_post_title('', true); 
            } else {
            bloginfo('name'); echo " - "; bloginfo('description');
            }
            ?>" />
        <?php
    }
}
add_action('wp_head', 'digiqole_meta_des_viewport', 1);