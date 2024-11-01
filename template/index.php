<?php 
if ( ! defined( 'ABSPATH' ) ) exit;

use WebSmarter\Includes\Gatway\Paypal\Paypal_Express;

$page_url =  admin_url( 'admin.php?page=wps_task_manager_settings' );
$taskListUrl =  admin_url( 'admin.php?page=wps_task_manager_view_task' );

$wpVals = $wpsTaskFunObj->wpDefaultValues();
$curlViewUrl = $wpsTaskFunObj->webServiceUrl("wp_plugin_setting/all/?email=".$wpVals['admin_email']);
$aResponse = $wpsTaskFunObj->postUrlUsingCurl($curlViewUrl);
$aJson = json_decode($aResponse,true);

if(isset($aJson['wp_settings']['paypal'])){
	unset($aJson['wp_settings']['paypal']);
}
/*echo "<pre>";
print_r($aJson);
die;*/
$aTeam = isset($aJson['aSetting']['assign'])?$aJson['aSetting']['assign']['options']:array(); 
$defaultTeam = isset($aJson['aSetting']['assign'])?$aJson['aSetting']['assign']['default']:''; 
$subscription = isset($aJson['aSetting']['subscription'])?$aJson['aSetting']['subscription']:array();
$currency = isset($aJson['aSetting']['currency'])?$aJson['aSetting']['currency']:'';
$periodArray = isset($aJson['aSetting']['period'])?$aJson['aSetting']['period']:array();

$aWspSetting = get_option(WSP_SETTING);
if(empty($aWspSetting)){
	$aWspSetting = array();
}
// update WP plugin setting with previous settings (if this plugin installed on site then we need to get last setting)
if(isset($aJson['wp_settings'])){
	$subScription = array();
	$subScriptionApp = array();
	$commonSett = $aWspSetting;
	$commonSettApp = $aJson['wp_settings'];
	

	if(isset($aWspSetting['subscription'])){
		$subScription = $aWspSetting['subscription'];
		unset($commonSett['subscription']);
	}
	if(isset($aJson['wp_settings']['subscription'])){
		$subScriptionApp = $aJson['wp_settings']['subscription'];
		unset($commonSettApp['subscription']);
	}

	$commonSettDiff = array_diff($commonSettApp,$commonSett);
	$subScrSettDiff = array_diff($subScriptionApp,$subScription);
	
	if(!empty($commonSettDiff) || !empty($subScrSettDiff)){
		$aWspSetting = $aJson['wp_settings'];
		update_option(WSP_SETTING,$aWspSetting);
	}elseif(empty($commonSettApp) || empty($subScriptionApp)){
		//$aWspSetting = $aJson['wp_settings'];
		//update_option(WSP_SETTING,$aWspSetting);
	}
}
//General Tab active default
$active = 'general';

