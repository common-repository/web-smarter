<?php 
namespace WebSmarter\Config;
/**
 * Locales Constants
 *
 * @package WebSmarter/Inc
 * 
 */

defined( 'ABSPATH' ) || exit;

if ( ! class_exists( 'Constant' ) ) {

	final class Constant
	{
		protected static $_pluginData;
		/**
		 * Main WebSmarter Instance.
		 *
		 * @since 1.1.5
		 * @static
		 * @see wbs()
		 * @return WebSmarter - Main instance.
		 */
		public static function init() {
			self::$_pluginData = get_file_data( plugin_dir_path( dirname(__FILE__) ).'index.php' , array( 'name'=>'Plugin Name', 'version'=>'Version', 'text'=>'Description','mode'=>'Mode','perfix'=>'Perfix' ,'text_domain'=>'Text Domain','powered_by'=>'Author URI') ,false);

			define( 'PREFIX',  self::$_pluginData['perfix'].'_');
			define( 'CURRENT_URL', (stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']);
			/**
			 *
			 * API constant define
			 *
			 */
			if ( ! defined( 'API_MODE' ) ) {
				define( 'API_MODE' ,strtoupper(self::$_pluginData['mode']));
			}

			if(API_MODE=='DEV'){
				define( 'API_URL', "https://dev.web-smarter.com/" );
			}elseif(API_MODE=='APP'){
				define( 'API_URL', "https://app.web-smarter.com/" );
			}else{
				define( 'API_URL', "http://localhost:8000/" );	
			}
			self::defineConstant();
		}
		protected static function defineConstant(){
			self::allmetatags_constants( 'BASE', dirname(plugin_basename( dirname(__FILE__) ) ));
			self::allmetatags_constants( 'URL', plugin_dir_url( dirname(__FILE__) ) );
			self::allmetatags_constants( 'PATH', plugin_dir_path( dirname(__FILE__) ) );
			self::allmetatags_constants( 'NAME', self::$_pluginData['name'] );
			self::allmetatags_constants( 'VERSION', self::$_pluginData['version'] );
			self::allmetatags_constants( 'TEXT', self::$_pluginData['text'] );
			self::allmetatags_constants( 'TEXT_DOMAIN', self::$_pluginData['text_domain']);
			self::allmetatags_constants( 'POWERED_BY_URI', self::$_pluginData['powered_by']);
			self::allmetatags_constants( 'ASSETS', plugin_dir_url( dirname(__FILE__) ).'assets/');
			self::allmetatags_constants( 'TEMPLATE', plugin_dir_path( dirname(__FILE__) ).'template/');
			self::allmetatags_constants( 'VUE', plugin_dir_path( dirname(__FILE__) ).'vue/');
			self::allmetatags_constants( 'VUE_COMPONENT', plugin_dir_path( dirname(__FILE__) ).'vue/components/');
			self::allmetatags_constants( 'VUE_COMMON_COMPONENT', plugin_dir_path( dirname(__FILE__) ).'vue/components/common');
			/**
			 * setting page constant
			 */
			self::allmetatags_constants( 'SETTING', 'wsp_settings');
			self::allmetatags_constants( 'PAYPAL', 'wsp_paypal_details');
		}
		/**
		 * define constant
		 *
		 */
		protected static function allmetatags_constants( $constant_name, $value ) {
		    $constant_name = PREFIX . $constant_name;
		    if ( !defined( $constant_name ) )
		        define( $constant_name, $value );
		}
	}

	Constant::init();
}



