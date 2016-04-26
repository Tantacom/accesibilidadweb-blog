<?php
/*
Plugin Name: Top Commenters Widget
Plugin URI: http://freepressblog.org/plugins/TopCommenters
Description: This plugin will add a list of the most frequent commenters (in descending order) to your blog. The list items will be links (if the commenters supplied a URL with their comments), and will indicate the number of comments they've posted to your blog. Derived from the "Top/Recent Commenters" plugin by Scott Reilly (http://www.coffee2code.com/wp-plugins/)
Author: Jared Bangs
Author URI: http://freepressblog.org/
Version: 4.1
*/ 

// Put functions into one big function we'll call at the plugins_loaded
// action. This ensures that all required plugin functions are defined.
function widget_fp_topcommenters_init() {

	// Check for the required plugin functions. This will prevent fatal
	// errors occurring when you deactivate the dynamic-sidebar plugin.
	if ( !function_exists('register_sidebar_widget') )
		return;

	// This is the function that outputs our sidebar widget.
	function widget_fp_topcommenters($args) {
		
		// $args is an array of strings that help widgets to conform to
		// the active theme: before_widget, before_title, after_widget,
		// and after_title are the array keys. Default tags: li and h2.
		extract($args);

		// Each widget can store its own options. We keep strings here.
		$options = get_option('widget_fp_topcommenters');
		$title = $options['title'];
		$numberOfPeople = $options['numberOfPeople'];
		$peopleToExclude = $options['peopleToExclude'];
		
		// These lines generate our output. Widgets can be very complex
		// but as you can see here, they can also be very, very simple.
		echo $before_widget . $before_title . $title . $after_title;

		echo fp_get_topcommenters($numberOfPeople, split(",",$peopleToExclude));

		echo $after_widget;
	}


	// This is the function that outputs the form to let the users edit
	// the widget's title. It's an optional feature that users cry for.
	function widget_fp_topcommenters_control() {

		// Get our options and see if we're handling a form submission.
		$options = get_option('widget_fp_topcommenters');
		
		// Initialize defaults
		if ( !is_array($options) ) {
			$options = array('title'=>'Top Commenters', 
				'numberOfPeople'=>10,
				'peopleToExclude'=>''
				);
		}


		// Process the post to update the options
		if ( $_POST['fp_topcommenters-submit'] ) {

			// Remember to sanitize and format use input appropriately.
			$options['title'] = strip_tags(stripslashes($_POST['fp_topcommenters-title']));
			$options['numberOfPeople'] = strip_tags(stripslashes($_POST['fp_topcommenters-numberOfPeople']));
			$options['peopleToExclude'] = strip_tags(stripslashes($_POST['fp_topcommenters-peopleToExclude']));

			update_option('widget_fp_topcommenters', $options);
		}


		// Be sure you format your options to be valid HTML attributes.
		$title = htmlspecialchars($options['title'], ENT_QUOTES);
		$numberOfPeople = htmlspecialchars($options['numberOfPeople'], ENT_QUOTES);
		$peopleToExclude = htmlspecialchars($options['peopleToExclude'], ENT_QUOTES);
		
		// Here is our little form segment. Notice that we don't need a
		// complete form. This will be embedded into the existing form.
		echo '<p style="text-align:right;"><label for="fp_topcommenters-title">Title: <input style="width: 200px;" id="fp_topcommenters-title" name="fp_topcommenters-title" type="text" value="'.$title.'" /></label></p>';
		
		echo '<p style="text-align:right;"><label for="fp_topcommenters-numberOfPeople">Quantity: <input style="width: 200px;" id="fp_topcommenters-numberOfPeople" name="fp_topcommenters-numberOfPeople" type="text" value="'.$numberOfPeople.'" /></label></p>';

echo '<p style="text-align:right;"><label for="fp_topcommenters-peopleToExclude">Names to exclude: (comma separated list)<input style="width: 200px;" id="fp_topcommenters-peopleToExclude" name="fp_topcommenters-peopleToExclude" type="text" value="'.$peopleToExclude.'" /></label></p>';


		echo '<input type="hidden" id="fp_topcommenters-submit" name="fp_topcommenters-submit" value="1" />';
	}


	// This registers our widget so it appears with the other available
	// widgets and can be dragged and dropped into any active sidebars.
	register_sidebar_widget('FreePress Top Commenters', 'widget_fp_topcommenters');

	// This registers our optional widget control form. Because of this
	// our widget will have a button that reveals a 300x100 pixel form.
	register_widget_control('FreePress Top Commenters', 'widget_fp_topcommenters_control', 300, 300);
}

function fp_get_topcommenters($num_people = 10, $exclude_from_listing = array()) {
	
	global $wpdb;
	
	$html = "<div class=\"topcommenters\"><ul>";

	if (0 >= $num_people) { $num_people=10; }

	$commenters = fp_get_topcommenters_query($num_people, $exclude_from_listing);

	if (empty($commenters)) return 'query returned no results';
	
	foreach ($commenters as $commenter) {

//		$html .= '<li class="topCommentersListItem">';

		$user_data = $wpdb->get_row("SELECT display_name, user_url FROM $wpdb->users WHERE display_name = '$commenter->comment_author'");
		if ( empty($user_data->user_url) ) {
			$html .= '<img src="/wp-content/themes/redactores/assets/images/icon_comment.gif"> &nbsp;' .$commenter->comment_author;
		}
		else {
			$html .= '<img src="/wp-content/themes/redactores/assets/images/icon_comment.gif"> &nbsp; <a title="Visita la Web de ' . $commenter->comment_author . '\'s" href="' . $user_data->user_url . '">' . $commenter->comment_author . '</a>';
		}

		$html .= ' (' . $commenter->total_comments . ')';
//		$html .= '</li>';
		$html .= "\n<br />";
	}

	return $html . "</ul></div>";
}

function fp_get_topcommenters_query($num_people, $exclude_from_listing) {

	global $wpdb, $tablecomments, $id;
	if (!isset($tablecomments)) $tablecomments = $wpdb->comments;

	$sql = "SELECT comment_author, comment_author_url, comment_author_email, comment_post_ID, ";
	$sql .= "COUNT(comment_ID) AS total_comments ";
	$sql .= "FROM $tablecomments ";
	$sql .= "WHERE comment_approved = '1' AND comment_author != '' ";

	if (get_option('WPTagboardPostID')) {
		$sql .= "AND comment_post_ID <> " . get_option('WPTagboardPostID') . " ";
	}

	// Exclude any specified names
	if (!empty($exclude_from_listing)) {
	   foreach ($exclude_from_listing as $exclude) {
		   $sql .= "AND comment_author != '$exclude' ";
		}   
	}

	$sql .= "GROUP BY comment_author ORDER BY ";
	$sql .= "total_comments ";
	$sql .= "DESC, comment_post_ID DESC LIMIT $num_people";
	$commenters = $wpdb->get_results($sql);

	return $commenters;
}

// Run our code later in case this loads prior to any required plugins.
add_action('plugins_loaded', 'widget_fp_topcommenters_init');

?>
