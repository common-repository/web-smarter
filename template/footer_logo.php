<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
$aWspSetting = get_option('wsp_settings');

if(!isset($aWspSetting['show_powered_by']) || (isset($aWspSetting['show_powered_by']) && $aWspSetting['show_powered_by']==1)) { 	?>
	<div class="wpsTaskFooterLogo">
		<a href="<?= WSP_POWERED_BY_URI;?>" target="_blank">
		<?php echo __( 'Powered by', WSP_TEXT_DOMAIN ); ?>&nbsp;&nbsp; <img src="<?= WSP_ASSETS.( 'images/web-logo.png' );?>" width="100px" />
		</a>
	</div>
<?php } 

if(current_user_can('administrator') && isset($aWspSetting['add_task_btn_on_front_client']) && $aWspSetting['add_task_btn_on_front_client']) { 
	include_once "popup_content.php";
}elseif(!current_user_can('administrator') && isset($aWspSetting['add_feedback_btn_visitors']) && $aWspSetting['add_feedback_btn_visitors'])
{
	$btnName = __( 'FeedBack', WSP_TEXT_DOMAIN );
	include_once "popup_content.php";
}
?>