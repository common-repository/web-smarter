<?php
namespace WebSmarter\Includes;

class Common_Functions
{	
	public $__defaultMethod = "GET";
	public $__paypalDetail = [];
	const BASEURL = API_URL;

	public $__iconUrl = "";

	/**
	 * Constructor
	 *
	 * @access	public
	 * @return	void
	 */
	public function __construct(){
		$this->__iconUrl = WSP_ASSETS. 'images/icon.png';
	}

	/**
	 * Set message in session on error , success and warning all type message.
	 *
	 * @access	public
	 * @param	string	$type			Raw API request string.
	 * @param	string	$msg			Raw API request string.
	 * @return	void
	 */
	public function setMsgs($type,$msg)
	{
		if($type == "success")
		{
			$_SESSION['wsp_success'] = $msg;
		}
		else
		{
			$_SESSION['wsp_error'] = $msg;
		}
	}	

	/**
	 * Get message from session and unset session.
	 *
	 * @access	public
	 * @return	string	$html 			Returns the raw string response
	 */
	public function getMsgs()
	{
		$html = '';
		
		if(isset($_SESSION['wsp_error'])){
			$error = $_SESSION['wsp_error'];
			$html = '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible error">'.
					'<p><strong>'.$error.'.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.
					'Dismiss this notice.</span></button></div>';
			unset($_SESSION['wsp_error']);
		}
		elseif(isset($_SESSION['wsp_success'])){
			$success = $_SESSION['wsp_success'];
			$html = '<div id="setting-error-settings_updated" class="updated settings-error notice is-dismissible">'.
					'<p><strong>'.$success.'.</strong></p><button type="button" class="notice-dismiss"><span class="screen-reader-text">'.
					'Dismiss this notice.</span></button></div>';
			unset($_SESSION['wsp_success']);
		} 
		echo $html;
	}	

	/**
	 * Get web-service url.
	 *
	 * @access	public
	 * @param	string 	$name 		Raw API name string
	 * @return	string	$url 		Returns the raw string response
	 */
	public function webServiceUrl($name)
	{
		return self::BASEURL."api/".$name;
	}

	/**
	 * CURL function to use third party API and get response.
	 *
	 * @access	public
	 * @param	string 	$url 		Raw API url string
	 * @param	array 	$aPost 		Raw API postdata array
	 * @return	string	$response 	Returns the raw string response
	 */
	public function postUrlUsingCurl($aUrl,$aPostData = array(),$aHeader = array())
	{
		if(empty($aHeader) || !isset($aHeader['AccessToken'])){
			$aHeader['AccessToken'] = get_user_meta(get_current_user_id(),'admin_token',true);
		}
		$args = array('method' => $this->__defaultMethod, 'sslverify' => false,'headers'=>$aHeader);
		
		if($aPostData)
		{
			$args['body'] = $aPostData;
		}


		$aResponse = wp_remote_request($aUrl,$args);
		$cData = wp_remote_retrieve_body( $aResponse );
		return $cData;
	}


