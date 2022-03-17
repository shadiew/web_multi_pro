<?php

namespace Digiqole;

defined('ABSPATH') || exit;


class Converter {

	const OK_FA4_TO_5_FLAG = 'fa_5_updated__';

	private $theme_name     = '';
	private $force_update   = false;
	private $backup_key     = '_backup';


	/**
	 * Converter constructor.
	 *
	 * @param bool $force
	 */
	public function __construct($force = false) {

		$this->force_update = $force;
		$this->theme_name   = get_option('stylesheet', '');

		if($force === true) {

			$this->backup_key = '_force';
		}
	}


	/**
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function option_keys_by_builder() {

		return [
			'kirki' => [
				'nav_social_links',
				'sidebar_social_icon',
				'footer_social_links',
			],

			'unyson' => [
				'general_social_links'
			],
		];
	}


	/**
	 *
	 * @since 1.0.0
	 *
	 * @param $name
	 */
	public function init($name = '') {

		/**
		 * Font-awesome updated in elementor 2.6
		 */
		$elementor_version = get_option('elementor_version');

		if(version_compare($elementor_version, '2.6.0', '>=')) {

			if($this->force_update || get_option($this->get_update_flag_key(), 'no') !== 'yes') {

				/**
				 * Get the theme customizer settings
				 * Take backup
				 * Get the settings
				 * update it
				 * Save it
				 *
				 */

				/**
				 * Taking a backup, if anything goes wrong
				 *
				 */
				$mod_ok = 'theme_mods_' . $this->theme_name;
				$opt1   = get_option($mod_ok);
				update_option($this->get_back_up_option_key($mod_ok), $opt1);


				/**
				 * Looping through the settings key
				 * Getting the value
				 * Converting the values
				 * Saving the updated values
				 *
				 */
				foreach($this->option_keys_by_builder() as $builder => $keys) {

					if(empty($keys) || !is_array($keys)) {
						continue;
					}

					if($builder === 'unyson') {
						
						if(!function_exists('fw_get_db_customizer_option')) continue;

						foreach($keys as $key) {

							
							$existing_settings = fw_get_db_customizer_option($key);

							if(!empty($existing_settings) && is_array($existing_settings)) {

								$existing_settings = $this->convert_fa4_to_fa5__2D($existing_settings);

								fw_set_db_customizer_option($key, $existing_settings);
							}
						}

					} elseif($builder === 'kirki') {

						foreach($keys as $key) {
							
							$existing_settings = get_theme_mod($key);

							if(!empty($existing_settings) && is_array($existing_settings)) {

								$existing_settings = $this->convert_fa4_to_fa5__2D($existing_settings);

								set_theme_mod($key, $existing_settings);
							}
						}
					}
				}


				/**
				 * We have converted the icon data
				 * as well as saved a backup copy of original
				 * Now setting the flag conversion is done, so it does not run every time
				 *
				 */
				update_option($this->get_update_flag_key(), 'yes');
			}
		}
	}


	/**
	 * Convert FA icons for two-dimensional array structure
	 *
	 * @since 1.0.0
	 *
	 * @param $valArray
	 *
	 * @return mixed
	 */
	public function convert_fa4_to_fa5__2D($valArray) {

		foreach($valArray as $idx => $settings) {
			foreach($settings as $key => $val) {
				if(!empty($val)) {
					$valArray[$idx][$key] = $this->check_and_replace($val);
				}
			}
		}

		return $valArray;
	}


	/**
	 * Find the old class from given string
	 * If found replace with new class and return the string
	 * If not return the unmodified string
	 *
	 * @since 1.0.0
	 *
	 * @param $str
	 *
	 * @return mixed
	 */
	private function check_and_replace($str) {

		$class_map = $this->get_replaced_classes();

		foreach($class_map as $old => $new) {

			if(strpos($str, $old) !== false) {

				return str_replace($old, $new, $str);
			}
		}

		return $str;
	}


	/**
	 *
	 * @since 1.0.0
	 *
	 * @param $key
	 *
	 * @return string
	 */
	private function get_back_up_option_key($key) {

		if(empty(get_option($key . $this->backup_key))) {

			return $key . $this->backup_key;
		}

		return $key . $this->backup_key . '_' . time();
	}


