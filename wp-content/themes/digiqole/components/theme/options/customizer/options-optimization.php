<?php if (!defined('ABSPATH')) die('Direct access forbidden.');
/**
 * customizer option: optimization
 */

$options =[
    'optimization_settings' => [
        'title'		 => esc_html__( 'Optimization settings', 'digiqole' ),

        'options'	 => [
            'optimization_blocklibrary_enable' => [
                'type'			 => 'switch',
                'label'			 => esc_html__( 'Load Block Library css files?', 'digiqole' ),
                'desc'			 => esc_html__( 'Do you want to load block library css files?', 'digiqole' ),
                'value'          => 'yes',
                'left-choice'	 => [
                    'value'	 => 'yes',
                    'label'	 => esc_html__( 'Yes', 'digiqole' ),
                ],
                'right-choice'	 => [
                    'value'	 => 'no',
                    'label'	 => esc_html__( 'No', 'digiqole' ),
                ],
            ],
            'optimization_fontawesome_enable' => [
                'type'			 => 'switch',
                'label'			 => esc_html__( 'Load Fontawesome icons?', 'digiqole' ),
                'desc'			 => esc_html__( 'Do you want to load font awesome icons?', 'digiqole' ),
                'value'          => 'yes',
                'left-choice'	 => [
                    'value'	 => 'yes',
                    'label'	 => esc_html__( 'Yes', 'digiqole' ),
                ],
                'right-choice'	 => [
                    'value'	 => 'no',
                    'label'	 => esc_html__( 'No', 'digiqole' ),
                ],
            ],
            'optimization_elementoricons_enable' => [
                'type'			 => 'switch',
                'label'			 => esc_html__( 'Load Elementor Icons?', 'digiqole' ),
                'desc'			 => esc_html__( 'Do you want to load elementor icons?', 'digiqole' ),
                'value'          => 'yes',
                'left-choice'	 => [
                    'value'	 => 'yes',
                    'label'	 => esc_html__( 'Yes', 'digiqole' ),
                ],
                'right-choice'	 => [
                    'value'	 => 'no',
                    'label'	 => esc_html__( 'No', 'digiqole' ),
                ],
            ],
            'optimization_elementkitsicons_enable' => [
                'type'			 => 'switch',
                'label'			 => esc_html__( 'Load Elementskit Icons?', 'digiqole' ),
                'desc'			 => esc_html__( 'Do you want to load elementskit icons?', 'digiqole' ),
                'value'          => 'yes',
                'left-choice'	 => [
                    'value'	 => 'yes',
                    'label'	 => esc_html__( 'Yes', 'digiqole' ),
                ],
                'right-choice'	 => [
                    'value'	 => 'no',
                    'label'	 => esc_html__( 'No', 'digiqole' ),
                ],
            ],
            'optimization_socialicons_enable' => [
                'type'			 => 'switch',
                'label'			 => esc_html__( 'Load Accesspress Icons?', 'digiqole' ),
                'desc'			 => esc_html__( 'Do you want to load accesspress social icons?', 'digiqole' ),
                'value'          => 'yes',
                'left-choice'	 => [
                    'value'	 => 'yes',
                    'label'	 => esc_html__( 'Yes', 'digiqole' ),
                ],
                'right-choice'	 => [
                    'value'	 => 'no',
                    'label'	 => esc_html__( 'No', 'digiqole' ),
                ],
            ],
            'optimization_dashicons_enable' => [
                'type'			 => 'switch',
                'label'			 => esc_html__( 'Load Dash Icons?', 'digiqole' ),
                'desc'			 => esc_html__( 'Do you want to load dash icons?', 'digiqole' ),
                'value'          => 'yes',
                'left-choice'	 => [
                    'value'	 => 'yes',
                    'label'	 => esc_html__( 'Yes', 'digiqole' ),
                ],
                'right-choice'	 => [
                    'value'	 => 'no',
                    'label'	 => esc_html__( 'No', 'digiqole' ),
                ],
            ],
            'optimization_meta_viewport' => [
                'type'			 => 'switch',
                'label'			 => esc_html__( 'Load Meta Description?', 'digiqole' ),
                'desc'			 => esc_html__( 'Do you want to load meta description in header?', 'digiqole' ),
                'value'          => 'yes',
                'left-choice'	 => [
                    'value'	 => 'yes',
                    'label'	 => esc_html__( 'Yes', 'digiqole' ),
                ],
                'right-choice'	 => [
                    'value'	 => 'no',
                    'label'	 => esc_html__( 'No', 'digiqole' ),
                ],
            ],
        ],
    ]
];