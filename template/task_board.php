<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php
$wpVals = $wpsTaskFunObj->wpDefaultValues();
$page_url =  admin_url( 'admin.php?page=wps_task_manager_transactions' );

$curlViewUrl = $wpsTaskFunObj->webServiceUrl("transactions/".$wpVals['admin_email']);

if(isset($_REQUEST['start']) && isset($_REQUEST['end']) && sanitize_key($_REQUEST['start']) && sanitize_key($_REQUEST['end'])){
	$selectedDate = ['start'=>sanitize_key($_REQUEST['start']),'end'=>sanitize_key($_REQUEST['end'])];
	$curlViewUrl = $curlViewUrl.'/'.$selectedDate['start'].'/'.$selectedDate['end'];
}
$aResponse = $wpsTaskFunObj->postUrlUsingCurl($curlViewUrl);
$aJson = json_decode($aResponse);

$aRows = [];
$balance = 0;
$aBalance = [];
$aTransactionType = [];
$currency = '';
if(!empty($aJson)){
	$aRows     	= $aJson->data;
	$balance 	= $aJson->balance;
	$aBalance 	= $aJson->amountData;
	$currency 	= $aJson->currency;
	$aTransactionType = $aJson->aType;
}
?>
<div class="wsp-transaction wpsContent wps_no_padding padding-20">
    <?php /*div id="wps_main">
   		<div id="wps_a2"><?= __("Balance",WSP_TEXT_DOMAIN );?>: $<?= number_format($balance,2)?></div>
	</div*/ ?>
   <div class="wps_details_acc">
   <span class="wps_task_acc"><?= __("Transaction History",WSP_TEXT_DOMAIN );?></span>
	<div class="wps_back_acc">
		<a href="<?= admin_url( 'admin.php?page=wps_task_manager_view_task' );?>">
                	<img src="<?= WSP_ASSETS.( 'images/back_icon.png' );?>" alt="BackIcon"> <?= __("Back to Task List",WSP_TEXT_DOMAIN );?>
                </a>
                </div>
    </div> 
   