	/**
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	public function get_update_flag_key() {

		return self::OK_FA4_TO_5_FLAG . $this->theme_name;
	}


	/**
	 *
	 * @since 1.0.0
	 *
	 * @return array
	 */
	public function get_replaced_classes() {

		/**
		 * This list need to be sorted in descending order
		 * otherwise, for example
		 * "fa fa-yc-square" became "fab fa-y-combinator-square" instead of "fab fa-hacker-news"
		 *
		 */
		return [
			'fa fa-youtube-square'       => 'fab fa-youtube-square',
			'fa fa-youtube-play'         => 'fab fa-youtube',
			'fa fa-youtube'              => 'fab fa-youtube',
			'fa fa-yoast'                => 'fab fa-yoast',
			'fa fa-yen'                  => 'fas fa-yen-sign',
			'fa fa-yelp'                 => 'fab fa-yelp',
			'fa fa-yc-square'            => 'fab fa-hacker-news',
			'fa fa-yc'                   => 'fab fa-y-combinator',
			'fa fa-yahoo'                => 'fab fa-yahoo',
			'fa fa-y-combinator-square'  => 'fab fa-hacker-news',
			'fa fa-y-combinator'         => 'fab fa-y-combinator',
			'fa fa-xing-square'          => 'fab fa-xing-square',
			'fa fa-xing'                 => 'fab fa-xing',
			'fa fa-wpforms'              => 'fab fa-wpforms',
			'fa fa-wpexplorer'           => 'fab fa-wpexplorer',
			'fa fa-wpbeginner'           => 'fab fa-wpbeginner',
			'fa fa-wordpress'            => 'fab fa-wordpress',
			'fa fa-won'                  => 'fas fa-won-sign',
			'fa fa-windows'              => 'fab fa-windows',
			'fa fa-window-restore'       => 'far fa-window-restore',
			'fa fa-window-maximize'      => 'far fa-window-maximize',
			'fa fa-window-close-o'       => 'far fa-window-close',
			'fa fa-wikipedia-w'          => 'fab fa-wikipedia-w',
			'fa fa-wheelchair-alt'       => 'fab fa-accessible-icon',
			'fa fa-whatsapp'             => 'fab fa-whatsapp',
			'fa fa-weixin'               => 'fab fa-weixin',
			'fa fa-weibo'                => 'fab fa-weibo',
			'fa fa-wechat'               => 'fab fa-weixin',
			'fa fa-warning'              => 'fas fa-exclamation-triangle',
			'fa fa-volume-control-phone' => 'fas fa-phone-volume',
			'fa fa-vk'                   => 'fab fa-vk',
			'fa fa-vine'                 => 'fab fa-vine',
			'fa fa-vimeo-square'         => 'fab fa-vimeo-square',
			'fa fa-vimeo'                => 'fab fa-vimeo-v',
			'fa fa-video-camera'         => 'fas fa-video',
			'fa fa-viadeo-square'        => 'fab fa-viadeo-square',
			'fa fa-viadeo'               => 'fab fa-viadeo',
			'fa fa-viacoin'              => 'fab fa-viacoin',
			'fa fa-vcard-o'              => 'far fa-address-card',
			'fa fa-vcard'                => 'fas fa-address-card',
			'fa fa-user-o'               => 'far fa-user',
			'fa fa-user-circle-o'        => 'far fa-user-circle',
			'fa fa-usd'                  => 'fas fa-dollar-sign',
			'fa fa-usb'                  => 'fab fa-usb',
			'fa fa-unsorted'             => 'fas fa-sort',
			'fa fa-twitter-square'       => 'fab fa-twitter-square',
			'fa fa-twitter'              => 'fab fa-twitter',
			'fa fa-twitch'               => 'fab fa-twitch',
			'fa fa-turkish-lira'         => 'fas fa-lira-sign',
			'fa fa-tumblr-square'        => 'fab fa-tumblr-square',
			'fa fa-tumblr'               => 'fab fa-tumblr',
			'fa fa-try'                  => 'fas fa-lira-sign',
			'fa fa-tripadvisor'          => 'fab fa-tripadvisor',
			'fa fa-trello'               => 'fab fa-trello',
			'fa fa-trash-o'              => 'far fa-trash-alt',
			'fa fa-trash'                => 'fas fa-trash-alt',
			'fa fa-toggle-up'            => 'far fa-caret-square-up',
			'fa fa-toggle-right'         => 'far fa-caret-square-right',
			'fa fa-toggle-left'          => 'far fa-caret-square-left',
			'fa fa-toggle-down'          => 'far fa-caret-square-down',
			'fa fa-times-rectangle-o'    => 'far fa-window-close',
			'fa fa-times-rectangle'      => 'fas fa-window-close',
			'fa fa-times-circle-o'       => 'far fa-times-circle',
			'fa fa-ticket'               => 'fas fa-ticket-alt',
			'fa fa-thumbs-o-up'          => 'far fa-thumbs-up',
			'fa fa-thumbs-o-down'        => 'far fa-thumbs-down',
			'fa fa-thumb-tack'           => 'fas fa-thumbtack',
			'fa fa-thermometer-4'        => 'fas fa-thermometer-full',
			'fa fa-thermometer-3'        => 'fas fa-thermometer-three-quarters',
			'fa fa-thermometer-2'        => 'fas fa-thermometer-half',
			'fa fa-thermometer-1'        => 'fas fa-thermometer-quarter',
			'fa fa-thermometer-0'        => 'fas fa-thermometer-empty',
			'fa fa-thermometer'          => 'fas fa-thermometer-full',
			'fa fa-themeisle'            => 'fab fa-themeisle',
			'fa fa-tencent-weibo'        => 'fab fa-tencent-weibo',
			'fa fa-television'           => 'fas fa-tv',
			'fa fa-telegram'             => 'fab fa-telegram',
			'fa fa-tachometer'           => 'fas fa-tachometer-alt',
			'fa fa-tablet'               => 'fas fa-tablet-alt',
			'fa fa-support'              => 'far fa-life-ring',
			'fa fa-superpowers'          => 'fab fa-superpowers',
			'fa fa-sun-o'                => 'far fa-sun',
			'fa fa-stumbleupon-circle'   => 'fab fa-stumbleupon-circle',
			'fa fa-stumbleupon'          => 'fab fa-stumbleupon',
			'fa fa-stop-circle-o'        => 'far fa-stop-circle',
			'fa fa-sticky-note-o'        => 'far fa-sticky-note',
			'fa fa-steam-square'         => 'fab fa-steam-square',
			'fa fa-steam'                => 'fab fa-steam',
			'fa fa-star-o'               => 'far fa-star',
			'fa fa-star-half-o'          => 'far fa-star-half',
			'fa fa-star-half-full'       => 'far fa-star-half',
			'fa fa-star-half-empty'      => 'far fa-star-half',
			'fa fa-stack-overflow'       => 'fab fa-stack-overflow',
			'fa fa-stack-exchange'       => 'fab fa-stack-exchange',
			'fa fa-square-o'             => 'far fa-square',
			'fa fa-spotify'              => 'fab fa-spotify',
			'fa fa-spoon'                => 'fas fa-utensil-spoon',
			'fa fa-soundcloud'           => 'fab fa-soundcloud',
			'fa fa-sort-numeric-desc'    => 'fas fa-sort-numeric-up',
			'fa fa-sort-numeric-asc'     => 'fas fa-sort-numeric-down',
			'fa fa-sort-desc'            => 'fas fa-sort-down',
			'fa fa-sort-asc'             => 'fas fa-sort-up',
			'fa fa-sort-amount-desc'     => 'fas fa-sort-amount-up',
			'fa fa-sort-amount-asc'      => 'fas fa-sort-amount-down',
			'fa fa-sort-alpha-desc'      => 'fas fa-sort-alpha-up',
			'fa fa-sort-alpha-asc'       => 'fas fa-sort-alpha-down',
			'fa fa-soccer-ball-o'        => 'far fa-futbol',
			'fa fa-snowflake-o'          => 'far fa-snowflake',
			'fa fa-snapchat-square'      => 'fab fa-snapchat-square',
			'fa fa-snapchat-ghost'       => 'fab fa-snapchat-ghost',
			'fa fa-snapchat'             => 'fab fa-snapchat',
			'fa fa-smile-o'              => 'far fa-smile',
			'fa fa-slideshare'           => 'fab fa-slideshare',
			'fa fa-sliders'              => 'fas fa-sliders-h',
			'fa fa-slack'                => 'fab fa-slack',
			'fa fa-skype'                => 'fab fa-skype',
			'fa fa-skyatlas'             => 'fab fa-skyatlas',
			'fa fa-simplybuilt'          => 'fab fa-simplybuilt',
			'fa fa-signing'              => 'fas fa-sign-language',
			'fa fa-sign-out'             => 'fas fa-sign-out-alt',
			'fa fa-sign-in'              => 'fas fa-sign-in-alt',
			'fa fa-shirtsinbulk'         => 'fab fa-shirtsinbulk',
			'fa fa-shield'               => 'fas fa-shield-alt',
			'fa fa-sheqel'               => 'fas fa-shekel-sign',
			'fa fa-shekel'               => 'fas fa-shekel-sign',
			'fa fa-share-square-o'       => 'far fa-share-square',
			'fa fa-send-o'               => 'far fa-paper-plane',
			'fa fa-send'                 => 'fas fa-paper-plane',
			'fa fa-sellsy'               => 'fab fa-sellsy',
			'fa fa-scribd'               => 'fab fa-scribd',
			'fa fa-scissors'             => 'fas fa-cut',
			'fa fa-safari'               => 'fab fa-safari',
			'fa fa-s15'                  => 'fas fa-bath',
			'fa fa-rupee'                => 'fas fa-rupee-sign',
			'fa fa-ruble'                => 'fas fa-ruble-sign',
			'fa fa-rub'                  => 'fas fa-ruble-sign',
			'fa fa-rouble'               => 'fas fa-ruble-sign',
			'fa fa-rotate-right'         => 'fas fa-redo',
			'fa fa-rotate-left'          => 'fas fa-undo',
			'fa fa-rmb'                  => 'fas fa-yen-sign',
			'fa fa-resistance'           => 'fab fa-rebel',
			'fa fa-repeat'               => 'fas fa-redo',
			'fa fa-reorder'              => 'fas fa-bars',
			'fa fa-renren'               => 'fab fa-renren',
			'fa fa-remove'               => 'fas fa-times',
			'fa fa-registered'           => 'far fa-registered',
			'fa fa-refresh'              => 'fas fa-sync',
			'fa fa-reddit-square'        => 'fab fa-reddit-square',
			'fa fa-reddit-alien'         => 'fab fa-reddit-alien',
			'fa fa-reddit'               => 'fab fa-reddit',
			'fa fa-rebel'                => 'fab fa-rebel',
			'fa fa-ravelry'              => 'fab fa-ravelry',
			'fa fa-ra'                   => 'fab fa-rebel',
			'fa fa-quora'                => 'fab fa-quora',
			'fa fa-question-circle-o'    => 'far fa-question-circle',
			'fa fa-qq'                   => 'fab fa-qq',
			'fa fa-product-hunt'         => 'fab fa-product-hunt',
			'fa fa-plus-square-o'        => 'far fa-plus-square',
			'fa fa-play-circle-o'        => 'far fa-play-circle',
			'fa fa-pinterest-square'     => 'fab fa-pinterest-square',
			'fa fa-pinterest-p'          => 'fab fa-pinterest-p',
			'fa fa-pinterest'            => 'fab fa-pinterest',
			'fa fa-pied-piper-pp'        => 'fab fa-pied-piper-pp',
			'fa fa-pied-piper-alt'       => 'fab fa-pied-piper-alt',
			'fa fa-pied-piper'           => 'fab fa-pied-piper',
			'fa fa-pie-chart'            => 'fas fa-chart-pie',
			'fa fa-picture-o'            => 'far fa-image',
			'fa fa-photo'                => 'far fa-image',
			'fa fa-pencil-square-o'      => 'far fa-edit',
			'fa fa-pencil-square'        => 'fas fa-pen-square',
			'fa fa-pencil'               => 'fas fa-pencil-alt',
			'fa fa-paypal'               => 'fab fa-paypal',
			'fa fa-pause-circle-o'       => 'far fa-pause-circle',
			'fa fa-paste'                => 'far fa-clipboard',
			'fa fa-paper-plane-o'        => 'far fa-paper-plane',
			'fa fa-pagelines'            => 'fab fa-pagelines',
			'fa fa-optin-monster'        => 'fab fa-optin-monster',
			'fa fa-opera'                => 'fab fa-opera',
			'fa fa-openid'               => 'fab fa-openid',
			'fa fa-opencart'             => 'fab fa-opencart',
			'fa fa-odnoklassniki-square' => 'fab fa-odnoklassniki-square',
			'fa fa-odnoklassniki'        => 'fab fa-odnoklassniki',
			'fa fa-object-ungroup'       => 'far fa-object-ungroup',
			'fa fa-object-group'         => 'far fa-object-group',
			'fa fa-newspaper-o'          => 'far fa-newspaper',
			'fa fa-navicon'              => 'fas fa-bars',
			'fa fa-mortar-board'         => 'fas fa-graduation-cap',
			'fa fa-moon-o'               => 'far fa-moon',
			'fa fa-money'                => 'far fa-money-bill-alt',
			'fa fa-modx'                 => 'fab fa-modx',
			'fa fa-mobile-phone'         => 'fas fa-mobile-alt',
			'fa fa-mobile'               => 'fas fa-mobile-alt',
			'fa fa-mixcloud'             => 'fab fa-mixcloud',
			'fa fa-minus-square-o'       => 'far fa-minus-square',
			'fa fa-meh-o'                => 'far fa-meh',
			'fa fa-meetup'               => 'fab fa-meetup',
			'fa fa-medium'               => 'fab fa-medium',
			'fa fa-meanpath'             => 'fab fa-font-awesome',
			'fa fa-maxcdn'               => 'fab fa-maxcdn',
			'fa fa-map-o'                => 'far fa-map',
			'fa fa-map-marker'           => 'fas fa-map-marker-alt',
			'fa fa-mail-reply-all'       => 'fas fa-reply-all',
			'fa fa-mail-reply'           => 'fas fa-reply',
			'fa fa-mail-forward'         => 'fas fa-share',
			'fa fa-long-arrow-up'        => 'fas fa-long-arrow-alt-up',
			'fa fa-long-arrow-right'     => 'fas fa-long-arrow-alt-right',
			'fa fa-long-arrow-left'      => 'fas fa-long-arrow-alt-left',
			'fa fa-long-arrow-down'      => 'fas fa-long-arrow-alt-down',
			'fa fa-list-alt'             => 'far fa-list-alt',
			'fa fa-linux'                => 'fab fa-linux',
			'fa fa-linode'               => 'fab fa-linode',
			'fa fa-linkedin-square'      => 'fab fa-linkedin',
			'fa fa-linkedin'             => 'fab fa-linkedin-in',
			'fa fa-line-chart'           => 'fas fa-chart-line',
			'fa fa-lightbulb-o'          => 'far fa-lightbulb',
			'fa fa-life-saver'           => 'far fa-life-ring',
			'fa fa-life-ring'            => 'far fa-life-ring',
			'fa fa-life-buoy'            => 'far fa-life-ring',
			'fa fa-life-bouy'            => 'far fa-life-ring',
			'fa fa-level-up'             => 'fas fa-level-up-alt',
			'fa fa-level-down'           => 'fas fa-level-down-alt',
			'fa fa-lemon-o'              => 'far fa-lemon',
			'fa fa-legal'                => 'fas fa-gavel',
			'fa fa-leanpub'              => 'fab fa-leanpub',
			'fa fa-lastfm-square'        => 'fab fa-lastfm-square',
			'fa fa-lastfm'               => 'fab fa-lastfm',
			'fa fa-krw'                  => 'fas fa-won-sign',
			'fa fa-keyboard-o'           => 'far fa-keyboard',
			'fa fa-jsfiddle'             => 'fab fa-jsfiddle',
			'fa fa-jpy'                  => 'fas fa-yen-sign',
			'fa fa-joomla'               => 'fab fa-joomla',
			'fa fa-ioxhost'              => 'fab fa-ioxhost',
			'fa fa-intersex'             => 'fas fa-transgender',
			'fa fa-internet-explorer'    => 'fab fa-internet-explorer',
			'fa fa-institution'          => 'fas fa-university',
			'fa fa-instagram'            => 'fab fa-instagram',
			'fa fa-inr'                  => 'fas fa-rupee-sign',
			'fa fa-imdb'                 => 'fab fa-imdb',
			'fa fa-image'                => 'far fa-image',
			'fa fa-ils'                  => 'fas fa-shekel-sign',
			'fa fa-id-card-o'            => 'far fa-id-card',
			'fa fa-id-badge'             => 'far fa-id-badge',
			'fa fa-html5'                => 'fab fa-html5',
			'fa fa-houzz'                => 'fab fa-houzz',
			'fa fa-hourglass-o'          => 'far fa-hourglass',
			'fa fa-hourglass-3'          => 'fas fa-hourglass-end',
			'fa fa-hourglass-2'          => 'fas fa-hourglass-half',
			'fa fa-hourglass-1'          => 'fas fa-hourglass-start',
			'fa fa-hotel'                => 'fas fa-bed',
			'fa fa-hospital-o'           => 'far fa-hospital',
			'fa fa-heart-o'              => 'far fa-heart',
			'fa fa-header'               => 'fas fa-heading',
			'fa fa-hdd-o'                => 'far fa-hdd',
			'fa fa-hard-of-hearing'      => 'fas fa-deaf',
			'fa fa-handshake-o'          => 'far fa-handshake',
			'fa fa-hand-stop-o'          => 'far fa-hand-paper',
			'fa fa-hand-spock-o'         => 'far fa-hand-spock',
			'fa fa-hand-scissors-o'      => 'far fa-hand-scissors',
			'fa fa-hand-rock-o'          => 'far fa-hand-rock',
			'fa fa-hand-pointer-o'       => 'far fa-hand-pointer',
			'fa fa-hand-peace-o'         => 'far fa-hand-peace',
			'fa fa-hand-paper-o'         => 'far fa-hand-paper',
			'fa fa-hand-o-up'            => 'far fa-hand-point-up',
			'fa fa-hand-o-right'         => 'far fa-hand-point-right',
			'fa fa-hand-o-left'          => 'far fa-hand-point-left',
			'fa fa-hand-o-down'          => 'far fa-hand-point-down',
			'fa fa-hand-lizard-o'        => 'far fa-hand-lizard',
			'fa fa-hand-grab-o'          => 'far fa-hand-rock',
			'fa fa-hacker-news'          => 'fab fa-hacker-news',
			'fa fa-group'                => 'fas fa-users',
			'fa fa-grav'                 => 'fab fa-grav',
			'fa fa-gratipay'             => 'fab fa-gratipay',
			'fa fa-google-wallet'        => 'fab fa-google-wallet',
			'fa fa-google-plus-square'   => 'fab fa-google-plus-square',
			'fa fa-google-plus-official' => 'fab fa-google-plus',
			'fa fa-google-plus-circle'   => 'fab fa-google-plus',
			'fa fa-google-plus'          => 'fab fa-google-plus-g',
			'fa fa-google'               => 'fab fa-google',
			'fa fa-glide-g'              => 'fab fa-glide-g',
			'fa fa-glide'                => 'fab fa-glide',
			'fa fa-glass'                => 'fas fa-glass-martini',
			'fa fa-gittip'               => 'fab fa-gratipay',
			'fa fa-gitlab'               => 'fab fa-gitlab',
			'fa fa-github-square'        => 'fab fa-github-square',
			'fa fa-github-alt'           => 'fab fa-github-alt',
			'fa fa-github'               => 'fab fa-github',
			'fa fa-git-square'           => 'fab fa-git-square',
			'fa fa-git'                  => 'fab fa-git',
			'fa fa-gg-circle'            => 'fab fa-gg-circle',
			'fa fa-gg'                   => 'fab fa-gg',
			'fa fa-get-pocket'           => 'fab fa-get-pocket',
			'fa fa-gears'                => 'fas fa-cogs',
			'fa fa-gear'                 => 'fas fa-cog',
			'fa fa-ge'                   => 'fab fa-empire',
			'fa fa-gbp'                  => 'fas fa-pound-sign',
			'fa fa-futbol-o'             => 'far fa-futbol',
			'fa fa-frown-o'              => 'far fa-frown',
			'fa fa-free-code-camp'       => 'fab fa-free-code-camp',
			'fa fa-foursquare'           => 'fab fa-foursquare',
			'fa fa-forumbee'             => 'fab fa-forumbee',
			'fa fa-fort-awesome'         => 'fab fa-fort-awesome',
			'fa fa-fonticons'            => 'fab fa-fonticons',
			'fa fa-font-awesome'         => 'fab fa-font-awesome',
			'fa fa-folder-open-o'        => 'far fa-folder-open',
			'fa fa-folder-o'             => 'far fa-folder',
			'fa fa-floppy-o'             => 'far fa-save',
			'fa fa-flickr'               => 'fab fa-flickr',
			'fa fa-flash'                => 'fas fa-bolt',
			'fa fa-flag-o'               => 'far fa-flag',
			'fa fa-first-order'          => 'fab fa-first-order',
			'fa fa-firefox'              => 'fab fa-firefox',
			'fa fa-files-o'              => 'far fa-copy',
			'fa fa-file-zip-o'           => 'far fa-file-archive',
			'fa fa-file-word-o'          => 'far fa-file-word',
			'fa fa-file-video-o'         => 'far fa-file-video',
			'fa fa-file-text-o'          => 'far fa-file-alt',
			'fa fa-file-text'            => 'fas fa-file-alt',
			'fa fa-file-sound-o'         => 'far fa-file-audio',
			'fa fa-file-powerpoint-o'    => 'far fa-file-powerpoint',
			'fa fa-file-picture-o'       => 'far fa-file-image',
			'fa fa-file-photo-o'         => 'far fa-file-image',
			'fa fa-file-pdf-o'           => 'far fa-file-pdf',
			'fa fa-file-o'               => 'far fa-file',
			'fa fa-file-movie-o'         => 'far fa-file-video',
			'fa fa-file-image-o'         => 'far fa-file-image',
			'fa fa-file-excel-o'         => 'far fa-file-excel',
			'fa fa-file-code-o'          => 'far fa-file-code',
			'fa fa-file-audio-o'         => 'far fa-file-audio',
			'fa fa-file-archive-o'       => 'far fa-file-archive',
			'fa fa-feed'                 => 'fas fa-rss',
			'fa fa-facebook-square'      => 'fab fa-facebook-square',
			'fa fa-facebook-official'    => 'fab fa-facebook',
			'fa fa-facebook-f'           => 'fab fa-facebook-f',
			'fa fa-facebook'             => 'fab fa-facebook-f',
			'fa fa-fa'                   => 'fab fa-font-awesome',
			'fa fa-eyedropper'           => 'fas fa-eye-dropper',
			'fa fa-eye-slash'            => 'far fa-eye-slash',
			'fa fa-eye'                  => 'far fa-eye',
			'fa fa-external-link-square' => 'fas fa-external-link-square-alt',
			'fa fa-external-link'        => 'fas fa-external-link-alt',
			'fa fa-expeditedssl'         => 'fab fa-expeditedssl',
			'fa fa-exchange'             => 'fas fa-exchange-alt',
			'fa fa-euro'                 => 'fas fa-euro-sign',
			'fa fa-eur'                  => 'fas fa-euro-sign',
			'fa fa-etsy'                 => 'fab fa-etsy',
			'fa fa-envira'               => 'fab fa-envira',
			'fa fa-envelope-open-o'      => 'far fa-envelope-open',
			'fa fa-envelope-o'           => 'far fa-envelope',
			'fa fa-empire'               => 'fab fa-empire',
			'fa fa-eercast'              => 'fab fa-sellcast',
			'fa fa-edge'                 => 'fab fa-edge',
			'fa fa-drupal'               => 'fab fa-drupal',
			'fa fa-dropbox'              => 'fab fa-dropbox',
			'fa fa-drivers-license-o'    => 'far fa-id-card',
			'fa fa-drivers-license'      => 'fas fa-id-card',
			'fa fa-dribbble'             => 'fab fa-dribbble',
			'fa fa-dot-circle-o'         => 'far fa-dot-circle',
			'fa fa-dollar'               => 'fas fa-dollar-sign',
			'fa fa-digg'                 => 'fab fa-digg',
			'fa fa-diamond'              => 'far fa-gem',
			'fa fa-deviantart'           => 'fab fa-deviantart',
			'fa fa-delicious'            => 'fab fa-delicious',
			'fa fa-dedent'               => 'fas fa-outdent',
			'fa fa-deafness'             => 'fas fa-deaf',
			'fa fa-dashcube'             => 'fab fa-dashcube',
			'fa fa-dashboard'            => 'fas fa-tachometer-alt',
			'fa fa-cutlery'              => 'fas fa-utensils',
			'fa fa-css3'                 => 'fab fa-css3',
			'fa fa-credit-card-alt'      => 'fas fa-credit-card',
			'fa fa-credit-card'          => 'far fa-credit-card',
			'fa fa-creative-commons'     => 'fab fa-creative-commons',
			'fa fa-copyright'            => 'far fa-copyright',
			'fa fa-contao'               => 'fab fa-contao',
			'fa fa-connectdevelop'       => 'fab fa-connectdevelop',
			'fa fa-compass'              => 'far fa-compass',
			'fa fa-comments-o'           => 'far fa-comments',
			'fa fa-commenting-o'         => 'far fa-comment-dots',
			'fa fa-commenting'           => 'far fa-comment-dots',
			'fa fa-comment-o'            => 'far fa-comment',
			'fa fa-codiepie'             => 'fab fa-codiepie',
			'fa fa-codepen'              => 'fab fa-codepen',
			'fa fa-code-fork'            => 'fas fa-code-branch',
			'fa fa-cny'                  => 'fas fa-yen-sign',
			'fa fa-cloud-upload'         => 'fas fa-cloud-upload-alt',
			'fa fa-cloud-download'       => 'fas fa-cloud-download-alt',
			'fa fa-close'                => 'fas fa-times',
			'fa fa-clone'                => 'far fa-clone',
			'fa fa-clock-o'              => 'far fa-clock',
			'fa fa-clipboard'            => 'far fa-clipboard',
			'fa fa-circle-thin'          => 'far fa-circle',
			'fa fa-circle-o-notch'       => 'fas fa-circle-notch',
			'fa fa-circle-o'             => 'far fa-circle',
			'fa fa-chrome'               => 'fab fa-chrome',
			'fa fa-check-square-o'       => 'far fa-check-square',
			'fa fa-check-circle-o'       => 'far fa-check-circle',
			'fa fa-chain-broken'         => 'fas fa-unlink',
			'fa fa-chain'                => 'fas fa-link',
			'fa fa-cc-visa'              => 'fab fa-cc-visa',
			'fa fa-cc-stripe'            => 'fab fa-cc-stripe',
			'fa fa-cc-paypal'            => 'fab fa-cc-paypal',
			'fa fa-cc-mastercard'        => 'fab fa-cc-mastercard',
			'fa fa-cc-jcb'               => 'fab fa-cc-jcb',
			'fa fa-cc-discover'          => 'fab fa-cc-discover',
			'fa fa-cc-diners-club'       => 'fab fa-cc-diners-club',
			'fa fa-cc-amex'              => 'fab fa-cc-amex',
			'fa fa-cc'                   => 'far fa-closed-captioning',
			'fa fa-caret-square-o-up'    => 'far fa-caret-square-up',
			'fa fa-caret-square-o-right' => 'far fa-caret-square-right',
			'fa fa-caret-square-o-left'  => 'far fa-caret-square-left',
			'fa fa-caret-square-o-down'  => 'far fa-caret-square-down',
			'fa fa-calendar-times-o'     => 'far fa-calendar-times',
			'fa fa-calendar-plus-o'      => 'far fa-calendar-plus',
			'fa fa-calendar-o'           => 'far fa-calendar',
			'fa fa-calendar-minus-o'     => 'far fa-calendar-minus',
			'fa fa-calendar-check-o'     => 'far fa-calendar-check',
			'fa fa-calendar'             => 'fas fa-calendar-alt',
			'fa fa-cab'                  => 'fas fa-taxi',
			'fa fa-buysellads'           => 'fab fa-buysellads',
			'fa fa-building-o'           => 'far fa-building',
			'fa fa-btc'                  => 'fab fa-btc',
			'fa fa-bookmark-o'           => 'far fa-bookmark',
			'fa fa-bluetooth-b'          => 'fab fa-bluetooth-b',
			'fa fa-bluetooth'            => 'fab fa-bluetooth',
			'fa fa-black-tie'            => 'fab fa-black-tie',
			'fa fa-bitcoin'              => 'fab fa-btc',
			'fa fa-bitbucket-square'     => 'fab fa-bitbucket',
			'fa fa-bitbucket'            => 'fab fa-bitbucket',
			'fa fa-bell-slash-o'         => 'far fa-bell-slash',
			'fa fa-bell-o'               => 'far fa-bell',
			'fa fa-behance-square'       => 'fab fa-behance-square',
			'fa fa-behance'              => 'fab fa-behance',
			'fa fa-battery-4'            => 'fas fa-battery-full',
			'fa fa-battery-3'            => 'fas fa-battery-three-quarters',
			'fa fa-battery-2'            => 'fas fa-battery-half',
			'fa fa-battery-1'            => 'fas fa-battery-quarter',
			'fa fa-battery-0'            => 'fas fa-battery-empty',
			'fa fa-battery'              => 'fas fa-battery-full',
			'fa fa-bathtub'              => 'fas fa-bath',
			'fa fa-bar-chart-o'          => 'far fa-chart-bar',
			'fa fa-bar-chart'            => 'far fa-chart-bar',
			'fa fa-bank'                 => 'fas fa-university',
			'fa fa-bandcamp'             => 'fab fa-bandcamp',
			'fa fa-automobile'           => 'fas fa-car',
			'fa fa-asl-interpreting'     => 'fas fa-american-sign-language-interpreting',
			'fa fa-arrows-v'             => 'fas fa-arrows-alt-v',
			'fa fa-arrows-h'             => 'fas fa-arrows-alt-h',
			'fa fa-arrows-alt'           => 'fas fa-expand-arrows-alt',
			'fa fa-arrows'               => 'fas fa-arrows-alt',
			'fa fa-arrow-circle-o-up'    => 'far fa-arrow-alt-circle-up',
			'fa fa-arrow-circle-o-right' => 'far fa-arrow-alt-circle-right',
			'fa fa-arrow-circle-o-left'  => 'far fa-arrow-alt-circle-left',
			'fa fa-arrow-circle-o-down'  => 'far fa-arrow-alt-circle-down',
			'fa fa-area-chart'           => 'fas fa-chart-area',
			'fa fa-apple'                => 'fab fa-apple',
			'fa fa-angellist'            => 'fab fa-angellist',
			'fa fa-android'              => 'fab fa-android',
			'fa fa-amazon'               => 'fab fa-amazon',
			'fa fa-adn'                  => 'fab fa-adn',
			'fa fa-address-card-o'       => 'far fa-address-card',
			'fa fa-address-book-o'       => 'far fa-address-book',
			'fa fa-500px'                => 'fab fa-500px',
		];
	}

}