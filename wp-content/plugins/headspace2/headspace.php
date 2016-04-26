<?php
/*
Plugin Name: HeadSpace2
Plugin URI: http://urbangiraffe.com/plugins/headspace2/
Description: Meta-data manager on steroids, allowing complete control over all SEO needs such as keywords/tags, titles, description, stylesheets, and many many other goodies.
Version: 3.6.17
Author: John Godley
Author URI: http://urbangiraffe.com/
============================================================================================================
3.4    - WordPress 2.5 compatability
3.4.1  - More compatability, fix #145
3.4.2  - Fix bug #180, add Brazillian Portuguese localisation
3.4.3  - WP 2.6
3.4.4  - Fixes for new WP2.6 page changes
3.4.5  - Add Dutch localization
3.4.6  - Feature #258
3.5    - WordPress 2.5+ only.  Update jeromes importer
3.5.1  - Clean up old code, fix IE bugs, split keywords/tags, add login pages
3.5.2  - Add force title option
3.5.3  - Add Apture & FeedBurner Stats module
3.5.4  - Fix tag localization in page edit
3.5.5  - Keyword fixes
3.5.6  - Keywords on home page, page editing fix
3.5.7  - Fix double tags box
3.5.8  - Module for Google Webmaster, Yahoo! Site Explorer, and Disable Visual Editing, Indonesian translation
3.5.9  - Add Piwik module
3.5.10 - Fix various IE caching issues
3.5.11 - Updated base class, add System Settings module, allow pages to appear in tag archives
3.5.12 - Fix to base settings class
3.5.13 - Update CrazyEgg module
3.5.14 - Refine keywords/tags interaction
3.5.15 - Small fixes to various modules
3.5.16 - Tighten up inheritance
3.5.17 - Plugin version information
3.5.18 - Have modules auto-save their order rather than rely on user to hit 'save modules' (fixing bug #302)
3.5.19 - Remove warning message for older WP installs
3.5.20 - Prevent WP-Ecommerce interfering with JavaScript
3.6    - WordPress 2.7 quick/bulk edit functions. Piwik bug (#306). Check theme files. Fix auto description
3.6.1  - Metabox workaround
3.6.2  - Minor fixes for WP2.7.  Fix bug #324. Add #325
3.6.3  - Romanian translation. Fix #334
3.6.4  - Added #348, #343
3.6.5  - Fix problem with category description in 2.7
3.6.6  - WP 2.7 styling
3.6.7  - Updated French translation, tags on pages (again)
3.6.8  - Option to prevent plugins modifying excerpts, Turkish translation, nonces
3.6.9  - Remove error introduced by 3.6.8
3.6.10 - Fix system module error reporting, fix error on page editing for some users. Fix #375, #377, #384.  Add #330.  Reduce memory usage
3.6.11 - Fix module save bug introduced by 3.6.10
3.6.12 - Add #401, AWstats tracking, support for non UTF-8 encodings
3.6.13 - Fix #402, #405
3.6.14 - Update Analytics module, explicit follow, %%excerpt_only%% tag, Follow Links module
3.6.15 - Fix #433
3.6.16 - Try to fix unauthorised access error
3.6.17 - Add #450, #446
============================================================================================================
This software is provided "as is" and any express or implied warranties, including, but not limited to, the
implied warranties of merchantibility and fitness for a particular purpose are disclaimed. In no event shall
the copyright owner or contributors be liable for any direct, indirect, incidental, special, exemplary, or
consequential damages (including, but not limited to, procurement of substitute goods or services; loss of
use, data, or profits; or business interruption) however caused and on any theory of liability, whether in
contract, strict liability, or tort (including negligence or otherwise) arising in any way out of the use of
this software, even if advised of the possibility of such damage.

For full license details see license.txt
============================================================================================================ */

include (dirname (__FILE__).'/plugin.php');
include (dirname (__FILE__).'/models/headspace.php');
include (dirname (__FILE__).'/headspace_library.php');

/**
 * The HeadSpace2 plugin
 *
 * @package HeadSpace2
 **/


class HeadSpace2_Admin extends HeadSpace2_Plugin
{
	var $types        = null;
	var $last_post_id = 0;

