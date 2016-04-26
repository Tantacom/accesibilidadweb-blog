function hs_setup_tags ()
{
 var clonebox = jQuery('#suggestions').clone ();

 clonebox.id = 'suggestions1';
 jQuery ('#suggestions').remove ();
 clonebox.id = 'suggestions';

 jQuery ('#tagsdiv .inside').append (clonebox);
 jQuery (clonebox).show ();
}

function refresh_tags ()
{
 var keywords = jQuery( '#tags-input' ).val().split(',');
 var suggested = jQuery('#suggested_tags a');
 var found = false;
 
 jQuery.each( suggested, function( key, val )
 {
   found = false;
   
    // Does this exist in the keywords?
    jQuery.each(keywords, function (subkey, subval) {
      if (val.innerHTML == subval)
        found = true;
    })
    
    if (found)
      jQuery(val).removeClass ('disabled').addClass('enabled');
    else
      jQuery(val).removeClass ('enabled').addClass('disabled');
 });
}

function add_tag (item,area)
{
 if (area == 'page')
   var element = '#tags-input';
 else
   var element = '#tags-input_' + area;
   
 var word = jQuery(item).text ();
 
 // Does this tag already exist?
 if (jQuery(element).val ().indexOf (word) == -1)
 {
   if (jQuery(element).val ().length > 0)
     jQuery(element).val (jQuery(element).val () + ', ');
     
   jQuery(element).val(jQuery(element).val () + word);
   
   jQuery(item).addClass('enabled');
   jQuery(item).removeClass('disabled');
 }
 else
 {
   var val = jQuery(element).val ();
   
   val = val.replace (word, '');
   val = val.replace (' , ', ', ');
   val = val.replace (',,', ', ');
   val = val.replace ('  ', ' ');
   val = jQuery.trim (val);
   
   if (val.charAt (val.length - 1) == ',')
     val = val.substr (0, val.length - 1);

   if (val.charAt (0) == ',')
     val = val.substr (1);
   
   jQuery(element).val (jQuery.trim (val));
   jQuery(item).addClass('disabled');
   jQuery(item).removeClass('enabled');
 }
 
 tag_update_quickclicks ();
}

function add_all_tags (area,keywords)
{
  if (area == 'page')
   var element = document.getElementById ('tags-input');
 else
   var element = document.getElementById ('tags-input_' + area);
 
 element.value += ',';

  jQuery.each(keywords, function (index,item)
  {
    element.value = element.value.replace (item + ',', '');
    element.value = element.value.replace (item, '');
    element.value += item + ', ';
  });
  
  element.value = element.value.replace ('  ', '');
  
  element.value = jQuery.trim (element.value);
  if (element.value.charAt (element.value.length - 1) == ',')
   element.value = element.value.substr (0, element.value.length - 1);

 update_keywords (area, keywords);
}

function update_keywords (area, keywords)
{
  if (area == 'page')
   var element = document.getElementById ('tags-input');
 else
   var element = document.getElementById ('tags-input_' + area);
   
 var matched = new Array ();
 var words   = element.value.split (',');

 for (var index = 0, len = words.length; index < len; ++index)
   words[index] = jQuery.trim (words[index]);

 jQuery.each (keywords, function(index,item) 
 {
   var onoff = false;
 
   // See if the word exists in the field - if so highlight the word, else de-highlight
   if (jQuery.inArray (item, words) != -1)
     matched[matched.length + 1] = item;
 });

 // Now go through the suggested words and highlight or dehighlight them
 jQuery('#suggested_tags a').each (function (pos,item)
 {
   if (jQuery.inArray (item.innerHTML, matched) != -1)
     item.className = 'enabled';
   else
     item.className = 'disabled';
 });
 
 tag_update_quickclicks ();
}

function update_suggestions (item,type)
{
 jQuery('#tag_loading').show ();
 jQuery('#suggestions').load (wp_hs_base + '?id=' + item + '&cmd=tag_update', { content: jQuery('#content').val () + ' ' + jQuery('#title').val (), type: type },
   function() { jQuery('#tag_loading').hide ()});
}