//Manage All request actions
if(isset($_REQUEST['subscr']) && !empty($_REQUEST['subscr'])){
	if(isset($aWspSetting['subscription']['subscr_id']) && !empty($aWspSetting['subscription']['subscr_id'])){
		//$paypal = $wpsTaskFunObj->getPaypalAPI("express_checkout"); 
		$paypal = get_option(WSP_PAYPAL);
		$aInitData['sandbox'] = ($paypal['mode']=='sandbox'?true:false);
		$aInitData['API'] = $paypal['api_details'];
		$paypalExpress = new Paypal_Express($aInitData);
		$aData = [];
		$aData = ['subscr_id'=>$aWspSetting['subscription']['subscr_id']];

		switch (sanitize_key($_REQUEST['subscr'])) {
			case "cancel":
				$aData['action'] = 'Cancel';
				$aData['note'] = __('Cancel Subscriptions for next cycle',WSP_TEXT_DOMAIN);
				break;
			default:
				$aData['action'] = $_REQUEST['subscr'];
				$aData['note'] = __('Cancel Subscriptions for next cycle',WSP_TEXT_DOMAIN);			
				break;
		}

		$manageRecurringResponse = $paypalExpress->ManageRecurringPaymentsProfileStatus($aData);
		if(isset($manageRecurringResponse['ACK']) && $manageRecurringResponse['ACK']=="Success"){
			/*$saveData = ['amount'=>$recurring['amount'],'payer_id'=>$recurring['payer_id'],'subscr_id'=>$aRecurringResponse['PROFILEID'],'extra'=>number_format(($recurring['amount']*$aWspSetting['subscription']['bonus']/100),2)];                
			$aResponse = $wpsTaskFunObj->saveTransactionData($saveData);*/
			
			$aWspSetting['subscription'] = [];
			update_option(WSP_SETTING,$aWspSetting);
			update_option(WSP_PAYPAL,'');
			
			$curlViewUrl = $wpsTaskFunObj->webServiceUrl("users/settings");
			$wpsTaskFunObj->__defaultMethod = "POST";
			$params = ['email'=>$wpVals['admin_email'],'detail'=>['wp_settings'=>json_encode($aWspSetting)]];
			$aResponse = $wpsTaskFunObj->postUrlUsingCurl($curlViewUrl,$params);
			
			$wpsTaskFunObj->setMsgs('success',__('Your subscription plan has been cancelled successfully.',WSP_TEXT_DOMAIN));
			
		}else{
			$wpsTaskFunObj->setMsgs('error',__('There is a problem in process.',WSP_TEXT_DOMAIN));
		}
	}
	header('location:'.$page_url);
	exit;
	die();
}elseif(isset($_REQUEST['pay_type']) && isset($_REQUEST['token'])){
	$saveSetting = $notError = false;
	switch (sanitize_key($_REQUEST['pay_type'])) {
		case "success":

			//$paypal = $wpsTaskFunObj->getPaypalAPI("express_checkout"); 
			$paypal = get_option(WSP_PAYPAL);

			$aInitData['sandbox'] = ($paypal['mode']=='sandbox'?true:false);
			$aInitData['API'] = $paypal['api_details'];
			$paypalExpress = new Paypal_Express($aInitData);
			
			$getExpressResponse = $paypalExpress->GetExpressCheckoutDetails(sanitize_text_field($_REQUEST['token']));
			
			if(!isset($getExpressResponse['ERRORS']) && $getExpressResponse['ACK']=="Success")
			{
				 $recurring = ['token'			=>	$getExpressResponse['TOKEN'],
				 			   'payer_id'		=>	$getExpressResponse['PAYERID'],
				 			   'profile_start'	=>	gmdate( 'Y-m-d\TH:i:s\Z'),
				 			   'descriptions'	=>	$aWspSetting['subscription']['desc'],
				 			   'period'			=>	$aWspSetting['subscription']['period'],
				 			   'amount'			=>	$aWspSetting['subscription']['amount'],
				 			   'currency'		=>	$paypal['currency'],
				 			   //'total_cycle'	=>	2
				 			  ];
				$aRecurringResponse = $paypalExpress->CreateRecurringPaymentsProfile($recurring);
				
				if(isset($aRecurringResponse['ACK']) && $aRecurringResponse['ACK']=="Success")
				{           
					$notError = true;
					$aWspSetting['subscription']['subscr_id'] = $aRecurringResponse['PROFILEID'];
					update_option(WSP_SETTING,$aWspSetting);
					$uSetting = get_option(WSP_SETTING);
					$uSetting['paypal'] = $paypal;
                    $saveData = ['amount'=>$recurring['amount'],'payer_id'=>$recurring['payer_id'],'subscr_id'=>$aRecurringResponse['PROFILEID'],'extra'=>number_format(($recurring['amount']*$aWspSetting['subscription']['bonus']/100),2),'details'=>$uSetting];                
					$aResponse = $wpsTaskFunObj->saveTransactionData($saveData);
					if($aResponse['code']==1){
						$saveSetting = true;
						$wpsTaskFunObj->setMsgs('success',__('Your subscription profile has been created successfully.',WSP_TEXT_DOMAIN));
					}else{
						$wpsTaskFunObj->setMsgs('error',__('Your subscription profile has been created successfully. But There is a problem in your saving request.',WSP_TEXT_DOMAIN));
					}		
                }else{
                	$wpsTaskFunObj->setMsgs('error',__('There is a problem in your request.',WSP_TEXT_DOMAIN));
				}
			}else{
				$wpsTaskFunObj->setMsgs('error',__('There is a problem in your request.',WSP_TEXT_DOMAIN));
			}
			break;
		
		default:
			$wpsTaskFunObj->setMsgs('error',__('There is a problem in your request.',WSP_TEXT_DOMAIN));
			break;
	}
	if(!$notError){
		unset($aWspSetting['subscription']);
        update_option(WSP_SETTING,$aWspSetting);
    }
    if(!$saveSetting){
        $curlViewUrl = $wpsTaskFunObj->webServiceUrl("users/settings");
		$wpsTaskFunObj->__defaultMethod = "POST";
		$params = ['email'=>$wpVals['admin_email'],'detail'=>['wp_settings'=>json_encode($uSetting)]];
		$aResponse = $wpsTaskFunObj->postUrlUsingCurl($curlViewUrl,$params);
	}
	header('location:'.$page_url);
	exit;
	die();
}elseif(( isset( $_POST['wps_websmarter_nonce_field'] )  && wp_verify_nonce( $_POST['wps_websmarter_nonce_field'], 'wps_websmarter_nonce_action' ) )){
	if($_POST['setting_type']){
		$agency = isset($aWspSetting["agency_ref_id"])?$aWspSetting["agency_ref_id"]:'';
		$aVals =  array_map( 'sanitize_text_field', wp_unslash( $_POST['val'] ) );	
		$active = $_POST['setting_type'];

		$pay = false;
		
		if($_POST['setting_type']=='general'){
			$aWspSetting['add_task_btn_on_front_client'] = isset($aVals['add_task_btn_on_front_client'])?$aVals['add_task_btn_on_front_client']:0;
			$aWspSetting['add_feedback_btn_visitors'] = isset($aVals['add_feedback_btn_visitors'])?$aVals['add_feedback_btn_visitors']:0;
			$aWspSetting['show_powered_by'] = isset($aVals['show_powered_by'])?$aVals['show_powered_by']:0;
			
			if(!$agency && !empty($aVals['agency_ref_id'])){// || $agency!=$aVals['wps_agency_ref_id']){
				$aWspSetting['agency_ref_id'] = $agency = $aVals['agency_ref_id'];
				
				$curlViewUrl = $wpsTaskFunObj->webServiceUrl("assign_agency/".$wpVals['admin_email']);
				
				$wpsTaskFunObj->__defaultMethod = "POST"; 
				$params = ['agency'=>$agency];
				$aResponse = $wpsTaskFunObj->postUrlUsingCurl($curlViewUrl,$params);
				$aJson = json_decode($aResponse,true);
				if(empty($aJson) || $aJson['code']==0){
					$wpsTaskFunObj->setMsgs(empty(($aJson['code']) || (isset($aJson['code']) && $aJson['code']==0)?'error':"success"),(isset($aJson['message'])?$aJson['message']:"There is a problem in your request."));
					header('location:'.$page_url);
					exit;
				}
			}else{
				$aWspSetting["agency_ref_id"] = $agency;
			}
		}elseif($_POST['setting_type']=='app'){
			if(isset($aVals['default_subscription']) && !empty($aVals['default_subscription'])){
				$amount = $aVals['default_subscription'];
				$period = wp_unslash($_POST['period']);
				$bonus = 0;
				if($aVals['default_subscription']=='custom'){
					$custom = array_map( 'sanitize_text_field', wp_unslash( $_POST['custom'] ) );
					$amount = $custom['amount'];
					$period = $custom['period'];
					$amountBonus = array_filter(explode(',', $custom['bouns']));
					foreach ($amountBonus as $key => $value) {
						list($bonus,$f,$t) = explode('-', $value);
						if(($amount>=$f && $amount<=$t) || ($key==(count($amountBonus)-1) && $amount>=$t)){
							break;
						}
						$bonus = 0;
					}
				}else{
					list($amount,$bonus) = explode('-', $aVals['default_subscription']);
				}
				$pay = true;
				$aVals['subscription'] = ['amount'=>$amount,'custom'=>($aVals['default_subscription']=='custom'?1:0),'status'=>0,'period'=>$period,'subscr_id'=>'','date'=>date("Y-m-d h:i:s a"),'bonus'=>$bonus,'currency'=>$currency];
				//$aVals['subscription'] = ['amount'=>'5','custom'=>0,'status'=>0,'period'=>'Day','subscr_id'=>''];
				$aVals['subscription']['desc'] = 'Subscription of '.$currency.$aVals['subscription']['amount'].' a '.$aVals['subscription']['period'].' - '.uniqid();
				//$saveAppSetting['subscription'] = $aVals['subscription'];
				unset($aVals['default_subscription']);
			}	
		}

		$aWspSetting = array_merge($aWspSetting,$aVals);		
		update_option(WSP_SETTING,$aWspSetting);
		//Save setting on websmarter
		if(!empty($aWspSetting)){
			if(isset($aVals['subscription'])){
				unset($aWspSetting['subscription']);
			}
			$curlViewUrl = $wpsTaskFunObj->webServiceUrl("users/settings");
			$wpsTaskFunObj->__defaultMethod = "POST";
			$uSetting = $aWspSetting;
			$paypal = get_option(WSP_PAYPAL);
			if(!empty($paypal)){
				$uSetting['paypal'] = $paypal;
			}
			$params = ['email'=>$wpVals['admin_email'],'detail'=>['wp_settings'=>json_encode($uSetting)]];
			$aResponse = $wpsTaskFunObj->postUrlUsingCurl($curlViewUrl,$params);
		}

		if($pay){
			//$aData = array('amount'=>$aVals['subscription']['amount'],'period'=>$aVals['subscription']['period']);
			$paypal = $wpsTaskFunObj->getPaypalAPI("express_checkout"); 
			update_option(WSP_PAYPAL,$paypal);

			$aInitData['sandbox'] = ($paypal['mode']=='sandbox'?true:false);
			$aInitData['API'] = $paypal['api_details'];
			$paypalExpress = new Paypal_Express($aInitData);

			$aData['cancel'] = $page_url . "&pay_type=cancel";
			$aData['return'] = $page_url . "&pay_type=success";
			$aData['recurring'] = 'RecurringPayments';
			$aData['recurringDesc'] = $aVals['subscription']['desc'];
			
			$setExpressResponse = $paypalExpress->setExpressCheckout($aData);

			if(!isset($setExpressResponse['ERRORS'])){
				header('location:'.$setExpressResponse['REDIRECTURL']);
				exit;
				die();
			}else{
				unset($aWspSetting['subscription']);
				update_option(WSP_SETTING,$aWspSetting);
				update_option(WSP_PAYPAL,array());
				$wpsTaskFunObj->setMsgs('error',__('Please try again for taking subscription plan.',WSP_TEXT_DOMAIN));
			}
		}else{
			$wpsTaskFunObj->setMsgs('success',__( 'Options updated successfully', WSP_TEXT_DOMAIN ));	
		}		
		//header('location:'.$page_url);
		//exit;
	}
}
/*echo "<pre>";
print_r($subscription);
echo "</pre>";*/

