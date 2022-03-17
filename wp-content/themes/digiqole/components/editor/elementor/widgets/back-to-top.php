<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit;


class Digiqole_BackToTop_Widget extends Widget_Base {


  public $base;

    public function get_name() {
        return 'digiqole-back-to-top';
    }

    public function get_title() {

        return esc_html__( 'Digiqole Back to Top', 'digiqole' );

    }

    public function get_icon() { 
        return 'eicon-spacer';
    }

    public function get_categories() {
        return [ 'digiqole-elements' ];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_tab',
            [
                'label' => esc_html__('Back to Top Settings', 'digiqole'),
                'tab' => Controls_Manager::TAB_CONTENT,
            ]
        );	
			 
		$this->add_control(
			'backto_button_icon',
			[
				'label' => esc_html__( 'Select Icon', 'digiqole' ),
				'type' => Controls_Manager::ICONS,
				'default' => [
                    'value' => 'fas fa-arrow-up',
                    'library' => 'solid',
				]
			]
		);

        $this->add_control(
            'backto_button_bg',
            [
                'label' => esc_html__('Scroll bg Color', 'digiqole'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ts-scroll-box .BackTo' => 'background-color: {{VALUE}};',
                ],
            ]
		);
		
        $this->add_control(
            'backto_button_hov_bg',
            [
                'label' => esc_html__('Scroll Hover bg Color', 'digiqole'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ts-scroll-box .BackTo:hover' => 'background-color: {{VALUE}};',
                ],
            ]
		);
		
        $this->add_control(
            'backto_button_color',
            [
                'label' => esc_html__('Backto Color', 'digiqole'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ts-scroll-box .BackTo a' => 'color: {{VALUE}};',
                ],
            ]
		);
		
        $this->add_control(
            'backto_button_hov_color',
            [
                'label' => esc_html__('Backto Hover Color', 'digiqole'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .ts-scroll-box .BackTo:hover a' => 'color: {{VALUE}};',
                ],
            ]
        );

        $this->add_responsive_control(
			'btn_align', [
				'label'			 => esc_html__( 'Alignment', 'digiqole' ),
				'type'			 => Controls_Manager::CHOOSE,
				'options'		 => [

               'left'		 => [
                  
                  'title'	 => esc_html__( 'Left', 'digiqole' ),
						'ts-icon'	 => 'fa fa-align-left',
               
               ],
				'center'	     => [
                  
                  'title'	 => esc_html__( 'Center', 'digiqole' ),
						'ts-icon'	 => 'fa fa-align-center',
               
               ],
				'right'		 => [

						'title'	 => esc_html__( 'Right', 'digiqole' ),
                  'ts-icon'	 => 'fa fa-align-right',
                  
					],
			
				],
            'default'		 => 'center',
            'selectors' => [
                     '{{WRAPPER}} .ts-scroll-box .BackTo' => 'text-align: {{VALUE}};',

				],
			]
		);//Responsive control end
		
		
		$this->add_responsive_control(
			'backto_border_radius',
			[
				'label' => esc_html__( 'Border Radius', 'digiqole' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ts-scroll-box .BackTo' => 'border-radius: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
		  );
		  
		$this->add_responsive_control(
			'backto_border_padding',
			[
				'label' => esc_html__( 'Button Padding', 'digiqole' ),
				'type' => Controls_Manager::DIMENSIONS,
				'size_units' => [ 'px', '%', 'em' ],
				'selectors' => [
					'{{WRAPPER}} .ts-scroll-box .BackTo' => 'padding: {{TOP}}{{UNIT}} {{RIGHT}}{{UNIT}} {{BOTTOM}}{{UNIT}} {{LEFT}}{{UNIT}};',
				],
			]
      );

		$this->end_controls_section(); 
     
    }

    protected function render( ) { 
        
        $settings = $this->get_settings();
        $backto_button_icon = $settings['backto_button_icon'];

    

    ?>
    <div class="ts-scroll-box">
        <div class="BackTo">
            <a href="#">
                <?php Icons_Manager::render_icon( $backto_button_icon, [ 'aria-hidden' => 'true' ] ); ?>
            </a>
        </div>
    </div>

    <?php  
    }
    protected function content_template() { }
}