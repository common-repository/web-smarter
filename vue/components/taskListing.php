<?php /* Task Listing Components */ ?>
<template id="listing-template">
  <div class="task-listing">
    <div class="wpsContent wps_no_padding padding-20">
        <?php echo $wpsTaskFunObj->postUrlUsingCurl(API_URL.'api/template?temp=listing');?>
        <?php //echo file_get_contents(API_URL.'api/template?temp=listing');?>
    </div>
    <model-popup v-if="showPayModal" :class="showPayModal?'show':''" @close="showPayModal = false">
        <span slot="close" class="wps-inline-modal-close" @click.prevent="showPayModal = false">&times;</span>
        <h4 slot="header" class="wps-inline-modal-title"><?= __("Pay with Wallet or Paypal",WSP_TEXT_DOMAIN)?></h4>
        <div slot="body">
            <form method="post" id="frmWpsAddTask">
                <div class="wps-form-msg"></div>
                <table>
                    <tr>
                        <td>
                            <table class="wpsTaskAddTbl">
                                <tr>
                                    <td width="30%"> 
                                        <div class="extra-fields">
                                            <div class="extra-container">
                                                <label class="pull-left">
                                                    <?= __("Wallet Balance",WSP_TEXT_DOMAIN)?>
                                                </label>
                                            </div>
                                            <div class="clear"></div>
                                        </div>
                                    </td>
                                    <td> 
                                        <div class="extra-fields">
                                            <div class="extra-container">
                                                <label class="pull-left">
                                                    <span v-if="walletBalance >= tasks[itemIndex].price">{{ (walletBalance-tasks[itemIndex].price)|showPriceFormat(currency) }} </span>
                                                    <span v-else="">{{ walletBalance|showPriceFormat(currency) }}</span>
                                                </label>
                                                <div class="clear"></div>    
                                            </div>
                                            <span class="remaining-amt">
                                                <i v-if="walletBalance >= tasks[itemIndex].price">({{tasks[itemIndex].price|showPriceFormat(currency)}} <?= __("has been deducted from your wallet balance which was",WSP_TEXT_DOMAIN);?> {{ (walletBalance)|showPriceFormat(currency) }}.)</i>
                                                <i v-else="">(<?= __("You don't have sufficient balance to pay from your Wallet.",WSP_TEXT_DOMAIN);?>)</i>
                                            </span>    
                                        </div>                                        
                                    </td>
                                </tr>
                            </table>                        
                        </td>
                    </tr> 
                    <tr>
                        <td>                            
                            <div class="wps_imgSubmit pull-left" v-if="walletBalance < tasks[itemIndex].price">
                                <button type="button" class="wpsSubmitBtn" v-on:click.prevent="showPayModal = false">
                                    <img src="<?= WSP_ASSETS .( 'images/right-arrow.png' );?>" alt="arrow"> <?= __("Pay in next cycle",WSP_TEXT_DOMAIN)?>
                                </button>
                            </div>
                            <div class="wps_imgSubmit pull-left"  v-if="walletBalance >= tasks[itemIndex].price">
                                <button type="button" v-on:click.prevent="payWithWallet(itemIndex)" class="wpsSubmitBtn">
                                    <img src="<?= WSP_ASSETS .( 'images/right-arrow.png' );?>" alt="arrow"> <?= __("Pay ",WSP_TEXT_DOMAIN);?>{{currency+'0'}}
                                </button>
                            </div>
                            <div class="wps_imgSubmit pull-left paypal_btn_wrapper"  v-else="">
                                <div class="paypal-btn wpsSubmitBtn">
                                <a data-balloon-length="large" data-balloon="<?= __( 'Pay to worker so worker can start working on that task', WSP_TEXT_DOMAIN ) ?>" 
                            data-balloon-pos="up" href="javascript:void(0);" class="wspActPay">
                                    <span class="wps_paypal_icon">
                                        <img src="<?= WSP_ASSETS .( 'images/paypal.png' );?>">
                                    </span> <?= __( 'Pay ', WSP_TEXT_DOMAIN ) ?>{{(tasks[itemIndex].price)|showPriceFormat(currency)}}
                                 </a>
                                <paypal-checkout
                                    :amount="$options.filters.priceFormat(tasks[itemIndex].price)"
                                    :currency="currency_code"
                                    :env="env"
                                    :client="paypal"
                                    :button-style="aStyle"
                                    :experience="experienceOptions"
                                    :items="getPaypalItem(tasks[itemIndex])"
                                    v-on:payment-authorized="paymentAuthorized"
                                    v-on:payment-completed="paymentCompleted"
                                    v-on:payment-cancelled="paymentCancelled">
                                </paypal-checkout>

                            </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td scope="row">
                             <table class="wpsTaskAddTbl">
                                <tr>
                                    <td class="pay-content">
                                    <span><b><?= __("Note",WSP_TEXT_DOMAIN)?> :</b></span>
                            <?= __("If your task amount is less then wallet amount in that case , task amount will be deducted from wallet. If your task amount is greater then wallet amount in that case you will have to pay complete task amount from paypal or will postpond later for next billing cycle.",WSP_TEXT_DOMAIN);?>
                                </td>
                            </tr>
                        </table>
                            
                        </td>
                    </tr>
                    
                </table>            
                <br>
                <div class="clear"></div>
            </form>
        </div>
    </model-popup>
    <model-popup v-if="showModal" :class="showModal?'show':''" @close="showModal = false">
        <span slot="close" class="wps-inline-modal-close" @click.prevent="showModal = false">&times;</span>
        <h4 slot="header" class="wps-inline-modal-title"><?= __("Move Task Under Estimation",WSP_TEXT_DOMAIN)?></h4>
        <div slot="body">
            <form method="post" id="frmWpsAddTask" v-on:submit.prevent="askEstimation(itemIndex)">
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
                            <img src="<?= WSP_ASSETS .( 'images/right-arrow.png' );?>" alt="arrow"> <?= __("Submit",WSP_TEXT_DOMAIN)?>
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