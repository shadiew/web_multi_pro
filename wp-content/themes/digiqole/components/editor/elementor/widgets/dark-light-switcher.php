<?php

namespace Elementor;

if ( ! defined( 'ABSPATH' ) ) exit;


class Digiqole_Darklight_Switcher_Widget extends Widget_Base {


  public $base;

    public function get_name() {
        return 'digiqole-darklight';
    }

    public function get_title() {

        return esc_html__( 'Dark Light Switcher', 'digiqole' );

    }

    public function get_icon() { 
        return 'fas fa-toggle-on';
    }

    public function get_categories() {
        return ['digiqole-elements'];
    }

    protected function _register_controls() {

        $this->start_controls_section(
            'section_tab',
            [
                'label' => esc_html__('Switcher settings', 'digiqole'),
            ]
        );
		
        $this->add_control(
            'switcher_bg_color',
            [
                'label' => esc_html__('Switcher Color', 'digiqole'),
                'type' => Controls_Manager::COLOR,
                'default' => '',
                'selectors' => [
                    '{{WRAPPER}} .digiqole-darklight-widget .color_swicher .switch_container' => 'background: {{VALUE}};',
                ],
            ]
        );
        $this->end_controls_section();
    }

    protected function render( ) { 
        $settings = $this->get_settings();
    ?>
    <div class="digiqole-darklight-widget">
        <div class="color_swicher change-mode">
            <div class="switch_container">
                <i class="ts-icon ts-icon-sun"></i>
                <i class="ts-icon ts-icon-moon"></i>
            </div>
        </div>
    </div>

    <?php  
    }
    protected function content_template() { }
}