<?php //if($aRows) { ?><br> 
    <form action="<?= $page_url;?>">
		<div class="wps_trans">
		    <div class="wps_cal pull-left">
		        <!--May,16 2018 - June,15 2018-->        
				<!-- <input type="date" name="date" class="date-form" id="tran_date"> -->  	
				<div id="reportrange" class="date-form">
				    <i class="fa fa-calendar pull-right"></i>&nbsp;
				    <span></span> 
				    <input type="hidden" name="rangeDate" id="rangeDate" value="<?= (!empty($selectedDate)?date("m/d/Y",strtotime($selectedDate['start'])).' - '.date("m/d/Y",strtotime($selectedDate['end'])):'');?>">
				</div>	
		  	</div>
	  
	        <div class="wps_transaction pull-left">
		        <!-- <select name="Transaction">
				    <option value="volvo">Transaction Type</option>
				    <option value="saab">All Transaction</option>
				    <option value="fiat">Deposit</option>
				    <option value="audi">Fixed-Price</option>
				    <option value="audi">Withdrawal</option>
			  	</select> -->
			  	<select class="select2" style="width:200px;" name="val[task_priority]"  placeholder="<?= __("Transaction Type",WSP_TEXT_DOMAIN);?>">
			  		<option value="" disabled=""><?= __("Transaction Type",WSP_TEXT_DOMAIN);?></option>
					<option value="all"><?= __("All Transaction",WSP_TEXT_DOMAIN);?></option>
					<option value="deposit"><?= __("Deposit",WSP_TEXT_DOMAIN);?></option>
					<option value="fixed_price"><?= __("Fixed-Price",WSP_TEXT_DOMAIN);?></option>
					<option value="withdrawal"><?= __("Withdrawal",WSP_TEXT_DOMAIN);?></option>
				</select>
			</div>
			<div class="wps_img pull-left">
			    <img src="<?= WSP_ASSETS.( 'images/go-orange.jpg' );?>" id="trans-go" alt="go"> 
    		</div>
                </div>
        <div class="pull-right" style=""><?= __("Balance",WSP_TEXT_DOMAIN );?>: $<?= number_format($balance,2)?></div>
	</form>
    <div class="clear"></div>
    <br>
    <?php //} ?>
    <br>
	<!-- <table class="widefat wpsTbl" style="border:none;">
		<tbody>
			
		</tbody>
	</table> -->
	<table class="widefat wpsTbl" style="border:none;">
		<thead>
			<tr class="tblWpsHead">
				<th width="10%"><?= __("Date",WSP_TEXT_DOMAIN );?></th>
				<th width="15%"><?= __("Type",WSP_TEXT_DOMAIN );?></th>
				<th width="40%"><?= __("Description",WSP_TEXT_DOMAIN );?></th>
				<th width="15%"><?= __("Amount",WSP_TEXT_DOMAIN );?></th>
				<th width="20%"><?= __("Ref ID",WSP_TEXT_DOMAIN );?></th>

			</tr>
		</thead>
		<?php if($aRows) { ?>
		<tbody>
			<?php foreach($aRows as $aKey => $aRow) { ?>
			<tr>
				<td>
					<?= date("j.n.y",strtotime($aRow->updated_at)); ?>
				</td>
				<td>
					<?php  echo $aTransactionType->{$aRow->type}; ?>
				</td>
				<td>
					<a href="<?= admin_url( 'admin.php?page=wps_task_manager_discussion&task_id=' ).$aRow->task_id?>">
					<?php  echo "Task-#".$aRow->task_id.' : ';?>
					</a>
					<?php  echo $aRow->task_name; ?>
					
				</td>
				<td ><?php echo $currency.number_format(($aRow->amount),2);//number_format(($aRow->estimation*$aRow->rate),2) ?></td>				
				<td><?php echo $aRow->txn; ?></td>	
			</tr>
			<?php }} else { ?>
			<tr><td colspan="6"><?= __("No data found",WSP_TEXT_DOMAIN );?> !!!</td></tr>
			<?php } ?>
		</tbody>
		
	</table>
	<hr>
			<?php if($aBalance) { ?>
		<!--	<div class="wps_total_border">-->
	<div class="wps_statement pull-right">
		<div class="wps_state pull-right">
		    <br>
		    <span class="wps_stmt"><b><?= __("Statement Period",WSP_TEXT_DOMAIN );?></b> 
		    	<?= (!empty($selectedDate)?date("M d, Y",strtotime($selectedDate['start'])):date("M d, Y",strtotime($aBalance->start)))?> <?= __("to",WSP_TEXT_DOMAIN );?> <?= (!empty($selectedDate)?date("M d, Y",strtotime($selectedDate['end'])):date("M d, Y",strtotime($aBalance->end)))?></span>
        </div>
        <br>
          <br>
          <br>
          <br>
          <br>
         
       
       <table class="pull-right">
		  <tbody class="wps_total_bal">
		      <?php /*tr>
			    <td align="right"><?= __("Opening Balance",WSP_TEXT_DOMAIN );?> </td>
			    <td><?= $currency.'0.00';?></td>
			  </tr>
			  <tr>
			    <td align="right"><?= __("Total Balance",WSP_TEXT_DOMAIN );?> </td>
			    <td><?= $currency.'0.00'?></td>
			  </tr>
			  <tr class="wps_total">
			    <td align="right"><?= __("Total withdrawal",WSP_TEXT_DOMAIN );?> </td>
			    <td><?= $currency.'0.00'?></td>
			  </tr*/ ?>
			  <tr>
			    <td align="right"><?= __("Total Balance",WSP_TEXT_DOMAIN);?> </td>
			    <td><?= $currency.(!empty($aBalance->total)?number_format($aBalance->total,2):0.00)?></td>
			  </tr>
			</tbody>
		</table>

    </div>
    <div class="clear"></div>
			    <?php } ?>
			    <hr>
			</div>
			