	/**
	 * Constructor sets up page types, starts all filters and actions
	 *
	 * @return void
	 **/
	function HeadSpace2_Admin ()
	{
		$this->register_plugin ('headspace', __FILE__);

		if (is_admin ())
		{
			$this->add_action ('admin_menu');
			$this->add_filter ('admin_head');

			$this->add_action ('save_post', 'save_tags');

			$this->add_action ('edit_category_form');
			$this->add_action ('edit_category');
			$this->add_action ('init', 'init', 15);
			$this->add_action ('wp_dashboard_setup');
			
			// WP 2.7 hooks
			$this->add_action ('manage_posts_columns');
			$this->add_action ('manage_pages_columns', 'manage_posts_columns');

			$this->add_action ('manage_posts_custom_column', 'manage_posts_custom_column', 10, 2);
			$this->add_action ('manage_pages_custom_column', 'manage_posts_custom_column', 10, 2);
			
//			$this->add_action ('quick_edit_custom_box', 'quick_edit_custom_box', 10, 2);
//			$this->add_action ('manage_categories_columns', 'manage_posts_columns');
//			$this->add_action ('bulk_edit_custom_box', 'bulk_edit_custom_box', 10, 2);

			$this->register_plugin_details ("http://svn.wp-plugins.org/headspace2/trunk/version.txt");
			
			$this->add_action ('wp_print_scripts');
			$this->add_action ('wp_print_styles');
			$this->add_filter ('contextual_help', 'contextual_help', 10, 2);
		}
	}
	
	function wp_dashboard_setup ()
	{
		if (function_exists ('wp_add_dashboard_widget'))
			wp_add_dashboard_widget ('dashboard_headspace', __ ('HeadSpace', 'headspace'), array (&$this, 'dashboard'));
	}
	
	function dashboard ()
	{
		$headspace = HeadSpace2::get ();
		
		$settings  = $headspace->get_current_settings ();
		$simple   = $headspace->modules->get_restricted ($headspace->get_simple_modules (), $settings, 'page');
		
		$this->render_admin ('dashboard', array ('simple' => $simple, 'advanced' => $advanced));
	}
	
	function manage_posts_columns ($columns)
	{
		$headspace = HeadSpace2::get ();
		
		$settings  = $headspace->get_current_settings ();
		
		$simple   = $headspace->modules->get_restricted ($headspace->get_simple_modules (), $settings, 'page');
		$advanced = $headspace->modules->get_restricted ($headspace->get_advanced_modules (), $settings, 'page');

		$modules = array_merge ($simple, $advanced);
		if (count ($modules) > 0)
		{
			foreach ($modules AS $module)
			{
				if ($module->can_quick_edit ())
					$columns[strtolower (get_class ($module))] = $module->name ();
			}
		}

		return $columns;
	}
	
	function manage_posts_custom_column ($column, $id)
	{
		$hs2 = HeadSpace2::get ();
		$meta = $hs2->get_post_settings ($id);
		
		$module = $hs2->modules->get (array ($column), $meta);
		if (count ($module) > 0)
			$module[0]->quick_view ();
	}
	
	function bulk_edit_custom_box ($column_name, $type)
	{
	}
	
	function quick_edit_custom_box ($column_name, $type)
	{
	}

	function init ()
	{
		// Initialize HS types
		$this->types = array
		(
			"global"   => array (__ ('Global Settings', 'headspace'), __ ('applied to everything unless otherwise specified', 'headspace')),
			"home"     => array (__ ('Home Page', 'headspace'), __ ('applied to the home page', 'headspace')),
			"archive"  => array (__ ('Archives', 'headspace'), __ ('applied when viewing the archives', 'headspace')),
			"category" => array (__ ('Categories', 'headspace'), __ ('applied to category pages without specific settings', 'headspace')),
			"post"     => array (__ ('Posts &amp; Pages', 'headspace'), __ ('applied to posts &amp; pages without specific settings', 'headspace')),
			"author"   => array (__ ('Author Pages', 'headspace'), __ ('applied to author pages', 'headspace')),
			"search"   => array (__ ('Search Pages', 'headspace'), __ ('applied when viewing search results', 'headspace')),
			"404"      => array (__ ('404 Page', 'headspace'), __ ('applied when viewing a 404 error', 'headspace')),
			'tags'     => array (__ ('Tag Pages', 'headspace'), __ ('applied when viewing tag pages', 'headspace')),
			'login'    => array (__ ('Login Pages', 'headspace'), __ ('applied when viewing login, logout, or registration pages', 'headspace')),
		);
		
		// Allow some customisation over core features
		if (file_exists (dirname (__FILE__).'/settings.php'))
			include (dirname (__FILE__).'/settings.php');
		else
		{
			define ('HEADSPACE_MENU', __ ('HeadSpace', 'headspace'));
			define ('HEADSPACE_META', __ ('Meta-data', 'headspace'));
			define ('HEADSPACE_ROLE', 'manage_options');
		}
	}