	/**
	 * send mail to given emails .
	 *
	 * @access	public
	 * @param	array 	$aTo 		Raw API aTo array
	 * @param	string 	$subject 	Raw API subject string
	 * @param	string 	$content 	Raw API cotent string
	 * @param	optional
	 * @return	string	$content 	Returns the raw string content
	 */
	public function sendEmail($aTo,$aSubject,$aContent,$aOpts = array())
	{
		$headers = array('Content-Type: text/html; charset=UTF-8');		 
		wp_mail( $aTo, $aSubject, $aContent, $headers );
	}
	/**
	 * get admin details.
	 *
	 * @access	public
	 * @param   string  $field  (optional) param
	 * @return	array	$aVals 	Returns the raw array admin data
	 */
	public function wpDefaultValues($field='')
	{
		$current_user = wp_get_current_user();
		$aVals = array();
		$aVals['admin_email'] = get_option('admin_email');
		$aVals['blog_url'] = get_home_url();
		if($current_user)
		{
			$aVals['admin_token'] = get_user_meta(get_current_user_id(),'admin_token',true);
			$aVals['admin_login'] = $current_user->user_login;
			$aVals['admin_fname'] = $current_user->user_firstname ? $current_user->user_firstname : $current_user->user_login;
			$aVals['admin_lname'] = $current_user->user_lastname  ? $current_user->user_lastname : $current_user->user_login;
		}
		if(!empty($field) && isset($aVals[$field])){
			return $aVals[$field];
		}
		return $aVals;
	}
	/**
	 * set email content with html tags.
	 *
	 * @access	public
	 * @param	
	 * @param	
	 * @return	string	$content 	Returns the raw string content
	 */
	public function setEmailContent($aType,$aVals = array())
	{
		$eMailContent = "<div>";
		$eMailContent .= "<p>Hello,</p>";
		if($aType == "pluginInstall")
		{			
			$eMailContent .= "<p>Thank you for installing our plugin.</p>";
		}

		$eMailContent .= "</div>";
		return $eMailContent;
	}	
    /**
	 * read new notification and mark new notifcation isread.
	 *
	 * @access	public
	 * @param	string 	$email		Raw API email atring
	 * @param	number 	$task_id	Raw API task_id numeric
	 * @return	void
	 */
    public function readNotification($email,$task_id){
    	$curlUrl = $this->webServiceUrl("task/readnotification/".$email.'/'.$task_id);
		$aResponse = $this->postUrlUsingCurl($curlUrl);
		$aJson = json_decode($aResponse);
    }
    /**
	 * Get Unopened task count from this current user.
	 *
	 * @access	public
	 * @param	string 	$email		Raw API email atring
	 * @param	number 	$task_id	Raw API task_id numeric
	 * @return	number	$count 		Returns the raw number count
	 */
    public function getUnOpnedTaskCount($email,$task_id=''){
    	if(!empty($task_id)){
    		$this->readNotification($email,$task_id);
    	}
		$curlUrl = $this->webServiceUrl("task/unopned/".$email);
		$aResponse = $this->postUrlUsingCurl($curlUrl);
		$aJson = json_decode($aResponse);
		$count = 0;
		if($aJson && $aJson->code > 0)
		{			
			$count = $aJson->count;
		}
		return $count;
	}

	/**
	 * save transaction data at web-smarter end through this function.
	 *
	 * @access	public
	 * @param	array 	$saveData	Raw API saveData array
	 * @param	array 	$extraData	Raw API extraData array
	 * @return	array	$response 	Returns the raw array response
	 */
	public function saveTransactionData($saveData,$type='subscription'){
		$this->__defaultMethod = "POST";
		$aData = $this->wpDefaultValues();
		$curlEUrl = $this->webServiceUrl("save_subscription_transaction/".$aData['admin_email'].'/'.$type);
		$aResponse = $this->postUrlUsingCurl($curlEUrl,$saveData);
		return $aResponse = json_decode($aResponse,true);
	}
	/**
	 * get PAYPAL API details from Laravel.
	 *
	 * @access	public
	 * @param	string 	$type		Raw API type string
	 * @return	array	$response 	Returns the raw array response
	 */
	public function getPaypalAPI($type='all'){
		$curlEUrl = $this->webServiceUrl("paypal_details").'?type='.$type; 
		$this->__defaultMethod = 'GET';
		$API = $this->postUrlUsingCurl($curlEUrl);
		return json_decode($API,true);
	}
	/**
	 * Get all statuses of tasks.
	 *
	 * @access	public
	 * @param	none
	 * @return	array	$response 	Returns the raw array response
	 */
	public function getTaskStatus()
	{
		$aRows = array();
		$curlUrl = $this->webServiceUrl("task_status");
		$aResponse = $this->postUrlUsingCurl($curlUrl);
		$aJson = json_decode($aResponse);
		if($aJson->status)
		{			
			$aRows = $aJson->status;
		}
		return $aRows;
	}
}