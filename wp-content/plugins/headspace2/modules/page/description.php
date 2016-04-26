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

class HSM_Description extends HSM_Module
{
	var $description = null;
	var $max_length  = 150;
	
	function HSM_Description ($options = array ())
	{
		if (isset ($options['length']))
			$this->max_length = $options['length'];
	}
	
	function load ($meta)
	{
		// Extract settings from $meta and $options
		if (isset ($meta['description']))
			$this->description = $meta['description'];
	}
	
	function head ()
	{
		if (strlen ($this->description) > 0)
		{
			if (function_exists ('mb_substr'))
				$description = mb_substr (strip_tags ($this->description), 0, $this->max_length);
			else
				$description = substr (strip_tags ($this->description), 0, $this->max_length);
				
			$description = trim (preg_replace ("/[\r\n ]+/", ' ', $description));
			$description = HeadSpace2_Plugin::specialchars ($description);

		  echo '<meta name="description" content="'.$description.'" />'."\r\n";
		}
	}
	
	function name ()
	{
		return __ ('Page description', 'headspace');
	}
	
	function description ()
	{
		return __ ('Allows a short description about the page that is used by search engines', 'headspace');
	}
	
	function has_config () { return true; }
	
	function edit_options ()
	{
		?>
		<tr>
			<th><?php _e ('Max length', 'headspace'); ?>:</th>
			<td>
				<input type="text" name="lengthx" size="5" value="<?php echo $this->max_length ?>"/>
				<span class="sub"><?php _e ('All descriptions will be trimmed to this length', 'headspace'); ?></span>
			</td>
		</tr>
		<?php
	}
	
	function save_options ($data)
	{
		return array ('length' => intval ($data['lengthx']));
	}
	
	function edit ($width, $area)
	{
	?>
	<tr>
		<th width="<?php echo $width ?>" align="right" style="vertical-align: top !important">
			<?php if ($area == 'page') : ?>
			<a href="#update" onclick="jQuery.post (wp_hs_base + '?id=0&cmd=auto_desc', { content: jQuery ('#content').val () }, function (e) {jQuery('textarea[name=headspace_description]').val (e)}); return false">
			<?php endif; ?>
			<?php _e ('Description', 'headspace') ?>:
			<?php if ($area == 'page') : ?>
			</a>
			<?php endif; ?>
		</th>
		<td>
			<textarea rows="2" name="headspace_description" style="width: 95%"><?php echo HeadSpace2_Plugin::specialchars ($this->description) ?></textarea>
		</td>
	</tr>
	<?php
	}
	
	function can_quick_edit () { return true; }
	
	function quick_view ()
	{
		echo $this->description;
	}
	
	function save ($data, $area)
	{
		return array ('description' => trim ($data['headspace_description']));
	}
	
	function file ()
	{
		return basename (__FILE__);
	}
}
?>
