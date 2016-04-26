<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<?php echo $module->name (); ?>
<form style="font-size: 0.9em" id="mod_edit_<?php echo $id ?>" action="<?php echo $this->url (); ?>/ajax.php?id=<?php echo $id ?>&amp;cmd=save_module&amp;_ajax_nonce=<?php echo wp_create_nonce ('headspace-save_module')?>" method="post" accept-charset="utf-8">
	<table class="headspace">
		<?php $module->edit_options (); ?>
		<tr>
			<th></th>
			<td>
				<input class="button-primary" type="submit" name="save" value="<?php _e ('Save', 'headspace'); ?>"/>
				<input class="button-secondary" type="submit" name="cancel" value="<?php _e ('Cancel', 'headspace'); ?>" onclick="return cancel_module ('<?php echo $id; ?>')"/>
			</td>
		</tr>
	</table>
</form>

<script type="text/javascript">
	jQuery('#mod_edit_<?php echo $id ?>').ajaxForm ( { success: function (data) { jQuery('#id_<?php echo $module->id () ?>').html (data);}});
</script>

