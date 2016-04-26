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

class HSM_Keywords extends HSM_Module
{
	var $metakey  = null;
	var $use_tags = true;
	
	function HSM_Keywords ($options = array ())
	{
		if (isset ($options['use_tags']))
			$this->use_tags = $options['use_tags'] ? true : false;
	}
	
	function load ($data)
	{
		if (!$this->use_tags || !class_exists ('HSM_Tags'))
			$this->metakey = $data['metakey'];
	}
	
	function head ()
	{
		if ($this->use_tags && class_exists ('HSM_Tags') && $this->metakey == '')
		{
			$hs = HeadSpace2::get ();
			$tags = $hs->modules->get ('hsm_tags');
			
			$this->metakey = $tags->normalize_tags ($tags->get_the_tags ());
		}

		if ($this->metakey)
		  echo '<meta name="keywords" content="'.$this->metakey.'" />'."\r\n";
	}
	
	function can_quick_edit () { return true; }
	
	function quick_view ()
	{
		echo $this->metakey;
	}
	
	function name ()
	{
		return __ ('Keywords', 'headspace');
	}
	
	function description ()
	{
		return __ ('Allows meta keywords to be defined, seperate from tags (if necessary, disable keyword display in the Tags module)', 'headspace');
	}
	
	function has_config () { return true; }

	function edit_options ()
	{
		?>
		<tr>
			<th width="80"><?php _e ('Use tags', 'headspace'); ?>:</th>
			<td>
				<input type="checkbox" name="use_tags"<?php if ($this->use_tags) echo ' checked="checked"' ?>/>
				<span class="sub"><?php _e ('Checking this will mean that your tags are also used as keywords and you will not be able to modify keywords independently', 'headspace'); ?></span>
			</td>
		</tr>
		<?php
	}
	
	function save_options ($data)
	{
		return array
		(
			'use_tags' => isset ($data['use_tags']) ? true : false,
		);
	}
	
	function edit ($width, $area)
	{
		if (!$this->use_tags || !class_exists ('HSM_Tags'))
		{
?>
<tr>
	<th width="<?php echo $width ?>" align="right">
		<?php if ($area == 'page') : ?>
		<a href="#update" onclick="jQuery('input[name=headspace_metakey]').val(jQuery('#tags-input').val ());return false;">
		<?php endif;?>
		<?php _e ('Keywords', 'headspace') ?>:
		<?php if ($area == 'page') : ?>
		</a>
		<?php endif; ?>
	</th>
	<td>
		<input type="text" name="headspace_metakey" style="width: 95%" value="<?php echo htmlspecialchars ($this->metakey) ?>"/>
	</td>
</tr>
<?php
		}
	}
	
	function save ($data, $area)
	{
		return array ('metakey' => $data['headspace_metakey']);
	}
	
	function file ()
	{
		return basename (__FILE__);
	}
}
?>