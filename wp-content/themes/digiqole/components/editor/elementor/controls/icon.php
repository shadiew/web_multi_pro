<?php

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * A Font Icon select box.
 *
 * @property array $icons   A list of font-ts-icon classes. [ 'class-name' => 'nicename', ... ]
 *                          Default Font Awesome icons. @see Control_Icon::get_icons().
 * @property array $include list of classes to include form the $icons property
 * @property array $exclude list of classes to exclude form the $icons property
 *
 * @since 1.0.0
 */
class DIGIQOLE_Icon_Controler extends Elementor\Base_Data_Control {

	public function get_type() {
		return 'icon';
	}

	/**
	 * Get icons list
	 *
	 * @return array
	 */

	public static function get_icons() {

		$icons = array(
                  'ts-icon ts-icon-square-full' => 'ts-icon ts-icon-square-full',
                  'ts-icon ts-icon-star-half-alt' => 'ts-icon ts-icon-star-half-alt',
                  'ts-icon ts-icon-star' => 'ts-icon ts-icon-star',
                  'ts-icon ts-icon-star-solid' => 'ts-icon ts-icon-star-solid',
                  'ts-icon ts-icon-times' => 'ts-icon ts-icon-times',
                  'ts-icon ts-icon-envelope-open-solid' => 'ts-icon ts-icon-envelope-open-solid',
                  'ts-icon ts-icon-ellipsis-v' => 'ts-icon ts-icon-ellipsis-v',
                  'ts-icon ts-icon-youtube' => 'ts-icon ts-icon-youtube',
                  'ts-icon ts-icon-phone-volume' => 'ts-icon ts-icon-phone-volume',
                  'ts-icon ts-icon-paper-plane' => 'ts-icon ts-icon-paper-plane',
                  'ts-icon ts-icon-envelope-solid' => 'ts-icon ts-icon-envelope-solid',
                  'ts-icon ts-icon-vimeo-v' => 'ts-icon ts-icon-vimeo-v',
                  'ts-icon ts-icon-tumblr-brands' => 'ts-icon ts-icon-tumblr-brands',
                  'ts-icon ts-icon-odnoklassniki' => 'ts-icon ts-icon-odnoklassniki',
                  'ts-icon ts-icon-icon-yelp' => 'ts-icon ts-icon-icon-yelp',
                  'ts-icon ts-icon-xing' => 'ts-icon ts-icon-xing',
                  'ts-icon ts-icon-vk' => 'ts-icon ts-icon-vk',
                  'ts-icon ts-icon-vine' => 'ts-icon ts-icon-vine',
                  'ts-icon ts-icon-stumbleupon' => 'ts-icon ts-icon-stumbleupon',
                  'ts-icon ts-icon-soundcloud' => 'ts-icon ts-icon-soundcloud',
                  'ts-icon ts-icon-skype' => 'ts-icon ts-icon-skype',
                  'ts-icon ts-icon-github' => 'ts-icon ts-icon-github',
                  'ts-icon ts-icon-flickr' => 'ts-icon ts-icon-flickr',
                  'ts-icon ts-icon-behance' => 'ts-icon ts-icon-behance',
                  'ts-icon ts-icon-linkedin-in' => 'ts-icon ts-icon-linkedin-in',
                  'ts-icon ts-icon-instagram' => 'ts-icon ts-icon-instagram',
                  'ts-icon ts-icon-pinterest' => 'ts-icon ts-icon-pinterest',
                  'ts-icon ts-icon-google-plus' => 'ts-icon ts-icon-google-plus',
                  'ts-icon ts-icon-twitter-brands' => 'ts-icon ts-icon-twitter-brands',
                  'ts-icon ts-icon-facebook-f' => 'ts-icon ts-icon-facebook-f',
                  'ts-icon ts-icon-calendar-check' => 'ts-icon ts-icon-calendar-check',
                  'ts-icon ts-icon-long-arrow-alt-right' => 'ts-icon ts-icon-long-arrow-alt-right',
                  'ts-icon ts-icon-long-arrow-alt-left' => 'ts-icon ts-icon-long-arrow-alt-left',
                  'ts-icon ts-icon-thumbtack' => 'ts-icon ts-icon-thumbtack',
                  'ts-icon ts-icon-eye-solid' => 'ts-icon ts-icon-eye-solid',
                  'ts-icon ts-icon-share' => 'ts-icon ts-icon-share',
                  'ts-icon ts-icon-comments' => 'ts-icon ts-icon-comments',
                  'ts-icon ts-icon-home-solid' => 'ts-icon ts-icon-home-solid',
                  'ts-icon ts-icon-user-solid' => 'ts-icon ts-icon-user-solid',
                  'ts-icon ts-icon-play-solid' => 'ts-icon ts-icon-play-solid',
                  'ts-icon ts-icon-angle-left' => 'ts-icon ts-icon-angle-left',
                  'ts-icon ts-icon-clock-regular' => 'ts-icon ts-icon-clock-regular',
                  'ts-icon ts-icon-calendar-solid' => 'ts-icon ts-icon-calendar-solid',
                  'ts-icon ts-icon-spinner' => 'ts-icon ts-icon-spinner',
                  'ts-icon ts-icon-moon-solid' => 'ts-icon ts-icon-moon-solid',
                  'ts-icon ts-icon-sun-solid' => 'ts-icon ts-icon-sun-solid',
                  'ts-icon ts-icon-reply-all' => 'ts-icon ts-icon-reply-all',
                  'ts-icon ts-icon-angle-right' => 'ts-icon ts-icon-angle-right',
                  'ts-icon ts-icon-arrow-circle-left' => 'ts-icon ts-icon-arrow-circle-left',
                  'ts-icon ts-icon-bolt' => 'ts-icon ts-icon-bolt',
                  'ts-icon ts-icon-envelope' => 'ts-icon ts-icon-envelope',
                  'ts-icon ts-icon-pencil' => 'ts-icon ts-icon-pencil',
                  'ts-icon ts-icon-global' => 'ts-icon ts-icon-global',
                  'ts-icon ts-icon-happy' => 'ts-icon ts-icon-happy',
                  'ts-icon ts-icon-sad' => 'ts-icon ts-icon-sad',
                  'ts-icon ts-icon-facebook' => 'ts-icon ts-icon-facebook',
                  'ts-icon ts-icon-twitter' => 'ts-icon ts-icon-twitter',
                  'ts-icon ts-icon-googleplus' => 'ts-icon ts-icon-googleplus',
                  'ts-icon ts-icon-rss' => 'ts-icon ts-icon-rss',
                  'ts-icon ts-icon-tumblr' => 'ts-icon ts-icon-tumblr',
                  'ts-icon ts-icon-linkedin' => 'ts-icon ts-icon-linkedin',
                  'ts-icon ts-icon-dribbble' => 'ts-icon ts-icon-dribbble',
                  'ts-icon ts-icon-home' => 'ts-icon ts-icon-home',
                  'ts-icon ts-icon-pencil1' => 'ts-icon ts-icon-pencil1',
                  'ts-icon ts-icon-sun' => 'ts-icon ts-icon-sun',
                  'ts-icon ts-icon-moon' => 'ts-icon ts-icon-moon',
                  'ts-icon ts-icon-envelope1' => 'ts-icon ts-icon-envelope1',
                  'ts-icon ts-icon-user' => 'ts-icon ts-icon-user',
                  'ts-icon ts-icon-phone-handset' => 'ts-icon ts-icon-phone-handset',
                  'ts-icon ts-icon-bubble' => 'ts-icon ts-icon-bubble',
                  'ts-icon ts-icon-magnifier' => 'ts-icon ts-icon-magnifier',
                  'ts-icon ts-icon-cross' => 'ts-icon ts-icon-cross',
                  'ts-icon ts-icon-chevron-up' => 'ts-icon ts-icon-chevron-up',
                  'ts-icon ts-icon-chevron-down' => 'ts-icon ts-icon-chevron-down',
                  'ts-icon ts-icon-chevron-left' => 'ts-icon ts-icon-chevron-left',
                  'ts-icon ts-icon-chevron-right' => 'ts-icon ts-icon-chevron-right',
                  'ts-icon ts-icon-arrow-up' => 'ts-icon ts-icon-arrow-up',
                  'ts-icon ts-icon-arrow-down' => 'ts-icon ts-icon-arrow-down',
                  'ts-icon ts-icon-arrow-left' => 'ts-icon ts-icon-arrow-left',
                  'ts-icon ts-icon-arrow-right' => 'ts-icon ts-icon-arrow-right',
                  'ts-icon ts-icon-arrow-left1' => 'ts-icon ts-icon-arrow-left1',
                  'ts-icon ts-icon-arrow-right1' => 'ts-icon ts-icon-arrow-right1',
                  'ts-icon ts-icon-left-arrow' => 'ts-icon ts-icon-left-arrow',
                  'ts-icon ts-icon-download2' => 'ts-icon ts-icon-download2',
                  'ts-icon ts-icon-envelope2' => 'ts-icon ts-icon-envelope2',
                  'ts-icon ts-icon-folder' => 'ts-icon ts-icon-folder',
                  'ts-icon ts-icon-percent' => 'ts-icon ts-icon-percent',
                  'ts-icon ts-icon-phone2' => 'ts-icon ts-icon-phone2',
                  'ts-icon ts-icon-play' => 'ts-icon ts-icon-play',
                  'ts-icon ts-icon-left-arrow2' => 'ts-icon ts-icon-left-arrow2',
                  'ts-icon ts-icon-search1' => 'ts-icon ts-icon-search1',
                  'ts-icon ts-icon-quote1' => 'ts-icon ts-icon-quote1',
                  'ts-icon ts-icon-right-arrow2' => 'ts-icon ts-icon-right-arrow2',
                  'ts-icon ts-icon-fire' => 'ts-icon ts-icon-fire',
                  'ts-icon ts-icon-weather' => 'ts-icon ts-icon-weather',
                  'ts-icon ts-icon-post_icon5' => 'ts-icon ts-icon-post_icon5',
		);

		return $icons;
	}