	function is_page ()
	{
		if (strpos ($_SERVER['REQUEST_URI'], 'page-new.php') !== false || strpos ($_SERVER['REQUEST_URI'], 'edit-page.php') !== false || strpos ($_SERVER['REQUEST_URI'], 'page.php') !== false)
			return true;
		return false;
	}


	/**
	 * Add HeadSpace menu
	 *
	 * @return void
	 **/

	function admin_menu ()
	{
		if (defined ('HEADSPACE_MANAGE'))
			add_management_page (HEADSPACE_MENU, HEADSPACE_MENU, HEADSPACE_ROLE, basename (__FILE__), array ($this, 'admin_screen'));
		else
			add_options_page (HEADSPACE_MENU, HEADSPACE_MENU, HEADSPACE_ROLE, basename (__FILE__), array ($this, 'admin_screen'));
			
		add_management_page (HEADSPACE_META, HEADSPACE_META, HEADSPACE_ROLE, basename (__FILE__), array ($this, 'admin_mass'));
	}



	/**
	 * Hooks into the WP category display and adds a HS meta data section
	 *
	 * @param category Category to edit
	 * @return void
	 **/

	function edit_category_form ($cat)
	{
		if (!empty ($cat))
		{
			$headspace = HeadSpace2::get ();
			$settings  = $headspace->get_current_settings (get_option ('headspace_cat_'.$cat->cat_ID));

			$simple   = $headspace->modules->get_restricted ($headspace->get_simple_modules (), $settings, 'category');
			$advanced = $headspace->modules->get_restricted ($headspace->get_advanced_modules (), $settings, 'category');

			$this->render_admin ('edit_category', array ('simple' => $simple, 'advanced' => $advanced));
		}
	}

	function metabox_tags ($post)
	{
		$headspace = HeadSpace2::get ();
		$settings  = $headspace->get_current_settings ();

		$tags = $headspace->modules->get ('hsm_tags');
		if ($tags !== false)
			$this->render_admin ('edit_page', array ('post_ID' => $post->ID));
	}
	
	function metabox ($post)
	{
		global $wp_meta_boxes;
		
		if (isset ($wp_meta_boxes['post']['normal']['sorted']['headspacestuff']))
			unset ($wp_meta_boxes['post']['normal']['sorted']['headspacestuff']);

		if (isset ($wp_meta_boxes['page']['normal']['sorted']['headspacestuff']))
			unset ($wp_meta_boxes['page']['normal']['sorted']['headspacestuff']);
			
		$headspace = HeadSpace2::get ();
		$settings  = $headspace->get_current_settings ();

		$simple   = $headspace->modules->get_restricted ($headspace->get_simple_modules (), $settings, 'page');
		$advanced = $headspace->modules->get_restricted ($headspace->get_advanced_modules (), $settings, 'page');

		$this->render_admin ('edit', array ('simple' => $simple, 'advanced' => $advanced, 'width' => 140, 'area' => 'page'));
	}
	

	/**
	 * Extract meta-data when saving a post
	 *
	 * @param int $id Post ID
	 * @return void
	 **/

	function save_tags ($id)
	{
		if (isset ($_POST['headspace']))
		{
			$headspace = HeadSpace2::get ();
			$headspace->save_post_settings ($id, $headspace->extract_module_settings ($_POST, 'page'));
		}
	}


	/**
	 * Extract HS meta data when editing a category
	 *
	 * @param int $id Category ID
	 * @return void
	 **/

	function edit_category ($id)
	{
		if (isset ($_POST['cat_ID']))
		{
			$headspace = HeadSpace2::get ();
			$settings  = $headspace->extract_module_settings ($_POST, 'category');

			if (empty ($settings))
				delete_option ('headspace_cat_'.$id);
			else
				update_option ('headspace_cat_'.$id, $settings);
		}
	}

