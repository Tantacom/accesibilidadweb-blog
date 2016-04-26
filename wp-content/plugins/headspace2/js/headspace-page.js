jQuery(document).ready( function()
{
	hs_setup_tags ();

	// Only do this if editing a page
	jQuery('#tags-input').hide();
	tag_update_quickclicks();
	
	// add the quickadd form
	jQuery('#jaxtag').prepend('<span id="ajaxtag"><input type="text" name="newtag" id="newtag" class="form-input-tip" size="16" autocomplete="off" value="'+postL10n.addTag+'" /><input type="button" class="button" id="tagadd" value="' + postL10n.add + '" tabindex="3" /><input type="hidden"/><input type="hidden"/><span class="howto">'+postL10n.separate+'</span></span>');
	jQuery('#tagadd').click( tag_flush_to_text );
	jQuery('#newtag').focus(function() {
		if ( this.value == postL10n.addTag )
			jQuery(this).val( '' ).removeClass( 'form-input-tip' );
	});
	
	jQuery('#newtag').blur(function() {
	if ( this.value == '' )
		jQuery(this).val( postL10n.addTag ).addClass( 'form-input-tip' );
		});
});


function tag_flush_to_text() {
 var newtags = jQuery('#tags-input').val() + ',' + jQuery('#newtag').val();
 // massage
 newtags = newtags.replace( /\s+,+\s*/g, ',' ).replace( /,+/g, ',' ).replace( /,+\s+,+/g, ',' ).replace( /,+\s*$/g, '' ).replace( /^\s*,+/g, '' );
 jQuery('#tags-input').val( newtags );
 tag_update_quickclicks();
 jQuery('#newtag').val('');
 jQuery('#newtag').focus();
 refresh_tags ();
 return false;
}

function new_tag_remove_tag() {
 var id = jQuery( this ).attr( 'id' );
 var num = id.substr( 10 );
 var current_tags = jQuery( '#tags-input' ).val().split(',');
 delete current_tags[num];
 var new_tags = [];
 jQuery.each( current_tags, function( key, val ) {
   if ( val && !val.match(/^\s+$/) && '' != val ) {
     new_tags = new_tags.concat( val );
   }
 });
 jQuery( '#tags-input' ).val( new_tags.join( ',' ).replace( /\s*,+\s*/, ',' ).replace( /,+/, ',' ).replace( /,+\s+,+/, ',' ).replace( /,+\s*$/, '' ).replace( /^\s*,+/, '' ) );
 tag_update_quickclicks();
 jQuery('#newtag').focus();
 
 refresh_tags ();
 return false;
}



// Copied from post.js
// this file contains all the scripts used in the post/edit page

function new_tag_remove_tag() {
 var id = jQuery( this ).attr( 'id' );
 var num = id.substr( 10 );
 var current_tags = jQuery( '#tags-input' ).val().split(',');
 delete current_tags[num];
 var new_tags = [];
 jQuery.each( current_tags, function( key, val ) {
   if ( val && !val.match(/^\s+$/) && '' != val ) {
     new_tags = new_tags.concat( val );
   }
 });
 jQuery( '#tags-input' ).val( new_tags.join( ',' ).replace( /\s*,+\s*/, ',' ).replace( /,+/, ',' ).replace( /,+\s+,+/, ',' ).replace( /,+\s*$/, '' ).replace( /^\s*,+/, '' ) );
 tag_update_quickclicks();
 jQuery('#newtag').focus();
 return false;
}

function tag_update_quickclicks() {
 if ( jQuery( '#tags-input' ).length == 0 )
   return;
 var current_tags = jQuery( '#tags-input' ).val().split(',');
 jQuery( '#tagchecklist' ).empty();
 shown = false;
// jQuery.merge( current_tags, current_tags ); // this doesn't work anymore, need something to array_unique
 jQuery.each( current_tags, function( key, val ) {
   val = val.replace( /^\s+/, '' ).replace( /\s+$/, '' ); // trim
   if ( !val.match(/^\s+$/) && '' != val ) {
     txt = '<span><a id="tag-check-' + key + '" class="ntdelbutton">X</a>&nbsp;' + val + '</span> ';
     jQuery( '#tagchecklist' ).append( txt );
     jQuery( '#tag-check-' + key ).click( new_tag_remove_tag );
     shown = true;
   }
 });
 if ( shown )
   jQuery( '#tagchecklist' ).prepend( '<strong>'+postL10n.tagsUsed+'</strong><br />' );
}

function tag_flush_to_text() {
 var newtags = jQuery('#tags-input').val() + ',' + jQuery('#newtag').val();
 // massage
 newtags = newtags.replace( /\s+,+\s*/g, ',' ).replace( /,+/g, ',' ).replace( /,+\s+,+/g, ',' ).replace( /,+\s*$/g, '' ).replace( /^\s*,+/g, '' );
 jQuery('#tags-input').val( newtags );
 tag_update_quickclicks();
 jQuery('#newtag').val('');
 jQuery('#newtag').focus();
 return false;
}

function tag_save_on_publish() {
 if ( jQuery('#newtag').val() != postL10n.addTag )
   tag_flush_to_text();
}

function tag_press_key( e ) {
 if ( 13 == e.keyCode ) {
   tag_flush_to_text();
   return false;
 }
}
