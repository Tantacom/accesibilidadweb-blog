<div class="option">
	<?php if ($module->has_config ()) : ?>
	<a href="#" onclick="return edit_module('<?php echo $module->id () ?>','<?php echo wp_create_nonce ('headspace-edit_module')?>')" id="link_<?php echo $module->id () ?>"><img src="<?php echo $this->url () ?>/images/edit.png" width="16" height="16" alt="Edit"/></a>
	<?php endif; ?>
	<a href="#" onclick="jQuery('#help-<?php echo $module->id (); ?>').toggle (); return false;"><img src="<?php echo $this->url () ?>/images/help.png" width="16" height="16" alt="Help"/></a>
</div>

<?php echo $module->name (); ?>

<div class="help" id="help-<?php echo $module->id () ?>" style="display: none">
	<?php echo htmlspecialchars ($module->description ()); ?>
</div>