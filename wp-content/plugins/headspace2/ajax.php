<?php

/**
 * HeadSpace AJAX
 *
 * @package HeadSpace
 * @author John Godley
 * @copyright Copyright (C) John Godley
 **/

/*
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

define('DOING_AJAX', true);
$_SERVER['REQUEST_URI'] = 'wp-admin/';    // Make the system think we are in admin mode

if (file_exists ('../../../wp-load.php'))
	include ('../../../wp-load.php');
else
	include ('../../../wp-config.php');
	
@header('Content-Type: ' . get_option('html_type') . '; charset=' . get_option('blog_charset'));
header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past

/**
 * HeadSpace2 AJAX functions
 *
 * @package HeadSpace2
 * @author John Godley
 * @copyright Copyright (C) John Godley
 **/

class Headspace2_AJAX extends HeadSpace2_Plugin
{
	function Headspace2_AJAX ($id, $command)
	{
		global $headspace2;
		$this->register_plugin ('headspace', __FILE__);
		
		$headspace2->init ();
		
		$allowed = false;
		if (($command == 'tag_update' || $command == 'auto_tag' || $command == 'auto_desc') && current_user_can ('edit_post', $id))
			$allowed = true;
		else if (current_user_can ('manage_options'))
			$allowed = true;

		if ($allowed)
		{
			$_POST = stripslashes_deep ($_POST);
		
			if (method_exists ($this, $command))
				$this->$command ($id);
			else
				die (__ ('<p style="color: red">That function is not defined</p>', 'headspace'));
		}
		else
			die (__ ('<p style="color: red">You are not allowed access to this resource</p>', 'headspace'));
	}
	
	function obj_to_array ($items)
	{
		$merged = array ();
		if (!empty ($items) > 0)
		{
			foreach ($items AS $key => $value)
			{
				if (!empty ($value))
					$merged[$key] = $value;
			}
		}
		
		return $merged;
	}
	
	function edit ($id)
	{
		global $headspace2;

		if (in_array ($id, array_keys ($headspace2->types)))
		{
			$headspace = HeadSpace2::get ();
			$settings = $this->obj_to_array (get_option ('headspace_'.$id));

			$simple   = $headspace->modules->get_restricted ($headspace->get_simple_modules (), $settings, $id);
			$advanced = $headspace->modules->get_restricted ($headspace->get_advanced_modules (), $settings, $id);

			$this->render_admin ('settings_item', array ('type' => $id, 'name' => $headspace2->types[$id][0], 'desc' => $headspace2->types[$id][1], 'nolink' => true));
			$this->render_admin ('edit_ajax', array ('simple' => $simple, 'advanced' => $advanced, 'type' => $id));
		}
	}
	
	function save ($id)
	{
		global $headspace2;

		if (is_array ($headspace2->types) && in_array ($id, array_keys ($headspace2->types)) && check_ajax_referer ('headspace-save_module_'.$id))
		{
			$headspace = HeadSpace2::get ();
			$settings = $headspace->extract_module_settings ($_POST, $id);
			if (empty ($settings))
				delete_option ('headspace_'.$id);
			else
				update_option ('headspace_'.$id, $settings);
							
			$this->render_admin ('settings_item', array ('type' => $id, 'name' => $headspace2->types[$id][0], 'desc' => $headspace2->types[$id][1], 'nolink' => false));
		}
	}
	
	function cancel ($id)
	{
		global $headspace2;
		
		if (in_array ($id, array_keys ($headspace2->types)))
		{
			$headspace = HeadSpace2::get ();
			$settings = get_option ('headspace_'.$id);
							
			$this->render_admin ('settings_item', array ('type' => $id, 'name' => $headspace2->types[$id][0], 'desc' => $headspace2->types[$id][1], 'nolink' => false));
		}
	}
	
