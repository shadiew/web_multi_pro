<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit;


class Digiqole_Site_Date_Widget extends Widget_Base {


  public $base;

    public function get_name() {
        return 'digiqole-date';
    }

    public function get_title() {

        return esc_html__( 'Digiqole Date', 'digiqole'  );

    }

    public function get_icon() { 
        return 'far fa-calendar-alt';
    }

    public function get_categories() {
        return [ 'digiqole-elements' ];
    }

    protected function _register_controls() {

      $this->start_controls_section(
            'section_tab',
            [
                'label' => esc_html__('Date settings', 'digiqole' ),
            ]
      );
         
      $this->add_control('title_color', 
            [
                'label'		 => esc_html__( 'Date color', 'digiqole' ),
                'type'		 => Controls_Manager::COLOR,
                'selectors'	 => [
                '{{WRAPPER}}  .digiqole-date span' => 'color: {{VALUE}};',
           ],
        ]
    );

        $this->add_responsive_control(
            'date_text_align', 
            [
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
                    '{{WRAPPER}} .digiqole-date' => 'text-align: {{VALUE}};'
                ],
            ]
        );
        $this->add_group_control(
			Group_Control_Typography::get_type(),
			[
				'name' => 'date_typography',
				'label' => esc_html__( 'Date Typography', 'digiqole' ),
				'scheme' => \Elementor\Core\Schemes\Typography::TYPOGRAPHY_1,
                'selector' => '{{WRAPPER}} .digiqole-date span',
			]
        );
 
        $this->end_controls_section();
    }

    protected function render( ) { 
        $settings = $this->get_settings();
    ?>
    <div class="digiqole-date">
       <span>
            <i class="ts-icon ts-icon-calendar-solid" aria-hidden="true"></i>  
            <?php echo date_i18n(get_option('date_format')); ?>
       </span>
    </div>

    <?php  
    }
    protected function content_template() { }
}