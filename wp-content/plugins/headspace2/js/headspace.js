/**
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

function makeSettingsClickable()
{
	jQuery('.settings ul li a').click(function(event)
	{
		var item = this.hash.substr(1);

		jQuery('#head_' + item + ' .option').show ();
		jQuery('#head_' + item).load(wp_hs_base + '?id=' + item + '&cmd=edit', function()
		{
			jQuery('#head_' + item + ' .option').hide ();
		});
		
		return false;
	});
}
	
function add_plugin ()
{
  var text = '<li>';
  text += '<div class="option"><a href="#" onclick="return delete_plugin(this);"><img src="' + headspace_delete + '" alt="delete" width="16" height="16"/></a></div>';
  text +=  document.getElementById('headspace_plugin').options[document.getElementById('headspace_plugin').selectedIndex].innerHTML;
  text += '<input type=\'hidden\' name=\'headspace_plugins[]\' value=\'' + jQuery('#headspace_plugin').val() + '\'/></li>';
  
  jQuery('#headspace_plugins').append (text);
  return false;
}

function delete_plugin (item)
{
	jQuery(item.parentNode.parentNode).remove ();
	return false;
}

function edit_module (item)
{
	jQuery('#id_' + item).load (wp_hs_base + '?id=' + item + '&cmd=edit_module', function () { });
	return false;
}

function cancel_module (item)
{
	jQuery('#id_' + item).load (wp_hs_base + '?id=' + item + '&cmd=cancel_module', function () { });
	return false;
}

function site_module_toggle (item,file,nonce)
{
  if (jQuery('#check_' + item).attr ('checked'))
  {
  	jQuery('#load_' + item).toggle ();
  	jQuery.post(wp_hs_base + '?id=' + item + '&cmd=enable_site_module&_ajax_nonce=' + nonce, { file: file }, function () {jQuery('#site_' + item).removeClass ('disabled'); jQuery('#load_' + item).toggle (); });
  }
  else
  {
  	jQuery('#load_' + item).toggle ();
  	jQuery.post(wp_hs_base + '?id=' + item + '&cmd=disable_site_module&_ajax_nonce=' + nonce, { file: file }, function () {jQuery('#site_' + item).addClass ('disabled'); jQuery('#load_' + item).toggle (); });
  }
  
  return false;
}

function edit_site_module (item)
{
	jQuery('#load_' + item).show ();
	jQuery('#site_' + item).load (wp_hs_base + '?id=' + item + '&cmd=edit_site_module', function () { jQuery('#load_' + item).hide ()})
	return false;
}

function cancel_site_module (item)
{
	jQuery('#load_' + item).show ();
	jQuery('#site_' + item).load (wp_hs_base + '?id=' + item + '&cmd=cancel_site_module', function () { jQuery('#load_' + item).hide ()})
	return false;
}

function auto_tag (item,meta)
{
	jQuery('#edit_' + item).val ('... loading...');
	jQuery.get (wp_hs_base + '?id=' + item + '&cmd=auto_tag&meta=' + meta, [], function (e) {jQuery('#edit_' + item).val (e)});
	return false;
}

function auto_title (item)
{
	jQuery('#edit_' + item).val (jQuery('#title_' + item).text ());
	return false;
}

function auto_desc (item)
{
	jQuery('#edit_' + item).val ('... loading...');
	jQuery.get (wp_hs_base + '?id=' + item + '&cmd=auto_desc', [], function (e) {jQuery('#edit_' + item).val (e)});
	return false;
}
