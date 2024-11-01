
<?php /*Model PopUp Component*/ ?>
<template id="model-popup-template">
    <div id="estimation-popup" class="wps-inline-modal" data-backdrop="static" data-keyboard="false" style="display: block" >
        <div class="wps-inline-modal-content"> 
            <div class="wps-popup-logo">
                <img src="<?= WSP_ASSETS . ( 'images/logo.png');?>" />
            </div>  
            <div class="wps-inline-modal-header">
                <slot name="close">
                  X
                </slot>
                <slot name="header">
                  <?= __("default header",WSP_TEXT_DOMAIN);?>
                </slot>
                
            </div>  
            <div class="clear"></div>
            <div class="wps-inline-modal-body wps_page_wrap">
                <slot name="body">
                </slot>
            </div>  
        </div>
    </div>
</template>
<?php /*End Common Component*/ ?>