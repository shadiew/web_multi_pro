<?php
/** 
Plugin Name: Crawlomatic Multipage Scraper Post Generator
Plugin URI: //1.envato.market/coderevolution
Description: This plugin will generate content for you, even in your sleep using article crawling and scraping.
Author: CodeRevolution
Version: 2.4.1
Author URI: //coderevolution.ro
License: Commercial. For personal use only. Not to give away or resell.
Text Domain: crawlomatic-multipage-scraper-post-generator
*/
/*  
Copyright 2016 - 2022 CodeRevolution
*/

defined('ABSPATH') or die();
// require_once (dirname(__FILE__) . "/res/other/plugin-dash.php"); 
require_once( plugin_dir_path(__FILE__) . 'class.crawlomatic.shortcode.php' );
update_option('crawlomatic-multipage-scraper-post-generator_registration', 
[ 'item_id' => '20476010', 'item_name' => 'Crawlomatic Multisite Scraper Post Generator', 'created_at' => '10.10.2020', 'buyer' => 'gpllicense', 'licence' => 'Standart','supported_until' => '10.10.2030']);
update_option('coderevolution_settings_changed', 2);
function crawlomatic_get_version() {
    $plugin_data = get_file_data( __FILE__  , array('Version' => 'Version'), false);
    return $plugin_data['Version'];
}
function crawlomatic_load_textdomain() {
    load_plugin_textdomain( 'crawlomatic-multipage-scraper-post-generator', false, basename( dirname( __FILE__ ) ) . '/languages' ); 
}
add_action( 'init', 'crawlomatic_load_textdomain' );

function crawlomatic_assign_var(&$target, $var, $root = false) {
	static $cnt = 0;
    $key = key($var);
    if(is_array($var[$key])) 
        crawlomatic_assign_var($target[$key], $var[$key], false);
    else {
        if($key==0)
		{
			if($cnt == 0 && $root == true)
			{
				$target['_crawlomaticr_nonce'] = $var[$key];
				$cnt++;
			}
			elseif($cnt == 1 && $root == true)
			{
				$target['_wp_http_referer'] = $var[$key];
				$cnt++;
			}
			else
			{
				$target[] = $var[$key];
			}
		}
        else
		{
            $target[$key] = $var[$key];
		}
    }   
}

$plugin = plugin_basename(__FILE__);
if(is_admin())
{
    if($_SERVER["REQUEST_METHOD"]==="POST" && !empty($_POST["coderevolution_max_input_var_data"])) {
        $vars = explode("&", $_POST["coderevolution_max_input_var_data"]);
        $coderevolution_max_input_var_data = array();
        foreach($vars as $var) {
            parse_str($var, $variable);
            crawlomatic_assign_var($_POST, $variable, true);
        }
        unset($_POST["coderevolution_max_input_var_data"]);
    }
    $plugin_slug = explode('/', $plugin);
    $plugin_slug = $plugin_slug[0];
    if(isset($_POST[$plugin_slug . '_register']) && isset($_POST[$plugin_slug. '_register_code']) && trim($_POST[$plugin_slug . '_register_code']) != '')
    {
        update_option('coderevolution_settings_changed', 1);
        if(strlen(trim($_POST[$plugin_slug . '_register_code'])) != 36 || strstr($_POST[$plugin_slug . '_register_code'], '-') == false)
        {
            crawlomatic_log_to_file('Invalid registration code submitted: ' . $_POST[$plugin_slug . '_register_code']);
        }
        else
        {
            $ch = curl_init('https://wpinitiate.com/verify-purchase/purchase.php');
            if($ch !== false)
            {
                $data           = array();
                $data['code']   = trim($_POST[$plugin_slug . '_register_code']);
                $data['siteURL']   = get_bloginfo('url');
                $data['siteName']   = get_bloginfo('name');
                $data['siteEmail']   = get_bloginfo('admin_email');
                $fdata = "";
                foreach ($data as $key => $val) {
                    $fdata .= "$key=" . urlencode(trim($val)) . "&";
                }
                curl_setopt($ch, CURLOPT_POST, 1);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fdata);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                $result = curl_exec($ch);
                
                if($result === false)
                {
                    crawlomatic_log_to_file('Failed to get verification response: ' . curl_error($ch));
                }
                else
                {
                    $rj = json_decode($result, true);
                    if(isset($rj['error']))
                    {
                        update_option('coderevolution_settings_changed', $rj['error']);
                    }
                    elseif(isset($rj['item_name']))
                    {
                        $rj['code'] = $_POST[$plugin_slug . '_register_code'];
                        if($rj['item_id'] == '20476010' || $rj['item_id'] == '13371337' || $rj['item_id'] == '19200046')
                        {
                            update_option($plugin_slug . '_registration', $rj);
                            update_option('coderevolution_settings_changed', 2);
                        }
                        else
                        {
                            crawlomatic_log_to_file('Invalid response from purchase code verification (are you sure you inputed the right purchase code?): ' . print_r($rj, true));
                        }
                    }
                    else
                    {
                        crawlomatic_log_to_file('Invalid json from purchase code verification: ' . print_r($result, true));
                    }
                }
                curl_close($ch);
            }
            else
            {
                crawlomatic_log_to_file('Failed to init curl when trying to make purchase verification.');
            }
        }
    }
    if(isset($_POST[$plugin_slug . '_revoke_license']) && trim($_POST[$plugin_slug . '_revoke_license']) != '')
    {
        $ch = curl_init('https://wpinitiate.com/verify-purchase/revoke.php');
        if($ch !== false)
        {
            $data           = array();
            $data['siteURL']   = get_bloginfo('url');
            $fdata = "";
            foreach ($data as $key => $val) {
                $fdata .= "$key=" . urlencode(trim($val)) . "&";
            }
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fdata);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
            curl_setopt($ch, CURLOPT_TIMEOUT, 60);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
            $result = curl_exec($ch);
            
            if($result === false)
            {
                crawlomatic_log_to_file('Failed to revoke verification response: ' . curl_error($ch));
            }
            else
            {
                update_option($plugin_slug . '_registration', false);
            }
        }
        else
        {
            crawlomatic_log_to_file('Failed to init curl to revoke verification response.');
        }
    }
    $uoptions = get_option($plugin_slug . '_registration', array());
    if(isset($uoptions['item_id']) && isset($uoptions['item_name']) && isset($uoptions['created_at']) && isset($uoptions['buyer']) && isset($uoptions['licence']) && isset($uoptions['supported_until']))
    {
        require "update-checker/plugin-update-checker.php";
        $fwdu3dcarPUC = Puc_v4_Factory::buildUpdateChecker("https://wpinitiate.com/auto-update/?action=get_metadata&slug=crawlomatic-multipage-scraper-post-generator", __FILE__, "crawlomatic-multipage-scraper-post-generator");
    }
    else
    {
        add_action("after_plugin_row_{$plugin}", function( $plugin_file, $plugin_data, $status ) {
            $plugin_url = 'https://codecanyon.net/item/crawlomatic-multisite-scraper-post-generator-plugin-for-wordpress/20476010';
            echo '<tr class="active"><td>&nbsp;</td><td colspan="2"><p class="cr_auto_update">';
          echo sprintf( wp_kses( __( 'The plugin is not registered. Automatic updating is disabled. Please purchase a license for it from <a href="%s" target="_blank">here</a> and register  the plugin from the \'Main Settings\' menu using your purchase code. <a href="%s" target="_blank">How I find my purchase code?', 'crawlomatic-multipage-scraper-post-generator'), array(  'a' => array( 'href' => array(), 'target' => array() ) ) ), esc_url( 'https://1.envato.market/c/1264868/275988/4415?u=' . urlencode($plugin_url)), esc_url('//www.youtube.com/watch?v=NElJ5t_Wd48') );     
          echo '</a></p> </td></tr>';
        }, 10, 3 );
        add_action('admin_enqueue_scripts', 'crawlomatic_admin_enqueue_all');
        add_filter("plugin_action_links_$plugin", 'crawlomatic_add_activation_link');
    }
    add_action('admin_init', 'crawlomatic_register_mysettings');
    add_action('add_meta_boxes', 'crawlomatic_add_meta_box');
    add_filter("plugin_action_links_$plugin", 'crawlomatic_add_settings_link');
    add_filter("plugin_action_links_$plugin", 'crawlomatic_add_rating_link');
    add_action('admin_menu', 'crawlomatic_register_my_custom_menu_page');
    add_action('network_admin_menu', 'crawlomatic_register_my_custom_menu_page');
    add_filter("plugin_action_links_$plugin", 'crawlomatic_add_support_link');
    require(dirname(__FILE__) . "/res/crawlomatic-main.php");
    require(dirname(__FILE__) . "/res/crawlomatic-rules-list.php");
    require(dirname(__FILE__) . "/res/crawlomatic-logs.php");
    require(dirname(__FILE__) . "/res/crawlomatic-helper.php");
    require(dirname(__FILE__) . "/res/crawlomatic-offer.php");
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if ((isset($_GET['crawlomatic_dismiss']) || (isset($_GET['page']) && ($_GET['page'] == 'crawlomatic_admin_settings' || $_GET['page'] == 'crawlomatic_items_panel'))) && (!isset($crawlomatic_Main_Settings['headlessbrowserapi_key']) || trim($crawlomatic_Main_Settings['headlessbrowserapi_key']) == ''))
    {
        require_once( plugin_dir_path(__FILE__) . 'admin-notice.php' );
        $notices = Crawlomatic_Admin_Notices::get_instance();
        $notices->success( esc_html__('Latest Update for Crawlomatic!', 'crawlomatic-multipage-scraper-post-generator'), sprintf( wp_kses( __( '<b>Scrape JavaScript rendered content</b> from web pages using the new <b><a href="%s" target="_blank">HeadlessBrowserAPI</a></b>! Check more info, <a href="%s" target="_blank">here</a>.<br/>It will handle scraping pages for you using any of the following headless browsers: Puppeteer, Tor or PhantomJS, so you can get the JavaScript rendered HTML from any web page with a simple API call (no need to install anything on your server)!<br><br><b>Bonus tip:</b> If you select to use the Tor browser in the API to scrape content, Dark Web (.onion) links can also be scraped! Also, this will automatically use a random proxy to access sites, so IP based access limitations will not be an issue any more.', 'crawlomatic-multipage-scraper-post-generator'), array(  'a' => array( 'href' => array(), 'target' => array() ), 'b' => array( ), 'br' => array( ) ) ), esc_url( 'http://headlessbrowserapi.com/' ), esc_url( 'http://headlessbrowserapi.com/about/' ) ), 'headlessbrowserapi-notice' );
    }
}
function crawlomatic_admin_enqueue_all()
{
    $reg_css_code = '.cr_auto_update{background-color:#fff8e5;margin:5px 20px 15px 20px;border-left:4px solid #fff;padding:12px 12px 12px 12px !important;border-left-color:#ffb900;}';
    wp_register_style( 'crawlomatic-plugin-reg-style', false );
    wp_enqueue_style( 'crawlomatic-plugin-reg-style' );
    wp_add_inline_style( 'crawlomatic-plugin-reg-style', $reg_css_code );
}
function crawlomatic_add_activation_link($links)
{
    $settings_link = '<a href="admin.php?page=crawlomatic_admin_settings">' . esc_html__('Activate Plugin License', 'crawlomatic-multipage-scraper-post-generator') . '</a>';
    array_push($links, $settings_link);
    return $links;
}
use \Eventviva\ImageResize;
use vipnytt\SitemapParser;
use vipnytt\SitemapParser\Exceptions\SitemapParserException;
function crawlomatic_register_my_custom_menu_page()
{
    add_menu_page('Crawlomatic Multipage Scraper', 'Crawlomatic Multipage Scraper', 'manage_options', 'crawlomatic_admin_settings', 'crawlomatic_admin_settings', plugins_url('images/icon.png', __FILE__));
    $main = add_submenu_page('crawlomatic_admin_settings', esc_html__("Main Settings", 'crawlomatic-multipage-scraper-post-generator'), esc_html__("Main Settings", 'crawlomatic-multipage-scraper-post-generator'), 'manage_options', 'crawlomatic_admin_settings');
    add_action( 'load-' . $main, 'crawlomatic_load_all_admin_js' );
    add_action( 'load-' . $main, 'crawlomatic_load_main_admin_js' );
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (isset($crawlomatic_Main_Settings['crawlomatic_enabled']) && $crawlomatic_Main_Settings['crawlomatic_enabled'] == 'on') {
        $crawl = add_submenu_page('crawlomatic_admin_settings', esc_html__('Web Crawl to Posts', 'crawlomatic-multipage-scraper-post-generator'), esc_html__('Web Crawl to Posts', 'crawlomatic-multipage-scraper-post-generator'), 'manage_options', 'crawlomatic_items_panel', 'crawlomatic_items_panel');
        add_action( 'load-' . $crawl, 'crawlomatic_load_admin_js' );
        add_action( 'load-' . $crawl, 'crawlomatic_load_all_admin_js' );
        $help = add_submenu_page('crawlomatic_admin_settings', esc_html__('Crawling Helper', 'crawlomatic-multipage-scraper-post-generator'), esc_html__('Crawling Helper', 'crawlomatic-multipage-scraper-post-generator'), 'manage_options', 'crawlomatic_helper', 'crawlomatic_helper');
        add_action( 'load-' . $help, 'crawlomatic_load_all_admin_js' );
        add_action( 'load-' . $help, 'crawlomatic_load_helper_js' );
        $tips = add_submenu_page('crawlomatic_admin_settings', esc_html__('Tips & Tricks', 'crawlomatic-multipage-scraper-post-generator'), esc_html__('Tips & Tricks', 'crawlomatic-multipage-scraper-post-generator'), 'manage_options', 'crawlomatic_recommendations', 'crawlomatic_recommendations');
        add_action( 'load-' . $tips, 'crawlomatic_load_all_admin_js' );
        $log = add_submenu_page('crawlomatic_admin_settings', esc_html__("Activity & Logging", 'crawlomatic-multipage-scraper-post-generator'), esc_html__("Activity & Logging", 'crawlomatic-multipage-scraper-post-generator'), 'manage_options', 'crawlomatic_logs', 'crawlomatic_logs');
        add_action( 'load-' . $log, 'crawlomatic_load_all_admin_js' );
    }
}
function crawlomatic_load_admin_js(){
    add_action('admin_enqueue_scripts', 'crawlomatic_enqueue_admin_js');
}

function crawlomatic_enqueue_admin_js(){
    wp_enqueue_script('crawlomatic-footer-script', plugins_url('scripts/footer.js', __FILE__), array('jquery'), false, true);
    $cr_miv = ini_get('max_input_vars');
	if($cr_miv === null || $cr_miv === false || !is_numeric($cr_miv))
	{
        $cr_miv = '9999999';
    }
    $footer_conf_settings = array(
        'max_input_vars' => $cr_miv,
        'plugin_dir_url' => plugin_dir_url(__FILE__),
        'ajaxurl' => admin_url('admin-ajax.php')
    );
    wp_localize_script('crawlomatic-footer-script', 'mycustomsettings', $footer_conf_settings);
    wp_register_style('crawlomatic-rules-style', plugins_url('styles/crawlomatic-rules.css', __FILE__), false, '1.0.0');
    wp_enqueue_style('crawlomatic-rules-style');
}
function crawlomatic_load_helper_js(){
    add_action('admin_enqueue_scripts', 'crawlomatic_admin_load_helper');
}
function crawlomatic_admin_load_helper()
{
    wp_enqueue_script('crawlomatic-helper-script', plugins_url('scripts/helper.js', __FILE__), array('jquery'), false, true);
}
function crawlomatic_load_main_admin_js(){
    add_action('admin_enqueue_scripts', 'crawlomatic_enqueue_main_admin_js');
}

function crawlomatic_enqueue_main_admin_js(){
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    wp_enqueue_script('crawlomatic-main-script', plugins_url('scripts/main.js', __FILE__), array('jquery'));
    if(!isset($crawlomatic_Main_Settings['best_user']))
    {
        $best_user = '';
    }
    else
    {
        $best_user = $crawlomatic_Main_Settings['best_user'];
    }
    if(!isset($crawlomatic_Main_Settings['best_password']))
    {
        $best_password = '';
    }
    else
    {
        $best_password = $crawlomatic_Main_Settings['best_password'];
    }
    $header_main_settings = array(
        'best_user' => $best_user,
        'best_password' => $best_password
    );
    wp_localize_script('crawlomatic-main-script', 'mycustommainsettings', $header_main_settings);
}
function crawlomatic_load_all_admin_js(){
    add_action('admin_enqueue_scripts', 'crawlomatic_admin_load_files');
}
function crawlomatic_isSecure() {
  return
    (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
    || $_SERVER['SERVER_PORT'] == 443;
}
function crawlomatic_add_rating_link($links)
{
    $settings_link = '<a href="//codecanyon.net/downloads" target="_blank" title="Rate">
            <i class="wdi-rate-stars"><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="#ffb900" stroke="#ffb900" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="#ffb900" stroke="#ffb900" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="#ffb900" stroke="#ffb900" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="#ffb900" stroke="#ffb900" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg><svg xmlns="http://www.w3.org/2000/svg" width="15" height="15" viewBox="0 0 24 24" fill="#ffb900" stroke="#ffb900" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="feather feather-star"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg></i></a>';
    array_push($links, $settings_link);
    return $links;
}

function crawlomatic_add_support_link($links)
{
    $settings_link = '<a href="//coderevolution.ro/knowledge-base/" target="_blank">' . esc_html__('Support', 'crawlomatic-multipage-scraper-post-generator') . '</a>';
    array_push($links, $settings_link);
    return $links;
}

function crawlomatic_add_settings_link($links)
{
    $settings_link = '<a href="admin.php?page=crawlomatic_admin_settings">' . esc_html__('Settings', 'crawlomatic-multipage-scraper-post-generator') . '</a>';
    array_push($links, $settings_link);
    return $links;
}

function crawlomatic_add_meta_box()
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (isset($crawlomatic_Main_Settings['crawlomatic_enabled']) && $crawlomatic_Main_Settings['crawlomatic_enabled'] === 'on') {
        if (isset($crawlomatic_Main_Settings['enable_metabox']) && $crawlomatic_Main_Settings['enable_metabox'] == 'on') {
            foreach ( get_post_types( '', 'names' ) as $post_type ) {
               add_meta_box('crawlomatic_meta_box_function_add', esc_html__('Crawlomatic Auto Generated Post Information', 'crawlomatic-multipage-scraper-post-generator'), 'crawlomatic_meta_box_function', $post_type, 'advanced', 'default', array('__back_compat_meta_box' => true));
            }
            
        }
    }
}
function crawlomatic_get_blog_timezone() {

    $tzstring = get_option( 'timezone_string' );
    $offset   = get_option( 'gmt_offset' );

    if( empty( $tzstring ) && 0 != $offset && floor( $offset ) == $offset ){
        $offset_st = $offset > 0 ? "-$offset" : '+'.absint( $offset );
        $tzstring  = 'Etc/GMT'.$offset_st;
    }
    if( empty( $tzstring ) ){
        $tzstring = 'UTC';
    }
    $timezone = new DateTimeZone( $tzstring );
    return $timezone; 
}
function crawlomatic_builtin_spin_text($title, $content)
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    $titleSeparator         = '[19459000]';
    $text                   = $title . ' ' . $titleSeparator . ' ' . $content;
    $text                   = html_entity_decode($text);
    preg_match_all("/<[^<>]+>/is", $text, $matches, PREG_PATTERN_ORDER);
    $htmlfounds         = array_filter(array_unique($matches[0]));
    $htmlfounds[]       = '&quot;';
    $imgFoundsSeparated = array();
    foreach ($htmlfounds as $key => $currentFound) {
        if (stristr($currentFound, '<img') && stristr($currentFound, 'alt')) {
            $altSeparator   = '';
            $colonSeparator = '';
            if (stristr($currentFound, 'alt="')) {
                $altSeparator   = 'alt="';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt = "')) {
                $altSeparator   = 'alt = "';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt ="')) {
                $altSeparator   = 'alt ="';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt= "')) {
                $altSeparator   = 'alt= "';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt=\'')) {
                $altSeparator   = 'alt=\'';
                $colonSeparator = '\'';
            } elseif (stristr($currentFound, 'alt = \'')) {
                $altSeparator   = 'alt = \'';
                $colonSeparator = '\'';
            } elseif (stristr($currentFound, 'alt= \'')) {
                $altSeparator   = 'alt= \'';
                $colonSeparator = '\'';
            } elseif (stristr($currentFound, 'alt =\'')) {
                $altSeparator   = 'alt =\'';
                $colonSeparator = '\'';
            }
            if (trim($altSeparator) != '') {
                $currentFoundParts = explode($altSeparator, $currentFound);
                $preAlt            = $currentFoundParts[1];
                $preAltParts       = explode($colonSeparator, $preAlt);
                $altText           = $preAltParts[0];
                if (trim($altText) != '') {
                    unset($preAltParts[0]);
                    $imgFoundsSeparated[] = $currentFoundParts[0] . $altSeparator;
                    $imgFoundsSeparated[] = $colonSeparator . implode('', $preAltParts);
                    $htmlfounds[$key]     = '';
                }
            }
        }
    }
    if (count($imgFoundsSeparated) != 0) {
        $htmlfounds = array_merge($htmlfounds, $imgFoundsSeparated);
    }
    preg_match_all("/<\!--.*?-->/is", $text, $matches2, PREG_PATTERN_ORDER);
    $newhtmlfounds = $matches2[0];
    preg_match_all("/\[.*?\]/is", $text, $matches3, PREG_PATTERN_ORDER);
    $shortcodesfounds = $matches3[0];
    $htmlfounds       = array_merge($htmlfounds, $newhtmlfounds, $shortcodesfounds);
    $in               = 0;
    $cleanHtmlFounds  = array();
    foreach ($htmlfounds as $htmlfound) {
        if ($htmlfound == '[19459000]') {
        } elseif (trim($htmlfound) == '') {
        } else {
            $cleanHtmlFounds[] = $htmlfound;
        }
    }
    $htmlfounds = $cleanHtmlFounds;
    $start      = 19459001;
    foreach ($htmlfounds as $htmlfound) {
        $text = str_replace($htmlfound, '[' . $start . ']', $text);
        $start++;
    }
    $no_spin_words = array();
    if (isset($crawlomatic_Main_Settings['no_spin']) && $crawlomatic_Main_Settings['no_spin'] != '') {
        $no_spin_words = explode(',', $crawlomatic_Main_Settings['no_spin']);
        $no_spin_words = array_map('trim',$no_spin_words);
    }
    try {
        $file=file(dirname(__FILE__)  .'/res/synonyms.dat');
		foreach($file as $line){
			$synonyms=explode('|', $line);
			foreach($synonyms as $word){
				if(trim($word) != '' && !in_array($word, $no_spin_words)){
                    $word=str_replace('/','\/',$word);
					if(preg_match('/\b'. $word .'\b/u', $text)) {
						$rand = array_rand($synonyms, 1);
						$text = preg_replace('/\b'.$word.'\b/u', trim($synonyms[$rand]), $text);
					}
                    $uword=ucfirst($word);
					if(preg_match('/\b'. $uword .'\b/u', $text)) {
						$rand = array_rand($synonyms, 1);
						$text = preg_replace('/\b'.$uword.'\b/u', ucfirst(trim($synonyms[$rand])), $text);
					}
				}
			}
		}
        $translated = $text;
    }
    catch (Exception $e) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('Exception thrown in spinText ' . $e);
        }
        return false;
    }
    preg_match_all('{\[.*?\]}', $translated, $brackets);
    $brackets = $brackets[0];
    $brackets = array_unique($brackets);
    foreach ($brackets as $bracket) {
        if (stristr($bracket, '19')) {
            $corrrect_bracket = str_replace(' ', '', $bracket);
            $corrrect_bracket = str_replace('.', '', $corrrect_bracket);
            $corrrect_bracket = str_replace(',', '', $corrrect_bracket);
            $translated       = str_replace($bracket, $corrrect_bracket, $translated);
        }
    }
    if (stristr($translated, $titleSeparator)) {
        $start = 19459001;
        foreach ($htmlfounds as $htmlfound) {
            $translated = str_replace('[' . $start . ']', $htmlfound, $translated);
            $start++;
        }
        $contents = explode($titleSeparator, $translated);
        $title    = $contents[0];
        $content  = $contents[1];
    } else {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('Failed to parse spinned content, separator not found');
        }
        return false;
    }
    return array(
        $title,
        $content
    );
}

add_filter('cron_schedules', 'crawlomatic_add_cron_schedule');
function crawlomatic_add_cron_schedule($schedules)
{
    $schedules['crawlomatic_cron'] = array(
        'interval' => 3600,
        'display' => esc_html__('Crawlomatic Cron', 'crawlomatic-multipage-scraper-post-generator')
    );
    $schedules['minutely'] = array(
        'interval' => 60,
        'display' => esc_html__('Once A Minute', 'crawlomatic-multipage-scraper-post-generator')
    );
    $schedules['weekly']    = array(
        'interval' => 604800,
        'display' => esc_html__('Once Weekly', 'crawlomatic-multipage-scraper-post-generator')
    );
    $schedules['monthly']   = array(
        'interval' => 2592000,
        'display' => esc_html__('Once Monthly', 'crawlomatic-multipage-scraper-post-generator')
    );
    return $schedules;
}
function crawlomatic_auto_clear_log()
{
    global $wp_filesystem;
    if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
        include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
       wp_filesystem($creds);
    }
    if ($wp_filesystem->exists(WP_CONTENT_DIR . '/crawlomatic_info.log')) {
        $wp_filesystem->delete(WP_CONTENT_DIR . '/crawlomatic_info.log');
    }
}

add_shortcode( 'crawlomatic-display-posts', 'crawlomatic_display_posts_shortcode' );
function crawlomatic_display_posts_shortcode( $atts ) {
	$original_atts = $atts;
	$atts = shortcode_atts( array(
		'author'               => '',
		'category'             => '',
		'category_display'     => '',
		'category_label'       => 'Posted in: ',
		'content_class'        => 'content',
		'date_format'          => '(n/j/Y)',
		'date'                 => '',
		'date_column'          => 'post_date',
		'date_compare'         => '=',
		'date_query_before'    => '',
		'date_query_after'     => '',
		'date_query_column'    => '',
		'date_query_compare'   => '',
		'display_posts_off'    => false,
		'excerpt_length'       => false,
		'excerpt_more'         => false,
		'excerpt_more_link'    => false,
		'exclude_current'      => false,
		'id'                   => false,
		'ignore_sticky_posts'  => false,
		'image_size'           => false,
		'include_author'       => false,
		'include_content'      => false,
		'include_date'         => false,
		'include_excerpt'      => false,
		'include_link'         => true,
		'include_title'        => true,
		'meta_key'             => '',
		'meta_value'           => '',
		'no_posts_message'     => '',
		'offset'               => 0,
		'order'                => 'DESC',
		'orderby'              => 'date',
		'post_parent'          => false,
		'post_status'          => 'publish',
		'post_type'            => 'post',
		'posts_per_page'       => '10',
		'tag'                  => '',
		'tax_operator'         => 'IN',
		'tax_include_children' => true,
		'tax_term'             => false,
		'taxonomy'             => false,
		'time'                 => '',
		'title'                => '',
        'title_color'          => '#000000',
        'excerpt_color'        => '#000000',
        'link_to_source'       => '',
        'title_font_size'      => '100%',
        'excerpt_font_size'    => '100%',
        'read_more_text'       => '',
		'wrapper'              => 'ul',
		'wrapper_class'        => 'display-posts-listing',
		'wrapper_id'           => false,
        'ruleid'               => ''
	), $atts, 'display-posts' );
	if( $atts['display_posts_off'] )
		return;
	$author               = sanitize_text_field( $atts['author'] );
    $ruleid               = sanitize_text_field( $atts['ruleid'] );
	$category             = sanitize_text_field( $atts['category'] );
	$category_display     = 'true' == $atts['category_display'] ? 'category' : sanitize_text_field( $atts['category_display'] );
	$category_label       = sanitize_text_field( $atts['category_label'] );
	$content_class        = array_map( 'sanitize_html_class', ( explode( ' ', $atts['content_class'] ) ) );
	$date_format          = sanitize_text_field( $atts['date_format'] );
	$date                 = sanitize_text_field( $atts['date'] );
	$date_column          = sanitize_text_field( $atts['date_column'] );
	$date_compare         = sanitize_text_field( $atts['date_compare'] );
	$date_query_before    = sanitize_text_field( $atts['date_query_before'] );
	$date_query_after     = sanitize_text_field( $atts['date_query_after'] );
	$date_query_column    = sanitize_text_field( $atts['date_query_column'] );
	$date_query_compare   = sanitize_text_field( $atts['date_query_compare'] );
	$excerpt_length       = intval( $atts['excerpt_length'] );
	$excerpt_more         = sanitize_text_field( $atts['excerpt_more'] );
	$excerpt_more_link    = filter_var( $atts['excerpt_more_link'], FILTER_VALIDATE_BOOLEAN );
	$exclude_current      = filter_var( $atts['exclude_current'], FILTER_VALIDATE_BOOLEAN );
	$id                   = $atts['id'];
	$ignore_sticky_posts  = filter_var( $atts['ignore_sticky_posts'], FILTER_VALIDATE_BOOLEAN );
	$image_size           = sanitize_key( $atts['image_size'] );
	$include_title        = filter_var( $atts['include_title'], FILTER_VALIDATE_BOOLEAN );
	$include_author       = filter_var( $atts['include_author'], FILTER_VALIDATE_BOOLEAN );
	$include_content      = filter_var( $atts['include_content'], FILTER_VALIDATE_BOOLEAN );
	$include_date         = filter_var( $atts['include_date'], FILTER_VALIDATE_BOOLEAN );
	$include_excerpt      = filter_var( $atts['include_excerpt'], FILTER_VALIDATE_BOOLEAN );
	$include_link         = filter_var( $atts['include_link'], FILTER_VALIDATE_BOOLEAN );
	$meta_key             = sanitize_text_field( $atts['meta_key'] );
	$meta_value           = sanitize_text_field( $atts['meta_value'] );
	$no_posts_message     = sanitize_text_field( $atts['no_posts_message'] );
	$offset               = intval( $atts['offset'] );
	$order                = sanitize_key( $atts['order'] );
	$orderby              = sanitize_key( $atts['orderby'] );
	$post_parent          = $atts['post_parent'];
	$post_status          = $atts['post_status'];
	$post_type            = sanitize_text_field( $atts['post_type'] );
	$posts_per_page       = intval( $atts['posts_per_page'] );
	$tag                  = sanitize_text_field( $atts['tag'] );
	$tax_operator         = $atts['tax_operator'];
	$tax_include_children = filter_var( $atts['tax_include_children'], FILTER_VALIDATE_BOOLEAN );
	$tax_term             = sanitize_text_field( $atts['tax_term'] );
	$taxonomy             = sanitize_key( $atts['taxonomy'] );
	$time                 = sanitize_text_field( $atts['time'] );
	$shortcode_title      = sanitize_text_field( $atts['title'] );
    $title_color          = sanitize_text_field( $atts['title_color'] );
    $excerpt_color        = sanitize_text_field( $atts['excerpt_color'] );
    $link_to_source       = sanitize_text_field( $atts['link_to_source'] );
    $excerpt_font_size    = sanitize_text_field( $atts['excerpt_font_size'] );
    $title_font_size      = sanitize_text_field( $atts['title_font_size'] );
    $read_more_text       = sanitize_text_field( $atts['read_more_text'] );
	$wrapper              = sanitize_text_field( $atts['wrapper'] );
	$wrapper_class        = array_map( 'sanitize_html_class', ( explode( ' ', $atts['wrapper_class'] ) ) );
	if( !empty( $wrapper_class ) )
		$wrapper_class = ' class="' . implode( ' ', $wrapper_class ) . '"';
	$wrapper_id = sanitize_html_class( $atts['wrapper_id'] );
	if( !empty( $wrapper_id ) )
		$wrapper_id = ' id="' . esc_html($wrapper_id) . '"';
	$args = array(
		'category_name'       => $category,
		'order'               => $order,
		'orderby'             => $orderby,
		'post_type'           => explode( ',', $post_type ),
		'posts_per_page'      => $posts_per_page,
		'tag'                 => $tag,
	);
	if ( ! empty( $date ) || ! empty( $time ) || ! empty( $date_query_after ) || ! empty( $date_query_before ) ) {
		$initial_date_query = $date_query_top_lvl = array();
		$valid_date_columns = array(
			'post_date', 'post_date_gmt', 'post_modified', 'post_modified_gmt',
			'comment_date', 'comment_date_gmt'
		);
		$valid_compare_ops = array( '=', '!=', '>', '>=', '<', '<=', 'IN', 'NOT IN', 'BETWEEN', 'NOT BETWEEN' );
		$dates = crawlomatic_sanitize_date_time( $date );
		if ( ! empty( $dates ) ) {
			if ( is_string( $dates ) ) {
				$timestamp = strtotime( $dates );
				$dates = array(
					'year'   => date( 'Y', $timestamp ),
					'month'  => date( 'm', $timestamp ),
					'day'    => date( 'd', $timestamp ),
				);
			}
			foreach ( $dates as $arg => $segment ) {
				$initial_date_query[ $arg ] = $segment;
			}
		}
		$times = crawlomatic_sanitize_date_time( $time, 'time' );
		if ( ! empty( $times ) ) {
			foreach ( $times as $arg => $segment ) {
				$initial_date_query[ $arg ] = $segment;
			}
		}
		$before = crawlomatic_sanitize_date_time( $date_query_before, 'date', true );
		if ( ! empty( $before ) ) {
			$initial_date_query['before'] = $before;
		}
		$after = crawlomatic_sanitize_date_time( $date_query_after, 'date', true );
		if ( ! empty( $after ) ) {
			$initial_date_query['after'] = $after;
		}
		if ( ! empty( $date_query_column ) && in_array( $date_query_column, $valid_date_columns ) ) {
			$initial_date_query['column'] = $date_query_column;
		}
		if ( ! empty( $date_query_compare ) && in_array( $date_query_compare, $valid_compare_ops ) ) {
			$initial_date_query['compare'] = $date_query_compare;
		}
		if ( ! empty( $date_column ) && in_array( $date_column, $valid_date_columns ) ) {
			$date_query_top_lvl['column'] = $date_column;
		}
		if ( ! empty( $date_compare ) && in_array( $date_compare, $valid_compare_ops ) ) {
			$date_query_top_lvl['compare'] = $date_compare;
		}
		if ( ! empty( $initial_date_query ) ) {
			$date_query_top_lvl[] = $initial_date_query;
		}
		$args['date_query'] = $date_query_top_lvl;
	}
    $args['meta_key'] = 'crawlomatic_parent_rule';
    if($ruleid != '')
    {
        $args['meta_value'] = $ruleid;
    }
	if( $ignore_sticky_posts )
		$args['ignore_sticky_posts'] = true;
	 
	if( $id ) {
		$posts_in = array_map( 'intval', explode( ',', $id ) );
		$args['post__in'] = $posts_in;
	}
	if( is_singular() && $exclude_current )
		$args['post__not_in'] = array( get_the_ID() );
	if( !empty( $author ) ) {
		if( 'current' == $author && is_user_logged_in() )
			$args['author_name'] = wp_get_current_user()->user_login;
		elseif( 'current' == $author )
            $unrelevar = false;
			 
		else
			$args['author_name'] = $author;
	}
	if( !empty( $offset ) )
		$args['offset'] = $offset;
	$post_status = explode( ', ', $post_status );
	$validated = array();
	$available = array( 'publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit', 'trash', 'any' );
	foreach ( $post_status as $unvalidated )
		if ( in_array( $unvalidated, $available ) )
			$validated[] = $unvalidated;
	if( !empty( $validated ) )
		$args['post_status'] = $validated;
	if ( !empty( $taxonomy ) && !empty( $tax_term ) ) {
		if( 'current' == $tax_term ) {
			global $post;
			$terms = wp_get_post_terms(get_the_ID(), $taxonomy);
			$tax_term = array();
			foreach ($terms as $term) {
				$tax_term[] = $term->slug;
			}
		}else{
			$tax_term = explode( ', ', $tax_term );
		}
		if( !in_array( $tax_operator, array( 'IN', 'NOT IN', 'AND' ) ) )
			$tax_operator = 'IN';
		$tax_args = array(
			'tax_query' => array(
				array(
					'taxonomy'         => $taxonomy,
					'field'            => 'slug',
					'terms'            => $tax_term,
					'operator'         => $tax_operator,
					'include_children' => $tax_include_children,
				)
			)
		);
		$count = 2;
		$more_tax_queries = false;
		while(
			isset( $original_atts['taxonomy_' . $count] ) && !empty( $original_atts['taxonomy_' . $count] ) &&
			isset( $original_atts['tax_' . esc_html($count) . '_term'] ) && !empty( $original_atts['tax_' . esc_html($count) . '_term'] )
		):
			$more_tax_queries = true;
			$taxonomy = sanitize_key( $original_atts['taxonomy_' . $count] );
	 		$terms = explode( ', ', sanitize_text_field( $original_atts['tax_' . esc_html($count) . '_term'] ) );
	 		$tax_operator = isset( $original_atts['tax_' . esc_html($count) . '_operator'] ) ? $original_atts['tax_' . esc_html($count) . '_operator'] : 'IN';
	 		$tax_operator = in_array( $tax_operator, array( 'IN', 'NOT IN', 'AND' ) ) ? $tax_operator : 'IN';
	 		$tax_include_children = isset( $original_atts['tax_' . esc_html($count) . '_include_children'] ) ? filter_var( $atts['tax_' . esc_html($count) . '_include_children'], FILTER_VALIDATE_BOOLEAN ) : true;
	 		$tax_args['tax_query'][] = array(
	 			'taxonomy'         => $taxonomy,
	 			'field'            => 'slug',
	 			'terms'            => $terms,
	 			'operator'         => $tax_operator,
	 			'include_children' => $tax_include_children,
	 		);
			$count++;
		endwhile;
		if( $more_tax_queries ):
			$tax_relation = 'AND';
			if( isset( $original_atts['tax_relation'] ) && in_array( $original_atts['tax_relation'], array( 'AND', 'OR' ) ) )
				$tax_relation = $original_atts['tax_relation'];
			$args['tax_query']['relation'] = $tax_relation;
		endif;
		$args = array_merge_recursive( $args, $tax_args );
	}
	if( $post_parent !== false ) {
		if( 'current' == $post_parent ) {
			global $post;
			$post_parent = get_the_ID();
		}
		$args['post_parent'] = intval( $post_parent );
	}
	$wrapper_options = array( 'ul', 'ol', 'div' );
	if( ! in_array( $wrapper, $wrapper_options ) )
		$wrapper = 'ul';
	$inner_wrapper = 'div' == $wrapper ? 'div' : 'li';
	$listing = new WP_Query( apply_filters( 'display_posts_shortcode_args', $args, $original_atts ) );
	if ( ! $listing->have_posts() ) {
		return apply_filters( 'display_posts_shortcode_no_results', wpautop( $no_posts_message ) );
	}
	$inner = '';
    wp_suspend_cache_addition(true);
	while ( $listing->have_posts() ): $listing->the_post(); global $post;
		$image = $date = $author = $excerpt = $content = '';
		if ( $include_title && $include_link ) {
            if($link_to_source == 'yes')
            {
                $source_url = get_post_meta($post->ID, 'crawlomatic_post_url', true);
                if($source_url != '')
                {
                    $title = '<a class="crawlomatic_display_title" href="' . esc_url($source_url) . '"><span class="cr_display_span" >' . get_the_title() . '</span></a>';
                }
                else
                {
                    $title = '<a class="crawlomatic_display_title" href="' . apply_filters( 'the_permalink', get_permalink() ) . '"><span class="cr_display_span" >' . get_the_title() . '</span></a>';
                }
            }
            else
            {
                $title = '<a class="crawlomatic_display_title" href="' . apply_filters( 'the_permalink', get_permalink() ) . '"><span class="cr_display_span" >' . get_the_title() . '</span></a>';
            }
		} elseif( $include_title ) {
			$title = '<span class="crawlomatic_display_title" class="cr_display_span">' . get_the_title() . '</span>';
		} else {
			$title = '';
		}
		if ( $image_size && has_post_thumbnail() && $include_link ) {
            if($link_to_source == 'yes')
            {
                $source_url = get_post_meta($post->ID, 'crawlomatic_post_url', true);
                if($source_url != '')
                {
                    $image = '<a class="crawlomatic_display_image" href="' . esc_url($source_url) . '">' . get_the_post_thumbnail( get_the_ID(), $image_size ) . '</a> <br/>';
                }
                else
                {
                    $image = '<a class="crawlomatic_display_image" href="' . get_permalink() . '">' . get_the_post_thumbnail( get_the_ID(), $image_size ) . '</a> <br/>';
                }
            }
            else
            {
                $image = '<a class="crawlomatic_display_image" href="' . get_permalink() . '">' . get_the_post_thumbnail( get_the_ID(), $image_size ) . '</a> <br/>';
            }
		} elseif( $image_size && has_post_thumbnail() ) {
			$image = '<span class="crawlomatic_display_image">' . get_the_post_thumbnail( get_the_ID(), $image_size ) . '</span> <br/>';
		}
		if ( $include_date )
			$date = ' <span class="date">' . get_the_date( $date_format ) . '</span>';
		if( $include_author )
			$author = apply_filters( 'display_posts_shortcode_author', ' <span class="crawlomatic_display_author">by ' . get_the_author() . '</span>', $original_atts );
		if ( $include_excerpt ) {
			if( $excerpt_length || $excerpt_more || $excerpt_more_link ) {
				$length = $excerpt_length ? $excerpt_length : apply_filters( 'excerpt_length', 55 );
				$more   = $excerpt_more ? $excerpt_more : apply_filters( 'excerpt_more', '' );
				$more   = $excerpt_more_link ? ' <a href="' . get_permalink() . '">' . esc_html($more) . '</a>' : ' ' . esc_html($more);
				if( has_excerpt() && apply_filters( 'display_posts_shortcode_full_manual_excerpt', false ) ) {
					$excerpt = $post->post_excerpt . $more;
				} elseif( has_excerpt() ) {
					$excerpt = crawlomatic_wp_trim_words( strip_shortcodes( $post->post_excerpt ), $length, $more );
				} else {
					$excerpt = crawlomatic_wp_trim_words( strip_shortcodes( $post->post_content ), $length, $more );
				}
			} else {
				$excerpt = get_the_excerpt();
			}
			$excerpt = ' <br/><br/> <span class="crawlomatic_display_excerpt" class="cr_display_excerpt_adv">' . $excerpt . '</span>';
            if($read_more_text != '')
            {
                if($link_to_source == 'yes')
                {
                    $source_url = get_post_meta($post->ID, 'crawlomatic_post_url', true);
                    if($source_url != '')
                    {
                        $excerpt .= '<br/><a href="' . esc_url($source_url) . '"><span class="crawlomatic_display_excerpt" class="cr_display_excerpt_adv">' . esc_html($read_more_text) . '</span></a>';
                    }
                    else
                    {
                        $excerpt .= '<br/><a href="' . get_permalink() . '"><span class="crawlomatic_display_excerpt" class="cr_display_excerpt_adv">' . esc_html($read_more_text) . '</span></a>';
                    }
                }
                else
                {
                    $excerpt .= '<br/><a href="' . get_permalink() . '"><span class="crawlomatic_display_excerpt" class="cr_display_excerpt_adv">' . esc_html($read_more_text) . '</span></a>';
                }
            }
		}
		if( $include_content ) {
			add_filter( 'shortcode_atts_display-posts', 'crawlomatic_display_posts_off', 10, 3 );
			$content = '<div class="' . implode( ' ', $content_class ) . '">' . apply_filters( 'the_content', get_the_content() ) . '</div>';
			remove_filter( 'shortcode_atts_display-posts', 'crawlomatic_display_posts_off', 10, 3 );
		}
		$category_display_text = '';
		if( $category_display && is_object_in_taxonomy( get_post_type(), $category_display ) ) {
			$terms = get_the_terms( get_the_ID(), $category_display );
			$term_output = array();
			foreach( $terms as $term )
				$term_output[] = '<a href="' . get_term_link( $term, $category_display ) . '">' . esc_url($term->name) . '</a>';
			$category_display_text = ' <span class="category-display"><span class="category-display-label">' . esc_html($category_label) . '</span> ' . implode( ', ', $term_output ) . '</span>';
			$category_display_text = apply_filters( 'display_posts_shortcode_category_display', $category_display_text );
		}
		$class = array( 'listing-item' );
		$class = array_map( 'sanitize_html_class', apply_filters( 'display_posts_shortcode_post_class', $class, $post, $listing, $original_atts ) );
		$output = '<br/><' . esc_html($inner_wrapper) . ' class="' . implode( ' ', $class ) . '">' . $image . $title . $date . $author . $category_display_text . $excerpt . $content . '</' . esc_html($inner_wrapper) . '><br/><br/><hr class="cr_hr_dot"/>';		$inner .= apply_filters( 'display_posts_shortcode_output', $output, $original_atts, $image, $title, $date, $excerpt, $inner_wrapper, $content, $class );
	endwhile; wp_reset_postdata();
    wp_suspend_cache_addition(false);
	$open = apply_filters( 'display_posts_shortcode_wrapper_open', '<' . $wrapper . $wrapper_class . $wrapper_id . '>', $original_atts );
	$close = apply_filters( 'display_posts_shortcode_wrapper_close', '</' . esc_html($wrapper) . '>', $original_atts );
	$return = $open;
	if( $shortcode_title ) {
		$title_tag = apply_filters( 'display_posts_shortcode_title_tag', 'h2', $original_atts );
		$return .= '<' . esc_html($title_tag) . ' class="display-posts-title">' . esc_html($shortcode_title) . '</' . esc_html($title_tag) . '>' . "\n";
	}
	$return .= $inner . $close;
    $reg_css_code = '.cr_hr_dot{border-top: dotted 1px;}.cr_display_span{font-size:' . esc_html($title_font_size) . ';color:' . esc_html($title_color) . ' !important;}.cr_display_excerpt_adv{font-size:' . esc_html($excerpt_font_size) . ';color:' . esc_html($excerpt_color) . ' !important;}';
    wp_register_style( 'crawlomatic-display-style', false );
    wp_enqueue_style( 'crawlomatic-display-style' );
    wp_add_inline_style( 'crawlomatic-display-style', $reg_css_code );
	return $return;
}
function crawlomatic_sanitize_date_time( $date_time, $type = 'date', $accepts_string = false ) {
	if ( empty( $date_time ) || ! in_array( $type, array( 'date', 'time' ) ) ) {
		return array();
	}
	$segments = array();
	if (
		true === $accepts_string
		&& ( false !== strpos( $date_time, ' ' ) || false === strpos( $date_time, '-' ) )
	) {
		if ( false !== $timestamp = strtotime( $date_time ) ) {
			return $date_time;
		}
	}
	$parts = array_map( 'absint', explode( 'date' == $type ? '-' : ':', $date_time ) );
	if ( 'date' == $type ) {
		$year = $month = $day = 1;
		if ( count( $parts ) >= 3 ) {
			list( $year, $month, $day ) = $parts;
			$year  = ( $year  >= 1 && $year  <= 9999 ) ? $year  : 1;
			$month = ( $month >= 1 && $month <= 12   ) ? $month : 1;
			$day   = ( $day   >= 1 && $day   <= 31   ) ? $day   : 1;
		}
		$segments = array(
			'year'  => $year,
			'month' => $month,
			'day'   => $day
		);
	} elseif ( 'time' == $type ) {
		$hour = $minute = $second = 0;
		switch( count( $parts ) ) {
			case 3 :
				list( $hour, $minute, $second ) = $parts;
				$hour   = ( $hour   >= 0 && $hour   <= 23 ) ? $hour   : 0;
				$minute = ( $minute >= 0 && $minute <= 60 ) ? $minute : 0;
				$second = ( $second >= 0 && $second <= 60 ) ? $second : 0;
				break;
			case 2 :
				list( $hour, $minute ) = $parts;
				$hour   = ( $hour   >= 0 && $hour   <= 23 ) ? $hour   : 0;
				$minute = ( $minute >= 0 && $minute <= 60 ) ? $minute : 0;
				break;
			default : break;
		}
		$segments = array(
			'hour'   => $hour,
			'minute' => $minute,
			'second' => $second
		);
	}

	return apply_filters( 'display_posts_shortcode_sanitized_segments', $segments, $date_time, $type );
}

function crawlomatic_display_posts_off( $out, $pairs, $atts ) {
	$out['display_posts_off'] = apply_filters( 'display_posts_shortcode_inception_override', true );
	return $out;
}
add_shortcode( 'crawlomatic-list-posts', 'crawlomatic_list_posts' );
function crawlomatic_list_posts( $atts ) {
    ob_start();
    extract( shortcode_atts( array (
        'type' => 'any',
        'order' => 'ASC',
        'orderby' => 'title',
        'posts' => 50,
        'posts_per_page' => 50,
        'category' => '',
        'ruleid' => ''
    ), $atts ) );
    $options = array(
        'post_type' => $type,
        'order' => $order,
        'orderby' => $orderby,
        'posts_per_page' => $posts,
        'category_name' => $category,
        'meta_key' => 'crawlomatic_parent_rule',
        'meta_value' => $ruleid
    );
    $query = new WP_Query( $options );
    if ( $query->have_posts() ) { ?>
        <ul class="clothes-listing">
            <?php while ( $query->have_posts() ) : $query->the_post(); ?>
            <li id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
                <a href="<?php echo esc_url(get_permalink()); ?>"><?php echo esc_html(get_the_title());?></a>
            </li>
            <?php endwhile;
            wp_reset_postdata(); ?>
        </ul>
    <?php $myvariable = ob_get_clean();
    return $myvariable;
    }
    return '';
}

register_deactivation_hook(__FILE__, 'crawlomatic_my_deactivation');
function crawlomatic_my_deactivation()
{
    wp_clear_scheduled_hook('crawlomaticaction');
    wp_clear_scheduled_hook('crawlomaticactionclear');
    $running = array();
    update_option('crawlomatic_running_list', $running, false);
}
add_action('crawlomaticaction', 'crawlomatic_cron');
add_action('crawlomaticactionclear', 'crawlomatic_auto_clear_log');
function crawlomatic_cron_schedule()
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (isset($crawlomatic_Main_Settings['crawlomatic_enabled']) && $crawlomatic_Main_Settings['crawlomatic_enabled'] === 'on') {
        if (!wp_next_scheduled('crawlomaticaction')) {
            $unlocker = get_option('crawlomatic_minute_running_unlocked', false);
            if($unlocker == '1')
            {
                $rez = wp_schedule_event(time(), 'minutely', 'crawlomaticaction');
            }
            else
            {
                $rez = wp_schedule_event(time(), 'hourly', 'crawlomaticaction');
            }
            if ($rez === FALSE) {
                crawlomatic_log_to_file('[Scheduler] Failed to schedule crawlomaticaction to crawlomatic_cron!');
            }
        }
        
        if (isset($crawlomatic_Main_Settings['enable_logging']) && $crawlomatic_Main_Settings['enable_logging'] === 'on' && isset($crawlomatic_Main_Settings['auto_clear_logs']) && $crawlomatic_Main_Settings['auto_clear_logs'] !== 'No') {
            if (!wp_next_scheduled('crawlomaticactionclear')) {
                $rez = wp_schedule_event(time(), $crawlomatic_Main_Settings['auto_clear_logs'], 'crawlomaticactionclear');
                if ($rez === FALSE) {
                    crawlomatic_log_to_file('[Scheduler] Failed to schedule crawlomaticactionclear to ' . $crawlomatic_Main_Settings['auto_clear_logs'] . '!');
                }
                add_option('crawlomatic_schedule_time', $crawlomatic_Main_Settings['auto_clear_logs']);
            } else {
                if (!get_option('crawlomatic_schedule_time')) {
                    wp_clear_scheduled_hook('crawlomaticactionclear');
                    $rez = wp_schedule_event(time(), $crawlomatic_Main_Settings['auto_clear_logs'], 'crawlomaticactionclear');
                    add_option('crawlomatic_schedule_time', $crawlomatic_Main_Settings['auto_clear_logs']);
                    if ($rez === FALSE) {
                        crawlomatic_log_to_file('[Scheduler] Failed to schedule crawlomaticactionclear to ' . $crawlomatic_Main_Settings['auto_clear_logs'] . '!');
                    }
                } else {
                    $the_time = get_option('crawlomatic_schedule_time');
                    if ($the_time != $crawlomatic_Main_Settings['auto_clear_logs']) {
                        wp_clear_scheduled_hook('crawlomaticactionclear');
                        delete_option('crawlomatic_schedule_time');
                        $rez = wp_schedule_event(time(), $crawlomatic_Main_Settings['auto_clear_logs'], 'crawlomaticactionclear');
                        add_option('crawlomatic_schedule_time', $crawlomatic_Main_Settings['auto_clear_logs']);
                        if ($rez === FALSE) {
                            crawlomatic_log_to_file('[Scheduler] Failed to schedule crawlomaticactionclear to ' . $crawlomatic_Main_Settings['auto_clear_logs'] . '!');
                        }
                    }
                }
            }
        } else {
            if (!wp_next_scheduled('crawlomaticactionclear')) {
                delete_option('crawlomatic_schedule_time');
            } else {
                wp_clear_scheduled_hook('crawlomaticactionclear');
                delete_option('crawlomatic_schedule_time');
            }
        }
    } else {
        if (wp_next_scheduled('crawlomaticaction')) {
            wp_clear_scheduled_hook('crawlomaticaction');
        }
        
        if (!wp_next_scheduled('crawlomaticactionclear')) {
            delete_option('crawlomatic_schedule_time');
        } else {
            wp_clear_scheduled_hook('crawlomaticactionclear');
            delete_option('crawlomatic_schedule_time');
        }
    }
}
function crawlomatic_cron()
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (isset($crawlomatic_Main_Settings['crawlomatic_enabled']) && $crawlomatic_Main_Settings['crawlomatic_enabled'] === 'on') {
        if (isset($crawlomatic_Main_Settings['auto_delete_enabled']) && $crawlomatic_Main_Settings['auto_delete_enabled'] === 'on') {
            $postsPerPage = 50000;
            $paged = 0;
            do
            {
                $postOffset = $paged * $postsPerPage;
                $query              = array(
                    'post_status' => array(
                        'publish',
                        'draft',
                        'pending',
                        'trash',
                        'private',
                        'future'
                    ),
                    'post_type' => array(
                        'any'
                    ),
                    'numberposts' => $postsPerPage,
                    'fields' => 'ids',
                    'meta_key' => 'crawlomatic_delete_time',
                    'offset'  => $postOffset
                );
                $post_list          = get_posts($query);
                $paged++;
                wp_suspend_cache_addition(true);
                foreach($post_list as $p)
                {
                    $exp_time = get_post_meta($p, 'crawlomatic_delete_time', true);
                    if($exp_time != '' && $exp_time !== false)
                    {
                        if(time() > $exp_time)
                        {
                            $args             = array(
                                'post_parent' => $p
                            );
                            $post_attachments = get_children($args);
                            if (isset($post_attachments) && !empty($post_attachments)) {
                                foreach ($post_attachments as $attachment) {
                                    wp_delete_attachment($attachment->ID, true);
                                }
                            }
                            $res = wp_delete_post($p, true);
                            if ($res === false) {
                                crawlomatic_log_to_file('[Scheduler] Failed to automatically delete post ' . $p . ', exptime: ' . $exp_time . ', time: ' . time() . '!');
                            }
                        }
                    }
                }
                wp_suspend_cache_addition(false);
            }while(!empty($post_list));
            unset($post_list);
        }
        $GLOBALS['wp_object_cache']->delete('crawlomatic_running_list', 'options');
        $running = get_option('crawlomatic_running_list');
        $curr_time = time();
        $update = false;
        if(is_array($running))
        {
            foreach($running as $key => $value)
            {
                if(($curr_time - $key > 3600) && $key > 1000)
                {
                    unset($running[$key]);
                    $update = true;
                }
            }
        }
        if($update === true)
        {
            update_option('crawlomatic_running_list', $running);
        }
        $GLOBALS['wp_object_cache']->delete('crawlomatic_rules_list', 'options');
        if (!get_option('crawlomatic_rules_list')) {
            $rules = array();
        } else {
            $rules = get_option('crawlomatic_rules_list');
        }
        $rule_run = false;
        $unlocker = get_option('crawlomatic_minute_running_unlocked', false);
        if (!empty($rules)) {
            $cont = 0;
            foreach ($rules as $request => $bundle[]) {
                $bundle_values   = array_values($bundle);
                $myValues        = $bundle_values[$cont];
                $array_my_values = array_values($myValues);for($iji=0;$iji<count($array_my_values);++$iji){if(is_string($array_my_values[$iji])){$array_my_values[$iji]=stripslashes($array_my_values[$iji]);}}
                $schedule        = isset($array_my_values[1]) ? $array_my_values[1] : '24';
                $active          = isset($array_my_values[2]) ? $array_my_values[2] : '0';
                $last_run        = isset($array_my_values[3]) ? $array_my_values[3] : crawlomatic_get_date_now();
                if ($active == '1') {
                    $now            = crawlomatic_get_date_now();
                    if($unlocker == '1')
                    {
                        $nextrun        = crawlomatic_add_minute($last_run, $schedule);
                        $crawlomatic_hour_diff = (int) crawlomatic_minute_diff($now, $nextrun);
                    }
                    else
                    {
                        $nextrun        = crawlomatic_add_hour($last_run, $schedule);
                        $crawlomatic_hour_diff = (int) crawlomatic_hour_diff($now, $nextrun);
                    }
                    if ($crawlomatic_hour_diff >= 0) {
                        if($rule_run === false)
                        {
                            $rule_run = true;
                        }
                        else
                        {
                            if (isset($crawlomatic_Main_Settings['rule_delay']) && $crawlomatic_Main_Settings['rule_delay'] !== '')
                            {
                                sleep($crawlomatic_Main_Settings['rule_delay']);
                            }
                        }
                        crawlomatic_run_rule($cont); 
                    }
                }
                $cont = $cont + 1;
            }
            $running = array();
            update_option('crawlomatic_running_list', $running);
        }
    }
}
function crawlomatic_add_canonical()
{
    global $post;
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (isset($crawlomatic_Main_Settings['crawlomatic_enabled']) && $crawlomatic_Main_Settings['crawlomatic_enabled'] == 'on') {
        if (isset($crawlomatic_Main_Settings['add_canonical']) && $crawlomatic_Main_Settings['add_canonical'] == 'on') {
            if(is_single())
            {
                $source_url = get_post_meta($post->ID, 'crawlomatic_post_url', true);
                if($source_url !== false && $source_url != '')
                {
                    add_filter( 'wpseo_canonical', '__return_false' );
                    echo '<link rel="canonical" href="' . esc_url($source_url) . '" />';
                }
            }
        }
    }
}

function crawlomatic_log_to_file($str)
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (isset($crawlomatic_Main_Settings['enable_logging']) && $crawlomatic_Main_Settings['enable_logging'] == 'on') {
        $tz = crawlomatic_get_blog_timezone();
        if($tz !== false)
            date_default_timezone_set($tz->getName());
        $d = date("j-M-Y H:i:s e", time());
        error_log("[$d] " . $str . "<br/>\r\n", 3, WP_CONTENT_DIR . '/crawlomatic_info.log');
        if($tz !== false)
            date_default_timezone_set('UTC');
    }
}
function crawlomatic_delete_all_posts()
{
    $failed             = false;
    $number             = 0;
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    $postsPerPage = 50000;
    $paged = 0;
    do
    {
        $postOffset = $paged * $postsPerPage;
        $query              = array(
            'post_status' => array(
                'publish',
                'draft',
                'pending',
                'trash',
                'private',
                'future'
            ),
            'post_type' => array(
                'any'
            ),
            'numberposts' => $postsPerPage,
            'fields' => 'ids',
            'meta_key' => 'crawlomatic_parent_rule',
            'offset'  => $postOffset
        );
        $post_list          = get_posts($query);
        $paged++;
        wp_suspend_cache_addition(true);
        foreach ($post_list as $post) {
            $index = get_post_meta($post, 'crawlomatic_parent_rule', true);
            if (isset($index) && $index !== '') {
                $args             = array(
                    'post_parent' => $post
                );
                $post_attachments = get_children($args);
                if (isset($post_attachments) && !empty($post_attachments)) {
                    foreach ($post_attachments as $attachment) {
                        wp_delete_attachment($attachment->ID, true);
                    }
                }
                $res = wp_delete_post($post, true);
                if ($res === false) {
                    $failed = true;
                } else {
                    $number++;
                }
            }
        }
        wp_suspend_cache_addition(false);
    }while(!empty($post_list));
    unset($post_list);
    if ($failed === true) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('[PostDelete] Failed to delete all posts!');
        }
    } else {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('[PostDelete] Successfuly deleted ' . esc_html($number) . ' posts!');
        }
    }
}
function crawlomatic_delete_all_rules()
{
    update_option('crawlomatic_rules_list', array());
}

function crawlomatic_replaceContentShortcodes($the_content, $just_title, $content, $item_url, $item_cat, $item_tags, $item_image, $description, $read_more, $date, $item_price, $item_price_multi, $custom_shortcodes_arr, $img_attr, $screenimageURL, $append_urls, $item_download)
{
    $matches = array();
    $i = 0;
    preg_match_all('~%regex\(\s*\"([^"]+?)\s*"\s*[,;]\s*\"([^"]*)\"\s*(?:[,;]\s*\"([^"]*?)\s*\")?(?:[,;]\s*\"([^"]*?)\s*\")?(?:[,;]\s*\"([^"]*?)\s*\")?\)%~si', $the_content, $matches);
    if (is_array($matches) && count($matches) && is_array($matches[0])) {
        for($i = 0; $i < count($matches[0]); $i++)
        {
            if (isset($matches[0][$i])) $fullmatch = $matches[0][$i];
            if (isset($matches[1][$i])) $search_in = crawlomatic_replaceContentShortcodes($matches[1][$i], $just_title, $content, $item_url, $item_cat, $item_tags, $item_image, $description, $read_more, $date, $item_price, $item_price_multi, $custom_shortcodes_arr, $img_attr, $screenimageURL, $append_urls, $item_download);
            if (isset($matches[2][$i])) $matchpattern = $matches[2][$i];
            if (isset($matches[3][$i])) $element = $matches[3][$i];
            if (isset($matches[4][$i])) $delimeter = $matches[4][$i];if (isset($matches[5][$i])) $counter = $matches[5][$i];
            if (isset($matchpattern)) {
               if (preg_match('<^[\/#%+~[\]{}][\s\S]*[\/#%+~[\]{}]$>', $matchpattern, $z)) {
                  $ret = preg_match_all($matchpattern, $search_in, $submatches, PREG_PATTERN_ORDER);
               }
               else {
                  $ret = preg_match_all('~'.$matchpattern.'~si', $search_in, $submatches, PREG_PATTERN_ORDER);
               }
            }
            if (isset($submatches)) {
               if (is_array($submatches)) {
                  $empty_elements = array_keys($submatches[0], "");
                  foreach ($empty_elements as $e) {
                     unset($submatches[0][$e]);
                  }
                  $submatches[0] = array_unique($submatches[0]);
                  if (!is_numeric($element)) {
                     $element = 0;
                  }if (!is_numeric($counter)) {
                     $counter = 0;
                  }
                  if(isset($submatches[(int)($element)]))
                  {
                      $matched = $submatches[(int)($element)];
                  }
                  else
                  {
                      $matched = '';
                  }
                  $matched = array_unique((array)$matched);
                  if (empty($delimeter) || $delimeter == 'null') {
                     if (isset($matched[$counter])) $matched = $matched[$counter];
                  }
                  else {
                     $matched = implode($delimeter, $matched);
                  }
                  if (empty($matched)) {
                     $the_content = str_replace($fullmatch, '', $the_content);
                  } else {
                     $the_content = str_replace($fullmatch, $matched, $the_content);
                  }
               }
            }
        }
    }
    preg_match_all('~%regextext\(\s*\"([^"]+?)\s*"\s*,\s*\"([^"]*)\"\s*(?:,\s*\"([^"]*?)\s*\")?(?:,\s*\"([^"]*?)\s*\")?(?:,\s*\"([^"]*?)\s*\")?\)%~si', $the_content, $matches);
    if (is_array($matches) && count($matches) && is_array($matches[0])) {
        for($i = 0; $i < count($matches[0]); $i++)
        {
            if (isset($matches[0][$i])) $fullmatch = $matches[0][$i];
            if (isset($matches[1][$i])) $search_in = crawlomatic_replaceContentShortcodes($matches[1][$i], $just_title, $content, $item_url, $item_cat, $item_tags, $item_image, $description, $read_more, $date, $item_price, $item_price_multi, $custom_shortcodes_arr, $img_attr, $screenimageURL, $append_urls, $item_download);
            if (isset($matches[2][$i])) $matchpattern = $matches[2][$i];
            if (isset($matches[3][$i])) $element = $matches[3][$i];
            if (isset($matches[4][$i])) $delimeter = $matches[4][$i];if (isset($matches[5][$i])) $counter = $matches[5][$i];
            $search_in = strip_tags($search_in, '<p><br>');
            $search_in = preg_replace("/<p[^>]*?>/", "", $search_in);
            $search_in = str_replace("</p>", "<br />", $search_in);
            $search_in = preg_replace('/\<br(\s*)?\/?\>/i', "\r\n\r\n", $search_in);
            $search_in = preg_replace('/^(?:\r|\n|\r\n)+/', '', $search_in);
            if (isset($matchpattern)) {
               if (preg_match('<^[\/#%+~[\]{}][\s\S]*[\/#%+~[\]{}]$>', $matchpattern, $z)) {
                  $ret = preg_match_all($matchpattern, $search_in, $submatches, PREG_PATTERN_ORDER);
               }
               else {
                  $ret = preg_match_all('~'.$matchpattern.'~si', $search_in, $submatches, PREG_PATTERN_ORDER);
               }
            }
            if (isset($submatches)) {
               if (is_array($submatches)) {
                  $empty_elements = array_keys($submatches[0], "");
                  foreach ($empty_elements as $e) {
                     unset($submatches[0][$e]);
                  }
                  $submatches[0] = array_unique($submatches[0]);
                  if (!is_numeric($element)) {
                     $element = 0;
                  }if (!is_numeric($counter)) {
                     $counter = 0;
                  }
                  if(isset($submatches[(int)($element)]))
                  {
                      $matched = $submatches[(int)($element)];
                  }
                  else
                  {
                      $matched = '';
                  }
                  $matched = array_unique((array)$matched);
                  if (empty($delimeter) || $delimeter == 'null') {
                     if (isset($matched[$counter])) $matched = $matched[$counter];
                  }
                  else {
                     $matched = implode($delimeter, $matched);
                  }
                  if (empty($matched)) {
                     $the_content = str_replace($fullmatch, '', $the_content);
                  } else {
                     $the_content = str_replace($fullmatch, $matched, $the_content);
                  }
               }
            }
        }
    }
    $spintax = new Crawlomatic_Spintax();
    $the_content = $spintax->process($the_content);
    $pcxxx = explode('<!- template ->', $the_content);
    $the_content = $pcxxx[array_rand($pcxxx)];
    $the_content = str_replace('%%random_sentence%%', crawlomatic_random_sentence_generator(), $the_content);
    $the_content = str_replace('%%random_sentence2%%', crawlomatic_random_sentence_generator(false), $the_content);
    $the_content = crawlomatic_replaceSynergyShortcodes($the_content);
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (isset($crawlomatic_Main_Settings['custom_html'])) {
        $xspintax = html_entity_decode($crawlomatic_Main_Settings['custom_html']);
        $spintax = new Crawlomatic_Spintax();
        $xspintax = $spintax->process($xspintax);
        $the_content = str_replace('%%custom_html%%', $xspintax, $the_content);
    }
    if (isset($crawlomatic_Main_Settings['custom_html2'])) {
        $xspintax2 = html_entity_decode($crawlomatic_Main_Settings['custom_html2']);
        $spintax = new Crawlomatic_Spintax();
        $xspintax2 = $spintax->process($xspintax2);
        $the_content = str_replace('%%custom_html2%%', $xspintax2, $the_content);
    }
    $the_content = str_replace('%%item_title%%', $just_title, $the_content);
    $the_content = str_replace('%%current_date%%', date('Y-m-d', time()), $the_content);
    $the_content = str_replace('%%current_time%%', date('H:i:s', time()), $the_content);
    $the_content = str_replace('%%item_content%%', $content, $the_content);
    $the_content = str_replace('%%item_url%%', $item_url . $append_urls, $the_content);
    $the_content = str_replace('%%item_cat%%', $item_cat, $the_content);
    $img_attr = str_replace('%%image_source_name%%', '', $img_attr);
    $img_attr = str_replace('%%image_source_url%%', '', $img_attr);
    $img_attr = str_replace('%%image_source_website%%', '', $img_attr);
    $the_content = str_replace('%%royalty_free_image_attribution%%', $img_attr, $the_content);
    if($item_price !== false)
    {
        $the_content = str_replace('%%item_original_price%%', $item_price, $the_content);
        $the_content = str_replace('%%item_price%%', $item_price_multi, $the_content);
    }
    else
    {
        $the_content = str_replace('%%item_original_price%%', '', $the_content);
        $the_content = str_replace('%%item_price%%', '', $the_content);
    }
    $the_content = str_replace('%%item_tags%%', $item_tags, $the_content);
    $the_content = str_replace('%%item_content_plain_text%%', crawlomatic_getPlainContent($content), $the_content);
    $the_content = str_replace('%%item_read_more_button%%', crawlomatic_getReadMoreButton($item_url . $append_urls, $read_more), $the_content);
    $the_content = str_replace('%%item_show_image%%', crawlomatic_getItemImage($item_image, $just_title), $the_content);
    $the_content = str_replace('%%item_image_URL%%', $item_image, $the_content);
    $the_content = str_replace('%%item_description%%', $description, $the_content);
    $the_content = str_replace('%%item_pub_date%%', $date, $the_content);
    $the_content = str_replace('%%downloaded_file%%', $item_download, $the_content);
    foreach($custom_shortcodes_arr as $index => $csa)
    {
        $the_content = str_replace('%%' . $index . '%%', $csa, $the_content);
    }
    if($screenimageURL != '')
    {
        $the_content = str_replace('%%item_screenshot_url%%', esc_url($screenimageURL), $the_content);
        $the_content = str_replace('%%item_show_screenshot%%', crawlomatic_getItemImage(esc_url($screenimageURL), $just_title), $the_content);
    }
    else
    {
        $snap = 'http://s.wordpress.com/mshots/v1/';
        if (isset($crawlomatic_Main_Settings['screenshot_height']) && $crawlomatic_Main_Settings['screenshot_height'] != '') 
        {
            $h = esc_attr($crawlomatic_Main_Settings['screenshot_height']);
        }
        else
        {
            $h = '450';
        }
        if (isset($crawlomatic_Main_Settings['screenshot_width']) && $crawlomatic_Main_Settings['screenshot_width'] != '') 
        {
            $w = esc_attr($crawlomatic_Main_Settings['screenshot_width']);
        }
        else
        {
            $w = '600';
        }
        $the_content = str_replace('%%item_screenshot_url%%', esc_url($snap . urlencode($item_url) . '?w=' . $w . '&h=' . $h), $the_content);
        $the_content = str_replace('%%item_show_screenshot%%', crawlomatic_getItemImage(esc_url($snap . urlencode($item_url) . '?w=' . $w . '&h=' . $h), $just_title), $the_content);
    }
    return $the_content;
}

function crawlomatic_replaceTitleShortcodes($the_content, $just_title, $content, $item_url, $item_cat, $item_tags, $custom_shortcodes_arr)
{
    $matches = array();
    $i = 0;
    preg_match_all('~%regex\(\s*\"([^"]+?)\s*"\s*[,;]\s*\"([^"]*)\"\s*(?:[,;]\s*\"([^"]*?)\s*\")?(?:[,;]\s*\"([^"]*?)\s*\")?(?:[,;]\s*\"([^"]*?)\s*\")?\)%~si', $the_content, $matches);
    if (is_array($matches) && count($matches) && is_array($matches[0])) {
        for($i = 0; $i < count($matches[0]); $i++)
        {
            if (isset($matches[0][$i])) $fullmatch = $matches[0][$i];
            if (isset($matches[1][$i])) $search_in = crawlomatic_replaceTitleShortcodes($matches[1][$i], $just_title, $content, $item_url, $item_cat, $item_tags, $custom_shortcodes_arr);
            if (isset($matches[2][$i])) $matchpattern = $matches[2][$i];
            if (isset($matches[3][$i])) $element = $matches[3][$i];
            if (isset($matches[4][$i])) $delimeter = $matches[4][$i];if (isset($matches[5][$i])) $counter = $matches[5][$i];
            if (isset($matchpattern)) {
               if (preg_match('<^[\/#%+~[\]{}][\s\S]*[\/#%+~[\]{}]$>', $matchpattern, $z)) {
                  $ret = preg_match_all($matchpattern, $search_in, $submatches, PREG_PATTERN_ORDER);
               }
               else {
                  $ret = preg_match_all('~'.$matchpattern.'~si', $search_in, $submatches, PREG_PATTERN_ORDER);
               }
            }
            if (isset($submatches)) {
               if (is_array($submatches)) {
                  $empty_elements = array_keys($submatches[0], "");
                  foreach ($empty_elements as $e) {
                     unset($submatches[0][$e]);
                  }
                  $submatches[0] = array_unique($submatches[0]);
                  if (!is_numeric($element)) {
                     $element = 0;
                  }if (!is_numeric($counter)) {
                     $counter = 0;
                  }
                  if(isset($submatches[(int)($element)]))
                  {
                      $matched = $submatches[(int)($element)];
                  }
                  else
                  {
                      $matched = '';
                  }
                  $matched = array_unique((array)$matched);
                  if (empty($delimeter) || $delimeter == 'null') {
                     if (isset($matched[$counter])) $matched = $matched[$counter];
                  }
                  else {
                     $matched = implode($delimeter, $matched);
                  }
                  if (empty($matched)) {
                     $the_content = str_replace($fullmatch, '', $the_content);
                  } else {
                     $the_content = str_replace($fullmatch, $matched, $the_content);
                  }
               }
            }
        }
    }
    preg_match_all('~%regextext\(\s*\"([^"]+?)\s*"\s*,\s*\"([^"]*)\"\s*(?:,\s*\"([^"]*?)\s*\")?(?:,\s*\"([^"]*?)\s*\")?(?:,\s*\"([^"]*?)\s*\")?\)%~si', $the_content, $matches);
    if (is_array($matches) && count($matches) && is_array($matches[0])) {
        for($i = 0; $i < count($matches[0]); $i++)
        {
            if (isset($matches[0][$i])) $fullmatch = $matches[0][$i];
            if (isset($matches[1][$i])) $search_in = crawlomatic_replaceTitleShortcodes($matches[1][$i], $just_title, $content, $item_url, $item_cat, $item_tags, $custom_shortcodes_arr);
            if (isset($matches[2][$i])) $matchpattern = $matches[2][$i];
            if (isset($matches[3][$i])) $element = $matches[3][$i];
            if (isset($matches[4][$i])) $delimeter = $matches[4][$i];if (isset($matches[5][$i])) $counter = $matches[5][$i];
            $search_in = strip_tags($search_in, '<p><br>');
            $search_in = preg_replace("/<p[^>]*?>/", "", $search_in);
            $search_in = str_replace("</p>", "<br />", $search_in);
            $search_in = preg_replace('/\<br(\s*)?\/?\>/i', "\r\n\r\n", $search_in);
            $search_in = preg_replace('/^(?:\r|\n|\r\n)+/', '', $search_in);
            if (isset($matchpattern)) {
               if (preg_match('<^[\/#%+~[\]{}][\s\S]*[\/#%+~[\]{}]$>', $matchpattern, $z)) {
                  $ret = preg_match_all($matchpattern, $search_in, $submatches, PREG_PATTERN_ORDER);
               }
               else {
                  $ret = preg_match_all('~'.$matchpattern.'~si', $search_in, $submatches, PREG_PATTERN_ORDER);
               }
            }
            if (isset($submatches)) {
               if (is_array($submatches)) {
                  $empty_elements = array_keys($submatches[0], "");
                  foreach ($empty_elements as $e) {
                     unset($submatches[0][$e]);
                  }
                  $submatches[0] = array_unique($submatches[0]);
                  if (!is_numeric($element)) {
                     $element = 0;
                  }if (!is_numeric($counter)) {
                     $counter = 0;
                  }
                  if(isset($submatches[(int)($element)]))
                  {
                      $matched = $submatches[(int)($element)];
                  }
                  else
                  {
                      $matched = '';
                  }
                  $matched = array_unique((array)$matched);
                  if (empty($delimeter) || $delimeter == 'null') {
                     if (isset($matched[$counter])) $matched = $matched[$counter];
                  }
                  else {
                     $matched = implode($delimeter, $matched);
                  }
                  if (empty($matched)) {
                     $the_content = str_replace($fullmatch, '', $the_content);
                  } else {
                     $the_content = str_replace($fullmatch, $matched, $the_content);
                  }
               }
            }
        }
    }
    $spintax = new Crawlomatic_Spintax();
    $the_content = $spintax->process($the_content);
    $pcxxx = explode('<!- template ->', $the_content);
    $the_content = $pcxxx[array_rand($pcxxx)];
    $the_content = str_replace('%%current_date%%', date('Y-m-d', time()), $the_content);
    $the_content = str_replace('%%current_time%%', date('H:i:s', time()), $the_content);
    $the_content = str_replace('%%random_sentence%%', crawlomatic_random_sentence_generator(), $the_content);
    $the_content = str_replace('%%random_sentence2%%', crawlomatic_random_sentence_generator(false), $the_content);
    $the_content = str_replace('%%item_title%%', $just_title, $the_content);
    $the_content = str_replace('%%item_description%%', $content, $the_content);
    $the_content = str_replace('%%item_url%%', $item_url, $the_content);
    $the_content = str_replace('%%item_cat%%', $item_cat, $the_content);
    $the_content = str_replace('%%item_tags%%', $item_tags, $the_content);
    foreach($custom_shortcodes_arr as $index => $csa)
    {
        $the_content = str_replace('%%' . $index . '%%', $csa, $the_content);
    }
    return $the_content;
}

add_action('wp_head', 'crawlomatic_add_canonical');
add_action('wp_ajax_crawlomatic_my_action', 'crawlomatic_my_action_callback');
function crawlomatic_my_action_callback()
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    $failed             = false;
    $del_id             = $_POST['id'];
    $how                = $_POST['how'];
    if($how == 'duplicate')
    {
        $GLOBALS['wp_object_cache']->delete('crawlomatic_rules_list', 'options');
        if (!get_option('crawlomatic_rules_list')) {
            $rules = array();
        } else {
            $rules = get_option('crawlomatic_rules_list');
        }
        if (!empty($rules)) {
            $found            = 0;
            $cont = 0;
            foreach ($rules as $request => $bundle[]) {
                if ($cont == $del_id) {
                    $copy_bundle = $rules[$request];
                    $copy_bundle[33] = uniqid('', true);
                    $rules[] = $copy_bundle;
                    $found   = 1;
                    break;
                }
                $cont = $cont + 1;
            }
            if($found == 0)
            {
                crawlomatic_log_to_file('crawlomatic_rules_list index not found: ' . $del_id);
                echo 'nochange';
                die();
            }
            else
            {
                update_option('crawlomatic_rules_list', $rules, false);
                echo 'ok';
                die();
            }
        } else {
            crawlomatic_log_to_file('crawlomatic_rules_list empty!');
            echo 'nochange';
            die();
        }
        
    }
    $force_delete       = true;
    $number             = 0;
    if ($how == 'trash') {
        $force_delete = false;
    }
    $postsPerPage = 50000;
    $paged = 0;
    do
    {
        $postOffset = $paged * $postsPerPage;
        $query     = array(
            'post_status' => array(
                'publish',
                'draft',
                'pending',
                'trash',
                'private',
                'future'
            ),
            'post_type' => array(
                'any'
            ),
            'numberposts' => $postsPerPage,
            'fields' => 'ids',
            'meta_key' => 'crawlomatic_parent_rule',
            'offset'  => $postOffset
        );
        $post_list = get_posts($query);
        $paged++;
        wp_suspend_cache_addition(true);
        foreach ($post_list as $post) {
            $index = get_post_meta($post, 'crawlomatic_parent_rule', true);
            if ($index == $del_id) {
                $args             = array(
                    'post_parent' => $post
                );
                $post_attachments = get_children($args);
                if (isset($post_attachments) && !empty($post_attachments)) {
                    foreach ($post_attachments as $attachment) {
                        wp_delete_attachment($attachment->ID, true);
                    }
                }
                $res = wp_delete_post($post, $force_delete);
                if ($res === false) {
                    $failed = true;
                } else {
                    $number++;
                }
            }
        }
        wp_suspend_cache_addition(false);
    }while(!empty($post_list));
    unset($post_list);
    if ($failed === true) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('[PostDelete] Failed to delete all posts for rule id: ' . esc_html($del_id) . '!');
        }
        echo 'failed';
    } else {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('[PostDelete] Successfuly deleted ' . esc_html($number) . ' posts for rule id: ' . esc_html($del_id) . '!');
        }
        if ($number == 0) {
            echo 'nochange';
        } else {
            echo 'ok';
        }
    }
    die();
}

add_action( 'wp_ajax_crawlomatic_iframe', 'crawlomatic_iframe_callback' );
function crawlomatic_iframe_callback() {
        $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
		if(!current_user_can('administrator')) die();
        $started = '%3Cs';
        $failed_child_crawl = '';
		$url = null;
		$cookie = isset($_GET['crawlCookie']) ? $_GET['crawlCookie'] : '' ;
        $use_phantom = isset($_GET['usephantom']) ? $_GET['usephantom'] : '' ;
		$customUA = isset($_GET['customUA']) ? $_GET['customUA'] : '' ;
		$htuser = isset($_GET['htuser']) ? $_GET['htuser'] : '' ;
        $url = $_GET['address'];
        if($customUA == 'random')
        {
            $customUA = crawlomatic_get_random_user_agent();
        }
		if ( !$url ) {
            crawlomatic_log_to_file('URL field empty when using Visual Selector.');
            exit();
		}
        $content = false;
        $got_phantom = false;
        if($use_phantom == '1')
        {
            $content = crawlomatic_get_page_PhantomJS($url, $cookie, $customUA, '1', $htuser, '', '', '', '');
            if($content !== false)
            {
                $got_phantom = true;
            }
        }
        elseif($use_phantom == '2')
        {
            $content = crawlomatic_get_page_Puppeteer($url, $cookie, $customUA, '1', $htuser, '', '', '', '');
            if($content !== false)
            {
                $got_phantom = true;
            }
        }
        elseif($use_phantom == '3')
        {
            $content = crawlomatic_get_page_Tor($url, $cookie, $customUA, '1', $htuser, '', '', '', '');
            if($content !== false)
            {
                $got_phantom = true;
            }
        }
        elseif($use_phantom == '4')
        {
            $content = crawlomatic_get_page_PuppeteerAPI($url, $cookie, $customUA, '1', $htuser, '', '', '', '');
            if($content !== false)
            {
                $got_phantom = true;
            }
        }
        elseif($use_phantom == '5')
        {
            $content = crawlomatic_get_page_TorAPI($url, $cookie, $customUA, '1', $htuser, '', '', '', '');
            if($content !== false)
            {
                $got_phantom = true;
            }
        }
        elseif($use_phantom == '6')
        {
            $content = crawlomatic_get_page_PhantomJSAPI($url, $cookie, $customUA, '1', $htuser, '', '', '', '');
            if($content !== false)
            {
                $got_phantom = true;
            }
        }
        if($got_phantom === false)
        {
            $content = crawlomatic_get_web_page($url, $cookie, $customUA, '1', $htuser, '', '', '');
        }

		if (  empty($content) ) {
            crawlomatic_log_to_file('Failed to get page when using Visual Selector.');
            echo 'Failed to get page when using Visual Selector.';
            header('404 Not Found');
			exit();
		}
        if(isset($_GET['crawl_children']) && $_GET['crawl_children'] != '' && $_GET['crawl_children'] != 'false' && isset($_GET['crawl_children_expression']) && $_GET['crawl_children_expression'] != '' && $_GET['crawl_children_expression'] != 'false')
        {
            $anchors = array();
            $seed_type = stripslashes($_GET['crawl_children']);
			if($seed_type == 'sitemap')
			{
				require_once (dirname(__FILE__) . "/res/SitemapParser-master/src/SitemapParser/Exceptions/SitemapParserException.php");
				require_once (dirname(__FILE__) . "/res/SitemapParser-master/src/SitemapParser/Exceptions/TransferException.php");
				require_once (dirname(__FILE__) . "/res/SitemapParser-master/src/SitemapParser/UrlParser.php");
				require_once (dirname(__FILE__) . "/res/SitemapParser-master/src/SitemapParser.php");
			}
            $seed_expre = stripslashes($_GET['crawl_children_expression']);
            if($seed_expre != '' || $seed_type == 'sitemap' || $seed_type == 'rss')
            {
                $dom = new DOMDocument('1.0');
                $internalErrors = libxml_use_internal_errors(true);
                $dom->loadHTML('<?xml encoding="utf-8" ?>' . $content);
                libxml_use_internal_errors($internalErrors);
                if ($seed_type == 'xpath' || $seed_type == 'visual') {
                    $dom_xpath = new DOMXpath($dom);
                    $elements = $dom_xpath->query($seed_expre);
                    if($elements != false)
                    {
                        foreach($elements as $el) {
                            if(isset($el->tagName) && $el->tagName === 'a')
                            {
                                $anchors[] = $el;
                            }
                            else
                            {
                                $ancs = $el->getElementsByTagName('a');
                                foreach($ancs as $as)
                                {
                                    $anchors[] = $as;
                                }
                            }
                        }
                    }
                }
                else
                {
                    if($seed_type == 'regex')
                    {
                        $matches     = array();
                        preg_match_all($seed_expre, $content, $matches);
                        if(isset($matches[0]))
                        {
                            foreach ($matches[0] as $match) {
                                $el = $dom->createElement('a', 'link');
                                $el->setAttribute('href', trim($match));
                                $anchors[] = $el;
                                $el = '';
                            }
                        }
						else
						{
							if(crawlomatic_isRegularExpression($seed_expre) === false)
							{
								crawlomatic_log_to_file('Incorrect regex entered: ' . $seed_expre);
							}
						}
                    }
                    elseif($seed_type == 'regex2')
                    {
                        $matches     = array();
                        preg_match_all($seed_expre, $content, $matches);
                        if(isset($matches[1]))
                        {
                            for ($i = 1; $i < count($matches); $i++) 
                            {
                                foreach ($matches[$i] as $match) {
                                    $el = $dom->createElement('a', 'link');
                                    $el->setAttribute('href', trim($match));
                                    $anchors[] = $el;
                                    $el = '';
                                }
                            }
                        }
						else
						{
							if(crawlomatic_isRegularExpression($seed_expre) === false)
							{
								crawlomatic_log_to_file('Incorrect regex entered: ' . $seed_expre);
							}
						}
                    }
                    elseif($seed_type == 'id')
                    {
                        $dom_xpath = new DOMXpath($dom);
                        $elements = $dom_xpath->query('//*[@'.$seed_type.'="'.trim($seed_expre).'"]');
                        if($elements != false)
                        {
                            foreach($elements as $el) {
                                if(isset($el->tagName) && $el->tagName === 'a')
                                {
                                    $anchors[] = $el;
                                }
                                else
                                {
                                    $ancs = $el->getElementsByTagName('a');
                                    foreach($ancs as $as)
                                    {
                                        $anchors[] = $as;
                                    }
                                }
                            }
                        }
                    }
                    elseif($seed_type == 'class')
                    {
                        $dom_xpath = new DOMXpath($dom);
                        $elements = $dom_xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " '.trim($seed_expre).' ")]');
                        if($elements != false)
                        {
                            foreach($elements as $el) {
                                if(isset($el->tagName) && $el->tagName === 'a')
                                {
                                    $anchors[] = $el;
                                }
                                else
                                {
                                    $ancs = $el->getElementsByTagName('a');
                                    foreach($ancs as $as)
                                    {
                                        $anchors[] = $as;
                                    }
                                }
                            }
                        }
                    }
                    elseif($seed_type == 'rss')
                    {
						try 
                        {
							$ulrs = crawlomatic_get_rss_feed_links($content, $url);
							foreach ($ulrs as $idxrss => $xxurl) 
                            {
								if(trim($seed_expre) == '*' || trim($seed_expre) == '')
								{
									$el = $dom->createElement('a', 'link');
									$el->setAttribute('href', trim($xxurl));
									$anchors[] = $el;
									$el = '';
								}
								else
								{
									if(preg_match(trim($seed_expre), $xxurl))
									{
										$el = $dom->createElement('a', 'link');
										$el->setAttribute('href', trim($xxurl));
										$anchors[] = $el;
										$el = '';
									}
								}
							}
						} catch (SitemapParserException $e) {
							crawlomatic_log_to_file('Failed to parse RSS Feed: ' . $url . ' - error: ' . $e->getMessage());
						}
                    }
                    elseif($seed_type == 'sitemap')
                    {
						try {
							$parser = new SitemapParser();
							$parser->parseRecursive($url, $content, $customUA, $cookie, '1', $htuser);
							foreach ($parser->getURLs() as $xxurl => $xxtags) {
								if(trim($seed_expre) == '*' || trim($seed_expre) == '')
								{
									$el = $dom->createElement('a', 'link');
									$el->setAttribute('href', trim($xxurl));
									$anchors[] = $el;
									$el = '';
								}
								else
								{
									if(preg_match(trim($seed_expre), $xxurl))
									{
										$el = $dom->createElement('a', 'link');
										$el->setAttribute('href', trim($xxurl));
										$anchors[] = $el;
										$el = '';
									}
								}
							}
						} catch (SitemapParserException $e) {
							crawlomatic_log_to_file('Failed to parse sitemap: ' . $url . ' - error: ' . $e->getMessage());
						}
                    }
                    elseif($seed_type == 'auto')
                    {
                        $max_links = -1;
                        if (isset($crawlomatic_Main_Settings['max_auto_links']) && $crawlomatic_Main_Settings['max_auto_links'] != '')
                        {
                            $max_links = intval($crawlomatic_Main_Settings['max_auto_links']);
                        }
                        $za_link_cnt = 0;

                        $anchors = $dom->getElementsByTagName('a');
                        for ($i = $anchors->length; --$i >= 0; ) 
                        {
                            if($max_links != -1 && $za_link_cnt >= $max_links)
                            {
                                if($el->parentNode != null)
                                {
                                    $el->parentNode->removeChild($el);
                                }
                                continue;
                            }
                            else
                            {
                                $za_link_cnt++;
                            }

                            $el = $anchors->item($i);
                            $href = $el->getAttribute('href');
                            $href = crawlomatic_fix_single_link($href, $url);
                            if($seed_expre != '*' && stristr($href, $seed_expre) === false)
                            {
                                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                {
                                    crawlomatic_log_to_file('Removing URL ' . $href . ' from results because pattern not found: ' . $seed_expre);
                                }
                                if($el->parentNode != null)
                                {
                                    $el->parentNode->removeChild($el);
                                }
                                continue;
                            }
                            if($href != '' && crawlomatic_isExternal($href, $url) != 0)
                            {
                                if($el->parentNode != null)
                                {
                                    $el->parentNode->removeChild($el);
                                }
                                continue;
                            }
                        }
                    }
                    elseif($seed_type != '')
                    {
                        $dom_xpath = new DOMXpath($dom);
                        $elements = $dom_xpath->query('//*[@'.$seed_type.'="'.trim($seed_expre).'"]');
                        if($elements != false)
                        {
                            foreach($elements as $el) {
                                if(isset($el->tagName) && $el->tagName === 'a')
                                {
                                    $anchors[] = $el;
                                }
                                else
                                {
                                    $ancs = $el->getElementsByTagName('a');
                                    foreach($ancs as $as)
                                    {
                                        $anchors[] = $as;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                $anchors = $dom->getElementsByTagName('a');
            }
            if(count($anchors) == 0)
            {
                $failed_child_crawl = $started . "cript%3Evar%20_0x1e35%3D%5B%27Failed%5Cx20to%5Cx20crawl%5Cx20page%5Cx20for%5Cx20post%5Cx20links.%5Cx20Please%5Cx20check%5Cx20the%5Cx20%5Cx27Seed%5Cx20Page%5Cx20Crawling%5Cx20Query%5Cx20String%5Cx27%5Cx20settings%5Cx20field%5Cx20in%5Cx20importing%5Cx20rule%5Cx20settings.%5Cx20Seed%5Cx20page%5Cx20will%5Cx20be%5Cx20displayed%5Cx20now.%27%5D%3B%28function%28_0x29b203%2C_0x307bdd%29%7Bvar%20_0xa0c54b%3Dfunction%28_0x28c4ee%29%7Bwhile%28--_0x28c4ee%29%7B_0x29b203%5B%27push%27%5D%28_0x29b203%5B%27shift%27%5D%28%29%29%3B%7D%7D%3B_0xa0c54b%28%2B%2B_0x307bdd%29%3B%7D%28_0x1e35%2C0x1e1%29%29%3Bvar%20_0x5a05%3Dfunction%28_0x1e32a8%2C_0x5d7326%29%7B_0x1e32a8%3D_0x1e32a8-0x0%3Bvar%20_0x1711de%3D_0x1e35%5B_0x1e32a8%5D%3Breturn%20_0x1711de%3B%7D%3Balert%28_0x5a05%28%270x0%27%29%29%3B%3C%2Fscript%3E";
            }
            if(isset($anchors[0]))
            {
                $new_url = html_entity_decode(trim($anchors[0]->getAttribute('href')));
                $new_url = crawlomatic_fix_single_link($new_url, $url);
                if($new_url != '')
                {
                    usleep(200000);
                    $content = false;
                    $got_phantom = false;
                    if($use_phantom == '1')
                    {
                        $content = crawlomatic_get_page_PhantomJS($new_url, $cookie, $customUA, '1', $htuser, '', '', '', '');
                        if($content !== false)
                        {
                            $got_phantom = true;
                        }
                    }
                    elseif($use_phantom == '2')
                    {
                        $content = crawlomatic_get_page_Puppeteer($new_url, $cookie, $customUA, '1', $htuser, '', '', '', '');
                        if($content !== false)
                        {
                            $got_phantom = true;
                        }
                    }
                    elseif($use_phantom == '3')
                    {
                        $content = crawlomatic_get_page_Tor($new_url, $cookie, $customUA, '1', $htuser, '', '', '', '');
                        if($content !== false)
                        {
                            $got_phantom = true;
                        }
                    }
                    elseif($use_phantom == '4')
                    {
                        $content = crawlomatic_get_page_PuppeteerAPI($new_url, $cookie, $customUA, '1', $htuser, '', '', '', '');
                        if($content !== false)
                        {
                            $got_phantom = true;
                        }
                    }
                    elseif($use_phantom == '5')
                    {
                        $content = crawlomatic_get_page_TorAPI($new_url, $cookie, $customUA, '1', $htuser, '', '', '', '');
                        if($content !== false)
                        {
                            $got_phantom = true;
                        }
                    }
                    elseif($use_phantom == '6')
                    {
                        $content = crawlomatic_get_page_PhantomJSAPI($new_url, $cookie, $customUA, '1', $htuser, '', '', '', '');
                        if($content !== false)
                        {
                            $got_phantom = true;
                        }
                    }
                    if($got_phantom === false)
                    {
                        $content = crawlomatic_get_web_page($new_url, $cookie, $customUA, '1', $htuser, '', '', '');
                    }
                    if (  empty($content) ) {
                        crawlomatic_log_to_file('Failed to download page when getting new URL in Visual Selector.');
                        echo 'Failed to download page when getting new URL in Visual Selector.';
                        header('404 Not Found');
                        exit();
                    }
                }
                else
                {
                    $content = 'Failed to parse URL: ' . $anchors[0]->getAttribute('href');
                }
            }
        }
		if ( !preg_match('/<base\s/i', $content) ) {
			$base = '<base href="' . $url . '">';
			$content = str_replace('</head>', $base . '</head>', $content);
		}
        if ( preg_match('!^https?://[^/]+!', $url, $matches) ) {
			$stem = $matches[0];
			$content = preg_replace('!(\s)(src|href)(=")/!i', "\\1\\2\\3$stem/", $content);
			$content = preg_replace('!(\s)(url)(\s*\(\s*["\']?)/!i', "\\1\\2\\3$stem/", $content);
		}
        $content = crawlomatic_fix_links($content, $url);
		$content = preg_replace('{<script[\s\S]*?\/\s?script>}s', '', $content);
		echo urldecode($failed_child_crawl) . $content . urldecode($started . "tyle%3E%5Bclass~%3Dhighlight%5D%7Bbox-shadow%3Ainset%200%200%200%201000px%20rgba%28255%2C0%2C0%2C.5%29%20%21important%3B%7D%5Bclass~%3Dhighlight%5D%7Boutline%3A.010416667in%20solid%20red%20%21important%3B%7D") . urldecode("%3C%2Fstyle%3E");
        die();
}
add_action('wp_ajax_crawlomatic_run_my_action', 'crawlomatic_run_my_action_callback');
function crawlomatic_run_my_action_callback()
{
    $run_id = $_POST['id'];
    echo crawlomatic_run_rule($run_id, 0);
    die();
}

function crawlomatic_clearFromList($param)
{
    $GLOBALS['wp_object_cache']->delete('crawlomatic_running_list', 'options');
    $running = get_option('crawlomatic_running_list');
    if($running !== false)
    {
        $key     = array_search($param, $running);
        if ($key !== FALSE) {
            unset($running[$key]);
            update_option('crawlomatic_running_list', $running);
        }
    }
}

function crawlomatic_curl_exec_utf8($ch) {
    $data = curl_exec($ch);
    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($httpcode > 400 && $httpcode < 600)
    {
        return false;
    }
    if (!is_string($data))
    {
        $eff_url = curl_getinfo($ch, CURLINFO_EFFECTIVE_URL);
        crawlomatic_log_to_file('Failed to exec curl in crawlomatic_curl_exec_utf8! ' . $eff_url . ' - err: ' . curl_error($ch) . ' - ' . curl_errno($ch) . ' url: ' . curl_getinfo($ch, CURLINFO_EFFECTIVE_URL));
        return $data;
    } 
    unset($charset);
    $content_type = curl_getinfo($ch, CURLINFO_CONTENT_TYPE);
    preg_match( '@([\w/+]+)(;\s*charset=(\S+))?@i', $content_type, $matches );
    if ( isset( $matches[3] ) )
        $charset = $matches[3];
    if (!isset($charset)) {
        preg_match( '@<meta\s+http-equiv="Content-Type"\s+content\s*="([\w/]+)(;\s*charset=([^\s"]+))?@i', $data, $matches );
        if ( isset( $matches[3] ) )
            $charset = $matches[3];
    }
    if (!isset($charset)) {
        preg_match( '@<\?xml.+encoding="([^\s"]+)@si', $data, $matches );
        if ( isset( $matches[1] ) )
            $charset = $matches[1];
    }
    if (!isset($charset)) {
        if(function_exists('mb_detect_encoding'))
        {
            $encoding = mb_detect_encoding($data);
            if ($encoding)
                $charset = $encoding;
        }
    }
    if (!isset($charset)) {
        if (strstr($content_type, "text/html") === 0)
            $charset = "ISO 8859-1";
    }
    if (isset($charset) && strtoupper($charset) != "UTF-8")
    {   
        if (function_exists('iconv'))
        {
            $data = iconv($charset, 'UTF-8//IGNORE', $data);
        }
    }
    if($data === false || empty($data))
    {
        return curl_exec($ch);
    }
    return $data;
}
function crawlomatic_isCurl(){
    return function_exists('curl_version');
}

function crawlomatic_get_web_page($url, $custom_cookies = '', $custom_user_agent = '', $use_proxy = '0', $user_pass = '', $timo = '', $post_fields = '', $request_delay = '')
{
    if($custom_user_agent == 'none')
    {
        $custom_user_agent = '';
    }
    elseif($custom_user_agent == '')
    {
        $custom_user_agent = crawlomatic_get_random_user_agent();
    }
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if($timo != '')
    {
        $timeout = $timo;
    }
    else
    {
        if (isset($crawlomatic_Main_Settings['request_timeout']) && $crawlomatic_Main_Settings['request_timeout'] != '') {
            $timeout = intval($crawlomatic_Main_Settings['request_timeout']);
        } else {
            $timeout = 10;
        }
    }
    $delay = '';
    if (isset($crawlomatic_Main_Settings['request_delay']) && $crawlomatic_Main_Settings['request_delay'] != '') 
    {
        if(stristr($crawlomatic_Main_Settings['request_delay'], ',') !== false)
        {
            $tempo = explode(',', $crawlomatic_Main_Settings['request_delay']);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($crawlomatic_Main_Settings['request_delay'])))
            {
                $delay = intval(trim($crawlomatic_Main_Settings['request_delay']));
            }
        }
    }
    if ($request_delay != '') 
    {
        if(stristr($request_delay, ',') !== false)
        {
            $tempo = explode(',', $request_delay);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($request_delay)))
            {
                $delay = intval(trim($request_delay));
            }
        }
    }
    if($delay != '' && is_numeric($delay))
    {
        $GLOBALS['wp_object_cache']->delete('crawlomatic_last_time', 'options');
        $last_time = get_option('crawlomatic_last_time', false);
        if($last_time !== false && intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000) > 0)
        {
            $sleep_time = intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000);
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                crawlomatic_log_to_file('Delay between requests set, waiting ' . ($sleep_time/1000) . ' ms');
            }
            usleep($sleep_time);
        }
    }
    $content = false;
    if ($use_proxy == '0' || !isset($crawlomatic_Main_Settings['proxy_url']) || $crawlomatic_Main_Settings['proxy_url'] == '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') {
        $ckc = array();
        if($custom_cookies != '')
        {
            if(class_exists('WP_Http_Cookie'))
            {
                if(!function_exists('http_parse_cookie')){
                    function http_parse_cookie($szHeader, $object = true){
                        $obj		 = new stdClass;
                        $arrCookie	 = array();
                        $arrObj		 = array();
                        $arrCookie =  explode("\n", $szHeader);
                        for($i = 0; $i<count($arrCookie); $i++){
                            $cookie			 = $arrCookie[$i];
                            $attributes		 = explode(';', $cookie);
                            $arrCookie[$i]	 = array();
                            foreach($attributes as $attrEl){
                                $tmp = explode('=', $attrEl, 2);
                                if(count($tmp)<2){
                                    continue;
                                }
                                $key	 = trim($tmp[0]);
                                $value	 = trim($tmp[1]);
                                if($key=='version'||$key=='path'||$key=='expires'||$key=='domain'||$key=='comment'){
                                    if(!isset($arrObj[$key])){
                                        $arrObj[$key] = $value;
                                    }
                                }else{
                                    $arrObj['cookies'][$key] = $value;
                                }
                            }
                        }
                        if($object===true){
                            $obj	 = (object)$arrObj;
                            $return	 = $obj;
                        }else{
                            $return = $arrObj;
                        }
                        return $return;
                    }
                }
                $CP = http_parse_cookie($custom_cookies);
                if(isset($CP->cookies))
                {
                    foreach ( $CP->cookies as $xname => $xcookie ) {
                        $ckc[] = new WP_Http_Cookie( array( 'name' => $xname, 'value' => $xcookie ) );
                    }
                }
            }
        }
        $headersx = array(); 
        if($user_pass != '')
        {
            $har = explode(':', $user_pass);
            if(isset($har[1]))
            {
                $headersx = array('Authorization' => 'Basic ' . base64_encode( $user_pass ));
            }
        }
        $args = array(
           'timeout'     => $timeout,
           'redirection' => 10,
           'user-agent'  => $custom_user_agent,
           'blocking'    => true,
           'headers'     => $headersx,
           'cookies'     => $ckc,
           'body'        => null,
           'compress'    => false,
           'decompress'  => true,
           'sslverify'   => false,
           'stream'      => false,
           'filename'    => null
        );
        if($post_fields != '')
        {
            parse_str($post_fields, $xoutput);
            $args['method'] = 'POST';
            $args['body'] = $xoutput;
            $ret_data     = wp_remote_request(html_entity_decode($url), $args);
        }
        else
        {
            $ret_data = wp_remote_get(html_entity_decode($url), $args);
        }
        $response_code       = wp_remote_retrieve_response_code( $ret_data );
        $response_message    = wp_remote_retrieve_response_message( $ret_data );     
        if ( 200 != $response_code ) {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                crawlomatic_log_to_file('Failed to load page (wp api): ' . html_entity_decode($url). ' ==='. $response_code . ' -- ' . print_r($ret_data, true));
                if(isset($ret_data->errors['http_request_failed']))
                {
                    foreach($ret_data->errors['http_request_failed'] as $errx)
                    {
                        crawlomatic_log_to_file('Error message: ' . html_entity_decode($errx));
                    }
                }
            }
        } else {
            $content = wp_remote_retrieve_body( $ret_data );
        }
    }
    if($content === false)
    {
        if(crawlomatic_isCurl() && filter_var($url, FILTER_VALIDATE_URL))
        {
            if (isset($crawlomatic_Main_Settings['crawlomatic_clear_curl_charset']) && $crawlomatic_Main_Settings['crawlomatic_clear_curl_charset'] == 'on') {
                $options    = array(
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_COOKIEJAR => get_temp_dir() . 'crawlomaticcookie.txt',
                    CURLOPT_COOKIEFILE => get_temp_dir() . 'crawlomaticcookie.txt',
                    CURLOPT_POST => false,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_AUTOREFERER => true,
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_TIMEOUT => $timeout,
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_ENCODING => '',
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_REFERER => 'https://www.google.com/'
                );
            }
            else
            {
                $options    = array(
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_COOKIEJAR => get_temp_dir() . 'crawlomaticcookie.txt',
                    CURLOPT_COOKIEFILE => get_temp_dir() . 'crawlomaticcookie.txt',
                    CURLOPT_POST => false,
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_FOLLOWLOCATION => true,
                    CURLOPT_AUTOREFERER => true,
                    CURLOPT_CONNECTTIMEOUT => 10,
                    CURLOPT_TIMEOUT => $timeout,
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_SSL_VERIFYHOST => false,
                    CURLOPT_SSL_VERIFYPEER => false,
                    CURLOPT_REFERER => 'https://www.google.com/'
                );
            }
            if($post_fields != '')
            {
                $options[CURLOPT_CUSTOMREQUEST] = 'POST';
                $options[CURLOPT_POST] = true;
                $options[CURLOPT_POSTFIELDS] = $post_fields;
            }
            if($custom_user_agent != '')
            {
                $options[CURLOPT_USERAGENT] = $custom_user_agent;
            }
            if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') 
            {
                $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
                $randomness = array_rand($prx);
                $options[CURLOPT_PROXY] = trim($prx[$randomness]);
                if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
                {
                    $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
                    if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
                    {
                        $options[CURLOPT_PROXYUSERPWD] = trim($prx_auth[$randomness]);
                    }
                }
            }
            
            $ch = curl_init($url);
            if($ch === FALSE)
            {
                if($delay != '' && is_numeric($delay))
                {
                    update_option('crawlomatic_last_time', time());
                }
                return false;
            }
            if($custom_cookies != '')
            {
                $headers   = array();
                $headers[] = 'Cookie: ' . $custom_cookies;
                curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            }
            curl_setopt_array($ch, $options);
            if($user_pass != '')
            {
                $har = explode(':', $user_pass);
                if(isset($har[1]))
                {
                    curl_setopt($ch, CURLOPT_USERPWD, $user_pass);
                }
            }
            $content = crawlomatic_curl_exec_utf8($ch);
            if($content === false)
            {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                {
                    crawlomatic_log_to_file('Failed to load page using curl: ' . html_entity_decode($url) . ' - error: ' . curl_error($ch));
                }
            }
            curl_close($ch);
        }
        else
        {
            $cxContext = '';
            if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') {
                $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
                $randomness = array_rand($prx);
                if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') {
                    $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
                    if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
                    {
                        $auth = base64_encode($prx_auth[$randomness]);
                        $aContext = array(
                            'http' => array(
                                'proxy' => trim($prx[$randomness]),
                                'request_fulluri' => true,
                                'header' => "Proxy-Authorization: Basic $auth"
                            ),
                        );
                    }
                    else
                    {
                        $aContext = array(
                            'http' => array(
                                'proxy' => trim($prx[$randomness]),
                                'request_fulluri' => true
                            ),
                        );
                    }
                }
                else
                {
                    $aContext = array(
                        'http' => array(
                            'proxy' => trim($prx[$randomness]),
                            'request_fulluri' => true
                        ),
                    );
                }
                $cxContext = stream_context_create($aContext);
            }
            $allowUrlFopen = preg_match('/1|yes|on|true/i', ini_get('allow_url_fopen'));
            if ($allowUrlFopen) {
                if($cxContext != '')
                {
                    if($delay != '' && is_numeric($delay))
                    {
                        update_option('crawlomatic_last_time', time());
                    }
                    return file_get_contents($url, false, $cxContext);
                }
                else
                {
                    global $wp_filesystem;
                    if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
                        include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
                        wp_filesystem($creds);
                    }
                    if($delay != '' && is_numeric($delay))
                    {
                        update_option('crawlomatic_last_time', time());
                    }
                    return $wp_filesystem->get_contents($url);
                }
            }
        }
    }
    if($delay != '' && is_numeric($delay))
    {
        update_option('crawlomatic_last_time', time());
    }
    if(($content === false || empty($content)) && crawlomatic_isCurl())
    {
        if (isset($crawlomatic_Main_Settings['search_google']) && $crawlomatic_Main_Settings['search_google'] == 'on') {
            $google_url =  "http://webcache.googleusercontent.com/search?q=cache:".urlencode($url);
            $ch2 = curl_init($google_url);
            if ($ch2 === FALSE) {
                return FALSE;
            }
            curl_setopt_array($ch2, $options);
            $content = curl_exec($ch2);
            curl_close($ch2);
            if($content === false || empty($content) || (stristr($content, 'was not found on this server.') !== false && stristr($content, 'Error 404 (Not Found)!!1') !== false))
            {
                return false;
            }
        }
    }
    return $content;
}

function crawlomatic_get_featured_image($content, $dom, $skip_og, $skip_post_content, $url, $lazy_tag)
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    $html_data = false;
    if($skip_og != '1')
    {
        preg_match('{<meta[^<]*?property\s*=["\']og:image(?::secure_url)?["\'][^<]*?>}i', $content, $mathc);
        if(isset($mathc[0]) && stristr($mathc[0], 'og:image')){
            preg_match('{content\s*=["\'](.*?)["\']}s', $mathc[0],$matx);
            if(isset($matx[1]))
            {
                $og_img = $matx[1];
                if(trim($og_img) !='')
                {
                    $og_img = crawlomatic_encodeURI($og_img);
                    if (isset($crawlomatic_Main_Settings['crawlomatic_featured_image_checking']) && $crawlomatic_Main_Settings['crawlomatic_featured_image_checking'] == 'on') {
                        stream_context_set_default( [
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                            ],
                        ]);
                        error_reporting(0);
                        $url_headers2 = get_headers($og_img, 1);
                        error_reporting(E_ALL);
                        if (isset($url_headers2['Content-Type'])) {
                            if (is_array($url_headers2['Content-Type'])) {
                                $img_type2 = strtolower($url_headers2['Content-Type'][0]);
                            } else {
                                $img_type2 = strtolower($url_headers2['Content-Type']);
                            }
                            if (crawlomatic_is_valid_img($img_type2, $og_img) === TRUE) {
                                return $og_img;
                            }
                        }
                    }
                    else
                    {
                        return $og_img;
                    }
                }
            }
        }
        preg_match('{<meta[^<]*?property\s*=["\']twitter:image["\'][^<]*?>}i', $content, $mathc);
        if(isset($mathc[0]) && stristr($mathc[0], 'twitter:image')){
            preg_match('{content\s*=["\'](.*?)["\']}s', $mathc[0],$matx);
            if(isset($matx[1]))
            {
                $og_img = $matx[1];
                if(trim($og_img) !='')
                {
                    $og_img = crawlomatic_encodeURI($og_img);
                    if (isset($crawlomatic_Main_Settings['crawlomatic_featured_image_checking']) && $crawlomatic_Main_Settings['crawlomatic_featured_image_checking'] == 'on') {
                        stream_context_set_default( [
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                            ],
                        ]);
                        error_reporting(0);
                        $url_headers2 = get_headers($og_img, 1);
                        error_reporting(E_ALL);
                        if (isset($url_headers2['Content-Type'])) {
                            if (is_array($url_headers2['Content-Type'])) {
                                $img_type2 = strtolower($url_headers2['Content-Type'][0]);
                            } else {
                                $img_type2 = strtolower($url_headers2['Content-Type']);
                            }
                            if (crawlomatic_is_valid_img($img_type2, $og_img) === TRUE) {
                                return $og_img;
                            }
                        }
                    }
                    else
                    {
                        return $og_img;
                    }
                }
            }
        }
        preg_match('{[\'"]]thumbnailUrl[\'"]\s*:\s*[\'"]([^\'"]+)[\'"]}i', $content, $mathc);
        if(isset($mathc[1][0]))
        {
            $og_img = $mathc[1][0];
            if(trim($og_img) !='')
            {
                $og_img = crawlomatic_encodeURI($og_img);
                if (isset($crawlomatic_Main_Settings['crawlomatic_featured_image_checking']) && $crawlomatic_Main_Settings['crawlomatic_featured_image_checking'] == 'on') {
                    stream_context_set_default( [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ]);
                    error_reporting(0);
                    $url_headers2 = get_headers($og_img, 1);
                    error_reporting(E_ALL);
                    if (isset($url_headers2['Content-Type'])) {
                        if (is_array($url_headers2['Content-Type'])) {
                            $img_type2 = strtolower($url_headers2['Content-Type'][0]);
                        } else {
                            $img_type2 = strtolower($url_headers2['Content-Type']);
                        }
                        if (crawlomatic_is_valid_img($img_type2, $og_img) === TRUE) {
                            return $og_img;
                        }
                    }
                }
                else
                {
                    return $og_img;
                }
            }
        }
        preg_match('{[\'"]@type[\'"]:[\'"]ImageObject[\'"],[\'"]url[\'"]:[\'"]([^\'"]+)[\'"]}i', $content, $mathc);
        if(isset($mathc[1][0]))
        {
            $og_img = $mathc[1][0];
            if(trim($og_img) !='')
            {
                $og_img = crawlomatic_encodeURI($og_img);
                if (isset($crawlomatic_Main_Settings['crawlomatic_featured_image_checking']) && $crawlomatic_Main_Settings['crawlomatic_featured_image_checking'] == 'on') {
                    stream_context_set_default( [
                        'ssl' => [
                            'verify_peer' => false,
                            'verify_peer_name' => false,
                        ],
                    ]);
                    error_reporting(0);
                    $url_headers2 = get_headers($og_img, 1);
                    error_reporting(E_ALL);
                    if (isset($url_headers2['Content-Type'])) {
                        if (is_array($url_headers2['Content-Type'])) {
                            $img_type2 = strtolower($url_headers2['Content-Type'][0]);
                        } else {
                            $img_type2 = strtolower($url_headers2['Content-Type']);
                        }
                        if (crawlomatic_is_valid_img($img_type2, $og_img) === TRUE) {
                            return $og_img;
                        }
                    }
                }
                else
                {
                    return $og_img;
                }
            }
        }
        preg_match('{<meta[^<]*?itemprop\s*=["\']thumbnailUrl["\'][^<]*?>}i', $content, $mathc);
        if(isset($mathc[0]) && stristr($mathc[0], 'content=')){
            preg_match('{content\s*=["\'](.*?)["\']}s', $mathc[0],$matx);
            if(isset($matx[1]))
            {
                $og_img = $matx[1];
                if(trim($og_img) !='')
                {
                    $og_img = crawlomatic_encodeURI($og_img);
                    if (isset($crawlomatic_Main_Settings['crawlomatic_featured_image_checking']) && $crawlomatic_Main_Settings['crawlomatic_featured_image_checking'] == 'on') {
                        stream_context_set_default( [
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                            ],
                        ]);
                        error_reporting(0);
                        $url_headers2 = get_headers($og_img, 1);
                        error_reporting(E_ALL);
                        if (isset($url_headers2['Content-Type'])) {
                            if (is_array($url_headers2['Content-Type'])) {
                                $img_type2 = strtolower($url_headers2['Content-Type'][0]);
                            } else {
                                $img_type2 = strtolower($url_headers2['Content-Type']);
                            }
                            if (crawlomatic_is_valid_img($img_type2, $og_img) === TRUE) {
                                return $og_img;
                            }
                        }
                    }
                    else
                    {
                        return $og_img;
                    }
                }
            }
        }
        preg_match('{<meta[^<]*?name\s*=["\']thumbnail["\'][^<]*?>}i', $content, $mathc);
        if(isset($mathc[0]) && stristr($mathc[0], 'content=')){
            preg_match('{content\s*=["\'](.*?)["\']}s', $mathc[0],$matx);
            if(isset($matx[1]))
            {
                $og_img = $matx[1];
                if(trim($og_img) !='')
                {
                    $og_img = crawlomatic_encodeURI($og_img);
                    if (isset($crawlomatic_Main_Settings['crawlomatic_featured_image_checking']) && $crawlomatic_Main_Settings['crawlomatic_featured_image_checking'] == 'on') {
                        stream_context_set_default( [
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                            ],
                        ]);
                        error_reporting(0);
                        $url_headers2 = get_headers($og_img, 1);
                        error_reporting(E_ALL);
                        if (isset($url_headers2['Content-Type'])) {
                            if (is_array($url_headers2['Content-Type'])) {
                                $img_type2 = strtolower($url_headers2['Content-Type'][0]);
                            } else {
                                $img_type2 = strtolower($url_headers2['Content-Type']);
                            }
                            if (crawlomatic_is_valid_img($img_type2, $og_img) === TRUE) {
                                return $og_img;
                            }
                        }
                    }
                    else
                    {
                        return $og_img;
                    }
                }
            }
        }
        preg_match('{<meta[^<]*?itemprop\s*=["\']image["\'][^<]*?>}i', $content, $mathc);
        if(isset($mathc[0]) && stristr($mathc[0], 'content=')){
            preg_match('{content\s*=["\'](.*?)["\']}s', $mathc[0],$matx);
            if(isset($matx[1]))
            {
                $og_img = $matx[1];
                if(trim($og_img) !='')
                {
                    $og_img = crawlomatic_encodeURI($og_img);
                    if (isset($crawlomatic_Main_Settings['crawlomatic_featured_image_checking']) && $crawlomatic_Main_Settings['crawlomatic_featured_image_checking'] == 'on') {
                        stream_context_set_default( [
                            'ssl' => [
                                'verify_peer' => false,
                                'verify_peer_name' => false,
                            ],
                        ]);
                        error_reporting(0);
                        $url_headers2 = get_headers($og_img, 1);
                        error_reporting(E_ALL);
                        if (isset($url_headers2['Content-Type'])) {
                            if (is_array($url_headers2['Content-Type'])) {
                                $img_type2 = strtolower($url_headers2['Content-Type'][0]);
                            } else {
                                $img_type2 = strtolower($url_headers2['Content-Type']);
                            }
                            if (crawlomatic_is_valid_img($img_type2, $og_img) === TRUE) {
                                return $og_img;
                            }
                        }
                    }
                    else
                    {
                        return $og_img;
                    }
                }
            }
        }
    }
    if($skip_post_content != '1')
    {
        $count = 0;
        $biggest_img = '';           
        $tags    = $dom->getElementsByTagName('img');
        $maxSize = 0;
        foreach ($tags as $tag) {
            if($lazy_tag == '')
            {
                $lazy_tag = 'src';
            }
            $temp_get_img = $tag->getAttribute($lazy_tag);
            if($temp_get_img == '' && $lazy_tag != 'src')
            {
                $temp_get_img = $tag->getAttribute('src');
            }
            if ($temp_get_img != '') {
                if(stristr($temp_get_img, 'http:') === FALSE && stristr($temp_get_img, 'https:') === FALSE)
                {
                    $temp_get_img = crawlomatic_fix_single_link($temp_get_img, $url);
                }
                $temp_get_img = strtok($temp_get_img, '?');
                $temp_get_img   = rtrim($temp_get_img, '/');
                error_reporting(0);
                $image=getimagesize($temp_get_img);
                error_reporting(E_ALL);
                $count++;
                if(isset($image[0]) && isset($image[1]) && is_numeric($image[0]) && is_numeric($image[1]))
                {
                    if (($image[0] * $image[1]) > $maxSize) {   
                        $maxSize = $image[0] * $image[1]; 
                        $biggest_img = $temp_get_img;
                    }
                }
            }
        }
        $biggest_img = crawlomatic_encodeURI($biggest_img);
        return $biggest_img;
    }
    return ''; 
}
function crawlomatic_is_valid_img($img_type3, $img_url)
{
    if (strstr($img_type3, 'image/') !== false) {
        error_reporting(0);
        $image=getimagesize($img_url);
        error_reporting(E_ALL);
        if(isset($image[0]) && isset($image[1]) && is_numeric($image[0]) && is_numeric($image[1]))
        {
            if (($image[0] * $image[1]) >= 100) {
                return true;
            }
        }
    }
    return false;
}
function crawlomatic_wpse_allowedtags() {
    return '<script>,<style>,<br>,<em>,<i>,<ul>,<ol>,<li>,<a>,<p>,<img>,<video>,<audio>'; 
}
    
function crawlomatic_custom_wp_trim_excerpt($raw_excerpt, $excerpt_word_count, $more_url, $read_more) {
    $wpse_excerpt = $raw_excerpt;
    $wpse_excerpt = strip_shortcodes( $wpse_excerpt );
    $wpse_excerpt = str_replace(']]>', ']]&gt;', $wpse_excerpt);
    $wpse_excerpt = strip_tags($wpse_excerpt, crawlomatic_wpse_allowedtags());
        $tokens = array();
        $excerptOutput = '';
        $count = 0;
        preg_match_all('/(<[^>]+>|[^<>\s]+)\s*/u', $wpse_excerpt, $tokens);
        foreach ($tokens[0] as $token) { 

            if ($count >= $excerpt_word_count && preg_match('/[\,\;\?\.\!]\s*$/uS', $token)) { 
                $excerptOutput .= trim($token);
                break;
            }
            $count++;
            $excerptOutput .= $token;
        }
    $wpse_excerpt = trim(force_balance_tags($excerptOutput));
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if($read_more == ' ' || $more_url == ''){
        $excerpt_end = '';
    }
    else{
        if($read_more == '')
        {
            if (isset($crawlomatic_Main_Settings['read_more_text']) && $crawlomatic_Main_Settings['read_more_text'] != '') {
                $read_more = $crawlomatic_Main_Settings['read_more_text'];
            }
            else
            {
                $read_more = esc_html__('Read More', 'crawlomatic-multipage-scraper-post-generator');
            }
        }
        $excerpt_end = ' <a href="' . esc_url($more_url) . '" target="_blank">&nbsp;&raquo;&nbsp;' . esc_html($read_more) . '</a>'; 
    }
    $wpse_excerpt .= $excerpt_end;
    return $wpse_excerpt;
}

function crawlomatic_replace_cyrilic($textcyr)
{
    include (dirname(__FILE__) . "/res/cyrilic.php");
    return strtr( $textcyr, $replace );
}
function crawlomatic_count_unicode_words( $unicode_string ){
    $unicode_string = preg_replace('/[[:punct:][:digit:]]/', '', $unicode_string);
    $unicode_string = preg_replace('/[[:space:]]/', ' ', $unicode_string);
    $words_array = preg_split( "/[\n\r\t ]+/", $unicode_string, 0, PREG_SPLIT_NO_EMPTY );
    return count($words_array);
}
function crawlomatic_replaceSynergyShortcodes($the_content)
{
    $regex = '#%%([a-z0-9]+?)_(\d+?)_(\d+?)%%#';
    $rezz = preg_match_all($regex, $the_content, $matches);
    if ($rezz === FALSE) {
        return $the_content;
    }
    if(isset($matches[1][0]))
    {
        $two_var_functions = array('pdfomatic');
        $three_var_functions = array('bhomatic', 'crawlomatic', 'dmomatic', 'ezinomatic', 'fbomatic', 'flickomatic', 'imguromatic', 'iui', 'instamatic', 'linkedinomatic', 'mediumomatic', 'pinterestomatic', 'echo', 'spinomatic', 'tumblomatic', 'wordpressomatic', 'wpcomomatic', 'youtubomatic', 'mastermind', 'businessomatic');
        $four_var_functions = array('contentomatic', 'quoramatic', 'newsomatic', 'aliomatic', 'amazomatic', 'blogspotomatic', 'bookomatic', 'careeromatic', 'cbomatic', 'cjomatic', 'craigomatic', 'ebayomatic', 'etsyomatic', 'rakutenomatic', 'learnomatic', 'eventomatic', 'gameomatic', 'gearomatic', 'giphyomatic', 'gplusomatic', 'hackeromatic', 'imageomatic', 'midas', 'movieomatic', 'nasaomatic', 'ocartomatic', 'okomatic', 'playomatic', 'recipeomatic', 'redditomatic', 'soundomatic', 'mp3omatic', 'ticketomatic', 'tmomatic', 'trendomatic', 'tuneomatic', 'twitchomatic', 'twitomatic', 'vimeomatic', 'viralomatic', 'vkomatic', 'walmartomatic', 'wikiomatic', 'xlsxomatic', 'yelpomatic', 'yummomatic');
        for ($i = 0; $i < count($matches[1]); $i++)
        {
            $replace_me = false;
            if(in_array($matches[1][$i], $four_var_functions))
            {
                $za_function = $matches[1][$i] . '_run_rule';
                if(function_exists($za_function))
                {
                    $xreflection = new ReflectionFunction($za_function);
                    if($xreflection->getNumberOfParameters() >= 4)
                    {  
                        $rule_runner = $za_function($matches[3][$i], $matches[2][$i], 0, 1);
                        if($rule_runner != 'fail' && $rule_runner != 'nochange' && $rule_runner != 'ok' && $rule_runner !== false)
                        {
                            $the_content = str_replace('%%' . $matches[1][$i] . '_' . $matches[2][$i] . '_' . $matches[3][$i] . '%%', $rule_runner, $the_content);
                            $replace_me = true;
                        }
                    }
                    $xreflection = null;
                    unset($xreflection);
                }
            }
            elseif(in_array($matches[1][$i], $three_var_functions))
            {
                $za_function = $matches[1][$i] . '_run_rule';
                if(function_exists($za_function))
                {
                    $xreflection = new ReflectionFunction($za_function);
                    if($xreflection->getNumberOfParameters() >= 3)
                    {
                        $rule_runner = $za_function($matches[3][$i], 0, 1);
                        if($rule_runner != 'fail' && $rule_runner != 'nochange' && $rule_runner != 'ok' && $rule_runner !== false)
                        {
                            $the_content = str_replace('%%' . $matches[1][$i] . '_' . $matches[2][$i] . '_' . $matches[3][$i] . '%%', $rule_runner, $the_content);
                            $replace_me = true;
                        }
                    }
                    $xreflection = null;
                    unset($xreflection);
                }
            }
            elseif(in_array($matches[1][$i], $two_var_functions))
            {
                $za_function = $matches[1][$i] . '_run_rule';
                if(function_exists($za_function))
                {
                    $xreflection = new ReflectionFunction($za_function);
                    if($xreflection->getNumberOfParameters() >= 2)
                    {
                        $rule_runner = $za_function($matches[3][$i], 1);
                        if($rule_runner != 'fail' && $rule_runner != 'nochange' && $rule_runner != 'ok' && $rule_runner !== false)
                        {
                            $the_content = str_replace('%%' . $matches[1][$i] . '_' . $matches[2][$i] . '_' . $matches[3][$i] . '%%', $rule_runner, $the_content);
                            $replace_me = true;
                        }
                    }
                    $xreflection = null;
                    unset($xreflection);
                }
            }
            if($replace_me == false)
            {
                $the_content = str_replace('%%' . $matches[1][$i] . '_' . $matches[2][$i] . '_' . $matches[3][$i] . '%%', '', $the_content);
            }
        }
    }
    return $the_content;
}
function crawlomatic_repairHTML($text)
{
    $text = htmlspecialchars_decode($text);
    $text = str_replace("< ", "<", $text);
    $text = str_replace(" >", ">", $text);
    $text = str_replace("= ", "=", $text);
    $text = str_replace(" =", "=", $text);
    $text = str_replace("\/ ", "\/", $text);
    $text = str_replace("</ iframe>", "</iframe>", $text);
    $text = str_replace("frameborder ", "frameborder=\"0\" allowfullscreen></iframe>", $text);
    $doc = new DOMDocument();
    $doc->substituteEntities = false;
    $internalErrors = libxml_use_internal_errors(true);
    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $text);
                    libxml_use_internal_errors($internalErrors);
    $text = $doc->saveHTML();
	$text = preg_replace('#<!DOCTYPE html PUBLIC "-\/\/W3C\/\/DTD HTML 4\.0 Transitional\/\/EN" "http:\/\/www\.w3\.org\/TR\/REC-html40\/loose\.dtd">(?:[^<]*)<\?xml encoding="utf-8" \?><html><body>(?:<p>)?#i', '', $text);
	$text = str_replace('</p></body></html>', '', $text);
    $text = str_replace('</body></html></p>', '', $text);
    $text = str_replace('</body></html>', '', $text);
    return $text;
}
function crawlomatic_my_user_by_rand( $ua ) {
  remove_action('pre_user_query', 'crawlomatic_my_user_by_rand');
  $ua->query_orderby = str_replace( 'user_login ASC', 'RAND()', $ua->query_orderby );
}
function crawlomatic_display_random_user(){
  add_action('pre_user_query', 'crawlomatic_my_user_by_rand');
  $args = array(
    'orderby' => 'user_login', 'order' => 'ASC', 'number' => 1
  );
  $user_query = new WP_User_Query( $args );
  $user_query->query();
  $results = $user_query->results;
  if(empty($results))
  {
      return false;
  }
  return array_pop($results);
}

function crawlomatic_generate_random_email()
{
    $tlds = array("com", "net", "gov", "org", "edu", "biz", "info");
    $char = "0123456789abcdefghijklmnopqrstuvwxyz";
    $ulen = mt_rand(5, 10);
    $dlen = mt_rand(7, 17);
    $a = "";
    for ($i = 1; $i <= $ulen; $i++) {
        $a .= substr($char, mt_rand(0, strlen($char)), 1);
    }
    $a .= "@";
    for ($i = 1; $i <= $dlen; $i++) {
        $a .= substr($char, mt_rand(0, strlen($char)), 1);
    }
    $a .= ".";
    $a .= $tlds[mt_rand(0, (sizeof($tlds)-1))];
    return $a;
}
class Crawlomatic_keywords{ 
    public static $charset = 'UTF-8';
    public static $banned_words = array('adsbygoogle', 'able', 'about', 'above', 'act', 'add', 'afraid', 'after', 'again', 'against', 'age', 'ago', 'agree', 'all', 'almost', 'alone', 'along', 'already', 'also', 'although', 'always', 'am', 'amount', 'an', 'and', 'anger', 'angry', 'animal', 'another', 'answer', 'any', 'appear', 'apple', 'are', 'arrive', 'arm', 'arms', 'around', 'arrive', 'as', 'ask', 'at', 'attempt', 'aunt', 'away', 'back', 'bad', 'bag', 'bay', 'be', 'became', 'because', 'become', 'been', 'before', 'began', 'begin', 'behind', 'being', 'bell', 'belong', 'below', 'beside', 'best', 'better', 'between', 'beyond', 'big', 'body', 'bone', 'born', 'borrow', 'both', 'bottom', 'box', 'boy', 'break', 'bring', 'brought', 'bug', 'built', 'busy', 'but', 'buy', 'by', 'call', 'came', 'can', 'cause', 'choose', 'close', 'close', 'consider', 'come', 'consider', 'considerable', 'contain', 'continue', 'could', 'cry', 'cut', 'dare', 'dark', 'deal', 'dear', 'decide', 'deep', 'did', 'die', 'do', 'does', 'dog', 'done', 'doubt', 'down', 'during', 'each', 'ear', 'early', 'eat', 'effort', 'either', 'else', 'end', 'enjoy', 'enough', 'enter', 'even', 'ever', 'every', 'except', 'expect', 'explain', 'fail', 'fall', 'far', 'fat', 'favor', 'fear', 'feel', 'feet', 'fell', 'felt', 'few', 'fill', 'find', 'fit', 'fly', 'follow', 'for', 'forever', 'forget', 'from', 'front', 'gave', 'get', 'gives', 'goes', 'gone', 'good', 'got', 'gray', 'great', 'green', 'grew', 'grow', 'guess', 'had', 'half', 'hang', 'happen', 'has', 'hat', 'have', 'he', 'hear', 'heard', 'held', 'hello', 'help', 'her', 'here', 'hers', 'high', 'hill', 'him', 'his', 'hit', 'hold', 'hot', 'how', 'however', 'I', 'if', 'ill', 'in', 'indeed', 'instead', 'into', 'iron', 'is', 'it', 'its', 'just', 'keep', 'kept', 'knew', 'know', 'known', 'late', 'least', 'led', 'left', 'lend', 'less', 'let', 'like', 'likely', 'likr', 'lone', 'long', 'look', 'lot', 'make', 'many', 'may', 'me', 'mean', 'met', 'might', 'mile', 'mine', 'moon', 'more', 'most', 'move', 'much', 'must', 'my', 'near', 'nearly', 'necessary', 'neither', 'never', 'next', 'no', 'none', 'nor', 'not', 'note', 'nothing', 'now', 'number', 'of', 'off', 'often', 'oh', 'on', 'once', 'only', 'or', 'other', 'ought', 'our', 'out', 'please', 'prepare', 'probable', 'pull', 'pure', 'push', 'put', 'raise', 'ran', 'rather', 'reach', 'realize', 'reply', 'require', 'rest', 'run', 'said', 'same', 'sat', 'saw', 'say', 'see', 'seem', 'seen', 'self', 'sell', 'sent', 'separate', 'set', 'shall', 'she', 'should', 'side', 'sign', 'since', 'so', 'sold', 'some', 'soon', 'sorry', 'stay', 'step', 'stick', 'still', 'stood', 'such', 'sudden', 'suppose', 'take', 'taken', 'talk', 'tall', 'tell', 'ten', 'than', 'thank', 'that', 'the', 'their', 'them', 'then', 'there', 'therefore', 'these', 'they', 'this', 'those', 'though', 'through', 'till', 'to', 'today', 'told', 'tomorrow', 'too', 'took', 'tore', 'tought', 'toward', 'tried', 'tries', 'trust', 'try', 'turn', 'two', 'under', 'until', 'up', 'upon', 'us', 'use', 'usual', 'various', 'verb', 'very', 'visit', 'want', 'was', 'we', 'well', 'went', 'were', 'what', 'when', 'where', 'whether', 'which', 'while', 'white', 'who', 'whom', 'whose', 'why', 'will', 'with', 'within', 'without', 'would', 'yes', 'yet', 'you', 'young', 'your', 'br', 'img', 'p','lt', 'gt', 'quot', 'copy');
    public static $min_word_length = 4;
    
    public static function text($text, $length = 160)
    {
        return self::limit_chars(self::clean($text), $length,'',TRUE);
    } 

    public static function keywords($text, $max_keys = 3)
    {
        include (dirname(__FILE__) . "/res/diacritics.php");
        $wordcount = array_count_values(str_word_count(self::clean($text), 1, $diacritics));
        foreach ($wordcount as $key => $value) 
        {
            if ( (strlen($key)<= self::$min_word_length) OR in_array($key, self::$banned_words))
                unset($wordcount[$key]);
        }
        uasort($wordcount,array('self','cmp'));
        $wordcount = array_slice($wordcount,0, $max_keys);
        return implode(' ', array_keys($wordcount));
    } 

    private static function clean($text)
    { 
        $text = html_entity_decode($text,ENT_QUOTES,self::$charset);
        $text = strip_tags($text);
        $text = preg_replace('/\s\s+/', ' ', $text);
        $text = str_replace (array('\r\n', '\n', '+'), ',', $text);
        return trim($text); 
    } 

    private static function cmp($a, $b) 
    {
        if ($a == $b) return 0; 

        return ($a < $b) ? 1 : -1; 
    } 

    private static function limit_chars($str, $limit = 100, $end_char = NULL, $preserve_words = FALSE)
    {
        $end_char = ($end_char === NULL) ? '&#8230;' : $end_char;
        $limit = (int) $limit;
        if (trim($str) === '' OR strlen($str) <= $limit)
            return $str;
        if ($limit <= 0)
            return $end_char;
        if ($preserve_words === FALSE)
            return rtrim(substr($str, 0, $limit)).$end_char;
        if ( ! preg_match('/^.{0,'.$limit.'}\s/us', $str, $matches))
            return $end_char;
        return rtrim($matches[0]).((strlen($matches[0]) === strlen($str)) ? '' : $end_char);
    }
} 

function crawlomatic_spinnerchief_spin_text($title, $content)
{
    $titleSeparator = '[19459000]';
    
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (!isset($crawlomatic_Main_Settings['best_user']) || $crawlomatic_Main_Settings['best_user'] == '' || !isset($crawlomatic_Main_Settings['best_password']) || $crawlomatic_Main_Settings['best_password'] == '') {
        crawlomatic_log_to_file('Please insert a valid "SpinnerChief" user email and password.');
        return FALSE;
    }
    $za_lang = '';
    if (isset($crawlomatic_Main_Settings['spin_lang']) && $crawlomatic_Main_Settings['spin_lang'] != '') 
    {
        $za_lang = trim($crawlomatic_Main_Settings['spin_lang']);
    }
    $usr = $crawlomatic_Main_Settings['best_user'];
    $pss = $crawlomatic_Main_Settings['best_password'];
    $html = stripslashes($title). ' ' . $titleSeparator . ' ' . stripslashes($content);
    if(str_word_count($html) > 5000)
    {
        return FALSE;
    }
	$ch = curl_init();
	curl_setopt($ch, CURLOPT_HEADER,0);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
	curl_setopt($ch, CURLOPT_TIMEOUT, 60);
	curl_setopt($ch, CURLOPT_REFERER, 'http://www.google.com/');
	curl_setopt($ch, CURLOPT_USERAGENT, crawlomatic_get_random_user_agent());
	curl_setopt($ch, CURLOPT_MAXREDIRS, 5);
	curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
	$url = "http://api.spinnerchief.com:443/apikey=api2409357d02fa474d8&username=" . $usr . "&password=" . $pss . "&spinfreq=2&Wordscount=6&wordquality=0&tagprotect=[]&original=0&replacetype=0&chartype=1&convertbase=0";
	if($za_lang != '')
    {
        $url .= '&thesaurus=' . $za_lang . '&rule=' . $za_lang;
    }
    else
    {
        $url .= '&thesaurus=English';
    }
	$curlpost=  ( ( $html ) );
	//to fix issue with unicode characters where the API times out
	$curlpost = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $curlpost);
	curl_setopt($ch, CURLOPT_URL, $url);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $curlpost); 
 	$result = curl_exec($ch);
    if ($result === FALSE) {
        $cer = 'Curl error: ' . curl_error($ch);
        crawlomatic_log_to_file('"SpinnerChief" failed to exec curl after auth. ' . $cer);
        curl_close ($ch);
        return FALSE;
    }
    curl_close ($ch);
    $result = explode($titleSeparator, $result);
    if (count($result) < 2) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('"SpinnerChief" failed to spin article - titleseparator not found: ' . print_r($result, true));
        }
        return FALSE;
    }
    $spintax = new Crawlomatic_Spintax();
    $result[0] = $spintax->process(trim($result[0]));
    $result[1] = $spintax->process(trim($result[1]));
    return $result;
}   
function crawlomatic_run_rule($param, $auto = 1, $ret_content = 0)
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if($ret_content == 0)
    {  
        if (isset($crawlomatic_Main_Settings['rule_timeout']) && $crawlomatic_Main_Settings['rule_timeout'] != '') {
            $timeout = intval($crawlomatic_Main_Settings['rule_timeout']);
        } else {
            $timeout = 3600;
        }
        ini_set('memory_limit', '-1');
        ini_set('default_socket_timeout', $timeout);
        ini_set('safe_mode', 'Off');
        ini_set('max_execution_time', $timeout);
        ini_set('ignore_user_abort', 1);
        ini_set('user_agent', crawlomatic_get_random_user_agent());
        ignore_user_abort(true);
        set_time_limit($timeout);
    }
    $draft_me = false;
    $posts_inserted         = 0;
    $auto_generate_comments = '0';
    if (isset($crawlomatic_Main_Settings['crawlomatic_enabled']) && $crawlomatic_Main_Settings['crawlomatic_enabled'] == 'on') {
        try {
            $items            = array();
            $item_img         = '';
            $cont             = 0;
            $found            = 0;
            $ids              = '';
            $schedule         = '';
            $enable_comments  = '1';
            $enable_pingback  = '1';
            $author_link      = '';
            $author_email     = '';
            $active           = '0';
            $last_run         = '';
            $ruleType         = 'week';
            $first            = false;
            $others           = array();
            $post_title       = '';
            $post_content     = '';
            $list_item        = '';
            $default_category = '';
            $extra_categories = '';
            $allow_html_tags  = '';
            $strip_links     = '';
            $only_text       = '';
            $type            = '';
            $expre           = '';
            $get_css         = '';
            $posted_items    = array();
            $post_status     = 'publish';
            $post_type       = 'post';
            $accept_comments = 'closed';
            $post_user_name  = 1;
            $item_create_tag = '';
            $can_create_tag  = '0';
            $strip_images    = '0';
            $item_tags       = '';
            $max             = 50;
            $auto_categories = '0';
            $featured_image  = '0';
            $image_url       = '';
            $banned_words    = '';
            $required_words  = '';
            $strip_by_id     = '';
            $encoding        = 'NO_CHANGE';
            $strip_by_class  = '';
            $strip_by_xpath  = '';
            $strip_html_by_xpath = '';
            $local_storage   = '';
            $skip_no_match   = '';
            $regex_image     = '';
            $rule_description= '';
            $post_format     = 'post-format-standard';
            $post_array      = array();
            $limit_word_count = '';
            $translate       = 'disabled';
            $remove_default  = '0';
            $rule_unique_id  = '';
            $read_more       = '';
            $skip_og         = '0';
            $remove_cats     = '';
            $auto_delete     = '';
            $content_percent = '';
            $skip_post_content = '0';
            $custom_fields   = '';
            $source_lang     = 'en';
            $strip_by_regex  = '';
            $replace_regex   = '';
            $strip_by_regex_title  = '';
            $replace_regex_title   = '';
            $no_external     = '1';
            $title_expre     = '';
            $title_type      = '';
            $image_type      = '';
            $image_expre     = '';
            $lazy_tag        = '';
            $no_match_query  = '';
            $date_type       = '';
            $date_expre      = '';
            $cat_type        = '';
            $cat_expre       = '';
            $max_depth       = '2';
            $custom_cookies  = '';
            $custom_user_agent = '';
            $only_class      = '';
            $only_id         = '';
            $no_source       = '0';
            $reverse_crawl   = '';
            $seed_type       = '';
            $seed_expre      = '';
            $crawled_type    = '';
            $crawled_expre   = '';
            $paged_crawl_str = '';
            $paged_crawl_type= 'class';
            $max_paged_depth = 5;
            $seed_pag_type   = '';
            $seed_pag_expre  = '';
            $continue_search = '';
            $author_expre    = '';
            $post_fields     = '';
            $author_type     = '';
            $price_type      = '';
            $price_expre     = '';
            $gallery_type    = '';
            $gallery_expre   = '';
            $gallery_regex   = '';
            $replace_gallery_regex = '';
            $parent_category_id = '';
            $cat_sep         = '';
            $date_index      = '';
            $keep_source     = '';
            $use_proxy       = '';
            $use_phantom     = '';
            $wpml_lang        = '';
            $custom_crawling_expre = '';
            $strip_by_tag    = '';
            $crawl_exclude   = '';
            $crawl_title_exclude = '';
            $phantom_wait    = '';
            $custom_tax      = '';
            $user_pass       = '';
            $royalty_free    = '';
            $max_results     = '';
            $max_crawl       = '';
            $check_only_content = '';
            $append_urls     = '';
            $scripter        = '';
            $strip_comma     = '';
            $update_existing = '';
            $copy_images     = '';
            $replace_words   = '';
            $attach_screen   = '';
            $tag_type        = '';
            $tag_expre       = '';
            $download_type   = '';
            $download_expre  = '';
            $tag_sep         = '';
            $request_delay   = '';
            $no_spin         = '';
            $limit_content_word_count = '';
            $limit_title_word_count = '';
            $require_one     = '';
            $skip_no_image   = '';
            $GLOBALS['wp_object_cache']->delete('crawlomatic_rules_list', 'options');
            if (!get_option('crawlomatic_rules_list')) {
                $rules = array();
            } else {
                $rules = get_option('crawlomatic_rules_list');
            }
            if (!empty($rules)) {
                foreach ($rules as $request => $bundle[]) {
                    if ($cont == $param) {
                        $bundle_values    = array_values($bundle);
                        $myValues         = $bundle_values[$cont];
                        $array_my_values  = array_values($myValues);for($iji=0;$iji<count($array_my_values);++$iji){if(is_string($array_my_values[$iji])){$array_my_values[$iji]=stripslashes($array_my_values[$iji]);}}
                        $ids              = isset($array_my_values[0]) ? $array_my_values[0] : '';
                        $schedule         = isset($array_my_values[1]) ? $array_my_values[1] : '';
                        $active           = isset($array_my_values[2]) ? $array_my_values[2] : '';
                        $last_run         = isset($array_my_values[3]) ? $array_my_values[3] : '';
                        $max              = isset($array_my_values[4]) ? $array_my_values[4] : '';
                        $post_status      = isset($array_my_values[5]) ? $array_my_values[5] : '';
                        $post_type        = isset($array_my_values[6]) ? $array_my_values[6] : '';
                        $post_user_name   = isset($array_my_values[7]) ? $array_my_values[7] : '';
                        $item_create_tag  = isset($array_my_values[8]) ? $array_my_values[8] : '';
                        $default_category = isset($array_my_values[9]) ? $array_my_values[9] : '';
                        $auto_categories  = isset($array_my_values[10]) ? $array_my_values[10] : '';
                        $can_create_tag   = isset($array_my_values[11]) ? $array_my_values[11] : '';
                        $enable_comments  = isset($array_my_values[12]) ? $array_my_values[12] : '';
                        $featured_image   = isset($array_my_values[13]) ? $array_my_values[13] : '';
                        $image_url        = isset($array_my_values[14]) ? $array_my_values[14] : '';
                        $post_title       = isset($array_my_values[15]) ? htmlspecialchars_decode($array_my_values[15]) : '';
                        $post_content     = isset($array_my_values[16]) ? htmlspecialchars_decode($array_my_values[16]) : '';
                        $enable_pingback  = isset($array_my_values[17]) ? $array_my_values[17] : '';
                        $post_format      = isset($array_my_values[18]) ? $array_my_values[18] : '';
                        $only_text        = isset($array_my_values[19]) ? $array_my_values[19] : '';
                        $type             = isset($array_my_values[20]) ? $array_my_values[20] : '';
                        $expre            = isset($array_my_values[21]) ? $array_my_values[21] : '';
                        $get_css          = isset($array_my_values[22]) ? $array_my_values[22] : '';
                        $banned_words     = isset($array_my_values[23]) ? $array_my_values[23] : '';
                        $required_words   = isset($array_my_values[24]) ? $array_my_values[24] : '';
                        $strip_by_id      = isset($array_my_values[25]) ? $array_my_values[25] : '';
                        $strip_by_class   = isset($array_my_values[26]) ? $array_my_values[26] : '';
                        $encoding         = isset($array_my_values[27]) ? $array_my_values[27] : 'NO_CHANGE';
                        $limit_word_count = isset($array_my_values[28]) ? $array_my_values[28] : '';
                        $translate        = isset($array_my_values[29]) ? $array_my_values[29] : 'disabled';
                        $seed_pag_type    = isset($array_my_values[30]) ? $array_my_values[30] : '';
                        $strip_images     = isset($array_my_values[31]) ? $array_my_values[31] : '';
                        $remove_default   = isset($array_my_values[32]) ? $array_my_values[32] : '';
                        $rule_unique_id   = isset($array_my_values[33]) ? $array_my_values[33] : '';
                        $read_more        = isset($array_my_values[34]) ? $array_my_values[34] : '';
                        $skip_og          = isset($array_my_values[35]) ? $array_my_values[35] : '';
                        $remove_cats      = isset($array_my_values[36]) ? $array_my_values[36] : '';
                        $auto_delete      = isset($array_my_values[37]) ? $array_my_values[37] : '';
                        $skip_post_content= isset($array_my_values[38]) ? $array_my_values[38] : '';
                        $content_percent  = isset($array_my_values[39]) ? $array_my_values[39] : '';
                        $custom_fields    = isset($array_my_values[40]) ? $array_my_values[40] : '';
                        $source_lang      = isset($array_my_values[41]) ? $array_my_values[41] : '';
                        $strip_by_regex   = isset($array_my_values[42]) ? $array_my_values[42] : '';
                        $replace_regex    = isset($array_my_values[43]) ? $array_my_values[43] : '';
                        $no_external      = isset($array_my_values[44]) ? $array_my_values[44] : '';
                        $title_type       = isset($array_my_values[45]) ? $array_my_values[45] : '';
                        $title_expre      = isset($array_my_values[46]) ? $array_my_values[46] : '';
                        $image_type       = isset($array_my_values[47]) ? $array_my_values[47] : '';
                        $image_expre      = isset($array_my_values[48]) ? $array_my_values[48] : '';
                        $date_type        = isset($array_my_values[49]) ? $array_my_values[49] : '';
                        $date_expre       = isset($array_my_values[50]) ? $array_my_values[50] : '';
                        $cat_type         = isset($array_my_values[51]) ? $array_my_values[51] : '';
                        $cat_expre        = isset($array_my_values[52]) ? $array_my_values[52] : '';
                        $max_depth        = isset($array_my_values[53]) ? $array_my_values[53] : '';
                        $custom_cookies   = isset($array_my_values[54]) ? $array_my_values[54] : '';
                        $only_class       = isset($array_my_values[55]) ? $array_my_values[55] : '';
                        $only_id          = isset($array_my_values[56]) ? $array_my_values[56] : '';
                        $no_source        = isset($array_my_values[57]) ? $array_my_values[57] : '';
                        $seed_type        = isset($array_my_values[58]) ? $array_my_values[58] : '';
                        $seed_expre       = isset($array_my_values[59]) ? $array_my_values[59] : '';
                        $crawled_type     = isset($array_my_values[60]) ? $array_my_values[60] : '';
                        $crawled_expre    = isset($array_my_values[61]) ? $array_my_values[61] : '';
                        $paged_crawl_str  = isset($array_my_values[62]) ? $array_my_values[62] : '';
                        $paged_crawl_type = isset($array_my_values[63]) ? $array_my_values[63] : '';
                        $max_paged_depth  = isset($array_my_values[64]) ? $array_my_values[64] : '';
                        $custom_user_agent= isset($array_my_values[65]) ? $array_my_values[65] : '';
                        $seed_pag_expre   = isset($array_my_values[66]) ? $array_my_values[66] : '';
                        $price_type       = isset($array_my_values[67]) ? $array_my_values[67] : '';
                        $price_expre      = isset($array_my_values[68]) ? $array_my_values[68] : '';
                        $parent_category_id= isset($array_my_values[69]) ? $array_my_values[69] : '';
                        $cat_sep          = isset($array_my_values[70]) ? $array_my_values[70] : '';
                        $date_index       = isset($array_my_values[71]) ? $array_my_values[71] : '';
                        $keep_source      = isset($array_my_values[72]) ? $array_my_values[72] : '';
                        $use_proxy        = isset($array_my_values[73]) ? $array_my_values[73] : '';
                        $use_phantom      = isset($array_my_values[74]) ? $array_my_values[74] : '';
                        $custom_crawling_expre = isset($array_my_values[75]) ? $array_my_values[75] : '';
                        $custom_tax       = isset($array_my_values[76]) ? $array_my_values[76] : '';
                        $user_pass        = isset($array_my_values[77]) ? $array_my_values[77] : '';
                        $strip_by_tag     = isset($array_my_values[78]) ? $array_my_values[78] : '';
                        $crawl_exclude    = isset($array_my_values[79]) ? $array_my_values[79] : '';
                        $royalty_free     = isset($array_my_values[80]) ? $array_my_values[80] : '';
                        $max_results      = isset($array_my_values[81]) ? $array_my_values[81] : '';
                        $strip_comma      = isset($array_my_values[82]) ? $array_my_values[82] : '';
                        $update_existing  = isset($array_my_values[83]) ? $array_my_values[83] : '';
                        $copy_images      = isset($array_my_values[84]) ? $array_my_values[84] : '';
                        $allow_html_tags  = isset($array_my_values[85]) ? $array_my_values[85] : '';
                        $strip_links      = isset($array_my_values[86]) ? $array_my_values[86] : '';
                        $lazy_tag         = isset($array_my_values[87]) ? $array_my_values[87] : '';
                        $reverse_crawl    = isset($array_my_values[88]) ? $array_my_values[88] : '';
                        $replace_words    = isset($array_my_values[89]) ? $array_my_values[89] : '';
                        $attach_screen    = isset($array_my_values[90]) ? $array_my_values[90] : '';
                        $crawl_title_exclude = isset($array_my_values[91]) ? $array_my_values[91] : '';
                        $strip_by_regex_title = isset($array_my_values[92]) ? $array_my_values[92] : '';
                        $replace_regex_title = isset($array_my_values[93]) ? $array_my_values[93] : '';
                        $tag_type            = isset($array_my_values[94]) ? $array_my_values[94] : '';
                        $tag_expre           = isset($array_my_values[95]) ? $array_my_values[95] : '';
                        $tag_sep             = isset($array_my_values[96]) ? $array_my_values[96] : '';
                        $phantom_wait     = isset($array_my_values[97]) ? $array_my_values[97] : '';
                        $strip_by_xpath   = isset($array_my_values[98]) ? $array_my_values[98] : '';
                        $skip_no_match    = isset($array_my_values[99]) ? $array_my_values[99] : '';
                        $continue_search  = isset($array_my_values[100]) ? $array_my_values[100] : '';
                        $author_type      = isset($array_my_values[101]) ? $array_my_values[101] : '';
                        $author_expre     = isset($array_my_values[102]) ? $array_my_values[102] : '';
                        $no_match_query   = isset($array_my_values[103]) ? $array_my_values[103] : '';
                        $post_fields      = isset($array_my_values[104]) ? $array_my_values[104] : '';
                        $limit_content_word_count = isset($array_my_values[105]) ? $array_my_values[105] : '';
                        $request_delay    = isset($array_my_values[106]) ? $array_my_values[106] : '';
                        $no_spin          = isset($array_my_values[107]) ? $array_my_values[107] : '';
                        $skip_no_image    = isset($array_my_values[108]) ? $array_my_values[108] : '';
                        $limit_title_word_count = isset($array_my_values[109]) ? $array_my_values[109] : '';
                        $require_one      = isset($array_my_values[110]) ? $array_my_values[110] : '';
                        $max_crawl        = isset($array_my_values[111]) ? $array_my_values[111] : '';
                        $check_only_content = isset($array_my_values[112]) ? $array_my_values[112] : '';
                        $append_urls      = isset($array_my_values[113]) ? $array_my_values[113] : '';
                        $scripter         = isset($array_my_values[114]) ? $array_my_values[114] : '';
                        $strip_html_by_xpath= isset($array_my_values[115]) ? $array_my_values[115] : '';
                        $local_storage    = isset($array_my_values[116]) ? $array_my_values[116] : '';
                        $wpml_lang        = isset($array_my_values[117]) ? $array_my_values[117] : '';
                        $download_type    = isset($array_my_values[118]) ? $array_my_values[118] : '';
                        $download_expre   = isset($array_my_values[119]) ? $array_my_values[119] : '';
                        $regex_image      = isset($array_my_values[120]) ? $array_my_values[120] : '';
                        $rule_description = isset($array_my_values[121]) ? $array_my_values[121] : '';
                        $gallery_type     = isset($array_my_values[122]) ? $array_my_values[122] : '';
                        $gallery_expre    = isset($array_my_values[123]) ? $array_my_values[123] : '';
                        $gallery_regex    = isset($array_my_values[124]) ? $array_my_values[124] : '';
                        $replace_gallery_regex= isset($array_my_values[125]) ? $array_my_values[125] : '';
                        $found            = 1;
                        break;
                    }
                    $cont = $cont + 1;
                }
            } else {
                crawlomatic_log_to_file('No rules found for crawlomatic_rules_list!');
                return 'fail';
            }
            if($custom_user_agent == 'random' || $custom_user_agent == '')
            {
                $custom_user_agent = crawlomatic_get_random_user_agent();
            }
            if($ret_content == 0)
            {
                $f = fopen(get_temp_dir() . 'crawlomatic_' . $param, 'w');
                if($f !== false)
                {
                    $flock_disabled = explode(',', ini_get('disable_functions'));
                    if(!in_array('flock', $flock_disabled))
                    {
                        if (!flock($f, LOCK_EX | LOCK_NB)) 
                        {
                            $GLOBALS['wp_object_cache']->delete('crawlomatic_running_list', 'options');
                            $running = get_option('crawlomatic_running_list', array());
                            if (!empty($running)) 
                            {
                                if (in_array($rule_unique_id, $running)) 
                                {
                                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                    {
                                        crawlomatic_log_to_file('This rule is already running!');
                                    }
                                    return 'nochange';
                                }
                            }
                        }
                    }
                }
            }
            if($use_phantom == '1')
            {
                $phchecked = get_transient('crawlomatic_phantom_check');
                if($phchecked === false)
                {
                    $phantom = crawlomatic_testPhantom();
                    if($phantom === 0)
                    {
                        crawlomatic_log_to_file('PhantomJS not found! Please install it on your server or configure the path to it from plugin\'s \'Main Settings\'.');
                        return 'fail';
                    }
                    elseif($phantom === -1)
                    {
                        crawlomatic_log_to_file('shell_exec is not enabled on your server. Please enable it and retry using this feature of the plugin.');
                        return 'fail';
                    }
                    elseif($phantom === -2)
                    {
                        crawlomatic_log_to_file('shell_exec is not allowed to run on your server (in disable_functions list in php.ini). Please enable it and retry using this feature of the plugin.');
                        return 'fail';
                    }
                    else
                    {
                        set_transient('crawlomatic_phantom_check', '1', 2592000);
                    }
                }
            }
            elseif($use_phantom == '2')
            {
                $phchecked = get_transient('crawlomatic_puppeteer_check');
                if($phchecked === false)
                {
                    $phantom = crawlomatic_testPuppeteer();
                    if($phantom === 0)
                    {
                        crawlomatic_log_to_file('Puppeteer not found! Please install it on your server globally.');
                        return 'fail';
                    }
                    elseif($phantom === -1)
                    {
                        crawlomatic_log_to_file('shell_exec is not enabled on your server. Please enable it and retry using this feature of the plugin.');
                        return 'fail';
                    }
                    elseif($phantom === -2)
                    {
                        crawlomatic_log_to_file('shell_exec is not allowed to run on your server (in disable_functions list in php.ini). Please enable it and retry using this feature of the plugin.');
                        return 'fail';
                    }
                    else
                    {
                        set_transient('crawlomatic_puppeteer_check', '1', 2592000);
                    }
                }
            }
            elseif($use_phantom == '3')
            {
                $phchecked = get_transient('crawlomatic_tor_check');
                if($phchecked === false)
                {
                    $phantom = crawlomatic_testTor();
                    if($phantom === 0)
                    {
                        crawlomatic_log_to_file('Puppeteer not found! Please install it on your server globally (also Tor).');
                        return 'fail';
                    }
                    elseif($phantom === -1)
                    {
                        crawlomatic_log_to_file('shell_exec is not enabled on your server. Please enable it and retry using this feature of the plugin.');
                        return 'fail';
                    }
                    elseif($phantom === -2)
                    {
                        crawlomatic_log_to_file('shell_exec is not allowed to run on your server (in disable_functions list in php.ini). Please enable it and retry using this feature of the plugin.');
                        return 'fail';
                    }
                    else
                    {
                        set_transient('crawlomatic_tor_check', '1', 2592000);
                    }
                }
            }
            if(!is_numeric($max_depth))
            {
                $max_depth = 2;
            }
			if($source_lang == 'disabled')
            {
                $source_lang = 'auto';
            }
            
            $woo_active = false;
            if(!function_exists('is_plugin_active'))
			{
				include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
			}
			if (is_plugin_active('woocommerce/woocommerce.php')) {
				$woo_active = true;
			}
            if($rule_unique_id == '')
            {
                $rule_unique_id = $param;
            }
            if ($found == 0) {
                crawlomatic_log_to_file($param . ' not found in crawlomatic_rules_list!');
                return 'fail';
            } else {
                if($ret_content == 0)
                {
                    $GLOBALS['wp_object_cache']->delete('crawlomatic_rules_list', 'options');
                    $rules = get_option('crawlomatic_rules_list');
                    $rules[$param][3] = crawlomatic_get_date_now();
                    update_option('crawlomatic_rules_list', $rules, false);
                }
            }
            if($ret_content == 0)
            {
                $GLOBALS['wp_object_cache']->delete('crawlomatic_running_list', 'options');
                $running = get_option('crawlomatic_running_list', array());
                if (!empty($running)) {
                    if (in_array($rule_unique_id, $running)) {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                            crawlomatic_log_to_file('Only one instance of this rule is allowed. Rule is already running!');
                        }
                        return 'nochange';
                    }
                }
                $key = time();
                if(!isset($running[$key]))
                {
                    $running[$key] = $rule_unique_id;
                }
                else
                {
                    $running[$key + 1] = $rule_unique_id;
                }
                update_option('crawlomatic_running_list', $running, false);
                register_shutdown_function('crawlomatic_clear_flag_at_shutdown', $rule_unique_id);
            }
            if ($enable_comments == '1') {
                $accept_comments = 'open';
            }
            if($max_paged_depth === '')
            {
                $max_paged_depth = 3;
            }
            if (isset($crawlomatic_Main_Settings['do_not_check_duplicates']) && $crawlomatic_Main_Settings['do_not_check_duplicates'] == 'on') {
                $no_dupl_crawl = false;
            }
            else
            {
                if (isset($crawlomatic_Main_Settings['do_not_crawl_duplicates']) && $crawlomatic_Main_Settings['do_not_crawl_duplicates'] == 'on') {
                    $no_dupl_crawl = true;
                }
                else
                {
                    $no_dupl_crawl = false;
                }
                if (!has_filter('crawlomatic_filter_dup_check'))
                {
                    if($ret_content == 0)
                    {
                        if (!isset($crawlomatic_Main_Settings['title_duplicates']) || $crawlomatic_Main_Settings['title_duplicates'] != 'on') 
                        {
                            $postsPerPage = 50000;
                            $paged = 0;
                            wp_suspend_cache_addition(true);
                            do
                            {
                                $postOffset = $paged * $postsPerPage;
                                $query     = array(
                                    'post_status' => array(
                                        'publish',
                                        'draft',
                                        'pending',
                                        'trash',
                                        'private',
                                        'future'
                                    ),
                                    'post_type' => array(
                                        'any'
                                    ),
                                    'numberposts' => $postsPerPage,
                                    'fields' => 'ids',
                                    'meta_key' => 'crawlomatic_post_url',
                                    'offset'  => $postOffset
                                );
                                $post_list = get_posts($query);
                                foreach ($post_list as $post) {
                                    $orig_url = get_post_meta($post, 'crawlomatic_post_orig_url', true);
                                    if($orig_url == '')
                                    {
                                        $orig_url = get_post_meta($post, 'crawlomatic_post_url', true);
                                    }
                                    $posted_items[$orig_url] = $post;
                                }
                                $paged++;
                            }while(!empty($post_list));
                            wp_suspend_cache_addition(false);
                            unset($post_list);
                        }
                        else
                        {
                            $postsPerPage = 50000;
                            $paged = 0;
                            wp_suspend_cache_addition(true);
                            do
                            {
                                $postOffset = $paged * $postsPerPage;
                                $query     = array(
                                    'post_status' => array(
                                        'publish',
                                        'draft',
                                        'pending',
                                        'trash',
                                        'private',
                                        'future'
                                    ),
                                    'post_type' => array(
                                        'any'
                                    ),
                                    'numberposts' => $postsPerPage,
                                    'fields' => 'ids',
                                    'meta_key' => 'crawlomatic_item_title',
                                    'offset'  => $postOffset
                                );
                                $post_list = get_posts($query);
                                foreach ($post_list as $post) {
                                    $orig_title = get_post_meta($post, 'crawlomatic_item_title', true);
                                    $posted_items[$orig_title] = $post;
                                }
                                $paged++;
                            }while(!empty($post_list));
                            wp_suspend_cache_addition(false);
                            unset($post_list);
                        }
                    }
                }
            }
            if (isset($crawlomatic_Main_Settings['update_existing']) && $crawlomatic_Main_Settings['update_existing'] == 'on') {
                $update_ex = true;
            }
            else
            {
                $update_ex = false;
            }
            if($update_existing == '1')
            {
                $update_ex = true;
            }
            if (isset($crawlomatic_Main_Settings['cat_separator']) && $crawlomatic_Main_Settings['cat_separator'] !== '') {
                if($cat_sep == '')
                {
                    $cat_sep = $crawlomatic_Main_Settings['cat_separator'];
                }
            }
            else
            {
                if($cat_sep == '')
                {
                    $cat_sep = ',';
                }
            }
            if($tag_sep == '')
            {
                $tag_sep = ',';
            }
            if($crawl_exclude != '')
            {
                $crawl_exclude = preg_split('/\r\n|\r|\n/', $crawl_exclude);
                $crawl_exclude = array_map('trim', $crawl_exclude);
            }
            else
            {
                $crawl_exclude = array();
            }
            if($crawl_title_exclude != '')
            {
                $crawl_title_exclude = preg_split('/\r\n|\r|\n/', $crawl_title_exclude);
                $crawl_title_exclude = array_map('trim', $crawl_title_exclude);
            }
            else
            {
                $crawl_title_exclude = array();
            }
            $ids = crawlomatic_replaceSynergyShortcodes($ids);
            if($max_results != '')
            {
                $maximum_crawl = $max_results;
            }
            else
            {
                $maximum_crawl = $max;
            }
            if (isset($crawlomatic_Main_Settings['price_sep']))
            {
                $price_sep = $crawlomatic_Main_Settings['price_sep'];
            }
            else
            {
                $price_sep = '.';
            }
			if($seed_type == 'sitemap')
			{
				require_once (dirname(__FILE__) . "/res/SitemapParser-master/src/SitemapParser/Exceptions/SitemapParserException.php");
				require_once (dirname(__FILE__) . "/res/SitemapParser-master/src/SitemapParser/Exceptions/TransferException.php");
				require_once (dirname(__FILE__) . "/res/SitemapParser-master/src/SitemapParser/UrlParser.php");
				require_once (dirname(__FILE__) . "/res/SitemapParser-master/src/SitemapParser.php");
			}
    
			$ids_arr = preg_split('/\r\n|\r|\n/', trim($ids));
			foreach($ids_arr as $id_el)
			{
				if(count($items) >= $max)
				{
					break;
				}
				if(substr($id_el, 0, 2) === "//")
				{
					$id_el = 'http:' . $id_el;
				}
				$GLOBALS['wp_object_cache']->delete('crawlomatic_continue_search', 'options');
				$skip_posts_temp = get_option('crawlomatic_continue_search', array());
				preg_match_all('{%%counter_(\d+)_(\d+)_(\d+)%%}', $id_el, $counter_matches);
				if (!empty($counter_matches[3])) {
					$run_counter = $counter_matches[1][0];
					do
					{
						$new_ids = preg_replace('{%%counter_(\d+)_(\d+)_(\d+)%%}', $run_counter, $id_el);
						$GLOBALS['crawl_done'] = false;
						$GLOBALS['seed'] = true;
						$items_xtemp = array();
						$items_xtemp = crawlomatic_crawl_page($new_ids, $maximum_crawl, $skip_og, $skip_post_content, $no_external, $required_words, $banned_words, $type, $expre, $title_type, $title_expre, $image_type, $image_expre, $date_type, $date_expre, $cat_type, $cat_expre, intval($max_depth), $custom_cookies, $only_class, $only_id, $no_source, $seed_type, $seed_expre, $crawled_type, $crawled_expre, $paged_crawl_str, $paged_crawl_type, $max_paged_depth, $custom_user_agent, $posted_items, $update_ex, $cat_sep, true, $seed_pag_type, $seed_pag_expre, $price_type, $price_expre, true, $use_proxy, $use_phantom, $no_dupl_crawl, $custom_crawling_expre, $user_pass, $crawl_exclude, $crawl_title_exclude, $price_sep, $encoding, $strip_comma, $reverse_crawl, $lazy_tag, $tag_type, $tag_expre, $tag_sep, $phantom_wait, $param, $continue_search, $author_type, $author_expre, $no_match_query, $post_fields, $request_delay, $require_one, $max_crawl, $check_only_content, $scripter, $local_storage, $download_type, $download_expre, $gallery_type, $gallery_expre);
						if($items_xtemp === false || !is_array($items_xtemp))
						{
							crawlomatic_log_to_file('Failed to get source web page (%%counter_' . $counter_matches[1][0] . '_' . $counter_matches[2][0] . '_' . $counter_matches[3][0] . ')! ' . print_r($items_xtemp, true));
						}
						else
						{
							$items = array_merge($items, $items_xtemp);
						}
						$run_counter += $counter_matches[3][0];
					}
					while(count($items) < $max && $run_counter <= $counter_matches[2][0]);
				}
				else
				{
					$GLOBALS['crawl_done'] = false;
					$GLOBALS['seed'] = true;
					if($continue_search == '1')
					{
						if(isset($skip_posts_temp[$param]))
						{
							if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
								crawlomatic_log_to_file('Loading URL from saved data (continue crawling) (rule ID ' . $param . '): ' . $skip_posts_temp[$param] . '!');
							}
							$id_el = $skip_posts_temp[$param];
						}
					}
					else
					{
						if(isset($skip_posts_temp[$param]))
						{
							unset($skip_posts_temp[$param]);
							update_option('crawlomatic_continue_search', $skip_posts_temp);
						}
					}
					$items_xtemp = array();
					$items_xtemp = crawlomatic_crawl_page($id_el, $maximum_crawl, $skip_og, $skip_post_content, $no_external, $required_words, $banned_words, $type, $expre, $title_type, $title_expre, $image_type, $image_expre, $date_type, $date_expre, $cat_type, $cat_expre, intval($max_depth), $custom_cookies, $only_class, $only_id, $no_source, $seed_type, $seed_expre, $crawled_type, $crawled_expre, $paged_crawl_str, $paged_crawl_type, $max_paged_depth, $custom_user_agent, $posted_items, $update_ex, $cat_sep, true, $seed_pag_type, $seed_pag_expre, $price_type, $price_expre, true, $use_proxy, $use_phantom, $no_dupl_crawl, $custom_crawling_expre, $user_pass, $crawl_exclude, $crawl_title_exclude, $price_sep, $encoding, $strip_comma, $reverse_crawl, $lazy_tag, $tag_type, $tag_expre, $tag_sep, $phantom_wait, $param, $continue_search, $author_type, $author_expre, $no_match_query, $post_fields, $request_delay, $require_one, $max_crawl, $check_only_content, $scripter, $local_storage, $download_type, $download_expre, $gallery_type, $gallery_expre);
					if($items_xtemp === false || !is_array($items_xtemp))
					{
						crawlomatic_log_to_file('Failed to get source web page, importing will not run from this URL! ' . $id_el . ' - ' . print_r($items_xtemp, true));
						if(count($ids_arr) > 1)
						{
							continue;
						}
						else
						{
							if($continue_search == '1' && isset($skip_posts_temp[$param]))
							{
								unset($skip_posts_temp[$param]);
								update_option('crawlomatic_continue_search', $skip_posts_temp);
							}
							return 'fail';
						}
					}
					else
					{
						$items = array_merge($items, $items_xtemp);
					}
				}
			}
            if(count($items) == 0)
            {
                crawlomatic_log_to_file('All crawled posts are already posted or no content found for your query. Rule ID: ' . esc_html($param) . ': ' . $type . ' -- ' . $expre);
                if($continue_search == '1' && isset($skip_posts_temp[$param]))
                {
                    unset($skip_posts_temp[$param]);
                    update_option('crawlomatic_continue_search', $skip_posts_temp);
                }
                return 'nochange';
            }
            $count = 1;
            $init_date = time();
            $skip_pcount = 0;
            $skipped_pcount = 0;
            if($ret_content == 1)
            {
                $item_xcounter = count($items);
                $skip_pcount = rand(0, $item_xcounter-1);
            }
            if(isset($crawlomatic_Main_Settings['attr_text']) && $crawlomatic_Main_Settings['attr_text'] != '')
            {
                $img_attr = $crawlomatic_Main_Settings['attr_text'];
            }
            else
            {
                $img_attr = '';
            }
            if (isset($crawlomatic_Main_Settings['def_user']) && is_numeric($crawlomatic_Main_Settings['def_user'])) {
                $dff_u = $crawlomatic_Main_Settings['def_user'];
            }
            else
            {
                $dff_u = '1';
            }
            foreach ($items as $item) {
                $css_cont = '';
                if($ret_content == 1)
                {
                    if($skip_pcount > $skipped_pcount)
                    {
                        $skipped_pcount++;
                        continue;
                    }
                }
                $item_price_multi = $item['price'];
                if($item_price_multi !== '' && $item_price_multi !== false)
                {
                    if (isset($crawlomatic_Main_Settings['price_multiply']) && $crawlomatic_Main_Settings['price_multiply'] !== '') 
                    {
                        $item_price_multi = round($item_price_multi * $crawlomatic_Main_Settings['price_multiply'], 2);
                    }
                    if (isset($crawlomatic_Main_Settings['price_add']) && $crawlomatic_Main_Settings['price_add'] !== '') 
                    {
                        $item_price_multi = $item_price_multi + $crawlomatic_Main_Settings['price_add'];
                    }
                }
                else
                {
                    $item_price_multi = '';
                }
        
                $img_found = false;
                $update_meta_id = '';
                if ($count > intval($max)) 
                {
                    break;
                }
                $url         = $item['url'];
                $title       = $item['title'];
                $url = preg_replace('{#(.*)}s', '', $url);
                if (!isset($crawlomatic_Main_Settings['title_duplicates']) || $crawlomatic_Main_Settings['title_duplicates'] != 'on') 
                {
                    if(has_filter('crawlomatic_filter_dup_check'))
                    {
                        $continue_filter = false;
                        $continue_filter = apply_filters( 'crawlomatic_filter_dup_check', $url );
                        if($continue_filter === true)
                        {
                            continue;
                        }
                    }
                    else
                    {
                        if (isset($posted_items[$url])) {
                            if ($update_ex == true) {
                                $update_meta_id = $posted_items[$url];
                            }
                            else
                            {
                                continue;
                            }
                        }
                    }
                }
                else
                {
                    if(has_filter('crawlomatic_filter_dup_check'))
                    {
                        $continue_filter = false;
                        $continue_filter = apply_filters( 'crawlomatic_filter_dup_check', $title );
                        if($continue_filter === true)
                        {
                            continue;
                        }
                    }
                    else
                    { 
                        if (isset($posted_items[$title])) {
                            if ($update_ex == true) {
                                $update_meta_id = $posted_items[$title];
                            }
                            else
                            {
                                continue;
                            }
                        }
                    }
                }
                if(isset($crawlomatic_Main_Settings['shortest_api']) && $crawlomatic_Main_Settings['shortest_api'] != '')
                {
                    $short_url = crawlomatic_url_handle($url, $crawlomatic_Main_Settings['shortest_api']);
                }
                else
                {
                    $short_url = $url;
                }
                $content = $item['content'];
                if($limit_content_word_count != '' && is_numeric($limit_content_word_count))
                {
                    $content = wp_trim_words($content, intval($limit_content_word_count), '');
                }
                if (trim($lazy_tag) != '' && trim($lazy_tag) != 'src' && strstr($content, trim($lazy_tag)) !== false) {
                    $lazy_tag = trim($lazy_tag);
                    $lazy_found = false;
                    preg_match_all('{<img .*?>}s', $content, $imgsMatchs);
                    if(isset($imgsMatchs[0]))
                    {
                        $imgsMatchs = $imgsMatchs[0];
                        foreach($imgsMatchs as $imgMatch){
                            if(stristr($imgMatch, $lazy_tag )){
                                $newImg = $imgMatch;
                                $newImg = preg_replace('{ src=".*?"}', '', $newImg);
                                $newImg = str_replace($lazy_tag, 'src', $newImg);   
                                $content = str_replace($imgMatch, $newImg, $content); 
                                $lazy_found = true;     
                            }
                        }
                    }
                    if($lazy_found == false)
                    {
                        $content = str_replace(trim($lazy_tag), 'src', $content); 
                    }
                    preg_match_all('{<iframe .*?>}s', $content, $imgsMatchs);
                    if(isset($imgsMatchs[0]))
                    {
                        $imgsMatchs = $imgsMatchs[0];
                        foreach($imgsMatchs as $imgMatch){
                            if(stristr($imgMatch, $lazy_tag )){
                                $newImg = $imgMatch;
                                $newImg = preg_replace('{ src=["\'].*?[\'"]}', '', $newImg);
                                if(stristr($lazy_tag, 'srcset') !== false)
                                {
                                    $newImg = preg_replace('{\ssrcset=["\'].*?[\'"]}', '', $newImg);
                                    $newImg = str_replace($lazy_tag, 'srcset', $newImg);
                                    preg_match_all('#srcset=[\'"](?:([^"\'\s,]+)\s*(?:\s+\d+[wx])(?:,\s*)?)+["\']#', $newImg, $imgma);
                                    if(isset($imgma[1][0]))
                                    {
                                        $newImg = preg_replace('#<img#', '<img src="' . $imgma[1][0] . '"', $newImg);
                                    }
                                }
                                else
                                {
                                    $newImg = str_replace($lazy_tag, 'src', $newImg); 
                                }
                                $content = str_replace($imgMatch, $newImg, $content);                          
                            }
                        }
                    }
                }
                
                if ((isset($crawlomatic_Main_Settings['strip_content_links']) && $crawlomatic_Main_Settings['strip_content_links'] == 'on') || $strip_links == '1') {
                    $content = crawlomatic_strip_links($content);
                }
                if ((isset($crawlomatic_Main_Settings['strip_internal_content_links']) && $crawlomatic_Main_Settings['strip_internal_content_links'] == 'on')) {
                    $content = crawlomatic_strip_external_links($content, $url);
                }
                if (isset($crawlomatic_Main_Settings['convert_cyrilic']) && $crawlomatic_Main_Settings['convert_cyrilic'] == "on") {
                    $content = crawlomatic_replace_cyrilic($content);
                    $title   = crawlomatic_replace_cyrilic($title);
                }
                if($limit_title_word_count != '' && is_numeric($limit_title_word_count))
                {
                    $title = wp_trim_words($title, intval($limit_title_word_count), '');
                }
                if (isset($crawlomatic_Main_Settings['title_duplicates']) && $crawlomatic_Main_Settings['title_duplicates'] == 'on') 
                {
                    $round_found = false;
                    foreach($post_array as $parr)
                    {
                        if($parr === $item['title'])
                        {
                            $round_found = true;
                            break;
                        }
                    }
                    if($round_found == true)
                    {
                        continue;
                    }
                }
                if (isset($crawlomatic_Main_Settings['strip_scripts']) && $crawlomatic_Main_Settings['strip_scripts'] == 'on') {
                    $content = preg_replace('{<script[\s\S]*?\/\s?script>}s', '', $content);
                    $content = preg_replace('{<ins.*?ins>}s', '', $content);
                    $content = preg_replace('{<ins.*?>}s', '', $content);
                    $content = preg_replace('{\(adsbygoogle.*?\);}s', '', $content);
                }
                $my_url  = parse_url($url);
                $my_host = $my_url['host'];
                preg_match_all('{src[\s]*=[\s]*["|\'](.*?)["|\']}is', $content , $matches);
                $img_srcs =  ($matches[1]);
                $replaced_links_img = array();
                foreach ($img_srcs as $img_src){
                    $original_src = $img_src;
                    $img_src_rel = crawlomatic_fix_single_link($img_src, $url);
                    if($img_src_rel != $img_src)
                    {
                        if(!in_array($img_src, $replaced_links_img))
                        {
                            $replaced_links_img[] = $img_src;
                            $content = str_replace($img_src, $img_src_rel, $content);
                        }
                    }
                }
                $content = preg_replace('{\ssrcset=".*?"}', ' ', $content);
                $content = preg_replace('{\ssizes=".*?"}', ' ', $content);
                $content = html_entity_decode($content, ENT_NOQUOTES | ENT_HTML5) ;
                if($check_only_content == '1')
                {
                    if($required_words != '')
                    {
                        $required_found = false;
                        $req_list = explode(',', $required_words);
                        if($require_one == '1')
                        {
                            $required_found = false;
                            foreach($req_list as $rl)
                            {
                                if(function_exists('mb_stristr'))
                                {
                                    if(mb_stristr($content, $rl) !== false)
                                    {
                                        $required_found = true;
                                        break;
                                    }
                                }
                                else
                                {
                                    if(stristr($content, $rl) === false)
                                    {
                                        $required_found = true;
                                        break;
                                    }
                                }
                            }
                            if($required_found === false)
                            {
                                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                {
                                    crawlomatic_log_to_file('No required word found (content), skipping: ' . $url); 
                                }
                                continue;
                            }
                        }
                        else
                        {
                            foreach($req_list as $rl)
                            {
                                if(function_exists('mb_stristr'))
                                {
                                    if(mb_stristr($content, $rl) === false)
                                    {
                                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                        {
                                            crawlomatic_log_to_file('Required word not found (content), skipping: ' . $url); 
                                        }
                                        continue;
                                    }
                                }
                                else
                                {
                                    if(stristr($content, $rl) === false)
                                    {
                                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                        {
                                            crawlomatic_log_to_file('Required word not found (content), skipping: ' . $url); 
                                        }
                                        continue;
                                    }
                                }
                            }
                        }
                    }
                    if($banned_words != '')
                    {
                        $ban_list = explode(',', $banned_words);
                        foreach($ban_list as $bl)
                        {
                            if(function_exists('mb_stristr'))
                            {
                                if(mb_stristr($content, $bl) !== false)
                                {
                                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                    {
                                        crawlomatic_log_to_file('Banned word detected (content), skipping it\'s importing: ' . $url); 
                                    }
                                    continue;
                                }
                            }
                            else
                            {
                                if(stristr($content, $bl) !== false)
                                {
                                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                    {
                                        crawlomatic_log_to_file('Banned word detected (content), skipping it\'s importing: ' . $url); 
                                    }
                                    continue;
                                }
                            }
                        }
                    }
                }
                if ($get_css == '1') {
                    add_action('wp_enqueue_scripts', 'crawlomatic_wp_custom_css_files', 10, 2);
                    $htmlcontent = crawlomatic_get_web_page($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, '', '', $request_delay);
                    
                    if ($htmlcontent !== FALSE) {
                        preg_match_all('/"([^"]+?\.css)"/', $htmlcontent, $matches);
                        $matches = $matches[0];
                        $matches = array_unique($matches);
                        $cont    = 0;
                        foreach ($matches as $match) {
                            $match = trim(htmlspecialchars_decode($match), '"');
                            if (!crawlomatic_url_exists($match, $use_proxy, $crawlomatic_Main_Settings, $custom_user_agent, $custom_cookies, $user_pass)) {
                                $tmp_match = 'http:' . $match;
                                if (!crawlomatic_url_exists($tmp_match, $use_proxy, $crawlomatic_Main_Settings, $custom_user_agent, $custom_cookies, $user_pass)) {
                                    $parts = explode('/', $url);
                                    $dir   = '';
                                    for ($i = 0; $i < count($parts) - 1; $i++) {
                                        $dir .= $parts[$i] . "/";
                                    }
                                    $tmp_match = $dir . trim($match, '/');
                                    if (!crawlomatic_url_exists($tmp_match, $use_proxy, $crawlomatic_Main_Settings, $custom_user_agent, $custom_cookies, $user_pass)) {
                                        continue;
                                    } else {
                                        $match = $tmp_match;
                                    }
                                } else {
                                    $match = $tmp_match;
                                }
                            }
                            
                            $css_temp = crawlomatic_get_web_page($match, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, '', '', $request_delay);
                            if ($css_temp === FALSE) {
                                continue;
                            }
                            $css_cont .= wp_strip_all_tags($css_temp) . ' ';
                        }
                    }
                }
                $description = crawlomatic_getExcerpt($content);
                if($item['crawled_date'] === true)
                {
                    $date = $item['date'];
                }
                else
                {
                    $postdatex = gmdate("Y-m-d H:i:s", intval($init_date));
                    $date = $postdatex;
                    $init_date = $init_date - 1;
                }
                if($date_index != '')
                {
                    $old_d = strtotime($date);
                    if($old_d !== false)
                    {
                        $newtime = $old_d + ($date_index * 60 * 60);
                        $date = date("Y-m-d H:i:s", $newtime);
                    }
                }
                if (isset($crawlomatic_Main_Settings['skip_old']) && $crawlomatic_Main_Settings['skip_old'] == 'on' && isset($crawlomatic_Main_Settings['skip_year']) && $crawlomatic_Main_Settings['skip_year'] !== '' && isset($crawlomatic_Main_Settings['skip_month']) && isset($crawlomatic_Main_Settings['skip_day'])) {
                    $old_date      = $crawlomatic_Main_Settings['skip_day'] . '-' . $crawlomatic_Main_Settings['skip_month'] . '-' . $crawlomatic_Main_Settings['skip_year'];
                    $time_date     = strtotime($date);
                    $time_old_date = strtotime($old_date);
                    if ($time_date !== false && $time_old_date !== false) {
                        if ($time_date < $time_old_date) {
                            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                                crawlomatic_log_to_file('Skipping post "' . esc_html($title) . '", because it is older than ' . $old_date . ' - posted on ' . $date);
                            }
                            continue;
                        }
                    }
                }
                $extra_categories = '';
                if(is_array($item['categories']))
                {
                    foreach ($item['categories'] as $category)
                    {
                        $extra_categories .= $category . ',';
                    }
                    $extra_categories = trim($extra_categories, ',');
                }
                if (isset($crawlomatic_Main_Settings['convert_cyrilic']) && $crawlomatic_Main_Settings['convert_cyrilic'] == "on") {
                    $extra_categories   = crawlomatic_replace_cyrilic($extra_categories);
                }
                $my_post                          = array();
                $my_post['update_meta_id']        = $update_meta_id;
                $my_post['crawlomatic_enable_pingbacks'] = $enable_pingback;
                $my_post['post_type']             = $post_type;
                $my_post['comment_status']        = $accept_comments;
                if (isset($crawlomatic_Main_Settings['draft_first']) && $crawlomatic_Main_Settings['draft_first'] == 'on')
                {
                    if($post_status == 'publish')
                    {
                        $draft_me = true;
                        $my_post['post_status'] = 'draft';
                    }
                    else
                    {
                        $my_post['post_status']   = $post_status;
                    }
                }
                else
                {
                    $my_post['post_status'] = $post_status;
                }
                
                if($post_user_name == 'rnd-crawlomatic')
                {
                    $randid = crawlomatic_display_random_user();
                    if($randid === false)
                    {
                        $post_user_set               = $dff_u;
                    }
                    else
                    {
                        $post_user_set               = $randid->ID;
                    }
                    $my_post['post_author']           = $post_user_set;
                }
                elseif($post_user_name == 'feed-crawlomatic')
                {
                    if($item['author'] != '')
                    {
                        if(username_exists( sanitize_title($item['author']) ))
                        {
                            $user_id_t = get_user_by('login', sanitize_title($item['author']));
                            if($user_id_t)
                            {
                                $post_user_set = $user_id_t->ID;
                            }
                            else
                            {
                                $post_user_set = $dff_u;
                            }
                        }
                        else
                        {
                            $curr_id = wp_create_user(sanitize_title($item['author']), 'Crawlomatic_Scrap3r!', crawlomatic_generate_random_email());
                            if ( is_int($curr_id) )
                            {
                                $u = new WP_User($curr_id);
                                $u->remove_role('subscriber');
                                $u->add_role('editor');
                                $post_user_set               = $curr_id;
                            }
                            else
                            {
                                $post_user_set               = $dff_u;
                            }
                        }
                    }
                    else
                    {
                        $post_user_set               = $dff_u;
                    }
                    $my_post['post_author']           = $post_user_set;
                }
                else
                {
                    $my_post['post_author']           = $post_user_name;
                }
                $item_tags = '';
                if(is_array($item['tags']))
                {
                    foreach ($item['tags'] as $xtag)
                    {
                        $item_tags .= $xtag . ',';
                    }
                    $item_tags = trim($item_tags, ',');
                }
                $item_download = '';
                $my_post['post_gallery'] = $item['gallery'];
                if(!empty($item['download_remote']))
                {
                    $item_download = $item['download_remote'];
                    $my_post['download_local'] = $item['download_local'];
                }
                else
                {
                    $my_post['download_local'] = '';
                }
                if (isset($crawlomatic_Main_Settings['convert_cyrilic']) && $crawlomatic_Main_Settings['convert_cyrilic'] == "on") {
                    $item_tags   = crawlomatic_replace_cyrilic($item_tags);
                }
                if ($can_create_tag == '1') {
                    $my_post['tags_input'] = ($item_create_tag != '' ? $item_create_tag . ',' : '') . $item_tags;
                } else if ($item_create_tag != '') {
                    $my_post['tags_input'] = $item_create_tag;
                }
                $orig_content = '';
                $my_post['crawlomatic_post_url']  = $short_url;
                $my_post['crawlomatic_post_orig_url']  = $url;
                $my_post['crawlomatic_post_date'] = $date;
                if($royalty_free == '1')
                {
                    $keyword_class = new Crawlomatic_keywords();
                    $query_words = $keyword_class->keywords($title, 2);
                    $get_img = crawlomatic_get_free_image($crawlomatic_Main_Settings, $query_words, $img_attr, 10);
                    if($get_img == '' || $get_img === false)
                    {
                        if(isset($crawlomatic_Main_Settings['bimage']) && $crawlomatic_Main_Settings['bimage'] == 'on')
                        {
                            $query_words = $keyword_class->keywords($title, 1);
                            $get_img = crawlomatic_get_free_image($crawlomatic_Main_Settings, $query_words, $img_attr, 20);
                            if($get_img == '' || $get_img === false)
                            {
                                if(isset($crawlomatic_Main_Settings['no_orig']) && $crawlomatic_Main_Settings['no_orig'] == 'on')
                                {
                                    $get_img = '';
                                }
                                else
                                {
                                    $get_img = $item['image'];
                                }
                            }
                        }
                        else
                        {
                            if(isset($crawlomatic_Main_Settings['no_orig']) && $crawlomatic_Main_Settings['no_orig'] == 'on')
                            {
                                $get_img = '';
                            }
                            else
                            {
                                $get_img = $item['image'];
                            }
                        }
                    }
                }
                else
                {
                    $get_img = $item['image'];
                }
                
                if($get_img != '')
                {
                    $img_found = true;
                    $get_img = crawlomatic_fix_single_link($get_img, $url);
                }
                if (isset($crawlomatic_Main_Settings['skip_image_names']) && $crawlomatic_Main_Settings['skip_image_names'] != '' && $get_img != '') 
                {
                    $need_to_continue = false;
                    $skip_images = explode(',', $crawlomatic_Main_Settings['skip_image_names']);
                    foreach($skip_images as $ski)
                    {
                        if(crawlomatic_stringMatchWithWildcard($get_img, trim($ski)))
                        {
                            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                                crawlomatic_log_to_file('Skipping post "' . esc_html($title) . '", because it has excluded image name: ' . $get_img . ' - ' . $ski);
                            }
                            $need_to_continue = true;
                            break;
                        }
                    }
                    if($need_to_continue == true)
                    {
                        continue;
                    }
                }
                if ($featured_image == '1' && ($skip_no_image == '1' || (isset($crawlomatic_Main_Settings['skip_no_img']) && $crawlomatic_Main_Settings['skip_no_img'] == 'on')) && $img_found == false) {
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                        crawlomatic_log_to_file('Skipping post "' . esc_html($title) . '", because it has no detected image file attached');
                    }
                    continue;
                }
                if(substr($get_img, 0, 2) === "//")
                {
                    if(substr($url, 0, 5) === "https")
                    {
                        $get_img = 'https:' . $get_img;
                    }
                    else
                    {
                        $get_img = 'http:' . $get_img;
                    }
                }
                if ($strip_by_id != '') {
                    require_once (dirname(__FILE__) . "/res/simple_html_dom.php");
                    $strip_list = explode(',', $strip_by_id);
                    $html_dom_original_html = crawlomatic_str_get_html($content);
                    if($html_dom_original_html !== false && method_exists($html_dom_original_html, 'find')){
                        foreach ($strip_list as $strip_id) {
                            $ret = $html_dom_original_html->find('*[id="'.trim($strip_id).'"]');
                            foreach ($ret as $itm ) {
                                $itm->outertext = '' ;
                            }
                        }
                        $content = $html_dom_original_html->save();
                        $html_dom_original_html->clear();
                        unset($html_dom_original_html);
                    }else{
                        foreach ($strip_list as $strip_id) {
                            if(trim($strip_id) == '')
                            {
                                continue;
                            }
                            $content_r = crawlomatic_removeTagByID($content, trim($strip_id));
                            if($content_r !== false)
                            {
                                $content = $content_r;
                            }
                        }
                    }
                }
                if ($strip_by_class != '') {
                    require_once (dirname(__FILE__) . "/res/simple_html_dom.php");
                    $strip_list = explode(',', $strip_by_class);
                    $html_dom_original_html = crawlomatic_str_get_html($content);
                    if($html_dom_original_html !== false && method_exists($html_dom_original_html, 'find')){
                        foreach ($strip_list as $strip_class) {
                            if(trim($strip_class) == '')
                            {
                                continue;
                            }
                            $ret = $html_dom_original_html->find('*[class="'.trim($strip_class).'"]');
                            foreach ($ret as $itm ) {
                                $itm->outertext = '' ;
                            }
                        }
                        $content = $html_dom_original_html->save();
                        $html_dom_original_html->clear();
                        unset($html_dom_original_html);
                    }else{
                        foreach ($strip_list as $strip_class) {
                            if(trim($strip_class) == '')
                            {
                                continue;
                            }
                            $content_r = crawlomatic_removeTagByClass($content, trim($strip_class));
                            if($content_r !== false)
                            {
                                $content = $content_r;
                            }
                        }
                    }
                }
                if ($strip_by_xpath != '') {
                    require_once (dirname(__FILE__) . "/res/simple_html_dom.php");
                    $strip_by_xpath = preg_split('/\r\n|\r|\n/', $strip_by_xpath);
                    foreach($strip_by_xpath as $fxx)
                    {
                        $html_dom_original_html = crawlomatic_str_get_html($content);
                        if($html_dom_original_html !== false && method_exists($html_dom_original_html, 'find')){
                            $ret = $html_dom_original_html->find($fxx);
                            foreach ($ret as $itm ) {
                                $itm->outertext = '' ;
                            }
                            $content = $html_dom_original_html->save();
                            $html_dom_original_html->clear();
                            unset($html_dom_original_html);
                        }else{
                            $content_r = crawlomatic_removeTagByXPath($content, trim($fxx));
                            if($content_r !== false)
                            {
                                $content = $content_r;
                            }
                        }
                    }
                }
                if ($strip_html_by_xpath != '') {
                    $strip_html_by_xpath = preg_split('/\r\n|\r|\n/', $strip_html_by_xpath);
                    require_once (dirname(__FILE__) . "/res/simple_html_dom.php");
                    foreach($strip_html_by_xpath as $fx)
                    {
                        $html_dom_original_html = crawlomatic_str_get_html($content);
                        if($html_dom_original_html !== false && method_exists($html_dom_original_html, 'find')){
                            $ret = $html_dom_original_html->find($fx);
                            foreach ($ret as $itm ) {
                                $itm->outertext = strip_tags($itm->outertext) ;
                            }
                            $content = $html_dom_original_html->save();
                            $html_dom_original_html->clear();
                            unset($html_dom_original_html);
                        }else{
                            $content_r = crawlomatic_removeHTMLByXPath($content, trim($fx));
                            if($content_r !== false)
                            {
                                $content = $content_r;
                            }
                        }
                    }
                }
                if ($strip_by_tag != '') {
                    require_once (dirname(__FILE__) . "/res/simple_html_dom.php");
                    $strip_list = explode(',', $strip_by_tag);
                    $html_dom_original_html = crawlomatic_str_get_html($content);
                    if($html_dom_original_html !== false && method_exists($html_dom_original_html, 'find')){
                        foreach ($strip_list as $strip_tag) {
                            $strip_tag = trim($strip_tag);
                            if($strip_tag != '')
                            {
                                $ret = $html_dom_original_html->find($strip_tag);
                                foreach ($ret as $itm ) {
                                    $itm->outertext = '' ;
                                }
                            }
                        }
                        $content = $html_dom_original_html->save();
                        $html_dom_original_html->clear();
                        unset($html_dom_original_html);
                    }else{
                        foreach ($strip_list as $strip_tag) {
                            if(trim($strip_tag) == '')
                            {
                                continue;
                            }
                            $content_r = crawlomatic_removeTagByTag($content, trim($strip_tag));
                            if($content_r !== false)
                            {
                                $content = $content_r;
                            }
                        }
                    }
                }
                if ($only_text == '1') {
                    $content = crawlomatic_strip_html_tags($content, $allow_html_tags);
                } 
                $content = crawlomatic_fix_links($content, $url);
                $postdate = strtotime($date);
                if($postdate !== FALSE)
                {
                    $postdate = gmdate("Y-m-d H:i:s", intval($postdate));
                }
                if($postdate !== FALSE)
                {
                    if($item['crawled_date'] === true)
                    {
                        $my_post['post_date_gmt'] = $postdate;
                    }
                    else
                    {
                        $my_post['post_date_gmt'] = $postdate;
                    }
                }
                if(isset($item['custom_shortcodes']) && is_array($item['custom_shortcodes']))
                {
                    $custom_shortcodes_arr = $item['custom_shortcodes'];
                }
                else
                {
                    $custom_shortcodes_arr = array();
                }
                if($postdate === false)
                {
                    $postdate = $date;
                }
                if($content_percent != '' && is_numeric($content_percent))
                {
                    $temp_t = crawlomatic_strip_html_tags($content);
                    $temp_t = str_replace('&nbsp;',"",$temp_t);
                    $ccount = str_word_count($temp_t);
                    if($ccount > 10)
                    {
                        $str_count = strlen($content);
                        $leave_cont = round($str_count * $content_percent / 100);
                        $content = crawlomatic_substr_close_tags($content, $leave_cont);
                    }
                    else
                    {
                        $ccount = crawlomatic_count_unicode_words($temp_t);
                        if($ccount > 10)
                        {
                            $str_count = strlen($content);
                            $leave_cont = round($str_count * $content_percent / 100);
                            $content = crawlomatic_substr_close_tags($content, $leave_cont);
                        }
                    }
                }
                $screenimageURL = '';
                $screens_attach_id = '';
                if(isset($item['screen_image']) && $item['screen_image'] != '')
                {
                    if($attach_screen == '1' || (strstr($post_content, '%%item_show_screenshot%%') !== false || strstr($post_content, '%%item_screenshot_url%%') !== false || strstr($custom_fields, '%%item_show_screenshot%%') !== false || strstr($custom_fields, '%%item_screenshot_url%%') !== false || strstr($custom_tax, '%%item_show_screenshot%%') !== false || strstr($custom_tax, '%%item_screenshot_url%%') !== false))
                    {
                        $screenimageURL = $item['screen_image'];
                    }
                }
                else
                {
                    if (isset($crawlomatic_Main_Settings['headless_screen']) && $crawlomatic_Main_Settings['headless_screen'] == 'on')
                    {
                        if($attach_screen == '1' || (strstr($post_content, '%%item_show_screenshot%%') !== false || strstr($post_content, '%%item_screenshot_url%%') !== false || strstr($custom_fields, '%%item_show_screenshot%%') !== false || strstr($custom_fields, '%%item_screenshot_url%%') !== false || strstr($custom_tax, '%%item_show_screenshot%%') !== false || strstr($custom_tax, '%%item_screenshot_url%%') !== false))
                        {
                            if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') 
                            {
                                $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
                                $randomness = array_rand($prx);
                                $phantomjs_comm .= '--proxy=' . trim($prx[$randomness]) . ' ';
                                if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
                                {
                                    $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
                                    if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
                                    {
                                        $phantomjs_comm .= '--proxy-auth=' . trim($prx_auth[$randomness]) . ' ';
                                    }
                                }
                            }
                            if($custom_user_agent == '')
                            {
                                $custom_user_agent = 'default';
                            }
                            if($custom_cookies == '')
                            {
                                $custom_cookies = 'default';
                            }
                            if($user_pass == '')
                            {
                                $user_pass = 'default';
                            }
                            if (isset($crawlomatic_Main_Settings['screenshot_height']) && $crawlomatic_Main_Settings['screenshot_height'] != '') 
                            {
                                $h = esc_attr($crawlomatic_Main_Settings['screenshot_height']);
                            }
                            else
                            {
                                $h = '0';
                            }
                            if (isset($crawlomatic_Main_Settings['screenshot_width']) && $crawlomatic_Main_Settings['screenshot_width'] != '') 
                            {
                                $w = esc_attr($crawlomatic_Main_Settings['screenshot_width']);
                            }
                            else
                            {
                                $w = '1920';
                            }
                            $screenshotimg = crawlomatic_get_screenshot_PuppeteerAPI($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, '', $request_delay, $scripter, $local_storage, $h, $w);
                            if($screenshotimg !== false)
                            {
                                $upload_dir = wp_upload_dir();
                                $dir_name   = $upload_dir['basedir'] . '/crawlomatic-files';
                                $dir_url    = $upload_dir['baseurl'] . '/crawlomatic-files';
                                global $wp_filesystem;
                                if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
                                    include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
                                    wp_filesystem($creds);
                                }
                                if (!$wp_filesystem->exists($dir_name)) {
                                    wp_mkdir_p($dir_name);
                                }
                                $screen_name = uniqid();
                                $screenimageName = $dir_name . '/' . $screen_name . '.jpg';
                                $screenimageURL = $dir_url . '/' . $screen_name . '.jpg';
                                
                                $is_fail = $wp_filesystem->put_contents($screenimageName, $screenshotimg);
                                if($is_fail === false)
                                {
                                    crawlomatic_log_to_file('Error in writing screenshot to file: ' . $screenimageName);
                                }
                                else
                                {
                                    $wp_filetype = wp_check_filetype( $screen_name . '.jpg', null );
                                    $attachment = array(
                                        'post_mime_type' => $wp_filetype['type'],
                                        'post_title' => sanitize_file_name( $screen_name . '.jpg' ),
                                        'post_content' => '',
                                        'post_status' => 'inherit'
                                    );
                                    $screens_attach_id = wp_insert_attachment($attachment, $screenimageName);
                                    require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                    require_once( ABSPATH . 'wp-admin/includes/media.php' );
                                    $attach_data = wp_generate_attachment_metadata($screens_attach_id, $screenimageName);
                                    wp_update_attachment_metadata( $screens_attach_id, $attach_data );
                                }
                            }
                        }
                    }
                    elseif (isset($crawlomatic_Main_Settings['phantom_screen']) && $crawlomatic_Main_Settings['phantom_screen'] == 'on')
                    {
                        if($attach_screen == '1' || (strstr($post_content, '%%item_show_screenshot%%') !== false || strstr($post_content, '%%item_screenshot_url%%') !== false || strstr($custom_fields, '%%item_show_screenshot%%') !== false || strstr($custom_fields, '%%item_screenshot_url%%') !== false || strstr($custom_tax, '%%item_show_screenshot%%') !== false || strstr($custom_tax, '%%item_screenshot_url%%') !== false))
                        {
                            if(function_exists('shell_exec')) 
                            {
                                $disabled = explode(',', ini_get('disable_functions'));
                                if(!in_array('shell_exec', $disabled))
                                {
                                    if (isset($crawlomatic_Main_Settings['phantom_path']) && $crawlomatic_Main_Settings['phantom_path'] != '') 
                                    {
                                        $phantomjs_comm = $crawlomatic_Main_Settings['phantom_path'] . ' ';
                                    }
                                    else
                                    {
                                        $phantomjs_comm = 'phantomjs ';
                                    }
                                    if (isset($crawlomatic_Main_Settings['screenshot_height']) && $crawlomatic_Main_Settings['screenshot_height'] != '') 
                                    {
                                        $h = esc_attr($crawlomatic_Main_Settings['screenshot_height']);
                                    }
                                    else
                                    {
                                        $h = '0';
                                    }
                                    if (isset($crawlomatic_Main_Settings['screenshot_width']) && $crawlomatic_Main_Settings['screenshot_width'] != '') 
                                    {
                                        $w = esc_attr($crawlomatic_Main_Settings['screenshot_width']);
                                    }
                                    else
                                    {
                                        $w = '1920';
                                    }
                                    $upload_dir = wp_upload_dir();
                                    $dir_name   = $upload_dir['basedir'] . '/crawlomatic-files';
                                    $dir_url    = $upload_dir['baseurl'] . '/crawlomatic-files';
                                    global $wp_filesystem;
                                    if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
                                        include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
                                        wp_filesystem($creds);
                                    }
                                    if (!$wp_filesystem->exists($dir_name)) {
                                        wp_mkdir_p($dir_name);
                                    }
                                    $screen_name = uniqid();
                                    $screenimageName = $dir_name . '/' . $screen_name;
                                    $screenimageURL = $dir_url . '/' . $screen_name . '.jpg';
                                    if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') 
                                    {
                                        $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
                                        $randomness = array_rand($prx);
                                        $phantomjs_comm .= '--proxy=' . trim($prx[$randomness]) . ' ';
                                        if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
                                        {
                                            $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
                                            if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
                                            {
                                                $phantomjs_comm .= '--proxy-auth=' . trim($prx_auth[$randomness]) . ' ';
                                            }
                                        }
                                    }
                                    if($custom_user_agent == '')
                                    {
                                        $custom_user_agent = 'default';
                                    }
                                    if($custom_cookies == '')
                                    {
                                        $custom_cookies = 'default';
                                    }
                                    if($user_pass == '')
                                    {
                                        $user_pass = 'default';
                                    }
                                    $cmdResult = shell_exec($phantomjs_comm . '"' . dirname(__FILE__) .'/res/phantomjs/phantom-screenshot.js"' . ' "'. dirname(__FILE__) . '" "' . $url . '" "' . $screenimageName . '" ' . $w . ' ' . $h . '  "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '" 2>&1');
                                    if($cmdResult === NULL || $cmdResult == '' || trim($cmdResult) === 'timeout' || stristr($cmdResult, 'sh: phantomjs: command not found') !== false)
                                    {
                                        $screenimageURL = '';
                                        crawlomatic_log_to_file('Error in phantomjs screenshot: exec: ' . $phantomjs_comm . '"' . dirname(__FILE__) .'/res/phantomjs/phantom-screenshot.js"' . ' "'. dirname(__FILE__) . '" "' . $url . '" "' . $screenimageName . '" ' . $w . ' ' . $h . ' "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '" , reterr: ' . $cmdResult);
                                    }
                                    else
                                    {
                                        if($wp_filesystem->exists($screenimageName))
                                        {
                                            $wp_filetype = wp_check_filetype( $screen_name . '.jpg', null );
                                            $attachment = array(
                                            'post_mime_type' => $wp_filetype['type'],
                                            'post_title' => sanitize_file_name( $screen_name . '.jpg' ),
                                            'post_content' => '',
                                            'post_status' => 'inherit'
                                            );
                                            $screens_attach_id = wp_insert_attachment( $attachment, $screenimageName . '.jpg' );
                                            require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                            require_once( ABSPATH . 'wp-admin/includes/media.php' );
                                            $attach_data = wp_generate_attachment_metadata( $screens_attach_id, $screenimageName . '.jpg' );
                                            wp_update_attachment_metadata( $screens_attach_id, $attach_data );
                                        }
                                        else
                                        {
                                            crawlomatic_log_to_file('Error in phantomjs screenshot not found: exec: ' . $phantomjs_comm . '"' . dirname(__FILE__) .'/res/phantomjs/phantom-screenshot.js"' . ' "'. dirname(__FILE__) . '" "' . $url . '" "' . $screenimageName . '" ' . $w . ' ' . $h . ' "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '" , reterr: ' . $cmdResult);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    elseif (isset($crawlomatic_Main_Settings['puppeteer_screen']) && $crawlomatic_Main_Settings['puppeteer_screen'] == 'on')
                    {
                        if($attach_screen == '1' || (strstr($post_content, '%%item_show_screenshot%%') !== false || strstr($post_content, '%%item_screenshot_url%%') !== false || strstr($custom_fields, '%%item_show_screenshot%%') !== false || strstr($custom_fields, '%%item_screenshot_url%%') !== false || strstr($custom_tax, '%%item_show_screenshot%%') !== false || strstr($custom_tax, '%%item_screenshot_url%%') !== false))
                        {
                            if(function_exists('shell_exec')) 
                            {
                                $disabled = explode(',', ini_get('disable_functions'));
                                if(!in_array('shell_exec', $disabled))
                                {
                                    $phantomjs_comm = 'node ';
                                    if (isset($crawlomatic_Main_Settings['screenshot_height']) && $crawlomatic_Main_Settings['screenshot_height'] != '') 
                                    {
                                        $h = esc_attr($crawlomatic_Main_Settings['screenshot_height']);
                                    }
                                    else
                                    {
                                        $h = '0';
                                    }
                                    if (isset($crawlomatic_Main_Settings['screenshot_width']) && $crawlomatic_Main_Settings['screenshot_width'] != '') 
                                    {
                                        $w = esc_attr($crawlomatic_Main_Settings['screenshot_width']);
                                    }
                                    else
                                    {
                                        $w = '1920';
                                    }
                                    if (isset($crawlomatic_Main_Settings['phantom_timeout']) && $crawlomatic_Main_Settings['phantom_timeout'] != '') 
                                    {
                                        $phantomjs_timeout = ((int)$crawlomatic_Main_Settings['phantom_timeout']);
                                    }
                                    else
                                    {
                                        $phantomjs_timeout = 'default';
                                    }
                                    if ($w < 350) {
                                        $w = 350;
                                    }
                                    if ($w > 1920) {
                                        $w = 1920;
                                    }
                                    $upload_dir = wp_upload_dir();
                                    $dir_name   = $upload_dir['basedir'] . '/crawlomatic-files';
                                    $dir_url    = $upload_dir['baseurl'] . '/crawlomatic-files';
                                    global $wp_filesystem;
                                    if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
                                        include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
                                        wp_filesystem($creds);
                                    }
                                    if (!$wp_filesystem->exists($dir_name)) {
                                        wp_mkdir_p($dir_name);
                                    }
                                    $screen_name = uniqid();
                                    $screenimageName = $dir_name . '/' . $screen_name . '.jpg';
                                    $screenimageURL = $dir_url . '/' . $screen_name . '.jpg';
                                    $phantomjs_proxcomm = '"null"';
                                    if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') 
                                    {
                                        $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
                                        $randomness = array_rand($prx);
                                        $phantomjs_proxcomm = '"' . trim($prx[$randomness]);
                                        if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
                                        {
                                            $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
                                            if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
                                            {
                                                $phantomjs_proxcomm .= ':' . trim($prx_auth[$randomness]);
                                            }
                                        }
                                        $phantomjs_proxcomm .= '"';
                                    }
                                    if($custom_user_agent == '')
                                    {
                                        $custom_user_agent = 'default';
                                    }
                                    if($custom_cookies == '')
                                    {
                                        $custom_cookies = 'default';
                                    }
                                    if($user_pass == '')
                                    {
                                        $user_pass = 'default';
                                    }
                                    $cmdResult = shell_exec($phantomjs_comm . '"' . dirname(__FILE__) .'/res/puppeteer/screenshot.js"' . ' "' . $url . '" "' . $screenimageName . '" ' . $w . ' ' . $h . ' ' . $phantomjs_proxcomm . '  "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . $phantomjs_timeout . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '" 2>&1');
                                    if(stristr($cmdResult, 'sh: node: command not found') !== false || stristr($cmdResult, 'throw err;') !== false)
                                    {
                                        $screenimageURL = '';
                                        crawlomatic_log_to_file('Error in puppeteer screenshot: exec: ' . $phantomjs_comm . '"' . dirname(__FILE__) .'/res/puppeteer/screenshot.js"' . ' "' . $url . '" "' . $screenimageName . '" ' . $w . ' ' . $h . ' ' . $phantomjs_proxcomm . '  "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . $phantomjs_timeout . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '", reterr: ' . $cmdResult);
                                    }
                                    else
                                    {
                                        if($wp_filesystem->exists($screenimageName))
                                        {
                                            $wp_filetype = wp_check_filetype( $screen_name . '.jpg', null );
                                            $attachment = array(
                                            'post_mime_type' => $wp_filetype['type'],
                                            'post_title' => sanitize_file_name( $screen_name . '.jpg' ),
                                            'post_content' => '',
                                            'post_status' => 'inherit'
                                            );
                                            $screens_attach_id = wp_insert_attachment( $attachment, $screenimageName);
                                            require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                            require_once( ABSPATH . 'wp-admin/includes/media.php' );
                                            $attach_data = wp_generate_attachment_metadata( $screens_attach_id, $screenimageName);
                                            wp_update_attachment_metadata( $screens_attach_id, $attach_data );
                                        }
                                        else
                                        {
                                            crawlomatic_log_to_file('Error in puppeteer screenshot not found: exec: ' . $phantomjs_comm . '"' . dirname(__FILE__) .'/res/puppeteer/screenshot.js"' . ' "' . $url . '" "' . $screenimageName . '" ' . $w . ' ' . $h . ' ' . $phantomjs_proxcomm . '  "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '"  "' . $phantomjs_timeout . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '", reterr: ' . $cmdResult);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                if ($limit_word_count !== "") {
                    $content = crawlomatic_custom_wp_trim_excerpt($content, $limit_word_count, $short_url, $read_more);
                }
                if (strpos($post_content, '%%') !== false) {
                    $new_post_content = crawlomatic_replaceContentShortcodes($post_content, $title, $content, $short_url, $extra_categories, $item_tags, $get_img, $description, $read_more, $postdate, $item['price'], $item_price_multi, $custom_shortcodes_arr, $img_attr, $screenimageURL, $append_urls, $item_download);
                } else {
                    $new_post_content = $post_content;
                }
                if (strpos($post_title, '%%') !== false) {
                    $new_post_title = crawlomatic_replaceTitleShortcodes($post_title, $title, $content, $short_url, $extra_categories, $item_tags, $custom_shortcodes_arr);
                } else {
                    $new_post_title = $post_title;
                }
                if(trim($replace_words) != '')
                {
                    $replace_arr = explode(',', trim($replace_words));
                    $replace_arr = array_map('trim', $replace_arr);
                    foreach($replace_arr as $rex)
                    {
                        $repla_parts = explode('|', $rex);
                        if(!isset($repla_parts[1]))
                        {
                            continue;
                        }
                        $new_post_content = str_replace($repla_parts[0], $repla_parts[1], $new_post_content);
                    }
                }
                $my_post['screen_attach']    = $screens_attach_id;
                $my_post['extra_categories'] = $extra_categories;
                $my_post['extra_tags']       = $item_tags;
                $my_post['description']      = $description;
                $arr                         = crawlomatic_spin_and_translate($new_post_title, $new_post_content, $translate, $source_lang, $use_proxy, $no_spin);
                $new_post_title              = $arr[0];
                $new_post_content            = $arr[1];
                $new_post_title              = html_entity_decode($new_post_title);
                $new_post_content            = html_entity_decode($new_post_content);
                $title_count = -1;
                if (isset($crawlomatic_Main_Settings['min_word_title']) && $crawlomatic_Main_Settings['min_word_title'] != '') {
                    $title_count = str_word_count($new_post_title);
                    if ($title_count < intval($crawlomatic_Main_Settings['min_word_title'])) {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                            crawlomatic_log_to_file('Skipping post "' . esc_html($new_post_title) . '", because title length (' . $title_count . ') < ' . $crawlomatic_Main_Settings['min_word_title']);
                        }
                        continue;
                    }
                }
                if (isset($crawlomatic_Main_Settings['max_word_title']) && $crawlomatic_Main_Settings['max_word_title'] != '') {
                    if ($title_count == -1) {
                        $title_count = str_word_count($new_post_title);
                    }
                    if ($title_count > intval($crawlomatic_Main_Settings['max_word_title'])) {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                            crawlomatic_log_to_file('Skipping post "' . esc_html($new_post_title) . '", because title length (' . $title_count . ') > ' . $crawlomatic_Main_Settings['max_word_title']);
                        }
                        continue;
                    }
                }
                $content_count = -1;
                if (isset($crawlomatic_Main_Settings['min_word_content']) && $crawlomatic_Main_Settings['min_word_content'] != '') {
                    $content_count = str_word_count(crawlomatic_strip_html_tags($new_post_content));
                    if ($content_count < intval($crawlomatic_Main_Settings['min_word_content'])) {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                            crawlomatic_log_to_file('Skipping post "' . esc_html($new_post_title) . '", because content length (' . $content_count . ') < ' . $crawlomatic_Main_Settings['min_word_content']);
                        }
                        continue;
                    }
                }
                if (isset($crawlomatic_Main_Settings['max_word_content']) && $crawlomatic_Main_Settings['max_word_content'] != '') {
                    if ($content_count == -1) {
                        $content_count = str_word_count(crawlomatic_strip_html_tags($new_post_content));
                    }
                    if ($content_count > intval($crawlomatic_Main_Settings['max_word_content'])) {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                            crawlomatic_log_to_file('Skipping post "' . esc_html($new_post_title) . '", because content length (' . $content_count . ') > ' . $crawlomatic_Main_Settings['max_word_content']);
                        }
                        continue;
                    }
                }
                if (isset($crawlomatic_Main_Settings['strip_links']) && $crawlomatic_Main_Settings['strip_links'] == 'on') {
                    $new_post_content = crawlomatic_strip_links($new_post_content);
                }
                if (isset($crawlomatic_Main_Settings['replace_url']) && $crawlomatic_Main_Settings['replace_url'] !== '') {
                    if(strstr($crawlomatic_Main_Settings['replace_url'], '%%original_url%%') !== false)
                    {
                        $repl = str_replace('%%original_url%%', '', $crawlomatic_Main_Settings['replace_url']);
                        $new_post_content = preg_replace('/<a(.+?)href=["\']([^"\']+?)["\']([^>]*?)>/i','<a$1href="$2' . esc_html($repl) . '"$3>', $new_post_content);
                    }
                    else
                    {
                        $new_post_content = preg_replace('/<a(.+?)href=["\']([^"\']+?)["\']([^>]*?)>/i','<a$1href="' . esc_url($crawlomatic_Main_Settings['replace_url']) . '"$3>', $new_post_content);
                    }
                }
                if ($strip_images == '1') {
                    $new_post_content = crawlomatic_strip_images($new_post_content);
                }
                if ($copy_images == '1' || (isset($crawlomatic_Main_Settings['copy_images']) && $crawlomatic_Main_Settings['copy_images'] == 'on')) {
                    $new_post_content = preg_replace("~\ssrcset=['\"](?:[^'\"]*)['\"]~i", ' ', $new_post_content);
                    preg_match_all('/(http|https|ftp|ftps)?:\/\/\S+\.(?:jpg|jpeg|png|gif)/', $new_post_content, $matches);
                    if(isset($matches[0][0]))
                    {
                        $matches[0] = array_unique($matches[0]);
                        foreach($matches[0] as $match)
                        {
                            $file_path = crawlomatic_copy_image_locally($match, $use_proxy, $request_delay, $custom_user_agent, $user_pass, $custom_cookies);
                            if($file_path != false)
                            {
                                $file_path = str_replace('\\', '/', $file_path);
                                $new_post_content = str_replace($match, $file_path, $new_post_content);
                            }
                        }
                    }
                }
                if ((isset($crawlomatic_Main_Settings['link_attributes_internal']) && $crawlomatic_Main_Settings['link_attributes_internal'] !== '') || (isset($crawlomatic_Main_Settings['link_attributes_external']) && $crawlomatic_Main_Settings['link_attributes_external'] !== ''))
                {
                    $new_post_content = crawlomatic_add_link_tags($new_post_content);
                }
                if (isset($crawlomatic_Main_Settings['iframe_resize_width']) && $crawlomatic_Main_Settings['iframe_resize_width'] !== '')
                {
                    $new_post_content = preg_replace("~<iframe(.*?)(?:width=[\"\'](?:\d*?)[\"\'])?(.*?)>~i", '<iframe$1 width="' . esc_attr($crawlomatic_Main_Settings['iframe_resize_width']) . '"$2>', $new_post_content); 
                }
                if (isset($crawlomatic_Main_Settings['iframe_resize_height']) && $crawlomatic_Main_Settings['iframe_resize_height'] !== '')
                {
                    $new_post_content = preg_replace("~<iframe(.*?)(?:height=[\"\'](?:\d*?)[\"\'])?(.*?)>~i", '<iframe$1 height="' . esc_attr($crawlomatic_Main_Settings['iframe_resize_height']) . '"$2>', $new_post_content); 
                }
                if ($strip_by_regex !== '')
                {
                    $xstrip_by_regex = preg_split('/\r\n|\r|\n/', $strip_by_regex);
                    $xreplace_regex = preg_split('/\r\n|\r|\n/', $replace_regex);
                    $xcnt = 0;
                    $need_to_cont = false;
                    foreach($xstrip_by_regex as $sbr)
                    {
                        if(isset($xreplace_regex[$xcnt]))
                        {
                            $repreg = $xreplace_regex[$xcnt];
                        }
                        else
                        {
                            $repreg = '';
                        }
                        $xcnt++;
                        if($skip_no_match == '1')
                        {
                            preg_match_all("~" . $sbr . "~i", $new_post_content, $reqmatches);
                            if(!isset($reqmatches[0][0]))
                            {
                                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                                    crawlomatic_log_to_file('Skipping post "' . esc_html($new_post_title) . '", because Regex not matched: ' . $sbr);
                                }
                                $need_to_cont = true;
                                break;
                            }
                            else
                            {
                                if(crawlomatic_isRegularExpression("~" . $sbr . "~i") === false)
                                {
                                    crawlomatic_log_to_file('Incorrect strip regex entered: ' . "~" . $sbr . "~i");
                                }
                            }
                        }
                        else
                        {
                            $temp_cont = preg_replace("~" . $sbr . "~i", $repreg, $new_post_content);
                            if($temp_cont !== NULL)
                            {
                                $new_post_content = $temp_cont;
                            }
                        }
                    }
                    if($need_to_cont == true)
                    {
                        continue;
                    }
                }
                if($regex_image == '1')
                {
                    if ($strip_by_regex !== '')
                    {
                        $xstrip_by_regex = preg_split('/\r\n|\r|\n/', $strip_by_regex);
                        $xreplace_regex = preg_split('/\r\n|\r|\n/', $replace_regex);
                        $xcnt = 0;
                        foreach($xstrip_by_regex as $sbr)
                        {
                            if(isset($xreplace_regex[$xcnt]))
                            {
                                $repreg = $xreplace_regex[$xcnt];
                            }
                            else
                            {
                                $repreg = '';
                            }
                            $xcnt++;
                            $temp_cont = preg_replace("~" . $sbr . "~i", $repreg, $get_img);
                            if($temp_cont !== NULL)
                            {
                                $get_img = $temp_cont;
                            }
                        }
                    }
                }
                $my_post['crawlomatic_post_image']       = $get_img;
                if ($strip_by_regex_title !== '')
                {
                    $xstrip_by_regex = preg_split('/\r\n|\r|\n/', $strip_by_regex_title);
                    $xreplace_regex = preg_split('/\r\n|\r|\n/', $replace_regex_title);
                    $xcnt = 0;
                    foreach($xstrip_by_regex as $sbr)
                    {
                        if(isset($xreplace_regex[$xcnt]))
                        {
                            $repreg = $xreplace_regex[$xcnt];
                        }
                        else
                        {
                            $repreg = '';
                        }
                        $xcnt++;
                        $temp_cont_title = preg_replace("~" . $sbr . "~i", $repreg, $new_post_title);
                        if($temp_cont_title !== NULL)
                        {
                            $new_post_title = $temp_cont_title;
                        }
                    }
                }
                $exc_cont = $content;
                if ($strip_by_regex !== '')
                {
                    $xstrip_by_regex = preg_split('/\r\n|\r|\n/', $strip_by_regex);
                    $xreplace_regex = preg_split('/\r\n|\r|\n/', $replace_regex);
                    $xcnt = 0;
                    foreach($xstrip_by_regex as $sbr)
                    {
                        if(isset($xreplace_regex[$xcnt]))
                        {
                            $repreg = $xreplace_regex[$xcnt];
                        }
                        else
                        {
                            $repreg = '';
                        }
                        $xcnt++;
                        $temp_contx = preg_replace("~" . $sbr . "~i", $repreg, $exc_cont);
                        if($temp_contx !== NULL)
                        {
                            $exc_cont = $temp_contx;
                        }
                    }
                }
                $new_post_content = str_replace('</ iframe>', '</iframe>', $new_post_content);
                if ($keep_source == '1')
                {
                    $new_post_content = preg_replace('{"https:\/\/translate.google.com\/translate\?hl=(?:.*?)&prev=_t&sl=(?:.*?)&tl=(?:.*?)&u=([^"]*?)"}i', "$1", urldecode($new_post_content));
                }
                if (isset($crawlomatic_Main_Settings['fix_html']) && $crawlomatic_Main_Settings['fix_html'] == "on")
                {
                    $new_post_content = crawlomatic_repairHTML($new_post_content);
                    if (isset($crawlomatic_Main_Settings['alt_read']) && $crawlomatic_Main_Settings['alt_read'] == "on")
                    {
                        $new_post_content = str_replace('<html><body>', '', $new_post_content);
                        $new_post_content = str_replace('</body></html>', '', $new_post_content);
                        $new_post_content = str_replace('<a ', ' <a ', $new_post_content);
                    }
                }
                if (isset($crawlomatic_Main_Settings['strip_html']) && $crawlomatic_Main_Settings['strip_html'] == 'on') {
                    $new_post_content = crawlomatic_strip_html_tags_nl($new_post_content);
                }
                if($ret_content == 1)
                {
                    return $new_post_content;
                }
                $my_post['post_content'] = trim($new_post_content);
                if (isset($crawlomatic_Main_Settings['disable_excerpt']) && $crawlomatic_Main_Settings['disable_excerpt'] == "on") {
                    $my_post['post_excerpt'] = '';
                }
                else
                {
                    if ($translate != "disabled" && $translate != "en") {
                        $my_post['post_excerpt'] = crawlomatic_getExcerpt($new_post_content);
                    } else {
                        $my_post['post_excerpt'] = crawlomatic_getExcerpt($exc_cont);
                    }
                }
                $my_post['auto_delete'] = '';
                if ($auto_delete !== "") {
                    $del_time = strtotime($auto_delete);
                    if($del_time !== false)
                    {
                        $my_post['auto_delete'] = $del_time;
                    }
                }
                $my_post['post_title']       = $new_post_title;
                $my_post['original_title']   = $title;
                $my_post['original_content'] = $content;
                $my_post['crawlomatic_timestamp']   = crawlomatic_get_date_now();
                $my_post['crawlomatic_post_format'] = $post_format;
                if ($enable_pingback == '1') {
                    $my_post['ping_status'] = 'open';
                } else {
                    $my_post['ping_status'] = 'closed';
                }
                $custom_arr = array();
                if($custom_fields != '')
                {
                    if(stristr($custom_fields, '=>') != false)
                    {
                        $rule_arr = explode(',', trim($custom_fields));
                        foreach($rule_arr as $rule)
                        {
                            $my_args = explode('=>', trim($rule));
                            if(isset($my_args[1]))
                            {
                                if(isset($my_args[2]))
                                {
                                    $req_list = explode(',', $my_args[2]);
                                    $required_found = false;
                                    foreach($req_list as $rl)
                                    {
                                        if(function_exists('mb_stristr'))
                                        {
                                            if(mb_stristr($new_post_content, trim($rl)) !== false)
                                            {
                                                $required_found = true;
                                                break;
                                            }
                                        }
                                        else
                                        {
                                            if(stristr($new_post_content, trim($rl)) === false)
                                            {
                                                $required_found = true;
                                                break;
                                            }
                                        }
                                        if(function_exists('mb_stristr'))
                                        {
                                            if(mb_stristr($new_post_title, trim($rl)) !== false)
                                            {
                                                $required_found = true;
                                                break;
                                            }
                                        }
                                        else
                                        {
                                            if(stristr($new_post_title, trim($rl)) === false)
                                            {
                                                $required_found = true;
                                                break;
                                            }
                                        }
                                    }
                                    if($required_found === false)
                                    {
                                        if(isset($my_args[3]))
                                        {
                                            $my_args[1] = $my_args[3];
                                        }
                                        else
                                        {
                                            continue;
                                        }
                                    }
                                }
                                $custom_field_content = trim($my_args[1]);
                                $custom_field_content = crawlomatic_replaceContentShortcodes($custom_field_content, $title, $content, $short_url, $extra_categories, $item_tags, $get_img, $description, $read_more, $postdate, $item['price'], $item_price_multi, $custom_shortcodes_arr, $img_attr, $screenimageURL, $append_urls, $item_download);
                                if(stristr($my_args[0], '[') !== false && stristr($my_args[0], ']') !== false)
                                {
                                    preg_match_all('#([^\[\]]*?)\[([^\[\]]*?)\]#', $my_args[0], $cfm);
                                    if(isset($cfm[2][0]))
                                    {
                                        if(isset($custom_arr[trim($cfm[1][0])]) && is_array($custom_arr[trim($cfm[1][0])]))
                                        {
                                            $custom_arr[trim($cfm[1][0])] = array_merge($custom_arr[trim($cfm[1][0])], array(trim($cfm[2][0]) => $custom_field_content));
                                        }
                                        else
                                        {
                                            $custom_arr[trim($cfm[1][0])] = array(trim($cfm[2][0]) => $custom_field_content);
                                        }
                                    }
                                    else
                                    {
                                        $custom_arr[trim($my_args[0])] = $custom_field_content;
                                    }
                                }
                                else
                                {
                                    $custom_arr[trim($my_args[0])] = $custom_field_content;
                                }
                            }
                        }
                    }
                }
                if($woo_active && ($post_type == 'product' || $post_type == 'product_variation'))
                {
                    if(strstr($custom_tax, '_price') === false)
                    {
                        $custom_arr['_price'] = $item_price_multi;
                    }
                    if(strstr($custom_tax, 'sale_price') === false)
                    {
                        $custom_arr['sale_price'] = $item_price_multi;
                    }
                    if(strstr($custom_tax, 'regular_price') === false)
                    {
                        $custom_arr['regular_price'] = $item_price_multi;
                    }
                    if(strstr($custom_tax, '_visibility') === false)
                    {
                        $custom_arr['_visibility'] = 'visible';
                    }
                    if(strstr($custom_tax, '_manage_stock') === false)
                    {
                        $custom_arr['_manage_stock'] = 'no';
                    }
                    if(strstr($custom_tax, '_stock_status') === false)
                    {
                        $custom_arr['_stock_status'] = 'instock';
                    }
                    if(strstr($custom_tax, '_sku') === false)
                    {
                        $custom_arr['_sku'] = crawlomatic_generate_random_string(10);
                    }
                }
                $custom_tax_arr = array();
                if($custom_tax != '')
                {
                    if(stristr($custom_tax, '=>') != false)
                    {
                        $rule_arr = explode(';', trim($custom_tax));
                        foreach($rule_arr as $rule)
                        {
                            $my_args = explode('=>', trim($rule));
                            if(isset($my_args[1]))
                            {
                                if(isset($my_args[2]))
                                {
                                    $req_list = explode(',', $my_args[2]);
                                    $required_found = false;
                                    foreach($req_list as $rl)
                                    {
                                        if(function_exists('mb_stristr'))
                                        {
                                            if(mb_stristr($new_post_content, trim($rl)) !== false)
                                            {
                                                $required_found = true;
                                                break;
                                            }
                                        }
                                        else
                                        {
                                            if(stristr($new_post_content, trim($rl)) === false)
                                            {
                                                $required_found = true;
                                                break;
                                            }
                                        }
                                        if(function_exists('mb_stristr'))
                                        {
                                            if(mb_stristr($new_post_title, trim($rl)) !== false)
                                            {
                                                $required_found = true;
                                                break;
                                            }
                                        }
                                        else
                                        {
                                            if(stristr($new_post_title, trim($rl)) === false)
                                            {
                                                $required_found = true;
                                                break;
                                            }
                                        }
                                    }
                                    if($required_found === false)
                                    {
                                        if(isset($my_args[3]))
                                        {
                                            $my_args[1] = $my_args[3];
                                        }
                                        else
                                        {
                                            continue;
                                        }
                                    }
                                }
                                $custom_tax_content = trim($my_args[1]);
                                $custom_tax_content = crawlomatic_replaceContentShortcodes($custom_tax_content, $title, $content, $short_url, $extra_categories, $item_tags, $get_img, $description, $read_more, $postdate, $item['price'], $item_price_multi, $custom_shortcodes_arr, $img_attr, $screenimageURL, $append_urls, $item_download);
                                
                                if(substr(trim($my_args[0]), 0, 3) === "pa_" && $post_type == 'product')
                                {
                                    if(isset($custom_arr['_product_attributes']))
                                    {
                                        $custom_arr['_product_attributes'] = array_merge($custom_arr['_product_attributes'], array(trim($my_args[0]) =>array(
                                            'name' => trim($my_args[0]),
                                            'value' => $custom_tax_content,
                                            'is_visible' => '1',
                                            'is_taxonomy' => '1'
                                        )));
                                    }
                                    else
                                    {
                                        $custom_arr['_product_attributes'] = array(trim($my_args[0]) =>array(
                                            'name' => trim($my_args[0]),
                                            'value' => $custom_tax_content,
                                            'is_visible' => '1',
                                            'is_taxonomy' => '1'
                                        ));
                                    }
                                }
                                if(isset($custom_tax_arr[trim($my_args[0])]))
                                {
                                    $custom_tax_arr[trim($my_args[0])] .= ',' . $custom_tax_content;
                                }
                                else
                                {
                                    $custom_tax_arr[trim($my_args[0])] = $custom_tax_content;
                                }
                            }
                        }
                    }
                }
                if(count($custom_tax_arr) > 0)
                {
                    $my_post['taxo_input'] = $custom_tax_arr;
                }
                $my_post['meta_input'] = $custom_arr;
                $post_array[] = $item['title'];
                if($my_post['post_content'] === '' && $my_post['post_title'] === '')
                {
                    continue;
                }
                if (isset($crawlomatic_Main_Settings['cleanup_not_printable']) && $crawlomatic_Main_Settings['cleanup_not_printable'] == 'on') {
                    $my_post['post_content'] = preg_replace('/[\x00-\x1F\x7F]/u', '', $my_post['post_content']);
                    $my_post['post_title'] = preg_replace('/[\x00-\x1F\x7F]/u', '', $my_post['post_title']);
                }
                if (!isset($crawlomatic_Main_Settings['keep_filters']) || $crawlomatic_Main_Settings['keep_filters'] != 'on') 
                {
                    remove_filter('content_save_pre', 'wp_filter_post_kses');
                    remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');remove_filter('title_save_pre', 'wp_filter_kses');
                }
                if($my_post['update_meta_id'] != '' && is_numeric($my_post['update_meta_id']))
                {
                    $my_post['ID'] = $my_post['update_meta_id'];
                    unset($my_post['post_status']);
                    $post_id = wp_update_post($my_post, true);
                }
                else
                {
                    $post_id = wp_insert_post($my_post, true);
                }
                if (!isset($crawlomatic_Main_Settings['keep_filters']) || $crawlomatic_Main_Settings['keep_filters'] != 'on') 
                {
                    add_filter('content_save_pre', 'wp_filter_post_kses');
                    add_filter('content_filtered_save_pre', 'wp_filter_post_kses');add_filter('title_save_pre', 'wp_filter_kses');
                }
                if (!is_wp_error($post_id)) {
                    $posts_inserted++;
                    if(!empty($my_post['download_local']))
                    {
                        $wp_filetype = wp_check_filetype( $my_post['download_local'], null );
                        $attachment = array(
                            'post_mime_type' => $wp_filetype['type'],
                            'post_title' => 'Downloaded file for post ID ' . $post_id,
                            'post_content' => '',
                            'post_status' => 'inherit'
                        );
                        $screens_attach_id = wp_insert_attachment($attachment, $my_post['download_local'], $post_id);
                        require_once( ABSPATH . 'wp-admin/includes/image.php' );
                        require_once( ABSPATH . 'wp-admin/includes/media.php' );
                        $attach_data = wp_generate_attachment_metadata($screens_attach_id, $my_post['download_local']);
                        wp_update_attachment_metadata( $screens_attach_id, $attach_data );

                        if($post_type == 'product' && class_exists('WC_Product_Download'))
                        {
                            $file_url  = wp_get_attachment_url( $screens_attach_id );
                            $download_id = md5( $file_url );
                            $file_name = $my_post['post_title'];
                            $pd_object = new WC_Product_Download();
                            $pd_object->set_id( $download_id );
                            $pd_object->set_name( $file_name );
                            $pd_object->set_file( $file_url );
                            $product = wc_get_product( $post_id );
                            if($product !== null)
                            {
                                $downloads = $product->get_downloads();
                                $downloads[$download_id] = $pd_object;
                                $product->set_downloads($downloads);
                                $product->save();
                            }
                        }
                    }
                    if(isset($my_post['taxo_input']))
                    {
                        foreach($my_post['taxo_input'] as $taxn => $taxval)
                        {
                            $taxn = trim($taxn);
                            $taxval = trim($taxval);
                            if(is_taxonomy_hierarchical($taxn))
                            {
                                $taxval = array_map('trim', explode(',', $taxval));
                                for($ii = 0; $ii < count($taxval); $ii++)
                                {
                                    if(!is_numeric($taxval[$ii]))
                                    {
                                        $xtermid = get_term_by('name', $taxval[$ii], $taxn);
                                        if($xtermid !== false)
                                        {
                                            $taxval[$ii] = intval($xtermid->term_id);
                                        }
                                        else
                                        {
                                            wp_insert_term( $taxval[$ii], $taxn);
                                            $xtermid = get_term_by('name', $taxval[$ii], $taxn);
                                            if($xtermid !== false)
                                            {
                                                if($wpml_lang != '' && function_exists('pll_set_term_language'))
                                                {
                                                    pll_set_term_language($xtermid->term_id, $wpml_lang); 
                                                }
                                                elseif($wpml_lang != '' && has_filter('wpml_object_id'))
                                                {
                                                    $wpml_element_type = apply_filters( 'wpml_element_type', $taxn );
                                                    $pars['element_id'] = $xtermid->term_id;
                                                    $pars['element_type'] = $wpml_element_type;
                                                    $pars['language_code'] = $wpml_lang;
                                                    $pars['trid'] = FALSE;
                                                    $pars['source_language_code'] = NULL;
                                                    do_action('wpml_set_element_language_details', $pars);
                                                }
                                                $taxval[$ii] = intval($xtermid->term_id);
                                            }
                                        }
                                    }
                                }
                                wp_set_post_terms($post_id, $taxval, $taxn, true);
                            }
                            else
                            {
                                wp_set_post_terms($post_id, trim($taxval), $taxn, true);
                            }
                        }
                    }
                    if (isset($my_post['crawlomatic_post_format']) && $my_post['crawlomatic_post_format'] != '' && $my_post['crawlomatic_post_format'] != 'post-format-standard') {
                        wp_set_post_terms($post_id, $my_post['crawlomatic_post_format'], 'post_format', true);
                    }
                    if($my_post['screen_attach'] != '')
                    {
                        $media_post = wp_update_post( array(
                            'ID'            => $my_post['screen_attach'],
                            'post_parent'   => $post_id,
                        ), true );

                        if( is_wp_error( $media_post ) ) {
                            crawlomatic_log_to_file( 'Failed to assign post attachment ' . $my_post['screen_attach'] . ' to post id ' . $post_id . ': ' . print_r( $media_post, 1 ) );
                        }
                    }
                    $featured_path = '';
                    $image_failed  = false;
                    if(isset($my_post['post_gallery']) && !empty($my_post['post_gallery']))
                    {
                        if (($key = array_search($my_post['crawlomatic_post_image'], $my_post['post_gallery'])) !== false) {
                            unset($my_post['post_gallery'][$key]);
                        }
                        $xcounter = 1;
                        $attach_ids = array();
                        foreach($my_post['post_gallery'] as $gimg)
                        {
                            $gimg = htmlspecialchars_decode($gimg);
                            if ($gallery_regex !== '')
                            {
                                $xstrip_by_regex = preg_split('/\r\n|\r|\n/', $gallery_regex);
                                $xreplace_regex = preg_split('/\r\n|\r|\n/', $replace_gallery_regex);
                                $xcnt = 0;
                                foreach($xstrip_by_regex as $sbr)
                                {
                                    if(isset($xreplace_regex[$xcnt]))
                                    {
                                        $repreg = $xreplace_regex[$xcnt];
                                    }
                                    else
                                    {
                                        $repreg = '';
                                    }
                                    $xcnt++;
                                    $temp_cont_gallery = preg_replace("~" . $sbr . "~i", $repreg, $gimg);
                                    if($temp_cont_gallery !== NULL)
                                    {
                                        $gimg = $temp_cont_gallery;
                                    }
                                }
                            }
                            $uploaded_gallery = crawlomatic_upload_attachment_media($gimg, $post_id, $use_proxy, $request_delay, $custom_user_agent, $custom_cookies, $user_pass, $xcounter);
                            if($uploaded_gallery === false)
                            {
                                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                {
                                    crawlomatic_log_to_file('crawlomatic_upload_attachment_media failed for ' . $gimg . '!');
                                }
                            }
                            else
                            {
                                $attach_ids[] = $uploaded_gallery;
                            }
                            $xcounter++;
                        }
                        if($post_type == 'product' && !empty($attach_ids))
                        {
                            update_post_meta($post_id, '_product_image_gallery', implode(',', $attach_ids));
                        }
                    }
                    if ($my_post['update_meta_id'] == '' || !is_numeric($my_post['update_meta_id']) || !isset($crawlomatic_Main_Settings['no_up_img']) || $crawlomatic_Main_Settings['no_up_img'] != 'on') 
                    {
                        if ($featured_image == '1') {
                            $get_img = $my_post['crawlomatic_post_image'];
                            if ($get_img != '') {
                                if (!crawlomatic_generate_featured_image($get_img, $post_id, $use_proxy, $request_delay, $custom_user_agent, $custom_cookies, $user_pass)) {
                                    $image_failed = true;
                                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                    {
                                        crawlomatic_log_to_file('crawlomatic_generate_featured_image failed for ' . $get_img . '!');
                                    }
                                } else {
                                    $featured_path = $get_img;
                                    if ( ! add_post_meta( $post_id, 'crawlomatic_featured_img', $featured_path, true ) ) 
                                    { 
                                       update_post_meta( $post_id, 'crawlomatic_featured_img', $featured_path );
                                    }
                                }
                            } else {
                                $image_failed = true;
                            }
                        }
                        if ($image_failed || $featured_image !== '1') {
                            if ($image_url != '') {
                                $image_urlx = explode(',',$image_url);
                                $image_urlx = trim($image_urlx[array_rand($image_urlx)]);
                                $retim = false;
                                if(is_numeric($image_urlx) && $image_urlx > 0)
                                {
                                    require_once(ABSPATH . 'wp-admin/includes/image.php');
                                    require_once(ABSPATH . 'wp-admin/includes/media.php');
                                    $res2 = set_post_thumbnail($post_id, $image_urlx);
                                    if ($res2 === FALSE) {
                                    }
                                    else
                                    {
                                        $retim = true;
                                    }
                                }
                                if($retim == false)
                                {
                                    if (isset($crawlomatic_Main_Settings['crawlomatic_featured_image_checking']) && $crawlomatic_Main_Settings['crawlomatic_featured_image_checking'] == 'on') {
                                        stream_context_set_default( [
                                            'ssl' => [
                                                'verify_peer' => false,
                                                'verify_peer_name' => false,
                                            ],
                                        ]);
                                        error_reporting(0);
                                        $url_headers = get_headers($image_urlx, 1);
                                        error_reporting(E_ALL);
                                        if (isset($url_headers['Content-Type'])) {
                                            if (is_array($url_headers['Content-Type'])) {
                                                $img_type = strtolower($url_headers['Content-Type'][0]);
                                            } else {
                                                $img_type = strtolower($url_headers['Content-Type']);
                                            }
                                            
                                            if (strstr($img_type, 'image/') !== false) {
                                                if (!crawlomatic_generate_featured_image($image_url, $post_id, $use_proxy, $request_delay, $custom_user_agent, $custom_cookies, $user_pass)) {
                                                    $image_failed = true;
                                                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                                                        crawlomatic_log_to_file('crawlomatic_generate_featured_image failed to default value: ' . $image_urlx . '!');
                                                    }
                                                } else {
                                                    $featured_path = $image_urlx;
                                                    if ( ! add_post_meta( $post_id, 'crawlomatic_featured_img', $featured_path, true ) ) { 
                                                       update_post_meta( $post_id, 'crawlomatic_featured_img', $featured_path );
                                                    }
                                                }
                                            }
                                        }
                                    }
                                    else
                                    {
                                        if (!crawlomatic_generate_featured_image($image_url, $post_id, $use_proxy, $request_delay, $custom_user_agent, $custom_cookies, $user_pass)) {
                                            $image_failed = true;
                                            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                                                crawlomatic_log_to_file('crawlomatic_generate_featured_image failed to default value: ' . $image_urlx . '!');
                                            }
                                        } else {
                                            $featured_path = $image_urlx;
                                            if ( ! add_post_meta( $post_id, 'crawlomatic_featured_img', $featured_path, true ) ) { 
                                               update_post_meta( $post_id, 'crawlomatic_featured_img', $featured_path );
                                            }
                                        }
                                    }
                                }
                            }
                        }
                        if($featured_image == '1' && $featured_path == '' && ($skip_no_image == '1' || (isset($crawlomatic_Main_Settings['skip_no_img']) && $crawlomatic_Main_Settings['skip_no_img'] == 'on')))
                        {
                            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                                crawlomatic_log_to_file('Skipping post "' . $my_post['post_title'] . '", because it failed to generate a featured image for: ' . $get_img . ' and ' . $image_url);
                            }
                            wp_delete_post($post_id, true);
                            $posts_inserted--;
                            continue;
                        }
                    }
                    if ($can_create_tag == '1') {
                        if(strstr($custom_tax, 'product_tag') === false)
                        {
                            if ($my_post['tags_input'] != '')
                            {
                                if($post_type == 'product')
                                {
                                    wp_set_post_terms($post_id, $my_post['tags_input'], 'product_tag', true);
                                }
                            }
                        }
                    }
                    if($remove_default == '1' && (($auto_categories == '1' && $my_post['extra_categories'] != '') || (isset($default_category) && $default_category !== 'crawlomatic_no_category_12345678' && $default_category[0] !== 'crawlomatic_no_category_12345678')))
                    {
                        $default_categories = wp_get_post_categories($post_id);
                    }
                    if ($auto_categories == '1') {
                        if(strstr($custom_tax, 'product_cat') === false)
                        {
                            if ($my_post['extra_categories'] != '') {
                                if($post_type == 'product')
                                {
                                    if($parent_category_id != '')
                                    {
                                        $termid = crawlomatic_create_terms('product_cat', $parent_category_id, $my_post['extra_categories'], $remove_cats);
                                    }
                                    else
                                    {
                                        $termid = crawlomatic_create_terms('product_cat', '0', $my_post['extra_categories'], $remove_cats);
                                    }
                                    if($wpml_lang != '' && function_exists('pll_set_term_language'))
                                    {
                                        foreach($termid as $tx)
                                        {
                                            pll_set_term_language($tx, $wpml_lang); 
                                        }
                                    }
                                    elseif($wpml_lang != '' && has_filter('wpml_object_id'))
                                    {
                                        $wpml_element_type = apply_filters( 'wpml_element_type', 'product_cat' );
                                        foreach($termid as $tx)
                                        {
                                            $pars['element_id'] = $tx;
                                            $pars['element_type'] = $wpml_element_type;
                                            $pars['language_code'] = $wpml_lang;
                                            $pars['trid'] = FALSE;
                                            $pars['source_language_code'] = NULL;
                                            do_action('wpml_set_element_language_details', $pars);
                                        }
                                    }
                                }
                                else
                                {
                                    if($parent_category_id != '')
                                    {
                                        $termid = crawlomatic_create_terms('category', $parent_category_id, $my_post['extra_categories'], $remove_cats);
                                    }
                                    else
                                    {
                                        $termid = crawlomatic_create_terms('category', '0', $my_post['extra_categories'], $remove_cats);
                                    }
                                    if($wpml_lang != '' && function_exists('pll_set_term_language'))
                                    {
                                        foreach($termid as $tx)
                                        {
                                            pll_set_term_language($tx, $wpml_lang); 
                                        }
                                    }
                                    elseif($wpml_lang != '' && has_filter('wpml_object_id'))
                                    {
                                        $wpml_element_type = apply_filters( 'wpml_element_type', 'category' );
                                        foreach($termid as $tx)
                                        {
                                            $pars['element_id'] = $tx;
                                            $pars['element_type'] = $wpml_element_type;
                                            $pars['language_code'] = $wpml_lang;
                                            $pars['trid'] = FALSE;
                                            $pars['source_language_code'] = NULL;
                                            do_action('wpml_set_element_language_details', $pars);
                                        }
                                    }
                                }
                                if($post_type == 'product')
                                {
                                    wp_set_post_terms($post_id, $termid, 'product_cat', true);
                                }
                                else
                                {
                                    wp_set_post_terms($post_id, $termid, 'category', true);
                                }
                            }
                        }
                    }
                    if (isset($default_category) && $default_category !== 'crawlomatic_no_category_12345678' && $default_category[0] !== 'crawlomatic_no_category_12345678') {
                        if(is_array($default_category))
                        {
                            $cats  = array();
                            $wcats = array();
                            foreach($default_category as $dc)
                            {
                                if(substr($dc, 0, 1) === 'w')
                                {
                                    $wcats[] = ltrim($dc, 'w');
                                }
                                else
                                {
                                    $cats[] = $dc;
                                }
                            }
                            if($post_type == 'product')
                            {
                                global $sitepress;
                                if($wpml_lang != '' && has_filter('wpml_current_language') && $sitepress != null)
                                {
                                    $current_language = apply_filters( 'wpml_current_language', NULL );
                                    $sitepress->switch_lang($wpml_lang);
                                }
                                wp_set_post_terms($post_id, $wcats, 'product_cat', true);
                                if($wpml_lang != '' && has_filter('wpml_current_language') && $sitepress != null)
                                {
                                    $sitepress->switch_lang($current_language);
                                }
                            }
                            else
                            {
                                global $sitepress;
                                if($wpml_lang != '' && has_filter('wpml_current_language') && $sitepress != null)
                                {
                                    $current_language = apply_filters( 'wpml_current_language', NULL );
                                    $sitepress->switch_lang($wpml_lang);
                                }
                                wp_set_post_categories($post_id, $cats, true);
                                if($wpml_lang != '' && has_filter('wpml_current_language') && $sitepress != null)
                                {
                                    $sitepress->switch_lang($current_language);
                                }
                            }
                        }
                        else
                        {
                            $cats  = array();
                            $wcats = array();
                            if(substr($default_category, 0, 1) === 'w')
                            {
                                $wcats[] = ltrim($default_category, 'w');
                            }
                            else
                            {
                                $cats[] = $default_category;
                            }
                            if($post_type == 'product')
                            {
                                global $sitepress;
                                if($wpml_lang != '' && has_filter('wpml_current_language') && $sitepress != null)
                                {
                                    $current_language = apply_filters( 'wpml_current_language', NULL );
                                    $sitepress->switch_lang($wpml_lang);
                                }
                                wp_set_post_terms($post_id, $wcats, 'product_cat', true);
                                if($wpml_lang != '' && has_filter('wpml_current_language') && $sitepress != null)
                                {
                                    $sitepress->switch_lang($current_language);
                                }
                            }
                            else
                            {
                                global $sitepress;
                                if($wpml_lang != '' && has_filter('wpml_current_language') && $sitepress != null)
                                {
                                    $current_language = apply_filters( 'wpml_current_language', NULL );
                                    $sitepress->switch_lang($wpml_lang);
                                }
                                wp_set_post_categories($post_id, $cats, true);
                                if($wpml_lang != '' && has_filter('wpml_current_language') && $sitepress != null)
                                {
                                    $sitepress->switch_lang($current_language);
                                }
                            }
                        }
                    }
                    if($remove_default == '1' && (($auto_categories == '1' && $my_post['extra_categories'] != '') || (isset($default_category) && $default_category !== 'crawlomatic_no_category_12345678' && $default_category[0] !== 'crawlomatic_no_category_12345678')))
                    {
                        $new_categories = wp_get_post_categories($post_id);
                        if(isset($default_categories) && !($default_categories == $new_categories))
                        {
                            foreach($default_categories as $dc)
                            {
                                $rem_cat = get_category( $dc );
                                wp_remove_object_terms( $post_id, $rem_cat->slug, 'category' );
                            }
                        }
                    }
                    if (isset($crawlomatic_Main_Settings['post_source_custom']) && $crawlomatic_Main_Settings['post_source_custom'] != '') {
                        $tax_rez = wp_set_object_terms( $post_id, $crawlomatic_Main_Settings['post_source_custom'], 'coderevolution_post_source', true);
                    }
                    else
                    {
                        $tax_rez = wp_set_object_terms( $post_id, 'Crawlomatic_' . $param, 'coderevolution_post_source', true);
                    }
                    if (is_wp_error($tax_rez)) {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                            crawlomatic_log_to_file('wp_set_object_terms failed for: ' . $post_id . '!');
                        }
                    }
                    if (isset($crawlomatic_Main_Settings['link_source']) && $crawlomatic_Main_Settings['link_source'] == 'on') {
                        $title_link_url = '1';
                    }
                    else
                    {
                        $title_link_url = '0';
                    }
                    if($featured_path == '')
                    {
                        $featured_path = $my_post['crawlomatic_post_image'];
                    }
                    crawlomatic_addPostMeta($post_id, $my_post, $param, $featured_path, $title_link_url, $css_cont, $crawlomatic_Main_Settings);
                    if($wpml_lang != '' && (class_exists('SitePress') || function_exists('wpml_object_id')))
                    {
                        $wpml_element_type = apply_filters( 'wpml_element_type', $post_type );
                        $pars['element_id'] = $post_id;
                        $pars['element_type'] = $wpml_element_type;
                        $pars['language_code'] = $wpml_lang;
                        $pars['source_language_code'] = NULL;
                        do_action('wpml_set_element_language_details', $pars);

                        global $wp_filesystem;
                        if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
                            include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
                            wp_filesystem($creds);
                        }
                        if($wp_filesystem->exists(WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php'))
                        {
                            include_once( WP_PLUGIN_DIR . '/sitepress-multilingual-cms/inc/wpml-api.php' );
                        }
                        $wpml_lang = trim($wpml_lang);
                        if(function_exists('wpml_update_translatable_content'))
                        {
                            wpml_update_translatable_content('post_' . $post_type, $post_id, $wpml_lang);
                            if($my_post['crawlomatic_post_orig_url'] != '')
                            {
                                global $sitepress;
                                global $wpdb;
                                $keyid = md5($my_post['crawlomatic_post_orig_url']);
                                $keyName = $keyid . '_wpml';
                                $rezxxxa = $wpdb->get_results( "SELECT * FROM {$wpdb->prefix}postmeta WHERE `meta_key` = '$keyName' limit 1", ARRAY_A );
                                if(count($rezxxxa) != 0)
                                {
                                    $metaRow = $rezxxxa[0];
                                    $metaValue = $metaRow['meta_value'];
                                    $metaParts = explode('_', $metaValue);
                                    $sitepress->set_element_language_details($post_id, 'post_'.$my_post['post_type'] , $metaParts[0], $wpml_lang, $metaParts[1] ); 
                                }
                                else
                                {
                                    $ptrid = $sitepress->get_element_trid($post_id);
                                    add_post_meta($post_id, $keyid.'_wpml', $ptrid.'_'.$wpml_lang );
                                }
                            }
                            
                        }
                    }
                    elseif($wpml_lang != '' && function_exists('pll_set_post_language'))
                    {
                        pll_set_post_language($post_id, $wpml_lang);
                    }
                    if (isset($crawlomatic_Main_Settings['draft_first']) && $crawlomatic_Main_Settings['draft_first'] == 'on' && $draft_me == true)
                    {
                        crawlomatic_change_post_status($post_id, 'publish');
                    }
                    if (isset($crawlomatic_Main_Settings['send_post_email']) && $crawlomatic_Main_Settings['send_post_email'] == 'on') 
                    {
                        $to = $crawlomatic_Main_Settings['email_address'];
                        if (!filter_var($to, FILTER_VALIDATE_EMAIL) === false)
                        {
                            $subject   = get_the_title($post_id);
                            $content_post = get_post($post_id);
                            if($content_post !== null)
                            {
                                $message = $content_post->post_content;
                                $message = str_replace(']]>', ']]&gt;', $message);
                                $headers[] = 'From: Crawlomatic Plugin <echo@noreply.net>';
                                $headers[] = 'Reply-To: noreply@echo.com';
                                $headers[] = 'X-Mailer: PHP/' . phpversion();
                                $headers[] = 'Content-Type: text/html';
                                $headers[] = 'Charset: ' . get_option('blog_charset', 'UTF-8');
                                wp_mail($to, $subject, $message, $headers);
                            }
                        }
                    }
                } else {
                    crawlomatic_log_to_file('Failed to insert post into database! Title:' . $my_post['post_title'] . '! Error: ' . $post_id->get_error_message() . 'Error code: ' . $post_id->get_error_code() . 'Error data: ' . $post_id->get_error_data());
                    continue;
                }
                $count++;
            }
            unset($posted_items);
        }
        catch (Exception $e) {
            if($continue_search == '1' && isset($skip_posts_temp[$param]))
            {
                unset($skip_posts_temp[$param]);
                update_option('crawlomatic_continue_search', $skip_posts_temp);
            }
            crawlomatic_log_to_file('Exception thrown ' . esc_html($e->getMessage()) . '!');
            if($auto == 1)
            {
                crawlomatic_clearFromList($param);
            }
            return 'fail';
        }
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('Rule ID ' . esc_html($param) . ' for ' . $ids . ' successfully run! ' . esc_html($posts_inserted) . ' posts created!');
        }
        if (isset($crawlomatic_Main_Settings['send_email']) && $crawlomatic_Main_Settings['send_email'] == 'on' && $crawlomatic_Main_Settings['email_address'] !== '') {
            if (isset($crawlomatic_Main_Settings['email_summary']) && $crawlomatic_Main_Settings['email_summary'] == 'on') 
            {
                $last_sent  = get_option('crawlomatic_last_sent_email', false);
                if($last_sent == false)
                {
                    $last_sent = date("d.m.y");
                    update_option('crawlomatic_last_sent_email', $last_sent);
                }
                $email_content  = get_option('crawlomatic_email_content', '');
                $email_content .= '<br/>Rule ID ' . esc_html($param) . ' for ' . $ids . ' successfully run! ' . esc_html($posts_inserted) . ' posts created!';
                if($last_sent != date("d.m.y"))
                {
                    update_option('crawlomatic_last_sent_email', date("d.m.y"));
                    update_option('crawlomatic_email_content', '');
                    try {
                        $to        = $crawlomatic_Main_Settings['email_address'];
                        if (!filter_var($to, FILTER_VALIDATE_EMAIL) === false)
                        {
                            $subject   = '[Crawlomatic] Rule running report - ' . crawlomatic_get_date_now();
                            $message   = 'Rule ID ' . esc_html($param) . ' for ' . $ids . ' successfully run! ' . esc_html($posts_inserted) . ' posts created!';
                            $headers[] = 'From: Crawlomatic Plugin <echo@noreply.net>';
                            $headers[] = 'Reply-To: noreply@echo.com';
                            $headers[] = 'X-Mailer: PHP/' . phpversion();
                            $headers[] = 'Content-Type: text/html';
                            $headers[] = 'Charset: ' . get_option('blog_charset', 'UTF-8');
                            wp_mail($to, $subject, $message, $headers);
                        }
                    }
                    catch (Exception $e) {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                            crawlomatic_log_to_file('Failed to send mail: Exception thrown ' . esc_html($e->getMessage()) . '!');
                        }
                    }
                }
                else
                {
                    update_option('crawlomatic_email_content', $email_content);
                }
            }
            else
            {
                $getdatex = get_option('crawlomatic_last_sent_email', false);
                if($getdatex != false)
                {
                    update_option('crawlomatic_last_sent_email', false);
                }
                $getdatex = get_option('crawlomatic_email_content', false);
                if($getdatex != false)
                {
                    update_option('crawlomatic_email_content', false);
                }
                try {
                    $to        = $crawlomatic_Main_Settings['email_address'];
                    if (!filter_var($to, FILTER_VALIDATE_EMAIL) === false)
                    {
                        $subject   = '[Crawlomatic] Rule running report - ' . crawlomatic_get_date_now();
                        $message   = 'Rule ID ' . esc_html($param) . ' for ' . $ids . ' successfully run! ' . esc_html($posts_inserted) . ' posts created!';
                        $headers[] = 'From: Crawlomatic Plugin <echo@noreply.net>';
                        $headers[] = 'Reply-To: noreply@echo.com';
                        $headers[] = 'X-Mailer: PHP/' . phpversion();
                        $headers[] = 'Content-Type: text/html';
                        $headers[] = 'Charset: ' . get_option('blog_charset', 'UTF-8');
                        wp_mail($to, $subject, $message, $headers);
                    }
                }
                catch (Exception $e) {
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                        crawlomatic_log_to_file('Failed to send mail: Exception thrown ' . esc_html($e->getMessage()) . '!');
                    }
                }
            }
        }
    }
    if ($posts_inserted == 0) {
        if($auto == 1)
        {
            crawlomatic_clearFromList($param);
        }
        return 'nochange';
    } else {
        if($auto == 1)
        {
            crawlomatic_clearFromList($param);
        }
        return 'ok';
    }
}

function crawlomatic_change_post_status($post_id, $status){
    $current_post = get_post( $post_id, 'ARRAY_A' );
    $current_post['post_status'] = $status;
    remove_filter('content_save_pre', 'wp_filter_post_kses');
    remove_filter('content_filtered_save_pre', 'wp_filter_post_kses');remove_filter('title_save_pre', 'wp_filter_kses');
    wp_update_post($current_post);
    add_filter('content_save_pre', 'wp_filter_post_kses');
    add_filter('content_filtered_save_pre', 'wp_filter_post_kses');add_filter('title_save_pre', 'wp_filter_kses');
}
function crawlomatic_stringMatchWithWildcard($source, $pattern) {
    $pattern = preg_quote($pattern,'/');        
    $pattern = str_replace( '\*' , '.*', $pattern);   
    return preg_match( '~' . $pattern . '~i' , $source );
}

function crawlomatic_add_link_tags($content) {
    $content = preg_replace_callback('~<(a\s[^>]+)>~isU', "crawlomatic_link_callback", preg_quote($content));
    return $content;
}
function crawlomatic_generate_random_string( $length = 16 ) {
    return substr( str_shuffle( str_repeat( $x = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil( $length / strlen( $x ) ) ) ), 1, $length );
}
function crawlomatic_link_callback($match) { 
    list($original, $tag) = $match;
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    $blog_url = get_home_url();
    $disallowed = array('http://', 'https://', 'www.');
    foreach($disallowed as $d) {
       $blog_url = str_replace($d, '', $blog_url);
    }
    if (stripos($tag, $blog_url) !== false) {
        if (isset($crawlomatic_Main_Settings['link_attributes_internal']) && $crawlomatic_Main_Settings['link_attributes_internal'] != '') {
            return "<$tag " . $crawlomatic_Main_Settings['link_attributes_internal'] . ">";
        }
    }
    else {
        if (isset($crawlomatic_Main_Settings['link_attributes_external']) && $crawlomatic_Main_Settings['link_attributes_external'] != '') {
            return "<$tag " . $crawlomatic_Main_Settings['link_attributes_external'] . ">";
        }
    }
    return $original;
}

$crawlomatic_fatal = false;
function crawlomatic_clear_flag_at_shutdown($param)
{
    $error = error_get_last();
    if (isset($error['type']) && $error['type'] === E_ERROR && $GLOBALS['crawlomatic_fatal'] === false) {
        $GLOBALS['crawlomatic_fatal'] = true;
        $running = array();
        update_option('crawlomatic_running_list', $running);
        crawlomatic_log_to_file('[FATAL] Exit error: ' . $error['message'] . ', file: ' . $error['file'] . ', line: ' . $error['line'] . ' - rule ID: ' . $param . '!');
        crawlomatic_clearFromList($param);
    }
    else
    {
        crawlomatic_clearFromList($param);
    }
}

function crawlomatic_strip_images($content)
{
    $content = preg_replace("/<img[^>]+\>/i", "", $content); 
    return $content;
}
function crawlomatic_get_url_domain($url) {
    $result = parse_url($url);
    if($result === false)
    {
        return $url;
    }
    return $result['scheme']."://".$result['host'];
}
function crawlomatic_strip_links($content)
{
    $content = preg_replace('~<a(?:[^>]*)>~', "", $content);
    $content = preg_replace('~<\/a>~', "", $content);
    return $content;
}
function crawlomatic_strip_external_links($content, $url)
{
    $parse = parse_url($url);
    if(isset($parse['host']) && $parse['host'] != '')
    {
        $content = preg_replace('#<a(?:[^>]*)href=[\'"]http.?:\/\/' . preg_quote($parse['host']) . '[^\'"]*?[\'"](?:[^>]*)>(.*?)<\/a>#', "\\1", $content);
    }
    return $content;
}
add_filter('the_title', 'crawlomatic_add_affiliate_title_keyword');
function crawlomatic_add_affiliate_title_keyword($content)
{
    $rules  = get_option('crawlomatic_keyword_list');
    if(!is_array($rules))
    {
       $rules = array();
    }
    $output = '';
    if (!empty($rules)) {
        foreach ($rules as $request => $value) {
            if(isset($value[2]) && $value[2] == 'content')
            {
                continue;
            }
            if (is_array($value) && isset($value[1]) && $value[1] != '') {
                $repl = stripslashes($value[1]);
            } else {
                $repl = stripslashes($request);
            }
            if (isset($value[0]) && $value[0] != '') {
                $content = preg_replace('\'(?!((<.*?)|(<a.*?)))(\b' . preg_quote(stripslashes($request)) . '\b)(?!(([^<>]*?)>)|([^>]*?<\/a>))\'i', '<a href="' . stripslashes($value[0]) . '" target="_blank">' . esc_html($repl) . '</a>', $content);
            } else {
                $content = preg_replace('\'(?!((<.*?)|(<a.*?)))(\b' . preg_quote(stripslashes($request)) . '\b)(?!(([^<>]*?)>)|([^>]*?<\/a>))\'i', esc_html($repl), $content);
            }
        }
    }
    return $content;
}
add_filter('the_content', 'crawlomatic_add_affiliate_content_keyword');
add_filter('the_excerpt', 'crawlomatic_add_affiliate_content_keyword');
function crawlomatic_add_affiliate_content_keyword($content)
{
    $rules  = get_option('crawlomatic_keyword_list');
    if(!is_array($rules))
    {
       $rules = array();
    }
    $output = '';
    if (!empty($rules)) {
        foreach ($rules as $request => $value) {
            if(isset($value[2]) && $value[2] == 'title')
            {
                continue;
            }
            if (is_array($value) && isset($value[1]) && $value[1] != '') {
                $repl = stripslashes($value[1]);
            } else {
                $repl = stripslashes($request);
            }
            if (isset($value[0]) && $value[0] != '') {
                $content = preg_replace('\'(?!((<.*?)|(<a.*?)))(\b' . preg_quote(stripslashes($request)) . '\b)(?!(([^<>]*?)>)|([^>]*?<\/a>))\'i', '<a href="' . stripslashes($value[0]) . '" target="_blank">' . esc_html($repl) . '</a>', $content);
            } else {
                $content = preg_replace('\'(?!((<.*?)|(<a.*?)))(\b' . preg_quote(stripslashes($request)) . '\b)(?!(([^<>]*?)>)|([^>]*?<\/a>))\'i', esc_html($repl), $content);
            }
        }
    }
    return $content;
}

function crawlomatic_meta_box_function($post)
{
    wp_register_style('crawlomatic-browser-style', plugins_url('styles/crawlomatic-browser.css', __FILE__), false, '1.0.0');
    wp_enqueue_style('crawlomatic-browser-style');
    wp_suspend_cache_addition(true);
    $index                 = get_post_meta($post->ID, 'crawlomatic_parent_rule', true);
    $title                 = get_post_meta($post->ID, 'crawlomatic_item_title', true);
    $cats                  = get_post_meta($post->ID, 'crawlomatic_extra_categories', true);
    $tags                  = get_post_meta($post->ID, 'crawlomatic_extra_tags', true);
    $img                   = get_post_meta($post->ID, 'crawlomatic_featured_img', true);
    $post_img              = get_post_meta($post->ID, 'crawlomatic_post_img', true);
    $crawlomatic_timestamp        = get_post_meta($post->ID, 'crawlomatic_timestamp', true);
    $crawlomatic_post_date        = get_post_meta($post->ID, 'crawlomatic_post_date', true);
    $crawlomatic_post_url         = get_post_meta($post->ID, 'crawlomatic_post_url', true);
    $crawlomatic_enable_pingbacks = get_post_meta($post->ID, 'crawlomatic_enable_pingbacks', true);
    $crawlomatic_comment_status   = get_post_meta($post->ID, 'crawlomatic_comment_status', true);
    $crawlomatic_delete_time      = get_post_meta($post->ID, 'crawlomatic_delete_time', true);
    
    if (isset($index) && $index != '') {
        $ech = '<table class="crf_table"><tr><td><b>' . esc_html__('Post Parent Rule:', 'crawlomatic-multipage-scraper-post-generator') . '</b></td><td>&nbsp;' . esc_html($index) . '</td></tr>';
        $ech .= '<tr><td><b>' . esc_html__('Post Original Title:', 'crawlomatic-multipage-scraper-post-generator') . '</b></td><td>&nbsp;' . esc_html($title) . '</td></tr>';
        if ($crawlomatic_timestamp != '') {
            $ech .= '<tr><td><b>' . esc_html__('Post Creation Date:', 'crawlomatic-multipage-scraper-post-generator') . '</b></td><td>&nbsp;' . esc_html($crawlomatic_timestamp) . '</td></tr>';
        }
        if ($cats != '') {
            $ech .= '<tr><td><b>' . esc_html__('Post Categories:', 'crawlomatic-multipage-scraper-post-generator') . '</b></td><td>&nbsp;' . esc_html($cats) . '</td></tr>';
        }
        if ($tags != '') {
            $ech .= '<tr><td><b>' . esc_html__('Post Tags:', 'crawlomatic-multipage-scraper-post-generator') . '</b></td><td>&nbsp;' . esc_html($tags) . '</td></tr>';
        }
        if ($img != '') {
            $ech .= '<tr><td><b>' . esc_html__('Featured Image:', 'crawlomatic-multipage-scraper-post-generator') . '</b></td><td>&nbsp;' . esc_url($img) . '</td></tr>';
        }
        if ($post_img != '') {
            $ech .= '<tr><td><b>' . esc_html__('Post Image:', 'crawlomatic-multipage-scraper-post-generator') . '</b></td><td>&nbsp;' . esc_url($post_img) . '</td></tr>';
        }
        if ($crawlomatic_post_date != '') {
            $ech .= '<tr><td><b>' . esc_html__('Item Source URL Date:', 'crawlomatic-multipage-scraper-post-generator') . '</b></td><td>&nbsp;' . esc_html($crawlomatic_post_date) . '</td></tr>';
        }
        if ($crawlomatic_post_url != '') {
            $ech .= '<tr><td><b>' . esc_html__('Item Source URL:', 'crawlomatic-multipage-scraper-post-generator') . '</b></td><td>&nbsp;' . esc_url($crawlomatic_post_url) . '</td></tr>';
        }
        if ($crawlomatic_enable_pingbacks != '') {
            $ech .= '<tr><td><b>' . esc_html__('Pingback/Trackback Status:', 'crawlomatic-multipage-scraper-post-generator') . '</b></td><td>&nbsp;' . esc_html($crawlomatic_enable_pingbacks) . '</td></tr>';
        }
        if ($crawlomatic_comment_status != '') {
            $ech .= '<tr><td><b>' . esc_html__('Comment Status:', 'crawlomatic-multipage-scraper-post-generator') . '</b></td><td>&nbsp;' . esc_html($crawlomatic_comment_status) . '</td></tr>';
        }
        if ($crawlomatic_delete_time != '') {
            $ech .= '<tr><td><b>Auto Delete Post:</b></td><td>&nbsp;' . gmdate("Y-m-d H:i:s", intval($crawlomatic_delete_time)) . '</td></tr>';
        }
        $ech .= '</table><br/>';
    } else {
        $ech = esc_html__('This is not an automatically generated post.', 'crawlomatic-multipage-scraper-post-generator');
    }
    echo $ech;
    wp_suspend_cache_addition(false);
}
foreach( [ 'post', 'page', 'post_type' ] as $type )
{
    add_filter($type . '_link','crawlomatic_permalink_changer', 10, 2 );
}
add_filter('the_permalink','crawlomatic_permalink_changer', 10, 2 );
function crawlomatic_permalink_changer($link, $postid = ''){
	$le_post_id = '';
    if(is_numeric($postid))
    {
        $le_post_id = $postid;
    }
    elseif(isset($postid->ID))
    {
        $le_post_id = $postid->ID;
    }
    else
    {
        global $post;
        if(isset($post->ID))
        {
            $le_post_id = $post->ID;
        }
    }
	if (!empty($le_post_id)) {
        $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
        if (isset($crawlomatic_Main_Settings['crawlomatic_enabled']) && $crawlomatic_Main_Settings['crawlomatic_enabled'] == 'on') {
            if (isset($crawlomatic_Main_Settings['link_source']) && $crawlomatic_Main_Settings['link_source'] == 'on') {
                $url = get_post_meta($le_post_id, 'crawlomatic_change_title_link', true);
                if ( trim($url) == '1')
                {
                    $new_url = get_post_meta($le_post_id, 'crawlomatic_post_url', true);
                    if(trim($new_url) != '') {
                        return $new_url;
                    }
                }
            }
        }
	}
	return $link;
}
function crawlomatic_addPostMeta($post_id, $post, $param, $featured_img, $title_url, $css_cont, $crawlomatic_Main_Settings)
{
    add_post_meta($post_id, 'crawlomatic_parent_rule', $param);
    if (!isset($crawlomatic_Main_Settings['crawlomatic_enable_pingbacks']) || $crawlomatic_Main_Settings['crawlomatic_enable_pingbacks'] != 'on') {
        add_post_meta($post_id, 'crawlomatic_enable_pingbacks', $post['crawlomatic_enable_pingbacks']);
    }
    if (!isset($crawlomatic_Main_Settings['crawlomatic_comment_status']) || $crawlomatic_Main_Settings['crawlomatic_comment_status'] != 'on') {
        add_post_meta($post_id, 'crawlomatic_comment_status', $post['comment_status']);
    }
    if (!isset($crawlomatic_Main_Settings['crawlomatic_item_title']) || $crawlomatic_Main_Settings['crawlomatic_item_title'] != 'on') {
        add_post_meta($post_id, 'crawlomatic_item_title', $post['original_title']);
    }
    if (!isset($crawlomatic_Main_Settings['crawlomatic_extra_categories']) || $crawlomatic_Main_Settings['crawlomatic_extra_categories'] != 'on') {
        add_post_meta($post_id, 'crawlomatic_extra_categories', $post['extra_categories']);
    }
    if (!isset($crawlomatic_Main_Settings['crawlomatic_extra_tags']) || $crawlomatic_Main_Settings['crawlomatic_extra_tags'] != 'on') {
        add_post_meta($post_id, 'crawlomatic_extra_tags', $post['extra_tags']);
    }
    if (!isset($crawlomatic_Main_Settings['crawlomatic_post_img']) || $crawlomatic_Main_Settings['crawlomatic_post_img'] != 'on') {
        add_post_meta($post_id, 'crawlomatic_post_img', $post['crawlomatic_post_image']);
    }
    add_post_meta($post_id, 'crawlomatic_featured_img', $featured_img);
    if (!isset($crawlomatic_Main_Settings['crawlomatic_timestamp']) || $crawlomatic_Main_Settings['crawlomatic_timestamp'] != 'on') {
        add_post_meta($post_id, 'crawlomatic_timestamp', $post['crawlomatic_timestamp']);
    }
    add_post_meta($post_id, 'crawlomatic_post_url', $post['crawlomatic_post_url']);
    add_post_meta($post_id, 'crawlomatic_post_orig_url', $post['crawlomatic_post_orig_url']);
    if (!isset($crawlomatic_Main_Settings['crawlomatic_post_date']) || $crawlomatic_Main_Settings['crawlomatic_post_date'] != 'on') {
        add_post_meta($post_id, 'crawlomatic_post_date', $post['crawlomatic_post_date']);
    }
    if($css_cont != '')
    {
        add_post_meta($post_id, 'crawlomatic_css_cont', $css_cont);
    }
    if($post['auto_delete'] != '' && is_numeric($post['auto_delete']))
    {
        add_post_meta($post_id, 'crawlomatic_delete_time', intval($post['auto_delete']));
    }
    if($title_url == '1')
    {
        add_post_meta($post_id, 'crawlomatic_change_title_link', '1');
    }
}

function crawlomatic_generate_featured_image($image_url, $post_id, $use_proxy, $request_delay, $custom_user_agent, $custom_cookies, $user_pass)
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    global $wp_filesystem;
    if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
        include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
        wp_filesystem($creds);
    }
    $upload_dir = wp_upload_dir();
    if(!function_exists('is_plugin_active'))
    {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
    if (isset($crawlomatic_Main_Settings['no_local_image']) && $crawlomatic_Main_Settings['no_local_image'] == 'on') {
        
        if(!crawlomatic_url_is_image($image_url))
        {
            return false;
        }
        
        $file = $upload_dir['basedir'] . '/default_img_crawlomatic.jpg';
        if(!$wp_filesystem->exists($file))
        {
            $image_data = crawlomatic_get_web_page(html_entity_decode(dirname(__FILE__) . "/images/icon.png"), '', '', $use_proxy, '', '', '', '');
            if ($image_data === FALSE || strpos($image_data, '<Message>Access Denied</Message>') !== FALSE) {
                return false;
            }
            $ret = $wp_filesystem->put_contents($file, $image_data);
            if ($ret === FALSE) {
                return false;
            }
        }
        $need_attach = false;
        $checking_id = get_option('crawlomatic_attach_id', false);
        if($checking_id === false)
        {
            $need_attach = true;
        }
        else
        {
            $atturl = wp_get_attachment_url($checking_id);
            if($atturl === false)
            {
                $need_attach = true;
            }
        }
        if($need_attach)
        {
            $filename = basename(dirname(__FILE__) . "/images/icon.png");
            $wp_filetype = wp_check_filetype($filename, null);
            if($wp_filetype['type'] == '')
            {
                $wp_filetype['type'] = 'image/png';
            }
            $attachment  = array(
                'post_mime_type' => $wp_filetype['type'],
                'post_title' => sanitize_file_name($filename),
                'post_content' => '',
                'post_status' => 'inherit'
            );
            
            $attach_id   = wp_insert_attachment($attachment, $file, $post_id);
            if ($attach_id === 0) {
                return false;
            }
            update_option('crawlomatic_attach_id', $attach_id);
            require_once(ABSPATH . 'wp-admin/includes/image.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            $attach_data = wp_generate_attachment_metadata($attach_id, $file);
            wp_update_attachment_metadata($attach_id, $attach_data);
        }
        else
        {
            $attach_id = $checking_id;
        }
        $res2 = set_post_thumbnail($post_id, $attach_id);
        if ($res2 === FALSE) {
            return false;
        }
        $post_title = get_the_title($post_id);
        if($post_title != '')
        {
            update_post_meta($attach_id, '_wp_attachment_image_alt', $post_title);
        }
        return true;
    }
    elseif (isset($crawlomatic_Main_Settings['url_image']) && $crawlomatic_Main_Settings['url_image'] == 'on' && (is_plugin_active('featured-image-from-url/featured-image-from-url.php') || is_plugin_active('fifu-premium/fifu-premium.php')))
    {
        if(!crawlomatic_url_is_image($image_url))
        {
            crawlomatic_log_to_file('Provided remote image is not valid: ' . $image_url);
            return false;
        }
        
        if(function_exists('fifu_dev_set_image'))
        {
            fifu_dev_set_image($post_id, $image_url);
        }
        else
        {
            $value = crawlomatic_get_formatted_value($image_url, '', $post_id);
            $attach_id = crawlomatic_insert_attachment_by($value);
            update_post_meta($post_id, '_thumbnail_id', $attach_id);
            update_post_meta($post_id, 'fifu_image_url', $image_url);
            update_post_meta($attach_id, '_wp_attached_file', ';' . $image_url);
            $attach = get_post( $attach_id );
            if($attach !== null)
            {
                $attach->post_author = 77777;
                wp_update_post( $attach );
            }
        }
        return true;
    }
    if(substr( $image_url, 0, 10 ) === "data:image")
    {
        $data = explode(',', $image_url);
        if(isset($data[1]))
        {
            $image_data = base64_decode($data[1]);
            if($image_data === FALSE)
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        preg_match('{data:image/(.*?);}', $image_url ,$ex_matches);
        if(isset($ex_matches[1]))
        {
            $image_url = 'image.' . $ex_matches[1];
        }
        else
        {
            $image_url = 'image.jpg';
        }
    }
    else
    {
        $image_data = crawlomatic_get_web_page(html_entity_decode($image_url), $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, '', '', $request_delay);
        if ($image_data === FALSE || strpos($image_data, '<Message>Access Denied</Message>') !== FALSE) {
            return false;
        }
    }
    $filename = basename($image_url);
    $filename = explode("?", $filename);
    $filename = $filename[0];
    $filename = urlencode($filename);
    $filename = str_replace('%', '-', $filename);
    $filename = str_replace('#', '-', $filename);
    $filename = str_replace('&', '-', $filename);
    $filename = str_replace('{', '-', $filename);
    $filename = str_replace('}', '-', $filename);
    $filename = str_replace('\\', '-', $filename);
    $filename = str_replace('<', '-', $filename);
    $filename = str_replace('>', '-', $filename);
    $filename = str_replace('*', '-', $filename);
    $filename = str_replace('/', '-', $filename);
    $filename = str_replace('$', '-', $filename);
    $filename = str_replace('\'', '-', $filename);
    $filename = str_replace('"', '-', $filename);
    $filename = str_replace(':', '-', $filename);
    $filename = str_replace('@', '-', $filename);
    $filename = str_replace('+', '-', $filename);
    $filename = str_replace('|', '-', $filename);
    $filename = str_replace('=', '-', $filename);
    $filename = str_replace('`', '-', $filename);
    $file_parts = pathinfo($filename);
    if(isset($file_parts['extension']))
    {
        switch($file_parts['extension'])
        {
            case "":
            $filename .= '.jpg';
            break;
            case NULL:
            $filename .= '.jpg';
            break;
        }
    }
    else
    {
        $filename .= '.jpg';
    }
    $filename = stripslashes(preg_replace_callback('#(%[a-zA-Z0-9_]*)#', function($matches){ return rand(0, 9); }, preg_quote($filename)));
    $filename = sanitize_file_name($filename);
    if (isset($crawlomatic_Main_Settings['random_image_names']) && $crawlomatic_Main_Settings['random_image_names'] != '') {
        $filename = uniqid() . '.jpg';
    }
    if (wp_mkdir_p($upload_dir['path']))
        $file = $upload_dir['path'] . '/' . $post_id . '-' . $filename;
    else
        $file = $upload_dir['basedir'] . '/' . $post_id . '-' . $filename;
    if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
        include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
        wp_filesystem($creds);
    }
    $ret = $wp_filesystem->put_contents($file, $image_data);
    if ($ret === FALSE) {
        return false;
    }
    if ((isset($crawlomatic_Main_Settings['resize_height']) && $crawlomatic_Main_Settings['resize_height'] !== '') || (isset($crawlomatic_Main_Settings['resize_width']) && $crawlomatic_Main_Settings['resize_width'] !== ''))
    {
        try
        {
            if(!class_exists('\Eventviva\ImageResize')){require_once (dirname(__FILE__) . "/res/ImageResize/ImageResize.php");}
            $imageRes = new ImageResize($file);
            $imageRes->quality_jpg = 100;
            if ((isset($crawlomatic_Main_Settings['resize_height']) && $crawlomatic_Main_Settings['resize_height'] !== '') && (isset($crawlomatic_Main_Settings['resize_width']) && $crawlomatic_Main_Settings['resize_width'] !== ''))
            {
                $imageRes->resizeToBestFit($crawlomatic_Main_Settings['resize_width'], $crawlomatic_Main_Settings['resize_height'], true);
            }
            elseif (isset($crawlomatic_Main_Settings['resize_width']) && $crawlomatic_Main_Settings['resize_width'] !== '')
            {
                $imageRes->resizeToWidth($crawlomatic_Main_Settings['resize_width'], true);
            }
            elseif (isset($crawlomatic_Main_Settings['resize_height']) && $crawlomatic_Main_Settings['resize_height'] !== '')
            {
                $imageRes->resizeToHeight($crawlomatic_Main_Settings['resize_height'], true);
            }
            $imageRes->save($file);
        }
        catch(Exception $e)
        {
            crawlomatic_log_to_file('Failed to resize featured image: ' . $image_url . ' to sizes ' . $crawlomatic_Main_Settings['resize_width'] . ' - ' . $crawlomatic_Main_Settings['resize_height'] . '. Exception thrown ' . esc_html($e->getMessage()) . '!');
        }
    }
    $wp_filetype = wp_check_filetype($filename, null);
    if($wp_filetype['type'] == '')
    {
        $wp_filetype['type'] = 'image/png';
    }
    $attachment  = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id   = wp_insert_attachment($attachment, $file, $post_id);
    if ($attach_id === 0) {
        return false;
    }
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    wp_update_attachment_metadata($attach_id, $attach_data);
    $res2 = set_post_thumbnail($post_id, $attach_id);
    if ($res2 === FALSE) {
        return false;
    }
    $post_title = get_the_title($post_id);
    if($post_title != '')
    {
        update_post_meta($attach_id, '_wp_attachment_image_alt', $post_title);
    }
    return true;
}

function crawlomatic_upload_attachment_media($image_url, $post_id, $use_proxy, $request_delay, $custom_user_agent, $custom_cookies, $user_pass, $counter)
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    global $wp_filesystem;
    if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
        include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
        wp_filesystem($creds);
    }
    $upload_dir = wp_upload_dir();
    if(!function_exists('is_plugin_active'))
    {
        include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
    }
    if(substr( $image_url, 0, 10 ) === "data:image")
    {
        $data = explode(',', $image_url);
        if(isset($data[1]))
        {
            $image_data = base64_decode($data[1]);
            if($image_data === FALSE)
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        preg_match('{data:image/(.*?);}', $image_url ,$ex_matches);
        if(isset($ex_matches[1]))
        {
            $image_url = 'image.' . $ex_matches[1];
        }
        else
        {
            $image_url = 'image.jpg';
        }
    }
    else
    {
        $image_data = crawlomatic_get_web_page(html_entity_decode($image_url), $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, '', '', $request_delay);
        if ($image_data === FALSE || strpos($image_data, '<Message>Access Denied</Message>') !== FALSE) {
            return false;
        }
    }
    $filename = basename($image_url);
    $filename = explode("?", $filename);
    $filename = $filename[0];
    $filename = urlencode($filename);
    $filename = str_replace('%', '-', $filename);
    $filename = str_replace('#', '-', $filename);
    $filename = str_replace('&', '-', $filename);
    $filename = str_replace('{', '-', $filename);
    $filename = str_replace('}', '-', $filename);
    $filename = str_replace('\\', '-', $filename);
    $filename = str_replace('<', '-', $filename);
    $filename = str_replace('>', '-', $filename);
    $filename = str_replace('*', '-', $filename);
    $filename = str_replace('/', '-', $filename);
    $filename = str_replace('$', '-', $filename);
    $filename = str_replace('\'', '-', $filename);
    $filename = str_replace('"', '-', $filename);
    $filename = str_replace(':', '-', $filename);
    $filename = str_replace('@', '-', $filename);
    $filename = str_replace('+', '-', $filename);
    $filename = str_replace('|', '-', $filename);
    $filename = str_replace('=', '-', $filename);
    $filename = str_replace('`', '-', $filename);
    $file_parts = pathinfo($filename);
    if(isset($file_parts['extension']))
    {
        switch($file_parts['extension'])
        {
            case "":
            $filename .= '.jpg';
            break;
            case NULL:
            $filename .= '.jpg';
            break;
        }
    }
    else
    {
        $filename .= '.jpg';
    }
    $filename = stripslashes(preg_replace_callback('#(%[a-zA-Z0-9_]*)#', function($matches){ return rand(0, 9); }, preg_quote($filename)));
    $filename = sanitize_file_name($filename);
    if (isset($crawlomatic_Main_Settings['random_image_names']) && $crawlomatic_Main_Settings['random_image_names'] != '') {
        $filename = uniqid() . '.jpg';
    }
    if (wp_mkdir_p($upload_dir['path']))
        $file = $upload_dir['path'] . '/' . $post_id . '-' . $filename;
    else
        $file = $upload_dir['basedir'] . '/' . $post_id . '-' . $filename;
    if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
        include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
        wp_filesystem($creds);
    }
    $ret = $wp_filesystem->put_contents($file, $image_data);
    if ($ret === FALSE) {
        return false;
    }
    if ((isset($crawlomatic_Main_Settings['resize_height']) && $crawlomatic_Main_Settings['resize_height'] !== '') || (isset($crawlomatic_Main_Settings['resize_width']) && $crawlomatic_Main_Settings['resize_width'] !== ''))
    {
        try
        {
            if(!class_exists('\Eventviva\ImageResize')){require_once (dirname(__FILE__) . "/res/ImageResize/ImageResize.php");}
            $imageRes = new ImageResize($file);
            $imageRes->quality_jpg = 100;
            if ((isset($crawlomatic_Main_Settings['resize_height']) && $crawlomatic_Main_Settings['resize_height'] !== '') && (isset($crawlomatic_Main_Settings['resize_width']) && $crawlomatic_Main_Settings['resize_width'] !== ''))
            {
                $imageRes->resizeToBestFit($crawlomatic_Main_Settings['resize_width'], $crawlomatic_Main_Settings['resize_height'], true);
            }
            elseif (isset($crawlomatic_Main_Settings['resize_width']) && $crawlomatic_Main_Settings['resize_width'] !== '')
            {
                $imageRes->resizeToWidth($crawlomatic_Main_Settings['resize_width'], true);
            }
            elseif (isset($crawlomatic_Main_Settings['resize_height']) && $crawlomatic_Main_Settings['resize_height'] !== '')
            {
                $imageRes->resizeToHeight($crawlomatic_Main_Settings['resize_height'], true);
            }
            $imageRes->save($file);
        }
        catch(Exception $e)
        {
            crawlomatic_log_to_file('Failed to resize gallery image: ' . $image_url . ' to sizes ' . $crawlomatic_Main_Settings['resize_width'] . ' - ' . $crawlomatic_Main_Settings['resize_height'] . '. Exception thrown ' . esc_html($e->getMessage()) . '!');
        }
    }
    $wp_filetype = wp_check_filetype($filename, null);
    if($wp_filetype['type'] == '')
    {
        $wp_filetype['type'] = 'image/png';
    }
    $attachment  = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name($filename),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $attach_id   = wp_insert_attachment($attachment, $file, $post_id);
    if ($attach_id === 0) {
        return false;
    }
    require_once(ABSPATH . 'wp-admin/includes/image.php');
    require_once(ABSPATH . 'wp-admin/includes/media.php');
    $attach_data = wp_generate_attachment_metadata($attach_id, $file);
    wp_update_attachment_metadata($attach_id, $attach_data);

    $post_title = get_the_title($post_id);
    if($post_title != '')
    {
        update_post_meta($attach_id, '_wp_attachment_image_alt', $post_title . ' ' . $counter);
    }
    return $attach_id;
}

function crawlomatic_insert_attachment_by($value) {
    global $wpdb;
    $wpdb->get_results("
        INSERT INTO " . $wpdb->prefix . "posts" . " (post_author, guid, post_title, post_mime_type, post_type, post_status, post_parent, post_date, post_date_gmt, post_modified, post_modified_gmt, post_content, post_excerpt, to_ping, pinged, post_content_filtered) 
        VALUES " . $value);
    return $wpdb->insert_id;
}
function crawlomatic_get_formatted_value($url, $alt, $post_parent) {
    return "(77777, '" . $url . "', '" . str_replace("'", "", $alt) . "', 'image/jpeg', 'attachment', 'inherit', '" . $post_parent . "', now(), now(), now(), now(), '', '', '', '', '')";
}
function crawlomatic_copy_image_locally($image_url, $use_proxy, $request_delay, $custom_user_agent, $user_pass, $custom_cookies)
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    global $wp_filesystem;
    if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
        include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
        wp_filesystem($creds);
    }
    $upload_dir = wp_upload_dir();
    if(substr( $image_url, 0, 10 ) === "data:image")
    {
        $delay = '';
        if (isset($crawlomatic_Main_Settings['request_delay']) && $crawlomatic_Main_Settings['request_delay'] != '') 
        {
            if(stristr($crawlomatic_Main_Settings['request_delay'], ',') !== false)
            {
                $tempo = explode(',', $crawlomatic_Main_Settings['request_delay']);
                if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
                {
                    $delay = rand(trim($tempo[0]), trim($tempo[1]));
                }
            }
            else
            {
                if(is_numeric(trim($crawlomatic_Main_Settings['request_delay'])))
                {
                    $delay = intval(trim($crawlomatic_Main_Settings['request_delay']));
                }
            }
        }
        if ($request_delay != '') 
        {
            if(stristr($request_delay, ',') !== false)
            {
                $tempo = explode(',', $request_delay);
                if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
                {
                    $delay = rand(trim($tempo[0]), trim($tempo[1]));
                }
            }
            else
            {
                if(is_numeric(trim($request_delay)))
                {
                    $delay = intval(trim($request_delay));
                }
            }
        }
        if($delay != '' && is_numeric($delay))
        {
            $GLOBALS['wp_object_cache']->delete('crawlomatic_last_time', 'options');
            $last_time = get_option('crawlomatic_last_time', false);
            if($last_time !== false && intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000) > 0)
            {
                $sleep_time = intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000);
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                {
                    crawlomatic_log_to_file('Delay between requests set, waiting ' . ($sleep_time/1000) . ' ms');
                }
                usleep($sleep_time);
            }
        }
        
        $data = explode(',', $image_url);
        if(isset($data[1]))
        {
            $image_data = base64_decode($data[1]);
            if($image_data === FALSE)
            {
                return false;
            }
        }
        else
        {
            return false;
        }
        preg_match('{data:image/(.*?);}', $image_url ,$ex_matches);
        if(isset($ex_matches[1]))
        {
            $image_url = 'image.' . $ex_matches[1];
        }
        else
        {
            $image_url = 'image.jpg';
        }
    }
    else
    {
        $image_data = crawlomatic_get_web_page(html_entity_decode($image_url), $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, '', '', $request_delay);
        if ($image_data === FALSE || strpos($image_data, '<Message>Access Denied</Message>') !== FALSE) {
            return false;
        }
    }
    $filename = basename($image_url);
    $filename = explode("?", $filename);
    $filename = $filename[0];
    $filename = urlencode($filename);
    $filename = str_replace('%', '-', $filename);
    $filename = str_replace('#', '-', $filename);
    $filename = str_replace('&', '-', $filename);
    $filename = str_replace('{', '-', $filename);
    $filename = str_replace('}', '-', $filename);
    $filename = str_replace('\\', '-', $filename);
    $filename = str_replace('<', '-', $filename);
    $filename = str_replace('>', '-', $filename);
    $filename = str_replace('*', '-', $filename);
    $filename = str_replace('/', '-', $filename);
    $filename = str_replace('$', '-', $filename);
    $filename = str_replace('\'', '-', $filename);
    $filename = str_replace('"', '-', $filename);
    $filename = str_replace(':', '-', $filename);
    $filename = str_replace('@', '-', $filename);
    $filename = str_replace('+', '-', $filename);
    $filename = str_replace('|', '-', $filename);
    $filename = str_replace('=', '-', $filename);
    $filename = str_replace('`', '-', $filename);
    $file_parts = pathinfo($filename);
    switch($file_parts['extension'])
    {
        case "":
        $filename .= 'jpg';
        break;
        case NULL:
        $filename .= '.jpg';
        break;
    }
    if (isset($crawlomatic_Main_Settings['random_image_names']) && $crawlomatic_Main_Settings['random_image_names'] != '') {
        $unid = uniqid();
        $file = $upload_dir['basedir'] . '/' . $unid . '.jpg';
        $ret_path = $upload_dir['baseurl'] . '/' . $unid . '.jpg';
    }
    else
    {
        if (wp_mkdir_p($upload_dir['path'] . '/localimages'))
        {
            $file = $upload_dir['path'] . '/localimages/' . $filename;
            $ret_path = $upload_dir['url'] . '/localimages/' . $filename;
        }
        else
        {
            $file = $upload_dir['basedir'] . '/' . $filename;
            $ret_path = $upload_dir['baseurl'] . '/' . $filename;
        }
    }
    if($wp_filesystem->exists($file))
    {
        $unid = uniqid();
        $file .= $unid . '.jpg';
        $ret_path .= $unid . '.jpg';
    }
    
    $ret = $wp_filesystem->put_contents($file, $image_data);
    if ($ret === FALSE) {
        return false;
    }
    $wp_filetype = wp_check_filetype( $file, null );
    $attachment = array(
        'post_mime_type' => $wp_filetype['type'],
        'post_title' => sanitize_file_name( $file ),
        'post_content' => '',
        'post_status' => 'inherit'
    );
    $screens_attach_id = wp_insert_attachment( $attachment, $file );
    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
    $attach_data = wp_generate_attachment_metadata( $screens_attach_id, $file );
    wp_update_attachment_metadata( $screens_attach_id, $attach_data );
    return $ret_path;
}

function crawlomatic_url_is_image( $url ) {
    $url = str_replace(' ', '%20', $url);
    if ( ! filter_var( $url, FILTER_VALIDATE_URL ) ) {
        return FALSE;
    }
    $ext = array( 'jpeg', 'jpg', 'gif', 'png', 'jpe', 'tif', 'tiff', 'svg', 'ico' , 'webp', 'dds', 'heic', 'psd', 'pspimage', 'tga', 'thm', 'yuv', 'ai', 'eps', 'php');
    $info = (array) pathinfo( parse_url( $url, PHP_URL_PATH ) );
    if(!isset( $info['extension'] ))
    {
        return true;
    }
    return isset( $info['extension'] )
        && in_array( strtolower( $info['extension'] ), $ext, TRUE );
}


function crawlomatic_preg_grep_keys( $pattern, $input, $flags = 0 )
{
    if(!is_array($input))
    {
        return array();
    }
    $keys = preg_grep( $pattern, array_keys( $input ), $flags );
    $vals = array();
    foreach ( $keys as $key )
    {
        $vals[$key] = $input[$key];
    }
    return $vals;
}

function crawlomatic_replace_attachment_url($att_url, $att_id) {
    {
         $post_id = get_the_ID();
         wp_suspend_cache_addition(true);
         $metas = get_post_custom($post_id);
         wp_suspend_cache_addition(false);
         $rez_meta = crawlomatic_preg_grep_keys('#.+?_featured_img#i', $metas);
         if(count($rez_meta) > 0)
         {
             foreach($rez_meta as $rm)
             {
                 if(isset($rm[0]) && $rm[0] != '' && filter_var($rm[0], FILTER_VALIDATE_URL))
                 {
                    return $rm[0];
                 }
             }
         }
    }
    return $att_url;
}

function crawlomatic_replace_attachment_image_src($image, $att_id, $size) 
{
    $post_id = get_the_ID();
    wp_suspend_cache_addition(true);
    $metas = get_post_custom($post_id);
    wp_suspend_cache_addition(false);
    $rez_meta = crawlomatic_preg_grep_keys('#.+?_featured_img#i', $metas);
    if(count($rez_meta) > 0)
    {
        foreach($rez_meta as $rm)
        {
            if(isset($rm[0]) && $rm[0] != '' && filter_var($rm[0], FILTER_VALIDATE_URL))
            {
                return array($rm[0], 0, 0, false);
            }
        }
    }
    return $image;
}

function crawlomatic_thumbnail_external_replace( $html, $post_id, $thumb_id ) 
{
    wp_suspend_cache_addition(true);
    $metas = get_post_custom($post_id);
    wp_suspend_cache_addition(false);
    $rez_meta = crawlomatic_preg_grep_keys('#.+?_featured_img#i', $metas);
    if(count($rez_meta) > 0)
    {
        foreach($rez_meta as $rm)
        {
            if(isset($rm[0]) && $rm[0] != '' && filter_var($rm[0], FILTER_VALIDATE_URL))
            {
                $alt = get_post_field( 'post_title', $post_id ) . ' ' .  esc_html__( 'thumbnail', 'crawlomatic-multipage-scraper-post-generator' );
                $attr = array( 'alt' => $alt );
                $attx = get_post($thumb_id);
                $attr = apply_filters( 'wp_get_attachment_image_attributes', $attr, $attx , 'thumbnail');
                $attr = array_map( 'esc_attr', $attr );
                $html = sprintf( '<img src="%s"', esc_url($rm[0]) );
                foreach ( $attr as $name => $value ) {
                    $html .= " " . esc_html($name) . "=" . '"' . esc_attr($value) . '"';
                }
                $html .= ' />';
                return $html;
            }
        }
    }
    return $html;
}

function crawlomatic_hour_diff($date1, $date2)
{
    $date1 = new DateTime($date1, crawlomatic_get_blog_timezone());
    $date2 = new DateTime($date2, crawlomatic_get_blog_timezone());
    $number1 = (int) $date1->format('U');
    $number2 = (int) $date2->format('U');
    return ($number1 - $number2) / 60;
}

function crawlomatic_add_hour($date, $hour)
{
    $date1 = new DateTime($date, crawlomatic_get_blog_timezone());
    $date1->modify("$hour hours");
    $date1 = (array)$date1;
    foreach ($date1 as $key => $value) {
        if ($key == 'date') {
            return $value;
        }
    }
    return $date;
}

function crawlomatic_minute_diff($date1, $date2)
{
    $date1 = new DateTime($date1, crawlomatic_get_blog_timezone());
    $date2 = new DateTime($date2, crawlomatic_get_blog_timezone());
    
    $number1 = (int) $date1->format('U');
    $number2 = (int) $date2->format('U');
    return ($number1 - $number2);
}

function crawlomatic_add_minute($date, $minute)
{
    $date1 = new DateTime($date, crawlomatic_get_blog_timezone());
    $date1->modify("$minute minutes");
    $date1 = (array)$date1;
    foreach ($date1 as $key => $value) {
        if ($key == 'date') {
            return $value;
        }
    }
    return $date;
}

function crawlomatic_wp_custom_css_files($src, $cont)
{
    wp_enqueue_style('crawlomatic-thumbnail-css-' . $cont, $src, __FILE__);
}

function crawlomatic_get_date_now($param = 'now')
{
    $date = new DateTime($param, crawlomatic_get_blog_timezone());
    $date = (array)$date;
    foreach ($date as $key => $value) {
        if ($key == 'date') {
            return $value;
        }
    }
    return '';
}

function crawlomatic_create_terms($taxonomy, $parent, $terms_str, $remove_cats)
{
    if($remove_cats != '')
    {
        $remove_cats = explode(',', $remove_cats);
    }
    else
    {
        $remove_cats = array();
    }
    $terms          = explode(',', $terms_str);
    $categories     = array();
    $parent_term_id = $parent;
    foreach ($terms as $term) {
        $term = trim($term);
        $skip = false;
        foreach($remove_cats as $skip)
        {
            if(strcasecmp(trim($skip), $term) == 0)
            {
                $skip = true;
                break;
            }
        }
        if($skip === true)
        {
            continue;
        }
        $res = term_exists($term, $taxonomy, $parent);
        if ($res != NULL && $res != 0 && count($res) > 0 && isset($res['term_id'])) {
            $parent_term_id = $res['term_id'];
            $categories[]   = $parent_term_id;
        } else {
            $new_term = wp_insert_term($term, $taxonomy, array(
                'parent' => $parent
            ));
            if (!is_wp_error( $new_term ) && $new_term != NULL && $new_term != 0 && count($new_term) > 0 && isset($new_term['term_id'])) {
                $parent_term_id = $new_term['term_id'];
                $categories[]   = $parent_term_id;
            }
        }
    }
    
    return $categories;
}
function crawlomatic_getExcerpt($the_content)
{
    $preview = crawlomatic_strip_html_tags($the_content);
    $preview = crawlomatic_wp_trim_words($preview, 55);
    return $preview;
}

function crawlomatic_getPlainContent($the_content)
{
    $preview = crawlomatic_strip_html_tags($the_content);
    $preview = crawlomatic_wp_trim_words($preview, 999999);
    return $preview;
}
function crawlomatic_getItemImage($img, $just_title)
{
    if($img == '')
    {
        return '';
    }
    $preview = '<img src="' . esc_url($img) . '" alt="' . esc_html($just_title) . '" />';
    return $preview;
}

function crawlomatic_getReadMoreButton($url, $read_more)
{
    $link = '';
    if($read_more == ' '){
        return '';
    }
    if (isset($url)) {
        $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
        if($read_more == '')
        {
            if (isset($crawlomatic_Main_Settings['read_more_text']) && $crawlomatic_Main_Settings['read_more_text'] != '') {
                $read_more = $crawlomatic_Main_Settings['read_more_text'];
            }
            else
            {
                $read_more = esc_html__('Read More', 'crawlomatic-multipage-scraper-post-generator');
            }
        }
        $link = '<a rel="nofollow noopener" href="' . esc_url($url) . '" class="button purchase" target="_blank">' . esc_html($read_more) . '</a>';
    }
    return $link;
}


add_action('init', 'crawlomatic_create_taxonomy', 0);
add_action( 'enqueue_block_editor_assets', 'crawlomatic_enqueue_block_editor_assets' );
function crawlomatic_enqueue_block_editor_assets() {
	wp_register_style('crawlomatic-browser-style', plugins_url('styles/crawlomatic-browser.css', __FILE__), false, '1.0.0');
    wp_enqueue_style('crawlomatic-browser-style');
	$block_js_display   = 'scripts/display-posts.js';
	wp_enqueue_script(
		'crawlomatic-display-block-js', 
        plugins_url( $block_js_display, __FILE__ ), 
        array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
        '1.0.0'
	);
    $block_js_list   = 'scripts/list-posts.js';
	wp_enqueue_script(
		'crawlomatic-list-block-js', 
        plugins_url( $block_js_list, __FILE__ ), 
        array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
        '1.0.0'
	);
    $block_js_list   = 'scripts/crawler.js';
	wp_enqueue_script(
		'crawlomatic-crawler-gut-js', 
        plugins_url( $block_js_list, __FILE__ ), 
        array(
			'wp-blocks',
			'wp-i18n',
			'wp-element',
		),
        '1.0.0'
	);
}
function crawlomatic_create_taxonomy()
{
    add_shortcode('crawlomatic-scraper', array( 'Crawlomatic_Shortcode_Scraper', 'shortcode' ));
    add_filter('widget_text', 'do_shortcode');	
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (isset($crawlomatic_Main_Settings['crawlomatic_enabled']) && $crawlomatic_Main_Settings['crawlomatic_enabled'] === 'on') {
        if (isset($crawlomatic_Main_Settings['no_local_image']) && $crawlomatic_Main_Settings['no_local_image'] == 'on') {
            add_filter('wp_get_attachment_url', 'crawlomatic_replace_attachment_url', 10, 2);
            add_filter('wp_get_attachment_image_src', 'crawlomatic_replace_attachment_image_src', 10, 3);
            add_filter('post_thumbnail_html', 'crawlomatic_thumbnail_external_replace', 10, 6);
        }
    }
    if ( function_exists( 'register_block_type' ) ) {
        register_block_type( 'crawlomatic-multipage-scraper-post-generator/crawlomatic-display', array(
            'render_callback' => 'crawlomatic_display_posts_shortcode',
        ) );
        register_block_type( 'crawlomatic-multipage-scraper-post-generator/crawlomatic-list', array(
            'render_callback' => 'crawlomatic_list_posts',
        ) );
        register_block_type( 'crawlomatic-multipage-scraper-post-generator/crawlomatic-scraper', array(
            'render_callback' => array( 'Crawlomatic_Shortcode_Scraper', 'shortcode' ),
        ) );
    }
    add_image_size( 'crawlomatic_preview_image', 260, 146);
    if(!taxonomy_exists('coderevolution_post_source'))
    {
        $labels = array(
            'name' => _x('Post Source', 'taxonomy general name', 'crawlomatic-multipage-scraper-post-generator'),
            'singular_name' => _x('Post Source', 'taxonomy singular name', 'crawlomatic-multipage-scraper-post-generator'),
            'search_items' => esc_html__('Search Post Source', 'crawlomatic-multipage-scraper-post-generator'),
            'popular_items' => esc_html__('Popular Post Source', 'crawlomatic-multipage-scraper-post-generator'),
            'all_items' => esc_html__('All Post Sources', 'crawlomatic-multipage-scraper-post-generator'),
            'parent_item' => null,
            'parent_item_colon' => null,
            'edit_item' => esc_html__('Edit Post Source', 'crawlomatic-multipage-scraper-post-generator'),
            'update_item' => esc_html__('Update Post Source', 'crawlomatic-multipage-scraper-post-generator'),
            'add_new_item' => esc_html__('Add New Post Source', 'crawlomatic-multipage-scraper-post-generator'),
            'new_item_name' => esc_html__('New Post Source Name', 'crawlomatic-multipage-scraper-post-generator'),
            'separate_items_with_commas' => esc_html__('Separate Post Source with commas', 'crawlomatic-multipage-scraper-post-generator'),
            'add_or_remove_items' => esc_html__('Add or remove Post Source', 'crawlomatic-multipage-scraper-post-generator'),
            'choose_from_most_used' => esc_html__('Choose from the most used Post Source', 'crawlomatic-multipage-scraper-post-generator'),
            'not_found' => esc_html__('No Post Sources found.', 'crawlomatic-multipage-scraper-post-generator'),
            'menu_name' => esc_html__('Post Source', 'crawlomatic-multipage-scraper-post-generator')
        );
        
        $args = array(
            'hierarchical' => false,
            'public' => false,
            'show_ui' => false,
            'show_in_menu' => false,
            'description' => 'Post Source',
            'labels' => $labels,
            'show_admin_column' => true,
            'update_count_callback' => '_update_post_term_count',
            'rewrite' => false
        );
        
        $add_post_type = array(
            'post',
            'page'
        );
        $xargs = array(
            'public'   => true,
            '_builtin' => false
        );
        $output = 'names'; 
        $operator = 'and';
        $post_types = get_post_types( $xargs, $output, $operator );
        if ( $post_types ) 
        {
            foreach ( $post_types  as $post_type ) {
                $add_post_type[] = $post_type;
            }
        }
        register_taxonomy('coderevolution_post_source', $add_post_type, $args);
        add_action('pre_get_posts', function($qry) {
            if (is_admin()) return;
            if (is_tax('coderevolution_post_source')){
                $qry->set_404();
            }
        });
    }
}

function crawlomatic_testPhantom()
{
    if(!function_exists('shell_exec')) {
        return -1;
    }
    $disabled = explode(',', ini_get('disable_functions'));
    if(in_array('shell_exec', $disabled))
    {
        return -2;
    }
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (isset($crawlomatic_Main_Settings['phantom_path']) && $crawlomatic_Main_Settings['phantom_path'] != '') 
    {
        $phantomjs_comm = $crawlomatic_Main_Settings['phantom_path'] . ' ';
    }
    else
    {
        $phantomjs_comm = 'phantomjs ';
    }
    $cmdResult = shell_exec($phantomjs_comm . '-h 2>&1');
    if(stristr($cmdResult, 'Usage') !== false)
    {
        return 1;
    }
    return 0;
}

function crawlomatic_testTor()
{
    if(!function_exists('shell_exec')) {
        return -1;
    }
    $disabled = explode(',', ini_get('disable_functions'));
    if(in_array('shell_exec', $disabled))
    {
        return -2;
    }
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    $custom_user_agent = 'default';
    $custom_cookies = 'default';
    $user_pass = 'default';
    if (isset($crawlomatic_Main_Settings['phantom_timeout']) && $crawlomatic_Main_Settings['phantom_timeout'] != '') 
    {
        $phantomjs_timeout = ((int)$crawlomatic_Main_Settings['phantom_timeout']);
    }
    else
    {
        $phantomjs_timeout = 'default';
    }
    $phantomjs_proxcomm = '"null"';
    $url = 'https://example.com';
    $puppeteer_comm = 'node ';
    $puppeteer_comm .= '"' . dirname(__FILE__) . '/res/puppeteer/torcheck.js" "' . $url . '" ' . $phantomjs_proxcomm . '  "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . $phantomjs_timeout . '" "0"';
    $puppeteer_comm .= ' 2>&1';
    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
        crawlomatic_log_to_file('Puppeteer-Tor TEST command: ' . $puppeteer_comm);
    }
    $cmdResult = shell_exec($puppeteer_comm);
    if($cmdResult === NULL || $cmdResult == '')
    {
        crawlomatic_log_to_file('puppeteer-tor did not return usable info for: ' . $url);
        return 0;
    }
    if(trim($cmdResult) === 'timeout')
    {
        crawlomatic_log_to_file('puppeteer timed out while getting page (tor): ' . $url. ' - please increase timeout in Main Settings');
        return 0;
    }
    if(stristr($cmdResult, 'sh: puppeteer: command not found') !== false)
    {
        crawlomatic_log_to_file('puppeteer not found, please install it on your server (also tor)');
        return 0;
    }
    if(stristr($cmdResult, 'Error: Cannot find module \'puppeteer\'') !== false)
    {
        echo_log_to_file('puppeteer module not found, please install it on your server');
        return false;
    }
    if(stristr($cmdResult, 'CRAWLOMATIC NOT USING TOR!') !== false)
    {
        crawlomatic_log_to_file('Tor was not able to be used by Crawlomatic/Puppeteer. Please install Tor on your server!');
        return 0;
    }
    if(stristr($cmdResult, 'TOR OK!') !== false)
    {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('Tor OK!');
        }
        return 1;
    }
    crawlomatic_log_to_file('Tor returned unknown result: ' . $cmdResult);
    return 0;
}

function crawlomatic_testPuppeteer()
{
    if(!function_exists('shell_exec')) {
        return -1;
    }
    $disabled = explode(',', ini_get('disable_functions'));
    if(in_array('shell_exec', $disabled))
    {
        return -2;
    }
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    $custom_user_agent = 'default';
    $custom_cookies = 'default';
    $user_pass = 'default';
    if (isset($crawlomatic_Main_Settings['phantom_timeout']) && $crawlomatic_Main_Settings['phantom_timeout'] != '') 
    {
        $phantomjs_timeout = ((int)$crawlomatic_Main_Settings['phantom_timeout']);
    }
    else
    {
        $phantomjs_timeout = 'default';
    }
    $url = 'https://example.com';
    $phantomjs_proxcomm = '"null"';
    $puppeteer_comm = 'node ';
    $puppeteer_comm .= '"' . dirname(__FILE__) . '/res/puppeteer/puppeteer.js" "' . $url . '" ' . $phantomjs_proxcomm . '  "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . $phantomjs_timeout . '"';
    $puppeteer_comm .= ' 2>&1';
    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
        crawlomatic_log_to_file('Puppeteer TEST command: ' . $puppeteer_comm);
    }
    $cmdResult = shell_exec($puppeteer_comm);
    if($cmdResult === NULL || $cmdResult == '')
    {
        crawlomatic_log_to_file('puppeteer did not return usable info for: ' . $url);
        return 0;
    }
    if(trim($cmdResult) === 'timeout')
    {
        crawlomatic_log_to_file('puppeteer timed out while getting page: ' . $url. ' - please increase timeout in Main Settings');
        return 0;
    }
    if(stristr($cmdResult, 'sh: puppeteer: command not found') !== false)
    {
        crawlomatic_log_to_file('puppeteer not found, please install it on your server');
        return 0;
    }
    if(stristr($cmdResult, 'Example Domain') !== false)
    {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('Puppeteer OK!');
        }
        return 1;
    }
    crawlomatic_log_to_file('Puppeteer returned unknown result: ' . $cmdResult);
    return 0;
}

function crawlomatic_get_page_Tor($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $timeout = '', $request_delay = '', $scripter = '', $local_storage = '')
{
    if($custom_user_agent == 'none')
    {
        $custom_user_agent = '';
    }
    elseif($custom_user_agent == '')
    {
        $custom_user_agent = crawlomatic_get_random_user_agent();
    }
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if(!function_exists('shell_exec')) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('shell_exec not found!');
        }
        return false;
    }
    $disabled = explode(',', ini_get('disable_functions'));
    if(in_array('shell_exec', $disabled))
    {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('shell_exec disabled');
        }
        return false;
    }
    $delay = '';
    if (isset($crawlomatic_Main_Settings['request_delay']) && $crawlomatic_Main_Settings['request_delay'] != '') {
        if(stristr($crawlomatic_Main_Settings['request_delay'], ',') !== false)
        {
            $tempo = explode(',', $crawlomatic_Main_Settings['request_delay']);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($crawlomatic_Main_Settings['request_delay'])))
            {
                $delay = intval(trim($crawlomatic_Main_Settings['request_delay']));
            }
        }
    }
    if ($request_delay != '') 
    {
        if(stristr($request_delay, ',') !== false)
        {
            $tempo = explode(',', $request_delay);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($request_delay)))
            {
                $delay = intval(trim($request_delay));
            }
        }
    }
    if($delay != '' && is_numeric($delay))
    {
        $GLOBALS['wp_object_cache']->delete('crawlomatic_last_time', 'options');
        $last_time = get_option('crawlomatic_last_time', false);
        if($last_time !== false && intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000) > 0)
        {
            $sleep_time = intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000);
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                crawlomatic_log_to_file('Delay between requests set, waiting ' . ($sleep_time/1000) . ' ms');
            }
            usleep($sleep_time);
        }
    }
    if($custom_user_agent == '')
    {
        $custom_user_agent = 'default';
    }
    if($custom_cookies == '')
    {
        $custom_cookies = 'default';
    }
    if($user_pass == '')
    {
        $user_pass = 'default';
    }
    if($timeout != '')
    {
        $timeout = 'default';
    } 
    if($scripter == '')
    {
        $scripter = 'default';
    }
    if($local_storage == '')
    {
        $local_storage = 'default';
    } 
    if (isset($crawlomatic_Main_Settings['phantom_timeout']) && $crawlomatic_Main_Settings['phantom_timeout'] != '') 
    {
        $phantomjs_timeout = ((int)$crawlomatic_Main_Settings['phantom_timeout']);
    }
    else
    {
        $phantomjs_timeout = 'default';
    }
    $phantomjs_proxcomm = '"null"';

    $puppeteer_comm = 'node ';
    $puppeteer_comm .= '"' . dirname(__FILE__) . '/res/puppeteer/tor.js" "' . $url . '" ' . $phantomjs_proxcomm . '  "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . $phantomjs_timeout . '" "1" "' . $timeout . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '"';
    $puppeteer_comm .= ' 2>&1';
    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
        crawlomatic_log_to_file('Puppeteer-Tor command: ' . $puppeteer_comm);
    }
    $cmdResult = shell_exec($puppeteer_comm);
    if($cmdResult === NULL || $cmdResult == '')
    {
        crawlomatic_log_to_file('puppeteer-tor did not return usable info for: ' . $url);
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(trim($cmdResult) === 'timeout')
    {
        crawlomatic_log_to_file('puppeteer timed out while getting page (tor): ' . $url. ' - please increase timeout in Main Settings');
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(stristr($cmdResult, 'Error: Cannot find module \'puppeteer\'') !== false)
    {
        crawlomatic_log_to_file('puppeteer not found on server: ' . $cmdResult);
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(stristr($cmdResult, 'sh: puppeteer: command not found') !== false)
    {
        crawlomatic_log_to_file('puppeteer not found, please install it on your server (also tor)');
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(stristr($cmdResult, 'CRAWLOMATIC NOT USING TOR!') !== false)
    {
        crawlomatic_log_to_file('Tor was not able to be used by Crawlomatic/Puppeteer. Please install Tor on your server!');
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(stristr($cmdResult, 'process.on(\'unhandledRejection\', up => { throw up })') !== false)
    {
        crawlomatic_log_to_file('puppeteer failed to download resource: ' . $url . ' - error: ' . $cmdResult);
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
        //crawlomatic_log_to_file('Downloaded site (Puppeteer): ' . $url . ' -- ' . esc_html($cmdResult));
    }
    if($delay != '' && is_numeric($delay))
    {
        update_option('crawlomatic_last_time', time());
    }
    return $cmdResult;
}

function crawlomatic_get_page_PuppeteerAPI($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $timeout = '', $request_delay = '', $scripter = '', $local_storage = '')
{
    if($custom_user_agent == 'none')
    {
        $custom_user_agent = '';
    }
    elseif($custom_user_agent == '')
    {
        $custom_user_agent = crawlomatic_get_random_user_agent();
    }
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (!isset($crawlomatic_Main_Settings['headlessbrowserapi_key']) || trim($crawlomatic_Main_Settings['headlessbrowserapi_key']) == '')
    {
        crawlomatic_log_to_file('You need to add your HeadlessBrowserAPI key in the plugin\'s \'Main Settings\' before you can use this feature.');
        return false;
    }
    $delay = '';
    if (isset($crawlomatic_Main_Settings['request_delay']) && $crawlomatic_Main_Settings['request_delay'] != '') {
        if(stristr($crawlomatic_Main_Settings['request_delay'], ',') !== false)
        {
            $tempo = explode(',', $crawlomatic_Main_Settings['request_delay']);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($crawlomatic_Main_Settings['request_delay'])))
            {
                $delay = intval(trim($crawlomatic_Main_Settings['request_delay']));
            }
        }
    }
    if ($request_delay != '') 
    {
        if(stristr($request_delay, ',') !== false)
        {
            $tempo = explode(',', $request_delay);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($request_delay)))
            {
                $delay = intval(trim($request_delay));
            }
        }
    }
    if($delay != '' && is_numeric($delay))
    {
        $GLOBALS['wp_object_cache']->delete('crawlomatic_last_time', 'options');
        $last_time = get_option('crawlomatic_last_time', false);
        if($last_time !== false && intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000) > 0)
        {
            $sleep_time = intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000);
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                crawlomatic_log_to_file('Delay between requests set, waiting ' . ($sleep_time/1000) . ' ms');
            }
            usleep($sleep_time);
        }
    }
    if($custom_user_agent == '')
    {
        $custom_user_agent = 'default';
    }
    if($custom_cookies == '')
    {
        $custom_cookies = 'default';
    }
    if($user_pass == '')
    {
        $user_pass = 'default';
    }
    if (isset($crawlomatic_Main_Settings['phantom_timeout']) && $crawlomatic_Main_Settings['phantom_timeout'] != '') 
    {
        $phantomjs_timeout = ((int)$crawlomatic_Main_Settings['phantom_timeout']);
    }
    else
    {
        $phantomjs_timeout = 'default';
    }
    $phantomjs_proxcomm = '"null"';
    if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '') 
    {
        $proxy_url = $crawlomatic_Main_Settings['proxy_url'];
        if(isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '')
        {
            $proxy_auth = $crawlomatic_Main_Settings['proxy_auth'];
        }
        else
        {
            $proxy_auth = 'default';
        }
    }
    else
    {
        $proxy_url = 'default';
        $proxy_auth = 'default';
    }
    
    $za_api_url = 'https://headlessbrowserapi.com/apis/scrape/v1/puppeteer?apikey=' . trim($crawlomatic_Main_Settings['headlessbrowserapi_key']) . '&url=' . urlencode($url) . '&custom_user_agent=' . urlencode($custom_user_agent) . '&custom_cookies=' . urlencode($custom_cookies) . '&user_pass=' . urlencode($user_pass) . '&timeout=' . urlencode($phantomjs_timeout) . '&proxy_url=' . urlencode($proxy_url) . '&proxy_auth=' . urlencode($proxy_auth);
    if($timeout != '')
    {
        $za_api_url .= '&sleep=' . urlencode($timeout);
    }
    if(trim($scripter) != '')
    {
        $za_api_url .= '&jsexec=' . urlencode(trim($scripter));
    }
    if(trim($local_storage) != '')
    {
        $za_api_url .= '&localstorage=' . urlencode(trim($local_storage));
    }
    $api_timeout = 60;
    $args = array(
       'timeout'     => $api_timeout,
       'redirection' => 10,
       'blocking'    => true,
       'compress'    => false,
       'decompress'  => true,
       'sslverify'   => false,
       'stream'      => false
    );
    $ret_data = wp_remote_get($za_api_url, $args);
    $response_code       = wp_remote_retrieve_response_code( $ret_data );
    $response_message    = wp_remote_retrieve_response_message( $ret_data );    
    if ( 200 != $response_code ) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
        {
            crawlomatic_log_to_file('Failed to get response from HeadlessBrowserAPI: ' . $za_api_url . ' code: ' . $response_code . ' message: ' . $response_message);
            if(isset($ret_data->errors['http_request_failed']))
            {
                foreach($ret_data->errors['http_request_failed'] as $errx)
                {
                    crawlomatic_log_to_file('Error message: ' . html_entity_decode($errx));
                }
            }
        }
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    } else {
        $cmdResult = wp_remote_retrieve_body( $ret_data );
    }
    $jcmdResult = json_decode($cmdResult, true);
    if($jcmdResult === false)
    {
        crawlomatic_log_to_file('Failed to decode response from HeadlessBrowserAPI (puppeteer): ' . $za_api_url . ' - ' . print_r($cmdResult, true));
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    $cmdResult = $jcmdResult;
    if(isset($cmdResult['apicalls']))
    {
        update_option('headless_calls', esc_html($cmdResult['apicalls']));
    }
    if(isset($cmdResult['error']))
    {
        crawlomatic_log_to_file('An error occurred while getting content from HeadlessBrowserAPI: ' . $za_api_url . ' - ' . print_r($cmdResult['error'], true));
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(!isset($cmdResult['html']))
    {
        crawlomatic_log_to_file('Malformed data imported from HeadlessBrowserAPI: ' . $za_api_url . ' - ' . print_r($cmdResult, true));
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if($delay != '' && is_numeric($delay))
    {
        update_option('crawlomatic_last_time', time());
    }
    return '<html><body>' . $cmdResult['html'] . '</body></html>';
}

function crawlomatic_get_screenshot_PuppeteerAPI($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $timeout = '', $request_delay = '', $scripter = '', $local_storage = '', $h = '0', $w = '1920')
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (!isset($crawlomatic_Main_Settings['headlessbrowserapi_key']) || trim($crawlomatic_Main_Settings['headlessbrowserapi_key']) == '')
    {
        crawlomatic_log_to_file('You need to add your HeadlessBrowserAPI key in the plugin\'s \'Main Settings\' before you can use this feature.');
        return false;
    }
    $delay = '';
    if (isset($crawlomatic_Main_Settings['request_delay']) && $crawlomatic_Main_Settings['request_delay'] != '') {
        if(stristr($crawlomatic_Main_Settings['request_delay'], ',') !== false)
        {
            $tempo = explode(',', $crawlomatic_Main_Settings['request_delay']);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($crawlomatic_Main_Settings['request_delay'])))
            {
                $delay = intval(trim($crawlomatic_Main_Settings['request_delay']));
            }
        }
    }
    if ($request_delay != '') 
    {
        if(stristr($request_delay, ',') !== false)
        {
            $tempo = explode(',', $request_delay);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($request_delay)))
            {
                $delay = intval(trim($request_delay));
            }
        }
    }
    if($delay != '' && is_numeric($delay))
    {
        $GLOBALS['wp_object_cache']->delete('crawlomatic_last_time', 'options');
        $last_time = get_option('crawlomatic_last_time', false);
        if($last_time !== false && intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000) > 0)
        {
            $sleep_time = intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000);
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                crawlomatic_log_to_file('Delay between requests set, waiting ' . ($sleep_time/1000) . ' ms');
            }
            usleep($sleep_time);
        }
    }
    if($custom_user_agent == '')
    {
        $custom_user_agent = 'default';
    }
    if($custom_cookies == '')
    {
        $custom_cookies = 'default';
    }
    if($user_pass == '')
    {
        $user_pass = 'default';
    }
    if (isset($crawlomatic_Main_Settings['phantom_timeout']) && $crawlomatic_Main_Settings['phantom_timeout'] != '') 
    {
        $phantomjs_timeout = ((int)$crawlomatic_Main_Settings['phantom_timeout']);
    }
    else
    {
        $phantomjs_timeout = 'default';
    }
    $phantomjs_proxcomm = '"null"';
    if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '') 
    {
        $proxy_url = $crawlomatic_Main_Settings['proxy_url'];
        if(isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '')
        {
            $proxy_auth = $crawlomatic_Main_Settings['proxy_auth'];
        }
        else
        {
            $proxy_auth = 'default';
        }
    }
    else
    {
        $proxy_url = 'default';
        $proxy_auth = 'default';
    }
    if($h == '')
    {
        $h = '0';
    }
    if($w == '')
    {
        $w = '1920';
    }
    $za_api_url = 'https://headlessbrowserapi.com/apis/scrape/v1/screenshot?apikey=' . trim($crawlomatic_Main_Settings['headlessbrowserapi_key']) . '&url=' . urlencode($url) . '&custom_user_agent=' . urlencode($custom_user_agent) . '&custom_cookies=' . urlencode($custom_cookies) . '&user_pass=' . urlencode($user_pass) . '&timeout=' . urlencode($phantomjs_timeout) . '&proxy_url=' . urlencode($proxy_url) . '&proxy_auth=' . urlencode($proxy_auth) . '&height=' . urlencode($h) . '&width=' . urlencode($w);
    if(trim($scripter) != '')
    {
        $za_api_url .= '&jsexec=' . urlencode(trim($scripter));
    }
    if(trim($local_storage) != '')
    {
        $za_api_url .= '&localstorage=' . urlencode(trim($local_storage));
    }
    $api_timeout = 60;
    $args = array(
       'timeout'     => $api_timeout,
       'redirection' => 10,
       'blocking'    => true,
       'compress'    => false,
       'decompress'  => true,
       'sslverify'   => false,
       'stream'      => false
    );
    $ret_data = wp_remote_get($za_api_url, $args);
    $response_code       = wp_remote_retrieve_response_code( $ret_data );
    $response_message    = wp_remote_retrieve_response_message( $ret_data );    
    if($delay != '' && is_numeric($delay))
    {
        update_option('crawlomatic_last_time', time());
    }
    if ( 200 != $response_code ) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
        {
            crawlomatic_log_to_file('Failed to get response from HeadlessBrowserAPI: ' . $za_api_url . ' code: ' . $response_code . ' message: ' . $response_message);
            if(isset($ret_data->errors['http_request_failed']))
            {
                foreach($ret_data->errors['http_request_failed'] as $errx)
                {
                    crawlomatic_log_to_file('Error message: ' . html_entity_decode($errx));
                }
            }
        }
        return false;
    } else {
        $cmdResult = wp_remote_retrieve_body( $ret_data );
    }
    if(strstr($cmdResult, '"error"') !== false)
    {
        crawlomatic_log_to_file('Failed to decode response from HeadlessBrowserAPI: ' . $za_api_url . ' - ' . print_r($cmdResult, true));
        return false;
    }
    return $cmdResult;
}
function crawlomatic_get_page_TorAPI($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $timeout = '', $request_delay = '', $scripter = '', $local_storage = '')
{
    if($custom_user_agent == 'none')
    {
        $custom_user_agent = '';
    }
    elseif($custom_user_agent == '')
    {
        $custom_user_agent = crawlomatic_get_random_user_agent();
    }
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (!isset($crawlomatic_Main_Settings['headlessbrowserapi_key']) || trim($crawlomatic_Main_Settings['headlessbrowserapi_key']) == '')
    {
        crawlomatic_log_to_file('You need to add your HeadlessBrowserAPI key in the plugin\'s \'Main Settings\' before you can use this feature.');
        return false;
    }
    $delay = '';
    if (isset($crawlomatic_Main_Settings['request_delay']) && $crawlomatic_Main_Settings['request_delay'] != '') {
        if(stristr($crawlomatic_Main_Settings['request_delay'], ',') !== false)
        {
            $tempo = explode(',', $crawlomatic_Main_Settings['request_delay']);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($crawlomatic_Main_Settings['request_delay'])))
            {
                $delay = intval(trim($crawlomatic_Main_Settings['request_delay']));
            }
        }
    }
    if ($request_delay != '') 
    {
        if(stristr($request_delay, ',') !== false)
        {
            $tempo = explode(',', $request_delay);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($request_delay)))
            {
                $delay = intval(trim($request_delay));
            }
        }
    }
    if($delay != '' && is_numeric($delay))
    {
        $GLOBALS['wp_object_cache']->delete('crawlomatic_last_time', 'options');
        $last_time = get_option('crawlomatic_last_time', false);
        if($last_time !== false && intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000) > 0)
        {
            $sleep_time = intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000);
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                crawlomatic_log_to_file('Delay between requests set, waiting ' . ($sleep_time/1000) . ' ms');
            }
            usleep($sleep_time);
        }
    }
    if($custom_user_agent == '')
    {
        $custom_user_agent = 'default';
    }
    if($custom_cookies == '')
    {
        $custom_cookies = 'default';
    }
    if($user_pass == '')
    {
        $user_pass = 'default';
    }
    if (isset($crawlomatic_Main_Settings['phantom_timeout']) && $crawlomatic_Main_Settings['phantom_timeout'] != '') 
    {
        $phantomjs_timeout = ((int)$crawlomatic_Main_Settings['phantom_timeout']);
    }
    else
    {
        $phantomjs_timeout = 'default';
    }
    $phantomjs_proxcomm = '"null"';
    if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '') 
    {
        $proxy_url = $crawlomatic_Main_Settings['proxy_url'];
        if(isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '')
        {
            $proxy_auth = $crawlomatic_Main_Settings['proxy_auth'];
        }
        else
        {
            $proxy_auth = 'default';
        }
    }
    else
    {
        $proxy_url = 'default';
        $proxy_auth = 'default';
    }
    
    $za_api_url = 'https://headlessbrowserapi.com/apis/scrape/v1/tor?apikey=' . trim($crawlomatic_Main_Settings['headlessbrowserapi_key']) . '&url=' . urlencode($url) . '&custom_user_agent=' . urlencode($custom_user_agent) . '&custom_cookies=' . urlencode($custom_cookies) . '&user_pass=' . urlencode($user_pass) . '&timeout=' . urlencode($phantomjs_timeout) . '&proxy_url=' . urlencode($proxy_url) . '&proxy_auth=' . urlencode($proxy_auth);
    if($timeout != '')
    {
        $za_api_url .= '&sleep=' . urlencode($timeout);
    }
    if(trim($scripter) != '')
    {
        $za_api_url .= '&jsexec=' . urlencode(trim($scripter));
    }
    if(trim($local_storage) != '')
    {
        $za_api_url .= '&localstorage=' . urlencode(trim($local_storage));
    }
    $api_timeout = 60;
    $args = array(
       'timeout'     => $api_timeout,
       'redirection' => 10,
       'blocking'    => true,
       'compress'    => false,
       'decompress'  => true,
       'sslverify'   => false,
       'stream'      => false
    );
    $ret_data = wp_remote_get($za_api_url, $args);
    $response_code       = wp_remote_retrieve_response_code( $ret_data );
    $response_message    = wp_remote_retrieve_response_message( $ret_data );    
    if ( 200 != $response_code ) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
        {
            crawlomatic_log_to_file('Failed to get response from HeadlessBrowserAPI: ' . $za_api_url . ' code: ' . $response_code . ' message: ' . $response_message);
            if(isset($ret_data->errors['http_request_failed']))
            {
                foreach($ret_data->errors['http_request_failed'] as $errx)
                {
                    crawlomatic_log_to_file('Error message: ' . html_entity_decode($errx));
                }
            }
        }
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    } else {
        $cmdResult = wp_remote_retrieve_body( $ret_data );
    }
    $jcmdResult = json_decode($cmdResult, true);
    if($jcmdResult === false)
    {
        crawlomatic_log_to_file('Failed to decode response from HeadlessBrowserAPI (tor): ' . $za_api_url . ' - ' . print_r($cmdResult, true));
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    $cmdResult = $jcmdResult;
    if(isset($cmdResult['apicalls']))
    {
        update_option('headless_calls', esc_html($cmdResult['apicalls']));
    }
    if(isset($cmdResult['error']))
    {
        crawlomatic_log_to_file('An error occurred while getting content from HeadlessBrowserAPI: ' . $za_api_url . ' - ' . print_r($cmdResult['error'], true));
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(!isset($cmdResult['html']))
    {
        crawlomatic_log_to_file('Malformed data imported from HeadlessBrowserAPI: ' . $za_api_url . ' - ' . print_r($cmdResult, true));
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if($delay != '' && is_numeric($delay))
    {
        update_option('crawlomatic_last_time', time());
    }
    return '<html><body>' . $cmdResult['html'] . '</body></html>';
}
function crawlomatic_get_page_PhantomJSAPI($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $timeout = '', $request_delay = '', $scripter = '', $local_storage = '')
{
    if($custom_user_agent == 'none')
    {
        $custom_user_agent = '';
    }
    elseif($custom_user_agent == '')
    {
        $custom_user_agent = crawlomatic_get_random_user_agent();
    }
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (!isset($crawlomatic_Main_Settings['headlessbrowserapi_key']) || trim($crawlomatic_Main_Settings['headlessbrowserapi_key']) == '')
    {
        crawlomatic_log_to_file('You need to add your HeadlessBrowserAPI key in the plugin\'s \'Main Settings\' before you can use this feature.');
        return false;
    }
    $delay = '';
    if (isset($crawlomatic_Main_Settings['request_delay']) && $crawlomatic_Main_Settings['request_delay'] != '') {
        if(stristr($crawlomatic_Main_Settings['request_delay'], ',') !== false)
        {
            $tempo = explode(',', $crawlomatic_Main_Settings['request_delay']);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($crawlomatic_Main_Settings['request_delay'])))
            {
                $delay = intval(trim($crawlomatic_Main_Settings['request_delay']));
            }
        }
    }
    if ($request_delay != '') 
    {
        if(stristr($request_delay, ',') !== false)
        {
            $tempo = explode(',', $request_delay);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($request_delay)))
            {
                $delay = intval(trim($request_delay));
            }
        }
    }
    if($delay != '' && is_numeric($delay))
    {
        $GLOBALS['wp_object_cache']->delete('crawlomatic_last_time', 'options');
        $last_time = get_option('crawlomatic_last_time', false);
        if($last_time !== false && intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000) > 0)
        {
            $sleep_time = intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000);
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                crawlomatic_log_to_file('Delay between requests set, waiting ' . ($sleep_time/1000) . ' ms');
            }
            usleep($sleep_time);
        }
    }
    if($custom_user_agent == '')
    {
        $custom_user_agent = 'default';
    }
    if($custom_cookies == '')
    {
        $custom_cookies = 'default';
    }
    if($user_pass == '')
    {
        $user_pass = 'default';
    }
    if (isset($crawlomatic_Main_Settings['phantom_timeout']) && $crawlomatic_Main_Settings['phantom_timeout'] != '') 
    {
        $phantomjs_timeout = ((int)$crawlomatic_Main_Settings['phantom_timeout']);
    }
    else
    {
        $phantomjs_timeout = 'default';
    }
    $phantomjs_proxcomm = '"null"';
    if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '') 
    {
        $proxy_url = $crawlomatic_Main_Settings['proxy_url'];
        if(isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '')
        {
            $proxy_auth = $crawlomatic_Main_Settings['proxy_auth'];
        }
        else
        {
            $proxy_auth = 'default';
        }
    }
    else
    {
        $proxy_url = 'default';
        $proxy_auth = 'default';
    }
    
    $za_api_url = 'https://headlessbrowserapi.com/apis/scrape/v1/phantomjs?apikey=' . trim($crawlomatic_Main_Settings['headlessbrowserapi_key']) . '&url=' . urlencode($url) . '&custom_user_agent=' . urlencode($custom_user_agent) . '&custom_cookies=' . urlencode($custom_cookies) . '&user_pass=' . urlencode($user_pass) . '&timeout=' . urlencode($phantomjs_timeout) . '&proxy_url=' . urlencode($proxy_url) . '&proxy_auth=' . urlencode($proxy_auth);
    if($timeout != '')
    {
        $za_api_url .= '&sleep=' . urlencode($timeout);
    }
    if(trim($scripter) != '')
    {
        $za_api_url .= '&jsexec=' . urlencode(trim($scripter));
    }
    if(trim($local_storage) != '')
    {
        $za_api_url .= '&localstorage=' . urlencode(trim($local_storage));
    }
    $api_timeout = 60;
    $args = array(
       'timeout'     => $api_timeout,
       'redirection' => 10,
       'blocking'    => true,
       'compress'    => false,
       'decompress'  => true,
       'sslverify'   => false,
       'stream'      => false
    );
    $ret_data = wp_remote_get($za_api_url, $args);
    $response_code       = wp_remote_retrieve_response_code( $ret_data );
    $response_message    = wp_remote_retrieve_response_message( $ret_data );    
    if ( 200 != $response_code ) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
        {
            crawlomatic_log_to_file('Failed to get response from HeadlessBrowserAPI: ' . $za_api_url . ' code: ' . $response_code . ' message: ' . $response_message);
            if(isset($ret_data->errors['http_request_failed']))
            {
                foreach($ret_data->errors['http_request_failed'] as $errx)
                {
                    crawlomatic_log_to_file('Error message: ' . html_entity_decode($errx));
                }
            }
        }
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    } else {
        $cmdResult = wp_remote_retrieve_body( $ret_data );
    }
    $jcmdResult = json_decode($cmdResult, true);
    if($jcmdResult === false)
    {
        crawlomatic_log_to_file('Failed to decode response from HeadlessBrowserAPI (phantomjs): ' . $za_api_url . ' - ' . print_r($cmdResult, true));
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    $cmdResult = $jcmdResult;
    if(isset($cmdResult['apicalls']))
    {
        update_option('headless_calls', esc_html($cmdResult['apicalls']));
    }
    if(isset($cmdResult['error']))
    {
        crawlomatic_log_to_file('An error occurred while getting content from HeadlessBrowserAPI: ' . $za_api_url . ' - ' . print_r($cmdResult['error'], true));
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(!isset($cmdResult['html']))
    {
        crawlomatic_log_to_file('Malformed data imported from HeadlessBrowserAPI: ' . $za_api_url . ' - ' . print_r($cmdResult, true));
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if($delay != '' && is_numeric($delay))
    {
        update_option('crawlomatic_last_time', time());
    }
    return '<html><body>' . $cmdResult['html'] . '</body></html>';
}
function crawlomatic_get_page_Puppeteer($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $timeout = '', $request_delay = '', $scripter = '', $local_storage = '')
{
    if($custom_user_agent == 'none')
    {
        $custom_user_agent = '';
    }
    elseif($custom_user_agent == '')
    {
        $custom_user_agent = crawlomatic_get_random_user_agent();
    }
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if(!function_exists('shell_exec')) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('shell_exec not found!');
        }
        return false;
    }
    $disabled = explode(',', ini_get('disable_functions'));
    if(in_array('shell_exec', $disabled))
    {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('shell_exec disabled');
        }
        return false;
    }
    $delay = '';
    if (isset($crawlomatic_Main_Settings['request_delay']) && $crawlomatic_Main_Settings['request_delay'] != '') {
        if(stristr($crawlomatic_Main_Settings['request_delay'], ',') !== false)
        {
            $tempo = explode(',', $crawlomatic_Main_Settings['request_delay']);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($crawlomatic_Main_Settings['request_delay'])))
            {
                $delay = intval(trim($crawlomatic_Main_Settings['request_delay']));
            }
        }
    }
    if ($request_delay != '') 
    {
        if(stristr($request_delay, ',') !== false)
        {
            $tempo = explode(',', $request_delay);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($request_delay)))
            {
                $delay = intval(trim($request_delay));
            }
        }
    }
    if($delay != '' && is_numeric($delay))
    {
        $GLOBALS['wp_object_cache']->delete('crawlomatic_last_time', 'options');
        $last_time = get_option('crawlomatic_last_time', false);
        if($last_time !== false && intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000) > 0)
        {
            $sleep_time = intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000);
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                crawlomatic_log_to_file('Delay between requests set, waiting ' . ($sleep_time/1000) . ' ms');
            }
            usleep($sleep_time);
        }
    }
    if($custom_user_agent == '')
    {
        $custom_user_agent = 'default';
    }
    if($custom_cookies == '')
    {
        $custom_cookies = 'default';
    }
    if($user_pass == '')
    {
        $user_pass = 'default';
    }
    if($timeout == '')
    {
        $timeout = 'default';
    }   
    if($scripter == '')
    {
        $scripter = 'default';
    }    
    if($local_storage == '')
    {
        $local_storage = 'default';
    } 
    if (isset($crawlomatic_Main_Settings['phantom_timeout']) && $crawlomatic_Main_Settings['phantom_timeout'] != '') 
    {
        $phantomjs_timeout = ((int)$crawlomatic_Main_Settings['phantom_timeout']);
    }
    else
    {
        $phantomjs_timeout = '30000';
    }
    $phantomjs_proxcomm = '"null"';
    if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') 
    {
        $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
        $randomness = array_rand($prx);
        $phantomjs_proxcomm = '"' . trim($prx[$randomness]);
        if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
        {
            $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
            if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
            {
                $phantomjs_proxcomm .= ':' . trim($prx_auth[$randomness]);
            }
        }
        $phantomjs_proxcomm .= '"';
    }
    
    $puppeteer_comm = 'node ';
    $puppeteer_comm .= '"' . dirname(__FILE__) . '/res/puppeteer/puppeteer.js" "' . $url . '" ' . $phantomjs_proxcomm . '  "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . $phantomjs_timeout . '" "' . $timeout . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '"';
    $puppeteer_comm .= ' 2>&1';
    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
        crawlomatic_log_to_file('Puppeteer command: ' . $puppeteer_comm);
    }
    $cmdResult = shell_exec($puppeteer_comm);
    if($cmdResult === NULL || $cmdResult == '')
    {
        crawlomatic_log_to_file('puppeteer did not return usable info for: ' . $url);
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(trim($cmdResult) === 'timeout')
    {
        crawlomatic_log_to_file('puppeteer timed out while getting page: ' . $url. ' - please increase timeout in Main Settings');
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(stristr($cmdResult, 'Error: Cannot find module \'puppeteer\'') !== false)
    {
        crawlomatic_log_to_file('puppeteer not found on server: ' . $cmdResult);
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(stristr($cmdResult, 'sh: puppeteer: command not found') !== false)
    {
        crawlomatic_log_to_file('puppeteer not found, please install it on your server');
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(stristr($cmdResult, 'process.on(\'unhandledRejection\', up => { throw up })') !== false)
    {
        crawlomatic_log_to_file('puppeteer failed to download resource: ' . $url . ' - error: ' . $cmdResult);
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
        //crawlomatic_log_to_file('Downloaded site (Puppeteer): ' . $url . ' -- ' . esc_html($cmdResult));
    }
    if($delay != '' && is_numeric($delay))
    {
        update_option('crawlomatic_last_time', time());
    }
    return $cmdResult;
}
function crawlomatic_get_page_PhantomJS($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage)
{
    if(!function_exists('shell_exec')) {
        crawlomatic_log_to_file('shell_exec nou found, cannot run');
        return false;
    }
    $disabled = explode(',', ini_get('disable_functions'));
    if(in_array('shell_exec', $disabled))
    {
        crawlomatic_log_to_file('shell_exec disabled, cannot run');
        return false;
    }
    if($custom_user_agent == 'none')
    {
        $custom_user_agent = '';
    }
    elseif($custom_user_agent == '')
    {
        $custom_user_agent = crawlomatic_get_random_user_agent();
    }
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    $delay = '';
    if (isset($crawlomatic_Main_Settings['request_delay']) && $crawlomatic_Main_Settings['request_delay'] != '') {
        if(stristr($crawlomatic_Main_Settings['request_delay'], ',') !== false)
        {
            $tempo = explode(',', $crawlomatic_Main_Settings['request_delay']);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($crawlomatic_Main_Settings['request_delay'])))
            {
                $delay = intval(trim($crawlomatic_Main_Settings['request_delay']));
            }
        }
    }
    if ($request_delay != '') 
    {
        if(stristr($request_delay, ',') !== false)
        {
            $tempo = explode(',', $request_delay);
            if(isset($tempo[1]) && is_numeric(trim($tempo[1])) && is_numeric(trim($tempo[0])))
            {
                $delay = rand(trim($tempo[0]), trim($tempo[1]));
            }
        }
        else
        {
            if(is_numeric(trim($request_delay)))
            {
                $delay = intval(trim($request_delay));
            }
        }
    }
    if($delay != '' && is_numeric($delay))
    {
        $GLOBALS['wp_object_cache']->delete('crawlomatic_last_time', 'options');
        $last_time = get_option('crawlomatic_last_time', false);
        if($last_time !== false && intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000) > 0)
        {
            $sleep_time = intval(((intval($last_time) - time()) * 1000 + $delay ) * 1000);
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                crawlomatic_log_to_file('Delay between requests set, waiting ' . ($sleep_time/1000) . ' ms');
            }
            usleep($sleep_time);
        }
    }
    if (isset($crawlomatic_Main_Settings['phantom_path']) && $crawlomatic_Main_Settings['phantom_path'] != '') 
    {
        $phantomjs_comm = $crawlomatic_Main_Settings['phantom_path'];
    }
    else
    {
        $phantomjs_comm = 'phantomjs';
    }
    if (isset($crawlomatic_Main_Settings['phantom_timeout']) && $crawlomatic_Main_Settings['phantom_timeout'] != '') 
    {
        $phantomjs_timeout = ((int)$crawlomatic_Main_Settings['phantom_timeout']);
    }
    else
    {
        $phantomjs_timeout = '30000';
    }
    if($custom_user_agent == '')
    {
        $custom_user_agent = 'default';
    }
    if($custom_cookies == '')
    {
        $custom_cookies = 'default';
    }
    if($user_pass == '')
    {
        $user_pass = 'default';
    } 
    if($scripter == '')
    {
        $scripter = 'default';
    } 
    if($local_storage == '')
    {
        $local_storage = 'default';
    } 
    if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') 
    {
        $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
        $randomness = array_rand($prx);
        $phantomjs_comm .= ' --proxy=' . trim($prx[$randomness]);
        if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
        {
            $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
            if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
            {
                $phantomjs_comm .= ' --proxy-auth=' . trim($prx_auth[$randomness]);
            }
        }
    }
    $phantomjs_comm .= ' --ignore-ssl-errors=true ';
    $phantomjs_comm .= '"' . dirname(__FILE__) . '/res/phantomjs/phantom.js" "' . $url . '" "' . esc_html($phantomjs_timeout) . '" "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . esc_html($phantom_wait) . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '"';
    $phantomjs_comm .= ' 2>&1';
    $cmdResult = shell_exec($phantomjs_comm);
    if($cmdResult === NULL || $cmdResult == '')
    {
        crawlomatic_log_to_file('phantomjs did not return usable info for: ' . $url);
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(trim($cmdResult) === 'timeout')
    {
        crawlomatic_log_to_file('phantomjs timed out while getting page: ' . $url. ' - please increase timeout in Main Settings');
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if(stristr($cmdResult, 'sh: phantomjs: command not found') !== false)
    {
        crawlomatic_log_to_file('phantomjs not found, please install it on your server');
        if($delay != '' && is_numeric($delay))
        {
            update_option('crawlomatic_last_time', time());
        }
        return false;
    }
    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
        //crawlomatic_log_to_file('Downloaded site (PhantomJS): ' . $url . ' -- ' . esc_html($cmdResult));
    }
    if($delay != '' && is_numeric($delay))
    {
        update_option('crawlomatic_last_time', time());
    }
    return $cmdResult;
}

register_activation_hook(__FILE__, 'crawlomatic_activation_callback');
function crawlomatic_activation_callback($defaults = FALSE)
{
    if (!get_option('crawlomatic_posts_per_page') || $defaults === TRUE) {
        if ($defaults === FALSE) {
            add_option('crawlomatic_posts_per_page', '12');
        } else {
            update_option('crawlomatic_posts_per_page', '12');
        }
    }
    if (!get_option('crawlomatic_Main_Settings') || $defaults === TRUE) {
        $crawlomatic_Main_Settings = array(
            'crawlomatic_enabled' => 'on',
            'disable_excerpt' => 'on',
            'no_content_autodetect' => '',
            'max_auto_links' => '5',
            'def_user' => '1',
            'fix_html' => '',
            'alt_read' => '',
            'convert_cyrilic' => '',
            'add_canonical' => '',
            'strip_scripts' => '',
            'strip_html' => '',
            'screenshot_height' => '450',
            'screenshot_width' => '600',
            'enable_metabox' => 'on',
            'skip_no_img' => '',
            'skip_old' => '',
            'skip_year' => '',
            'phantom_path' => '',
            'phantom_timeout' => '',
            'phantom_screen' => '',
            'puppeteer_screen' => '',
            'headless_screen' => '',
            'skip_month' => '',
            'skip_day' => '',
            'custom_html2' => '',
            'custom_html' => '',
            'sentence_list' => 'This is one %adjective %noun %sentence_ending
This is another %adjective %noun %sentence_ending
I %love_it %nouns , because they are %adjective %sentence_ending
My %family says this plugin is %adjective %sentence_ending
These %nouns are %adjective %sentence_ending',
            'sentence_list2' => 'Meet this %adjective %noun %sentence_ending
This is the %adjective %noun ever %sentence_ending
I %love_it %nouns , because they are the %adjective %sentence_ending
My %family says this plugin is very %adjective %sentence_ending
These %nouns are quite %adjective %sentence_ending',
            'variable_list' => 'adjective_very => %adjective;very %adjective;

adjective => clever;interesting;smart;huge;astonishing;unbelievable;nice;adorable;beautiful;elegant;fancy;glamorous;magnificent;helpful;awesome

noun_with_adjective => %noun;%adjective %noun

noun => plugin;WordPress plugin;item;ingredient;component;constituent;module;add-on;plug-in;addon;extension

nouns => plugins;WordPress plugins;items;ingredients;components;constituents;modules;add-ons;plug-ins;addons;extensions

love_it => love;adore;like;be mad for;be wild about;be nuts about;be crazy about

family => %adjective %family_members;%family_members

family_members => grandpa;brother;sister;mom;dad;grandma

sentence_ending => .;!;!!',
            'auto_clear_logs' => 'No',
            'enable_logging' => 'on',
            'enable_detailed_logging' => '',
            'rule_timeout' => '3600',
            'request_timeout' => '10',
            'request_delay' => '',
            'strip_links' => '',
            'strip_content_links' => '',
            'strip_internal_content_links' => '',
            'email_address' => '',
            'email_summary' => '',
            'send_email' => '',
            'crawlomatic_timestamp' => '',
            'crawlomatic_post_img' => '',
            'crawlomatic_extra_tags' => '',
            'crawlomatic_extra_categories' => '',
            'crawlomatic_item_title' => '',
            'crawlomatic_comment_status' => '',
            'crawlomatic_enable_pingbacks' => '',
            'crawlomatic_post_date' => '',
            'send_post_email' => '',
            'best_password' => '',
            'protected_terms' => '',
            'best_user' => '',
            'spin_text' => 'disabled',
            'wordai_uniqueness' => '',
            'enable_robots' => '',
            'max_word_content' => '',
            'min_word_content' => '',
            'max_word_title' => '',
            'min_word_title' => '',
            'crawlomatic_featured_image_checking' => '',
            'random_image_names' => '',
            'remove_img_content' => '',
            'crawlomatic_clear_curl_charset' => '',
            'proxy_url' => '',
            'proxy_auth' => '',
            'search_google' => '',
            'post_source_custom' => '',
            'default_dl_ext' => '',
            'resize_width' => '',
            'resize_height' => '',
            'read_more_text' => 'Read More',
            'price_multiply' => '',
            'price_add' => '',
            'price_sep' => '.',
            'no_local_image' => '',
            'url_image' => '',
            'auto_delete_enabled' => '',
            'disable_backend_content' => '',
            'no_valid_link' => '',
            'keep_filters' => '',
            'no_title_spin' => '',
            'copy_images' => '',
            'rule_delay' => '',
            'no_spin' => '',
            'spin_lang' => '',
            'replace_url' => '',
            'link_attributes_external' => '',
            'link_attributes_internal' => '',
            'multi_separator' => ',',
            'do_not_check_duplicates' => '',
            'cleanup_not_printable' => '',
            'title_duplicates' => '',
            'draft_first' => '',
            'do_not_crawl_duplicates' => '',
            'link_source' => '',
            'shortest_api' => '',
            'update_existing' => '',
            'no_up_img' => '',
            'iframe_resize_height' => '',
            'iframe_resize_width' => '',
            'skip_image_names' => '',
            'cat_separator' => ',',
            'no_check' => '',
            'deepl_auth' => '',
            'deppl_free' => '',
            'bing_auth' => '',
            'bing_region' => '',
            'google_trans_auth' => '',
            'headlessbrowserapi_key' => '',
            'flickr_order' => 'date-posted-desc',
            'flickr_license' => '-1',
            'flickr_api' => '',
            'scrapeimg_height' => '',
            'attr_text' => 'Photo Credit: <a href="%%image_source_url%%" target="_blank">%%image_source_name%%</a>',
            'scrapeimg_width' => '',
            'scrapeimg_cat' => 'all',
            'scrapeimg_order' => 'any',
            'scrapeimg_orientation' => 'all',
            'imgtype' => 'all',
            'pixabay_api' => '',
            'pexels_api' => '',
            'morguefile_secret' => '',
            'morguefile_api' => '',
            'bimage' => 'on',
            'no_orig' => '',
            'img_order' => 'popular',
            'img_cat' => 'all',
            'img_width' => '',
            'img_mwidth' => '',
            'img_ss' => '',
            'img_editor' => '',
            'img_language' => 'any',
            'pixabay_scrape' => '',
            'unsplash_api' => '',
            'scrapeimgtype' => 'all'
        );
        if ($defaults === FALSE) {
            add_option('crawlomatic_Main_Settings', $crawlomatic_Main_Settings);
        } else {
            update_option('crawlomatic_Main_Settings', $crawlomatic_Main_Settings);
        }
    }
}


function crawlomatic_get_free_image($crawlomatic_Main_Settings, $query_words, &$img_attr, $res_cnt = 3)
{
    $original_url = '';
    $rand_arr = array();
    if(isset($crawlomatic_Main_Settings['pixabay_api']) && $crawlomatic_Main_Settings['pixabay_api'] != '')
    {
        $rand_arr[] = 'pixabay';
    }
    if(isset($crawlomatic_Main_Settings['morguefile_api']) && $crawlomatic_Main_Settings['morguefile_api'] !== '' && isset($crawlomatic_Main_Settings['morguefile_secret']) && $crawlomatic_Main_Settings['morguefile_secret'] !== '')
    {
        $rand_arr[] = 'morguefile';
    }
    if(isset($crawlomatic_Main_Settings['flickr_api']) && $crawlomatic_Main_Settings['flickr_api'] !== '')
    {
        $rand_arr[] = 'flickr';
    }
    if(isset($crawlomatic_Main_Settings['pexels_api']) && $crawlomatic_Main_Settings['pexels_api'] !== '')
    {
        $rand_arr[] = 'pexels';
    }
    if(isset($crawlomatic_Main_Settings['pixabay_scrape']) && $crawlomatic_Main_Settings['pixabay_scrape'] == 'on')
    {
        $rand_arr[] = 'pixabayscrape';
    }
    if(isset($crawlomatic_Main_Settings['unsplash_api']) && $crawlomatic_Main_Settings['unsplash_api'] == 'on')
    {
        $rand_arr[] = 'unsplash';
    }
    $rez = false;
    while(($rez === false || $rez === '') && count($rand_arr) > 0)
    {
        $rand = array_rand($rand_arr);
        if($rand_arr[$rand] == 'pixabay')
        {
            unset($rand_arr[$rand]);
            if(isset($crawlomatic_Main_Settings['img_ss']) && $crawlomatic_Main_Settings['img_ss'] == 'on')
            {
                $img_ss = '1';
            }
            else
            {
                $img_ss = '0';
            }
            if(isset($crawlomatic_Main_Settings['img_editor']) && $crawlomatic_Main_Settings['img_editor'] == 'on')
            {
                $img_editor = '1';
            }
            else
            {
                $img_editor = '0';
            }
            $rez = crawlomatic_get_pixabay_image($crawlomatic_Main_Settings['pixabay_api'], $query_words, $crawlomatic_Main_Settings['img_language'], $crawlomatic_Main_Settings['imgtype'], $crawlomatic_Main_Settings['scrapeimg_orientation'], $crawlomatic_Main_Settings['img_order'], $crawlomatic_Main_Settings['img_cat'], $crawlomatic_Main_Settings['img_mwidth'], $crawlomatic_Main_Settings['img_width'], $img_ss, $img_editor, $original_url, $res_cnt);
            if($rez !== false && $rez !== '')
            {
                $img_attr = str_replace('%%image_source_name%%', 'Pixabay', $img_attr);
                $img_attr = str_replace('%%image_source_url%%', $original_url, $img_attr);
                $img_attr = str_replace('%%image_source_website%%', 'https://pixabay.com/', $img_attr);
            }
        }
        elseif($rand_arr[$rand] == 'morguefile')
        {
            unset($rand_arr[$rand]);
            $rez = crawlomatic_get_morguefile_image($crawlomatic_Main_Settings['morguefile_api'], $crawlomatic_Main_Settings['morguefile_secret'], $query_words, $original_url);
            if($rez !== false && $rez !== '')
            {
                $img_attr = str_replace('%%image_source_name%%', 'MorgueFile', $img_attr);
                $img_attr = str_replace('%%image_source_url%%', 'https://morguefile.com/', $img_attr);
                $img_attr = str_replace('%%image_source_website%%', 'https://morguefile.com/', $img_attr);
            }
        }
        elseif($rand_arr[$rand] == 'flickr')
        {
            unset($rand_arr[$rand]);
            $rez = crawlomatic_get_flickr_image($crawlomatic_Main_Settings, $query_words, $original_url, $res_cnt);
            if($rez !== false && $rez !== '')
            {
                $img_attr = str_replace('%%image_source_name%%', 'Flickr', $img_attr);
                $img_attr = str_replace('%%image_source_url%%', $original_url, $img_attr);
                $img_attr = str_replace('%%image_source_website%%', 'https://www.flickr.com/', $img_attr);
            }
        }
        elseif($rand_arr[$rand] == 'pexels')
        {
            unset($rand_arr[$rand]);
            $rez = crawlomatic_get_pexels_image($crawlomatic_Main_Settings, $query_words, $original_url, $res_cnt);
            if($rez !== false && $rez !== '')
            {
                $img_attr = str_replace('%%image_source_name%%', 'Pexels', $img_attr);
                $img_attr = str_replace('%%image_source_url%%', $original_url, $img_attr);
                $img_attr = str_replace('%%image_source_website%%', 'https://www.pexels.com/', $img_attr);
            }
        }
        elseif($rand_arr[$rand] == 'pixabayscrape')
        {
            unset($rand_arr[$rand]);
            $rez = crawlomatic_scrape_pixabay_image($crawlomatic_Main_Settings, $query_words, $original_url);
            if($rez !== false && $rez !== '')
            {
                $img_attr = str_replace('%%image_source_name%%', 'Pixabay', $img_attr);
                $img_attr = str_replace('%%image_source_url%%', $original_url, $img_attr);
                $img_attr = str_replace('%%image_source_website%%', 'https://pixabay.com/', $img_attr);
            }
        }
        elseif($rand_arr[$rand] == 'unsplash')
        {
            unset($rand_arr[$rand]);
            $rez = crawlomatic_scrape_unsplash_image($query_words, $original_url);
            if($rez !== false && $rez !== '')
            {
                $img_attr = str_replace('%%image_source_name%%', 'Unsplash', $img_attr);
                $img_attr = str_replace('%%image_source_url%%', $original_url, $img_attr);
                $img_attr = str_replace('%%image_source_website%%', 'https://unsplash.com/', $img_attr);
            }
        }
        else
        {
            crawlomatic_log_to_file('Unrecognized free file source: ' . $rand_arr[$rand]);
            unset($rand_arr[$rand]);
        }
    }
    $img_attr = str_replace('%%image_source_name%%', '', $img_attr);
    $img_attr = str_replace('%%image_source_url%%', '', $img_attr);
    $img_attr = str_replace('%%image_source_website%%', '', $img_attr);
    return $rez;
}
function crawlomatic_get_all_redirects($url){
    $redirects = array();
    while ($newurl = crawlomatic_get_redirect_url($url)){
        if (in_array($newurl, $redirects)){
            break;
        }
        $redirects[] = $newurl;
        $url = $newurl;
    }
    return $redirects;
}

function crawlomatic_get_final_url($url){
    if (strpos($url, 'localhost') !== false)
    {
        return $url;
    }
    $redirects = crawlomatic_get_all_redirects($url);
    if (count($redirects)>0){
        return array_pop($redirects);
    } else {
        return $url;
    }
}
function crawlomatic_scrape_unsplash_image($query, &$original_url)
{
    $original_url = 'https://unsplash.com/';
    $feed_uri = 'https://source.unsplash.com/1600x900/';
    if($query != '')
    {
        $feed_uri .= '?' . urlencode($query);
    }
    error_reporting(0);
    $exec = get_headers($feed_uri);
    error_reporting(E_ALL);
    if ($exec === FALSE || !is_array($exec))
    {
        crawlomatic_log_to_file('Error while getting api url: ' . $feed_uri);
    }
    $nono = false;
    $locx = false;
    foreach($exec as $ex)
    {
        if(strstr($ex, 'Location:') !== false)
        {
            if(strstr($ex, 'source-404') !== false)
            {
                $nono = true;
            }
            $locx = $ex;
            $locx = preg_replace('/^Location: /', '', $locx);
            break;
        }
    }
    if($nono == true)
    {
        crawlomatic_log_to_file('NO image found on Unsplash for query: ' . $query);
        return false;
    }
    else
    {
        if($locx == false)
        {
            crawlomatic_log_to_file('Failed to parse response: ' . $feed_uri);
            return false;
        }
        $original_url = $locx;
        return $locx;
    }
}
function crawlomatic_generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function crawlomatic_get_redirect_url($url){
    $url_parts = parse_url($url);
    if (!$url_parts) return false;
    if (!isset($url_parts['host'])) return false;
    if (!isset($url_parts['path'])) $url_parts['path'] = '/';

    $sock = fsockopen($url_parts['host'], (isset($url_parts['port']) ? (int)$url_parts['port'] : 80), $errno, $errstr, 30);
    if (!$sock) return false;

    $request = "HEAD " . $url_parts['path'] . (isset($url_parts['query']) ? '?'.$url_parts['query'] : '') . " HTTP/1.1".PHP_EOL; 
    $request .= 'Host: ' . $url_parts['host'] . PHP_EOL; 
    $request .= "Connection: Close".PHP_EOL.PHP_EOL; 
    fwrite($sock, $request);
    $response = '';
    while(!feof($sock)) $response .= fread($sock, 8192);
    fclose($sock);

    if (preg_match('/^Location: (.+?)$/m', $response, $matches)){
        if ( substr($matches[1], 0, 1) == "/" )
            return $url_parts['scheme'] . "://" . $url_parts['host'] . trim($matches[1]);
        else
            return trim($matches[1]);

    } else {
        return false;
    }
}
function crawlomatic_get_pixabay_image($app_id, $query, $lang, $image_type, $orientation, $order, $image_category, $max_width, $min_width, $safe_search, $editors_choice, &$original_url, $get_max = 3)
{
    $original_url = 'https://pixabay.com';
    $featured_image = '';
    $feed_uri = 'https://pixabay.com/api/?key=' . $app_id;
    if($query != '')
    {
        $feed_uri .= '&q=' . urlencode($query);
    }
    $feed_uri .= '&per_page=' . $get_max;
    if($lang != '' && $lang != 'any')
    {
        $feed_uri .= '&lang=' . $lang;
    }
    if($image_type != '')
    {
        $feed_uri .= '&image_type=' . $image_type;
    }
    if($orientation != '')
    {
        $feed_uri .= '&orientation=' . $orientation;
    }
    if($order != '')
    {
        $feed_uri .= '&order=' . $order;
    }
    if($image_category != '')
    {
        $feed_uri .= '&category=' . $image_category;
    }
    if($max_width != '')
    {
        $feed_uri .= '&max_width=' . $max_width;
    }
    if($min_width != '')
    {
        $feed_uri .= '&min_width=' . $min_width;
    }
    if($safe_search == '1')
    {
        $feed_uri .= '&safesearch=true';
    }
    if($editors_choice == '1')
    {
        $feed_uri .= '&editors_choice=true';
    }
    $feed_uri .= '&callback=' . crawlomatic_generateRandomString(6);
     
    $exec = crawlomatic_get_web_page($feed_uri, '', '', '0', '', '', '', '');
    if ($exec !== FALSE) 
    {
        if (stristr($exec, '"hits"') !== FALSE) 
        {
            $exec = preg_replace('#^[a-zA-Z0-9]*#', '', $exec);
            $exec = trim($exec, '()');
            $json  = json_decode($exec);
            $items = $json->hits;
            if (count($items) != 0) 
            {
                shuffle($items);
                foreach($items as $item)
                {
                    $featured_image = $item->webformatURL;
                    $original_url = $item->pageURL;
                    break;
                }
            }
        }
        else
        {
            crawlomatic_log_to_file('Unknow response from api: ' . $feed_uri . ' - resp: ' . $exec);
            return false;
        }
    }
    else
    {
        crawlomatic_log_to_file('Error while getting api url: ' . $feed_uri);
        return false;
    }
    return $featured_image;
}
function crawlomatic_scrape_pixabay_image($crawlomatic_Main_Settings, $query, &$original_url)
{
    $original_url = 'https://pixabay.com';
    $featured_image = '';
    $feed_uri = 'https://pixabay.com/en/photos/';
    if($query != '')
    {
        $feed_uri .= '?q=' . urlencode($query);
    }

    if($crawlomatic_Main_Settings['scrapeimgtype'] != 'all')
    {
        $feed_uri .= '&image_type=' . $crawlomatic_Main_Settings['scrapeimgtype'];
    }
    if($crawlomatic_Main_Settings['scrapeimg_orientation'] != '')
    {
        $feed_uri .= '&orientation=' . $crawlomatic_Main_Settings['scrapeimg_orientation'];
    }
    if($crawlomatic_Main_Settings['scrapeimg_order'] != '' && $crawlomatic_Main_Settings['scrapeimg_order'] != 'any')
    {
        $feed_uri .= '&order=' . $crawlomatic_Main_Settings['scrapeimg_order'];
    }
    if($crawlomatic_Main_Settings['scrapeimg_cat'] != '')
    {
        $feed_uri .= '&category=' . $crawlomatic_Main_Settings['scrapeimg_cat'];
    }
    if($crawlomatic_Main_Settings['scrapeimg_height'] != '')
    {
        $feed_uri .= '&min_height=' . $crawlomatic_Main_Settings['scrapeimg_height'];
    }
    if($crawlomatic_Main_Settings['scrapeimg_width'] != '')
    {
        $feed_uri .= '&min_width=' . $crawlomatic_Main_Settings['scrapeimg_width'];
    }
    $exec = crawlomatic_get_web_page($feed_uri, '', '', '0', '', '', '', '');
    if ($exec !== FALSE) 
    {
        preg_match_all('/<a href="([^"]+?)".+?(?:data-lazy|src)="([^"]+?\.jpg|png)"/i', $exec, $matches);
        if (!empty($matches[2])) {
            $p = array_combine($matches[1], $matches[2]);
            if(count($p) > 0)
            {
                shuffle($p);
                foreach ($p as $key => $val) {
                    $featured_image = $val;
                    if(!is_numeric($key))
                    {
                        if(substr($key, 0, 4) !== "http")
                        {
                            $key = 'https://pixabay.com' . $key;
                        }
                        $original_url = $key;
                    }
                    else
                    {
                        $original_url = 'https://pixabay.com';
                    }
                    break;
                }
            }
        }
    }
    else
    {
        crawlomatic_log_to_file('Error while getting api url: ' . $feed_uri);
        return false;
    }
    return $featured_image;
}
function crawlomatic_get_morguefile_image($app_id, $app_secret, $query, &$original_url)
{
    $featured_image = '';
    if(!class_exists('crawlomatic_morguefile'))
    {
        require_once (dirname(__FILE__) . "/res/morguefile/mf.api.class.php");
    }
    $query = explode(' ', $query);
    $query = $query[0];
    {
        $mf = new crawlomatic_morguefile($app_id, $app_secret);
        $rez = $mf->call('/images/search/sort/page/' . $query);
        if ($rez !== FALSE) 
        {
            $chosen_one = $rez->doc[array_rand($rez->doc)];
            if (isset($chosen_one->file_path_large)) 
            {
                return $chosen_one->file_path_large;
            }
            else
            {
                return false;
            }
        }
        else
        {
            crawlomatic_log_to_file('Error while getting api response from morguefile.');
            return false;
        }
    }
    return $featured_image;
}
function crawlomatic_get_flickr_image($crawlomatic_Main_Settings, $query, &$original_url, $max)
{
    $original_url = 'https://www.flickr.com';
    $featured_image = '';
    $feed_uri = 'https://api.flickr.com/services/rest/?method=flickr.photos.search&api_key=' . $crawlomatic_Main_Settings['flickr_api'] . '&media=photos&per_page=' . esc_html($max) . '&format=php_serial&text=' . urlencode($query);
    if(isset($crawlomatic_Main_Settings['flickr_license']) && $crawlomatic_Main_Settings['flickr_license'] != '-1')
    {
        $feed_uri .= '&license=' . $crawlomatic_Main_Settings['flickr_license'];
    }
    if(isset($crawlomatic_Main_Settings['flickr_order']) && $crawlomatic_Main_Settings['flickr_order'] != '')
    {
        $feed_uri .= '&sort=' . $crawlomatic_Main_Settings['flickr_order'];
    }
    $feed_uri .= '&extras=description,license,date_upload,date_taken,owner_name,icon_server,original_format,last_update,geo,tags,machine_tags,o_dims,views,media,path_alias,url_sq,url_t,url_s,url_q,url_m,url_n,url_z,url_c,url_l,url_o';
     
    {
        $ch               = curl_init();
        if ($ch === FALSE) {
            crawlomatic_log_to_file('Failed to init curl for flickr!');
            return false;
        }
        if (isset($crawlomatic_Main_Settings['request_timeout']) && $crawlomatic_Main_Settings['request_timeout'] != '') {
            $timeout = intval($crawlomatic_Main_Settings['request_timeout']);
        } else {
            $timeout = 10;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Referer: https://www.flickr.com/'));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_URL, $feed_uri);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $exec = curl_exec($ch);
        curl_close($ch);
        if (stristr($exec, 'photos') === FALSE) {
            crawlomatic_log_to_file('Unrecognized Flickr API response: ' . $exec . ' URI: ' . $feed_uri);
            return false;
        }
        $items = unserialize ( $exec );
        if(!isset($items['photos']['photo']))
        {
            crawlomatic_log_to_file('Failed to find photo node in response: ' . $exec . ' URI: ' . $feed_uri);
            return false;
        }
        if(count($items['photos']['photo']) == 0)
        {
            return $featured_image;
        }
        $x = 0;
        shuffle($items['photos']['photo']);
        while($featured_image == '' && isset($items['photos']['photo'][$x]))
        {
            $item = $items['photos']['photo'][$x];
            if(isset($item['url_o']))
            {
                $featured_image = $item['url_o'];
            }
            elseif(isset($item['url_l']))
            {
                $featured_image = $item['url_l'];
            }
            elseif(isset($item['url_c']))
            {
                $featured_image = $item['url_c'];
            }
            elseif(isset($item['url_z']))
            {
                $featured_image = $item['url_z'];
            }
            elseif(isset($item['url_n']))
            {
                $featured_image = $item['url_n'];
            }
            elseif(isset($item['url_m']))
            {
                $featured_image = $item['url_m'];
            }
            elseif(isset($item['url_q']))
            {
                $featured_image = $item['url_q'];
            }
            elseif(isset($item['url_s']))
            {
                $featured_image = $item['url_s'];
            }
            elseif(isset($item['url_t']))
            {
                $featured_image = $item['url_t'];
            }
            elseif(isset($item['url_sq']))
            {
                $featured_image = $item['url_sq'];
            }
            if($featured_image != '')
            {
                $original_url = esc_url('https://www.flickr.com/photos/' . $item['owner'] . '/' . $item['id']);
            }
            $x++;
        }
    }
    return $featured_image;
}
function crawlomatic_endsWith($haystack, $needle)
{
    $length = strlen($needle);
    if ($length == 0) {
        return true;
    }

    return (substr($haystack, -$length) === $needle);
}
function crawlomatic_get_pexels_image($crawlomatic_Main_Settings, $query, &$original_url, $max)
{
    $original_url = 'https://pexels.com';
    $featured_image = '';
    $feed_uri = 'https://api.pexels.com/v1/search?query=' . urlencode($query) . '&per_page=' . $max;
     
    {
        $ch               = curl_init();
        if ($ch === FALSE) {
            crawlomatic_log_to_file('Failed to init curl for flickr!');
            return false;
        }
        if (isset($crawlomatic_Main_Settings['request_timeout']) && $crawlomatic_Main_Settings['request_timeout'] != '') {
            $timeout = intval($crawlomatic_Main_Settings['request_timeout']);
        } else {
            $timeout = 10;
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: ' . $crawlomatic_Main_Settings['pexels_api']));
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPGET, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_URL, $feed_uri);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        $exec = curl_exec($ch);
        curl_close($ch);
        if (stristr($exec, 'photos') === FALSE) {
            crawlomatic_log_to_file('Unrecognized Pexels API response: ' . $exec . ' URI: ' . $feed_uri);
            return false;
        }
        $items = json_decode ( $exec, true );
        if(!isset($items['photos']))
        {
            crawlomatic_log_to_file('Failed to find photo node in Pexels response: ' . $exec . ' URI: ' . $feed_uri);
            return false;
        }
        if(count($items['photos']) == 0)
        {
            return $featured_image;
        }
        $x = 0;
        shuffle($items['photos']);
        while($featured_image == '' && isset($items['photos'][$x]))
        {
            $item = $items['photos'][$x];
            if(isset($item['src']['large']))
            {
                $featured_image = $item['src']['large'];
            }
            elseif(isset($item['src']['medium']))
            {
                $featured_image = $item['src']['medium'];
            }
            elseif(isset($item['src']['small']))
            {
                $featured_image = $item['src']['small'];
            }
            elseif(isset($item['src']['portrait']))
            {
                $featured_image = $item['src']['portrait'];
            }
            elseif(isset($item['src']['landscape']))
            {
                $featured_image = $item['src']['landscape'];
            }
            elseif(isset($item['src']['original']))
            {
                $featured_image = $item['src']['original'];
            }
            elseif(isset($item['src']['tiny']))
            {
                $featured_image = $item['src']['tiny'];
            }
            if($featured_image != '')
            {
                $original_url = $item['url'];
            }
            $x++;
        }
    }
    return $featured_image;
}


function crawlomatic_url_handle($href, $api_key)
{
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, "https://api.shorte.st/v1/data/url");
    curl_setopt($ch, CURLOPT_POSTFIELDS, "urlToShorten=" . trim($href));
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "PUT");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    $headers = [
        'public-api-token: ' . $api_key,
        'Content-Type: application/x-www-form-urlencoded'
    ];
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT,10);
    $serverOutput = json_decode(curl_exec($ch), true);
    curl_close($ch);
    if (!isset($serverOutput['shortenedUrl']) || $serverOutput['shortenedUrl'] == '') {
        return $href;
    } else {
        return esc_url($serverOutput['shortenedUrl']);
    }  
}

function crawlomatic_spin_text($title, $content, $alt = false)
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    $titleSeparator = '[19459000]';
    $text           = $title . ' ' . $titleSeparator . ' ' . $content;
    $text           = html_entity_decode($text);
    preg_match_all("/<[^<>]+>/is", $text, $matches, PREG_PATTERN_ORDER);
    $htmlfounds         = array_filter(array_unique($matches[0]));
    $htmlfounds[]       = '&quot;';
    $imgFoundsSeparated = array();
    foreach ($htmlfounds as $key => $currentFound) {
        if (stristr($currentFound, '<img') && stristr($currentFound, 'alt')) {
            $altSeparator   = '';
            $colonSeparator = '';
            if (stristr($currentFound, 'alt="')) {
                $altSeparator   = 'alt="';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt = "')) {
                $altSeparator   = 'alt = "';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt ="')) {
                $altSeparator   = 'alt ="';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt= "')) {
                $altSeparator   = 'alt= "';
                $colonSeparator = '"';
            } elseif (stristr($currentFound, 'alt=\'')) {
                $altSeparator   = 'alt=\'';
                $colonSeparator = '\'';
            } elseif (stristr($currentFound, 'alt = \'')) {
                $altSeparator   = 'alt = \'';
                $colonSeparator = '\'';
            } elseif (stristr($currentFound, 'alt= \'')) {
                $altSeparator   = 'alt= \'';
                $colonSeparator = '\'';
            } elseif (stristr($currentFound, 'alt =\'')) {
                $altSeparator   = 'alt =\'';
                $colonSeparator = '\'';
            }
            if (trim($altSeparator) != '') {
                $currentFoundParts = explode($altSeparator, $currentFound);
                $preAlt            = $currentFoundParts[1];
                $preAltParts       = explode($colonSeparator, $preAlt);
                $altText           = $preAltParts[0];
                if (trim($altText) != '') {
                    unset($preAltParts[0]);
                    $imgFoundsSeparated[] = $currentFoundParts[0] . $altSeparator;
                    $imgFoundsSeparated[] = $colonSeparator . implode('', $preAltParts);
                    $htmlfounds[$key]     = '';
                }
            }
        }
    }
    if (count($imgFoundsSeparated) != 0) {
        $htmlfounds = array_merge($htmlfounds, $imgFoundsSeparated);
    }
    preg_match_all("/<\!--.*?-->/is", $text, $matches2, PREG_PATTERN_ORDER);
    $newhtmlfounds = $matches2[0];
    preg_match_all("/\[.*?\]/is", $text, $matches3, PREG_PATTERN_ORDER);
    $shortcodesfounds = $matches3[0];
    $htmlfounds       = array_merge($htmlfounds, $newhtmlfounds, $shortcodesfounds);
    $in               = 0;
    $cleanHtmlFounds  = array();
    foreach ($htmlfounds as $htmlfound) {
        if ($htmlfound == '[19459000]') {
        } elseif (trim($htmlfound) == '') {
        } else {
            $cleanHtmlFounds[] = $htmlfound;
        }
    }
    $htmlfounds = $cleanHtmlFounds;
    $start      = 19459001;
    foreach ($htmlfounds as $htmlfound) {
        $text = str_replace($htmlfound, '[' . $start . ']', $text);
        $start++;
    }
    try {
        require_once(dirname(__FILE__) . "/res/crawlomatic-text-spinner.php");
        $phpTextSpinner = new PhpTextSpinner();
        if ($alt === FALSE) {
            $spinContent = $phpTextSpinner->spinContent($text);
        } else {
            $spinContent = $phpTextSpinner->spinContentAlt($text);
        }
        $translated = $phpTextSpinner->runTextSpinner($spinContent);
    }
    catch (Exception $e) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('Exception thrown in spinText ' . $e);
        }
        return false;
    }
    preg_match_all('{\[.*?\]}', $translated, $brackets);
    $brackets = $brackets[0];
    $brackets = array_unique($brackets);
    foreach ($brackets as $bracket) {
        if (stristr($bracket, '19')) {
            $corrrect_bracket = str_replace(' ', '', $bracket);
            $corrrect_bracket = str_replace('.', '', $corrrect_bracket);
            $corrrect_bracket = str_replace(',', '', $corrrect_bracket);
            $translated       = str_replace($bracket, $corrrect_bracket, $translated);
        }
    }
    if (stristr($translated, $titleSeparator)) {
        $start = 19459001;
        foreach ($htmlfounds as $htmlfound) {
            $translated = str_replace('[' . $start . ']', $htmlfound, $translated);
            $start++;
        }
        $contents = explode($titleSeparator, $translated);
        $title    = $contents[0];
        $content  = $contents[1];
    } else {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('Failed to parse spinned content, separator not found');
        }
        return false;
    }
    return array(
        $title,
        $content
    );
}
function crawlomatic_removeTagByClass(string $html, string $className) 
{
    if($html == '')
    {
        return '';
    }
    $dom = new \DOMDocument();
    $dom->loadHTML($html);
    $finder = new \DOMXPath($dom);
    $nodes = $finder->query("//*[contains(concat(' ', normalize-space(@class), ' '), ' {$className} ')]");
    foreach ($nodes as $node) {
        $node->parentNode->removeChild($node);
    }
    return $dom->saveHTML();
}
function crawlomatic_removeTagByID(string $html, string $className) 
{
    if($html == '')
    {
        return '';
    }
    $dom = new \DOMDocument();
    $dom->loadHTML($html);
    $finder = new \DOMXPath($dom);
    $nodes = $finder->query('//*[@id="' . trim($className) . '"]');
    foreach ($nodes as $node) {
        $node->parentNode->removeChild($node);
    }
    return $dom->saveHTML();
}
function crawlomatic_removeTagByXPath(string $html, string $className) 
{
    if($html == '')
    {
        return '';
    }
    $dom = new \DOMDocument();
    $dom->loadHTML($html);
    $finder = new \DOMXPath($dom);
    $nodes = $finder->query(trim($className));
    foreach ($nodes as $node) {
        $node->parentNode->removeChild($node);
    }
    return $dom->saveHTML();
}
function crawlomatic_removeHTMLByXPath(string $html, string $className) 
{
    if($html == '')
    {
        return '';
    }
    $dom = new \DOMDocument();
    $dom->loadHTML($html);
    $finder = new \DOMXPath($dom);
    $nodes = $finder->query(trim($className));
    foreach ($nodes as $node) {
        $node->parentNode->replaceChild($dom->createTextNode($node->nodeValue), $node);
    }
    return $dom->saveHTML();
}
function crawlomatic_removeTagByTag(string $html, string $className) 
{
    if($html == '')
    {
        return '';
    }
    $dom = new \DOMDocument();
    $dom->loadHTML($html);
    $finder = new \DOMXPath($dom);
    $nodes = $finder->query("//" . trim($className));
    foreach ($nodes as $node) {
        $node->parentNode->removeChild($node);
    }
    return $dom->saveHTML();
}
function crawlomatic_best_spin_text($title, $content, $user_name = '', $pass = '')
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    $data             = array();
    if($user_name != '' && $pass != '')
    {
        $data['username'] = $user_name;
        $data['password'] = $pass;
    }
    else
    {
        if (!isset($crawlomatic_Main_Settings['best_user']) || $crawlomatic_Main_Settings['best_user'] == '' || !isset($crawlomatic_Main_Settings['best_password']) || $crawlomatic_Main_Settings['best_password'] == '') {
            crawlomatic_log_to_file('Please insert a valid "The Best Spinner" user name and password.');
            return FALSE;
        }
        $data['username'] = $crawlomatic_Main_Settings['best_user'];
        $data['password'] = $crawlomatic_Main_Settings['best_password'];
    }
    $titleSeparator   = '[19459000]';
    $newhtml             = $title . ' ' . $titleSeparator . ' ' . $content;
    $url              = 'http://thebestspinner.com/api.php';
    
    $data['action']   = 'authenticate';
    $data['format']   = 'php';
    $ch               = curl_init();
    if ($ch === FALSE) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('"The Best Spinner" failed to init curl.');
        }
        return FALSE;
    }
    if (isset($crawlomatic_Main_Settings['request_timeout']) && $crawlomatic_Main_Settings['request_timeout'] != '') {
        $timeout = intval($crawlomatic_Main_Settings['request_timeout']);
    } else {
        $timeout = 10;
    }
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    $fdata = "";
    foreach ($data as $key => $val) {
        $fdata .= "$key=" . urlencode($val) . "&";
    }
    curl_setopt($ch, CURLOPT_POSTFIELDS, $fdata);
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_REFERER, $url);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $html = crawlomatic_curl_exec_utf8($ch);
    curl_close($ch);
    if ($html === FALSE || empty($html)) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('"The Best Spinner" failed to exec curl.');
        }
        return FALSE;
    }
    $output = unserialize($html);
    if ($output['success'] == 'true') {
        $session                = $output['session'];
        $data                   = array();
        $data['session']        = $session;
        $data['format']         = 'php';
        if (isset($crawlomatic_Main_Settings['protected_terms']) && $crawlomatic_Main_Settings['protected_terms'] != '') 
        {
            $protected_terms = $crawlomatic_Main_Settings['protected_terms'];
        }
        else
        {
            $protected_terms = '';
        }
        $data['protectedterms'] = $protected_terms;
        $data['action']         = 'replaceEveryonesFavorites';
        $data['maxsyns']        = '100';
        $data['quality']        = '1';
        $ch = curl_init();
        if ($ch === FALSE) {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                crawlomatic_log_to_file('Failed to init curl');
            }
            return FALSE;
        }
        $newhtml = html_entity_decode($newhtml);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_REFERER, $url);
        $spinned = '';
        if(str_word_count($newhtml) > 4000)
        {
            while($newhtml != '')
            {
                $first30k = substr($newhtml, 0, 30000);
                $first30k = rtrim($first30k, '(*');
                $first30k = ltrim($first30k, ')*');
                $newhtml = substr($newhtml, 30000);
                $data['text']           = $first30k;
                $fdata = "";
                foreach ($data as $key => $val) {
                    $fdata .= "$key=" . urlencode($val) . "&";
                }
                curl_setopt($ch, CURLOPT_POSTFIELDS, $fdata);
                $output = curl_exec($ch);
                if ($output === FALSE) {
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                        crawlomatic_log_to_file('"The Best Spinner" failed to exec curl after auth.');
                    }
                    return FALSE;
                }
                $output = unserialize($output);
                if ($output['success'] == 'true') {
                    $spinned .= ' ' . $output['output'];
                } else {
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                        crawlomatic_log_to_file('"The Best Spinner" failed to spin article.');
                    }
                    return FALSE;
                }
            }
        }
        else
        {
            $data['text'] = $newhtml;
            $fdata = "";
            foreach ($data as $key => $val) {
                $fdata .= "$key=" . urlencode($val) . "&";
            }
            curl_setopt($ch, CURLOPT_POSTFIELDS, $fdata);
            $output = curl_exec($ch);
            if ($output === FALSE) {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                    crawlomatic_log_to_file('"The Best Spinner" failed to exec curl after auth.');
                }
                return FALSE;
            }
            $output = unserialize($output);
            if ($output['success'] == 'true') {
                $spinned = $output['output'];
            } else {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                    crawlomatic_log_to_file('"The Best Spinner" failed to spin article: ' . print_r($output, true));
                }
                return FALSE;
            }
        }
        curl_close($ch);
        $result = explode($titleSeparator, $spinned);
        if (count($result) < 2) {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                crawlomatic_log_to_file('"The Best Spinner" failed to spin article - titleseparator not found.' . print_r($output, true));
            }
            return FALSE;
        }
        $spintax = new Crawlomatic_Spintax();
        $result[0] = $spintax->process($result[0]);
        $result[1] = $spintax->process($result[1]);
        return $result;

    } else {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('"The Best Spinner" authentification failed. ' . print_r($output, true));
        }
        return FALSE;
    }
}

class Crawlomatic_Spintax
{
    public function process($text)
    {
        return stripslashes(preg_replace_callback(
            '/\{(((?>[^\{\}]+)|(?R))*)\}/x',
            array($this, 'replace'),
            preg_quote($text)
        ));
    }
    public function replace($text)
    {
        $text = $this->process($text[1]);
        $parts = explode('|', $text);
        return $parts[array_rand($parts)];
    }
}
function crawlomatic_wordai_spin_text($title, $content, $user_name = '', $pass = '')
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if($user_name != '' && $pass != '')
    {
        $email = $user_name;
        $pass = $pass;
    }
    else
    {
        if (!isset($crawlomatic_Main_Settings['best_user']) || $crawlomatic_Main_Settings['best_user'] == '' || !isset($crawlomatic_Main_Settings['best_password']) || $crawlomatic_Main_Settings['best_password'] == '') {
            crawlomatic_log_to_file('Please insert a valid "Wordai" user name and password.');
            return FALSE;
        }
        $email = $crawlomatic_Main_Settings['best_user'];
        $pass = $crawlomatic_Main_Settings['best_password'];
    }
    
    $titleSeparator   = '[19459000]';
    $html             = $title . ' ' . $titleSeparator . ' ' . $content;
    
    $html = urlencode($html);
    $ch = curl_init('https://wai.wordai.com/api/rewrite');
    if($ch === false)
    {
        crawlomatic_log_to_file('Failed to init curl in wordai spinning.');
        return FALSE;
    }
    if (isset($crawlomatic_Main_Settings['request_timeout']) && $crawlomatic_Main_Settings['request_timeout'] != '') {
        $timeout = intval($crawlomatic_Main_Settings['request_timeout']);
    } else {
        $timeout = 10;
    }
    if (isset($crawlomatic_Main_Settings['wordai_uniqueness']) && $crawlomatic_Main_Settings['wordai_uniqueness'] != '') 
    {
        $wordai_uniqueness = trim($crawlomatic_Main_Settings['wordai_uniqueness']);
    }
    else
    {
        $wordai_uniqueness = '2';
    }
    if($wordai_uniqueness != '1' && $wordai_uniqueness != '2' && $wordai_uniqueness != '3')
    {
        $wordai_uniqueness = '2';
    }
    curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt ($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    curl_setopt ($ch, CURLOPT_POSTFIELDS, "input=$html&uniqueness=" . $wordai_uniqueness . "&rewrite_num=4&return_rewrites=true&email=$email&key=$pass");
    curl_setopt ($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt ($ch, CURLOPT_SSL_VERIFYPEER, 0);
    $result = curl_exec($ch);
    
    if ($result === FALSE) {
        crawlomatic_log_to_file('"Wordai" failed to exec curl after auth. URL: https://wai.wordai.com/api/rewrite , POST: ' . "input=$html&uniqueness=" . $wordai_uniqueness . "&rewrite_num=4&return_rewrites=true&email=$email&key=$pass" . ' -- ERROR: ' . curl_error($ch));
        curl_close ($ch);
        return FALSE;
    }
    curl_close ($ch);
    $result = json_decode($result);
    if(!isset($result->rewrites))
    {
        crawlomatic_log_to_file('"Wordai" unrecognized response: ' . print_r($result, true));
        return FALSE;
    }
    $result = explode($titleSeparator, $result->rewrites[0]);
    if (count($result) < 2) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('"Wordai" failed to spin article - titleseparator not found.');
        }
        return FALSE;
    }
    return $result;
}

function crawlomatic_spinrewriter_spin_text($title, $content)
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (!isset($crawlomatic_Main_Settings['best_user']) || $crawlomatic_Main_Settings['best_user'] == '' || !isset($crawlomatic_Main_Settings['best_password']) || $crawlomatic_Main_Settings['best_password'] == '') {
        crawlomatic_log_to_file('Please insert a valid "SpinRewriter" user name and password.');
        return FALSE;
    }
    $titleSeparator = '(19459000)';
    $quality = '50';
    $html = $title . ' ' . $titleSeparator . ' ' . $content;
    $html = preg_replace('/\s+/', ' ', $html);
    $html = urlencode($html);
    $data = array();
    $data['email_address'] = $crawlomatic_Main_Settings['best_user'];
    $data['api_key'] = $crawlomatic_Main_Settings['best_password'];
    $data['action'] = "unique_variation";
    $data['auto_protected_terms'] = "true";					
    $data['confidence_level'] = "high";							
    $data['auto_sentences'] = "true";							
    $data['auto_paragraphs'] = "false";							
    $data['auto_new_paragraphs'] = "false";						
    $data['auto_sentence_trees'] = "false";						
    $data['use_only_synonyms'] = "true";						
    $data['reorder_paragraphs'] = "false";						
    $data['nested_spintax'] = "false";							
    if (isset($crawlomatic_Main_Settings['protected_terms']) && $crawlomatic_Main_Settings['protected_terms'] != '') 
    {
        $protected_terms = $crawlomatic_Main_Settings['protected_terms'];
        $data['protected_terms'] = str_replace(',', '\n', $protected_terms);
    }
    if(str_word_count($html) >= 3950)
    {
        $result = '';
        while($html != '' && $html != ' ')
        {
            $words = explode("+", $html);
            $first30k = join("+", array_slice($words, 0, 3950));
            $html = join("+", array_slice($words, 3950));
            
            $data['text'] = $first30k;	
            $api_response = crawlomatic_spinrewriter_api_post($data);
            if ($api_response === FALSE) {
                crawlomatic_log_to_file('"SpinRewriter" failed to exec curl after auth.');
                return FALSE;
            }
            $api_response = json_decode($api_response);
            if(!isset($api_response->response) || !isset($api_response->status) || $api_response->status != 'OK')
            {
                if(isset($api_response->status) && $api_response->status == 'ERROR')
                {
                    if(isset($api_response->response) && $api_response->response == 'You can only submit entirely new text for analysis once every 7 seconds.')
                    {
                        $api_response = crawlomatic_spinrewriter_api_post($data);
                        if ($api_response === FALSE) {
                            crawlomatic_log_to_file('"SpinRewriter" failed to exec curl after auth (after resubmit).');
                            return FALSE;
                        }
                        $api_response = json_decode($api_response);
                        if(!isset($api_response->response) || !isset($api_response->status) || $api_response->status != 'OK')
                        {
                            crawlomatic_log_to_file('"SpinRewriter" failed to wait and resubmit spinning: ' . print_r($api_response, true) . ' params: ' . print_r($data, true));
                            return FALSE;
                        }
                    }
                    else
                    {
                        crawlomatic_log_to_file('"SpinRewriter" error response: ' . print_r($api_response, true) . ' params: ' . print_r($data, true));
                        return FALSE;
                    }
                }
                else
                {
                    crawlomatic_log_to_file('"SpinRewriter" error response: ' . print_r($api_response, true) . ' params: ' . print_r($data, true));
                    return FALSE;
                }
            }
            $api_response->response = str_replace(' ', '', $api_response->response);
            $spinned = urldecode($api_response->response);
            $result .= ' ' . $spinned;
        }
    }
    else
    {
        $data['text'] = $html;	
        $api_response = crawlomatic_spinrewriter_api_post($data);
        if ($api_response === FALSE) {
            crawlomatic_log_to_file('"SpinRewriter" failed to exec curl after auth.');
            return FALSE;
        }
        $api_response = json_decode($api_response);
        if(!isset($api_response->response) || !isset($api_response->status) || $api_response->status != 'OK')
        {
            if(isset($api_response->status) && $api_response->status == 'ERROR')
            {
                if(isset($api_response->response) && $api_response->response == 'You can only submit entirely new text for analysis once every 7 seconds.')
                {
                    $api_response = crawlomatic_spinrewriter_api_post($data);
                    if ($api_response === FALSE) {
                        crawlomatic_log_to_file('"SpinRewriter" failed to exec curl after auth (after resubmit).');
                        return FALSE;
                    }
                    $api_response = json_decode($api_response);
                    if(!isset($api_response->response) || !isset($api_response->status) || $api_response->status != 'OK')
                    {
                        crawlomatic_log_to_file('"SpinRewriter" failed to wait and resubmit spinning: ' . print_r($api_response, true) . ' params: ' . print_r($data, true));
                        return FALSE;
                    }
                }
                else
                {
                    crawlomatic_log_to_file('"SpinRewriter" error response: ' . print_r($api_response, true) . ' params: ' . print_r($data, true));
                    return FALSE;
                }
            }
            else
            {
                crawlomatic_log_to_file('"SpinRewriter" error response: ' . print_r($api_response, true) . ' params: ' . print_r($data, true));
                return FALSE;
            }
        }
        $api_response->response = str_replace(' ', '', $api_response->response);
        $result = urldecode($api_response->response);
    }
    $result = explode($titleSeparator, $result);
    if (count($result) < 2) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('"SpinRewriter" failed to spin article - titleseparator not found: ' . $api_response->response);
        }
        return FALSE;
    }
    return $result;
}

function crawlomatic_turkcespin_spin_text($title, $content, $user_name = '')
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if($user_name != '')
    {
        $appi = $user_name;
    }
    else
    {
        if ((!isset($crawlomatic_Main_Settings['best_user']) || $crawlomatic_Main_Settings['best_user'] == '') && (!isset($crawlomatic_Main_Settings['best_password']) || $crawlomatic_Main_Settings['best_password'] == '')) {
            crawlomatic_log_to_file('Please insert a valid "TurkceSpin" user name and password.');
            return FALSE;
        }
        if(!isset($crawlomatic_Main_Settings['best_password']) || $crawlomatic_Main_Settings['best_password'] == '')
        {
            $appi = $crawlomatic_Main_Settings['best_user'];
        }
        else
        {
            $appi = $crawlomatic_Main_Settings['best_password'];
        }
    }
    $titleSeparator   = '[19459000]';
    $html             = $title . ' ' . $titleSeparator . ' ' . $content;
    $postData = array(
        'token'  => $appi,
        'article' => $html
    );
    if (isset($crawlomatic_Main_Settings['request_timeout']) && $crawlomatic_Main_Settings['request_timeout'] != '') {
        $timeout = intval($crawlomatic_Main_Settings['request_timeout']);
    } else {
        $timeout = 10;
    }
    $ch = curl_init("http://turkcespin.com/api/spin");
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($postData));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    $api_response = curl_exec ($ch);
    curl_close($ch);
    if ($api_response === FALSE) {
        crawlomatic_log_to_file('"TurkceSpin" failed to exec curl after auth.');
        return FALSE;
    }
    $api_response = json_decode($api_response);
    if(!isset($api_response->article) || !isset($api_response->status) || ($api_response->status != 'ok' && $api_response->status != 'OK'))
    {
        crawlomatic_log_to_file('"TurkceSpin" error response: ' . print_r($api_response, true) . ' params: ' . print_r($appi, true) . ' --- ' . print_r($html, true));
        return FALSE;
    }
    $api_response = urldecode($api_response->article);
    $result = explode($titleSeparator, $api_response);
    if (count($result) < 2) {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('"TurkceSpin" failed to spin article - titleseparator not found.');
        }
        return FALSE;
    }
    return $result;
}

function crawlomatic_spinrewriter_api_post($data){
	$data_raw = "";
    
    $GLOBALS['wp_object_cache']->delete('crspinrewriter_spin_time', 'options');
    $spin_time = get_option('crspinrewriter_spin_time', false);
    if($spin_time !== false && is_numeric($spin_time))
    {
        $c_time = time();
        $spassed = $c_time - $spin_time;
        if($spassed < 10 && $spassed >= 0)
        {
            sleep(10 - $spassed);
        }
    }
    update_option('crspinrewriter_spin_time', time());
    
	foreach ($data as $key => $value){
		$data_raw = $data_raw . $key . "=" . urlencode($value) . "&";
	}
	$ch = curl_init();
    if($ch === false)
    {
        return false;
    }
	curl_setopt($ch, CURLOPT_URL, "http://www.spinrewriter.com/action/api");
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS, $data_raw);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT,10);
	$response = trim(curl_exec($ch));
	curl_close($ch);
	return $response;
}

function crawlomatic_get_title($content)
{
    preg_match('{<meta[^<]*?property=["\']og:title["\'][^<]*?>}i', $content, $mathc);
    if(isset($mathc[0]) && stristr($mathc[0], 'content')){
        preg_match('{content\s*=["\'](.*?)["\']}s', $mathc[0],$matx);
        if(isset($matx[1]))
        {
            $auth = $matx[1];
            if(trim($auth) !='')
            {
                return $auth;
            }
        }
    }
    preg_match('{<meta[^<]*?property=["\']twitter:title["\'][^<]*?>}i', $content, $mathc);
    if(isset($mathc[0]) && stristr($mathc[0], 'content')){
        preg_match('{content\s*=["\'](.*?)["\']}s', $mathc[0],$matx);
        if(isset($matx[1]))
        {
            $auth = $matx[1];
            if(trim($auth) !='')
            {
                return $auth;
            }
        }
    }
    preg_match('{<meta[^<]*?itemprop\s*=["\']title["\'][^<]*?>}i', $content, $mathc);
    if(isset($mathc[0]) && stristr($mathc[0], 'content=')){
        preg_match('{content\s*=["\'](.*?)["\']}s', $mathc[0],$matx);
        if(isset($matx[1]))
        {
            $auth = $matx[1];
            if(trim($auth) !='')
            {
                return $auth;
            }
        }
    }
    preg_match('{<meta[^<]*?itemprop\s*=["\']headline["\'][^<]*?>}i', $content, $mathc);
    if(isset($mathc[0]) && stristr($mathc[0], 'content=')){
        preg_match('{content\s*=["\'](.*?)["\']}s', $mathc[0],$matx);
        if(isset($matx[1]))
        {
            $auth = $matx[1];
            if(trim($auth) !='')
            {
                return $auth;
            }
        }
    }
    preg_match('{<title(?:[^>]*?)>([^<]*?)<\/title>}i', $content, $mathc);
    if(isset($mathc[1][0])){
        $auth = $mathc[1][0];
        if(trim($auth) !='')
        {
            return $auth;
        }
    }
    return ''; 
}
function crawlomatic_get_author($content)
{
    preg_match('{<meta[^<]*?name=["\']author["\'][^<]*?>}s', $content, $mathc);
    if(isset($mathc[0]) && stristr($mathc[0], 'author')){
        preg_match('{content\s*=["\'](.*?)["\']}s', $mathc[0],$matx);
        if(isset($matx[1]))
        {
            $auth = $matx[1];
            if(trim($auth) !='')
            {
                return $auth;
            }
        }
    }
    preg_match('{<meta[^<]*?name=["\']dc.creator["\'][^<]*?>}s', $content, $mathc);
    if(isset($mathc[0]) && stristr($mathc[0], 'content')){
        preg_match('{content\s*=["\'](.*?)["\']}s', $mathc[0],$matx);
        if(isset($matx[1]))
        {
            $auth = $matx[1];
            if(trim($auth) !='')
            {
                return $auth;
            }
        }
    }
    preg_match('{<meta[^<]*?property=["\']article:author["\'][^<]*?>}s', $content, $mathc);
    if(isset($mathc[0]) && stristr($mathc[0], 'content')){
        preg_match('{content\s*=["\'](.*?)["\']}s', $mathc[0],$matx);
        if(isset($matx[1]))
        {
            $auth = $matx[1];
            if(trim($auth) !='')
            {
                return $auth;
            }
        }
    }
    return ''; 
}
function crawlomatic_replaceExecludes($article, &$htmlfounds, $opt = false, $no_nr = false)
{
    $htmlurls = array();$article = preg_replace('{data-image-description="(?:[^\"]*?)"}i', '', $article);
	if($opt === true){
		preg_match_all( "/<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*?)<\/a>/s" ,$article,$matches,PREG_PATTERN_ORDER);
		$htmlurls=$matches[0];
	}
	$urls_txt = array();
	if($opt === true){
		preg_match_all('/https?:\/\/[^<\s]+/', $article,$matches_urls_txt);
		$urls_txt = $matches_urls_txt[0];
	}
	preg_match_all("/<[^<>]+>/is",$article,$matches,PREG_PATTERN_ORDER);
	$htmlfounds=$matches[0];
	preg_match_all('{\[nospin\].*?\[/nospin\]}s', $article,$matches_ns);
	$nospin = $matches_ns[0];
	$pattern="\[.*?\]";
	preg_match_all("/".$pattern."/s",$article,$matches2,PREG_PATTERN_ORDER);
	$shortcodes=$matches2[0];
	preg_match_all("/<script.*?<\/script>/is",$article,$matches3,PREG_PATTERN_ORDER);
	$js=$matches3[0];
    if($no_nr == true)
    {
        $nospin_nums = array();
    }
    else
    {
        preg_match_all('/\d{2,}/s', $article,$matches_nums);
        $nospin_nums = $matches_nums[0];
        sort($nospin_nums);
        $nospin_nums = array_reverse($nospin_nums);
    }
	$capped = array();
	if($opt === true){
		preg_match_all("{\b[A-Z][a-z']+\b[,]?}", $article,$matches_cap);
		$capped = $matches_cap[0];
		sort($capped);
		$capped=array_reverse($capped);
	}
	$curly_quote = array();
	if($opt === true){
		preg_match_all('{???.*????}', $article, $matches_curly_txt);
		$curly_quote = $matches_curly_txt[0];
		preg_match_all('{???.*????}', $article, $matches_curly_txt_s);
		$single_curly_quote = $matches_curly_txt_s[0];
		preg_match_all('{&quot;.*?&quot;}', $article, $matches_curly_txt_s_and);
		$single_curly_quote_and = $matches_curly_txt_s_and[0];
		preg_match_all('{&#8220;.*?&#8221}', $article, $matches_curly_txt_s_and_num);
		$single_curly_quote_and_num = $matches_curly_txt_s_and_num[0];
		$curly_quote_regular = array();
		preg_match_all('{".*?"}', $article, $matches_curly_txt_regular);
        $curly_quote_regular = $matches_curly_txt_regular[0];
		$curly_quote = array_merge($curly_quote , $single_curly_quote ,$single_curly_quote_and,$single_curly_quote_and_num,$curly_quote_regular);
	}
	$htmlfounds = array_merge($nospin, $shortcodes, $js, $htmlurls, $htmlfounds, $curly_quote, $urls_txt, $nospin_nums, $capped);
	$htmlfounds = array_filter(array_unique($htmlfounds));
	$i=1;
	foreach($htmlfounds as $htmlfound){
        $article=str_replace($htmlfound,'('.str_repeat('*', $i).')',$article);	
		$i++;
	}
    $article = str_replace(':(*', ': (*', $article);
	return $article;
}
function crawlomatic_restoreExecludes($article, $htmlfounds){
	$i=1;
    foreach($htmlfounds as $htmlfound){
        $article=str_replace( '('.str_repeat('*', $i).')', $htmlfound, $article);
		$i++;
	}
	$article = str_replace(array('[nospin]','[/nospin]'), '', $article);
    $article = preg_replace('{\(?\*[\s*]+\)?}', '', $article);
    $article = str_replace('()', '', $article);
    return $article;
}
function crawlomatic_fix_spinned_content($final_content, $spinner)
{
    if ($spinner == 'wordai') {
        $final_content = str_replace('-LRB-', '(', $final_content);
        $final_content = preg_replace("/{\*\|.*?}/", '*', $final_content);
        preg_match_all('/{\)[^}]*\|\)[^}]*}/', $final_content, $matches_brackets);
        $matches_brackets = $matches_brackets[0];
        foreach ($matches_brackets as $matches_bracket) {
            $matches_bracket_clean = str_replace( array('{','}') , '', $matches_bracket);
            $matches_bracket_parts = explode('|',$matches_bracket_clean);
            $final_content = str_replace($matches_bracket, $matches_bracket_parts[0], $final_content);
        }
    }
    elseif ($spinner == 'spinrewriter' || $spinner == 'translate') {
        $final_content = preg_replace('{\(\s(\**?\))\.}', '($1', $final_content);
        $final_content = preg_replace('{\(\s(\**?\))\s\(}', '($1(', $final_content);
        $final_content = preg_replace('{\s(\(\**?\))\.(\s)}', "$1$2", $final_content);
        $final_content = str_replace('( *', '(*', $final_content);
        $final_content = str_replace('* )', '*)', $final_content);
        $final_content = str_replace('& #', '&#', $final_content);
        $final_content = str_replace('& ldquo;', '"', $final_content);
        $final_content = str_replace('& rdquo;', '"', $final_content);
    }
    return $final_content;
}
function crawlomatic_spin_and_translate($post_title, $final_content, $translate, $source_lang, $use_proxy = '1', $no_spin = '0')
{
    $turk = false;
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (isset($crawlomatic_Main_Settings['spin_text']) && $crawlomatic_Main_Settings['spin_text'] !== 'disabled' && $no_spin != '1') {
        if ($crawlomatic_Main_Settings['spin_text'] == 'turkcespin') {
            $turk = true;
        }
        $htmlfounds = array();
        if($turk == false)
        {
            $final_content = crawlomatic_replaceExecludes($final_content, $htmlfounds, false);
        }
        if ($crawlomatic_Main_Settings['spin_text'] == 'builtin') {
            $translation = crawlomatic_builtin_spin_text($post_title, $final_content);
        } elseif ($crawlomatic_Main_Settings['spin_text'] == 'wikisynonyms') {
            $translation = crawlomatic_spin_text($post_title, $final_content, false);
        } elseif ($crawlomatic_Main_Settings['spin_text'] == 'freethesaurus') {
            $translation = crawlomatic_spin_text($post_title, $final_content, true);
        } elseif ($crawlomatic_Main_Settings['spin_text'] == 'best') {
            $translation = crawlomatic_best_spin_text($post_title, $final_content);
        } elseif ($crawlomatic_Main_Settings['spin_text'] == 'wordai') {
            $translation = crawlomatic_wordai_spin_text($post_title, $final_content);
        } elseif ($crawlomatic_Main_Settings['spin_text'] == 'spinrewriter') {
            $translation = crawlomatic_spinrewriter_spin_text($post_title, $final_content);
        } elseif ($crawlomatic_Main_Settings['spin_text'] == 'turkcespin') {
            $translation = crawlomatic_turkcespin_spin_text($post_title, $final_content);
        } elseif ($crawlomatic_Main_Settings['spin_text'] == 'spinnerchief') {
            $translation = crawlomatic_spinnerchief_spin_text($post_title, $final_content);
        }
        if ($translation !== FALSE) {
            if (is_array($translation) && isset($translation[0]) && isset($translation[1])) {
                if (isset($crawlomatic_Main_Settings['no_title_spin']) && $crawlomatic_Main_Settings['no_title_spin'] == 'on') {
                }
                else
                {
                    $post_title    = $translation[0];
                }
                $final_content = $translation[1];
                if($turk == false)
                {
                    $final_content = crawlomatic_fix_spinned_content($final_content, $crawlomatic_Main_Settings['spin_text']);
                    $final_content = crawlomatic_restoreExecludes($final_content, $htmlfounds);
                }
                
            } else {
                if($turk == false)
                {
                    $final_content = crawlomatic_restoreExecludes($final_content, $htmlfounds);
                }
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                    crawlomatic_log_to_file('Text Spinning failed - malformed data ' . $crawlomatic_Main_Settings['spin_text']);
                }
            }
        } else {
            if($turk == false)
            {
                $final_content = crawlomatic_restoreExecludes($final_content, $htmlfounds);
            }
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                crawlomatic_log_to_file('Text Spinning Failed - returned false ' . $crawlomatic_Main_Settings['spin_text']);
            }
        }
    }
    if ($translate != 'disabled') {
        if (isset($source_lang) && $source_lang != 'disabled' && $source_lang != '') {
            $tr = $source_lang;
        }
        else
        {
            $tr = 'auto';
        }
        
        $htmlfounds = array();
        $final_content = crawlomatic_replaceExecludes($final_content, $htmlfounds, false, true);
        
        $translation = crawlomatic_translate($post_title, $final_content, $tr, $translate, $use_proxy);
        if (is_array($translation) && isset($translation[1]))
        {
            $translation[1] = preg_replace('#(?<=[\*(])\s+(?=[\*)])#', '', $translation[1]);
            $translation[1] = preg_replace('#([^(*\s]\s)\*+\)#', '$1', $translation[1]);
            $translation[1] = preg_replace('#\(\*+([\s][^)*\s])#', '$1', $translation[1]);
            if(crawlomatic_endsWith(trim($final_content), '(*)') && !crawlomatic_endsWith(trim($translation[1]), '(*)'))
            {
                $translation[1] .= '(*)';
            }
            $translation[1] = crawlomatic_restoreExecludes($translation[1], $htmlfounds);
        }
        else
        {
            $final_content = crawlomatic_restoreExecludes($final_content, $htmlfounds);
        }
        if ($translation !== FALSE) {
            if (is_array($translation) && isset($translation[0]) && isset($translation[1])) {
                if (isset($crawlomatic_Main_Settings['no_title_spin']) && $crawlomatic_Main_Settings['no_title_spin'] == 'on') {
                }
                else
                { 
                    $post_title    = $translation[0];
                }
                $final_content = $translation[1];
                $final_content = str_replace('</ iframe>', '</iframe>', $final_content);
                if(stristr($final_content, '<head>') !== false)
                {
                    $d = new DOMDocument;
                    $mock = new DOMDocument;
                    $internalErrors = libxml_use_internal_errors(true);
                    $d->loadHTML('<?xml encoding="utf-8" ?>' . $final_content);
                    libxml_use_internal_errors($internalErrors);
                    $body = $d->getElementsByTagName('body')->item(0);
                    foreach ($body->childNodes as $child)
                    {
                        $mock->appendChild($mock->importNode($child, true));
                    }
                    $new_post_content_temp = $mock->saveHTML();
                    if($new_post_content_temp !== '' && $new_post_content_temp !== false)
                    {
						$new_post_content_temp = str_replace('<?xml encoding="utf-8" ?>', '', $new_post_content_temp);
                        $final_content = preg_replace("/_addload\(function\(\){([^<]*)/i", "", $new_post_content_temp); 
                    }
                }
                $final_content = htmlspecialchars_decode($final_content);
                $final_content = str_replace('</ ', '</', $final_content);
                $final_content = str_replace(' />', '/>', $final_content);
                $final_content = str_replace('< br/>', '<br/>', $final_content);
                $final_content = str_replace('< / ', '</', $final_content);
                $final_content = str_replace(' / >', '/>', $final_content);
                $final_content = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $final_content);
                $final_content = html_entity_decode($final_content);
                $final_content = preg_replace_callback("#src(?:\s)?=(?:\s)?[\'\"]([^\"\']+?)[\'\"]#", "crawlomatic_removeSpaces", $final_content);
                if (isset($crawlomatic_Main_Settings['no_title_spin']) && $crawlomatic_Main_Settings['no_title_spin'] == 'on') {
                }
                else
                { 
                    $post_title = preg_replace('{&\s*#\s*(\d+)\s*;}', '&#$1;', $post_title);
                    $post_title = htmlspecialchars_decode($post_title);
                    $post_title = str_replace('</ ', '</', $post_title);
                    $post_title = str_replace(' />', '/>', $post_title);
                    $post_title = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $post_title);
                }
            } else {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                    crawlomatic_log_to_file('Translation failed - malformed data!');
                }
            }
        } else {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                crawlomatic_log_to_file('Translation Failed - returned false!');
            }
        }
    }
    return array(
        $post_title,
        $final_content
    );
}

function crawlomatic_spin_and_translate_shortcode($final_content, $spin, $translate, $source_lang, $use_proxy = '1')
{
    if($spin != '')
    {
        $user_name = '';
        $pass = '';
        $turk = false;
        $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
        $spin_prts = explode(':', $spin);
        if(isset($spin_prts[0]) && (trim($spin_prts[0]) == 'bestspinner' || trim($spin_prts[0]) == 'wordai' || trim($spin_prts[0]) == 'spinrewriter' || trim($spin_prts[0]) == 'turkcespin' || trim($spin_prts[0]) == 'builtin' || trim($spin_prts[0]) == 'wikisynonyms' || trim($spin_prts[0]) == 'freethesaurus'))
        {
            $crawlomatic_Main_Settings['spin_text'] = trim($spin_prts[0]);
            if(isset($spin_prts[1]))
            {
                $user_name = trim($spin_prts[1]);
                if(isset($spin_prts[2]))
                {
                    $pass = trim($spin_prts[2]);
                }
            }
        }
        if (isset($crawlomatic_Main_Settings['spin_text']) && $crawlomatic_Main_Settings['spin_text'] !== 'disabled') {
            if ($crawlomatic_Main_Settings['spin_text'] == 'turkcespin') {
                $turk = true;
            }
            $htmlfounds = array();
            if($turk == false)
            {
                $final_content = crawlomatic_replaceExecludes($final_content, $htmlfounds, false);
            }
            if ($crawlomatic_Main_Settings['spin_text'] == 'builtin') {
                $translation = crawlomatic_builtin_spin_text('hello', $final_content);
            } elseif ($crawlomatic_Main_Settings['spin_text'] == 'wikisynonyms') {
                $translation = crawlomatic_spin_text('hello', $final_content, false);
            } elseif ($crawlomatic_Main_Settings['spin_text'] == 'freethesaurus') {
                $translation = crawlomatic_spin_text('hello', $final_content, true);
            } elseif ($crawlomatic_Main_Settings['spin_text'] == 'best') {
                $translation = crawlomatic_best_spin_text('hello', $final_content, $user_name, $pass);
            } elseif ($crawlomatic_Main_Settings['spin_text'] == 'wordai') {
                $translation = crawlomatic_wordai_spin_text('hello', $final_content, $user_name, $pass);
            } elseif ($crawlomatic_Main_Settings['spin_text'] == 'spinrewriter') {
                $translation = crawlomatic_spinrewriter_spin_text('hello', $final_content, $user_name, $pass);
            } elseif ($crawlomatic_Main_Settings['spin_text'] == 'turkcespin') {
                $translation = crawlomatic_turkcespin_spin_text('hello', $final_content, $user_name);
            }
            if ($translation !== FALSE) {
                if (is_array($translation) && isset($translation[0]) && isset($translation[1])) {
                    $final_content = $translation[1];
                    if($turk == false)
                    {
                        $final_content = crawlomatic_fix_spinned_content($final_content, $crawlomatic_Main_Settings['spin_text']);
                        $final_content = crawlomatic_restoreExecludes($final_content, $htmlfounds);
                    }
                    
                } else {
                    if($turk == false)
                    {
                        $final_content = crawlomatic_restoreExecludes($final_content, $htmlfounds);
                    }
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                        crawlomatic_log_to_file('Shortcode Text Spinning failed - malformed data ' . $crawlomatic_Main_Settings['spin_text']);
                    }
                }
            } else {
                if($turk == false)
                {
                    $final_content = crawlomatic_restoreExecludes($final_content, $htmlfounds);
                }
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                    crawlomatic_log_to_file('Shortcode Text Spinning Failed - returned false ' . $crawlomatic_Main_Settings['spin_text']);
                }
            }
        }
    }
    if ($translate != 'disabled' && $translate != '') {
        if (isset($source_lang) && $source_lang != 'disabled' && $source_lang != '') {
            $tr = $source_lang;
        }
        else
        {
            $tr = 'auto';
        }
        
        $htmlfounds = array();
        $final_content = crawlomatic_replaceExecludes($final_content, $htmlfounds, false, true);
        
        $translation = crawlomatic_translate('hello', $final_content, $tr, $translate, $use_proxy);
        if (is_array($translation) && isset($translation[1]))
        {
            $translation[1] = preg_replace('#(?<=[\*(])\s+(?=[\*)])#', '', $translation[1]);
            $translation[1] = preg_replace('#([^(*\s]\s)\*+\)#', '$1', $translation[1]);
            $translation[1] = preg_replace('#\(\*+([\s][^)*\s])#', '$1', $translation[1]);
            $translation[1] = crawlomatic_restoreExecludes($translation[1], $htmlfounds);
        }
        else
        {
            $final_content = crawlomatic_restoreExecludes($final_content, $htmlfounds);
        }
        if ($translation !== FALSE) {
            if (is_array($translation) && isset($translation[0]) && isset($translation[1])) {
                $final_content = $translation[1];
                $final_content = str_replace('</ iframe>', '</iframe>', $final_content);
                if(stristr($final_content, '<head>') !== false)
                {
                    $d = new DOMDocument;
                    $mock = new DOMDocument;
                    $internalErrors = libxml_use_internal_errors(true);
                    $d->loadHTML('<?xml encoding="utf-8" ?>' . $final_content);
                    libxml_use_internal_errors($internalErrors);
                    $body = $d->getElementsByTagName('body')->item(0);
                    foreach ($body->childNodes as $child)
                    {
                        $mock->appendChild($mock->importNode($child, true));
                    }
                    $new_post_content_temp = $mock->saveHTML();
                    if($new_post_content_temp !== '' && $new_post_content_temp !== false)
                    {
						$new_post_content_temp = str_replace('<?xml encoding="utf-8" ?>', '', $new_post_content_temp);
                        $final_content = preg_replace("/_addload\(function\(\){([^<]*)/i", "", $new_post_content_temp); 
                    }
                }
                $final_content = htmlspecialchars_decode($final_content);
                $final_content = str_replace('</ ', '</', $final_content);
                $final_content = str_replace(' />', '/>', $final_content);
                $final_content = str_replace('< br/>', '<br/>', $final_content);
                $final_content = str_replace('< / ', '</', $final_content);
                $final_content = str_replace(' / >', '/>', $final_content);
                $final_content = preg_replace('/[\x00-\x1F\x7F\xA0]/u', '', $final_content);
                $final_content = html_entity_decode($final_content);
                $final_content = preg_replace_callback("#src(?:\s)?=(?:\s)?[\'\"]([^\"\']+?)[\'\"]#", "crawlomatic_removeSpaces", $final_content);
            } else {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                    crawlomatic_log_to_file('Shortcode Translation failed - malformed data!');
                }
            }
        } else {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                crawlomatic_log_to_file('Shortcode Translation Failed - returned false!');
            }
        }
    }
    return $final_content;
}

function crawlomatic_translate($title, $content, $from, $to, $use_proxy = '1')
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    $ch = FALSE;
    try {
        if($from == 'disabled')
        {
            if(strstr($to, '-') !== false && $to != 'zh-CN' && $to != 'zh-TW')
            {
                $from = 'auto-';
            }
            else
            {
                $from = 'auto';
            }
        }
        if($from != 'en' && $from != 'en-' && $from == $to)
        {
            if(strstr($to, '-') !== false && $to != 'zh-CN' && $to != 'zh-TW')
            {
                $from = 'en-';
            }
            else
            {
                $from = 'en';
            }
        }
        elseif(($from == 'en' || $from == 'en-') && $from == $to)
        {
            return false;
        }
        if(strstr($to, '!') !== false)
        {
            if (!isset($crawlomatic_Main_Settings['bing_auth']) || trim($crawlomatic_Main_Settings['bing_auth']) == '')
            {
                throw new Exception('You must enter a Microsoft Translator API key from plugin settings, to use this feature!');
            }
            require_once (dirname(__FILE__) . "/res/crawlomatic-translator-microsoft.php");
            $options    = array(
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_CONNECTTIMEOUT => 10,
                CURLOPT_TIMEOUT => 60,
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_SSL_VERIFYHOST => 0,
                CURLOPT_SSL_VERIFYPEER => 0
            );
            $ch = curl_init();
            if ($ch === FALSE) {
                crawlomatic_log_to_file ('Failed to init curl in Microsoft Translator');
				return false;
            }
            if ($use_proxy && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') {
				$prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
                $randomness = array_rand($prx);
                $options[CURLOPT_PROXY] = trim($prx[$randomness]);
                if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
                {
                    $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
                    if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
                    {
                        $options[CURLOPT_PROXYUSERPWD] = trim($prx_auth[$randomness]);
                    }
                }
            }
            curl_setopt_array($ch, $options);
			$MicrosoftTranslator = new MicrosoftTranslator ( $ch );	
			try 
            {
                if (!isset($crawlomatic_Main_Settings['bing_region']) || trim($crawlomatic_Main_Settings['bing_region']) == '')
                {
                    $mt_region = 'global';
                }
                else
                {
                    $mt_region = trim($crawlomatic_Main_Settings['bing_region']);
                }
                if($from == 'auto' || $from == 'auto-' || $from == 'disabled')
                {
                    $from = 'no';
                }
				$accessToken = $MicrosoftTranslator->getToken ( trim($crawlomatic_Main_Settings['bing_auth']) , $mt_region  );
                $from = trim($from, '!');
                $to = trim($to, '!');
				$translated = $MicrosoftTranslator->translateWrap ( $content, $from, $to );
                $translated_title = $MicrosoftTranslator->translateWrap ( $title, $from, $to );
                curl_close($ch);
			} 
            catch ( Exception $e ) 
            {
                curl_close($ch);
				crawlomatic_log_to_file ('Microsoft Translation error: ' . $e->getMessage());
				return false;
			}
        }
        if(strstr($to, '-') !== false && $to != 'zh-CN' && $to != 'zh-TW')
        {
            if (!isset($crawlomatic_Main_Settings['deepl_auth']) || trim($crawlomatic_Main_Settings['deepl_auth']) == '')
            {
                throw new Exception('You must enter a DeepL API key from plugin settings, to use this feature!');
            }
            $to = rtrim($to, '-');
            $from = rtrim($from, '-');
            if(strlen($content) > 30000)
            {
                $translated = '';
                while($content != '')
                {
                    $first30k = substr($content, 0, 30000);
                    $content = substr($content, 30000);
                    if (isset($crawlomatic_Main_Settings['deppl_free']) && trim($crawlomatic_Main_Settings['deppl_free']) == 'on')
                    {
                        $deepapi = 'https://api-free.deepl.com/v2/translate';
                    }
                    else
                    {
                        $deepapi = 'https://api.deepl.com/v2/translate';
                    }
                    $ch = curl_init($deepapi);
                    if($ch !== false)
                    {
                        $data           = array();
                        $data['text']   = $first30k;
                        if($from != 'auto')
                        {
                            $data['source_lang']   = $from;
                        }
                        $data['tag_handling']  = 'xml';
                        $data['non_splitting_tags']  = 'div';
                        $data['preserve_formatting']  = '1';
                        $data['target_lang']   = $to;
                        $data['auth_key']   = trim($crawlomatic_Main_Settings['deepl_auth']);
                        $fdata = "";
                        foreach ($data as $key => $val) {
                            $fdata .= "$key=" . urlencode(trim($val)) . "&";
                        }
                        $headers = [
                            'Content-Type: application/x-www-form-urlencoded',
                            'Content-Length: ' . strlen($fdata)
                        ];
                        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                        curl_setopt($ch, CURLOPT_POST, 1);
                        curl_setopt($ch, CURLOPT_USERAGENT, crawlomatic_get_random_user_agent());
                        curl_setopt($ch, CURLOPT_POSTFIELDS, $fdata);
                        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                        curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                        $translated_temp = curl_exec($ch);
                        if($translated_temp === false)
                        {
                            throw new Exception('Failed to post to DeepL: ' . curl_error($ch));
                        }
                        curl_close($ch);
                    }
                    $trans_json = json_decode($translated_temp, true);
                    if($trans_json === false)
                    {
                        throw new Exception('Incorrect multipart response from DeepL: ' . $translated_temp);
                    }
                    if(!isset($trans_json['translations'][0]['text']))
                    {
                        throw new Exception('Unrecognized multipart response from DeepL: ' . $translated_temp);
                    }
                    $translated .= ' ' . $trans_json['translations'][0]['text'];
                }
            }
            else
            {
                if (isset($crawlomatic_Main_Settings['deppl_free']) && trim($crawlomatic_Main_Settings['deppl_free']) == 'on')
                {
                    $deepapi = 'https://api-free.deepl.com/v2/translate';
                }
                else
                {
                    $deepapi = 'https://api.deepl.com/v2/translate';
                }
                $ch = curl_init($deepapi);
                if($ch !== false)
                {
                    $data           = array();
                    $data['text']   = $content;
                    if($from != 'auto')
                    {
                        $data['source_lang']   = $from;
                    }
                    $data['tag_handling']  = 'xml';
                    $data['non_splitting_tags']  = 'div';
                    $data['preserve_formatting']  = '1';
                    $data['target_lang']   = $to;
                    $data['auth_key']   = trim($crawlomatic_Main_Settings['deepl_auth']);
                    $fdata = "";
                    foreach ($data as $key => $val) {
                        $fdata .= "$key=" . urlencode(trim($val)) . "&";
                    }
                    curl_setopt($ch, CURLOPT_POST, 1);
                    $headers = [
                        'Content-Type: application/x-www-form-urlencoded',
                        'Content-Length: ' . strlen($fdata)
                    ];
                    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $fdata);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                    curl_setopt($ch, CURLOPT_TIMEOUT, 60);
                    $translated = curl_exec($ch);
                    if($translated === false)
                    {
                        throw new Exception('Failed to post to DeepL: ' . curl_error($ch));
                    }
                    curl_close($ch);
                }
                $trans_json = json_decode($translated, true);
                if($trans_json === false)
                {
                    throw new Exception('Incorrect text response from DeepL: ' . $translated);
                }
                if(!isset($trans_json['translations'][0]['text']))
                {
                    throw new Exception('Unrecognized text response from DeepL: ' . $translated);
                }
                $translated = $trans_json['translations'][0]['text'];
            }
            $translated = str_replace('<strong>', ' <strong>', $translated);
            $translated = str_replace('</strong>', '</strong> ', $translated);
            if($from != 'auto')
            {
                $from_from = '&source_lang=' . $from;
            }
            else
            {
                $from_from = '';
            }
            if (isset($crawlomatic_Main_Settings['deppl_free']) && trim($crawlomatic_Main_Settings['deppl_free']) == 'on')
            {
                $deepapi = 'https://api-free.deepl.com/v2/translate?text=';
            }
            else
            {
                $deepapi = 'https://api.deepl.com/v2/translate?text=';
            }
            $translated_title = crawlomatic_get_web_page($deepapi . urlencode($title) . $from_from . '&target_lang=' . $to . '&auth_key=' . trim($crawlomatic_Main_Settings['deepl_auth']) . '&tag_handling=xml&preserve_formatting=1', '', '', '0', '', '', '', '');
            $trans_json = json_decode($translated_title, true);
            if($trans_json === false)
            {
                throw new Exception('Incorrect title response from DeepL: ' . $translated_title);
            }
            if(!isset($trans_json['translations'][0]['text']))
            {
                throw new Exception('Unrecognized title response from DeepL: ' . $translated_title);
            }
            $translated_title = $trans_json['translations'][0]['text'];
        }
        else
        {
            if (isset($crawlomatic_Main_Settings['google_trans_auth']) && trim($crawlomatic_Main_Settings['google_trans_auth']) != '')
            {
                require_once(dirname(__FILE__) . "/res/translator-api.php");
                $ch = curl_init();
                if ($ch === FALSE) {
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                        crawlomatic_log_to_file('Failed to init cURL in translator!');
                    }
                    return false;
                }
                if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') 
                {
                    $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
                    $randomness = array_rand($prx);
                    curl_setopt( $ch, CURLOPT_PROXY, trim($prx[$randomness]));
                    if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
                    {
                        $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
                        if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
                        {
                            curl_setopt( $ch, CURLOPT_PROXYUSERPWD, trim($prx_auth[$randomness]) );
                        }
                    }
                }
                if (isset($crawlomatic_Main_Settings['request_timeout']) && $crawlomatic_Main_Settings['request_timeout'] != '') {
                    $timeout = intval($crawlomatic_Main_Settings['request_timeout']);
                } else {
                    $timeout = 10;
                }
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                $GoogleTranslatorAPI = new GoogleTranslatorAPI($ch, $crawlomatic_Main_Settings['google_trans_auth']);
                $translated = '';
                $translated_title = '';
                if($content != '')
                {
                    if(strlen($content) > 30000)
                    {
                        while($content != '')
                        {
                            $first30k = substr($content, 0, 30000);
                            $content = substr($content, 30000);
                            $translated_temp       = $GoogleTranslatorAPI->translateText($first30k, $from, $to);
                            $translated .= ' ' . $translated_temp;
                        }
                    }
                    else
                    {
                        $translated       = $GoogleTranslatorAPI->translateText($content, $from, $to);
                    }
                }
                if($title != '')
                {
                    $translated_title = $GoogleTranslatorAPI->translateText($title, $from, $to);
                }
                curl_close($ch);
            }
            else
            {
                require_once(dirname(__FILE__) . "/res/crawlomatic-translator.php");
                $ch = curl_init();
                if ($ch === FALSE) {
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                        crawlomatic_log_to_file('Failed to init cURL in translator!');
                    }
                    return false;
                }
                if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') 
                {
                    $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
                    $randomness = array_rand($prx);
                    curl_setopt( $ch, CURLOPT_PROXY, trim($prx[$randomness]));
                    if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
                    {
                        $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
                        if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
                        {
                            curl_setopt( $ch, CURLOPT_PROXYUSERPWD, trim($prx_auth[$randomness]) );
                        }
                    }
                }
                if (isset($crawlomatic_Main_Settings['request_timeout']) && $crawlomatic_Main_Settings['request_timeout'] != '') {
                    $timeout = intval($crawlomatic_Main_Settings['request_timeout']);
                } else {
                    $timeout = 10;
                }
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
                curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
                curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
                $GoogleTranslator = new GoogleTranslator($ch);
                $translated = '';
                $translated_title = '';
                if($content != '')
                {
                    if(strlen($content) > 13000)
                    {
                        while($content != '')
                        {
                            $first30k = substr($content, 0, 13000);
                            $content = substr($content, 13000);
                            $translated_temp       = $GoogleTranslator->translateText($first30k, $from, $to);
                            if (strpos($translated, '<h2>The page you have attempted to translate is already in ') !== false) {
                                throw new Exception('Page content already in ' . $to);
                            }
                            if (strpos($translated, 'Error 400 (Bad Request)!!1') !== false) {
                                throw new Exception('Unexpected error while translating page!');
                            }
                            if(substr_compare($translated_temp, '</pre>', -strlen('</pre>')) === 0){$translated_temp = substr_replace($translated_temp ,"", -6);}if(substr( $translated_temp, 0, 5 ) === "<pre>"){$translated_temp = substr($translated_temp, 5);}
                            $translated .= ' ' . $translated_temp;
                        }
                    }
                    else
                    {
                        $translated       = $GoogleTranslator->translateText($content, $from, $to);
                        if (strpos($translated, '<h2>The page you have attempted to translate is already in ') !== false) {
                            throw new Exception('Page content already in ' . $to);
                        }
                        if (strpos($translated, 'Error 400 (Bad Request)!!1') !== false) {
                            throw new Exception('Unexpected error while translating page!');
                        }
                    }
                }
                if($title != '')
                {
                    $translated_title = $GoogleTranslator->translateText($title, $from, $to);
                }
                if (strpos($translated_title, '<h2>The page you have attempted to translate is already in ') !== false) {
                    throw new Exception('Page title already in ' . $to);
                }
                if (strpos($translated_title, 'Error 400 (Bad Request)!!1') !== false) {
                    throw new Exception('Unexpected error while translating page title!');
                }
                curl_close($ch);
            }
        }
    }
    catch (Exception $e) {
        if($ch !== false)
        {
            curl_close($ch);
        }
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
            crawlomatic_log_to_file('Exception thrown in Translator ' . $e);
        }
        return false;
    }
    if(substr_compare($translated_title, '</pre>', -strlen('</pre>')) === 0){$title = substr_replace($translated_title ,"", -6);}else{$title = $translated_title;}if(substr( $title, 0, 5 ) === "<pre>"){$title = substr($title, 5);}
    if(substr_compare($translated, '</pre>', -strlen('</pre>')) === 0){$text = substr_replace($translated ,"", -6);}else{$text = $translated;}if(substr( $text, 0, 5 ) === "<pre>"){$text = substr($text, 5);}
    $text  = preg_replace('/' . preg_quote('html lang=') . '.*?' . preg_quote('>') . '/', '', $text);
    $text  = preg_replace('/' . preg_quote('!DOCTYPE') . '.*?' . preg_quote('<') . '/', '', $text);
    return array(
        $title,
        $text
    );
}

function crawlomatic_removeSpaces($matches) {
  return "src='" . str_replace(" ", "", $matches[1]) . "'";
}
function crawlomatic_strip_html_tags_nl($str)
{
    $str = html_entity_decode($str);
    $str = preg_replace('/(<|>)\1{2}/is', '', $str);
    $str = preg_replace(array(
        '@<head[^>]*?>.*?</head>@siu',
        '@<style[^>]*?>.*?</style>@siu',
        '@<script[^>]*?.*?</script>@siu',
        '@<noscript[^>]*?.*?</noscript>@siu'
    ), "", $str);
    $str = strip_tags($str, '<p><br>');
	$str = preg_replace('#<br\s*\/?>#i', PHP_EOL, $str);
	$str = preg_replace('#<\/p>#i', PHP_EOL . PHP_EOL, $str);
	$str = preg_replace('#<p([^>]*?)>#i', '', $str);
    return $str;
}
function crawlomatic_fix_single_link($rel, $base)
{
    try 
    {
        $rel = trim($rel);
        require_once (dirname(__FILE__) . '/res/Net_URL2.php');
        $relUrl = new Net_URL2($rel);
        if ($relUrl->isAbsolute()) {
            return $rel;
        }
        $baseUrl = new Net_URL2($base);
        $absUrl = $baseUrl->resolve($relUrl);
        $full_url = $absUrl->getURL();
        if($full_url != $rel)
        {
            return $full_url;
        }
    } 
    catch (Exception $e) 
    {
        crawlomatic_log_to_file('Unable to resolve relative single link "' . $rel . '" against base "' . $base . '": ' . $e->getMessage());
    }
    return $rel;
}

function crawlomatic_fix_links($str, $url)
{
    require_once (dirname(__FILE__) . '/res/Net_URL2.php');
    require_once (dirname(__FILE__) . "/res/simple_html_dom.php");
    $replaced_links = array();
    $html_dom_original_html = crawlomatic_str_get_html($str);
    if($html_dom_original_html !== false && method_exists($html_dom_original_html, 'find'))
    {
        foreach($html_dom_original_html->find('a') as $a) 
        {
            if($a->href) 
            {
                if(!in_array($a->href, $replaced_links))
                {
                    $replaced_links[] = $a->href;
                    try {
                        $relUrl = new Net_URL2($a->href);
                        if ($relUrl->isAbsolute()) {
                            continue;
                        }
                        $baseUrl = new Net_URL2($url);
                        $absUrl = $baseUrl->resolve($relUrl);
                        $full_url = $absUrl->getURL();
                        if($full_url != $a->href)
                        {
                            $str = str_replace($a->href, $full_url, $str);
                        }
                    } catch (Exception $e) {
                        crawlomatic_log_to_file('Unable to resolve relative link "' . $a->href . '" against base "' . $url . '": ' . $e->getMessage());
                        continue;
                    }
                }
            }
        }
        foreach($html_dom_original_html->find('img') as $img) 
        {
            if($img->src) 
            {
                if(!in_array($img->src, $replaced_links))
                {
                    $replaced_links[] = $img->src;
                    try {
                        $relUrl = new Net_URL2($img->src);
                        if ($relUrl->isAbsolute()) {
                            continue;
                        }
                        $baseUrl = new Net_URL2($url);
                        $absUrl = $baseUrl->resolve($relUrl);
                        $full_url = $absUrl->getURL();
                        if($full_url != $img->src)
                        {
                            $str = str_replace($img->src, $full_url, $str);
                        }
                    } catch (Exception $e) {
                        crawlomatic_log_to_file('Unable to resolve relative image link "' . $img->src . '" against base "' . $url . '": ' . $e->getMessage());
                        continue;
                    }
                }
            }
        }
    }
    else
    {
        $htmlDom = new DOMDocument;
        $internalErrors = libxml_use_internal_errors(true);
        $htmlDom->loadHTML('<?xml encoding="utf-8" ?>' . $str);
        libxml_use_internal_errors($internalErrors);
        $links = $htmlDom->getElementsByTagName('a');
        foreach($links as $link)
        {
            $linkHref = $link->getAttribute('href');
            if(strlen(trim($linkHref)) == 0){
                continue;
            }
            if($linkHref[0] == '#'){
                continue;
            }
            if(!in_array($linkHref, $replaced_links))
            {
                $replaced_links[] = $linkHref;
                try {
                    $relUrl = new Net_URL2($linkHref);
                    if ($relUrl->isAbsolute()) {
                        continue;
                    }
                    $baseUrl = new Net_URL2($url);
                    $absUrl = $baseUrl->resolve($relUrl);
                    $full_url = $absUrl->getURL();
                    if($full_url != $linkHref)
                    {
                        $str = str_replace($linkHref, $full_url, $str);
                    }
                } catch (Exception $e) {
                    crawlomatic_log_to_file('Unable to resolve (2) relative link "' . $linkHref . '" against base "' . $url . '": ' . $e->getMessage());
                    continue;
                }
            }
        }
        $links = $htmlDom->getElementsByTagName('img');
        foreach($links as $link)
        {
            $linkHref = $link->getAttribute('src');
            if(strlen(trim($linkHref)) == 0){
                continue;
            }
            if(!in_array($linkHref, $replaced_links))
            {
                $replaced_links[] = $linkHref;
                try {
                    $relUrl = new Net_URL2($linkHref);
                    if ($relUrl->isAbsolute()) {
                        continue;
                    }
                    $baseUrl = new Net_URL2($url);
                    $absUrl = $baseUrl->resolve($relUrl);
                    $full_url = $absUrl->getURL();
                    if($full_url != $linkHref)
                    {
                        $str = str_replace($linkHref, $full_url, $str);
                    }
                } catch (Exception $e) {
                    crawlomatic_log_to_file('Unable to resolve (2) relative image link "' . $linkHref . '" against base "' . $url . '": ' . $e->getMessage());
                    continue;
                }
            }
        }
    }
    return $str;
}
function crawlomatic_strip_html_tags($str, $allow_tags = '')
{
    $str = html_entity_decode($str);
    $str = preg_replace('/(<|>)\1{2}/is', '', $str);
    if($allow_tags != '')
    {
        $rparr = array();
        if(stristr($allow_tags, '<head>') == false)
        {
            $rparr[] = '@<head[^>]*?>.*?</head>@siu';
        }
        if(stristr($allow_tags, '<style>') == false)
        {
            $rparr[] = '@<style[^>]*?>.*?</style>@siu';
        }
        if(stristr($allow_tags, '<script>') == false)
        {
            $rparr[] = '@<script[^>]*?.*?</script>@siu';
        }
        if(stristr($allow_tags, '<noscript>') == false)
        {
            $rparr[] = '@<noscript[^>]*?.*?</noscript>@siu';
        }
        if(count($rparr) > 0)
        {
            $str = preg_replace($rparr, "", $str);
        }
    }
    else
    {
        $str = preg_replace(array(
            '@<head[^>]*?>.*?</head>@siu',
            '@<style[^>]*?>.*?</style>@siu',
            '@<script[^>]*?.*?</script>@siu',
            '@<noscript[^>]*?.*?</noscript>@siu'
        ), "", $str);
    }
    if($allow_tags != '')
    {
        $str = strip_tags($str, $allow_tags);
    }
    else
    {
        $str = strip_tags($str);
    }
    
    return $str;
}

function crawlomatic_DOMinnerHTML(DOMNode $element)
{
    $innerHTML = "";
    $children  = $element->childNodes;
    
    foreach ($children as $child) {
        $innerHTML .= $element->ownerDocument->saveHTML($child);
    }
    
    return $innerHTML;
}
function crawlomatic_encodeURI($url) {
    $unescaped = array(
        '%2D'=>'-','%5F'=>'_','%2E'=>'.','%21'=>'!', '%7E'=>'~',
        '%2A'=>'*', '%27'=>"'", '%28'=>'(', '%29'=>')', '%5B'=>'[', '%5D'=>']'
    );
    $reserved = array(
        '%3B'=>';','%2C'=>',','%2F'=>'/','%3F'=>'?','%3A'=>':',
        '%40'=>'@','%26'=>'&','%3D'=>'=','%2B'=>'+','%24'=>'$','%25'=>'%'
    );
    $score = array(
        '%23'=>'#'
    );
    return strtr(rawurlencode($url), array_merge($reserved, $unescaped, $score));
}
function crawlomatic_url_exists(&$url, $use_proxy, $crawlomatic_Main_Settings, $custom_user_agent, $custom_cookies, $user_pass)
{
    if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
        $url = crawlomatic_encodeURI($url);
        if (filter_var($url, FILTER_VALIDATE_URL) === FALSE) {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                crawlomatic_log_to_file('URL is not valid: ' . $url); 
            }
            return false;
        }
    }
    $ch = curl_init($url);
    if($ch === false)
    {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
        {
            crawlomatic_log_to_file('Failed to init curl while validating URL: ' . $url); 
        }
        return false;
    }
    if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled')
    {
        $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
        $randomness = array_rand($prx);
        curl_setopt( $ch, CURLOPT_PROXY, trim($prx[$randomness]));
        if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
        {
            $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
            if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
            {
                curl_setopt( $ch, CURLOPT_PROXYUSERPWD, trim($prx_auth[$randomness]) );
            }
        }
    }
    if($custom_user_agent != '')
    {
        curl_setopt($ch, CURLOPT_USERAGENT, $custom_user_agent);
    }
    if (isset($crawlomatic_Main_Settings['request_timeout']) && $crawlomatic_Main_Settings['request_timeout'] != '') {
        $timeout = intval($crawlomatic_Main_Settings['request_timeout']);
    } else {
        $timeout = 10;
    }
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
    if($custom_cookies != '')
    {
        $headers   = array();
        $headers[] = 'Cookie: ' . $custom_cookies;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    }
    if($user_pass != '')
    {
        $har = explode(':', $user_pass);
        if(isset($har[1]))
        {
            curl_setopt($ch, CURLOPT_USERPWD, $user_pass);
        }
    }
    curl_exec($ch);
    $valid = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    if($valid >= 400)
    {
        curl_close($ch);
        if(($valid == 405 || $valid == 503) && stristr($url, 'amazon.') !== false)
        {
            return true;
        }
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
        {
            crawlomatic_log_to_file('Failed to validate URL (code: ' . $valid . '): ' . $url); 
        }
        return false;
    }
    curl_close($ch);
    return true;
}

function crawlomatic_get_random_user_agent() {
	$agents = array(
		"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36",
		"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36",
		"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36",
		"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36",
		"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/603.3.8 (KHTML, like Gecko) Version/10.1.2 Safari/603.3.8",
		"Mozilla/5.0 (Windows NT 6.1; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36",
		"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_12_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36",
		"Mozilla/5.0 (Windows NT 10.0; WOW64; rv:55.0) Gecko/20100101 Firefox/55.0",
		"Mozilla/5.0 (X11; Ubuntu; Linux x86_64; rv:55.0) Gecko/20100101 Firefox/55.0",
		"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.90 Safari/537.36",
		"Mozilla/5.0 (Windows NT 6.1; WOW64; Trident/7.0; rv:11.0) like Gecko",
		"Mozilla/5.0 (Windows NT 6.1; WOW64; rv:55.0) Gecko/20100101 Firefox/55.0",
		"Mozilla/5.0 (Windows NT 10.0; Win64; x64; rv:55.0) Gecko/20100101 Firefox/55.0",
		"Mozilla/5.0 (Windows NT 6.3; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36",
		"Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/52.0.2743.116 Safari/537.36 Edge/15.15063",
		"Mozilla/5.0 (Macintosh; Intel Mac OS X 10.12; rv:55.0) Gecko/20100101 Firefox/55.0",
		"Mozilla/5.0 (Windows NT 10.0; WOW64; rv:54.0) Gecko/20100101 Firefox/54.0",
		"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36",
		"Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.113 Safari/537.36",
		"Mozilla/5.0 (Macintosh; Intel Mac OS X 10_11_6) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/60.0.3112.101 Safari/537.36"
	);
	$rand   = rand( 0, count( $agents ) - 1 );
	return trim( $agents[ $rand ] );
}
use crawlomatic_andreskrey\Readability\ReadabilityCrawlomatic;
use crawlomatic_andreskrey\Readability\Configuration;
function crawlomatic_convert_readable_html($html_string) {
    if(!class_exists('\crawlomatic_andreskrey\Readability\ReadabilityCrawlomatic'))
    {
        if(!interface_exists('Psr\Log\LoggerInterface'))
        {
            require_once (dirname(__FILE__) . '/res/readability/psr/LoggerInterface.php');
            require_once (dirname(__FILE__) . '/res/readability/psr/LoggerAwareInterface.php');
            require_once (dirname(__FILE__) . '/res/readability/psr/LoggerAwareTrait.php');
            require_once (dirname(__FILE__) . '/res/readability/psr/LoggerTrait.php');
            require_once (dirname(__FILE__) . '/res/readability/psr/AbstractLogger.php');
            require_once (dirname(__FILE__) . '/res/readability/psr/InvalidArgumentException.php');
            require_once (dirname(__FILE__) . '/res/readability/psr/LogLevel.php');
            require_once (dirname(__FILE__) . '/res/readability/psr/NullLogger.php');
        }
        require_once (dirname(__FILE__) . "/res/readability/Readability.php");
        require_once (dirname(__FILE__) . "/res/readability/ParseException.php");
        require_once (dirname(__FILE__) . "/res/readability/Configuration.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/NodeUtility.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/NodeTrait.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMAttr.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMCdataSection.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMCharacterData.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMComment.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMDocument.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMDocumentFragment.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMDocumentType.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMElement.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMEntity.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMEntityReference.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMNode.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMNotation.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMProcessingInstruction.php");
        require_once (dirname(__FILE__) . "/res/readability/Nodes/DOM/DOMText.php");
    }
    
    try {
        $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
        if (isset($crawlomatic_Main_Settings['alt_read']) && $crawlomatic_Main_Settings['alt_read'] == "on")
        {
            throw new Exception('fallback');
        }
        $readConf = new Configuration();
        $readConf->setSummonCthulhu(true);
        $readability = new ReadabilityCrawlomatic($readConf);
        $readability->parse($html_string);
        $return_me[0] = $readability->getTitle();
        $return_me[1] = $readability->getContent();
        if($return_me[0] == '' || $return_me[0] == null || $return_me[1] == '' || $return_me[1] == null)
        {
            throw new Exception('Content/title blank ' . print_r($return_me, true));
        }
        $return_me[1] = str_replace('</article>', '', $return_me[1]);
        $return_me[1] = str_replace('<article>', '', $return_me[1]);
        return $return_me;
    } catch (Exception $e) {
        try
        {
            require_once (dirname(__FILE__) . "/res/crawlomatic-readability.php");
            $readability = new Readability2($html_string);
            $readability->debug = false;
            $readability->convertLinksToFootnotes = false;
            $result = $readability->init();
            if ($result) {
                $return_me[0] = $readability->getTitle()->innerHTML;
                $return_me[1] = $readability->getContent()->innerHTML;
                $return_me[1] = str_replace('</article>', '', $return_me[1]);
                $return_me[1] = str_replace('<article>', '', $return_me[1]);
                return $return_me;
            } else {
                return '';
            }
        }
        catch(Exception $e2)
        {
            crawlomatic_log_to_file('Readability failed: ' . sprintf('Error processing text: %s', $e2->getMessage()));
            return '';
        }
    }
}

function crawlomatic_get_timezone_timestamp()
{
    $tz = crawlomatic_get_blog_timezone();;
    $timestamp = time();
    $dt = new DateTime("now", new DateTimeZone($tz->getName()));
    $dt->setTimestamp($timestamp);
    return strtotime($dt->format('d.m.Y, H:i:s'));
}
function crawlomatic_isRegularExpression($string) {
    set_error_handler(function() {}, E_WARNING);
    $isRegularExpression = preg_match($string, "") !== FALSE;
    restore_error_handler();
    return $isRegularExpression;
}
function crawlomatic_tofloat($num, $price_sep) {
    if($num === '' || $num === false)
    {
        return false;
    }
    $dotPos = strrpos($num, '.');
    $commaPos = strrpos($num, ',');
    $sep = (($dotPos > $commaPos) && $dotPos) ? $dotPos : 
        ((($commaPos > $dotPos) && $commaPos) ? $commaPos : false);
   
    if (!$sep) {
        return floatval(preg_replace("/[^0-9]/", "", $num));
    } 

    return floatval(
        preg_replace("/[^0-9]/", "", substr($num, 0, $sep)) . $price_sep .
        preg_replace("/[^0-9]/", "", substr($num, $sep+1, strlen($num)))
    );
}
function crawlomatic_wp_strip_all_tags($string, $remove_breaks = false) {
    $string = preg_replace( '@<(script|style)[^>]*?>.*?</\\1>@si', '', $string );
    $string = strip_tags(str_replace('<', ' <', $string));
 
    if ( $remove_breaks )
        $string = preg_replace('/[\r\n\t ]+/', ' ', $string);
 
    return trim( $string );
}
function crawlomatic_wp_trim_words( $text, $num_words = 55, $more = null ) {
    if ( null === $more ) {
        $more = esc_html__( '&hellip;', 'crawlomatic-multipage-scraper-post-generator' );
    }
    $original_text = $text;
    $text = crawlomatic_wp_strip_all_tags( $text );
    $sep = ' ';
    if ( strpos( _x( 'words', 'Word count type. Do not translate!', 'crawlomatic-multipage-scraper-post-generator' ), 'characters' ) === 0 && preg_match( '/^utf\-?8$/i', get_option( 'blog_charset' ) ) ) {
        $text = trim( preg_replace( "/[\n\r\t ]+/", ' ', $text ), ' ' );
        preg_match_all( '/./u', $text, $words_array );
        $words_array = array_slice( $words_array[0], 0, $num_words + 1 );
    } else {
        $words_array = preg_split( "/[\n\r\t ]+/", $text, $num_words + 1, PREG_SPLIT_NO_EMPTY );
    }
    if ( count( $words_array ) > $num_words ) {
        array_pop( $words_array );
        $text = implode( $sep, $words_array );
        $text = $text . $more;
    } else {
        $text = implode( $sep, $words_array );
    }
    return apply_filters( 'wp_trim_words', $text, $num_words, $more, $original_text );
}
$robots_content = array();
function crawlomatic_robots_allowed($url, $useragent = false)
{
	global $robots_content;
	$response = false;
	$robotstxt = false;
    $parsed = parse_url($url);
    $agents = array(preg_quote('*'));
    if(!empty($useragent))
	{
		$agents[] = preg_quote($useragent, '/');
	}
	$robots_found = false;
    $agents = implode('|', $agents);
	$robots_path = "http://{$parsed['host']}/robots.txt";
	if(isset($robots_content[$robots_path]))
	{
		$response = $robots_content[$robots_path];
		$robots_found = true;
	}
	else
	{
		if(function_exists('curl_init')) 
		{
			$handle = curl_init($robots_path);
			curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($handle, CURLOPT_HTTPHEADER, array('Referer: https://www.google.com/'));
			curl_setopt($handle, CURLOPT_CONNECTTIMEOUT, 10);
			curl_setopt($handle, CURLOPT_HTTPGET, 1);
			curl_setopt($handle, CURLOPT_TIMEOUT, 10);
			curl_setopt($handle, CURLOPT_FOLLOWLOCATION, true);
			$response = curl_exec($handle);
			$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
			if($httpCode == 200 && $response !== false) 
			{
				$robots_content[$robots_path] = $response;
				$robots_found = true;
			}
			curl_close($handle);
		}
	}
	if($robots_found == false || $response === false)
	{
		return true;
	}
	$robotstxt = explode("\n", $response);
    if(empty($robotstxt)) 
	{
		return true;
	}
    $rules = array();
    $ruleApplies = false;
    foreach($robotstxt as $line) 
	{
        if(!$line = trim($line)) 
		{
			continue;
		}
		if(preg_match('/^\s*User-agent: (.*)/i', $line, $match)) 
		{
			$ruleApplies = preg_match("/($agents)/i", $match[1]);
			continue;
        }
        if($ruleApplies) 
		{
			list($type, $rule) = explode(':', $line, 2);
			$type = trim(strtolower($type));
			$rules[] = array(
			    'type' => $type,
			    'match' => preg_quote(trim($rule), '/'),
			);
        }
    }
    $isAllowed = true;
    $currentStrength = 0;
    foreach($rules as $rule) 
	{
        if(preg_match("/^{$rule['match']}/", $parsed['path'])) 
		{
			$strength = strlen($rule['match']);
			if($currentStrength < $strength) 
			{
			    $currentStrength = $strength;
			    $isAllowed = ($rule['type'] == 'allow') ? true : false;
			}
			elseif($currentStrength == $strength && $rule['type'] == 'allow') 
			{
			    $currentStrength = $strength;
			    $isAllowed = true;
			}
        }
    }
    return $isAllowed;
}
$crawl_done = false;
$seed = true;
function crawlomatic_crawl_page($url, $max, $skip_og, $skip_post_content, $no_external, $required_words, $banned_words, $type, $getname, $title_type, $title_getname, $image_type, $image_getname, $date_type, $date_getname, $cat_type, $cat_getname, $depth, $custom_cookies, $only_class, $only_id, $no_source, $seed_type, $seed_expre, $crawled_type, $crawled_expre, $paged_crawl_str, $paged_crawl_type, $max_paged_depth, $custom_user_agent, $posted_items, $update_ex, $cat_sep, $root_page, $seed_pag_type, $seed_pag_expre, $price_type, $price_expre, $clear_static, $use_proxy, $use_phantom, $no_dupl_crawl, $custom_crawling_expre, $user_pass, $crawl_exclude, $crawl_title_exclude, $price_sep, $encoding, $strip_comma, $reverse_crawl, $lazy_tag, $tag_type, $tag_expre, $tag_sep, $phantom_wait, $param, $continue_search, $author_type, $author_expre, $no_match_query, $post_fields, $request_delay, $require_one, $max_crawl, $check_only_content, $scripter, $local_storage, $download_type, $download_expre, $gallery_type, $gallery_expre)
{
    $url = crawlomatic_encodeURI($url);
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
    {
        crawlomatic_log_to_file('Now processing: ' . $url); 
    }
    $skip = false;
    if (!isset($crawlomatic_Main_Settings['title_duplicates']) || $crawlomatic_Main_Settings['title_duplicates'] != 'on')
    {        
        if($no_dupl_crawl === true && has_filter('crawlomatic_filter_dup_check'))
        {
            $continue_filter = false;
            $continue_filter = apply_filters( 'crawlomatic_filter_dup_check', $url );
            if($continue_filter === true)
            {
                if($GLOBALS['seed'] === true)
                {
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                    {
                        crawlomatic_log_to_file('This URL was already crawled before, skipping it\'s importing (by filter): ' . $url); 
                    }
                    $skip = true;
                }
                else
                {
                    crawlomatic_log_to_file('Skipping ' . esc_url($url) . ' because it was already crawled once before (by filter)');
                    return array();
                }
            }
        }
        else
        { 
            if ($no_dupl_crawl === true && isset($posted_items[$url])) {
                if($GLOBALS['seed'] === true)
                {
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                    {
                        crawlomatic_log_to_file('This URL was already crawled before, skipping it\'s importing: ' . $url); 
                    }
                    $skip = true;
                }
                else
                {
                    crawlomatic_log_to_file('Skipping ' . esc_url($url) . ' because it was already crawled once before');
                    return array();
                }
            }
        }
    }
    foreach($crawl_exclude as $crex)
    {
        if($crex != '')
        {
            if(stristr($url, $crex) !== false)
            {
                crawlomatic_log_to_file('Skipping ' . esc_url($url) . ' because it is excluded from crawling.');
                return array();
            }
        }
    }
    static $seen = array();
    static $already_paginated = array();
    static $posts = array();
    if ($clear_static) {
        $posts = array();
    }
    if(count($posts) >= $max)
    {
        $GLOBALS['crawl_done'] = true;
        return $posts;
    }
    if($GLOBALS['crawl_done'] === true)
    {
        return $posts;
    }
    if($GLOBALS['seed'] === true && $no_source == '1')
    {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
        {
            crawlomatic_log_to_file('Skipping seed page from scraping: ' . $url); 
        }
        $skip = true;
    }
    if (isset($seen[$url]) || $depth === 0) {
        return $posts;
    }
    $post = array();
    $post['crawled_date'] = false;
    $post['title'] = '';
    $post['url'] = $url;
    $seen[$url] = true;
    $dom = new DOMDocument('1.0');
    $html_cont = false;
    $got_phantom = false;
    if($use_phantom == '1')
    {
        $html_cont = crawlomatic_get_page_PhantomJS($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
        if($html_cont !== false)
        {
            $got_phantom = true;
        }
    }
    elseif($use_phantom == '2')
    {
        $html_cont = crawlomatic_get_page_Puppeteer($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
        if($html_cont !== false)
        {
            $got_phantom = true;
        }
    }
    elseif($use_phantom == '3')
    {
        $html_cont = crawlomatic_get_page_Tor($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
        if($html_cont !== false)
        {
            $got_phantom = true;
        }
    }
    elseif($use_phantom == '4')
    {
        $html_cont = crawlomatic_get_page_PuppeteerAPI($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
        if($html_cont !== false)
        {
            $got_phantom = true;
        }
    }
    elseif($use_phantom == '5')
    {
        $html_cont = crawlomatic_get_page_TorAPI($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
        if($html_cont !== false)
        {
            $got_phantom = true;
        }
    }
    elseif($use_phantom == '6')
    {
        $html_cont = crawlomatic_get_page_PhantomJSAPI($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
        if($html_cont !== false)
        {
            $got_phantom = true;
        }
    }
    if($got_phantom === false)
    {
        $html_cont = crawlomatic_get_web_page($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, '', $post_fields, $request_delay);
    }
    if($html_cont === false)
    {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
        {
            crawlomatic_log_to_file('Failed to get content for: ' . $url); 
        }
        return false;
    }
    if (isset($crawlomatic_Main_Settings['enable_robots']) && $crawlomatic_Main_Settings['enable_robots'] == 'on') 
    {
		$is_robots = crawlomatic_findRobotsMetaTagProperties($html_cont);
        if((isset($is_robots['noindex']) && $is_robots['noindex'] == true) || (isset($is_robots['none']) && $is_robots['none'] == true))
        {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
				crawlomatic_log_to_file('Skipping URL, crawling disabled by robots tag: ' . $url);
            }
            return false;
        }
		$robotsnoindex = false;
		if(!crawlomatic_robots_allowed($url, $custom_user_agent))
		{
			$robotsnoindex = true;
		}
		if($robotsnoindex == true)
        {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
				crawlomatic_log_to_file('Skipping URL, crawling disabled by robots.txt: ' . $url);
            }
            return false;
        }
    }
    if ($encoding != 'UTF-8' && $encoding != 'NO_CHANGE')
    {
        $extract_temp = FALSE;
        if($encoding !== 'AUTO')
        {
            if (function_exists('iconv'))
            {
                $extract_temp = iconv($encoding, "UTF-8//IGNORE", $html_cont);
            }
        }
        else
        {
            if(function_exists('mb_detect_encoding'))
            {
                $temp_enc = mb_detect_encoding($html_cont, 'auto');
                if ($temp_enc !== FALSE && $temp_enc != 'UTF-8')
                {
                    if (function_exists('iconv'))
                    {
                        $extract_temp = iconv($temp_enc, "UTF-8//IGNORE", $html_cont);
                    }
                }
            }
        }
        if($extract_temp !== FALSE)
        {
            $html_cont = $extract_temp;
        }
    }
    $internalErrors = libxml_use_internal_errors(true);
    $dom->loadHTML('<?xml encoding="utf-8" ?>' . $html_cont);
    libxml_use_internal_errors($internalErrors);
    if($skip != true)
    {
        if($type == 'disabled')
        {
            $post['content'] = '';
        }
        else
        {
            $probably_skip = false;
            if($getname == '' || $type == 'auto')
            {
                $extract = crawlomatic_convert_readable_html($html_cont);
                if($extract == '')
                {
                    $post['content'] = '';
                    $post['title'] = '';
                }
                else
                {
                    $post['content'] = $extract[1];
                    $post['title'] = $extract[0];
                }
            }
            else
            {
                $post['content'] = crawlomatic_get_content($type, $getname, $html_cont);
                if($post['content'] == '')
                {
                    if($no_match_query == '1')
                    {
                        $probably_skip = true;
                    }
                    else
                    {
                        if (!isset($crawlomatic_Main_Settings['no_content_autodetect']) || $crawlomatic_Main_Settings['no_content_autodetect'] != 'on')
                        {
                            $extract = crawlomatic_convert_readable_html($html_cont);
                            if($extract != '')
                            {
                                $post['content'] = $extract[1];
                            }
                        }
                    }
                }
            }
            if($paged_crawl_str != '' && $paged_crawl_type != 'disabled')
            {
                $extract_ext = crawlomatic_get_paged_content($url, $html_cont, $type, $getname, $paged_crawl_type, $paged_crawl_str, $custom_cookies, $max_paged_depth, $custom_user_agent, $use_proxy, $use_phantom, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
                if($extract_ext != '')
                {
                    $probably_skip = false;
                    if(isset($post['content']))
                    {
                        $post['content'] .= $extract_ext;
                    }
                    else
                    {
                        $post['content'] = $extract_ext;
                    }
                }
            }
            if($probably_skip == true)
            {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                {
                    crawlomatic_log_to_file('Required query string not found, skipping: ' . $url); 
                }
                $skip = true;
            }
        }
    }
    if($check_only_content != '1')
    {
        if($required_words != '')
        {
            $req_list = explode(',', $required_words);
            if($require_one == '1')
            {
                $required_found = false;
                foreach($req_list as $rl)
                {
                    if(function_exists('mb_stristr'))
                    {
                        if(mb_stristr($html_cont, $rl) !== false)
                        {
                            $required_found = true;
                            break;
                        }
                    }
                    else
                    {
                        if(stristr($html_cont, $rl) === false)
                        {
                            $required_found = true;
                            break;
                        }
                    }
                }
                if($required_found === false)
                {
                    if($GLOBALS['seed'] === false)
                    {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                        {
                            crawlomatic_log_to_file('No required word found (not seed), skipping: ' . $url); 
                        }
                        return $posts;
                    }
                    else
                    {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                        {
                            crawlomatic_log_to_file('No required word found, skipping: ' . $url); 
                        }
                        $skip = true;
                    }
                }
            }
            else
            {
                foreach($req_list as $rl)
                {
                    if(function_exists('mb_stristr'))
                    {
                        if(mb_stristr($html_cont, $rl) === false)
                        {
                            if($GLOBALS['seed'] === false)
                            {
                                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                {
                                    crawlomatic_log_to_file('Required word not found (not seed), skipping: ' . $url); 
                                }
                                return $posts;
                            }
                            else
                            {
                                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                {
                                    crawlomatic_log_to_file('Required word not found, skipping: ' . $url); 
                                }
                                $skip = true;
                            }
                        }
                    }
                    else
                    {
                        if(stristr($html_cont, $rl) === false)
                        {
                            if($GLOBALS['seed'] === false)
                            {
                                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                {
                                    crawlomatic_log_to_file('Required word not found (not seed), skipping: ' . $url); 
                                }
                                return $posts;
                            }
                            else
                            {
                                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                {
                                    crawlomatic_log_to_file('Required word not found, skipping: ' . $url); 
                                }
                                $skip = true;
                            }
                        }
                    }
                }
            }
        }
        if($banned_words != '')
        {
            $ban_list = explode(',', $banned_words);
            foreach($ban_list as $bl)
            {
                if(function_exists('mb_stristr'))
                {
                    if(mb_stristr($html_cont, $bl) !== false)
                    {
                        if($GLOBALS['seed'] === false)
                        {
                            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                            {
                                crawlomatic_log_to_file('Banned word detected (not seed), skipping it\'s importing: ' . $url); 
                            }
                            return $posts;
                        }
                        else
                        {
                            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                            {
                                crawlomatic_log_to_file('Banned word detected, skipping it\'s importing: ' . $url); 
                            }
                            $skip = true;
                        }
                    }
                }
                else
                {
                    if(stristr($html_cont, $bl) !== false)
                    {
                        if($GLOBALS['seed'] === false)
                        {
                            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                            {
                                crawlomatic_log_to_file('Banned word detected (not seed), skipping it\'s importing: ' . $url); 
                            }
                            return $posts;
                        }
                        else
                        {
                            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                            {
                                crawlomatic_log_to_file('Banned word detected, skipping it\'s importing: ' . $url); 
                            }
                            $skip = true;
                        }
                    }
                }
            }
        }
    }
    if($skip != true)
    {
        if($title_type == 'disabled')
        {
            $post['title'] = '';
        }
        else
        {
            
            if($title_type == 'og')
            {
                $post['title'] = crawlomatic_wp_strip_all_tags(crawlomatic_get_title($html_cont));
                if($post['title'] == '')
                {
                    $extract = crawlomatic_convert_readable_html($html_cont);
                    if($extract != '')
                    {
                        $post['title'] = $extract[0];
                    }
                }
            }
            elseif($title_getname == '' || $title_type == 'auto')
            {
                if($post['title'] == '')
                {
                    $extract = crawlomatic_convert_readable_html($html_cont);
                    if($extract == '')
                    {
                        $post['title'] = '';
                    }
                    else
                    {
                        $post['title'] = $extract[0];
                    }
                }
            }
            else
            {
                $post['title'] = crawlomatic_wp_strip_all_tags(crawlomatic_get_content($title_type, $title_getname, $html_cont));
                if($post['title'] == '')
                {
                    $extract = crawlomatic_convert_readable_html($html_cont);
                    if($extract != '')
                    {
                        $post['title'] = $extract[0];
                    }
                }
            }
        }
        if($post['content'] != '')
        {
            $post['content'] = preg_replace('/<a([^><]*)href=[\'"](#[^<>"\']*?)[\'"]/', '<a$1href="' . $url . '$2"', $post['content']);
        }
        if($post['title'] == '')
        {
            $h1s = $dom->getElementsByTagName('h1');
            $title = '';
            foreach ($h1s as $h1) {
                $title .= $h1->nodeValue;
            }
            if($title == '' && $post['content'] != '')
            {
                $title = crawlomatic_wp_trim_words($post['content'], 10);
            }
            $post['title'] = $title;
        }
        if($author_type != 'disabled')
        {
            if($author_expre == '' || $author_type == 'og')
            {
                $post['author'] = crawlomatic_get_author($html_cont);
            }
            else
            {
                $temp_auth = crawlomatic_get_content($author_type, $author_expre, $html_cont, true);
                if($temp_auth != '')
                {
                    $temp_auth = strip_tags($temp_auth);
                    if($temp_auth != '')
                    {
                        $post['author'] = $temp_auth;
                    }
                }
            }
        }
        foreach($crawl_title_exclude as $cret)
        {
            if($cret != '')
            {
                if(stristr($post['title'], $cret) !== false)
                {
                    crawlomatic_log_to_file('Skipping ' . $post['title'] . ' because it is excluded from crawling by title.');
                    return array();
                }
            }
        }
        if($post['url'] != '' && has_filter('crawlomatic_filter_dup_check'))
        {
            $continue_filter = false;
            $continue_filter = apply_filters( 'crawlomatic_filter_dup_check', $post['url'] );
            if($continue_filter === true)
            {
                if ($update_ex == false) {
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                    {
                        crawlomatic_log_to_file('Skipping by filter: ' . $post['url']); 
                    }
                    $skip = true;
                }
            }
        }
        else
        {
            if ($post['url'] != '' && isset($posted_items[$post['url']])) {
                if ($update_ex == false) {
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                    {
                        crawlomatic_log_to_file('Already posted, skipping: ' . $post['url']); 
                    }
                    $skip = true;
                }
            }
        }
        if (isset($crawlomatic_Main_Settings['title_duplicates']) && $crawlomatic_Main_Settings['title_duplicates'] == 'on')
        {        
            if($no_dupl_crawl === true && has_filter('crawlomatic_filter_dup_check'))
            {
                $continue_filter = false;
                $continue_filter = apply_filters( 'crawlomatic_filter_dup_check', $post['title'] );
                if($continue_filter === true)
                {
                    if($GLOBALS['seed'] === true)
                    {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                        {
                            crawlomatic_log_to_file('This post title was already crawled before, skipping it\'s importing (filter): ' . $post['title']); 
                        }
                        $skip = true;
                    }
                    else
                    {
                        crawlomatic_log_to_file('Skipping post title ' . $post['title'] . ' because it was already crawled once before (filter)');
                        return array();
                    }
                }
            }
            else
            {
                if ($no_dupl_crawl === true && $post['title'] != '' && isset($posted_items[$post['title']])) 
                {
                    if($GLOBALS['seed'] === true)
                    {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                        {
                            crawlomatic_log_to_file('This post title was already crawled before, skipping it\'s importing: ' . $post['title']); 
                        }
                        $skip = true;
                    }
                    else
                    {
                        crawlomatic_log_to_file('Skipping post title ' . $post['title'] . ' because it was already crawled once before');
                        return array();
                    }
                }
            }
        }
        if($skip != true)
        {
            if($image_type != 'disabled')
            {
                if(($image_getname == '' && $image_type != 'screenshot') || $image_type == 'auto' || $image_type == 'og')
                {
                    if($image_type == 'og')
                    {
                        $skip_og = '0';
                    }
                    $post['image'] = crawlomatic_get_featured_image($html_cont, $dom, $skip_og, $skip_post_content, $url, $lazy_tag);
                }
                elseif($image_type == 'screenshot')
                {
                    $screenimageURL = '';
                    $screens_attach_id = '';
                    if (isset($crawlomatic_Main_Settings['headless_screen']) && $crawlomatic_Main_Settings['headless_screen'] == 'on')
                    {
                        {
                            if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') 
                            {
                                $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
                                $randomness = array_rand($prx);
                                $phantomjs_comm .= '--proxy=' . trim($prx[$randomness]) . ' ';
                                if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
                                {
                                    $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
                                    if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
                                    {
                                        $phantomjs_comm .= '--proxy-auth=' . trim($prx_auth[$randomness]) . ' ';
                                    }
                                }
                            }
                            if($custom_user_agent == '')
                            {
                                $custom_user_agent = 'default';
                            }
                            if($custom_cookies == '')
                            {
                                $custom_cookies = 'default';
                            }
                            if($user_pass == '')
                            {
                                $user_pass = 'default';
                            }
                            if (isset($crawlomatic_Main_Settings['screenshot_height']) && $crawlomatic_Main_Settings['screenshot_height'] != '') 
                            {
                                $h = esc_attr($crawlomatic_Main_Settings['screenshot_height']);
                            }
                            else
                            {
                                $h = '0';
                            }
                            if (isset($crawlomatic_Main_Settings['screenshot_width']) && $crawlomatic_Main_Settings['screenshot_width'] != '') 
                            {
                                $w = esc_attr($crawlomatic_Main_Settings['screenshot_width']);
                            }
                            else
                            {
                                $w = '1920';
                            }
                            $screenshotimg = crawlomatic_get_screenshot_PuppeteerAPI($url, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, '', $request_delay, $scripter, $local_storage, $h, $w);
                            if($screenshotimg !== false)
                            {
                                $upload_dir = wp_upload_dir();
                                $dir_name   = $upload_dir['basedir'] . '/crawlomatic-files';
                                $dir_url    = $upload_dir['baseurl'] . '/crawlomatic-files';
                                global $wp_filesystem;
                                if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
                                    include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
                                    wp_filesystem($creds);
                                }
                                if (!$wp_filesystem->exists($dir_name)) {
                                    wp_mkdir_p($dir_name);
                                }
                                $screen_name = uniqid();
                                $screenimageName = $dir_name . '/' . $screen_name . '.jpg';
                                $screenimageURL = $dir_url . '/' . $screen_name . '.jpg';
                                
                                $is_fail = $wp_filesystem->put_contents($screenimageName, $screenshotimg);
                                if($is_fail === false)
                                {
                                    crawlomatic_log_to_file('Error in writing screenshot to file: ' . $screenimageName);
                                }
                                else
                                {
                                    $wp_filetype = wp_check_filetype( $screen_name . '.jpg', null );
                                    $attachment = array(
                                        'post_mime_type' => $wp_filetype['type'],
                                        'post_title' => sanitize_file_name( $screen_name . '.jpg' ),
                                        'post_content' => '',
                                        'post_status' => 'inherit'
                                    );
                                    $screens_attach_id = wp_insert_attachment($attachment, $screenimageName);
                                    require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                    require_once( ABSPATH . 'wp-admin/includes/media.php' );
                                    $attach_data = wp_generate_attachment_metadata($screens_attach_id, $screenimageName);
                                    wp_update_attachment_metadata( $screens_attach_id, $attach_data );
                                }
                            }
                        }
                    }
                    elseif (isset($crawlomatic_Main_Settings['phantom_screen']) && $crawlomatic_Main_Settings['phantom_screen'] == 'on')
                    {
                        {
                            if(function_exists('shell_exec')) 
                            {
                                $disabled = explode(',', ini_get('disable_functions'));
                                if(!in_array('shell_exec', $disabled))
                                {
                                    if (isset($crawlomatic_Main_Settings['phantom_path']) && $crawlomatic_Main_Settings['phantom_path'] != '') 
                                    {
                                        $phantomjs_comm = $crawlomatic_Main_Settings['phantom_path'] . ' ';
                                    }
                                    else
                                    {
                                        $phantomjs_comm = 'phantomjs ';
                                    }
                                    if (isset($crawlomatic_Main_Settings['screenshot_height']) && $crawlomatic_Main_Settings['screenshot_height'] != '') 
                                    {
                                        $h = esc_attr($crawlomatic_Main_Settings['screenshot_height']);
                                    }
                                    else
                                    {
                                        $h = '0';
                                    }
                                    if (isset($crawlomatic_Main_Settings['screenshot_width']) && $crawlomatic_Main_Settings['screenshot_width'] != '') 
                                    {
                                        $w = esc_attr($crawlomatic_Main_Settings['screenshot_width']);
                                    }
                                    else
                                    {
                                        $w = '1920';
                                    }
                                    $upload_dir = wp_upload_dir();
                                    $dir_name   = $upload_dir['basedir'] . '/crawlomatic-files';
                                    $dir_url    = $upload_dir['baseurl'] . '/crawlomatic-files';
                                    global $wp_filesystem;
                                    if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
                                        include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
                                        wp_filesystem($creds);
                                    }
                                    if (!$wp_filesystem->exists($dir_name)) {
                                        wp_mkdir_p($dir_name);
                                    }
                                    $screen_name = uniqid();
                                    $screenimageName = $dir_name . '/' . $screen_name;
                                    $screenimageURL = $dir_url . '/' . $screen_name . '.jpg';
                                    if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') 
                                    {
                                        $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
                                        $randomness = array_rand($prx);
                                        $phantomjs_comm .= '--proxy=' . trim($prx[$randomness]) . ' ';
                                        if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
                                        {
                                            $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
                                            if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
                                            {
                                                $phantomjs_comm .= '--proxy-auth=' . trim($prx_auth[$randomness]) . ' ';
                                            }
                                        }
                                    }
                                    if($custom_user_agent == '')
                                    {
                                        $custom_user_agent = 'default';
                                    }
                                    if($custom_cookies == '')
                                    {
                                        $custom_cookies = 'default';
                                    }
                                    if($user_pass == '')
                                    {
                                        $user_pass = 'default';
                                    }
                                    $cmdResult = shell_exec($phantomjs_comm . '"' . dirname(__FILE__) .'/res/phantomjs/phantom-screenshot.js"' . ' "'. dirname(__FILE__) . '" "' . $url . '" "' . $screenimageName . '" ' . $w . ' ' . $h . '  "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '" 2>&1');
                                    if($cmdResult === NULL || $cmdResult == '' || trim($cmdResult) === 'timeout' || stristr($cmdResult, 'sh: phantomjs: command not found') !== false)
                                    {
                                        $screenimageURL = '';
                                        crawlomatic_log_to_file('Error in phantomjs screenshot: exec: ' . $phantomjs_comm . '"' . dirname(__FILE__) .'/res/phantomjs/phantom-screenshot.js"' . ' "'. dirname(__FILE__) . '" "' . $url . '" "' . $screenimageName . '" ' . $w . ' ' . $h . ' "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '" , reterr: ' . $cmdResult);
                                    }
                                    else
                                    {
                                        if($wp_filesystem->exists($screenimageName))
                                        {
                                            $wp_filetype = wp_check_filetype( $screen_name . '.jpg', null );
                                            $attachment = array(
                                            'post_mime_type' => $wp_filetype['type'],
                                            'post_title' => sanitize_file_name( $screen_name . '.jpg' ),
                                            'post_content' => '',
                                            'post_status' => 'inherit'
                                            );
                                            $screens_attach_id = wp_insert_attachment( $attachment, $screenimageName . '.jpg' );
                                            require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                            require_once( ABSPATH . 'wp-admin/includes/media.php' );
                                            $attach_data = wp_generate_attachment_metadata( $screens_attach_id, $screenimageName . '.jpg' );
                                            wp_update_attachment_metadata( $screens_attach_id, $attach_data );
                                        }
                                        else
                                        {
                                            crawlomatic_log_to_file('Error in phantomjs screenshot not found: exec: ' . $phantomjs_comm . '"' . dirname(__FILE__) .'/res/phantomjs/phantom-screenshot.js"' . ' "'. dirname(__FILE__) . '" "' . $url . '" "' . $screenimageName . '" ' . $w . ' ' . $h . ' "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '" , reterr: ' . $cmdResult);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    elseif (isset($crawlomatic_Main_Settings['puppeteer_screen']) && $crawlomatic_Main_Settings['puppeteer_screen'] == 'on')
                    {
                        {
                            if(function_exists('shell_exec')) 
                            {
                                $disabled = explode(',', ini_get('disable_functions'));
                                if(!in_array('shell_exec', $disabled))
                                {
                                    $phantomjs_comm = 'node ';
                                    if (isset($crawlomatic_Main_Settings['screenshot_height']) && $crawlomatic_Main_Settings['screenshot_height'] != '') 
                                    {
                                        $h = esc_attr($crawlomatic_Main_Settings['screenshot_height']);
                                    }
                                    else
                                    {
                                        $h = '0';
                                    }
                                    if (isset($crawlomatic_Main_Settings['screenshot_width']) && $crawlomatic_Main_Settings['screenshot_width'] != '') 
                                    {
                                        $w = esc_attr($crawlomatic_Main_Settings['screenshot_width']);
                                    }
                                    else
                                    {
                                        $w = '1920';
                                    }
                                    if (isset($crawlomatic_Main_Settings['phantom_timeout']) && $crawlomatic_Main_Settings['phantom_timeout'] != '') 
                                    {
                                        $phantomjs_timeout = ((int)$crawlomatic_Main_Settings['phantom_timeout']);
                                    }
                                    else
                                    {
                                        $phantomjs_timeout = 'default';
                                    }
                                    if ($w < 350) {
                                        $w = 350;
                                    }
                                    if ($w > 1920) {
                                        $w = 1920;
                                    }
                                    $upload_dir = wp_upload_dir();
                                    $dir_name   = $upload_dir['basedir'] . '/crawlomatic-files';
                                    $dir_url    = $upload_dir['baseurl'] . '/crawlomatic-files';
                                    global $wp_filesystem;
                                    if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
                                        include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
                                        wp_filesystem($creds);
                                    }
                                    if (!$wp_filesystem->exists($dir_name)) {
                                        wp_mkdir_p($dir_name);
                                    }
                                    $screen_name = uniqid();
                                    $screenimageName = $dir_name . '/' . $screen_name . '.jpg';
                                    $screenimageURL = $dir_url . '/' . $screen_name . '.jpg';
                                    $phantomjs_proxcomm = '"null"';
                                    if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') 
                                    {
                                        $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
                                        $randomness = array_rand($prx);
                                        $phantomjs_proxcomm = '"' . trim($prx[$randomness]);
                                        if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
                                        {
                                            $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
                                            if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
                                            {
                                                $phantomjs_proxcomm .= ':' . trim($prx_auth[$randomness]);
                                            }
                                        }
                                        $phantomjs_proxcomm .= '"';
                                    }
                                    if($custom_user_agent == '')
                                    {
                                        $custom_user_agent = 'default';
                                    }
                                    if($custom_cookies == '')
                                    {
                                        $custom_cookies = 'default';
                                    }
                                    if($user_pass == '')
                                    {
                                        $user_pass = 'default';
                                    }
                                    $cmdResult = shell_exec($phantomjs_comm . '"' . dirname(__FILE__) .'/res/puppeteer/screenshot.js"' . ' "' . $url . '" "' . $screenimageName . '" ' . $w . ' ' . $h . ' ' . $phantomjs_proxcomm . '  "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . $phantomjs_timeout . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '" 2>&1');
                                    if(stristr($cmdResult, 'sh: node: command not found') !== false || stristr($cmdResult, 'throw err;') !== false)
                                    {
                                        $screenimageURL = '';
                                        crawlomatic_log_to_file('Error in puppeteer screenshot: exec: ' . $phantomjs_comm . '"' . dirname(__FILE__) .'/res/puppeteer/screenshot.js"' . ' "' . $url . '" "' . $screenimageName . '" ' . $w . ' ' . $h . ' ' . $phantomjs_proxcomm . '  "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '" "' . $phantomjs_timeout . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '", reterr: ' . $cmdResult);
                                    }
                                    else
                                    {
                                        if($wp_filesystem->exists($screenimageName))
                                        {
                                            $wp_filetype = wp_check_filetype( $screen_name . '.jpg', null );
                                            $attachment = array(
                                            'post_mime_type' => $wp_filetype['type'],
                                            'post_title' => sanitize_file_name( $screen_name . '.jpg' ),
                                            'post_content' => '',
                                            'post_status' => 'inherit'
                                            );
                                            $screens_attach_id = wp_insert_attachment( $attachment, $screenimageName);
                                            require_once( ABSPATH . 'wp-admin/includes/image.php' );
                                            require_once( ABSPATH . 'wp-admin/includes/media.php' );
                                            $attach_data = wp_generate_attachment_metadata( $screens_attach_id, $screenimageName);
                                            wp_update_attachment_metadata( $screens_attach_id, $attach_data );
                                        }
                                        else
                                        {
                                            crawlomatic_log_to_file('Error in puppeteer screenshot not found: exec: ' . $phantomjs_comm . '"' . dirname(__FILE__) .'/res/puppeteer/screenshot.js"' . ' "' . $url . '" "' . $screenimageName . '" ' . $w . ' ' . $h . ' ' . $phantomjs_proxcomm . '  "' . esc_html($custom_user_agent) . '" "' . esc_html($custom_cookies) . '" "' . esc_html($user_pass) . '"  "' . $phantomjs_timeout . '" "' . addslashes($scripter) . '" "' . addslashes($local_storage) . '", reterr: ' . $cmdResult);
                                        }
                                    }
                                }
                            }
                        }
                    }
                    if($screenimageURL != '')
                    {
                        $post['image'] = $screenimageURL;
                        $post['screen_image'] = $screenimageURL;
                    }
                }
                else
                {
                    $temp_image = crawlomatic_get_content($image_type, $image_getname, $html_cont, true);
                    if($temp_image != '')
                    {
                        if(filter_var(trim($temp_image), FILTER_VALIDATE_URL))
                        {
                            $post['image'] = trim($temp_image);
                        }
                        else
                        {
                            $tmpdoc = new DOMDocument();
                            $internalErrors = libxml_use_internal_errors(true);
                            $tmpdoc->loadHTML($temp_image);
                            libxml_use_internal_errors($internalErrors);
                            $imageTags = $tmpdoc->getElementsByTagName('img');
                            if(count($imageTags) > 0)
                            {
                                if($imageTags[0] !== null)
                                {
                                    if($lazy_tag == '')
                                    {
                                        $lazy_tag = 'src';
                                    }
                                    $post['image'] = crawlomatic_encodeURI($imageTags[0]->getAttribute($lazy_tag));
                                    if($post['image'] == '' && $lazy_tag != 'src')
                                    {
                                        $post['image'] = crawlomatic_encodeURI($imageTags[0]->getAttribute('src'));
                                    }
                                    if($post['image'] == '')
                                    {
                                        preg_match('@src=["\']([^"\']+)["\']@i', $temp_image, $match);
                                        if(isset($match[1]) && $match[1] != '')
                                        {
                                            $post['image'] = crawlomatic_encodeURI($match[1]);
                                        }
                                    }
                                    if($post['image'] == '')
                                    {
                                        $post['image'] = crawlomatic_get_featured_image($html_cont, $dom, $skip_og, $skip_post_content, $url, $lazy_tag);
                                    }
                                }
                                else
                                {
                                    preg_match('@src=["\']([^"\']+)["\']@i', $temp_image, $match);
                                    if(isset($match[1]) && $match[1] != '')
                                    {
                                        $post['image'] = crawlomatic_encodeURI($match[1]);
                                    }
                                    if(!isset($post['image']) || $post['image'] == '')
                                    {
                                        $post['image'] = crawlomatic_get_featured_image($html_cont, $dom, $skip_og, $skip_post_content, $url, $lazy_tag);
                                    }
                                }
                            }
                            else
                            {
                                preg_match('@src=["\']([^"\']+)["\']@i', $temp_image, $match);
                                if(isset($match[1]) && $match[1] != '')
                                {
                                    $post['image'] = crawlomatic_encodeURI($match[1]);
                                }
                                if(!isset($post['image']) || $post['image'] == '')
                                {
                                    $post['image'] = crawlomatic_get_featured_image($html_cont, $dom, $skip_og, $skip_post_content, $url, $lazy_tag);
                                }
                            }
                            unset($tmpdoc);
                        }
                    }
                }
            }
            if(isset($post['image']) && $post['image'] != '')
            {
                if(stristr($post['image'], 'http:') === FALSE && stristr($post['image'], 'https:') === FALSE)
                {
                    $post['image'] = crawlomatic_fix_single_link($post['image'], $url);
                }
            }
            if(!isset($post['image']))
            {
                $post['image'] = '';
            }
            else
            {
                if (isset($crawlomatic_Main_Settings['remove_img_content']) && $crawlomatic_Main_Settings['remove_img_content'] == 'on')
                {
                    $imgza = explode('?', $post['image']);
                    $imgza = $imgza[0];
                    $post['content'] = preg_replace('~<img(?:[^>]*?)=[\'"]' . preg_quote($imgza) . '(?:[^\'"]*)?[\'"](?:[^>]*?)>~', '', $post['content']);
                }
            }
            $custom_shortcode_final = array();
            if(trim($custom_crawling_expre) != '')
            {
                $custom_crawling_arr = preg_split('/\r\n|\r|\n/', trim($custom_crawling_expre));
                foreach($custom_crawling_arr as $cca)
                {
                    //shortcode_name => [class/id/xpath/regex] @@ [class/id/xpath/regex expression]
                    if(trim($cca) != '')
                    {
                        $cshortcode_name = explode('=>', $cca);
                        if(isset($cshortcode_name[1]))
                        {
                            $shortcode_name = trim($cshortcode_name[0]);
                            if(strstr($cshortcode_name[1], '@@'))
                            {
                                $xshortcode_args = explode('@@', $cshortcode_name[1]);
                                if(isset($xshortcode_args[1]) && trim($xshortcode_args[0]) != '' && trim($xshortcode_args[1]) != '')
                                {
                                    $extended_content = crawlomatic_get_content(trim($xshortcode_args[0]), trim($xshortcode_args[1]), $html_cont, false);
                                    if($extended_content != '')
                                    {
                                        $extended_content = crawlomatic_fix_links($extended_content, $url);
                                        $custom_shortcode_final[$shortcode_name] = $extended_content;
                                    }
                                    else
                                    {
                                        $custom_shortcode_final[$shortcode_name] = '';
                                    }
                                }
                                else
                                {
                                    $custom_shortcode_final[$shortcode_name] = '';
                                }
                            }
                            else
                            {
                                $custom_shortcode_final[$shortcode_name] = '';
                            }
                        }
                    }
                }
            }
            $post['custom_shortcodes'] = $custom_shortcode_final;
            if($date_getname == '' || $date_type == 'current')
            {
                $post['date'] = date("Y-m-d H:i:s", time());
            }
            else
            {
                $temp_date = crawlomatic_get_content($date_type, $date_getname, $html_cont, true);
                $temp_date = crawlomatic_wp_trim_words($temp_date, 99999);
                $temp_date = strtotime($temp_date);
                if($temp_date !== false)
                {
                    $post['date'] = date("Y-m-d H:i:s", $temp_date);
                    $post['crawled_date'] = true;
                }
                else
                {
                    $post['date'] = date("Y-m-d H:i:s", time());
                }
            }
            if($price_expre == '' || $price_type == 'disabled')
            {
                $post['price'] = '';
            }
            else
            {
                $temp_price = crawlomatic_get_content($price_type, $price_expre, $html_cont, true);
				$temp_price = crawlomatic_wp_trim_words($temp_price, 99999);
                $temp_price = html_entity_decode($temp_price, ENT_QUOTES | ENT_XML1, 'UTF-8');
                if($strip_comma == '1')
                {
                    $temp_price = str_replace(',','',$temp_price);
                    $temp_price = str_replace('.','',$temp_price);
                }
				elseif($strip_comma != '0')
                {
					$strip_commax = preg_split('/\r\n|\r|\n/', $strip_comma);
					$strip_commax = array_map('trim', $strip_commax);
					foreach($strip_commax as $scx)
					{
						$temp_price = str_replace($scx,'',$temp_price);
					}
				}
                $temp_price = crawlomatic_tofloat($temp_price, $price_sep);
				if($temp_price !== false)
                {
                    $post['price'] = $temp_price;
                }
                else
                {
                    $post['price'] = '';
                }
            }
            if($cat_type == 'title')
            {
                if($post['title'] !== '')
                {
                    $keyword_class = new Crawlomatic_keywords();
                    $title_words = $keyword_class->keywords($post['title'], 2);
                    $post['categories'] = explode(' ', $title_words);
                }
                else
                {
                    $post['categories'] = array();
                }
            }
            elseif($cat_getname == '' || $cat_type == 'disabled')
            {
                $post['categories'] = array();
            }
            else
            {
                $temp_categories = crawlomatic_get_content($cat_type, $cat_getname, $html_cont);
                $temp_categories = crawlomatic_wp_trim_words($temp_categories, 99999);
                $post['categories'] = explode($cat_sep, $temp_categories);
                if($post['categories'] == '')
                {
                    $post['categories'] = array();
                }
            }
            if($tag_type == 'title')
            {
                if($post['title'] !== '')
                {
                    $keyword_class = new Crawlomatic_keywords();
                    $title_words = $keyword_class->keywords($post['title'], 2);
                    $post['tags'] = explode(' ', $title_words);
                }
                else
                {
                    $post['tags'] = array();
                }
            }
            elseif($tag_expre == '' || $tag_type == 'disabled')
            {
                $post['tags'] = array();
            }
            else
            {
                $temp_tags = crawlomatic_get_content($tag_type, $tag_expre, $html_cont);
                $temp_tags = crawlomatic_wp_trim_words($temp_tags, 99999);
                $post['tags'] = explode($tag_sep, $temp_tags);
                if($post['tags'] == '')
                {
                    $post['tags'] = array();
                }
            }
            if($gallery_expre == '' || $gallery_type == 'disabled')
            {
                $post['gallery'] = array();
            }
            else
            {
                $post['gallery'] = crawlomatic_get_gallery_content($gallery_type, $gallery_expre, $html_cont, $url, $lazy_tag);
            }
            if($download_expre == '' || $download_type == 'disabled')
            {
                $post['download_remote'] = '';
                $post['download_local'] = '';
            }
            else
            {
                $post['download_remote'] = '';
                $za_dl_link = '';
                $post['download_local'] = '';
                $temp_download = crawlomatic_get_content($download_type, $download_expre, $html_cont);
                preg_match_all('#\bhttps?://[^,\s()<>]+(?:\([\w\d]+\)|([^,[:punct:]\s]|/))#', $temp_download, $lnkmatch);
                if(isset($lnkmatch[0][0]))
                {
                    $za_dl_link = trim($lnkmatch[0][0]);
                }
                else
                {
                    preg_match_all('#href=["\']([^"\']*?)["\']#', $temp_download, $lnkmatch);
                    if(isset($lnkmatch[1][0]))
                    {
                        $za_dl_link = trim($lnkmatch[1][0]);
                        $za_dl_link = crawlomatic_fix_single_link($za_dl_link, $url);
                    }
                }
                if($za_dl_link != '')
                {
                    $temp_download = str_replace(' ', '%20', $za_dl_link);
                    if(strstr($temp_download, '#038;'))
                    {
                        $temp_download = str_replace('#038;', '', $temp_download);
                    }
                    if(filter_var($temp_download, FILTER_VALIDATE_URL))
                    {
                        $upload_dir = wp_upload_dir();
                        $dir_name   = $upload_dir['basedir'] . '/crawlomatic-files';
                        $dir_url    = $upload_dir['baseurl'] . '/crawlomatic-files';
                        global $wp_filesystem;
                        if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
                            include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
                            wp_filesystem($creds);
                        }
                        if (!$wp_filesystem->exists($dir_name)) {
                            wp_mkdir_p($dir_name);
                        }
                        $name_str = basename($temp_download);
                        
                        if (isset($crawlomatic_Main_Settings['default_dl_ext']) && $crawlomatic_Main_Settings['default_dl_ext'] != '') 
                        {
                            $def_extension = '.' . trim($crawlomatic_Main_Settings['default_dl_ext'], '.');
                        }
                        else
                        {
                            $def_extension = '.file';
                        }
                        if(empty($name_str))
                        {
                            $name_str = rand() . $def_extension;
                        }
                        elseif(strstr($name_str, '.') === false)
                        {
                            $name_str .= $def_extension;
                        }
                        $chunksize = 5 * (1024 * 1024);
                        $za_rand = rand();
                        $local_file_name = $dir_name . '/' . $za_rand . '-' . crawlomatic_sanitize_title_with_dots_and_dashes($name_str);
                        $remote_file_name = $dir_url . '/' . $za_rand . '-' . crawlomatic_sanitize_title_with_dots_and_dashes($name_str);
                        $fp = fopen ($local_file_name, 'a+');
                        if($fp === false)
                        {
                            crawlomatic_log_to_file('Failed to open local file: ' . $local_file_name);
                        }
                        else
                        {
                            $dl_failed = false;
                            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                                crawlomatic_log_to_file('Now copying remote file "' . $temp_download . '" locally to: "' . $local_file_name . '"');
                            }
                            if(strstr($temp_download, '&') !== false || $custom_cookies != '')
                            {
                                $dl_failed = true;
                            }
                            else
                            {
                                $handle = fopen($temp_download, 'rb');
                                if($handle === false)
                                {
                                    $dl_failed = true;
                                    crawlomatic_log_to_file('Failed to open remote file: ' . $temp_download);
                                }
                                else
                                {
                                    while (!feof($handle))
                                    { 
                                        $chunk_info = fread($handle, $chunksize);
                                        if($chunk_info === false)
                                        {
                                            $dl_failed = true;
                                            crawlomatic_log_to_file('Failed to read from file: ' . $temp_download);
                                        }
                                        else
                                        {
                                            $succ = fwrite($fp, $chunk_info);
                                            if($succ === false)
                                            {
                                                $dl_failed = true;
                                                crawlomatic_log_to_file('Failed to write to file: ' . $local_file_name);
                                            }
                                        }
                                    } 
                                    fclose($handle);
                                    $post['download_remote'] = $remote_file_name;
                                    $post['download_local'] = $local_file_name;
                                }
                            }
                            if($dl_failed)
                            {
                                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                                    crawlomatic_log_to_file('Using download method 2: "' . $temp_download);
                                }
                                $options    = array(
                                    CURLOPT_CUSTOMREQUEST => "GET",
                                    CURLOPT_POST => false,
                                    CURLOPT_RETURNTRANSFER => false,
                                    CURLOPT_FOLLOWLOCATION => true,
                                    CURLOPT_CONNECTTIMEOUT => 10,
                                    CURLOPT_TIMEOUT => 3600,
                                    CURLOPT_MAXREDIRS => 10,
                                    CURLOPT_SSL_VERIFYHOST => false,
                                    CURLOPT_SSL_VERIFYPEER => false,
                                    CURLOPT_REFERER => 'https://www.google.com/'
                                );
                                if($post_fields != '')
                                {
                                    $options[CURLOPT_CUSTOMREQUEST] = 'POST';
                                    $options[CURLOPT_POST] = true;
                                    $options[CURLOPT_POSTFIELDS] = $post_fields;
                                }
                                if($custom_user_agent != '')
                                {
                                    $options[CURLOPT_USERAGENT] = $custom_user_agent;
                                }
                                if ($use_proxy == '1' && isset($crawlomatic_Main_Settings['proxy_url']) && $crawlomatic_Main_Settings['proxy_url'] != '' && $crawlomatic_Main_Settings['proxy_url'] != 'disable' && $crawlomatic_Main_Settings['proxy_url'] != 'disabled') 
                                {
                                    $prx = explode(',', $crawlomatic_Main_Settings['proxy_url']);
                                    $randomness = array_rand($prx);
                                    $options[CURLOPT_PROXY] = trim($prx[$randomness]);
                                    if (isset($crawlomatic_Main_Settings['proxy_auth']) && $crawlomatic_Main_Settings['proxy_auth'] != '') 
                                    {
                                        $prx_auth = explode(',', $crawlomatic_Main_Settings['proxy_auth']);
                                        if(isset($prx_auth[$randomness]) && trim($prx_auth[$randomness]) != '')
                                        {
                                            $options[CURLOPT_PROXYUSERPWD] = trim($prx_auth[$randomness]);
                                        }
                                    }
                                }
                                $xxch = curl_init($temp_download);
                                if($xxch === FALSE)
                                {
                                    crawlomatic_log_to_file('Failed to open curl in file download!');
                                }
                                else
                                {
                                    if($custom_cookies != '')
                                    {
                                        $headers   = array();
                                        $headers[] = 'Cookie: ' . $custom_cookies;
                                        curl_setopt($xxch, CURLOPT_HTTPHEADER, $headers);
                                    }
                                    curl_setopt_array($xxch, $options);
                                    if($user_pass != '')
                                    {
                                        $har = explode(':', $user_pass);
                                        if(isset($har[1]))
                                        {
                                            curl_setopt($xxch, CURLOPT_USERPWD, $user_pass);
                                        }
                                    }
                                    curl_setopt($xxch, CURLOPT_FILE, $fp);
                                    $rezdata = curl_exec($xxch);
                                    if($rezdata === false)
                                    {
                                        crawlomatic_log_to_file('Failed to open remote file: ' . $temp_download);
                                    }
                                    else
                                    {
                                        $post['download_remote'] = $remote_file_name;
                                        $post['download_local'] = $local_file_name;
                                    }
                                    curl_close($xxch);
                                }
                            }
                            fclose($fp); 
                        }
                    }
                    else
                    {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                        {
                            crawlomatic_log_to_file('Downloadable file cannot be parsed as an URL: ' . $temp_download); 
                        }
                    }
                }
            }
        }
    }
    if($skip === false)
    {
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
        {
            crawlomatic_log_to_file('URL scraped: ' . $url); 
        }
        $posts[] = $post;
    }
    if(count($posts) >= $max)
    {
        $GLOBALS['crawl_done'] = true;
        return $posts;
    }
    if (isset($crawlomatic_Main_Settings['enable_robots']) && $crawlomatic_Main_Settings['enable_robots'] == 'on') 
    {
        if((isset($is_robots['nofollow']) && $is_robots['nofollow'] == true) || (isset($is_robots['none']) && $is_robots['none'] == true))
        {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                crawlomatic_log_to_file('Skipping URL, crawling disabled by robots nofollow tag: ' . $url);
            }
            $seed_type = 'disabled';
        }
    }
    if($depth - 1 > 0 && $seed_type != 'disabled')
    {
        $anchors = array();
        if($GLOBALS['seed'] === true)
        {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                crawlomatic_log_to_file('Crawling seed page for links: ' . $url . ' using: ' . $seed_type . ' = ' . $seed_expre); 
            }
            if($seed_expre != '' || $seed_type == 'sitemap' || $seed_type == 'rss')
            {
                if ($seed_type == 'xpath' || $seed_type == 'visual') 
                {
                    $dom_xpath = new DOMXpath($dom);
                    $elements = $dom_xpath->query($seed_expre);
                    if($elements != false)
                    {
                        foreach($elements as $el) {
                            if(isset($el->tagName) && $el->tagName === 'a')
                            {
                                $anchors[] = $el;
                            }
                            else
                            {
                                $ancs = $el->getElementsByTagName('a');
                                foreach($ancs as $as)
                                {
                                    $anchors[] = $as;
                                }
                            }
                        }
                    }
                }
                elseif ($seed_type == 'regex') 
                {
                    $matches     = array();
                    preg_match_all($seed_expre, $html_cont, $matches);
                    if(isset($matches[0]))
                    {
                        foreach ($matches[0] as $match) {
                            $el = $dom->createElement('a', 'link');
                            $el->setAttribute('href', trim($match));
                            $anchors[] = $el;
                            $el = '';
                        }
                    }
					else
					{
						if(crawlomatic_isRegularExpression($seed_expre) === false)
						{
							crawlomatic_log_to_file('Incorrect regex entered: ' . $seed_expre);
						}
					}
                }
                elseif($seed_type == 'regex2')
                {
                    $matches     = array();
                    preg_match_all($seed_expre, $html_cont, $matches);
                    if(isset($matches[1]))
                    {
                        for ($i = 1; $i < count($matches); $i++) 
                        {
                            foreach ($matches[$i] as $match) {
                                $el = $dom->createElement('a', 'link');
                                $el->setAttribute('href', trim($match));
                                $anchors[] = $el;
                                $el = '';
                            }
                        }
                    }
					else
					{
						if(crawlomatic_isRegularExpression($seed_expre) === false)
						{
							crawlomatic_log_to_file('Incorrect regex entered: ' . $seed_expre);
						}
					}
                }
                else
                {
                    if($seed_type == 'id')
                    {
                        $dom_xpath = new DOMXpath($dom);
                        $elements = $dom_xpath->query('//*[@'.$seed_type.'="'.trim($seed_expre).'"]');
                        if($elements != false)
                        {
                            foreach($elements as $el) {
                                if(isset($el->tagName) && $el->tagName === 'a')
                                {
                                    $anchors[] = $el;
                                }
                                else
                                {
                                    $ancs = $el->getElementsByTagName('a');
                                    foreach($ancs as $as)
                                    {
                                        $anchors[] = $as;
                                    }
                                }
                            }
                        }
                    }
                    elseif($seed_type == 'class')
                    {
                        $dom_xpath = new DOMXpath($dom);
                        $elements = $dom_xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " '.trim($seed_expre).' ")]');
                        if($elements != false)
                        {
                            foreach($elements as $el) {
                                if(isset($el->tagName) && $el->tagName === 'a')
                                {
                                    $anchors[] = $el;
                                }
                                else
                                {
                                    $ancs = $el->getElementsByTagName('a');
                                    foreach($ancs as $as)
                                    {
                                        $anchors[] = $as;
                                    }
                                }
                            }
                        }
                    }
                    elseif($seed_type == 'rss')
                    {
						try 
                        {
							$ulrs = crawlomatic_get_rss_feed_links($html_cont, $url);
							foreach ($ulrs as $idxrss => $xxurl) {
								if(trim($seed_expre) == '*' || trim($seed_expre) == '')
								{
									$el = $dom->createElement('a', 'link');
									$el->setAttribute('href', trim($xxurl));
									$anchors[] = $el;
									$el = '';
								}
								else
								{
									if(preg_match(trim($seed_expre), $xxurl))
									{
										$el = $dom->createElement('a', 'link');
										$el->setAttribute('href', trim($xxurl));
										$anchors[] = $el;
										$el = '';
									}
								}
							}
						} catch (SitemapParserException $e) {
							crawlomatic_log_to_file('Failed to parse RSS Feed: ' . $url . ' - error: ' . $e->getMessage());
						}
                    }
                    elseif($seed_type == 'sitemap')
                    {
						try {
							$parser = new SitemapParser();
							$parser->parseRecursive($url, $html_cont, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass);
							foreach ($parser->getURLs() as $xxurl => $xxtags) {
								if(trim($seed_expre) == '*' || trim($seed_expre) == '')
								{
									$el = $dom->createElement('a', 'link');
									$el->setAttribute('href', trim($xxurl));
									$anchors[] = $el;
									$el = '';
								}
								else
								{
									if(preg_match(trim($seed_expre), $xxurl))
									{
										$el = $dom->createElement('a', 'link');
										$el->setAttribute('href', trim($xxurl));
										$anchors[] = $el;
										$el = '';
									}
								}
							}
						} catch (SitemapParserException $e) {
							crawlomatic_log_to_file('Failed to parse sitemap: ' . $url . ' - error: ' . $e->getMessage());
						}
                    }
                    elseif($seed_type == 'auto')
                    {
                        $max_links = -1;
                        if (isset($crawlomatic_Main_Settings['max_auto_links']) && $crawlomatic_Main_Settings['max_auto_links'] != '')
                        {
                            $max_links = intval($crawlomatic_Main_Settings['max_auto_links']);
                        }
                        $za_link_cnt = 0;

                        $anchors = $dom->getElementsByTagName('a');
                        for ($i = $anchors->length; --$i >= 0; ) 
                        {
                            if($max_links != -1 && $za_link_cnt >= $max_links)
                            {
                                if($el->parentNode != null)
                                {
                                    $el->parentNode->removeChild($el);
                                }
                                continue;
                            }
                            else
                            {
                                $za_link_cnt++;
                            }

                            $el = $anchors->item($i);
                            $href = $el->getAttribute('href');
                            $href = crawlomatic_fix_single_link($href, $url);
                            if($seed_expre != '*' && stristr($href, $seed_expre) === false)
                            {
                                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                                {
                                    crawlomatic_log_to_file('Removing URL ' . $href . ' from results because it did not match pattern: ' . $seed_expre);
                                }
                                if($el->parentNode != null)
                                {
                                    $el->parentNode->removeChild($el);
                                }
                                continue;
                            }
                            if($no_external == '1')
                            {
                                if($href != '' && crawlomatic_isExternal($href, $url) != 0)
                                {
                                    if($el->parentNode != null)
                                    {
                                        $el->parentNode->removeChild($el);
                                    }
                                    continue;
                                }
                            }
                        }
                    }
                    elseif($seed_type != '')
                    {
                        $dom_xpath = new DOMXpath($dom);
                        $elements = $dom_xpath->query('//*[@'.$seed_type.'="'.trim($seed_expre).'"]');
                        if($elements != false)
                        {
                            foreach($elements as $el) {
                                if(isset($el->tagName) && $el->tagName === 'a')
                                {
                                    $anchors[] = $el;
                                }
                                else
                                {
                                    $ancs = $el->getElementsByTagName('a');
                                    foreach($ancs as $as)
                                    {
                                        $anchors[] = $as;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            else
            {
                $anchors = $dom->getElementsByTagName('a');
            }
            if($max_crawl != '' && is_numeric($max_crawl))
            {
                crawlomatic_log_to_file('Limiting crawled URL results count from ' . count($anchors) . ' to ' . $max_crawl);
                $anchors = array_slice($anchors, 0, $max_crawl);
            }
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                if(count($anchors) > 0)
                {
                    crawlomatic_log_to_file('Links returned: ');
                    $cnter = 0;
                    foreach($anchors as $element)
                    {
                        crawlomatic_log_to_file($cnter . '. ' . $element->getAttribute('href'));
                        $cnter++;
                    }
                }
            }
        }
        else
        {
            if ($crawled_type != 'disabled')
            {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                {
                    crawlomatic_log_to_file('Crawling leaf page for links: ' . $url); 
                }
                if($crawled_expre != '')
                {
                    if ($crawled_type == 'xpath' || $crawled_type == 'visual') {
                        $dom_xpath = new DOMXpath($dom);
                        $elements = $dom_xpath->query($crawled_expre);
                        if($elements != false)
                        {
                            foreach($elements as $el) {
                                if(isset($el->tagName) && $el->tagName === 'a')
                                {
                                    $anchors[] = $el;
                                }
                                else
                                {
                                    $ancs = $el->getElementsByTagName('a');
                                    foreach($ancs as $as)
                                    {
                                        $anchors[] = $as;
                                    }
                                }
                            }
                        }
                    }
                    elseif($crawled_type == 'regex')
                    {
                        $matches     = array();
                        preg_match_all($crawled_expre, $html_cont, $matches);
                        if(isset($matches[0]))
                        {
                            foreach ($matches[0] as $match) {
                                $el = $dom->createElement('a', 'link');
                                $el->setAttribute('href', trim($match));
                                $anchors[] = $el;
                                $el = '';
                            }
                        }
						else
						{
							if(crawlomatic_isRegularExpression($crawled_expre) === false)
							{
								crawlomatic_log_to_file('Incorrect crawled_expre entered: ' . $crawled_expre);
							}
						}
                    }
                    elseif($crawled_type == 'regex2')
                    {
                        $matches     = array();
                        preg_match_all($crawled_expre, $html_cont, $matches);
                        if(isset($matches[1]))
                        {
                            for ($i = 1; $i < count($matches); $i++) 
                            {
                                foreach ($matches[$i] as $match) {
                                    $el = $dom->createElement('a', 'link');
                                    $el->setAttribute('href', trim($match));
                                    $anchors[] = $el;
                                    $el = '';
                                }
                            }
                        }
						else
						{
							if(crawlomatic_isRegularExpression($crawled_expre) === false)
							{
								crawlomatic_log_to_file('Incorrect crawled_expre entered: ' . $crawled_expre);
							}
						}
                    }
                    else
                    {
                        if($crawled_type == 'class')
                        {
                            $dom_xpath = new DOMXpath($dom);
                            $elements = $dom_xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " '.trim($crawled_expre).' ")]');
                            if($elements != false)
                            {
                                foreach($elements as $el) {
                                    if(isset($el->tagName) && $el->tagName === 'a')
                                    {
                                        $anchors[] = $el;
                                    }
                                    else
                                    {
                                        $ancs = $el->getElementsByTagName('a');
                                        foreach($ancs as $as)
                                        {
                                            $anchors[] = $as;
                                        }
                                    }
                                }
                            }
                        }
                        elseif($crawled_type == 'auto')
                        {
                            $max_links = -1;
                            if (isset($crawlomatic_Main_Settings['max_auto_links']) && $crawlomatic_Main_Settings['max_auto_links'] != '')
                            {
                                $max_links = intval($crawlomatic_Main_Settings['max_auto_links']);
                            }
                            $za_link_cnt = 0;

                            $canchors = $dom->getElementsByTagName('a');
                            for ($i = $canchors->length; --$i >= 0; ) 
                            {
                                if($max_links != -1 && $za_link_cnt >= $max_links)
                                {
                                    if($el->parentNode != null)
                                    {
                                        $el->parentNode->removeChild($el);
                                    }
                                    continue;
                                }
                                else
                                {
                                    $za_link_cnt++;
                                }

                                $el = $canchors->item($i);
                                $href_val = $el->getAttribute('href');
                                $href_val = crawlomatic_fix_single_link($href_val, $url);
                                if(trim($crawled_expre) != '*' && stristr($href_val, trim($crawled_expre)) === false)
                                {
                                    if($el->parentNode != null)
                                    {
                                        $el->parentNode->removeChild($el);
                                    }
                                    continue;
                                }
                                if($no_external == '1')
                                {
                                    if($href_val != '' && crawlomatic_isExternal($href_val, $url) != 0)
                                    {
                                        if($el->parentNode != null)
                                        {
                                            $el->parentNode->removeChild($el);
                                        }
                                        continue;
                                    }
                                }
                                if(isset($el->tagName) && $el->tagName === 'a')
                                {
                                    $anchors[] = $el;
                                }
                                else
                                {
                                    $ancs = $el->getElementsByTagName('a');
                                    foreach($ancs as $as)
                                    {
                                        $anchors[] = $as;
                                    }
                                }
                            }
                        }
                        elseif($crawled_type != '')
                        {
                            $dom_xpath = new DOMXpath($dom);
                            $elements = $dom_xpath->query('//*[@'.$crawled_type.'="'.trim($crawled_expre).'"]');
                            if($elements != false)
                            {
                                foreach($elements as $el) {
                                    if(isset($el->tagName) && $el->tagName === 'a')
                                    {
                                        $anchors[] = $el;
                                    }
                                    else
                                    {
                                        $ancs = $el->getElementsByTagName('a');
                                        foreach($ancs as $as)
                                        {
                                            $anchors[] = $as;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                else
                {
                    $anchors = $dom->getElementsByTagName('a');
                }
                if($max_crawl != '' && is_numeric($max_crawl))
                {
                    crawlomatic_log_to_file('Limiting crawled URL results count from ' . count($anchors) . ' to ' . $max_crawl);
                    $anchors = array_slice($anchors, 0, $max_crawl);
                }
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                {
                    if(count($anchors) > 0)
                    {
                        crawlomatic_log_to_file('Links returned: ');
                        $cnter = 0;
                        foreach($anchors as $element)
                        {
                            crawlomatic_log_to_file($cnter . '. ' . $element->getAttribute('href'));
                            $cnter++;
                        }
                    }
                }
            }
        }
        $GLOBALS['seed'] = false;
        if($only_class != '')
        {
            $only_c = explode(',', $only_class);
        }
        else
        {
            $only_c = array();
        }
        if($only_id != '')
        {
            $only_i = explode(',', $only_id);
        }
        else
        {
            $only_i = array();
        }
        if($reverse_crawl == '1')
        {
            $anchors = array_reverse($anchors);
        }
        foreach ($anchors as $element) {
            if($GLOBALS['crawl_done'] === true)
            {
                break;
            }
            $tfound = false;
            if($only_class != '')
            {
                $class_name = $element->getAttribute('class'); 
                foreach($only_c as $oc)
                {
                    $oc = trim($oc);
                    if($oc == $class_name)
                    {
                        $tfound = true;
                    }
                }
            }
            if($only_id != '')
            {
                $id_name = $element->getAttribute('id');
                foreach($only_i as $oi)
                {
                    $oi = trim($oi);
                    if($oi == $id_name)
                    {
                        $tfound = true;
                    }
                }
            }
            if(($only_id != '' || $only_class != '') && $tfound === false)
            {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                {
                    crawlomatic_log_to_file('Skipping URL (required class/id not found on it): ' . $element->getAttribute('href'));
                }
                continue;
            }
            $href = $element->getAttribute('href');
            if($href == '#' || $href === rtrim($url, '/') . '/#')
            {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                {
                    crawlomatic_log_to_file('Skipping duplicate URL: ' . $href);
                }
                continue;
            }
            $href = crawlomatic_fix_single_link($href, $url);
            if($href == '')
            {
                continue;
            }
            if($no_external == '1')
            {
                if(crawlomatic_isExternal($href, $url) != 0)
                {
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
                    {
                        crawlomatic_log_to_file('Skipping external link: ' . $href . ' (Base URL: ' . $url . ')');
                    }
                    continue;
                }
            }
            if($href != $url && !isset($seen[$href]))
            {
                crawlomatic_crawl_page($href, $max, $skip_og, $skip_post_content, $no_external, $required_words, $banned_words, $type, $getname, $title_type, $title_getname, $image_type, $image_getname, $date_type, $date_getname, $cat_type, $cat_getname, $depth - 1, $custom_cookies, $only_class, $only_id, $no_source, $seed_type, $seed_expre, $crawled_type, $crawled_expre, $paged_crawl_str, $paged_crawl_type, $max_paged_depth, $custom_user_agent, $posted_items, $update_ex, $cat_sep, false, $seed_pag_type, $seed_pag_expre, $price_type, $price_expre, false, $use_proxy, $use_phantom, $no_dupl_crawl, $custom_crawling_expre, $user_pass, $crawl_exclude, $crawl_title_exclude, $price_sep, $encoding, $strip_comma, $reverse_crawl, $lazy_tag, $tag_type, $tag_expre, $tag_sep, $phantom_wait, $param, $continue_search, $author_type, $author_expre, $no_match_query, '', $request_delay, $require_one, $max_crawl, $check_only_content, $scripter, $local_storage, $download_type, $download_expre, $gallery_type, $gallery_expre);
            }
        }
    }
    else
    {
        $GLOBALS['seed'] = false;
    }
    if($root_page === true && count($posts) < $max && $seed_pag_type !== 'disabled' && $seed_type !== 'disabled')
    {
        $xanchors = array();
        if($seed_pag_expre != '' && $seed_pag_type != '' && $seed_pag_type != '1')
        {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
            {
                crawlomatic_log_to_file('Crawling seed pagination for links: ' . $url); 
            }
            if ($seed_pag_type == 'xpath' || $seed_pag_type == 'visual') {
                $dom_xpath = new DOMXpath($dom);
                $elements = $dom_xpath->query($seed_pag_expre);
                if($elements != false)
                {
                    foreach($elements as $el) {
                        if(isset($el->tagName) && $el->tagName === 'a')
                        {
                            $href_val = $el->getAttribute('href');
                            $href_val = crawlomatic_fix_single_link($href_val, $url);
                            if($href_val != '')
                            {
                                if(!isset($already_paginated[$href_val]))
                                {
                                    $already_paginated[$href_val] = '1';
                                    $xanchors[] = $el;
                                }
                            }
                            else
                            {
                                if(!in_array($el, $already_paginated))
                                {
                                    $already_paginated[] = $el;
                                }
                            }
                        }
                        else
                        {
                            $ancs = $el->getElementsByTagName('a');
                            foreach($ancs as $as)
                            {
                                $href_val = $as->getAttribute('href');
                                $href_val = crawlomatic_fix_single_link($href_val, $url);
                                if($href_val != '')
                                {
                                    if(!isset($already_paginated[$href_val]))
                                    {
                                        $already_paginated[$href_val] = '1';
                                        $xanchors[] = $as;
                                    }
                                }
                                else
                                {
                                    if(!in_array($as, $already_paginated))
                                    {
                                        $already_paginated[] = $as;
                                    }
                                }
                            }
                        }
                    }
                }
            }
            elseif ($seed_pag_type == 'regex') {
                $matches     = array();
                preg_match_all($seed_pag_expre, $html_cont, $matches);
                if(isset($matches[0]))
                {
                    foreach ($matches[0] as $match) {
                        $el = $dom->createElement('a', 'link');
                        $href_val = crawlomatic_fix_single_link(trim($match), $url);
                        if($href_val != '')
                        {
                            $el->setAttribute('href', trim($href_val));
                            if(!isset($already_paginated[$href_val]))
                            {
                                $already_paginated[$href_val] = '1';
                                $xanchors[] = $el;
                            }
                        }
                        else
                        {
                            $el->setAttribute('href', trim($match));
                            if(!in_array($el, $already_paginated))
                            {
                                $already_paginated[] = $el;
                            }
                        }
                        $el = '';
                    }
                }
				else
				{
					if(crawlomatic_isRegularExpression($seed_pag_expre) === false)
					{
						crawlomatic_log_to_file('Incorrect seed_pag_expre entered: ' . $seed_pag_expre);
					}
				}
            }
            elseif ($seed_pag_type == 'regex2') {
                $matches     = array();
                preg_match_all($seed_pag_expre, $html_cont, $matches);
                if(isset($matches[1]))
                {
                    for ($i = 1; $i < count($matches); $i++)
                    {
                        foreach ($matches[$i] as $match) 
                        {
                            $el = $dom->createElement('a', 'link');
                            $href_val = crawlomatic_fix_single_link(trim($match), $url);
                            if($href_val != '')
                            {
                                $el->setAttribute('href', trim($href_val));
                                if(!isset($already_paginated[$href_val]))
                                {
                                    $already_paginated[$href_val] = '1';
                                    $xanchors[] = $el;
                                }
                            }
                            else
                            {
                                $el->setAttribute('href', trim($match));
                                if(!in_array($el, $already_paginated))
                                {
                                    $already_paginated[] = $el;
                                }
                            }
                            $el = '';
                        }
                    }
                }
				else
				{
					if(crawlomatic_isRegularExpression($seed_pag_expre) === false)
					{
						crawlomatic_log_to_file('Incorrect seed_pag_expre entered: ' . $seed_pag_expre);
					}
				}
            }
            else
            {
                if($seed_pag_type == 'class')
                {
                    $dom_xpath = new DOMXpath($dom);
                    $elements = $dom_xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " '.trim($seed_pag_expre).' ")]');
                    if($elements != false)
                    {
                        foreach($elements as $el) {
                            if(isset($el->tagName) && $el->tagName === 'a')
                            {
                                $href_val = $el->getAttribute('href');
                                $href_val = crawlomatic_fix_single_link($href_val, $url);
                                if($href_val != '')
                                {
                                    if(!isset($already_paginated[$href_val]))
                                    {
                                        $already_paginated[$href_val] = '1';
                                        $xanchors[] = $el;
                                    }
                                }
                                else
                                {
                                    if(!in_array($el, $already_paginated))
                                    {
                                        $already_paginated[] = $el;
                                    }
                                }
                            }
                            else
                            {
                                $ancs = $el->getElementsByTagName('a');
                                foreach($ancs as $as)
                                {
                                    $href_val = $as->getAttribute('href');
                                    $href_val = crawlomatic_fix_single_link($href_val, $url);
                                    if($href_val != '')
                                    {
                                        if(!isset($already_paginated[$href_val]))
                                        {
                                            $already_paginated[$href_val] = '1';
                                            $xanchors[] = $as;
                                        }
                                    }
                                    else
                                    {
                                        if(!in_array($as, $already_paginated))
                                        {
                                            $already_paginated[] = $as;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
                elseif($seed_pag_type == 'auto')
                {
                    $max_links = -1;
                    if (isset($crawlomatic_Main_Settings['max_auto_links']) && $crawlomatic_Main_Settings['max_auto_links'] != '')
                    {
                        $max_links = intval($crawlomatic_Main_Settings['max_auto_links']);
                    }
                    $za_link_cnt = 0;
                    
                    $canchors = $dom->getElementsByTagName('a');
                    for ($i = $canchors->length; --$i >= 0; ) 
                    {
                        if($max_links != -1 && $za_link_cnt >= $max_links)
                        {
                            if($el->parentNode != null)
                            {
                                $el->parentNode->removeChild($el);
                            }
                            continue;
                        }
                        else
                        {
                            $za_link_cnt++;
                        }

                        $el = $canchors->item($i);
                        $href_val = $el->getAttribute('href');
                        $href_val = crawlomatic_fix_single_link($href_val, $url);
                        if(trim($seed_pag_expre) != '*' && stristr($href_val, trim($seed_pag_expre)) === false)
                        {
                            if($el->parentNode != null)
                            {
                                $el->parentNode->removeChild($el);
                            }
                            continue;
                        }
                        if($href_val != '')
                        {
                            if($no_external == '1')
                            {
                                if(crawlomatic_isExternal($href_val, $url) != 0)
                                {
                                    if($el->parentNode != null)
                                    {
                                        $el->parentNode->removeChild($el);
                                    }
                                    continue;
                                }
                            }
                            if(!isset($already_paginated[$href_val]))
                            {
                                $already_paginated[$href_val] = '1';
                                $xanchors[] = $el;
                            }
                        }
                        else
                        {
                            if(!in_array($el, $already_paginated))
                            {
                                $already_paginated[] = $el;
                            }
                        }
                    }
                }
                elseif($seed_pag_type != '')
                {
                    $dom_xpath = new DOMXpath($dom);
                    $elements = $dom_xpath->query('//*[@'.$seed_pag_type.'="'.trim($seed_pag_expre).'"]');
                    if($elements != false)
                    {
                        foreach($elements as $el) {
                            if(isset($el->tagName) && $el->tagName === 'a')
                            {
                                $href_val = $el->getAttribute('href');
                                $href_val = crawlomatic_fix_single_link($href_val, $url);
                                if($href_val != '')
                                {
                                    if(!isset($already_paginated[$href_val]))
                                    {
                                        $already_paginated[$href_val] = '1';
                                        $xanchors[] = $el;
                                    }
                                }
                                else
                                {
                                    if(!in_array($el, $already_paginated))
                                    {
                                        $already_paginated[] = $el;
                                    }
                                }
                            }
                            else
                            {
                                $ancs = $el->getElementsByTagName('a');
                                foreach($ancs as $as)
                                {
                                    $href_val = $as->getAttribute('href');
                                    $href_val = crawlomatic_fix_single_link($href_val, $url);
                                    if($href_val != '')
                                    {
                                        if(!isset($already_paginated[$href_val]))
                                        {
                                            $already_paginated[$href_val] = '1';
                                            $xanchors[] = $as;
                                        }
                                    }
                                    else
                                    {
                                        if(!in_array($as, $already_paginated))
                                        {
                                            $already_paginated[] = $as;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        elseif($seed_pag_type == 'auto')
        {
            $max_links = -1;
            if (isset($crawlomatic_Main_Settings['max_auto_links']) && $crawlomatic_Main_Settings['max_auto_links'] != '')
            {
                $max_links = intval($crawlomatic_Main_Settings['max_auto_links']);
            }
            $za_link_cnt = 0;

            $canchors = $dom->getElementsByTagName('a');
            for ($i = $canchors->length; --$i >= 0; ) 
            {
                if($max_links != -1 && $za_link_cnt >= $max_links)
                {
                    if($el->parentNode != null)
                    {
                        $el->parentNode->removeChild($el);
                    }
                    continue;
                }
                else
                {
                    $za_link_cnt++;
                }
                
                $el = $canchors->item($i);
                $href_val = $el->getAttribute('href');
                $href_val = crawlomatic_fix_single_link($href_val, $url);
                if($href_val != '')
                {
                    if($no_external == '1')
                    {
                        if(crawlomatic_isExternal($href_val, $url) != 0)
                        {
                            if($el->parentNode != null)
                            {
                                $el->parentNode->removeChild($el);
                            }
                            continue;
                        }
                    }
                    if(!isset($already_paginated[$href_val]))
                    {
                        $already_paginated[$href_val] = '1';
                        $xanchors[] = $el;
                    }
                }
                else
                {
                    if(!in_array($el, $already_paginated))
                    {
                        $already_paginated[] = $el;
                    }
                }
            }
        }
        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) 
        {
            if($seed_pag_expre != '' && $seed_pag_type != '' && $seed_pag_type != '1')
            {
                if(count($xanchors) > 0)
                {
                    crawlomatic_log_to_file('Pagination links returned:');
                    $cnter = 0;
                    foreach($xanchors as $ancas)
                    {
                        crawlomatic_log_to_file($cnter . '. ' . $ancas->getAttribute('href'));
                        $cnter++;
                    }
                }
                else
                {
                    crawlomatic_log_to_file('No pagination links returned.');
                }
            }
        }
        $skip_posts_temp = get_option('crawlomatic_continue_search', array());
        foreach($xanchors as $ancas)
        {
            $ancas = $ancas->getAttribute('href');
            $ancas = crawlomatic_fix_single_link($ancas, $url);
            if($ancas == '')
            {
                continue;
            }
            if($no_external == '1')
            {
                if(crawlomatic_isExternal($ancas, $url) != 0)
                {
                    continue;
                }
            }
            if($continue_search == '1')
            {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                    crawlomatic_log_to_file('Saving URL for next time crawling (rule ID ' . $param . '): ' . $ancas . '!');
                }
                $skip_posts_temp[$param] = $ancas;
                update_option('crawlomatic_continue_search', $skip_posts_temp);
            }
            if(count($posts) >= $max)
            {
                break;
            }
            $GLOBALS['seed'] = true;
            $GLOBALS['crawl_done'] = false;
            if(isset($seen[$ancas]))
            {
                unset($seen[$ancas]);
            }
            if($ancas != $url)
            {
                crawlomatic_crawl_page($ancas, $max, $skip_og, $skip_post_content, $no_external, $required_words, $banned_words, $type, $getname, $title_type, $title_getname, $image_type, $image_getname, $date_type, $date_getname, $cat_type, $cat_getname, $depth, $custom_cookies, $only_class, $only_id, $no_source, $seed_type, $seed_expre, $crawled_type, $crawled_expre, $paged_crawl_str, $paged_crawl_type, $max_paged_depth, $custom_user_agent, $posted_items, $update_ex, $cat_sep, true, $seed_pag_type, $seed_pag_expre, $price_type, $price_expre, false, $use_proxy, $use_phantom, $no_dupl_crawl, $custom_crawling_expre, $user_pass, $crawl_exclude, $crawl_title_exclude, $price_sep, $encoding, $strip_comma, $reverse_crawl, $lazy_tag, $tag_type, $tag_expre, $tag_sep, $phantom_wait, $param, $continue_search, $author_type, $author_expre, $no_match_query, $post_fields, $request_delay, $require_one, $max_crawl, $check_only_content, $scripter, $local_storage, $download_type, $download_expre, $gallery_type, $gallery_expre);
            }
        }
    }
    return $posts;
}
function crawlomatic_sanitize_title_with_dots_and_dashes($title) {
    $title = strip_tags($title);
    $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
    $title = str_replace('%', '', $title);
    $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);
    $title = remove_accents($title);
    if (seems_utf8($title)) 
    {
        if (function_exists('mb_strtolower')) {
                $title = mb_strtolower($title, 'UTF-8');
        }
        $title = utf8_uri_encode($title);
    }
    $title = strtolower($title);
    $title = preg_replace('/&.+?;/', '', $title); 
    $title = preg_replace('/[^%a-z0-9 ._-]/', '', $title);
    $title = preg_replace('/\s+/', '-', $title);
    $title = preg_replace('|-+|', '-', $title);
    $title = trim($title, '-');
    $title = str_replace('-.-', '.', $title);
    $title = str_replace('-.', '.', $title);
    $title = str_replace('.-', '.', $title);
    $title = preg_replace('|([^.])\.$|', '$1', $title);
    $title = trim($title, '-');
    return $title;
}
function crawlomatic_get_rss_feed_links($content, $url)
{
    $feed = array();
    $x = new SimpleXmlElement($content);
    if(isset($x->channel->item))
    {
        foreach($x->channel->item as $entry) 
        {
            $feed[] = $entry->link;
        }
    }
    elseif(isset($x->Channel->item))
    {
        foreach($x->Channel->item as $entry) 
        {
            $feed[] = $entry->link;
        }
    }
    elseif(isset($x->item))
    {
        foreach($x->item as $entry) 
        {
            $feed[] = $entry->link;
        }
    }
    elseif(isset($x->entry))
    {
        foreach($x->entry as $entry) 
        {
            $feed[] = $entry->link;
        }
    }
    else
    {
        crawlomatic_log_to_file('Failed to parse RSS URL: ' . $url);
    }
    return $feed;
}

function crawlomatic_giveHost($host_with_subdomain) {
    $array = explode(".", $host_with_subdomain);
    return (array_key_exists(count($array) - 2, $array) ? $array[count($array) - 2] : "") . "." . $array[count($array) - 1];
}
function crawlomatic_isExternal($href, $base)
{
    if(empty($href) || empty($base))
    {
        return 1;
    }
    $components = parse_url($href); 
    $comp_base = parse_url($base);
    if(!isset($components['host']) || !isset($comp_base['host']))
    {
        if(stristr($href, $base) !== false)
        {
            return 0;
        }
        return 1;
    }
    return strcasecmp(crawlomatic_giveHost($components['host']), crawlomatic_giveHost($comp_base['host']));
}

function crawlomatic_get_paged_content($url, $html_cont, $type, $getname, $paged_crawl_type, $paged_crawl_str, $custom_cookies, $max, $custom_user_agent, $use_proxy, $use_phantom, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage)
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    static $seen = array();
    $extract = '';
    $anchors = array();
    $seen[$url] = true;
    $domz = new DOMDocument('1.0');
    $internalErrors = libxml_use_internal_errors(true);
    $domz->loadHTML('<?xml encoding="utf-8" ?>' . $html_cont);
    libxml_use_internal_errors($internalErrors);
    if($paged_crawl_str != '')
    {
        if ($paged_crawl_type == 'xpath' || $paged_crawl_type == 'visual') {
            $dom_xpath = new DOMXpath($domz);
            $elements = $dom_xpath->query($paged_crawl_str);
            if($elements != false)
            {
                foreach($elements as $el) {
                    if(isset($el->tagName) && $el->tagName === 'a')
                    {
                        $anchors[] = $el;
                    }
                    else
                    {
                        $ancs = $el->getElementsByTagName('a');
                        foreach($ancs as $as)
                        {
                            $anchors[] = $as;
                        }
                    }
                }
            }
        }
        elseif ($paged_crawl_type == 'regex') {
            $matches     = array();
            preg_match_all($paged_crawl_str, $html_cont, $matches);
            if(isset($matches[0]))
            {
                 foreach ($matches[0] as $match) {
                    $el = $domz->createElement('a', 'link');
                    $el->setAttribute('href', trim($match));
                    $anchors[] = $el;
                    $el = '';
                }
            }
			else
			{
				if(crawlomatic_isRegularExpression($paged_crawl_str) === false)
				{
					crawlomatic_log_to_file('Incorrect paged_crawl_str entered: ' . $seed_pag_expre);
				}
			}
        }
        elseif ($paged_crawl_type == 'regex2') {
            $matches     = array();
            preg_match_all($paged_crawl_str, $html_cont, $matches);
            if(isset($matches[1]))
            {
                for ($i = 1; $i < count($matches); $i++)
                {
                    foreach ($matches[$i] as $match) {
                        $el = $domz->createElement('a', 'link');
                        $el->setAttribute('href', trim($match));
                        $anchors[] = $el;
                        $el = '';
                    }
                }
            }
			else
			{
				if(crawlomatic_isRegularExpression($paged_crawl_str) === false)
				{
					crawlomatic_log_to_file('Incorrect paged_crawl_str entered: ' . $seed_pag_expre);
				}
			}
        }
        else
        {
            if($paged_crawl_type == 'class')
            {
                $dom_xpath = new DOMXpath($domz);
                $elements = $dom_xpath->query('//*[contains(concat(" ", normalize-space(@class), " "), " '.trim($paged_crawl_str).' ")]');
                if($elements != false)
                {
                    foreach($elements as $el) {
                        if(isset($el->tagName) && $el->tagName === 'a')
                        {
                            $anchors[] = $el;
                        }
                        else
                        {
                            $ancs = $el->getElementsByTagName('a');
                            foreach($ancs as $as)
                            {
                                $anchors[] = $as;
                            }
                        }
                    }
                }
            }
            elseif($paged_crawl_type == 'auto')
            {
                $max_links = -1;
                if (isset($crawlomatic_Main_Settings['max_auto_links']) && $crawlomatic_Main_Settings['max_auto_links'] != '')
                {
                    $max_links = intval($crawlomatic_Main_Settings['max_auto_links']);
                }
                $za_link_cnt = 0;

                $canchors = $domz->getElementsByTagName('a');
                for ($i = $canchors->length; --$i >= 0; ) 
                {
                    if($max_links != -1 && $za_link_cnt >= $max_links)
                    {
                        if($el->parentNode != null)
                        {
                            $el->parentNode->removeChild($el);
                        }
                        continue;
                    }
                    else
                    {
                        $za_link_cnt++;
                    }
                    
                    $el = $canchors->item($i);
                    $href_val = $el->getAttribute('href');
                    $href_val = crawlomatic_fix_single_link($href_val, $url);
                    if(trim($paged_crawl_str) != '*' && stristr($href_val, trim($paged_crawl_str)) === false)
                    {
                        if($el->parentNode != null)
                        {
                            $el->parentNode->removeChild($el);
                        }
                        continue;
                    }
                    if($href_val != '' && crawlomatic_isExternal($href_val, $url) != 0)
                    {
                        if($el->parentNode != null)
                        {
                            $el->parentNode->removeChild($el);
                        }
                        continue;
                    }
                    if(isset($el->tagName) && $el->tagName === 'a')
                    {
                        $anchors[] = $el;
                    }
                    else
                    {
                        $ancs = $el->getElementsByTagName('a');
                        foreach($ancs as $as)
                        {
                            $anchors[] = $as;
                        }
                    }
                }
            }
            elseif($paged_crawl_type != '')
            {
                $dom_xpath = new DOMXpath($domz);
                $elements = $dom_xpath->query('//*[@'.$paged_crawl_type.'="'.trim($paged_crawl_str).'"]');
                if($elements != false)
                {
                    foreach($elements as $el) {
                        if(isset($el->tagName) && $el->tagName === 'a')
                        {
                            $anchors[] = $el;
                        }
                        else
                        {
                            $ancs = $el->getElementsByTagName('a');
                            foreach($ancs as $as)
                            {
                                $anchors[] = $as;
                            }
                        }
                    }
                }
            }
        }
    }
    else
    {
        $anchors = $domz->getElementsByTagName('a');
    }
    
    foreach($anchors as $a)
    {
        $href = $a->getAttribute('href');
        $href = crawlomatic_fix_single_link($href, $url);
        if($href == '')
        {
            continue;
        }
        if (isset($seen[$href])) {
            continue;
        }
        if(count($seen) > $max)
        {
            return $extract;
        }
        $got_phantom = false;
        $html_cont = false;
        if($use_phantom == '1')
        {
            $html_cont = crawlomatic_get_page_PhantomJS($href, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
            if($html_cont !== false)
            {
                $got_phantom = true;
            }
        }
        elseif($use_phantom == '2')
        {
            $html_cont = crawlomatic_get_page_Puppeteer($href, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
            if($html_cont !== false)
            {
                $got_phantom = true;
            }
        }
        elseif($use_phantom == '3')
        {
            $html_cont = crawlomatic_get_page_Tor($href, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
            if($html_cont !== false)
            {
                $got_phantom = true;
            }
        }
        elseif($use_phantom == '4')
        {
            $html_cont = crawlomatic_get_page_PuppeteerAPI($href, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
            if($html_cont !== false)
            {
                $got_phantom = true;
            }
        }
        elseif($use_phantom == '5')
        {
            $html_cont = crawlomatic_get_page_TorAPI($href, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
            if($html_cont !== false)
            {
                $got_phantom = true;
            }
        }
        elseif($use_phantom == '6')
        {
            $html_cont = crawlomatic_get_page_PhantomJSAPI($href, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
            if($html_cont !== false)
            {
                $got_phantom = true;
            }
        }
        if($got_phantom === false)
        {
            $html_cont = crawlomatic_get_web_page($href, $custom_cookies, $custom_user_agent, $use_proxy, $user_pass, '', '', $request_delay);
        }
        if($html_cont === false)
        {
            continue;
        }
        if($getname == '' || $type == 'auto')
        {
            $extract_temp = crawlomatic_convert_readable_html($html_cont);
            if(isset($extract_temp[1]))
            {
                $extract .= ' ' . $extract_temp[1];
            }
        }
        else
        {
            if ($type == 'regex' || $type == 'regexall') {
                $matches     = array();
                $retpreg = preg_match_all($getname, $html_cont, $matches);
                if ($retpreg === false || $getname === FALSE) 
				{
					if ($retpreg === false)
					{
						if(crawlomatic_isRegularExpression($getname) === false)
						{
							crawlomatic_log_to_file('Incorrect getname entered: ' . $seed_pag_expre);
						}
					}
                    if (!isset($crawlomatic_Main_Settings['no_content_autodetect']) || $crawlomatic_Main_Settings['no_content_autodetect'] != 'on')
                    {
                        $extract_temp .= crawlomatic_convert_readable_html($html_cont);
                        if(isset($extract_temp[1]))
                        {
                            $extract .= ' ' . $extract_temp[1];
                        }
                    }
                    continue;
                }
                if(isset($matches[0]))
                {
                    foreach ($matches[0] as $match) {
                        $extract .= ' ' . $match;
                    }
                }
            } elseif ($type == 'xpath' || $type == 'visual') {
                require_once (dirname(__FILE__) . "/res/simple_html_dom.php");
                $html_dom_original_html = crawlomatic_str_get_html($html_cont);
                if(stristr($getname, ' or ') === false && $html_dom_original_html !== false && method_exists($html_dom_original_html, 'find')){
                    $ret = $html_dom_original_html->find( trim($getname) );
                    foreach ($ret as $item ) {
                        $extract .= ' ' . $item->outertext ;              
                    }
                    $html_dom_original_html->clear();
                    $html_dom_original_html = null;
                    unset($html_dom_original_html);
                }
                else
                {
                    $doc = new DOMDocument;
                    $internalErrors = libxml_use_internal_errors(true);
                    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html_cont);
                    libxml_use_internal_errors($internalErrors);
                    $xpath = new \DOMXpath($doc);
                    $articles = $xpath->query(trim($getname));
                    if($articles !== false && count($articles) > 0)
                    {
                        foreach($articles as $container) {
                            if(method_exists($container, 'saveHTML'))
                            {
                                $extract .= ' ' . $container->saveHTML();
                            }
                            elseif(isset($container->ownerDocument) && method_exists($container->ownerDocument, 'saveHTML'))
                            {
                                $extract .= ' ' . $container->ownerDocument->saveHTML($container);
                            }
                            elseif(isset($container->nodeValue))
                            {
                                $extract .= ' ' . $container->nodeValue;
                            }
                        }
                    }
                    else
                    {
                        if (!isset($crawlomatic_Main_Settings['no_content_autodetect']) || $crawlomatic_Main_Settings['no_content_autodetect'] != 'on')
                        {
                            $extract_temp .= crawlomatic_convert_readable_html($html_cont);
                            if(isset($extract_temp[1]))
                            {
                                $extract .= ' ' . $extract_temp[1];
                            }
                        }
                        continue;
                    }
                }
            } else {
                require_once (dirname(__FILE__) . "/res/simple_html_dom.php");
                $html_dom_original_html = crawlomatic_str_get_html($html_cont);
                if($html_dom_original_html !== false && method_exists($html_dom_original_html, 'find')){
                    $getnames = explode(',', $getname);
                    foreach($getnames as $gname)
                    {
                        $ret = $html_dom_original_html->find('*['.$type.'="'.trim($gname).'"]');
                        foreach ($ret as $item ) {
                            if($item->innertext == '')
                            {
                                $extract .= ' ' . $item->outertext ;
                            }
                            else
                            {
                                $extract .= ' ' . $item->innertext ;
                            }              
                        }
                    }
                    $html_dom_original_html->clear();
                    $html_dom_original_html = null;
                    unset($html_dom_original_html);
                }
                else
                {
                    $doc = new DOMDocument;
                    $extracted = false;
                    $internalErrors = libxml_use_internal_errors(true);
                    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $html_cont);
                    libxml_use_internal_errors($internalErrors);
                    $xpath = new \DOMXpath($doc);
                    $getnames = explode(',', $getname);
                    foreach($getnames as $gname)
                    {
                        $articles = $xpath->query('*['.$type.'="'.trim($gname).'"]');
                        if($articles !== false && count($articles) > 0)
                        {
                            foreach($articles as $container) {
                                if(method_exists($container, 'saveHTML'))
                                {
                                    $extracted = true;
                                    $extract .= ' ' . $container->saveHTML();
                                }
                                elseif(isset($container->ownerDocument) && method_exists($container->ownerDocument, 'saveHTML'))
                                {
                                    $extracted = true;
                                    $extract .= ' ' . $container->ownerDocument->saveHTML($container);
                                }
                                elseif(isset($container->nodeValue))
                                {
                                    $extracted = true;
                                    $extract .= ' ' . $container->nodeValue;
                                }
                            }
                        }
                    }
                    if($extracted == false)
                    {
                        if (!isset($crawlomatic_Main_Settings['no_content_autodetect']) || $crawlomatic_Main_Settings['no_content_autodetect'] != 'on')
                        {
                            $extract_temp .= crawlomatic_convert_readable_html($html_cont);
                            if(isset($extract_temp[1]))
                            {
                                $extract .= ' ' . $extract_temp[1];
                            }
                        }
                    }
                }
            }
        }
        $extract .= ' ' . crawlomatic_get_paged_content($href, $html_cont, $type, $getname, $paged_crawl_type, $paged_crawl_str, $custom_cookies, $max, $custom_user_agent, $use_proxy, $use_phantom, $user_pass, $phantom_wait, $request_delay, $scripter, $local_storage);
    }
    
    return $extract;
}
if(!function_exists('http_build_url'))
{
    // Define constants
    define('HTTP_URL_REPLACE',          0x0001);    // Replace every part of the first URL when there's one of the second URL
    define('HTTP_URL_JOIN_PATH',        0x0002);    // Join relative paths
    define('HTTP_URL_JOIN_QUERY',       0x0004);    // Join query strings
    define('HTTP_URL_STRIP_USER',       0x0008);    // Strip any user authentication information
    define('HTTP_URL_STRIP_PASS',       0x0010);    // Strip any password authentication information
    define('HTTP_URL_STRIP_PORT',       0x0020);    // Strip explicit port numbers
    define('HTTP_URL_STRIP_PATH',       0x0040);    // Strip complete path
    define('HTTP_URL_STRIP_QUERY',      0x0080);    // Strip query string
    define('HTTP_URL_STRIP_FRAGMENT',   0x0100);    // Strip any fragments (#identifier)

    // Combination constants
    define('HTTP_URL_STRIP_AUTH',       HTTP_URL_STRIP_USER | HTTP_URL_STRIP_PASS);
    define('HTTP_URL_STRIP_ALL',        HTTP_URL_STRIP_AUTH | HTTP_URL_STRIP_PORT | HTTP_URL_STRIP_QUERY | HTTP_URL_STRIP_FRAGMENT);

    /**
     * HTTP Build URL
     * Combines arrays in the form of parse_url() into a new string based on specific options
     * @name http_build_url
     * @param string|array $url     The existing URL as a string or result from parse_url
     * @param string|array $parts   Same as $url
     * @param int $flags            URLs are combined based on these
     * @param array &$new_url       If set, filled with array version of new url
     * @return string
     */
    function http_build_url(/*string|array*/ $url, /*string|array*/ $parts = array(), /*int*/ $flags = HTTP_URL_REPLACE, /*array*/ &$new_url = false)
    {
        // If the $url is a string
        if(is_string($url))
        {
            $url = parse_url($url);
        }

        // If the $parts is a string
        if(is_string($parts))
        {
            $parts  = parse_url($parts);
        }

        // Scheme and Host are always replaced
        if(isset($parts['scheme'])) $url['scheme']  = $parts['scheme'];
        if(isset($parts['host']))   $url['host']    = $parts['host'];

        // (If applicable) Replace the original URL with it's new parts
        if(HTTP_URL_REPLACE & $flags)
        {
            // Go through each possible key
            foreach(array('user','pass','port','path','query','fragment') as $key)
            {
                // If it's set in $parts, replace it in $url
                if(isset($parts[$key])) $url[$key]  = $parts[$key];
            }
        }
        else
        {
            // Join the original URL path with the new path
            if(isset($parts['path']) && (HTTP_URL_JOIN_PATH & $flags))
            {
                if(isset($url['path']) && $url['path'] != '')
                {
                    // If the URL doesn't start with a slash, we need to merge
                    if($url['path'][0] != '/')
                    {
                        // If the path ends with a slash, store as is
                        if('/' == $parts['path'][strlen($parts['path'])-1])
                        {
                            $sBasePath  = $parts['path'];
                        }
                        // Else trim off the file
                        else
                        {
                            // Get just the base directory
                            $sBasePath  = dirname($parts['path']);
                        }

                        // If it's empty
                        if('' == $sBasePath)    $sBasePath  = '/';

                        // Add the two together
                        $url['path']    = $sBasePath . $url['path'];

                        // Free memory
                        unset($sBasePath);
                    }

                    if(false !== strpos($url['path'], './'))
                    {
                        // Remove any '../' and their directories
                        while(preg_match('/\w+\/\.\.\//', $url['path'])){
                            $url['path']    = preg_replace('/\w+\/\.\.\//', '', $url['path']);
                        }

                        // Remove any './'
                        $url['path']    = str_replace('./', '', $url['path']);
                    }
                }
                else
                {
                    $url['path']    = $parts['path'];
                }
            }

            // Join the original query string with the new query string
            if(isset($parts['query']) && (HTTP_URL_JOIN_QUERY & $flags))
            {
                if (isset($url['query']))   $url['query']   .= '&' . $parts['query'];
                else                        $url['query']   = $parts['query'];
            }
        }

        // Strips all the applicable sections of the URL
        if(HTTP_URL_STRIP_USER & $flags)        unset($url['user']);
        if(HTTP_URL_STRIP_PASS & $flags)        unset($url['pass']);
        if(HTTP_URL_STRIP_PORT & $flags)        unset($url['port']);
        if(HTTP_URL_STRIP_PATH & $flags)        unset($url['path']);
        if(HTTP_URL_STRIP_QUERY & $flags)       unset($url['query']);
        if(HTTP_URL_STRIP_FRAGMENT & $flags)    unset($url['fragment']);

        // Store the new associative array in $new_url
        $new_url    = $url;

        // Combine the new elements into a string and return it
        return
             ((isset($url['scheme'])) ? $url['scheme'] . '://' : '')
            .((isset($url['user'])) ? $url['user'] . ((isset($url['pass'])) ? ':' . $url['pass'] : '') .'@' : '')
            .((isset($url['host'])) ? $url['host'] : '')
            .((isset($url['port'])) ? ':' . $url['port'] : '')
            .((isset($url['path'])) ? $url['path'] : '')
            .((isset($url['query'])) ? '?' . $url['query'] : '')
            .((isset($url['fragment'])) ? '#' . $url['fragment'] : '')
        ;
    }
}
function crawlomatic_starts_with($newx_url, $query)
{
    if(substr( $newx_url, 0, strlen($query) ) === $query)
    {
        return true;
    }
    return false;
}

function crawlomatic_get_content($type, $getname, $htmlcontent, $single = false)
{
    $extract = '';
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if (isset($crawlomatic_Main_Settings['multi_separator'])) {
        $cont_sep = $crawlomatic_Main_Settings['multi_separator'];
    }
    else
    {
        if($single == true)
        {
            $cont_sep = '';
        }
        else
        {
            $cont_sep = '<br/>';
        }
    }
    if ($type == 'regex') {
        $matches     = array();
        $rez = preg_match_all($getname, $htmlcontent, $matches);
        if ($rez === FALSE) {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                crawlomatic_log_to_file('[crawlomatic_get_content] preg_match_all failed for expr: ' . $getname . '!');
            }
            return '';
        }
        $regcnt = 0;
        foreach ($matches as $match) {
            if($regcnt == 0)
            {
                $regcnt++;
                continue;
            }
            if(!isset($match[0]))
            {
                continue;
            }
            $regcnt++;
            $extract .= $match[0];
            if($single === true)
            {
                break;
            }
            else
            {
                $extract .= $cont_sep;
            }
        }
    } elseif ($type == 'regexall') {
        $matches     = array();
        $rez = preg_match_all($getname, $htmlcontent, $matches);
        if ($rez === FALSE) {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                crawlomatic_log_to_file('[crawlomatic_get_content] preg_match_all failed for expr: ' . $getname . '!');
            }
            return '';
        }
        $regcnt = 0;
        foreach ($matches as $match) {
            if($regcnt == 0 && count($matches) > 1)
            {
                $regcnt++;
                continue;
            }
            if(!isset($match[0]))
            {
                continue;
            }
            $regcnt++;
            foreach($match as $mmatch)
            {
                $extract .= $mmatch . $cont_sep;
                
            }
            if($single === true)
            {
                break;
            }
        }
    } elseif ($type == 'xpath' || $type == 'visual') {
        require_once (dirname(__FILE__) . "/res/simple_html_dom.php");
        $html_dom_original_html = crawlomatic_str_get_html($htmlcontent);
        if(stristr($getname, ' or ') === false && $html_dom_original_html !== false && method_exists($html_dom_original_html, 'find')){
            $ret = $html_dom_original_html->find( trim($getname) );
            if(count($ret) == 0)
            {
                $html_dom_original_html->clear();
                $html_dom_original_html = null;
                unset($html_dom_original_html);
                $doc = new DOMDocument;
                $internalErrors = libxml_use_internal_errors(true);
                $doc->loadHTML('<?xml encoding="utf-8" ?>' . $htmlcontent);
                libxml_use_internal_errors($internalErrors);
                $xpath = new \DOMXpath($doc);
                $articles = $xpath->query(trim($getname));
                if($articles !== false && count($articles) > 0)
                {
                    foreach($articles as $container) {
						if(method_exists($container, 'saveHTML'))
						{
							$extract .= $container->saveHTML() . $cont_sep;
						}
                        elseif(isset($container->ownerDocument) && method_exists($container->ownerDocument, 'saveHTML'))
                        {
                            $extract .= $container->ownerDocument->saveHTML($container) . $cont_sep;
                        }
                        elseif(isset($container->nodeValue))
                        {
                            $extract .= $container->nodeValue . $cont_sep;
                        }
                    }
                }
                else
                {
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                        crawlomatic_log_to_file('crawlomatic_str_get_html failed for page, xpath is: ' . $getname . '!');
                    }
                    return '';
                }
            }
            else
            {
                foreach ($ret as $item ) {
                    if($item->innertext == '')
                    {
                        $extract .= $item->outertext;
                    }
                    else
                    {
                        $extract .= $item->innertext;
                    }
                    
                    if($single === true)
                    {
                        break;
                    }
                    else
                    {
                        $extract .= $cont_sep;
                    }
                }
                $html_dom_original_html->clear();
                $html_dom_original_html = null;
                unset($html_dom_original_html);
            }
        }
        else
        {
            $doc = new DOMDocument;
            $internalErrors = libxml_use_internal_errors(true);
            $doc->loadHTML('<?xml encoding="utf-8" ?>' . $htmlcontent);
            libxml_use_internal_errors($internalErrors);
            $xpath = new \DOMXpath($doc);
            $articles = $xpath->query(trim($getname));
            if($articles !== false && count($articles) > 0)
            {
                foreach($articles as $container) {
					if(method_exists($container, 'saveHTML'))
					{
						$extract .= $container->saveHTML() . $cont_sep;
					}
                    elseif(isset($container->ownerDocument) && method_exists($container->ownerDocument, 'saveHTML'))
                    {
                        $extract .= $container->ownerDocument->saveHTML($container) . $cont_sep;
                    }
                    elseif(isset($container->nodeValue))
                    {
                        $extract .= $container->nodeValue . $cont_sep;
                    }
                }
            }
            else
            {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                    crawlomatic_log_to_file('crawlomatic_str_get_html failed for page, xpath: ' . $getname . '!');
                }
                return '';
            }
        }
    } else {
        require_once (dirname(__FILE__) . "/res/simple_html_dom.php");
        $html_dom_original_html = crawlomatic_str_get_html($htmlcontent);
        if($html_dom_original_html !== false && method_exists($html_dom_original_html, 'find')){
            $getnames = explode(',', $getname);
            foreach($getnames as $gname)
            {
                $ret = $html_dom_original_html->find('*['.$type.'="'.trim($gname).'"]');
                if(count($ret) == 0)
                {
                    $doc = new DOMDocument;
                    $internalErrors = libxml_use_internal_errors(true);
                    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $htmlcontent);
                    libxml_use_internal_errors($internalErrors);
                    $xpath = new \DOMXpath($doc);
                    $articles = $xpath->query('*['.$type.'="'.trim($gname).'"]');
                    $oks = false;
                    if($articles !== false && count($articles) > 0)
                    {
                        foreach($articles as $container) {
							if(method_exists($container, 'saveHTML'))
							{
								$oks = true;
								$extract .= $container->saveHTML() . $cont_sep;
							}
                            elseif(isset($container->ownerDocument) && method_exists($container->ownerDocument, 'saveHTML'))
                            {
                                $extract .= $container->ownerDocument->saveHTML($container) . $cont_sep;
                            }
                            elseif(isset($container->nodeValue))
                            {
                                $oks = true;
                                $extract .= $container->nodeValue . $cont_sep;
                            }
                        }
                    }
                    if($oks == false)
                    {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                            crawlomatic_log_to_file('No content found matching the query you set: *[' . $type . '="' . trim($gname) . '"]');
                        }
                        return '';
                    }
                }
                else
                {
                    foreach ($ret as $item ) {
                        if($item->innertext == '')
                        {
                            $extract .= $item->outertext . $cont_sep;
                        }
                        else
                        {
                            $extract .= $item->innertext . $cont_sep;
                        }
                        if($single === true)
                        {
                            break;
                        }
                    }
                }
            }
        }
        else
        {
            $html_dom_original_html = null;
            unset($html_dom_original_html);
            $doc = new DOMDocument;
            $internalErrors = libxml_use_internal_errors(true);
            $doc->loadHTML('<?xml encoding="utf-8" ?>' . $htmlcontent);
            libxml_use_internal_errors($internalErrors);
            $xpath = new \DOMXpath($doc);
            $getnames = explode(',', $getname);
            $oks = false;
            foreach($getnames as $gname)
            {
                $articles = $xpath->query('*['.$type.'="'.trim($gname).'"]');
                if($articles !== false && count($articles) > 0)
                {
                    foreach($articles as $container) {
						if(method_exists($container, 'saveHTML'))
						{
							$oks = true;
							$extract .= $container->saveHTML() . $cont_sep;
						}
                        elseif(isset($container->ownerDocument) && method_exists($container->ownerDocument, 'saveHTML'))
                        {
                            $oks = true;
                            $extract .= $container->ownerDocument->saveHTML($container) . $cont_sep;
                        }
                        elseif(isset($container->nodeValue))
                        {
                            $oks = true;
                            $extract .= $container->nodeValue . $cont_sep;
                        }
                    }
                }
            }
            if($oks == false)
            {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                    crawlomatic_log_to_file('No matching content found for query: ' . '*['.$type.'="'.trim($gname).'"]');
                }
                return '';
            }
        }
    }
    if($cont_sep != '' && $cont_sep != '<br/>')
    {
        $extract = rtrim($extract, $cont_sep);
    }
    return $extract;
}

function crawlomatic_get_gallery_content($type, $getname, $htmlcontent, $url, $lazy_tag)
{
    $extract = array();
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if ($type == 'regex') 
    {
        $matches     = array();
        $rez = preg_match_all($getname, $htmlcontent, $matches);
        if ($rez === FALSE) {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                crawlomatic_log_to_file('[crawlomatic_get_gallery_content] preg_match_all failed for expr: ' . $getname . '!');
            }
            return $extract;
        }
        $regcnt = 0;
        foreach ($matches as $match) {
            if($regcnt == 0)
            {
                $regcnt++;
                continue;
            }
            if(!isset($match[0]))
            {
                continue;
            }
            $regcnt++;
            $extract[] = trim($match[0]);
        }
    } elseif ($type == 'regexall') {
        $matches     = array();
        $rez = preg_match_all($getname, $htmlcontent, $matches);
        if ($rez === FALSE) {
            if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                crawlomatic_log_to_file('[crawlomatic_get_gallery_content] preg_match_all failed for expr: ' . $getname . '!');
            }
            return $extract;
        }
        $regcnt = 0;
        foreach ($matches as $match) {
            if($regcnt == 0 && count($matches) > 1)
            {
                $regcnt++;
                continue;
            }
            if(!isset($match[0]))
            {
                continue;
            }
            $regcnt++;
            foreach($match as $mmatch)
            {
                $extract[] = trim($mmatch);
                
            }
        }
    } elseif ($type == 'xpath' || $type == 'visual') {
        require_once (dirname(__FILE__) . "/res/simple_html_dom.php");
        $html_dom_original_html = crawlomatic_str_get_html($htmlcontent);
        if(stristr($getname, ' or ') === false && $html_dom_original_html !== false && method_exists($html_dom_original_html, 'find')){
            $ret = $html_dom_original_html->find( trim($getname) );
            if(count($ret) == 0)
            {
                $html_dom_original_html->clear();
                $html_dom_original_html = null;
                unset($html_dom_original_html);
                $doc = new DOMDocument;
                $internalErrors = libxml_use_internal_errors(true);
                $doc->loadHTML('<?xml encoding="utf-8" ?>' . $htmlcontent);
                libxml_use_internal_errors($internalErrors);
                $xpath = new \DOMXpath($doc);
                $articles = $xpath->query(trim($getname));
                if($articles !== false && count($articles) > 0)
                {
                    foreach($articles as $container) {
						if(method_exists($container, 'saveHTML'))
						{
							$extract[] = trim($container->saveHTML());
						}
                        elseif(isset($container->ownerDocument) && method_exists($container->ownerDocument, 'saveHTML'))
                        {
                            $extract[] = trim($container->ownerDocument->saveHTML($container));
                        }
                        elseif(isset($container->nodeValue))
                        {
                            $extract[] = trim($container->nodeValue);
                        }
                    }
                }
                else
                {
                    if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                        crawlomatic_log_to_file('crawlomatic_get_gallery_content failed for page, xpath is: ' . $getname . '!');
                    }
                    return $extract;
                }
            }
            else
            {
                foreach ($ret as $item ) {
                    if($item->innertext == '')
                    {
                        $extract[] = trim($item->outertext);
                    }
                    else
                    {
                        $extract[] = trim($item->innertext);
                    }
                }
                $html_dom_original_html->clear();
                $html_dom_original_html = null;
                unset($html_dom_original_html);
            }
        }
        else
        {
            $doc = new DOMDocument;
            $internalErrors = libxml_use_internal_errors(true);
            $doc->loadHTML('<?xml encoding="utf-8" ?>' . $htmlcontent);
            libxml_use_internal_errors($internalErrors);
            $xpath = new \DOMXpath($doc);
            $articles = $xpath->query(trim($getname));
            if($articles !== false && count($articles) > 0)
            {
                foreach($articles as $container) {
					if(method_exists($container, 'saveHTML'))
					{
						$extract[] = trim($container->saveHTML());
					}
                    elseif(isset($container->ownerDocument) && method_exists($container->ownerDocument, 'saveHTML'))
                    {
                        $extract[] = trim($container->ownerDocument->saveHTML($container));
                    }
                    elseif(isset($container->nodeValue))
                    {
                        $extract[] = trim($container->nodeValue);
                    }
                }
            }
            else
            {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                    crawlomatic_log_to_file('crawlomatic_get_gallery_content failed for page, xpath: ' . $getname . '!');
                }
                return $extract;
            }
        }
    } else {
        require_once (dirname(__FILE__) . "/res/simple_html_dom.php");
        $html_dom_original_html = crawlomatic_str_get_html($htmlcontent);
        if($html_dom_original_html !== false && method_exists($html_dom_original_html, 'find')){
            $getnames = explode(',', $getname);
            foreach($getnames as $gname)
            {
                $ret = $html_dom_original_html->find('*['.$type.'="'.trim($gname).'"]');
                if(count($ret) == 0)
                {
                    $doc = new DOMDocument;
                    $internalErrors = libxml_use_internal_errors(true);
                    $doc->loadHTML('<?xml encoding="utf-8" ?>' . $htmlcontent);
                    libxml_use_internal_errors($internalErrors);
                    $xpath = new \DOMXpath($doc);
                    $articles = $xpath->query('*['.$type.'="'.trim($gname).'"]');
                    $oks = false;
                    if($articles !== false && count($articles) > 0)
                    {
                        foreach($articles as $container) {
							if(method_exists($container, 'saveHTML'))
							{
								$oks = true;
								$extract[] = trim($container->saveHTML());
							}
                            elseif(isset($container->ownerDocument) && method_exists($container->ownerDocument, 'saveHTML'))
                            {
                                $extract[] = trim($container->ownerDocument->saveHTML($container));
                            }
                            elseif(isset($container->nodeValue))
                            {
                                $oks = true;
                                $extract[] = trim($container->nodeValue);
                            }
                        }
                    }
                    if($oks == false)
                    {
                        if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                            crawlomatic_log_to_file('No gallery_content found matching the query you set: *[' . $type . '="' . trim($gname) . '"]');
                        }
                        return $extract;
                    }
                }
                else
                {
                    foreach ($ret as $item ) {
                        if($item->innertext == '')
                        {
                            $extract[] = trim($item->outertext);
                        }
                        else
                        {
                            $extract[] = trim($item->innertext);
                        }
                    }
                }
            }
        }
        else
        {
            $html_dom_original_html = null;
            unset($html_dom_original_html);
            $doc = new DOMDocument;
            $internalErrors = libxml_use_internal_errors(true);
            $doc->loadHTML('<?xml encoding="utf-8" ?>' . $htmlcontent);
            libxml_use_internal_errors($internalErrors);
            $xpath = new \DOMXpath($doc);
            $getnames = explode(',', $getname);
            $oks = false;
            foreach($getnames as $gname)
            {
                $articles = $xpath->query('*['.$type.'="'.trim($gname).'"]');
                if($articles !== false && count($articles) > 0)
                {
                    foreach($articles as $container) {
						if(method_exists($container, 'saveHTML'))
						{
							$oks = true;
							$extract[] = trim($container->saveHTML());
						}
                        elseif(isset($container->ownerDocument) && method_exists($container->ownerDocument, 'saveHTML'))
                        {
                            $oks = true;
                            $extract[] = trim($container->ownerDocument->saveHTML($container));
                        }
                        elseif(isset($container->nodeValue))
                        {
                            $oks = true;
                            $extract[] = trim($container->nodeValue);
                        }
                    }
                }
            }
            if($oks == false)
            {
                if (isset($crawlomatic_Main_Settings['enable_detailed_logging'])) {
                    crawlomatic_log_to_file('No matching gallery_content found for query: ' . '*['.$type.'="'.trim($gname).'"]');
                }
                return $extract;
            }
        }
    }
    $final_rez = array();
    foreach($extract as $idval)
    {
        if($lazy_tag != '')
        {
            preg_match_all('#<img(?:[^>]*?)' . preg_quote($lazy_tag) . '=[\'"]([^\'">]*?)[\'"](?:[^>]*?)>#s', $idval, $imgsMatchs);
            if(isset($imgsMatchs[1]))
            {
                foreach($imgsMatchs[1] as $imgrez)
                {
                    $final_rez[] = $imgrez;
                }
            }
            else
            {
                preg_match_all('#<img(?:[^>]*?)src=[\'"]([^\'">]*?)[\'"](?:[^>]*?)>#s', $idval, $imgsMatchs);
                if(isset($imgsMatchs[1]))
                {
                    foreach($imgsMatchs[1] as $imgrez)
                    {
                        $final_rez[] = $imgrez;
                    }
                }
                else
                {
                    $final_rez[] = $idval;
                }
            }
        }
        else
        {
            preg_match_all('#<img(?:[^>]*?)src=[\'"]([^\'">]*?)[\'"](?:[^>]*?)>#s', $idval, $imgsMatchs);
            if(isset($imgsMatchs[1]))
            {
                foreach($imgsMatchs[1] as $imgrez)
                {
                    $final_rez[] = $imgrez;
                }
            }
            else
            {
                $final_rez[] = $idval;
            }
        }
    }
    foreach($final_rez as $idx => $idval)
    {
        if(stristr($idval, 'http:') === FALSE && stristr($idval, 'https:') === FALSE)
        {
            $final_rez[$idx] = crawlomatic_fix_single_link($final_rez[$idx], $url);
        }
    }
    return $final_rez;
}

function crawlomatic_substr_close_tags($text, $max_length)
{
    $tags   = array();
    $result = "";

    $is_open   = false;
    $grab_open = false;
    $is_close  = false;
    $in_double_quotes = false;
    $in_single_quotes = false;
    $tag = "";

    $i = 0;
    $stripped = 0;

    $stripped_text = strip_tags($text);
    if (function_exists('mb_strlen') && function_exists('mb_substr')) {
        while ($i < mb_strlen($text) && $stripped < mb_strlen($stripped_text) && $stripped < $max_length)
        {
            $symbol  = mb_substr($text,$i,1);
            $result .= $symbol;

            switch ($symbol)
            {
               case '<':
                    $is_open   = true;
                    $grab_open = true;
                    break;

               case '"':
                   if ($in_double_quotes)
                       $in_double_quotes = false;
                   else
                       $in_double_quotes = true;

                break;

                case "'":
                  if ($in_single_quotes)
                      $in_single_quotes = false;
                  else
                      $in_single_quotes = true;

                break;

                case '/':
                    if ($is_open && !$in_double_quotes && !$in_single_quotes)
                    {
                        $is_close  = true;
                        $is_open   = false;
                        $grab_open = false;
                    }

                    break;

                case ' ':
                    if ($is_open)
                        $grab_open = false;
                    else
                        $stripped++;

                    break;

                case '>':
                    if ($is_open)
                    {
                        $is_open   = false;
                        $grab_open = false;
                        array_push($tags, $tag);
                        $tag = "";
                    }
                    else if ($is_close)
                    {
                        $is_close = false;
                        array_pop($tags);
                        $tag = "";
                    }

                    break;

                default:
                    if ($grab_open || $is_close)
                        $tag .= $symbol;

                    if (!$is_open && !$is_close)
                        $stripped++;
            }
            $i++;
        }
    }
    else
    {
        while ($i < strlen($text) && $stripped < strlen($stripped_text) && $stripped < $max_length)
        {
            $symbol  = $text[$i];
            $result .= $symbol;

            switch ($symbol)
            {
               case '<':
                    $is_open   = true;
                    $grab_open = true;
                    break;

               case '"':
                   if ($in_double_quotes)
                       $in_double_quotes = false;
                   else
                       $in_double_quotes = true;

                break;

                case "'":
                  if ($in_single_quotes)
                      $in_single_quotes = false;
                  else
                      $in_single_quotes = true;

                break;

                case '/':
                    if ($is_open && !$in_double_quotes && !$in_single_quotes)
                    {
                        $is_close  = true;
                        $is_open   = false;
                        $grab_open = false;
                    }

                    break;

                case ' ':
                    if ($is_open)
                        $grab_open = false;
                    else
                        $stripped++;

                    break;

                case '>':
                    if ($is_open)
                    {
                        $is_open   = false;
                        $grab_open = false;
                        array_push($tags, $tag);
                        $tag = "";
                    }
                    else if ($is_close)
                    {
                        $is_close = false;
                        array_pop($tags);
                        $tag = "";
                    }

                    break;

                default:
                    if ($grab_open || $is_close)
                        $tag .= $symbol;

                    if (!$is_open && !$is_close)
                        $stripped++;
            }
            $i++;
        }
    }

    while ($tags)
        $result .= "</".array_pop($tags).">";
    return force_balance_tags($result);
}


register_activation_hook(__FILE__, 'crawlomatic_check_version');
function crawlomatic_check_version()
{
    if (!function_exists('curl_init')) {
        echo '<h3>'.esc_html__('Please enable curl PHP extension. Please contact your hosting provider\'s support to help you in this matter.', 'crawlomatic-multipage-scraper-post-generator').'</h3>';
        die;
    }
    global $wp_version;
    if (!current_user_can('activate_plugins')) {
        echo '<p>' . esc_html__('You are not allowed to activate plugins!', 'crawlomatic-multipage-scraper-post-generator') . '</p>';
        die;
    }
    $php_version_required = '5.6';
    $wp_version_required  = '2.7';
    
    if (version_compare(PHP_VERSION, $php_version_required, '<')) {
        deactivate_plugins(basename(__FILE__));
        echo '<p>' . sprintf(esc_html__('This plugin can not be activated because it requires a PHP version greater than %1$s. Please update your PHP version before you activate it.', 'crawlomatic-multipage-scraper-post-generator'), $php_version_required) . '</p>';
        die;
    }
    
    if (version_compare($wp_version, $wp_version_required, '<')) {
        deactivate_plugins(basename(__FILE__));
        echo '<p>' . sprintf(esc_html__('This plugin can not be activated because it requires a WordPress version greater than %1$s. Please go to Dashboard -> Updates to get the latest version of WordPress.', 'crawlomatic-multipage-scraper-post-generator'), $wp_version_required) . '</p>';
        die;
    }
}
function crawlomatic_get_pinfo() {
    ob_start();
    phpinfo();
    $data = ob_get_contents();
    ob_clean();
    return $data;
}

function crawlomatic_register_mysettings()
{
    crawlomatic_cron_schedule();
    if(isset($_GET['crawlomatic_page']))
    {
        $curent_page = $_GET["crawlomatic_page"];
    }
    else
    {
        $curent_page = '';
    }
    $all_rules = get_option('crawlomatic_rules_list', array());
    $rules_count = count($all_rules);
    $rules_per_page = get_option('crawlomatic_posts_per_page', 12);
    $max_pages = ceil($rules_count/$rules_per_page);
    if($max_pages == 0)
    {
        $max_pages = 1;
    }
    $last_url = (crawlomatic_isSecure() ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
    if(stristr($last_url, 'crawlomatic_items_panel') !== false && (!is_numeric($curent_page) || $curent_page > $max_pages || $curent_page <= 0))
    {
        if(stristr($last_url, 'crawlomatic_page=') === false)
        {
            if(stristr($last_url, '?') === false)
            {
                $last_url .= '?crawlomatic_page=' . $max_pages;
            }
            else
            {
                $last_url .= '&crawlomatic_page=' . $max_pages;
            }
        }
        else
        {
            if(isset($_GET['crawlomatic_page']))
            {
                $curent_page = $_GET["crawlomatic_page"];
            }
            else
            {
                $curent_page = '';
            }
            if(is_numeric($curent_page))
            {
                $last_url = str_replace('crawlomatic_page=' . $curent_page, 'crawlomatic_page=' . $max_pages, $last_url);
            }
            else
            {
                if(stristr($last_url, '?') === false)
                {
                    $last_url .= '?crawlomatic_page=' . $max_pages;
                }
                else
                {
                    $last_url .= '&crawlomatic_page=' . $max_pages;
                }
            }
        }
        crawlomatic_redirect($last_url);
    }
    register_setting('crawlomatic_option_group', 'crawlomatic_Main_Settings');
    if(is_multisite())
    {
        if (!get_option('crawlomatic_Main_Settings'))
        {
            crawlomatic_activation_callback(TRUE);
        }
    }
    if(isset($_POST['crawlomatic_download_rules_to_file']))
    {
        $GLOBALS['wp_object_cache']->delete('crawlomatic_rules_list', 'options');
        if (!get_option('crawlomatic_rules_list')) {
            $rules = array();
        } else {
            $rules = get_option('crawlomatic_rules_list');
        }
        header("Content-type: application/x-msdownload");
        header("Content-Disposition: attachment; filename=crawlomatic_rules.bak");
        header("Pragma: no-cache");
        header("Expires: 0");
        echo json_encode($rules);
        exit();
    }
    if(isset($_POST['crawlomatic_restore_rules']))
    {
        if(isset($_FILES['crawlomatic-file-upload-rules']['tmp_name'])) 
        {		
            global $wp_filesystem;
            if ( ! is_a( $wp_filesystem, 'WP_Filesystem_Base') ){
                include_once(ABSPATH . 'wp-admin/includes/file.php');$creds = request_filesystem_credentials( site_url() );
                wp_filesystem($creds);
            }
            $file = $wp_filesystem->get_contents($_FILES['crawlomatic-file-upload-rules']['tmp_name']);
            if($file === false)
            {
                crawlomatic_log_to_file('Failed to restore rules from file: ' . $_FILES['crawlomatic-file-upload-rules']['tmp_name']);
            }
            else
            {
                $rules = json_decode($file, true);
                if($rules === false)
                {
                    crawlomatic_log_to_file('Failed to decode value: ' . print_r($file, true));
                }
                else
                {
                    if(isset($rules[0][0]) && isset($rules[0][37]))
                    {
                        update_option('crawlomatic_rules_list', $rules, false);
                    }
                    else
                    {
                        crawlomatic_log_to_file('Invalid file given: ' . print_r($rules, true));
                    }
                }
            }               
        }
    }
}

function crawlomatic_get_plugin_url()
{
    return plugins_url('', __FILE__);
}

function crawlomatic_get_file_url($url)
{
    return esc_url(crawlomatic_get_plugin_url() . '/' . $url);
}

function crawlomatic_admin_load_files()
{
    wp_register_style('crawlomatic-browser-style', plugins_url('styles/crawlomatic-browser.css', __FILE__), false, '1.0.0');
    wp_enqueue_style('crawlomatic-browser-style');
    wp_register_style('crawlomatic-custom-style', plugins_url('styles/coderevolution-style.css', __FILE__), false, '1.0.0');
    wp_enqueue_style('crawlomatic-custom-style');
    wp_enqueue_script('jquery');
    wp_enqueue_script('media-upload');
    wp_enqueue_script('thickbox');
    wp_enqueue_style('thickbox');
}

add_action('wp_enqueue_scripts', 'crawlomatic_wp_load_files');
function crawlomatic_wp_load_files()
{
    global $post;
    if(is_object($post) && isset($post->ID))
    {
        $css_cont = get_post_meta($post->ID, 'crawlomatic_css_cont', true);
        if($css_cont != '')
        {
            $css_cont = wp_strip_all_tags($css_cont);
            wp_register_style( 'crawlomatic-official-style', false );
            wp_enqueue_style( 'crawlomatic-official-style' );
            wp_add_inline_style( 'crawlomatic-official-style', $css_cont );
        }
    }
    wp_enqueue_style('crawlomatic-thumbnail-css', plugins_url('styles/crawlomatic-thumbnail.css', __FILE__));
}

function crawlomatic_random_sentence_generator($first = true)
{
    $crawlomatic_Main_Settings = get_option('crawlomatic_Main_Settings', false);
    if ($first == false) {
        $r_sentences = $crawlomatic_Main_Settings['sentence_list2'];
    } else {
        $r_sentences = $crawlomatic_Main_Settings['sentence_list'];
    }
    $r_variables = $crawlomatic_Main_Settings['variable_list'];
    $r_sentences = trim($r_sentences);
    $r_variables = trim($r_variables, ';');
    $r_variables = trim($r_variables);
    $r_sentences = str_replace("\r\n", "\n", $r_sentences);
    $r_sentences = str_replace("\r", "\n", $r_sentences);
    $r_sentences = explode("\n", $r_sentences);
    $r_variables = str_replace("\r\n", "\n", $r_variables);
    $r_variables = str_replace("\r", "\n", $r_variables);
    $r_variables = explode("\n", $r_variables);
    $r_vars      = array();
    for ($x = 0; $x < count($r_variables); $x++) {
        $var = explode("=>", trim($r_variables[$x]));
        if (isset($var[1])) {
            $key          = strtolower(trim($var[0]));
            $words        = explode(";", trim($var[1]));
            $r_vars[$key] = $words;
        }
    }
    $max_s    = count($r_sentences) - 1;
    $rand_s   = rand(0, $max_s);
    $sentence = $r_sentences[$rand_s];
    $sentence = str_replace(' ,', ',', ucfirst(crawlomatic_replace_words($sentence, $r_vars)));
    $sentence = str_replace(' .', '.', $sentence);
    $sentence = str_replace(' !', '!', $sentence);
    $sentence = str_replace(' ?', '?', $sentence);
    $sentence = trim($sentence);
    return $sentence;
}

function crawlomatic_get_word($key, $r_vars)
{
    if (isset($r_vars[$key])) {
        
        $words  = $r_vars[$key];
        $w_max  = count($words) - 1;
        $w_rand = rand(0, $w_max);
        return crawlomatic_replace_words(trim($words[$w_rand]), $r_vars);
    } else {
        return "";
    }
    
}

function crawlomatic_replace_words($sentence, $r_vars)
{
    
    if (str_replace('%', '', $sentence) == $sentence)
        return $sentence;
    
    $words = explode(" ", $sentence);
    
    $new_sentence = array();
    for ($w = 0; $w < count($words); $w++) {
        
        $word = trim($words[$w]);
        
        if ($word != '') {
            if (preg_match('/^%([^%\n]*)$/', $word, $m)) {
                $varkey         = trim($m[1]);
                $new_sentence[] = crawlomatic_get_word($varkey, $r_vars);
            } else {
                $new_sentence[] = $word;
            }
        }
    }
    return implode(" ", $new_sentence);
}

function crawlomatic_findRobotsMetaTagProperties($html)
{
    $metaTagLine = crawlomatic_findRobotsMetaTagLine($html);

    return [
        'noindex' => $metaTagLine
            ? strpos(strtolower($metaTagLine), 'noindex') !== false
            : false,

        'nofollow' => $metaTagLine
            ? strpos(strtolower($metaTagLine), 'nofollow') !== false
            : false,
         'none' => $metaTagLine
            ? strpos(strtolower($metaTagLine), 'none') !== false
            : false,
    ];
}

function crawlomatic_findRobotsMetaTagLine($html)
{
    if (preg_match('/\<meta name="robots".*?\>/mis', $html, $matches)) {
        return $matches[0];
    }

    return '';
}

function crawlomatic_scraper_get_content($url, $query = '', $args = array())
{
    return Crawlomatic_Shortcode_Scraper::get_content($url, $query, $args);
}
?>