	function is_25 ()
	{
		global $wp_version;
		if (version_compare ('2.5', $wp_version) <= 0)
			return true;
		return false;
	}

	function submenu ($inwrap = false)
	{
		// Decide what to do
		$sub = isset ($_GET['sub']) ? $_GET['sub'] : '';
		$url = explode ('&', $_SERVER['REQUEST_URI']);
		$url = $url[0];
		
		if ($inwrap == true)
			$this->render_admin ('submenu', array ('url' => $url, 'sub' => $sub));
		return $sub;
	}

	function admin_mass ()
	{
		$sub = $this->submenu ();

		// Display screen
		if ($sub == '')
			$this->admin_mass_edit ();
		else if ($sub == 'tags')
			$this->admin_tags ();
	}

	function annoy ()
	{
		$options = $this->get_options ();

		$last = 0;
		if (isset ($options['annoy_last']))
			$last = $options['annoy_last'];

		// Annoy you every 3 days
		if (!$options['support'] && time () > $last + (60 * 60 * 24 * 3))
		{
			$options['annoy_last'] = time ();
			update_option ('headspace_options', $options);

			$this->render_admin ('support');
		}
	}
	
	
	/**
	 * Checks the current theme footer.php and header.php to ensure it contains the appropriate function calls
	 * to allow HS to work.  Hopefully this will reduce support questions regarding this
	 */
	function check_theme_files ()
	{
		$base = get_template_directory ();
		
		$messages = array ();
		if (file_exists ($base.DIRECTORY_SEPARATOR.'header.php'))
		{
			$theme_data = implode ('', file ($base.DIRECTORY_SEPARATOR.'header.php'));
			
			if (strpos ($theme_data, 'wp_head') === false)
				$messages[] = __ ('<code>wp_head</code> was not found in <code>header.php</code> (<a href="http://codex.wordpress.org/Hook_Reference/wp_head">documentation</a>)');
		}
		
		if (file_exists ($base.DIRECTORY_SEPARATOR.'footer.php'))
		{
			$theme_data = implode ('', file ($base.DIRECTORY_SEPARATOR.'footer.php'));
			
			if (strpos ($theme_data, 'wp_footer') === false)
				$messages[] = __ ('<code>wp_footer</code> was not found in <code>footer.php</code> (<a href="http://codex.wordpress.org/Theme_Development">documentation</a>)');
		}
		
		if (count ($messages) > 0)
		{
			$msg = '';
			foreach ($messages AS $message)
				$msg .= '<li>'.$message.'</li>';
				
			$this->render_error ('<p>There are some problems with your theme.  Before HeadSpace will function correctly you should fix these.</p><ol>'.$msg.'</oi>');
		}
	}
	
	
	/**
	 * Choose which admin screen is displayed as well as displaying RSS version feed
	 *
	 * @return void
	 **/

	function admin_screen ()
	{
		if (get_option ('headspace_version') != 9)
		{
			include (dirname (__FILE__).'/models/upgrade.php');

			HS_Upgrade::upgrade (get_option ('headspace_version'), 9);
		}

		if (!$this->is_25 ())
		{
			$this->render_error (__ ('The HeadSpace administration interface is now WordPress 2.5+ only.  Your meta-data will continue to function but you should either upgrade your WordPress or downgrade HeadSpace.', 'headspace'));
			return;
		}
		
		// Decide what to do
		$sub = $this->submenu ();

		$this->annoy ();
		$this->check_theme_files ();
		
		// Display screen
		if ($sub == '')
			$this->admin_settings ();
		else if ($sub == 'options')
			$this->admin_options ();
		else if ($sub == 'keywords')
			$this->admin_keywords ();
		else if ($sub == 'import')
			$this->admin_import ();
		else if ($sub == 'site')
			$this->admin_site ();
		else if ($sub == 'mass')
			$this->admin_mass_edit ();
		else if ($sub == 'modules')
			$this->admin_modules ();
	}

	function get_options ()
	{
		$options = get_option ('headspace_options');
		if ($options === false)
			$options = array ();
			
		$defaults = array
		(
			'inherit' => true,
			'excerpt' => true,
			'debug'   => false,
			'support' => false
		);
		
		foreach ($defaults AS $key => $value)
		{
			if (!isset ($options[$key]))
				$options[$key] = $value;
		}
		
		return $options;
	}
	
