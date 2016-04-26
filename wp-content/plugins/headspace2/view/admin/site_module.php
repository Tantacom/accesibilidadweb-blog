<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?><div class="option">
	<img style="display: none" src="<?php echo $this->url () ?>/images/progress.gif" width="50" height="16" alt="Progress" id="load_<?php echo $module->id () ?>"/>
	
	<?php if ($module->has_config ()) : ?>
	<a href="#" onclick="return edit_site_module('<?php echo $module->id () ?>')"><img src="<?php echo $this->url () ?>/images/edit.png" width="16" height="16" alt="Edit"/></a>
	<?php endif; ?>
	<a href="#" onclick="jQuery('#help_<?php echo $module->id () ?>').toggle (); return false"><img src="<?php echo $this->url () ?>/images/help.png" width="16" height="16" alt="Help"/></a>
</div>

<input type="checkbox" <?php if ($module->is_active ()) echo ' checked="checked"' ?> name="site_modules[]" value="<?php echo $module->id () ?>" id="check_<?php echo $module->id () ?>" onchange="return site_module_toggle ('<?php echo $module->id () ?>','<?php echo $module->file () ?>','<?php echo wp_create_nonce ('headspace-site_module_toggle_'.$module->id ())?>');"/>
	
<?php echo $module->name (); ?>

<div class="help" id="help_<?php echo $module->id () ?>" style="display: none">
	<?php echo $module->description (); ?>
</div>