	function save_order ($id)
	{
		if (check_ajax_referer ('headspace-save_order'))
		{
			global $headspace2;
		
			parse_str ($_POST['simple'], $simple);
			parse_str ($_POST['advanced'], $advanced);

			$options = $headspace2->get_options ();

			$options['simple_modules']   = $simple['id_hsm'];
			$options['advanced_modules'] = $advanced['id_hsm'];

			if (count ($options['simple_modules']) > 0)
			{
				foreach ($options['simple_modules'] AS $name)
				{
					$name = 'hsm_'.str_replace ('-', '_', strtolower ($name));
					$module = new $name;
					$newmod[$module->file ()] = $name;
				}

				$options['simple_modules'] = $newmod;
			}
			else
				$options['simple_modules'] = array ();

			if (count ($options['advanced_modules']) > 0)
			{
				$newmod = array ();
				foreach ($options['advanced_modules'] AS $name)
				{
					$name = 'hsm_'.str_replace ('-', '_', strtolower ($name));
					$module = new $name;
					$newmod[$module->file ()] = $name;
				}

				$options['advanced_modules'] = $newmod;
			}
			else
				$options['advanced_modules'] = array ();

			update_option ('headspace_options', $options);
		}
	}
	
	
	function edit_module ($id)
	{
		$headspace = HeadSpace2::get ();
		$module    = $headspace->modules->get ($id);
		
		if ($module)
			$this->render_admin ('module_edit', array ('module' => $module, 'id' => $id));
		else
			$this->render_error (__ ('Invalid module', 'headspace'));
	}
	
	function save_module ($id)
	{
		if (check_ajax_referer ('headspace-save_module'))
		{
			$headspace = HeadSpace2::get ();
			$module    = $headspace->modules->get ($id);

			if ($module)
			{
				$module->update ($_POST);
				$this->render_admin ('module_item', array ('module' => $module));
			}
			else
				$this->render_error (__ ('Invalid module', 'headspace'));
		}
	}
	
	function cancel_module ($id)
	{
		$headspace = HeadSpace2::get ();
		$module    = $headspace->modules->get ($id);

		if ($module)
			$this->render_admin ('module_item', array ('module' => $module));
		else
			$this->render_error (__ ('Invalid module', 'headspace'));
	}
	
	function edit_site_module ($id)
	{
		$headspace = HeadSpace2::get ();
		$module    = $headspace->site->get ($id);

		if ($module)
			$this->render_admin ('site_module_edit', array ('module' => $module, 'id' => $id));
		else
			$this->render_error (__ ('Invalid module', 'headspace'));
	}
	
	function cancel_site_module ($id)
	{
		$headspace = HeadSpace2::get ();
		$module    = $headspace->site->get ($id);

		if ($module)
			$this->render_admin ('site_module', array ('module' => $module));
		else
			$this->render_error (__ ('Invalid module', 'headspace'));
	}
	
	function save_site_module ($id)
	{
		if (check_ajax_referer ('headspace-save_site_module'))
		{
			$headspace = HeadSpace2::get ();
			$module    = $headspace->site->get ($id);

			if ($module)
			{
				$module->update ($_POST);
				$this->render_admin ('site_module', array ('module' => $module));
			}
			else
				$this->render_error (__ ('Invalid module', 'headspace'));
		}
	}
	
	function enable_site_module ($id)
	{
		if (check_ajax_referer ('headspace-site_module_toggle_'.$id))
		{
			$options = get_option ('headspace_options');
			if ($options === false)
				$options = array ();
		
			if (!in_array ($id, $options['site_modules']) && isset ($_POST['file']) && $_POST['file'])
				$options['site_modules'][$_POST['file']] = $id;

			if (count ($options['site_modules']) > 0)
			{
				foreach ($options['site_modules'] AS $key => $value)
				{
					if ($key == '')
						unset ($options['site_modules'][$key]);
				}
			}
		
			update_option ('headspace_options', array_filter ($options));
		}
	}
	
	function disable_site_module ($id)
	{
		if (check_ajax_referer ('headspace-site_module_toggle_'.$id))
		{
			$options = get_option ('headspace_options');
			if ($options === false)
				$options = array ();
		
			if (in_array ($id, $options['site_modules']))
				unset ($options['site_modules'][$_POST['file']]);

			if (count ($options['site_modules']) > 0)
			{
				foreach ($options['site_modules'] AS $key => $value)
				{
					if ($key == '')
						unset ($options['site_modules'][$key]);
				}
			}

			update_option ('headspace_options', array_filter ($options));
		}
	}
	
