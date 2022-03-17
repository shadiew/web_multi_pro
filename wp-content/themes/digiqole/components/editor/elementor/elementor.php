<?php

if ( ! defined( 'ABSPATH' ) ) exit;

if(defined('ELEMENTOR_VERSION')):
    
include_once DIGIQOLE_EDITOR . '/elementor/manager/controls.php';

class DIGIQOLE_Shortcode{

	/**
     * Holds the class object.
     *
     * @since 1.0
     *
     */
    public static $_instance;
    

    /**
     * Localize data array
     *
     * @var array
     */
    public $localize_data = array();

	/**
     * Load Construct
     * 
     * @since 1.0
     */

	public function __construct(){

		add_action('elementor/init', array($this, 'digiqole_elementor_init'));
        add_action('elementor/controls/controls_registered', array( $this, 'digiqole_icon_pack' ), 11 );

        if(!class_exists('ElementsKit_Lite')){
            add_action('elementor/controls/controls_registered', array( $this, 'control_image_choose' ), 13 );
            add_action('elementor/controls/controls_registered', array( $this, 'digiqole_ajax_select2' ), 13 );
        }

        add_action('elementor/widgets/widgets_registered', array($this, 'digiqole_shortcode_elements'));
        add_action( 'elementor/editor/after_enqueue_styles', array( $this, 'editor_enqueue_styles' ) );
        add_action( 'elementor/frontend/before_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
        add_action( 'elementor/preview/enqueue_styles', array( $this, 'preview_enqueue_scripts' ) );

        // elemntor icon load
        $this -> Digiqole_elementor_icon_pack();
        
	}


    /**
     * Enqueue Scripts
     *
     * @return void  
     */ 
    
     public function enqueue_scripts() {
        wp_enqueue_script( 'digiqole-main-elementor', DIGIQOLE_JS  . '/elementor.js',array( 'jquery', 'elementor-frontend' ), DIGIQOLE_VERSION, true );
    }

    /**
     * Enqueue editor styles
     *
     * @return void
     */

    public function editor_enqueue_styles() {
        wp_enqueue_style( 'digiqole-ts-icon-elementor', DIGIQOLE_CSS.'/icon-font.css',null, DIGIQOLE_VERSION );
        wp_enqueue_style( 'digiqole-panel-elementor', DIGIQOLE_CSS.'/panel.css',null, DIGIQOLE_VERSION );
        wp_enqueue_script('digiqole-admin', DIGIQOLE_JS . '/digiqole-admin.js', array('jquery'), DIGIQOLE_VERSION, true);
    }

    /**
     * Preview Enqueue Scripts
     *
     * @return void
     */

    public function preview_enqueue_scripts() {}
	/**
     * Elementor Initialization
     *
     * @since 1.0
     *
     */

    public function DIGIQOLE_elementor_init(){
    
        \Elementor\Plugin::$instance->elements_manager->add_category(
            'digiqole-elements',
            [
                'title' =>esc_html__( 'digiqole', 'digiqole' ),
                'ts-icon' => 'fa fa-plug',
            ],
            1
        );
    }

    /**
     * Extend Icon pack core controls.
     *
     * @param  object $controls_manager Controls manager instance.
     * @return void
     */

    public function digiqole_icon_pack( $controls_manager ) {

        require_once DIGIQOLE_EDITOR_ELEMENTOR. '/controls/icon.php';

        $controls = array(
            $controls_manager::ICON => 'DIGIQOLE_Icon_Controler',
        );

        foreach ( $controls as $control_id => $class_name ) {
            $controls_manager->unregister_control( $control_id );
            $controls_manager->register_control( $control_id, new $class_name() );
        }

    }
    // registering ajax select 2 control
    public function digiqole_ajax_select2( $controls_manager ) {
        require_once DIGIQOLE_EDITOR_ELEMENTOR. '/controls/select2.php';
        $controls_manager->register_control( 'ajaxselect2', new \Control_Ajax_Select2() );
    }
    
    // registering image choose
    public function control_image_choose( $controls_manager ) {
        require_once DIGIQOLE_EDITOR_ELEMENTOR. '/controls/choose.php';
        $controls_manager->register_control( 'imagechoose', new \Control_Image_Choose() );
    }

    public function digiqole_shortcode_elements($widgets_manager){
       
      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/post-tab.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_post_tab_widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/post-block.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Post_block_Widget());
     
      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/post-list-tab.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_post_list_tab_widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/category-list.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Category_List_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/category-classic.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Category_List_classic_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/post-grid.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Post_Grid_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/post-grid-loadmore.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Post_Grid_Loadmore_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/post-list.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Post_List_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/video-post-tab.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Video_Post_Tab_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/post-grid-slider.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Post_Grid_Slider_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/post-block-slider.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Post_block_Slider_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/title.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Title_Widget());
      
      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/comments.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Comment_Widget());
    

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/main-slider.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Main_Slider_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/post-slider.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Post_Slider_Widget());


      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/video-post-slider2.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Video_Post_Slider2_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/editor-pick.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Editor_Pick_Post_Slider_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/post-vertical-grid.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Post_Vertical_Grid_Widget());
      
      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/feature-post-tab.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Feature_Post_Tab_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/post-horizontal-block.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Horizonal_Post_Block_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/site-logo.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Site_Logo_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/date.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Site_Date_Widget());

      if(class_exists('\Elementor\Digiqole_Widget_Instagram_Feed')){
        $widgets_manager->register_widget_type(new Elementor\Digiqole_Widget_Instagram_Feed());
      }

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/posts-toptitle.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Posts_Toptitle_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/back-to-top.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_BackToTop_Widget());

      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/news-ticker.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_News_Ticker_Widget());
   
      require_once DIGIQOLE_EDITOR_ELEMENTOR.'/widgets/dark-light-switcher.php';
      $widgets_manager->register_widget_type(new Elementor\Digiqole_Darklight_Switcher_Widget());

    }
    
	public static function digiqole_get_instance() {
        if (!isset(self::$_instance)) {
            self::$_instance = new DIGIQOLE_Shortcode();
        }
        return self::$_instance;
    }

    /**
     * Extend Icon pack core controls.
     *
     * @param  object $controls_manager Controls manager instance.
     * @return void
     */

    
    // elementor icon fonts loaded
    public function Digiqole_elementor_icon_pack(  ) {

		$this->__generate_font();
		
        add_filter( 'elementor/icons_manager/additional_tabs', [ $this, '__add_font']);
		
    }
    
    public function __add_font( $font){
        $font_new['icon-electionify'] = [
            'name' => 'icon-digiqole',
            'label' => esc_html__( 'Digiqole Icons', 'digiqole' ),
            'url' => DIGIQOLE_CSS . '/icon-font.css',
            'enqueue' => [ DIGIQOLE_CSS . '/icon-font.css' ],
            'prefix' => 'ts-',
            'displayPrefix' => 'ts-icon',
            'labelIcon' => 'ts-icon ts-play_icon',
            'ver' => '5.9.0',
            'fetchJson' => DIGIQOLE_JS . '/icon-font.js',
            'native' => true,
        ];
        return  array_merge($font, $font_new);
    }


    public function __generate_font(){
        global $wp_filesystem;

        require_once ( ABSPATH . '/wp-admin/includes/file.php' );
        WP_Filesystem();
        $css_file =  DIGIQOLE_CSS_DIR . '/icon-font.css';
    
        if ( $wp_filesystem->exists( $css_file ) ) {
            $css_source = $wp_filesystem->get_contents( $css_file );
        } // End If Statement
        
        preg_match_all( "/\.(ts-.*?):\w*?\s*?{/", $css_source, $matches, PREG_SET_ORDER, 0 );
        $iconList = []; 
        
        foreach ( $matches as $match ) {
            $new_icons[$match[1] ] = str_replace('ts-', '', $match[1]);
            $iconList[] = str_replace('ts-', '', $match[1]);
        }

        $icons = new \stdClass();
        $icons->icons = $iconList;
        $icon_data = json_encode($icons);
        
        $file = DIGIQOLE_THEME_DIR . '/assets/js/icon-font.js';
        
            global $wp_filesystem;
            require_once ( ABSPATH . '/wp-admin/includes/file.php' );
            WP_Filesystem();
            if ( $wp_filesystem->exists( $file ) ) {
                $content =  $wp_filesystem->put_contents( $file, $icon_data) ;
            } 
        
    }

}
$DIGIQOLE_Shortcode = DIGIQOLE_Shortcode::digiqole_get_instance();

endif;