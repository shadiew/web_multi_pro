<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit;

class Digiqole_post_list_tab_widget extends Widget_Base {

	public function get_name() {
		return 'newszone-post-list-tab';
	}

	public function get_title() {
		return esc_html__( 'Post List tab', 'digiqole' );
	}

	public function get_icon() {
		return 'eicon-gallery-group';
	}

	public function get_categories() {
		return [ 'digiqole-elements' ];
   }
   protected function _register_controls() {
    
      $this->start_controls_section(
         'section_tab',
         [
             'label' => esc_html__('Post list', 'digiqole'),
         ]
     );
    
     $this->add_control(
         'tab_left_title',
         [
             'label' => esc_html__('Tab Left Title', 'digiqole'),
             'type' => Controls_Manager::TEXT,
             'default' => esc_html__( 'RECENT', 'digiqole' )
         ]
     );

     $this->add_control(
      'tab_center_title',
      [
          'label' => esc_html__('Tab Center Title', 'digiqole'),
          'type' => Controls_Manager::TEXT,
          'default' => esc_html__( 'POPULAR', 'digiqole' )
      ]
     );

     $this->add_control(
      'tab_right_title',
         [
            'label' => esc_html__('Tab right Title', 'digiqole'),
            'type' => Controls_Manager::TEXT,
            'default' => esc_html__( 'COMMENT', 'digiqole' )
         ]
     );
     
     $this->add_control(
       'post_count',
       [
         'label'         => esc_html__( 'Post count', 'digiqole' ),
         'type'          => Controls_Manager::NUMBER,
         'default'       => '5',
       ]
     );
     $this->add_control(
       'post_title_crop',
       [
         'label'         => esc_html__( 'Post title crop', 'digiqole' ),
         'type'          => Controls_Manager::NUMBER,
         'default'       => '4',
       ]
     );
     
     $this->add_control(
         'show_cat',
         [
             'label' => esc_html__('Show Category', 'digiqole'),
             'type' => Controls_Manager::SWITCHER,
             'label_on' => esc_html__('Yes', 'digiqole'),
             'label_off' => esc_html__('No', 'digiqole'),
             'default' => 'yes',
         ]
     );
     $this->add_control(
      'show_date',
      [
          'label' => esc_html__('Show Date', 'digiqole'),
          'type' => Controls_Manager::SWITCHER,
          'label_on' => esc_html__('Yes', 'digiqole'),
          'label_off' => esc_html__('No', 'digiqole'),
          'default' => 'yes',
      ]
  );
     $this->add_control(
         'recent_post_sortby',
         [
             'label'     =>esc_html__( 'left post sort by', 'digiqole' ),
             'type'      => Controls_Manager::SELECT,
             'default'   => 'latestpost',
             'options'   => [
                     'latestpost'      =>esc_html__( 'Latest posts', 'digiqole' ),
                     'viewcount'    =>esc_html__( 'View count', 'digiqole' ),
                     'mostdiscussed'    =>esc_html__( 'Most discussed', 'digiqole' ),
                     'title'       =>esc_html__( 'Title', 'digiqole' ),
                    'name'       =>esc_html__( 'Name', 'digiqole' ),
                    'rand'       =>esc_html__( 'Random', 'digiqole' ),
                    'ID'       =>esc_html__( 'ID', 'digiqole' ),
                 ],
         ]
     );
     $this->add_control(
         'post_sortby',
         [
             'label'     =>esc_html__( 'center post sort by', 'digiqole' ),
             'type'      => Controls_Manager::SELECT,
             'default'   => 'latestpost',
             'options'   => [
                'latestpost'      =>esc_html__( 'Latest posts', 'digiqole' ),
                'viewcount'    =>esc_html__( 'View count', 'digiqole' ),
                'mostdiscussed'    =>esc_html__( 'Most discussed', 'digiqole' ),
                'title'       =>esc_html__( 'Title', 'digiqole' ),
                'name'       =>esc_html__( 'Name', 'digiqole' ),
                'rand'       =>esc_html__( 'Random', 'digiqole' ),
                'ID'       =>esc_html__( 'ID', 'digiqole' ),
                 ],
         ]
     );
     $this->add_control(
        'comments_post_sortby',
        [
            'label'     =>esc_html__( 'Right post sort by', 'digiqole' ),
            'type'      => Controls_Manager::SELECT,
            'default'   => 'latestpost',
            'options'   => [
                    'latestpost'      =>esc_html__( 'Latest posts', 'digiqole' ),
                    'viewcount'    =>esc_html__( 'View count', 'digiqole' ),
                    'mostdiscussed'    =>esc_html__( 'Most discussed', 'digiqole' ),
                    'title'       =>esc_html__( 'Title', 'digiqole' ),
                    'name'       =>esc_html__( 'Name', 'digiqole' ),
                    'rand'       =>esc_html__( 'Random', 'digiqole' ),
                    'ID'       =>esc_html__( 'ID', 'digiqole' ),
                ],
        ]
    );

     $this->end_controls_section();

     $this->start_controls_section('digiqole_style_tilet_section',
     [
        'label' => esc_html__( 'Post title', 'digiqole' ),
        'tab' => Controls_Manager::TAB_STYLE,
     ]
    );
    $this->add_control(
      'post_title_color',
      [
         'label' => esc_html__('Color', 'digiqole'),
         'type' => Controls_Manager::COLOR,
         'default' => '',
         'selectors' => [
            '{{WRAPPER}}  .post-content .post-title a' => 'color: {{VALUE}};',
         ],
      ]
      );

      $this->add_control(
         'post_title_hv_color',
         [
            'label' => esc_html__('Hover Color', 'digiqole'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
            'selectors' => [
               '{{WRAPPER}}  .post-content .post-title:hover a' => 'color: {{VALUE}};',
            ],
         ]
         );

         $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'post_title_typography',
				'label' => esc_html__( 'Typography', 'digiqole' ),
				'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
				'selector' => '{{WRAPPER}} .post-list-item .post-content.media .post-title ',
			]
		);