	function tag_update ($id)
	{
		$headspace = HeadSpace2::get ();
		
		$tags = $headspace->modules->get ('hsm_tags');
		$tags->load ($headspace->get_post_settings ($id));
		
		$tags->suggestions ($id, $_POST['content'], $_POST['type']);
	}
	
	function auto_desc ($id)
	{
		if ($id > 0)
		{
			$post = get_post ($id);

			$excerpt = $post->post_content;
			if ($post->post_excerpt)
				$excerpt = $post->post_excerpt;
		}
		else
			$excerpt = $_POST['content'];

		// Remove any [tags]
		$excerpt = preg_replace ('/\[(.*?)\]/', '', $excerpt);
		$excerpt = trim ($excerpt);

		// Extract 1st paragraph first blank line
		if (function_exists ('mb_strpos'))
		{
			$pos     = mb_strpos ($excerpt, ".");
			if ($pos !== false)
				$excerpt = mb_substr ($excerpt, 0, $pos + 1);
		}
		else
		{
			$pos     = strpos ($excerpt, ".");
			if ($pos !== false)
				$excerpt = substr ($excerpt, 0, $pos + 1);
		}

		// Replace all returns and HTML
		$excerpt = str_replace ("\r", '', $excerpt);
		$excerpt = str_replace ("\n", '', $excerpt);
		$excerpt = strip_tags ($excerpt);
		
		// Restrict it to HS description length setting
		if (function_exists ('mb_substr'))
			$excerpt = mb_substr ($excerpt, 0, 500);
		else
			$excerpt = substr ($excerpt, 0, 500);
			
		echo $excerpt;
	}
	
	function auto_tag ($id)
	{
		global $wp_db_version;

		$headspace = HeadSpace2::get ();
		$settings  = $headspace->get_post_settings ($id);
		
		$tags = $headspace->modules->get ('hsm_tags');
		$tags->load ($settings);

		include (ABSPATH.'wp-admin/admin-functions.php');
	
		$metaid = intval ($_GET['meta']);
		$post = get_post ($id);
		
		$suggestions = $tags->get_suggestions ($post->post_content.' '.$post->post_title);
 		echo HeadSpace2_Plugin::specialchars (implode (', ', $suggestions));
	}
	
	function edit_term ($id)
	{
		$taxonomy = $_POST['taxonomy'];
		
		$term = get_term ($id, $taxonomy);
		$this->render_admin ('tags_edit', array ('term' => $term));
	}
	
	function save_term ($id)
	{
		$taxonomy = $_POST['taxonomy'];

		wp_update_term ($id, $taxonomy, array ('name' => $_POST['tag_name'], 'slug' => $_POST['tag_slug']));
	}
	
	function show_term ($id)
	{
		$taxonomy = $_POST['taxonomy'];
		
		$term = get_term ($id, $taxonomy);
		$this->render_admin ('tags_item', array ('term' => $term));
	}
	
	function merge_terms ($id)
	{
		$taxonomy = $_POST['taxonomy'];
		$from     = intval ($_POST['from']);
		$term     = get_term ($id, $taxonomy);
		
		$items = get_objects_in_term ($from, $taxonomy);
		if (count ($items) > 0)
		{
			foreach ($items AS $item)
				wp_set_object_terms ($item, $term->term_id, $taxonomy, true);
			
			wp_delete_term ($from, $taxonomy);
			$term = get_term ($id, $taxonomy);
		}
		
		$this->render_admin ('tags_item', array ('term' => $term));
		?>
		<script type="text/javascript" charset="utf-8">
		new Draggable ('drag_<?php echo $term->term_id ?>', { revert: true, constraint: 'vertical'});
		</script>
		<?php
	}
	
	function delete_terms ($id)
	{
		$taxonomy = $_POST['taxonomy'];

		wp_delete_term ($id, $taxonomy);
	}
}


$id  = $_GET['id'];
$cmd = $_GET['cmd'];

$obj = new Headspace2_AJAX ($id, $cmd);

?>