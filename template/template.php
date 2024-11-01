<?php if ( ! defined( 'ABSPATH' ) ) exit; 

if($aParams['view'] == 'html'){
	if(isset($_SESSION['wps_success']) && $_SESSION['wps_success']) { ?>
		<div class="updated"><p><?php echo $_SESSION['wps_success']; ?></p></div>
		<?php unset($_SESSION['wps_success']);
	}
 	if(isset($_SESSION['wps_error']) && $_SESSION['wps_error']) { ?>
		<div class="error"><p><?php echo $_SESSION['wps_error']; ?></p></div>
		<?php unset($_SESSION['wps_error']);
	} ?>
	<div class="wrap">
		<div class="wps_page_wrap">
			<div class="postbox">
			    
			    <div class="wps_top_bar">
			    	<div class="wps_logo">
			       		<img class="wps_web_logo" src="<?= WSP_ASSETS.( 'images/web-logo.png');?>" alt="logo">
			        </div>
			        <?php if(isset($_REQUEST['page']) && in_array($_REQUEST['page'],['wps_task_manager_view_task','wps_task_manager_transactions'])){
			        	?>
			        <div class="wps_btn">
			        	<a href="https://tawk.to/chat/5b06c2aa10b99c7b36d44496/default/?$_tawk_popout=true" target="_blank">
							<span class="wps_needHelp"><?= __("Need Help?",WSP_TEXT_DOMAIN);?> <img src="<?= WSP_ASSETS .( 'images/help.png');?>" alt="help"></span>
						</a>
					</div>
					<?php }?>
					<div class="clear"></div>		
			    </div>		        
			    <?php include_once WSP_TEMPLATE.$aParams['template'].".php"; ?>
			</div>
		</div>
	</div>
<?php 
}else{
	include_once WSP_TEMPLATE.$aParams['template'].".php";
} ?>