    $this->end_controls_section();


     
        //Title Style Section
		$this->start_controls_section(
			'section_title_style', [
				'label'	 => esc_html__( 'Tab Menu active', 'digiqole' ),
				'tab'	    => Controls_Manager::TAB_STYLE,
			]
        );
     
      $this->add_control(
         'tab_menu_item_bg_color', [
 
             'label'		 => esc_html__( 'Menu Item Bg color', 'digiqole' ),
             'type'		 => Controls_Manager::COLOR,
             'selectors'	 => [
             '{{WRAPPER}} .post-list-item .recen-tab-menu.nav-tabs li a.active' => 'background-color:{{VALUE}}; ',  
             '{{WRAPPER}} .post-list-item .recen-tab-menu.nav-tabs li a:before' => 'background-color:{{VALUE}}!important; ' ,         
             '{{WRAPPER}} .post-list-item .recen-tab-menu.nav-tabs li a:after' => 'border-left-color:{{VALUE}}!important; ' ,         
             ],
         ]
 
      );
        
        $this->add_control(
			'menu_item_color', [

				'label'		 => esc_html__( 'Menu item color', 'digiqole' ),
				'type'		 => Controls_Manager::COLOR,
				'selectors'	 => [

               '{{WRAPPER}}  .post-list-item .recen-tab-menu.nav-tabs li a.active' => 'color: {{VALUE}};',
				],
			]
        );

        
        $this->end_controls_section();

   }
   
   protected function render( ) { 
      $settings = $this->get_settings();
      $show_cat = $settings['show_cat'];
      $show_date = $settings['show_date'];

      $arg_recent = [
          'post_type'   =>  'post',
          'post_status' => 'publish',
          'posts_per_page' => $settings['post_count'],
          'ignore_sticky_posts' => 1,
          'suppress_filters' => false,
      ];
      $arg_fav = [
          'post_type'   =>  'post',
          'post_status' => 'publish',
          'posts_per_page' => $settings['post_count'],
          'ignore_sticky_posts' => 1
      ];

      $arg_commnets = [

         'post_type'   =>  'post',
         'post_status' => 'publish',
         'orderby' => 'comment_count',
         'posts_per_page' => $settings['post_count'],
         'ignore_sticky_posts' => 1,
         'suppress_filters' => false,
         
      ];

      switch($settings['post_sortby']){
        case 'viewcount':
            $arg_fav['meta_key'] = 'newszone_post_views_count';
            $arg_fav['orderby'] = 'meta_value_num';
        break;
        case 'mostdiscussed':
            $arg_fav['orderby'] = 'comment_count';
        break;
        case 'title':
            $arg_fav['orderby'] = 'title';
        break;
        case 'ID':
            $arg_fav['orderby'] = 'ID';
        break;
        case 'rand':
            $arg_fav['orderby'] = 'rand';
        break;
        case 'name':
            $arg_fav['orderby'] = 'name';
        break;
        default:
            $arg_fav['orderby'] = 'date';
        break;
     }
      switch($settings['recent_post_sortby']){
        case 'viewcount':
            $arg_recent['meta_key'] = 'newszone_post_views_count';
            $arg_recent['orderby'] = 'meta_value_num';
        break;
        case 'mostdiscussed':
            $arg_recent['orderby'] = 'comment_count';
        break;
        case 'title':
            $arg_recent['orderby'] = 'title';
        break;
        case 'ID':
            $arg_recent['orderby'] = 'ID';
        break;
        case 'rand':
            $arg_recent['orderby'] = 'rand';
        break;
        case 'name':
            $arg_recent['orderby'] = 'name';
        break;
        default:
            $arg_recent['orderby'] = 'date';
        break;
    }
      switch($settings['comments_post_sortby']){
        case 'viewcount':
            $arg_commnets['meta_key'] = 'newszone_post_views_count';
            $arg_commnets['orderby'] = 'meta_value_num';
        break;
        case 'mostdiscussed':
            $arg_commnets['orderby'] = 'comment_count';
        break;
        case 'title':
            $arg_commnets['orderby'] = 'title';
        break;
        case 'ID':
            $arg_commnets['orderby'] = 'ID';
        break;
        case 'rand':
            $arg_commnets['orderby'] = 'rand';
        break;
        case 'name':
            $arg_commnets['orderby'] = 'name';
        break;
        default:
            $arg_commnets['orderby'] = 'date';
        break;
    }
      $settings['show_author'] = 'no';
      $query_recent = new \WP_Query( $arg_recent );
      ?>
      
   
      <div class="post-list-item widgets">
          <ul class="nav nav-tabs recen-tab-menu">
              <li>
                  <a class="active show" href="#<?php echo esc_attr($this->get_id()); ?>-recent" data-toggle="tab">
                  <span></span>
                     <?php echo esc_html($settings['tab_left_title']); ?>
                 </a>
              </li>
              <li>
                    <a href="#<?php echo esc_attr($this->get_id()); ?>-popular" data-toggle="tab">
                    <span></span>
                        <?php echo esc_html($settings['tab_center_title']); ?>
                    </a>
              </li>

              <li>
                    <a href="#<?php echo esc_attr($this->get_id()); ?>-tab-comment" data-toggle="tab">
                     <span></span>
                        <?php echo esc_html($settings['tab_right_title']); ?>
                    </a>
              </li>

          </ul>
          <div class="tab-content">
              <div class="tab-pane active post-tab-list post-thumb-bg" id="<?php echo esc_attr($this->get_id()); ?>-recent">
                  <?php if ( $query_recent->have_posts() ) : ?>
                        <?php $i = 0; while ($query_recent->have_posts()) : $query_recent->the_post(); $i++; ?>
                           <div class="post-content media">    
                                <?php if(has_post_thumbnail()):?>
                                    <div class="post-thumb post-thumb-radius">
                                    <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                                        <span class="digiqole-sm-bg-img" style="background-image: url(<?php echo esc_url(digiqole_post_thumbnail(get_the_ID())) ?>);"></span>                                    
                                        <span class="tab-post-count"> <?php echo esc_html($i); ?></span>
                                    </a>
                                    </div>
                                <?php endif; ?>
                              <div class="media-body">
                                   <?php if($show_cat == 'yes'): ?>
                                        <?php require DIGIQOLE_THEME_DIR . '/template-parts/blog/category/parts/cat-style2.php'; ?>
                                    <?php  endif; ?>
                                    <h3 class="post-title">
                                    <a href="<?php the_permalink(); ?>"><?php echo esc_html(wp_trim_words(get_the_title(), $settings['post_title_crop'],'')); ?></a>
                                    </h3>
                                    <?php if($show_date=='yes'): ?>
                                       <span class="post-date"> 
                                          <i class="ts-icon ts-icon-clock-regular" aria-hidden="true"></i>
                                          <?php echo get_the_date(get_option('date_format')); ?>
                                       </span>
                                    <?php endif; ?>
                              </div>
                           </div>
                        <?php endwhile; 
                        wp_reset_postdata(); ?>
                  <?php endif; ?>
              </div>
              <div class="tab-pane post-tab-list post-thumb-bg" id="<?php echo esc_attr($this->get_id()); ?>-popular">
                  <?php
                  $query_fav = new \WP_Query( $arg_fav );
                  if ( $query_fav->have_posts() ) : ?>
                      <?php $i = 0; while ($query_fav->have_posts()) : $query_fav->the_post(); $i++; ?>
                          <div class="post-content media">
                            <?php if(has_post_thumbnail()):?>    
                                <div class="post-thumb post-thumb-radius">
                                    <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                                        <span class="digiqole-sm-bg-img" style="background-image: url(<?php echo esc_url(digiqole_post_thumbnail(get_the_ID())); ?>);"></span>
                                        <span class="tab-post-count"> <?php echo esc_html($i); ?></span></a>
                                </div>
                            <?php endif; ?>
                              <div class="media-body">
                                   <?php require DIGIQOLE_THEME_DIR . '/template-parts/blog/category/parts/cat-style2.php'; ?>
                                  <h3 class="post-title">
                                      <a href="<?php the_permalink(); ?>"><?php echo esc_html(wp_trim_words(get_the_title(), $settings['post_title_crop'],'')); ?></a>
                                  </h3>
                                  <?php if($show_date=='yes'): ?>
                                       <span class="post-date"> 
                                          <i class="ts-icon ts-icon-clock-regular" aria-hidden="true"></i>
                                          <?php echo get_the_date(get_option('date_format')); ?> 
                                       </span>
                                    <?php endif; ?>
                              </div>
                          </div>
                      <?php endwhile; 
                     wp_reset_postdata(); ?>
                  <?php endif; ?>
              </div>

              <div class="tab-pane post-tab-list post-thumb-bg" id="<?php echo esc_attr($this->get_id()); ?>-tab-comment">
                  <?php
                  $query_comments = new \WP_Query( $arg_commnets );
                  if ( $query_fav->have_posts() ) : ?>
                      <?php $i = 0; while ($query_comments->have_posts()) : $query_comments->the_post(); $i++; ?>
                          <div class="post-content media"> 
                          <?php if(has_post_thumbnail()):?>   
                                <div class="post-thumb post-thumb-radius">
                                <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" title="<?php the_title_attribute(); ?>">
                                    <span class="digiqole-sm-bg-img" style="background-image: url(<?php echo esc_url(digiqole_post_thumbnail(get_the_ID())); ?>);"></span>
                                    <span class="tab-post-count"> <?php echo esc_html($i); ?></span>
                                </a>
                                </div>
                          <?php endif; ?>
                              <div class="media-body">
                                    <?php require DIGIQOLE_THEME_DIR . '/template-parts/blog/category/parts/cat-style2.php'; ?>
                                  <h3 class="post-title">
                                      <a href="<?php the_permalink(); ?>"><?php echo esc_html(wp_trim_words(get_the_title(), $settings['post_title_crop'],'')); ?></a>
                                  </h3>
                                  <?php if($show_date=='yes'): ?>
                                       <span class="post-date"> 
                                          <i class="ts-icon ts-icon-clock-regular" aria-hidden="true"></i>
                                          <?php echo get_the_date(get_option('date_format')); ?>
                                       </span>
                                    <?php endif; ?>
                              </div>
                          </div>
                      <?php endwhile; 
                      wp_reset_postdata();?>
                  <?php endif; ?>
              </div>
          </div>
      </div>
    
    <?php  
  }
   
}  