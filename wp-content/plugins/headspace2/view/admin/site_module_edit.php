<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<div class="option">
	<img style="display: none" src="<?php echo $this->url () ?>/images/progress.gif" width="50" height="16" alt="Progress" id="load_<?php echo $module->id () ?>"/>
</div>

<input type="checkbox" <?php if ($module->is_active ()) echo ' checked="checked"' ?> name="site_modules[]" value="<?php echo $module->id () ?>" id="check_<?php echo $module->id () ?>" onchange="return site_module_toggle ('<?php echo $module->id () ?>','<?php echo $module->file () ?>');"/>

<?php echo $module->name (); ?>

<form action="<?php echo $this->url ().'/ajax.php?id='.$module->id ().'&amp;cmd=save_site_module&_ajax_nonce='.wp_create_nonce ('headspace-save_site_module') ?>" method="post" id="site_module_form_<?php echo $module->id (); ?>">
	<table class="headspace">
		<?php $module->edit (); ?>
		<tr>
			<th></th>
			<td>
				<input class="button-primary" type="submit" name="save" value="<?php _e ('Save', 'headspace'); ?>"/>
				<input class="button-secondary" type="submit" name="cancel" value="<?php _e ('Cancel', 'headspace'); ?>" onclick="return cancel_site_module ('<?php echo $module->id () ?>')"/>
			</td>
		</tr>
	</table>
</form>

<script type="text/javascript">
	jQuery('#site_module_form_<?php echo $module->id (); ?>').ajaxForm ( {beforeSubmit: function () { jQuery('#load_<?php echo $module->id (); ?>').show () }, success: function (data) { jQuery('#site_<?php echo $module->id () ?>').html (data); jQuery('#load_<?php echo $module->id (); ?>').hide ()}});
</script>
