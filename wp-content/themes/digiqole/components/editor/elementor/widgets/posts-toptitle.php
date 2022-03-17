<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit;

class Digiqole_Posts_Toptitle_Widget extends Widget_Base {

  public $base;

    public function get_name() {
        return 'newszone-posts-toptitle';
    }

    public function get_title() {
        return esc_html__( 'Post Top Title', 'digiqole' );
    }

    public function get_icon() { 
        return 'eicon-post-list';
    }

    public function get_categories() {
        return [ 'digiqole-elements' ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_tab',
            [
                'label' => esc_html__('Posts', 'digiqole'),
            ]
        );
 
        $this->add_control(

            'grid_style', [
                'label' => esc_html__('Choose Style', 'digiqole'),
                'type' => Custom_Controls_Manager::IMAGECHOOSE,
                'default' => 'style1',
                'options' => [
                  'style1' => [
                     'title' =>esc_html__( 'Style 1', 'digiqole' ),
                           'imagelarge' => DIGIQOLE_IMG . '/elementor/post-list/style1-l.png',
                           'imagesmall' => DIGIQOLE_IMG . '/elementor/post-list/style1-l.png',
                           'width' => '50%',
                  ],				
				  ],

            ]
        ); 
     
        $this->add_control(
          'post_count',
          [
            'label'         => esc_html__( 'Post count', 'digiqole' ),
            'type'          => Controls_Manager::NUMBER,
            'default'       => '4',

          ]
        );

        $this->add_control(
         'post_title_crop',
         [
           'label'         => esc_html__( 'Post title crop', 'digiqole' ),
           'type'          => Controls_Manager::NUMBER,
           'default'       => '100',
          
         ]
       );

       $this->add_responsive_control(
			'thumbnail_height',
			[
				'label' =>esc_html__( 'Image height', 'digiqole' ),
                'type' => \Elementor\Controls_Manager::SLIDER,
				'range' => [
					'px' => [
						'min' => 0,
						'max' => 1000,
					],
				],
				'devices' => [ 'desktop', 'tablet', 'mobile' ],
				           
            'condition' => [ 'grid_style' => ['style1'] ],
				'selectors' => [
					'{{WRAPPER}} .ts-overlay-style' => 'min-height: {{SIZE}}{{UNIT}};',
				],
			]
      );
 
        $this->add_control(
         'show_desc',
         [
             'label' => esc_html__('Show post description', 'digiqole'),
             'type' => Controls_Manager::SWITCHER,
             'label_on' => esc_html__('Yes', 'digiqole'),
             'label_off' => esc_html__('No', 'digiqole'),
             'default' => 'yes',
             'condition' => [ 'grid_style' => ['style1'] ]
         ]
         ); 
         $this->add_control(
            'post_content_crop',
            [
              'label'         => esc_html__( 'Post content crop', 'digiqole' ),
              'type'          => Controls_Manager::NUMBER,
              'default'       => '50',
              'condition' => [ 'show_desc' => ['yes'],'grid_style' => ['style1'] ]
            ]
        );

        $this->add_control(
         'ts_offset_enable',
            [
               'label' => esc_html__('Post skip', 'digiqole'),
               'type' => Controls_Manager::SWITCHER,
               'label_on' => esc_html__('Yes', 'digiqole'),
               'label_off' => esc_html__('No', 'digiqole'),
               'default' => 'no',
               
            ]
      );
      
      $this->add_control(
         'ts_offset_item_num',
         [
         'label'         => esc_html__( 'Skip post count', 'digiqole' ),
         'type'          => Controls_Manager::NUMBER,
         'default'       => '1',
         'condition' => [ 'ts_offset_enable' => 'yes' ]

         ]
      );
         
     
      $this->add_control(
            'show_author',
               [
                  'label' => esc_html__('Show Author', 'digiqole'),
                  'type' => Controls_Manager::SWITCHER,
                  'label_on' => esc_html__('Yes', 'digiqole'),
                  'label_off' => esc_html__('No', 'digiqole'),
                  'default' => 'no',
                  
               ]
      );
   
        

    $this->add_control(
      'show_author_avator',
         [
            'label' => esc_html__('Show Author image', 'digiqole'),
            'type' => Controls_Manager::SWITCHER,
            'label_on'  => esc_html__('Yes', 'digiqole'),
            'label_off' => esc_html__('No', 'digiqole'),
            'default' => 'no',
         ]
     );
    
     $this->add_responsive_control(
      'author_avator_custom_dimension',
      [
         'label' =>esc_html__( 'Avatar image size', 'digiqole' ),
             'type' => \Elementor\Controls_Manager::SLIDER,
         'range' => [
            'px' => [
               'min' => 0,
               'max' => 100,
            ],
         ],
         'condition' => [ 'show_author_avator' => ['yes'] ],
         'devices' => [ 'desktop', 'tablet', 'mobile' ],
         'desktop_default' => [
            'size' => 45,
            'unit' => 'px',
         ],
         'tablet_default' => [
            'size' => 45,
            'unit' => 'px',
         ],
         'mobile_default' => [
            'size' => 45,
            'unit' => 'px',
         ],
         'default' => [
            'unit' => 'px',
            'size' => 45,
         ],
         'selectors' => [
            '{{WRAPPER}} .ts-author-avatar img' => 'height: {{SIZE}}{{UNIT}};width: {{SIZE}}{{UNIT}};',
           
         ],
      ]
   );

        $this->add_control(
            'post_cats',
            [
                'label' =>esc_html__('Select Categories', 'digiqole'),
                'type'      => Custom_Controls_Manager::SELECT2,
                'options'   =>$this->post_category(),
                'label_block' => true,
                'multiple'  => true,
            ]
        );

        
        $this->add_control(
         'post_tags',
         [
            'label' =>esc_html__('Select tags', 'digiqole'),
            'type'      => Controls_Manager::SELECT2,
            'options'   => digiqole_post_tags(),
            'label_block' => true,
            'multiple'  => true,
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
         'show_comment',
         [
               'label' => esc_html__('Show Comment', 'digiqole'),
               'type' => Controls_Manager::SWITCHER,
               'label_on' => esc_html__('Yes', 'digiqole'),
               'label_off' => esc_html__('No', 'digiqole'),
               'default' => 'yes',
               'condition' => [ 'grid_style' => ['style1'] ]

         ]
      );

      $this->add_control(
         'show_view_count',
         [
               'label' => esc_html__('Show view Count', 'digiqole'),
               'type' => Controls_Manager::SWITCHER,
               'label_on' => esc_html__('Yes', 'digiqole'),
               'label_off' => esc_html__('No', 'digiqole'),
               'default' => 'yes',
               'condition' => [ 'grid_style' => ['style1'] ]

         ]
      );
      $this->add_control(
            'col_reverse',
            [
                'label' => esc_html__('Column Reverse', 'digiqole'),
                'type' => Controls_Manager::SWITCHER,
                'label_on' => esc_html__('Yes', 'digiqole'),
                'label_off' => esc_html__('No', 'digiqole'),
                'default' => 'yes',
                'condition' => [ 'grid_style' => ['style1'] ]
            ]
        );

        $this->add_control(
            'post_sortby',
            [
                'label'     =>esc_html__( 'Post sort by', 'digiqole' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'latestpost',
                'options'   => [
                        'latestpost'      =>esc_html__( 'Latest posts', 'digiqole' ),
                        'popularposts'    =>esc_html__( 'Popular posts', 'digiqole' ),
                        'mostdiscussed'    =>esc_html__( 'Most discussed', 'digiqole' ),
                        'title'       =>esc_html__( 'Title', 'digiqole' ),
                        'name'       =>esc_html__( 'Name', 'digiqole' ),
                        'rand'       =>esc_html__( 'Random', 'digiqole' ),
                        'ID'       =>esc_html__( 'ID', 'digiqole' ),
                    ],
            ]
        );
        $this->add_control(
            'post_order',
            [
                'label'     =>esc_html__( 'Post order', 'digiqole' ),
                'type'      => Controls_Manager::SELECT,
                'default'   => 'DESC',
                'options'   => [
                        'DESC'      =>esc_html__( 'Descending', 'digiqole' ),
                        'ASC'       =>esc_html__( 'Ascending', 'digiqole' ),
                    ],
            ]
        );

        $this->end_controls_section();

      $this->start_controls_section('digiqole_style_block_feature_section',
         [
            'label' => esc_html__( ' Post feature', 'digiqole' ),
            'tab' => Controls_Manager::TAB_STYLE,
         ]
     );

     $this->add_responsive_control(
      'title_margin',
      [
         'label' => esc_html__( 'Title margin', 'digiqole' ),
         'type' => Controls_Manager::DIMENSIONS,
             'size_units' => [ 'px','%'],
         'selectors' => [
            '{{WRAPPER}} .ts-posts-toptitle-item-1 .post-title' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
         ],
      ]
     );

     $this->add_responsive_control(
      'meta_margin',
      [
         'label' => esc_html__( 'Meta margin', 'digiqole' ),
         'type' => Controls_Manager::DIMENSIONS,
             'size_units' => [ 'px','%'],
         'selectors' => [
            '{{WRAPPER}} .ts-posts-toptitle-item-1 .post-meta' => 'margin: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
         ],
      ]
     );

   $this->end_controls_section();

   $this->start_controls_section('digiqole_style_block_section',
         [
            'label' => esc_html__( 'Post', 'digiqole' ),
            'tab' => Controls_Manager::TAB_STYLE,
         ]
   );

   $this->add_control(
      'block_title_color',
         [
            'label' => esc_html__('Title color', 'digiqole'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
          
            'selectors' => [
               '{{WRAPPER}} .ts-posts-toptitle-item-1 .post-title a' => 'color: {{VALUE}};',
            ],
      ]
   );

   $this->add_control(
         'block_title_hv_color',
         [
            'label' => esc_html__('Title hover color', 'digiqole'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
          
            'selectors' => [
               '{{WRAPPER}} .ts-posts-toptitle-item-1 .post-title:hover a' => 'color: {{VALUE}};',
            ],
         ]
      );

      $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'post_title_typography',
				'label' => esc_html__( 'Typography title ', 'digiqole' ),
            'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
             'selector' => '{{WRAPPER}} .ts-posts-toptitle-item-1 .post-title a',
			]
        );
      
        $this->add_control(
         'block_content_color',
         [
            'label' => esc_html__('Content color', 'digiqole'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
          
            'selectors' => [
               '{{WRAPPER}} .ts-posts-toptitle-item-1 .post-content p' => 'color: {{VALUE}};',
            ],
         ]
      );

      $this->add_group_control(
      Group_Control_Typography::get_type(),
         [
            'name' => 'post_content_typography',
            'label' => esc_html__( 'Typography content ', 'digiqole' ),
            'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_3,
            'selector' => '{{WRAPPER}} .post-content p',
         ]
      );
      $this->add_control(
         'block_meta_date_color',
         [
            'label' => esc_html__('Meta color', 'digiqole'),
            'type' => Controls_Manager::COLOR,
            'default' => '',
          
            'selectors' => [
               '{{WRAPPER}} .ts-posts-toptitle-item-1 .post-meta span' => 'color: {{VALUE}};',             
            ],
         ]
      );

      $this->add_responsive_control(
			'content_padding',
			[
				'label' => esc_html__( 'Content Padding', 'digiqole' ),
				'type' => Controls_Manager::DIMENSIONS,
                'size_units' => [ 'px','%'],
				'selectors' => [
					'{{WRAPPER}} .ts-posts-toptitle-item-1 .post-content' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
        );

      $this->end_controls_section();
         
    }

    protected function render( ) { 
        $settings = $this->get_settings();
        
        $show_date          = $settings['show_date'];
        $show_comment       = $settings['show_comment'];
        $show_desc          = $settings['show_desc'];
        $post_title_crop    = $settings['post_title_crop'];
        $post_content_crop  = $settings['post_content_crop'];
        $show_author        = $settings['show_author'];      
        $show_view_count    = $settings['show_view_count'];
        $col_reverse        = $settings['col_reverse'];
        $show_author_avator = 'no';
        $readmore           = '';
        

        $arg = [
            'post_type'   =>  'post',
            'post_status' => 'publish',
            'order'       => $settings['post_order'],
            'posts_per_page' => $settings['post_count'],
            'category__in'   => $settings['post_cats'],
            'tag__in'        => $settings['post_tags'],
            'suppress_filters' => false,
        ];

        if($settings['ts_offset_enable']=='yes'){
         $arg['offset'] = $settings['ts_offset_item_num'];
       }
         
       switch($settings['post_sortby']){
         case 'popularposts':
              $arg['meta_key'] = 'newszone_post_views_count';
              $arg['orderby'] = 'meta_value_num';
         break;
         case 'mostdiscussed':
              $arg['orderby'] = 'comment_count';
         break;
         case 'title':
             $arg['orderby'] = 'title';
         break;
         case 'ID':
             $arg['orderby'] = 'ID';
         break;
         case 'rand':
             $arg['orderby'] = 'rand';
         break;
         case 'name':
             $arg['orderby'] = 'name';
         break;
         default:
              $arg['orderby'] = 'date';
         break;
      }
        $settings['ts_image_size'] = 'post-thumbnail';
        $query = get_posts( $arg );

        require 'style/posts-toptitle/content-'.$settings['grid_style'].'.php';
        
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
