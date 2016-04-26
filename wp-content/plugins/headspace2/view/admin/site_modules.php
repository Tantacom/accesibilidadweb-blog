<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>
<div class="wrap">
	<?php screen_icon(); ?>

	<h2><?php printf (__ ('%s | Site Modules', 'headspace'), HEADSPACE_MENU); ?></h2>
	
	<?php $this->submenu (true); ?>
	
	<p><?php _e ('Site modules apply to your site as a whole.  Only checked modules will run (when properly configured).', 'headspace'); ?></p>
	
	<div class="settings">
		<ul>
			<?php foreach ($site->modules AS $module) : ?>
				<li id="site_<?php echo $module->id () ?>" class="module <?php if (!$module->is_active ()) echo 'disabled'?>">
					<?php $this->render_admin ('site_module', array ('module' => $module)); ?>
				</li>
			<?php endforeach; ?>
		</ul>
	</div>
</div>