	function admin_mass_edit ()
	{
		include (dirname (__FILE__).'/models/pager.php');
		include (dirname (__FILE__).'/models/mass_edit.php');

		$headspace = HeadSpace2::get ();
		$types     = HSM_Mass_Editor::available ();

		$current = 'hs_mass_pagetitle';
		if (isset ($_GET['type']) && isset ($types[$_GET['type']]))
			$current = $_GET['type'];

		$current = $types[$current];

		if (isset ($_POST['save']) && check_admin_referer ('headspace-mass_edit'))
		{
			$current->update (stripslashes_deep ($_POST));
			$this->render_message (__ ('Posts successfully updated', 'headspace'));
		}

		$pager   = $current->get_pager ();
		$posts   = $current->get ($pager);

		$this->render_admin ('mass_edit', array ('types' => $types, 'posts' => $posts, 'pager' => $pager, 'current' => $current));
	}


	/**
	 * Display the settings screen
	 *
	 * @return void
	 **/

	function admin_settings ()
	{
		$this->render_admin ('settings', array ('types' => $this->types));
	}


	/**
	 * Display the options screen
	 *
	 * @return void
	 **/

	function admin_options ()
	{
		// Save
		if (isset ($_POST['save']) && check_admin_referer ('headspace-update_options'))
		{
			$options = $this->get_options ();
			$options['inherit'] = isset ($_POST['inherit']) ? true : false;
			$options['debug']   = isset ($_POST['debug']) ? true : false;
			$options['support'] = isset ($_POST['support']) ? true : false;
			$options['excerpt'] = isset ($_POST['excerpt']) ? true : false;

			update_option ('headspace_options', $options);
			$this->render_message (__ ('Your options have been updated', 'headspace'));
		}
		else if (isset ($_POST['delete']) && check_admin_referer ('headspace-delete_plugin'))
		{
			include (dirname (__FILE__).'/models/upgrade.php');

			HS_Upgrade::remove (__FILE__);
			$this->render_message (__ ('HeadSpace has been removed', 'headspace'));
		}

		$this->render_admin ('options', array ('options' => $this->get_options ()));
	}


	function admin_modules ()
	{
		$headspace = HeadSpace2::get ();

		$simple   = $headspace->modules->get ($headspace->get_simple_modules ());
		$advanced = $headspace->modules->get ($headspace->get_advanced_modules ());

		$this->render_admin ('modules', array ('simple' => $simple, 'advanced' => $advanced, 'disabled' => $headspace->modules->get_disabled ($simple, $advanced)));
	}
	
	function admin_site ()
	{
		$headspace = HeadSpace2::get ();
		
		$this->render_admin ('site_modules', array ('site' => $headspace->site));
	}
	
	function admin_import ()
	{
		include (dirname (__FILE__).'/models/importer.php');

		$importmanager = new HS_ImportManager ();

		if ((isset ($_POST['import']) || isset ($_POST['import_cleanup'])) && check_admin_referer ('headspace-import'))
		{
			$importer = $importmanager->get ($_POST['importer']);
			$count    = $importer->import ();

			if (isset ($_POST['import_cleanup']))
				$importer->cleanup ();

			$this->render_message (sprintf (__ ('%d items were imported from %s', 'headspace'), $count, $importer->name ()));
		}

		$this->render_admin ('import', array ('modules' => $importmanager->available ()));
	}
	
