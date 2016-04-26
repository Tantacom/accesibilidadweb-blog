<?php

/**
 * HeadSpace
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

include (dirname (__FILE__).'/modules.php');
include (dirname (__FILE__).'/site.php');
include (dirname (__FILE__).'/inline_tags.php');


class HeadSpace2 extends HeadSpace2_Plugin
{
	var $modules  = null;
	var $site     = null;
	var $disabled = false;
	
	function HeadSpace2 ()
	{
		$this->register_plugin ('headspace', dirname (__FILE__));
		
		// Load active modules
		$this->modules = new HSM_ModuleManager ($this->get_active_modules ());
		$this->site    = new HS_SiteManager ($this->get_site_modules ());

		// Add our own filter
		$this->add_action ('wp_head');
		$this->add_action ('headspace_wp_head', 'wp_head');   // For custom themes
		$this->add_action ('login_head', 'wp_head');
		
		// 'plugins_loaded' seems to cause problem on non-english sites for 2.7
		$this->add_action ('init', 'plugins_loaded');
	}

	function get_simple_modules ()
	{
		$options = $this->get_options ();
		return $options['simple_modules'];
	}
	
	function get_advanced_modules ()
	{
		$options = $this->get_options ();
		return $options['advanced_modules'];
	}
	
	function get_site_modules ()
	{
		$options = get_option ('headspace_options');
		if ($options === false)
			$options = array ();

		if (!isset ($options['site_modules']))
			$options['site_modules'] = array ();
		return $options['site_modules'];
	}
	
	function get_active_modules ()
	{
		return array_merge ($this->get_simple_modules (), $this->get_advanced_modules ());
	}
	
	function get_options ()
	{
		$options = get_option ('headspace_options');
		if ($options === false)
			$options = array ();

		if (!isset ($options['simple_modules']))
			$options['simple_modules'] = array ('page_title.php' => 'hsm_pagetitle', 'description.php' => 'hsm_description', 'tags.php' => 'hsm_tags');

		if (!isset ($options['advanced_modules']))
			$options['advanced_modules'] = array ('javascript.php' => 'hsm_javascript', 'stylesheet.php' => 'hsm_stylesheet');
		
		if (!isset ($options['inherit']))
			$options['inherit'] = true;
		return $options;
	}
	
	function extract_module_settings ($data, $area)
	{
		$data = stripslashes_deep ($data);
		
		$modules = $this->modules->get_restricted ($this->get_simple_modules (), array (), $area);
		$modules = array_merge ($modules, $this->modules->get_restricted ($this->get_advanced_modules (), array (), $area));
		
		$save = array ();
		if (count ($modules) > 0)
		{
			foreach ($modules AS $pos => $module)
				$save = array_merge ($save, $modules[$pos]->save ($data, $area));
		}
		
		return $save;
	}

	function get_current_settings ($override = '')
	{
		global $post;
		
		if ($this->disabled == true)
			return array ();				// This is useful for when we call a filter to prevent infinite loops
			
		if ($override)
			$meta[] = $override;
		else if (is_admin ())
			$meta[] = $this->get_post_settings (intval ($_GET['post']));
		else
		{
			if (!is_admin ())
				$meta[] = get_option ('headspace_global');   // We don't get this in admin mode as it will affect our settings

			// Decide what kind of page we're on
			// Note that on the home page we want headspace_home settings when outside the loop, but post settings inside
			if (is_single () || is_page () || ((is_front_page () || is_home () || is_archive () || is_search ()) && in_the_loop ()) || $this->is_posts_page ())
			{
				$meta[] = get_option ('headspace_post');
				if (!empty ($post->ID))
					$meta[] = $this->get_post_settings ($post->ID);
			}
			else if (is_404 ())
				$meta[] = get_option ('headspace_404');
			else if (is_category ())
			{
				$meta[] = get_option ('headspace_category');
				$meta[] = get_option ('headspace_cat_'.intval (get_query_var ('cat')));
			}
			else if (is_author ())
				$meta[] = get_option ('headspace_author');
			else if (is_home () || is_front_page ())
				$meta[] = get_option ('headspace_home');
			else if (is_search ())
				$meta[] = get_option ('headspace_search');
			else if (function_exists ('is_tag') && is_tag ())
				$meta[] = get_option ('headspace_tags');
			else if (is_archive ())
				$meta[] = get_option ('headspace_archive');
			else if (strpos ($_SERVER['REQUEST_URI'], 'wp-login.php') !== false)
				$meta[] = get_option ('headspace_login');

		}

		$meta = array_filter ($meta);

		// Do we merge the settings?
		$options = $this->get_options ();
		if ($options['inherit'] !== true && count ($meta) > 1)
			$meta = array ($meta[count ($meta) - 1]);
		
		// Merge the settings together
		$merged = array ();
		foreach ($meta AS $item)
		{
			if (!empty ($item))
			{
				foreach ($item AS $key => $value)
				{
					if (!empty ($value))
						$merged[$key] = $value;
				}
			}
		}

		$meta = $merged;
		if (!$override && !is_admin ())
		{
			// Replace any inline tags
			if (count ($meta) > 0)
			{
				foreach ($meta AS $key => $value)
					$meta[$key] = HS_InlineTags::replace ($value, $post);
			}
			
			$meta = array_filter ($meta);
		}

		$this->meta = $meta;
		return $this->meta;
	}
	
	function get_post_settings ($id)
	{
		$meta   = array ();
		$custom = get_post_custom ($id);

		if (count ($custom) > 0 && is_array ($custom))
		{
			foreach ($custom AS $key => $value)
			{
				$var = substr ($key, 0, 10);
				if ($var == '_headspace')
				{
					$field = substr ($key, 11);
					$meta[$field] = $value;
				}
			}
			
			// Flatten any arrays with one element
			foreach ($meta AS $field => $value)
			{
				if (is_array ($value) && count ($value) == 1)
					$meta[$field] = $value[0];
			}
		}
		
		return $meta;
	}
	
	function save_post_settings ($postid, $settings)
	{
		global $wpdb;

		// Try to find existing headspace meta for this post
		$existing = has_meta ($postid);
		$ids      = array ();

		// Save each variable
		foreach ($settings AS $var => $values)
		{
			$field = '_headspace_'.$var;

			// Does this field already exist?
			$ids = array ();
			if (is_array ($existing) && count ($existing) > 0)
			{
				foreach ($existing AS $item)
				{
					if ($item['meta_key'] == $field)
						$ids[] = $item['meta_id'];
				}
			}

			if (!is_array ($values))
				$values = array ($values);

			// Delete any extra
			if (count ($values) < count ($ids))
			{
				$count = 0;
				foreach ($ids AS $pos => $id)
				{
					$count++;
					if ($count > (count ($ids) - count ($values)))
						$rest[] = $id;
					else
						delete_meta ($id);
				}
			
				$ids = $rest;
			}

			foreach ($values AS $pos => $value)
			{
				// Update or insert
				if (!isset ($ids[$pos]) && !empty ($value))
					$wpdb->query ("INSERT INTO {$wpdb->postmeta} (post_id,meta_key,meta_value) VALUES ('$postid','$field','".wpdb::escape ($value)."')");
				else if (isset ($ids[$pos]) && !empty ($value))
					update_meta ($ids[$pos], $field, $value);
				else if (isset ($ids[$pos]) > 0)
					delete_meta ($ids[$pos]);
			}
		}
	}
	
	function reload (&$obj)
	{
		$headspace = HeadSpace2::get ();
		$obj->load ($headspace->get_current_settings ());
	}

	function wp_head ()
	{
		$modules = array_merge ($this->site->get_active (), $this->modules->get_active ($this->get_current_settings ()));

		if (count ($modules) > 0)
		{
			foreach ($modules AS $module)
				$module->head ();
		}
	}
	
	function plugins_loaded ()
	{
		$modules = array_merge ($this->site->get_active (), $this->modules->get_active ($this->get_current_settings ()));

		if (count ($modules) > 0)
		{
			foreach ($modules AS $module)
				$module->plugins_loaded ();
		}
	}
	
	function is_posts_page ()
	{
		global $wp_query;

		if ($wp_query)
		{
			$post = $wp_query->get_queried_object ();
			return is_home () && get_option ('show_on_front') == 'page' && $post->ID == get_option('page_for_posts');
		}
		return false;
	}

	function debug ($text)
	{
		echo '<pre>';
		echo '<h4>HeadSpace Debug</h4>';
		echo $text;
		echo '</pre>';
	}
	
	function &get ()
	{
    static $instance;

    if (!isset ($instance))
		{
			$c = __CLASS__;
			$instance = new $c;
    }

    return $instance;
	}
}

// Cause the singleton to fire
HeadSpace2::get ();
?>