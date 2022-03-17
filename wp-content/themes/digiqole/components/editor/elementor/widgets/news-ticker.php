<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit;


class Digiqole_News_Ticker_Widget extends Widget_Base {


  public $base;

    public function get_name() {
        return 'digiqole-newsticker';
    }

    public function get_title() {

        return esc_html__( 'News Ticker', 'digiqole'  );

    }

    public function get_icon() { 
        return 'eicon-image';
    }

    public function get_categories() {
        return [ 'digiqole-elements' ];
    }

    protected function _register_controls() {

    $this->start_controls_section(
        'section_tab',
        [
            'label' => esc_html__('News Ticket Content', 'digiqole' ),
        ]
    );
    $this->add_control(
    'show_navigation',
    [
        'label' => esc_html__('Show Navigation', 'digiqole'),
        'type' => Controls_Manager::SWITCHER,
        'label_on' => esc_html__('Yes', 'digiqole'),
        'label_off' => esc_html__('No', 'digiqole'),
        'default' => 'no',
        
    ]
    );

    $this->add_control(
        'title', [
            'label'			  => esc_html__( 'Title', 'digiqole' ),
            'type'			  => Controls_Manager::TEXT,
            'label_block'	  => true,
            'placeholder'    => esc_html__( 'News ticker title', 'digiqole' ),
            'default'	     => esc_html__( 'Trending', 'digiqole' ),
            
        ]
    );

    $this->add_control(
        'orderby',
        [
            'label'     =>esc_html__( 'Posts Select By', 'digiqole' ),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'date',
            'options'   => [
                    'date'              =>esc_html__( 'Date', 'digiqole' ),
                    'ID'                =>esc_html__( 'ID', 'digiqole' ),
                    'title'             =>esc_html__( 'Title', 'digiqole' ),
                    'comment_count'     =>esc_html__( 'Comment Count', 'digiqole' ),
                    'name'              =>esc_html__( 'Name', 'digiqole' ),
                    'rand'              =>esc_html__( 'Random', 'digiqole' ),
                    'menu_order'        =>esc_html__( 'Menu Order', 'digiqole' ),
                ],
        ]
    );

    $this->add_control(
        'order',
        [
            'label'     =>esc_html__( 'Post Order', 'digiqole' ),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'DESC',
            'options'   => [
                    'DESC'      =>esc_html__( 'Descending', 'digiqole' ),
                    'ASC'       =>esc_html__( 'Ascending', 'digiqole' ),
                ],
        ]
    );

    $this->add_control(
        'posts_per_page',
        [
            'label'         => esc_html__( 'Post Count', 'digiqole' ),
            'type'          => Controls_Manager::NUMBER,
            'default'       => '5',
        ]
    );

    $this->add_control(
        'post_cats',
        [
            'label' =>esc_html__('Select Categories', 'digiqole'),
            'type'      => Controls_Manager::SELECT2,
            'options'   => $this->post_category(),
            'label_block' => true,
            'multiple'  => true,
        ]
    );
    
    $this->add_responsive_control(
        'text_align', [
            'label'          => esc_html__( 'Alignment', 'digiqole'  ),
            'type'           => Controls_Manager::CHOOSE,
            'options'        => [

                'left'         => [
                    'title'    => esc_html__( 'Left', 'digiqole'  ),
                    'ts-icon'     => 'fa fa-align-left',
                ],
                'center'     => [
                    'title'    => esc_html__( 'Center', 'digiqole'  ),
                    'ts-icon'     => 'fa fa-align-center',
                ],
                'right'         => [
                    'title'     => esc_html__( 'Right', 'digiqole'  ),
                    'ts-icon'     => 'fa fa-align-right',
                ],
            ],
            'default'         => '',
            'selectors' => [
                '{{WRAPPER}} .tranding-bg-white' => 'text-align: {{VALUE}};'
            ],
        ]
    ); 

    $this->add_responsive_control(
        'ticker_padding',
        [
            'label' =>esc_html__( 'Padding', 'digiqole'  ),
            'type' => \Elementor\Controls_Manager::DIMENSIONS,
            'size_units' => [ 'px', 'em', '%' ],
            'selectors' => [
                '{{WRAPPER}} .tranding-bg-white' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
            ],
        ]
    );
       
    $this->end_controls_section();

    $this->start_controls_section('digiqole_style_block_section',
    [
        'label' => esc_html__( ' Post', 'digiqole' ),
        'tab' => Controls_Manager::TAB_STYLE,
    ]
    );

    $this->add_control(
        'title_color',
        [
           'label' => esc_html__('Title color', 'digiqole'),
           'type' => Controls_Manager::COLOR,
           'selectors' => [
              '{{WRAPPER}} .tranding-bar .trending-slide .trending-title' => 'color: {{VALUE}};',
           ],
        ]
     );

     $this->add_group_control(
        Group_Control_Typography::get_type(),
        [
           'name' => 'post_title_typography',
           'label' => esc_html__( 'Typography', 'digiqole' ),
           'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
           'selector' => '{{WRAPPER}} .tranding-bar .trending-slide .trending-title',
        ]
     );

     $this->add_control(
        'news_color',
        [
           'label' => esc_html__('News Color', 'digiqole'),
           'type' => Controls_Manager::COLOR,
           'selectors' => [
              '{{WRAPPER}} .tranding-bar .trending-slide .post-title a' => 'color: {{VALUE}};',
           ],
        ]
     );

     $this->add_group_control(
        Group_Control_Typography::get_type(),
        [
           'name' => 'news_title_typography',
           'label' => esc_html__( 'Typography', 'digiqole' ),
           'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
           'selector' => '{{WRAPPER}} .tranding-bar .trending-slide .post-title a',
        ]
     );

     $this->end_controls_section();

    }

