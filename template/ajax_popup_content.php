<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $wp;

use WebSmarter\Includes\Common_Functions;
//include_once dirname(__FILE__)."/function.php";

$wpsTaskFunObj = new Common_Functions;	
$curlViewUrl = $wpsTaskFunObj->webServiceUrl("wp_plugin_setting/priority");
$aResponse = $wpsTaskFunObj->postUrlUsingCurl($curlViewUrl);
$aJson = json_decode($aResponse,true);
$priority = array_reverse($aJson['priorities']['options']);
$defaultPriority = $aJson['priorities']['default'];
?>
<div id="<?= $id?>" class="wps-inline-modal" data-backdrop="static" data-keyboard="false" >
	<div class="wps-inline-modal-content"> 
	<div class="wps-popup-logo"><img src="<?php echo WSP_ASSETS.( 'images/logo.png' ); ?>" /></div> 	
	<div class="wps-inline-modal-header">

		<span class="wps-inline-modal-close" onclick="wpsCloseModalPopup('wps-admin-task-popup');">&times;</span>
		<h4 class="wps-inline-modal-title"><?= __("Add new Task",WSP_TEXT_DOMAIN)?></h4>

	</div>	
		
		<div class="clear"></div>
	<div class="wps-inline-modal-body wps_page_wrap">
	
		<form method="post" id="frmWpsAddTask" onsubmit="return wpsAddTask();">
			<?php wp_nonce_field( 'wps_websmarter_addtask_nonce_action', 'wps_websmarter_addtask_nonce_field' ); ?>
			<input type="hidden" id="wpsAjaxurl" value="<?php echo admin_url( 'admin-ajax.php' ) ?>">
			<input type="hidden" name="action" value="wps_task_manager_add_ajax">
			<div class="wps-form-msg"></div>
			<table>
				<tr>
					<td>
						<table class="wpsTaskAddTbl">
							<tr>
								<td>
									<label class="wpsTblLabel"><?= __("Title",WSP_TEXT_DOMAIN)?></label>
									<input type="text" required="" name="val[task_title]" class="wpsTblInput" placeholder="<?= __("Choose a self explanatory name for the task",WSP_TEXT_DOMAIN);?>">
								</td>
							</tr>
							<tr>
								<td>
									<label class="wpsTblLabel"><?= __("Url",WSP_TEXT_DOMAIN)?></label>
									<input type="url" required="" name="val[task_url]" value="<?= $link; ?>" class="wpsTblInput">
								</td>
							</tr>
							<tr>
								<td>
									<label class="wpsTblLabel"><?= __("Description",WSP_TEXT_DOMAIN)?></label>
									<textarea required="true" name="val[task_description]" rows="5" cols="20" class="wpsTblInput" placeholder="<?= __("Add details of the task",WSP_TEXT_DOMAIN);?>"></textarea>
								</td>
							</tr>
							<tr>
								<td> 
									<div class="extra-fields">
										<div class="extra-container">
											<label class="pull-left">
												<?= __("Add priority to task",WSP_TEXT_DOMAIN)?>
											</label>
											<div class="pull-left align-middle">
												<select class="priority" name="val[task_priority]"  data-placeholder="<?= __("Select Task Prority",WSP_TEXT_DOMAIN);?>">
													<option value=""><?= __("Select Task Prority",WSP_TEXT_DOMAIN);?></option>
													<?php if(!empty($priority)){
														foreach($priority as $key=>$name){?>
													<option value="<?= $key;?>" data-title="resersf" <?= ($defaultPriority==$key?'selected="true"':'');?>><?= $name;?></option>
													<?php }} ?>
												</select>
											</div>
										</div>
										<div class="clear"></div>
									</div>
								</td>
							</tr>
						</table>						
					</td>
				</tr> 
			</table>
			<div class="image-tool">
				<div class="wps_imgDropdown hide fabric-tool-container">
					<ul class="drop_icons_list">
						<!-- <li class="wps_border">
							<a href="">
								<img src="<?= WSP_ASSETS.( 'images/icon-1.png' );?>" alt="icon">
							</a>
						</li> -->
						<li class="wps_border">
							<a href="javascript:void(0);" data-balloon-length="large" data-balloon="Draw an arrow" 
							data-balloon-pos="up" data-type="arrow" class="fab-tool">
								<img src="<?= WSP_ASSETS.( 'images/icon-2.png' );?>" alt="icon">
							</a>
						</li>
						<li class="wps_border">
							<a href="javascript:void(0);" data-balloon-length="large" data-balloon="Add a text" 
							data-balloon-pos="up" data-type="text" class="fab-tool">
								<img src="<?= WSP_ASSETS.( 'images/text.png');?>" alt="icon">
							</a>
						</li>
						<?php /*li class="wps_border">
					        <div class="dropdown">
					            <span>
					            	<img src="<?= WSP_ASSETS.( 'images/icon-3.png');?>" alt="icon">
					            </span>
					            <div class="dropdown-content">
						           <ul>
							   	        <li>
							   	        	<a href="javascript:void(0);" data-val="10" data-type="size" class="fab-tool">
							   	        		<div class="wps_text">10px</div>
							   	        	</a>
							   	        </li>
							   	        <li>
							   	        	<a href="javascript:void(0);" data-val="11" data-type="size" class="fab-tool">
							   	        		<div class="wps_text">11px</div>
							   	        	</a>
							   	        </li>
							   	        <li>
							   	        	<a href="javascript:void(0);" data-val="12" data-type="size" class="fab-tool">
							   	        		<div class="wps_text">12px</div>
							   	        	</a>
							   	        </li>
							   	        <li>
							   	        	<a href="javascript:void(0);" data-val="16" data-type="size" class="fab-tool">
							   	        		<div class="wps_text">16px</div>
							   	        	</a>
							   	        </li>
							   	        <li>
							   	        	<a href="javascript:void(0);" data-val="20" data-type="size" class="fab-tool">
							   	        		<div class="wps_text">20px</div>
							   	        	</a>
							   	        </li>
							   	        <li>
							   	        	<a href="javascript:void(0);" data-val="32" data-type="size" class="fab-tool">
							   	        		<div class="wps_text">32px</div>
							   	        	</a>
							   	        </li>
							   	        <li>
							   	        	<a href="javascript:void(0);" data-val="62" data-type="size" class="fab-tool">
							   	        		<div class="wps_text">62px</div>
							   	        	</a>
							   	        </li>
							   	        <li>
							   	        	<a href="javascript:void(0);" data-val="70" data-type="size" class="fab-tool">
							   	        		<div class="wps_text">70px</div>
							   	        	</a>
							   	        </li>
						           </ul>
					            </div>
				            </div>
					    </li*/ ?>
					    <li class="wps_border">
					    	<a href="javascript:void(0);" data-balloon-length="large" data-balloon="<?= __("Draw an reactangle",WSP_TEXT_DOMAIN);?>" 
							data-balloon-pos="up" data-type="reactangle" class="fab-tool">
					    		<img src="<?= WSP_ASSETS.( 'images/icon-4.png' );?>" alt="icon">
					    	</a>
					    </li>
					    <li class="wps_border">
				            <div class="dropdown">
				            	<span  data-balloon-length="large" data-balloon="<?= __("Choose a color",WSP_TEXT_DOMAIN);?>" 
							data-balloon-pos="up">
				            		<img src="<?= WSP_ASSETS.( 'images/icon-5.png' );?>" alt="icon">
				            	</span>
					            <div class="dropdown-content">
						            <ul>
							   	        <li>
							   	        	<a href="javascript:void(0);" data-val="red" data-type="color" class="fab-tool">
							   	        		<div class="color red"></div>
							   	        	</a>
							   	        </li>
							   	        <li>
							   	        	<a href="javascript:void(0);" data-val="green" data-type="color" class="fab-tool">
							   	        		<div class="color green"></div>
							   	        	</a>
							   	       	</li>
							   	        <li>
							   	        	<a href="javascript:void(0);" data-val="black" data-type="color" class="fab-tool">
							   	        		<div class="color black"></div>
							   	        	</a>
							   	        </li>
							   	        <li>
							   	        	<a href="javascript:void(0);" data-val="blue" data-type="color" class="fab-tool">
							   	        		<div class="color blue"></div>
							   	        	</a>
							   	        </li>
							   	        <li>
							   	        	<a href="javascript:void(0);" data-val="yellow" data-type="color" class="fab-tool">
							   	        		<div class="color yellow"></div>
							   	        	</a>
							   	        </li>
							   	        <li class="active">
							   	        	<a href="javascript:void(0);" data-val="purple" data-type="color" class="fab-tool">
							   	        		<div class="color purple"></div>
							   	        	</a>
							   	        </li>
						            </ul>
					            </div>
				           </div>
					    </li>
						<!-- <li class="wps_border">
							<a href="">
								<img src="<?= WSP_ASSETS.( 'images/icon-6.png' );?>" alt="icon">
							</a>
						</li>
						<li class="wps_border">
							<a href="">
								<img src="<?= WSP_ASSETS.( 'images/icon-7.png' );?>" alt="icon">
							</a>
						</li> -->
						<li class="wps_border">
							<a href="javascript:void(0);" class="fab-tool-save" data-balloon-length="large" data-balloon="<?= __("Save an image",WSP_TEXT_DOMAIN);?>" 
							data-balloon-pos="up">
								<img src="<?= WSP_ASSETS.( 'images/icon-8.png');?>" alt="icon">
							</a>
						</li>
					</ul>
				</div>
			    <div class="wps_image_wrapper align-center hide" id="taskUploadImageContainer">
					<img src="<?php echo WSP_ASSETS.( 'images/task-default.gif'); ?>" id="taskUploadImage">
				</div>
				<div class="note hide"><b><?= __("Note",WSP_TEXT_DOMAIN);?> </b>: <span><?= sprintf(__("Please click on save icon (%s) and save image with all changes",WSP_TEXT_DOMAIN),'<i class="fa fa-save"></i>');?> .</span></div>
			</div>
			<div class="wps_uploadimg_btn">
				<br>
				<div class="wps_upload_btn">
					<div class="pull-left file-div">
					<a href="javascript:void(0);" class="wps_button_blue capture">
					    <img src="<?= WSP_ASSETS.( 'images/upload.png' );?>" alt="upload">  <?= __("Screenshot",WSP_TEXT_DOMAIN)?>
					   
					</a>
					<br>
					 <span class="wps_optional">(<?= __("optional",WSP_TEXT_DOMAIN)?>)</span>
</div>
					<div class="pull-left file-div">
						<a href="javascript:void(0);" class="wps_button_blue file-take">
						    <img src="<?= WSP_ASSETS.( 'images/upload.png');?>" alt="upload">  <?= __("Upload File",WSP_TEXT_DOMAIN)?><br>
						    
						</a>
						<br>
						<span class="wps_optional">(<?= __("optional",WSP_TEXT_DOMAIN)?>)</span>
						<input type="file" name="val[task_image]" class="hide browse" accept="image/*">
					</div>
				</div>
			</div>
			
			<br>
			<p class="wpsSubmitWrapper">
				<input type="hidden" name="val[image_file_name]" id="task_file_name" value="">
				<div class="wps_imgSubmit pull-right">
					<button type="submit" class="wpsSubmitBtn">
	                	<img src="<?= WSP_ASSETS.( 'images/right-arrow.png');?>" alt="arrow"> <?= __("Submit",WSP_TEXT_DOMAIN)?>
	                </button>
            	</div>
            	<div class="clear"></div>
			</p>
			<div class="clear"></div>
		</form>	
	</div>	
	</div>
</div>