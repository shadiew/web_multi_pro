<?php if (!defined('ABSPATH')) die('Direct access forbidden.');
/**
 * enqueue all theme scripts and styles
 */


// stylesheets
// ----------------------------------------------------------------------------------------
if ( !is_admin() ) {
	// 3rd party css
	//wp_enqueue_style( 'digiqole-fonts', digiqole_google_fonts_url(['Barlow:300,300i,400,400i,500,500i,600,600i,700,700i,800,800i,900,900i', 'Roboto:300,300i,400,400i,500,500i,700,700i,900,900i']), null,  DIGIQOLE_VERSION );
	if( is_rtl() ){
		wp_enqueue_style( 'bootstrap-rtl',  DIGIQOLE_CSS . '/bootstrap.min-rtl.css', null,  DIGIQOLE_VERSION );
	}else{
		wp_enqueue_style( 'bootstrap',  DIGIQOLE_CSS . '/bootstrap.min.css', null,  DIGIQOLE_VERSION );
	}

    wp_enqueue_style( 'icon-font',  DIGIQOLE_CSS . '/icon-font.css', null,  DIGIQOLE_VERSION );

	// all style included sticky, custom scrollbar, magnific popup
	wp_enqueue_style( 'digiqole-all-style',  DIGIQOLE_CSS . '/all.css', null,  DIGIQOLE_VERSION );

	wp_enqueue_style( 'digiqole-master',  DIGIQOLE_CSS . '/master.css', null,  DIGIQOLE_VERSION );
	
	 if( is_rtl() ){
		wp_enqueue_style( 'digiqole-rtl', DIGIQOLE_THEME_URI . '/rtl.css', null, DIGIQOLE_VERSION );
	}
}

// javascripts
// ----------------------------------------------------------------------------------------
if ( !is_admin() ) {

	// 3rd party scripts
	if ( is_rtl() ) {
		wp_enqueue_script( 'bootstrap-rtl',  DIGIQOLE_JS . '/bootstrap.min-rtl.js', array( 'jquery' ),  DIGIQOLE_VERSION, true );
	}else{
		wp_enqueue_script( 'bootstrap',  DIGIQOLE_JS . '/bootstrap.min.js', array( 'jquery' ),  DIGIQOLE_VERSION, true );
	}

	wp_enqueue_script( 'bootstrap',  DIGIQOLE_JS . '/bootstrap.min.js', array( 'jquery' ),  DIGIQOLE_VERSION, true );

	// all script included easy pic chart, sticky, custom scrollbar, popper, magnific popup
	wp_enqueue_script( 'digiqole-all-script',  DIGIQOLE_JS . '/all.js', array( ), true, true );

	wp_enqueue_script( 'swiper',  DIGIQOLE_JS . '/swiper.min.js', array( 'jquery' ),  DIGIQOLE_VERSION, true );

	wp_enqueue_script( 'fontfaceobserver',  DIGIQOLE_JS . '/fontfaceobserver.js', array( ), true, true );

	// 	theme scripts
	wp_enqueue_script( 'digiqole-script',  DIGIQOLE_JS . '/script.js', array( 'jquery' ),  DIGIQOLE_VERSION, true );
	$blog_sticky_sidebar  = digiqole_option('blog_sticky_sidebar'); 

//    if( is_singular( 'post' ) ){
// 		if(get_the_id()==96){
// 		$blog_sticky_sidebar = 'yes';    
// 		}
// 	}

   wp_localize_script( 'digiqole-script', 'digiqole_ajax', array(
	   
	   'ajax_url' => admin_url( 'admin-ajax.php' ),
	   'blog_sticky_sidebar' => $blog_sticky_sidebar,
        
	   ) );
	  
	
	
	// Load WordPress Comment js
	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}

	add_filter( 'style_loader_tag',  'digiqole_preload_filter', 10, 2 );
	function digiqole_preload_filter( $html, $handle ){
		if (strcmp($handle, 'digiqole-all-style') == 0) {
			$html = str_replace("rel='stylesheet'", "rel='preload' as='style'", $html);
		}
		return $html;
	}

	add_filter( 'script_loader_tag', function ( $tag, $handle ) {
		if ( 'digiqole-all-script' !== $handle )
			return $tag;
	
		return str_replace( ' src', ' defer="defer" src', $tag );
	}, 10, 2 );
}