    protected function render( ) { 
        $settings = $this->get_settings();

        $title = $settings['title'];
        $newsticker_nav_enable = $settings['show_navigation'];
        

        $args =  array(
            'orderby'           => $settings['orderby'],
            'order'             => $settings['order'],
            'posts_per_page'    => $settings['posts_per_page'],
        );

        if(!empty($settings['post_cats'])) {
            $args['tax_query'] = array(
                     array(
                     'taxonomy' => 'category',
                     'field' => 'id',
                     'terms' => $settings['post_cats']
                  ) 
              );   
          }

        $posts = get_posts( $args );

        
      
    ?>
    <?php if ( $posts) : ?>
        <div class="tranding-bg-white">
            <div class="tranding-bar">
                <div id="tredingcarousel" class="trending-slide carousel slide trending-slide-bg" data-ride="carousel">
                    <?php  if($title !='') { ?>
                        <p class="trending-title"><i class="ts-icon ts-icon-bolt"></i> <?php echo esc_html($title);?></p>
                    <?php } ?>
                    <div class="carousel-inner">
                        <?php $k = 1; ?>
                        <?php foreach($posts as $post): ?>                            
                            <?php if( $k == 1 ){ ?>
                            <div class="carousel-item active">
                            <?php } else { ?>
                            <div class="carousel-item">
                            <?php } ?>
                                <div class="post-content">
                                    <p class="post-title title-small"><a href="<?php echo esc_url( get_permalink($post->ID) ); ?>"><?php echo get_the_title($post->ID); ?></a></p>
                                </div><!--/.most-view-item-content -->
                            </div><!--/.carousel-item -->
                                <?php
                                $k++;
                        endforeach;
                        wp_reset_postdata(); ?>
                        </div> <!--/.carousel-inner-->
                        <?php if( $newsticker_nav_enable == 'yes' ) { ?>
                            <div class="tp-control">
                                <a class="tp-control-prev" href="#tredingcarousel" role="button" data-slide="prev">
                                    <i class="ts-icon ts-icon-angle-left"></i>
                                </a>
                                <a class="tp-control-next" href="#tredingcarousel" role="button" data-slide="next">
                                    <i class="ts-icon ts-icon-angle-right"></i>
                                </a>
                            </div>
                        <?php } ?>
            </div> <!--/.trending-slide-->
        </div> <!--/.tranding-bar-->
    </div>
    <?php endif; ?>
    <?php  
    }
    protected function content_template() { }

    public function post_category() {

        $terms = get_terms( array(
              'taxonomy'    => 'category',
              'hide_empty'  => false,
              'posts_per_page' => -1, 
        ) );
  
        $cat_list = [];
        foreach($terms as $post) {
        $cat_list[$post->term_id]  = [$post->name];
        }
        return $cat_list;
     }
}