	/**
	 * Retrieve icons control default settings.
	 *
	 * Get the default settings of the icons control. Used to return the default
	 * settings while initializing the icons control.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @return array Control default settings.
	 */

	protected function get_default_settings() {
		return [
			'options' => self::get_icons(),
		];
	}

	/**
	 * Render icons control output in the editor.
	 *
	 * Used to generate the control HTML in the editor using Underscore JS
	 * template. The variables for the class are available using `data` JS
	 * object.
	 *
	 * @since 1.0.0
	 * @access public
	 */

	public function content_template() {
		?>
		<div class="elementor-control-field">
			<label class="elementor-control-title">{{{ data.label }}}</label>
			<div class="elementor-control-input-wrapper">

				<select class="elementor-control-icon" data-setting="{{ data.name }}" data-placeholder="<?php esc_attr_e( 'Select Icon', 'digiqole' ); ?>">

					<option value=""><?php esc_html_e( 'Select Icon', 'digiqole' ); ?></option>
					<# _.each( data.options, function( option_title, option_value ) { #>
					<option value="{{ option_value }}">{{{ option_title }}}</option>
					<# } ); #>
				</select>
			</div>
		</div>
		<# if ( data.description ) { #>
		<div class="elementor-control-field-description">{{ data.description }}</div>
		<# } #>
		<?php
	}

}