?>
<div class="wpsContent wps_no_padding padding-20">
	<div class="wps_task_details">
        <div class="wps_task_hash"><?php echo __( $aParams['title'] ,WSP_TEXT_DOMAIN ) ?></div>
        <div class="wps_invoice">
             <a href="<?= $taskListUrl?>">
                <img src="<?= WSP_ASSETS.'images/back_icon.png';?>" alt="BackIcon"> <?= __("Back to Task List",WSP_TEXT_DOMAIN);?>
            </a>
        </div>
    </div>
<h1></h1>
<?= $wpsTaskFunObj->getMsgs(); ?>
<div class="wpsSettings">
	<div class="tab pull-left">
	  <a class="tablinks <?= ($active=='general'?'active':'');?>" href="#general-info"><?= __("General Info",WSP_TEXT_DOMAIN);?></a>
	  <a class="tablinks <?= ($active!='general'?'active':'');?>" href="#app-settings"><?= __("App Settings",WSP_TEXT_DOMAIN);?></a>
	</div>
	<div class="pull-left tab-container">
		<div class="tab-control <?= ($active=='general'?'':'hide');?> appSetting" id="general-info">
			<h4><?= __("Please set your general info",WSP_TEXT_DOMAIN);?></h4>
			<form action="" method="post" enctype="multipart/form-data" autocomplete="off" class="setting_form">
				<?php wp_nonce_field( 'wps_websmarter_nonce_action', 'wps_websmarter_nonce_field' ); ?>
				<input type="hidden" name="setting_type" value="general">
				<table class="form-table">
					<tbody>			
						<tr>
							<td>
								<label class="checkbox-container pull-left">
								  <input id="add_task_btn_on_front_client" value="1" name="val[add_task_btn_on_front_client]" class="wps_setting" type="checkbox" <?= (isset($aWspSetting['add_task_btn_on_front_client']) && $aWspSetting['add_task_btn_on_front_client']==1)?'checked="checked"':'';?>>
								  <span class="checkmark"></span>
								</label>
								<label class="pull-left" for="add_task_btn_on_front_client"><?= __("Show Add Task button on front",WSP_TEXT_DOMAIN);?> <span>(<?= __("Admin Only","wps-task-manager");?>)</span> </label>
							</td>
						</tr>
						<tr>
							<td>
								<label class="checkbox-container pull-left">
								  <input id="add_feedback_btn_visitors" value="1" name="val[add_feedback_btn_visitors]" class="wps_setting" type="checkbox" <?= (isset($aWspSetting['add_feedback_btn_visitors']) && $aWspSetting['add_feedback_btn_visitors']==1)?'checked="checked"':'';?>>
								  <span class="checkmark"></span>
								</label>
								<label class="pull-left" for="add_feedback_btn_visitors"><?= __("Show Feedback button for all visitors",WSP_TEXT_DOMAIN);?> </label>
							</td>
						</tr>
						<tr>
							<td>
								<label class="checkbox-container pull-left">
								  <input id="show_powered_by" value="1" name="val[show_powered_by]" class="wps_setting" type="checkbox" <?= (!isset($aWspSetting['show_powered_by']) || (isset($aWspSetting['show_powered_by']) && $aWspSetting['show_powered_by']==1))?'checked="checked"':'';?>>
								  <span class="checkmark"></span>
								</label>
								<label class="pull-left" for="show_powered_by"><?= __("Add to footer, Powered by",WSP_TEXT_DOMAIN);?> </label>
								<img class="wps_setting_logo" src="<?= WSP_ASSETS . 'images/web-logo.png';?>" alt="logo">
							</td>
						</tr>	
							
					</tbody>
				</table>
				<table class="form-table preferences">
					<tbody>
						<?php 
						if(isset($aWspSetting['agency_ref_id']) && !empty($aWspSetting['agency_ref_id'])){?>
							<tr>
							<th scope="row" colspan="2" class="settingData"><?php echo __( 'Your Agency Ref. Key',WSP_TEXT_DOMAIN ) ?> : <span>#<?= $aWspSetting['agency_ref_id'];?></span></th>
						</tr>
						<?php
						}else{
						 ?>
						<tr>
							<th scope="row" colspan="2">
								<?= __(" You can specify tasks to a specific agency. if you know the agency ref ID, please put it here",WSP_TEXT_DOMAIN);?>. 
							</th>
						</tr>	
						<tr>
							<th scope="row"><?= __("Agency Ref. ID",WSP_TEXT_DOMAIN);?></th>
							<td>
								<input name="val[agency_ref_id]" value="" class="wpsInputText wps_setting" type="text">
							</td>
						</tr>
						<?php }?>		
					</tbody>
				</table>
				<br>
				<p class="submit">
					<input type="submit" disabled="true" onclick="Task.showLoader();" value="<?php echo __( 'Update Info',WSP_TEXT_DOMAIN ) ?>" class="btn disabled" id="submit" name="submit">
				</p>
			</form>
		</div>

		<div class="<?= ($active=='general'?'hide':'');?> appSetting tab-control" id="app-settings">
			<h4><?= __("Please set up your preferences here","wps-task-manager");?></h4>
			<form action="" method="post" enctype="multipart/form-data" autocomplete="off" class="setting_form">
				<?php wp_nonce_field( 'wps_websmarter_nonce_action', 'wps_websmarter_nonce_field' ); ?>
				<input type="hidden" name="setting_type" value="app">
				<table class="form-table subscription">
					<tbody>	
						<tr>
							<th scope="row" class="title" colspan="3"><?= __("Set default assignee",WSP_TEXT_DOMAIN);?></th>
						</tr>
						<tr>
							<th scope="row" colspan="3" class="content">
								<?= __("Set what default team will be assigned to your new tasks",WSP_TEXT_DOMAIN);//__("You can choose to hire default workers, who will automatically be assigned to you. You can also ask us to assign your task to specific workers of your choice",WSP_TEXT_DOMAIN);?>. 
							</th>
						</tr>
						<tr>
							<th scope="row"><?php echo __( 'My default assignee',WSP_TEXT_DOMAIN) ?></th>
							
							<td class="subscribe-plan">
								<select name="val[default_team]" class="select2 wps_setting" data-placeholder="<?= __("Select Default Team",WSP_TEXT_DOMAIN)?>">
									<option value=""><?= __("Select Default Team",WSP_TEXT_DOMAIN)?></option>
									<?php 
									if(!empty($aTeam)){
										foreach ($aTeam as $key => $team) { 
											if((!isset($aWspSetting['agency_ref_id']) || empty($aWspSetting['agency_ref_id'])) && $key=='agency'){

											}else{
											?>
										<option value="<?= $key; ?>" <?= ((!isset($aWspSetting['default_team']) && $key==$defaultTeam) || (isset($aWspSetting['default_team']) && !empty($aWspSetting['default_team']) && $aWspSetting['default_team']==$key)?'selected="true"':'');?>><?= $team; ?></option>	
									<?php }
										}
									}
									?>
								</select>
							</td>
							<td>
								<button type="submit" onclick="Task.showLoader();" disabled="true" class="disabled">
	                				<img src="<?= WSP_ASSETS.'images/right-arrow.png';?>" alt="arrow" class="align-middle" width="20px"> 
	                				<?php echo __( 'Set Team',WSP_TEXT_DOMAIN) ?>
	                			</button>
							</td>
						</tr>
						
					</tbody>
				</table>
			</form>
			<br>
			<hr>
			<br>
			<form action="" method="post" enctype="multipart/form-data" autocomplete="off" id="subscription_form" onsubmit="Task.showLoader();">
				<?php wp_nonce_field( 'wps_websmarter_nonce_action', 'wps_websmarter_nonce_field' ); ?>
				<input type="hidden" name="setting_type" value="app">
				<input type="hidden" name="period" value="<?= isset($subscription['period'])?$subscription['period']:''?>">
				<table class="form-table subscription">
					<tbody>	
						<tr>
							<th scope="row" class="title" colspan="3"><?php //= sprintf(__("Autopay %s and earn %s extra credits","wps-task-manager"),strtolower($subscription['period_name']),$subscription['bonus'].'%');?>
								<?= __("Autopay subscription plan and earn extra credits",WSP_TEXT_DOMAIN);?>
							</th>
						</tr>
						<tr>
							<th scope="row" colspan="3" class="content">
								<?php 
								$subs = '';
								if(isset($subscription['plans'])){
									foreach ($subscription['plans'] as $key => $value) {
										$subs .= ' '.$value['amount'].$currency.' a '.strtolower($subscription['period']).',';
									}
								}
								?>
								<?php //sprintf(__("Make paying hassle free. Choose a %s subscription plan from %s or custom amount. Enjoy a %s extra bonus added in your balance %s","wps-task-manager"),strtolower($subscription['period_name']),substr($subs,0,-1),$subscription['bonus'].'%',strtolower($subscription['period_name']));?>
								<?= __("Make paying hassle free. Choose a subscription plan from fixed or custom amount. <br>Enjoy extra bonus added in your balance according to your plan",WSP_TEXT_DOMAIN)?>. 
							</th>
						</tr>

						<tr>
							<th scope="row"><?php echo __( 'My Subscription Plan',WSP_TEXT_DOMAIN ) ?></th>
							<?php if(!isset($aWspSetting['subscription']) || (isset($aWspSetting['subscription']) && empty($aWspSetting['subscription']['subscr_id']))){?>
							<td class="subscribe-plan">
								<select name="val[default_subscription]" class="select2 subscr_plan" required="true" data-placeholder="<?= __("Select Subscription Plan",WSP_TEXT_DOMAIN)?>">
									<option value=""><?= __("Select Subscription Plan",WSP_TEXT_DOMAIN)?></option>
									<?php
									if(isset($subscription['plans'])){
									 foreach ($subscription['plans'] as $key => $value) {?>
									<option value="<?= $value['amount'].'-'.$value['bonus'];?>"><?= $value['amount'].$currency.' a '.strtolower($subscription['period']).' + '.$value['bonus'].'% bonus'?></option>
									<?php }
									}?>
									<option value="custom"><?= __("Custom amount",WSP_TEXT_DOMAIN);?></option>
								</select>
							</td>
							<td>
								<button type="submit" class="disabled" disabled="true">
	                				<img src="<?= WSP_ASSETS.'images/right-arrow.png';?>" alt="arrow" class="align-middle" width="20px"> 
	                				<?php echo __( 'Set Autopay',WSP_TEXT_DOMAIN ) ?>
	                			</button>
							</td>
						<?php }else{?>
							<td class="subscribe-plan" colspan="2">
								<span><?= sprintf(__('%s a %s and %s extra bonus',WSP_TEXT_DOMAIN),$aWspSetting['subscription']['currency'].$aWspSetting['subscription']['amount'],$aWspSetting['subscription']['period'],$aWspSetting['subscription']['bonus'].'%')?>.</span>
							</td>
						<?php }?>
						</tr>
						<?php if(!isset($aWspSetting['subscription']) || (isset($aWspSetting['subscription']) && empty($aWspSetting['subscription']['subscr_id']))){?>
						<tr id="custom-plan" class="hide">
							<th scope="row">
								<?php 
								$bonusString = '';
								if(isset($subscription['bonus'])){
									foreach ($subscription['bonus'] as $key => $value) {
										$bonusString .= $value['bonus'].'-'.$value['from'].'-'.$value['to'].',';
									}
								}
								$bonusString = substr($bonusString, 0,-1);
								?>
								<input type="hidden" name="custom[bouns]" value="<?= $bonusString?>">
							</th>
							<td class="custom-plan">
								<i class="fa fa-dollar"></i> 
								<input type="text" data-type="number" name="custom[amount]" value="" placeholder="Amount" class="wpsInputText custom_class">
								<select name="custom[period]" class="select2 custom_class" style="width:65%;">
									<option value="" disabled=""><?= __("Period",WSP_TEXT_DOMAIN);?></option>
									<?php
									if(!empty($periodArray)){
									 foreach ($periodArray as $key => $value) {?>
									<option value="<?= $key;?>"><?= $value;?></option>
									<?php }
									}?>
								</select>
							</td>
							<td>
								<button type="submit">
	                				<img src="<?= WSP_ASSETS.'images/right-arrow.png';?>" alt="arrow" class="align-middle" width="20px"> 
	                				<?php echo __( 'Set Autopay',WSP_TEXT_DOMAIN ) ?>
	                			</button>
							</td>
						</tr>
						<?php }?>
						<?php if(isset($aWspSetting['subscription']) && !empty($aWspSetting['subscription']['subscr_id'])){?>
						<tr>
							<th scope="row" ></th>
							
							<td colspan="2"><a href="<?= $page_url.'&subscr=cancel'?>" onclick="Task.showLoader();" class="cancel_subs"><?= __("Click here to Cancel Subscription",WSP_TEXT_DOMAIN);?></a>
							</td>
						</tr>
						<?php }?>
						
					</tbody>
				</table>
			</form>
			<br>
			<hr>
			<br>
			<table class="form-table">
				<tbody>	
					<tr>
						<th scope="row" class="title" colspan="2"><?= __("Invoice",WSP_TEXT_DOMAIN);?></th>
					</tr>
					<tr>
						<th scope="row" colspan="2" class="content">
							<?= __("You will be invoiced per task basis or by subscription. Agency / Web-Smarter worker will be balanced, only open tasks done by them. Balance transfer will be done by agency in full minus websmarter commission, or for websmarter worker by worker rate",WSP_TEXT_DOMAIN);?>. 
						</th>
					</tr>				
				</tbody>
			</table>
			<?php /*p class="submit"><input type="submit" value="<?php echo __( 'Save Changes', 'wps-task-manager' ) ?>" class="button button-primary" id="submit" name="submit"></p */ ?>
		</div>
	</div>
</div>
</div>