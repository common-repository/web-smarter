<?php /* task Details Components*/ ?>
<template id="details-template">
  <div>
    <div class="wpsContent wps_no_padding padding-20">
        <?php echo $wpsTaskFunObj->postUrlUsingCurl(API_URL.'api/template?temp=details');?>
       <?php //echo file_get_contents('http://localhost:8000/api/template?temp=details');?>
    </div>
    <model-popup v-if="showModal" :class="showModal?'show':''" @close="showModal = false">
        <span slot="close" class="wps-inline-modal-close" @click.prevent="showModal = false">&times;</span>
        <h4 slot="header" class="wps-inline-modal-title"><?= __("Move Task Under Estimation",WSP_TEXT_DOMAIN)?></h4>
        <div slot="body">
            <form method="post" id="frmWpsAddTask" v-on:submit.prevent="askEstimation()">
                <div class="wps-form-msg"></div>
                <table>
                    <tr>
                        <td>
                            <table class="wpsTaskAddTbl">
                                <tr>
                                    <td> 
                                        <div class="extra-fields">
                                            <div class="extra-container">
                                                <label class="pull-left">
                                                    <?= __("Assign Task to The Team",WSP_TEXT_DOMAIN)?>
                                                </label>
                                                <div class="pull-left align-middle">
                                                    <select2 :options="options" cls="form-control col-md-8" v-model="defaultTeam" placeholder="<?= __("Select Team",WSP_TEXT_DOMAIN);?>">
                                                        <option value="0"><?= __("Select Team",WSP_TEXT_DOMAIN);?></option>
                                                    </select2>
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
                <br>
                <p class="wpsSubmitWrapper">
                    <div class="wps_imgSubmit pull-right">
                        <button type="submit" class="wpsSubmitBtn">
                            <img src="<?= WSP_ASSETS.( 'images/right-arrow.png' );?>" alt="arrow"> <?= __("Submit",WSP_TEXT_DOMAIN)?>
                        </button>
                    </div>
                    <div class="clear"></div>
                </p>
                <div class="clear"></div>
            </form>
        </div>
    </model-popup>
  </div>
</template>