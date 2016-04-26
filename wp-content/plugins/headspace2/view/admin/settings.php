<?php if (!defined ('ABSPATH')) die ('No direct access allowed'); ?>

<div class="wrap">
	<?php screen_icon(); ?>
	
	<h2><?php printf (__ ('%s | Page Settings', 'headspace'), HEADSPACE_MENU); ?></h2>
	
	<?php $this->submenu (true); ?>
	
	<p><?php _e ('Click the page type to change settings.  You can enable additional modules to provide more choices.', 'headspace'); ?></p>
	
	<div class="settings">
		<ul>
			<?php foreach ($types AS $type => $detail) : ?>
			<li id="head_<?php echo $type ?>">
				 <?php $this->render_admin ('settings_item', array ('type' => $type, 'name' => $detail[0], 'desc' => $detail[1])); ?>
			</li>
			<?php endforeach; ?>
		</ul>
	</div>

	<p><?php _e ('Settings can be applied to specific posts &amp; pages from the <strong>post edit page</strong>, and to specific categories from the <strong>edit category</strong> page.', 'headspace'); ?></p>
</div>

<script type="text/javascript">
jQuery(document).ready( function()
{
	makeSettingsClickable ();
});
</script>

<?php $this->render_admin ('help'); ?>
