<?php
namespace WebSmarter;
/**
 * Plugin Name: Web Smarter
 * Plugin URI: 
 * Description: Web-smarter allow the website admin to gather all his tasks for the website from within the website.Any change, bug or maintenance task that you have can be managed and monitored through this extension.
 * Version: 1.2.3
 * Perfix: WSP
 * Author: websmarter
 * Author URI:  https://www.web-smarter.com
 * Mode: app
 * Text Domain: websmarter
 * Domain Path: /i18n/languages
 */
error_reporting(E_ALL);

if ( ! defined( 'ABSPATH' ) ) {
	die("You can't access this file directly"); // disable direct access
} 
use WebSmarter\Includes\Common_Functions;

// Define WSP_PLUGIN_FILE Constants. 
require_once( trailingslashit( dirname( __FILE__ ) ) . 'config/constant.php' );

if ( ! class_exists( 'WebSmarter' ) ) {

	final class WebSmarter
	{		
		protected static $_instance = null;
		public $__webSmarterIcon = "";
		public $__unopenTask = 0;
		public $functionClass;
		public $_page = array('wps_task_manager_settings','wps_task_manager_view_task','wps_task_manager_transactions','wps_task_manager_board');

		/**
		 * Main WebSmarter Instance.
		 *
		 * @since 1.1.5
		 * @static
		 * @see wbs()
		 * @return WebSmarter - Main instance.
		 */
		public static function init() {
			if ( is_null( self::$_instance ) ) {
				self::$_instance = new self();
			}
			return self::$_instance;
		}
		public function __construct()
		{
			$this->aParams = array();	
			
			add_action('init', array($this, 'wps_task_manager_init'));
			
			add_action( 'admin_menu', array(&$this, 'wps_task_manager_admin_menu'));
			add_action( 'wp_footer',array(&$this, 'wps_task_manager_footer'));
			add_action( 'admin_footer',array(&$this, 'wps_task_manager_admin_footer'));
			add_action( 'admin_enqueue_scripts', array(&$this,'wps_admin_enqueue_scripts'));
			add_action( 'wp_head',array(&$this, 'wps_admin_enqueue_scripts'));

			add_action( 'wp_ajax_wps_task_manager_add_ajax', array(&$this,'wps_task_manager_add_ajax' ));
			add_action('wp_ajax_nopriv_wps_task_manager_add_ajax', array(&$this,'wps_task_manager_add_ajax'));

			/*add_action( 'wp_ajax_wps_task_get_listing', array(&$this,'wps_task_get_listing' ));
			add_action('wp_ajax_nopriv_wps_task_get_listing', array(&$this,'wps_task_get_listing'));*/

			add_action( 'wp_ajax_wps_task_upload_image', array(&$this,'wps_task_upload_image_ajax' ));
			add_action('wp_ajax_nopriv_wps_task_upload_image', array(&$this,'wps_task_upload_image_ajax'));

			add_action( 'wp_ajax_task_popup', array(&$this,'task_popup' ));
			add_action('wp_ajax_nopriv_task_popup', array(&$this,'task_popup'));

			register_activation_hook( __FILE__, array(&$this,'activation_wps_plugin') );
			register_deactivation_hook( __FILE__, array(&$this,'deactivation_wps_plugin'));
			register_uninstall_hook( __FILE__, 'uninstall_wps_plugin');
		}
		/**
	     * Plugin Init.
	     * 
	     * Used to init all resources for plugin use.
	     *
	     * @access  public
	     * @param   none 
	     * @return  none
	     */
		public function wps_task_manager_init()
		{
			global $wpsTaskFunObj;
			ob_start();
		    if(!session_id()) {
		        session_start();
		    }
		    // Include the autoloader so we can dynamically include the rest of the classes.
			require_once( trailingslashit( dirname( __FILE__ ) ) . 'inc/autoloader.php' );


			$this->functionClass = $wpsTaskFunObj = new Common_Functions;	
			$this->__webSmarterIcon = $wpsTaskFunObj->__iconUrl;
		}
		/**
	     * Plugin Menu.
	     * 
	     * Used to add menu in admin panel for plugin.
	     *
	     * @access  public
	     * @param   none 
	     * @return  none
	     */
		public function wps_task_manager_admin_menu()
		{	
			$data = $this->functionClass->wpDefaultValues();
			$this->__unopenTask = $this->functionClass->getUnOpnedTaskCount($data['admin_email'],intval(isset($_REQUEST['task_id']) ? $_REQUEST['task_id'] : 0));

			add_menu_page(__( 'Web-Smarter', WSP_TEXT_DOMAIN ), sprintf(__( 'Web-Smarter %s', WSP_TEXT_DOMAIN ),($this->__unopenTask>0?'<span class="update-plugins count-4"><span class="plugin-count">'.$this->__unopenTask.'</span></span>':'')), 'manage_options', 'wps_task_manager_view_task',array($this, 'wps_task_manager_view_task'),$this->__webSmarterIcon);

			add_submenu_page('wps_task_manager_view_task', __( 'Task Central', WSP_TEXT_DOMAIN ),__( 'Task Central', WSP_TEXT_DOMAIN ), 'manage_options', 'wps_task_manager_view_task',array($this, 'wps_task_manager_view_task'));
			
			add_submenu_page('wps_task_manager_view_task', __( 'My Account', WSP_TEXT_DOMAIN ),__( 'My Account', WSP_TEXT_DOMAIN ), 'manage_options', 'wps_task_manager_settings',array($this, 'wps_task_manager_settings'));
			
			
			add_submenu_page('wps_task_manager_view_task',__( 'View Report', WSP_TEXT_DOMAIN ), __( 'View Report', WSP_TEXT_DOMAIN ), 'manage_options', 'wps_task_manager_transactions',array($this, 'wps_task_manager_transactions'));

			add_submenu_page('',__( 'View Task Board', WSP_TEXT_DOMAIN ), __( 'View Task Board', WSP_TEXT_DOMAIN ), 'manage_options', 'wps_task_manager_board',array($this, 'wps_task_manager_board'));
			//add_submenu_page('',__( 'Worker List', WSP_TEXT_DOMAIN ), __( 'Worker List', WSP_TEXT_DOMAIN ), 'manage_options', 'wps_task_manager_workers',array($this, 'wps_task_manager_workers'));
			//add_submenu_page('',__( 'Discussion', WSP_TEXT_DOMAIN ), __( 'Discussion', WSP_TEXT_DOMAIN ), 'manage_options', 'wps_task_manager_discussion',array($this, 'wps_task_manager_discussion'));	
			//add_submenu_page('',__( 'Extra', WSP_TEXT_DOMAIN), __( 'Extra', WSP_TEXT_DOMAIN ), 'manage_options', 'wps_task_manager_extra',array($this, 'wps_task_manager_extra'));	
		}
		/**
	     * Plugin Page.
	     * 
	     * Used to call plugin custom pages.
	     *
	     * @access  public
	     * @param   mixed[] 	$aParams 	an array 
	     * @return  none
	     */
		public function wps_task_manager_workers($aParams)
		{
			$this->set_template('worker',$aParams,'html','Worker List');
		}
		public function wps_task_manager_view_task($aParams)
		{
			$this->set_template('view',$aParams,'html','All tasks');
		}
		public function wps_task_manager_transactions($aParams)
		{
			$this->set_template('transaction',$aParams,'html','Transaction History');
		}
		public function wps_task_manager_settings($aParams)
		{
			$this->set_template('index',$aParams,'html','My account');
		}
		public function wps_task_manager_footer($aParams)
		{ 
			$this->set_template('footer_logo',$aParams);
		}	
		public function wps_task_manager_admin_footer($aParams)
		{ 
			$this->set_template('footer_admin',$aParams,"nohtml");
		}
		public function wps_task_manager_board($aParams)
		{ 
			$this->set_template('task_board',$aParams,"html","Task Listing Board");
		}

		/*public function wps_task_manager_discussion($aParams)
		{
			$this->set_template('discussion',$aParams,'html','Task Detail');
		}
		public function wps_task_manager_extra($aParams)
		{
			$this->set_template('extra',$aParams,'html','Task Detail');
		}*/	
		/**
	     * Plugin Css/Js Add.
	     * 
	     * Used to add css and js of plugin.
	     *
	     * @access  public
	     * @param   none
	     * @return  none
	     */
		public function wps_admin_enqueue_scripts()
		{ 

			wp_enqueue_script('media-upload');
	        wp_enqueue_script('thickbox');
	        wp_enqueue_style('thickbox');

	        wp_enqueue_style('wps-task-style-font', 'https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css'  , array(), true);
			$wpVals = $this->functionClass->wpDefaultValues();
			if(isset($_REQUEST['page']) && in_array($_REQUEST['page'],$this->_page)){
				wp_enqueue_style('wps-task-bootstrap-min',API_URL.'css/bootstrap.min.css',array(),true);
			}
			wp_enqueue_style('wps-task-style-css',API_URL.'css/common.css',array(),true);
			wp_enqueue_style('wps-task-style', WSP_ASSETS . 'css/style.css' , array(), true);
			//https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/css/select2.min.css
			//wp_enqueue_style('wps-select2-css', plugin_dir_url( __FILE__ ) . 'src/select2/select2.min.css' , array(), true);
			wp_enqueue_style('wps-task-darkroom', WSP_ASSETS . 'css/darkroom/darkroom.css' , array(), true);


			wp_enqueue_script('wps-task-html2canvas',WSP_ASSETS . 'js/html2canvas/html2canvas.js'   , array(), true,true);
			wp_enqueue_script('wps-task-fabric',WSP_ASSETS . 'js/darkroom/fabric.js'   , array(), true,true);
			wp_enqueue_script('wps-task-darkroom-js', WSP_ASSETS . 'js/darkroom/darkroom.js'   , array(), true,true);
			wp_enqueue_script('wps-task-custom-fabric',WSP_ASSETS . 'js/darkroom/custom.js'   , array(), true,true);

			wp_enqueue_script('wps-task-moment-js',WSP_ASSETS . 'js/moment/moment.min.js'   , array(), true,true);

			if(isset($_REQUEST['page']) && $_REQUEST['page']=='wps_task_manager_transactions'){
				wp_enqueue_script('wps-task-daterangepicker-js',WSP_ASSETS . 'js/daterangepicker/daterangepicker.min.js'   , array(), true,true);
				wp_enqueue_style('wps-task-daterangepicker-css', WSP_ASSETS . 'css/daterangepicker/daterangepicker.css' , array(), true);
			}
			wp_enqueue_script('wps-task-script',WSP_ASSETS . 'js/fun.js'   , array(), true,true);
			wp_localize_script('wps-task-script','task_ajax_object',array('url'=>admin_url('admin-ajax.php')));
			//https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.5/js/select2.full.min.js
			wp_enqueue_script('select2-library-js',WSP_ASSETS . 'js/select2/select2.full.min.js'   , array(), true,true);

			if(isset($_REQUEST['page']) && $_REQUEST['page']=='wps_task_manager_view_task'){
				wp_enqueue_script('vue-library-js','https://unpkg.com/vue@^2.5/dist/vue.js'   , array(), true,true);
				wp_enqueue_script('vue-paypal-checkout',WSP_ASSETS . 'js/vue/vue-paypal-checkout.min.js'   , array(), true,true);
				//wp_enqueue_script('vue-paypal-checkout','https://unpkg.com/vue-paypal-checkout/dist/vue-paypal-checkout.min.js'   , array(), true,true);
				//wp_enqueue_script('moment-local','https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.22.2/moment-with-locales.js'   , array(), true,true);
				wp_enqueue_style('wps-task-jkanban', API_URL . 'css/jkanban/jkanban.min.css' , array(), true);
				wp_enqueue_script('wps-task-jkanban',API_URL . 'js/jkanban/jkanban.js'   , array(), false,true);
				wp_enqueue_script('vue-component',WSP_ASSETS . 'js/vue/index.js'   , array(), false,true);
				// Localize the script with new data
				$aWspSetting = get_option('wsp_settings');
				$translation_array = array('baseUrl'=>Common_Functions::BASEURL,
										'currentUData'=>json_encode($wpVals),
										'pluginUrl'=>plugins_url( 'src/', dirname(__FILE__) ),
										'defaultTeam'=>isset($aWspSetting['default_team'])?$aWspSetting['default_team']:'',
										'assignAgency'=>isset($aWspSetting['agency_ref_id'])?true:false);
				wp_localize_script( 'vue-component', 'vue_js_object', $translation_array );
			}
			$translation_array = array('baseUrl'=>Common_Functions::BASEURL,'addClass'=>false);
			if(isset($_REQUEST['page']) && in_array($_REQUEST['page'],$this->_page)){
				$translation_array = array('baseUrl'=>Common_Functions::BASEURL,'addClass'=>true);
			}

			wp_localize_script( 'wps-task-script', 'web_smarter_js_object', $translation_array );
		}
		/**
	     * Plugin Admin Bar.
	     * 
	     * Used to customize plugin toolbar at admin panel.
	     *
	     * @access  public
	     * @param   obj 	$wp_admin_bar 	An instance of the global object WP_Admin_Bar
	     * @return  none
	     */
		public function wps_task_manager_modify_admin_bar($wp_admin_bar){
			global $wp;
			$current_url = (stripos($_SERVER['SERVER_PROTOCOL'],'https') === true ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			$args = array(
			  'id'    => 'my_page',
			  'title' => '<img src="'.$this->__webSmarterIcon.'" alt="Plugin Icon" style="vertical-align:middle;margin-right:5px;"> '.__( 'Task', WSP_TEXT_DOMAIN ),
			  'parent' => "new-content",
			  'href'  => '#',
			  'meta'  => array( 'class' => '','data-action'=>'' ),
			  
			 );
			 $wp_admin_bar->add_node( $args );
		}
		/**
	     * Task Add Request.
	     * 
	     * Used to add task data at APP end.
	     *
	     * @access  public
	     * @param   none
	     * @return  string $aResponse  string consist json array.
	     */
		public function wps_task_manager_add_ajax()
		{
			global $wpsTaskFunObj;
			if(( isset( $_POST['wps_websmarter_addtask_nonce_field'] )  && wp_verify_nonce( $_POST['wps_websmarter_addtask_nonce_field'], 'wps_websmarter_addtask_nonce_action' ) ))
			{
				//$aVals = $_POST;
				$aVals =  array_map( 'sanitize_text_field', wp_unslash( $_POST['val'] ) );	
				$curlUrl = $wpsTaskFunObj->webServiceUrl("tasks/create");
				$wpVals = $wpsTaskFunObj->wpDefaultValues();
				$aWspSetting = get_option('wsp_settings');
				$aPostData = array("email" => $wpVals['admin_email'],"name" =>$aVals['task_title'],"url" =>$aVals['task_url'],
					"description" =>$aVals['task_description'],'image'=>$aVals['image_file_name'],'priority'=>$aVals['task_priority'],'team'=>isset($aWspSetting['default_team'])?$aWspSetting['default_team']:'');
				
				$wpsTaskFunObj->__defaultMethod = "POST";
				$aResponse = $wpsTaskFunObj->postUrlUsingCurl($curlUrl,$aPostData,['AccessToken'=>$wpVals['admin_token']]);
				
				$response = json_decode($aResponse,true);
				
				//Unlink all images
				//if($response['code']==1){
					$prefix = "user_".get_current_user_id()."_";
					$upload_dir  = wp_upload_dir();
					$upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;
					$mask = $upload_path.$prefix.'*.*';
					//array_map('unlink', glob($mask));
					unlink($aVals['image_file_name']);
				//}
				echo $aResponse;
				exit;
			}	
		}
		/*
		public function wps_task_get_listing(){
			global $wpsTaskFunObj;
			$response = '';
			$aVals = $_POST;
			$aVals =  array_map( 'sanitize_text_field', wp_unslash( $_POST ) );	
			$status = 'all';
			if(!empty($aVals)){
				if(isset($aVals['status'])){
					$status = $aVals['status'];
				}
				$wpVals = $wpsTaskFunObj->wpDefaultValues();
				$apiType = isset($aVals['api'])?$aVals['api']:'';
				$aPostData = array();
				switch ($apiType) {
					case 'details':
						$curlUrl = $wpsTaskFunObj->webServiceUrl("task/".$wpVals['admin_email']."/".isset($aVals['tId'])?$aVals['tId']:'');		
						break;
					default:
						$curlUrl = $wpsTaskFunObj->webServiceUrl("tasks/".$wpVals['admin_email']."/".$status);	
						break;
				}
				$response = $wpsTaskFunObj->postUrlUsingCurl($curlUrl,$aPostData);
			}
			echo $response;
			exit;
		}*/
		/**
	     * Task Upload Image
	     * 
	     * Used to upload task image by ajax request.
	     *
	     * @access  public
	     * @param   none
	     * @return  string $response  string consist json array.
	     */
		public function wps_task_upload_image_ajax()
		{
			WP_Filesystem($_SESSION['creds']);

	        global $wp_filesystem;
			//global $wp_filesystem;
			$prefix = "user_".get_current_user_id()."_";
			$upload_dir  = wp_upload_dir();
			//$upload_path = str_replace( '/', DIRECTORY_SEPARATOR, $upload_dir['path'] ) . DIRECTORY_SEPARATOR;
			$upload_path = trailingslashit( wp_upload_dir()['path'] ) ;

			if (! is_dir($upload_path)) {
		       wp_mkdir_p( $upload_path );
		       //mkdir( $upload_path, 0755 );
		    }
			$aVals =  array_map( 'sanitize_text_field', wp_unslash( $_POST ) );	

			if(isset($aVals['base64data']) && !empty($aVals['base64data'])){
				$data = $aVals['base64data'];
				list($type, $data) = explode(';', $data);
				list(, $data)      = explode(',', $data);
				$data = base64_decode($data);
				$fileName = time().'.png';
				
				$hashed_filename = $prefix.md5( microtime() ) . '_' . $fileName;
				if(isset($aVals['file_name']) && !empty($aVals['file_name'])){
					$mask = $upload_path.$prefix.'*.*';
					array_map('unlink', glob($mask));
				}

				// Save the image in the uploads directory.
				$upload_file = file_put_contents( $upload_path . $hashed_filename, $data );
				if(!$upload_file){
					echo json_encode(array());
					exit;
				}
				//print_r($rData);
			}else{
				$aFile =  array_map( 'sanitize_text_field', wp_unslash( $_FILES['file'] ) );	
				$filename = $aFile['name'];
				$imageFileType = pathinfo($filename,PATHINFO_EXTENSION);
				$fileName = time().'.'.$imageFileType;
				$hashed_filename = $prefix.md5( microtime() ) . '_' . $fileName;
				if(isset($aVals['file_name']) && !empty($aVals['file_name'])){
					$mask = $upload_path.$prefix.'*.*';
					array_map('unlink', glob($mask));
				}
				if(!move_uploaded_file($aFile['tmp_name'],$upload_path . $hashed_filename)){
					echo json_encode(array('message'=> "There is a problem in your rquest."));
					exit;
				}

			}
			echo json_encode(array('image'=> $upload_dir['url'].'/'.$hashed_filename,'fileName'=>$upload_dir['url'].'/'.$hashed_filename));
			exit;
		}
		/**
	     * Task PopUp
	     * 
	     * Used to open add task form in popup.
	     *
	     * @access  public
	     * @param   none
	     * @return  string $response  string consist json array.
	     */
		public function task_popup(){
			ob_start();	
			$id = "wps-admin-task-popup";
			$link = sanitize_text_field($_POST['link']);
			include "template/ajax_popup_content.php";		
			$temp = ob_get_contents();		
			ob_get_clean();
			echo json_encode(array('html'=>$temp,'id'=>$id));
			exit;
		}
		/**
	     * Set Template.
	     * 
	     * Used to set teamplate file on page.
	     *
	     * @access  public
	     * @param   string 		$tempName	template name which we want to set on page.
	     * @param   mixed[]		$aOpts 		an array params which we get on page.
	     * @param   string 		$view 		view type.
	     * @param   string 		$title 		template title.
	     * @return  string 		$html 		html content which we get form template.
	     */
		public function set_template($tempName,$aOpts,$view = '',$title = '')
		{		
			global $wpsTaskFunObj;
			$aParams['template'] = $tempName;
			$aParams['view'] = $view;
			$aParams['title'] = $title;
			$aVars = $aOpts;// ? array_merge($this->aParams,$aOpts) : $this->aParams;
			ob_start();	
			include "template/template.php";		
			return ob_get_contents();		
			ob_get_clean();
		}
		/**
	     * Activate WebSmarter plugin.
	     * 
	     * Used to activatae that plugin and call web-service to create user at App end.
	     *
	     * @access  public
	     * @param   none
	     * @return  none
	     */
		public function activation_wps_plugin()
		{
			global $wpdb,$wpsTaskFunObj;	
			$this->wps_task_manager_init();	
			$wpVals = $wpsTaskFunObj->wpDefaultValues();
			$curlUrl = $wpsTaskFunObj->webServiceUrl("users/create");
			$aPostData = array("email" => $wpVals['admin_email'],"name" =>$wpVals['admin_fname']." ".$wpVals['admin_lname'],"domain"=>home_url());
			$wpsTaskFunObj->__defaultMethod = "POST";
			$aResponse = $wpsTaskFunObj->postUrlUsingCurl($curlUrl,$aPostData,['AccessToken'=>$wpVals['admin_token']]);
			$response = json_decode($aResponse,true);
			
			if(trim($response['code']) == 0){ 
				deactivate_plugins( plugin_basename( __FILE__ ) );
				//set_transient( 'admin-notice-example', true, 5 );
				wp_die( __( 'Plugin could not be activated because '.$response['message'], 'my-plugin' ), 'Plugin dependency check', array( 'back_link' => true ) );
			}else{
				if(isset($response['user']['access_token']))
					update_user_meta(get_current_user_id(), "admin_token", $response['user']['access_token']);

				$option = get_option(WSP_SETTING);
				if(empty($option)){
					$option = [];
				}
				//$paypal = [];
				if(isset($response['user']['wp_settings'])){
					if(isset($response['user']['wp_settings']['paypal'])){
						//$paypal = $response['user']['wp_settings']['paypal'];
						update_option(WSP_PAYPAL,$response['user']['wp_settings']['paypal']);
						unset($response['user']['wp_settings']['paypal']);
					}
					$aVals = $response['user']['wp_settings'];
					$option = array_merge($option,$aVals);
				}
				if(!isset($option['default_team'])){
					$option['default_team'] = $response['default_team'];
				}
				if(!empty($option)){
					update_option(WSP_SETTING,$option);
				}
			}
		}
		/**
	     * DeActivate WebSmarter plugin.
	     * 
	     * Used to deactivate that plugin.
	     *
	     * @access  public
	     * @param   none
	     * @return  none
	     */
		public function deactivation_wps_plugin(){
			global $wpsTaskFunObj;
			$wpVals = $wpsTaskFunObj->wpDefaultValues();
			$curlUrl = $wpsTaskFunObj->webServiceUrl("users/plugin_uninstall/".$wpVals['admin_email']);
			$aResponse = $wpsTaskFunObj->postUrlUsingCurl($curlUrl,[],['AccessToken'=>$wpVals['admin_token']]);
			//$response = json_decode($aResponse,true);	
		}

		/**
	     * UnInstall WebSmarter plugin.
	     * 
	     * Used to delete plugin varibale which was created for plugin uses.
	     *
	     * @access  public
	     * @param   none
	     * @return  none
	     */
		public function uninstall_wps_plugin(){

			delete_option( WSP_SETTING );
			delete_option( WSP_PAYPAL );
		}		
	}
}

/**
 * Plugin Object.
 * 
 * Used to call plugin init functin for access in entire site.
 *
 * @access  public
 * @param   none
 * @return  none
 */
function wbs() {
	return WebSmarter::init();
}

// Global for backwards compatibility.
$GLOBALS['websmarter'] = wbs();