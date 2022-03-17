<?php if (!defined('ABSPATH')) die('Direct access forbidden.');
/**
 * customizer option: amp
 */

$options =[
    'amp_settings' => [
        'title'		 => esc_html__( 'AMP settings', 'digiqole' ),

        'options'	 => [            
            'amp_header_logo' => [
               'label'	        => esc_html__( 'Header Logo', 'digiqole' ),
               'type'	        => 'upload',
               'image_only'    => true,
            ],
            
            'amp_header_menu' =>array(
                'type'  => 'select',              
                'attr'  => array( 'class' => 'amp-meu', 'data-foo' => 'digiqole_amp_builder_select' ),
                'label' => esc_html__('Header Menu', 'digiqole'),             
                'choices' => digiqole_amp_menus(),              
                'no-validate' => false,
             ),

             'amp_header_top_info_show' => [
                'type'			    => 'switch',
                'label'			 => esc_html__( 'Topbar', 'digiqole' ),
                'desc'			    => esc_html__( 'Do you want to show topbar?', 'digiqole' ),
                'value'          => 'no',
                'left-choice'	 => [
                    'value'	 => 'yes',
                    'label'	 => esc_html__('Yes', 'digiqole'),
                ],
 
                'right-choice'	 => [
                    'value'	 => 'no',
                    'label'	 => esc_html__('No', 'digiqole'),
                   ],
            ],

            'amp_header_nav_search_section' => [
                'type'			    => 'switch',
                'label'			 => esc_html__( 'Search', 'digiqole' ),
                'desc'			    => esc_html__( 'Do you want to show search?', 'digiqole' ),
                'value'          => 'yes',
                'left-choice'	 => [
                    'value'	 => 'yes',
                    'label'	 => esc_html__('Yes', 'digiqole'),
                ],
 
                'right-choice'	 => [
                    'value'	 => 'no',
                    'label'	 => esc_html__('No', 'digiqole'),
                   ],
            ],
            'amp_footer_logo' => [
                'label'	        => esc_html__( 'Footer Logo', 'digiqole' ),
                'type'	        => 'upload',
                'image_only'    => true,
             ],
             'amp_footer_newsletter_section' => [
                'type'			    => 'switch',
                'label'			 => esc_html__( 'Footer Top', 'digiqole' ),
                'desc'			    => esc_html__( 'Do you want to show footer top?', 'digiqole' ),
                'value'          => 'yes',
                'left-choice'	 => [
                    'value'	 => 'yes',
                    'label'	 => esc_html__('Yes', 'digiqole'),
                ],
 
                'right-choice'	 => [
                    'value'	 => 'no',
                    'label'	 => esc_html__('No', 'digiqole'),
                   ],
            ],
            'amp_footer_mailchimp' => [
                'label'	 => esc_html__( 'Mailchimp Shortcode', 'digiqole'),
                 'type'	 => 'text',
             
              ],
            'amp_footer_copyright'	 => [
                'type'	 => 'textarea',
                'value'  =>  esc_html__('&copy; '.date('Y').', Digiqole. All rights reserved','digiqole'),
                'label'	 => esc_html__( 'Copyright text', 'digiqole' ),
                'desc'	 => esc_html__( 'This text will be shown at the footer of all pages.', 'digiqole' ),
            ],
            'amp_back_to_top'	 => [
                'type'			 => 'switch',
                'value'			 => 'hello',
                'label'			 => esc_html__( 'Back to top', 'digiqole'),
                'left-choice'	 => [
                    'value'	 => 'yes',
                    'label'	 => esc_html__( 'Yes', 'digiqole'),
                ],
                'right-choice'	 => [
                    'value'	 => 'no',
                    'label'	 => esc_html__( 'No', 'digiqole'),
                ],
            ],
        ],            
        ]
    ];