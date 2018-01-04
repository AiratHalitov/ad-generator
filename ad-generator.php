<?php
/**
 * Plugin Name: Ad Generator
 * Plugin URI:  https://github.com/AiratHalitov/ad-generator
 * Description: Ad Generator / Text Randomizer
 * Author:      Airat Halitov
 * Author URI:  https://airat.biz
 * Version:     1.0.2
 * Text Domain: ad-generator
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// https://habrahabr.ru/company/dataart/blog/265245/
// [ad_generator]

class ad_generator_shortcode {
	
	static $add_script = false;
	static $max_res = 10;
	
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
			$result_text .=  '<textarea name="ad_text" cols="100" rows="10" autofocus maxlength="10000" placeholder="Введите шаблон">' . htmlspecialchars($ad_text) . '</textarea>';
		} else {
			$result_text .=  '<textarea name="ad_text" cols="100" rows="10" autofocus maxlength="10000" placeholder="Введите шаблон">{Рандомизатор|Рандомайзер} {|текста}</textarea>';
		} 
		
		$result_text .=  '<br /><button class="btn btn-large btn-primary" type="submit">Генерировать</button></form>';
		if ($ad_text) $result_text .=  '<a href='.$_SERVER['REQUEST_URI'].'>Очистить и начать заново</a>';
		
		if ($ad_text && self::$add_script) {
			require_once plugin_dir_path( __FILE__ ).'/includes/Natty/TextRandomizer.php';
			
			$tRand = new Natty_TextRandomizer($ad_text);
			$num_var = $tRand->numVariant();
			
			if ($num_var > 1) {
				$result_text .=  '<p class="alert alert-info">Число всех возможных вариантов: <strong>' . $num_var . '</strong>. Вот случайные <strong>' . self::$max_res. '</strong> из них (возможны повторения):</p>';
				
				for ($i = 0; $i < self::$max_res; ++$i) {
					$result_text .=  '<p>'.nl2br(htmlspecialchars($tRand->getText())).'</p><hr />';
				}
			} else {
				$result_text .=  '<p class="alert alert-info">Только <strong>1</strong> возможный вариант:</p>';
				$result_text .=  '<p>'.nl2br(htmlspecialchars($tRand->getText())).'</p><hr />';
				
			}
			
		}
		
		$result_text .= '<br /> <p>Страница проекта на GitHub: <a href="https://github.com/AiratHalitov/ad-generator" target=_blank>https://github.com/AiratHalitov/ad-generator</a>';
		
		return $result_text;
	}
	
}

ad_generator_shortcode::init();

