<?php if ( ! defined( 'ABSPATH' ) ) exit; ?>
<?php global $wp;
$current_url = CURRENT_URL;
?>

<!-- <a href="javascript:void(0);" class="wpsTaskAdminBtn wpsTaskCreateBtn" >
	<img src="<?php echo WSP_ASSETS.( 'images/side_plus.png' ); ?>" alt="plus"> <span class="wps_orange_plus"><?= __("Add task",WSP_TEXT_DOMAIN);?></span>
</a> -->
<a href="javascript:void(0);" data-action="<?= $current_url?>" class="wpsTaskAdminBtn">+ <img src="<?php echo WSP_ASSETS.( 'images/icon.png' ); ?>" alt=""> <?= (isset($btnName)?$btnName:__("Task",WSP_TEXT_DOMAIN))?></a>