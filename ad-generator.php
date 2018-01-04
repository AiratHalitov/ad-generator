<?php
/**
 * Plugin Name: Ad Generator
 * Plugin URI:  https://github.com/AiratHalitov/ad-generator
 * Description: Ad Generator / Text Randomizer
 * Author:      Airat Halitov
 * Author URI:  https://airat.biz
 * Version:     1.0.0
 * Text Domain: ad-generator
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// https://habrahabr.ru/company/dataart/blog/265245/
// [ad_generator]

class ad_generator_shortcode {
	
	static $add_script;
	
	static function init () {
		add_shortcode('ad_generator', array(__CLASS__, 'ad_generator_func'));
		//add_action('init', array(__CLASS__, 'register_script'));
		//add_action('wp_footer', array(__CLASS__, 'print_script'));
	}
	
	static function ad_generator_func( $atts ) {
		self::$add_script = true; 
		$result_text = '';
		
		$result_text .= '<form method="post" action="">';
		$ad_text = isset($_POST['ad_text']) ? (string) $_POST['ad_text'] : '';
		
		if ($ad_text) {
			$result_text .=  '<textarea name="ad_text" cols="100" rows="14">' . htmlspecialchars($ad_text) . '</textarea>';
		} else {
			$result_text .=  '<textarea name="ad_text" cols="100" rows="14">{Рандомизатор|Рандомайзер} {|текста}</textarea>';
		} 
		
		$result_text .=  '<br /><input type="submit" value="Генерировать" /></form>';
		
		if ($ad_text) {
			require_once plugin_dir_path( __FILE__ ).'/includes/Natty/TextRandomizer.php';
			
			$tRand = new Natty_TextRandomizer($ad_text);
			$result_text .=  '<p>Число всех возможных вариантов: <strong>' . $tRand->numVariant(). '</strong>. Из них случайные 10:</p>';
			
			for ($i=0; $i<10; ++$i) {
				$result_text .=  '<p>'.htmlspecialchars($tRand->getText()).'</p><hr />';
			}
		}
		
		return $result_text;
	}
	
}

ad_generator_shortcode::init();