	function wp_print_scripts ()
	{
		global $wp_scripts;
		
		add_meta_box ('headspacestuff', __ ('HeadSpace', 'headspace'), array (&$this, 'metabox'), 'post', 'normal', 'high');
		add_meta_box ('headspacestuff', __ ('HeadSpace', 'headspace'), array (&$this, 'metabox'), 'page', 'normal', 'high');
		add_meta_box ('tagsdiv', __ ('Tags', 'headspace'), array (&$this, 'metabox_tags'), 'page', 'normal', 'high');
		
		// Rejig the localization
		if ($this->is_page ())
			$wp_scripts->registered['page']->extra['l10n'] = $wp_scripts->registered['post']->extra['l10n'];
		
		// We need to do this because the WP-Ecommerce plugin inserts some JS that interferes with HeadSpace
		if (isset ($wp_scripts->registered['ui-tabs']) && strpos ($_SERVER['REQUEST_URI'], 'headspace.php') !== false)
			unset ($wp_scripts->registered['ui-tabs']);

		if (strpos ($_SERVER['REQUEST_URI'], 'headspace') || $this->is_category_edit ())
			wp_enqueue_script ('headspace', $this->url ().'/js/headspace.js', array ('jquery-form', 'jquery-ui-sortable'), $this->version ());
		else if ($this->is_page ())
		{
			wp_enqueue_script ('headspace', $this->url ().'/js/headspace.js', array ('jquery-form', 'jquery-ui-sortable'), $this->version ());
			wp_enqueue_script ('headspace-tags', $this->url ().'/js/headspace-tags.js', array ('headspace'), $this->version ());
			wp_enqueue_script ('headspace-page', $this->url ().'/js/headspace-page.js', array ('headspace'), $this->version ());
		}
		else if ($this->is_post_edit ())
		{
			wp_enqueue_script ('headspace', $this->url ().'/js/headspace.js', array ('jquery-form', 'jquery-ui-sortable'), $this->version ());
			wp_enqueue_script ('headspace-tags', $this->url ().'/js/headspace-tags.js', array ('headspace'), $this->version ());
			wp_enqueue_script ('headspace-post', $this->url ().'/js/headspace-post.js', array ('headspace'), $this->version ());
		}
	}
	
	function is_post_edit ()
	{
		if (strpos ($_SERVER['REQUEST_URI'], 'post.php') !== false || strpos ($_SERVER['REQUEST_URI'], 'post-new.php') !== false)
			return true;
		return false;
	}
	
	function is_category_edit ()
	{
		if (strpos ($_SERVER['REQUEST_URI'], 'categories.php'))
			return true;
		return false;
	}
	
	function wp_print_styles ()
	{
		if (strpos ($_SERVER['REQUEST_URI'], 'headspace.php') || $this->is_page () || $this->is_category_edit () || $this->is_post_edit ())
			wp_enqueue_style ('headspace', $this->url ().'/admin.css', array (), $this->version ());
	}
	
	
	/**
	 * Insert CSS and JS into administration page
	 *
	 * @return void
	 **/

	function admin_head ()
	{
		if (strpos ($_SERVER['REQUEST_URI'], 'post.php') !== false || strpos ($_SERVER['REQUEST_URI'], 'post-new.php') !== false || $this->is_page () || strpos ($_SERVER['REQUEST_URI'], 'headspace') || strpos ($_SERVER['REQUEST_URI'], 'categories.php'))
			$this->render_admin ('head_admin');
	}
	
	function version ()
	{
		$plugin_data = implode ('', file (__FILE__));
		
		if (preg_match ('|Version:(.*)|i', $plugin_data, $version))
			return trim ($version[1]);
		return '';
	}
	
	function contextual_help ($help, $screen)
	{
		if ($screen == 'settings_page_headspace')
		{
			$help .= '<h5>' . __('HeadSpace Help') . '</h5><div class="metabox-prefs">';
			$help .= '<a href="http://urbangiraffe.com/plugins/headspace2/">'.__ ('HeadSpace Documentation', 'headspace').'</a><br/>';
			$help .= '<a href="http://urbangiraffe.com/support/forum/headspace">'.__ ('HeadSpace Support Forum', 'headspace').'</a><br/>';
			$help .= '<a href="http://urbangiraffe.com/tracker/projects/headspace/issues?set_filter=1&tracker_id=1">'.__ ('HeadSpace Bug Tracker', 'headspace').'</a><br/>';
			$help .= '<a href="http://urbangiraffe.com/plugins/headspace/faq/">'.__ ('HeadSpace FAQ', 'headspace').'</a><br/>';
			$help .= __ ('Please read the documentation and FAQ, and check the bug tracker, before asking a question.', 'headspace');
			$help .= '</div>';
		}
		
		return $help;
	}
}


/**
 * Instantiate the plugin
 *
 * @global
 **/

$headspace2 = new HeadSpace2_Admin;


/**
 * Template function to display tags
 *
 * @return void
 **/

function the_head_tags ()
{
	$headspace = HeadSpace2::get ();

	$settings = $headspace->get_current_settings ();
	echo $headspace->capture ('tags', array ('tags' => explode (',', $settings['keywords'])));
}

?>
