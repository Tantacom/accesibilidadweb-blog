<?php
/*
Plugin Name: Related Posts
Version: 3.6.1
Plugin URI: http://wordpress.org/extend/plugins/related-posts/
Description: Link to related content to help your readers. Get attention from other authors. Make great outbound links for SEO. With just a few clicks.
Author: Sovrn, zemanta
Author URI: http://www.sovrn.com
*/

if (! function_exists('wp_rp_init_zemanta')) {
	function gp_init_error() {
		?>
		<div class="updated">
        <p><?php _e('Related Posts couldn\'t initialize.'); ?></p>
		</div>
		<?php
	}
	
	try {
		include_once(dirname(__FILE__) . '/init.php');
	}
	catch (Exception $e) {
		add_action( 'admin_notices', 'gp_init_error' );
	}
}
else {
	function gp_multiple_plugins_notice() {
		?>
		<div class="updated">
        <p><?php _e( 'Oh, it\'s OK, looks like you\'ve already got one related posts plugin installed, so no need for another one.', 'wp_rp_related_posts' ); ?></p>
		</div>
		<?php
	}
	add_action( 'admin_notices', 'gp_multiple_plugins_notice' );
}
