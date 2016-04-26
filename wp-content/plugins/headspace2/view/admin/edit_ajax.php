<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<form action="<?php echo $this->url ().'/ajax.php?id='.$type.'&amp;cmd=save&_ajax_nonce='.wp_create_nonce ('headspace-save_module_'.$type) ?>" method="post" id="form_<?php echo $type ?>">
	<?php $this->render_admin ('edit', array ('simple' => $simple, 'advanced' => $advanced, 'width' => '140px', 'id' => $type));?>

	<input class="button-primary" style="margin-left: 118px" type="submit" name="save" value="<?php _e ('Save', 'headspace'); ?>"/>
	<input class="button-secondary" type="submit" name="cancel" value="<?php _e ('Cancel', 'headspace'); ?>"/>
</form>

<script type="text/javascript">
	jQuery('#form_<?php echo $type ?> input[name=cancel]').click (function (event)
	{
		jQuery('#loading').show ();
		jQuery('#head_<?php echo $type ?>').load (jQuery('#form_<?php echo $type ?>').attr('action').replace ('save','cancel'), '', function () { jQuery('#loading').hide (); makeSettingsClickable ()});
		return false;
	 });
	 
	jQuery('#form_<?php echo $type ?>').ajaxForm ( {beforeSubmit: function () { jQuery('#loading').show () }, success: function (data) { jQuery('#head_<?php echo $type ?>').html (data); jQuery('#loading').hide (); makeSettingsClickable ()}});
</script>
