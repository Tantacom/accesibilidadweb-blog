<?php

/*
Plugin Name: Wordpress Mobile 
Plugin URI: http://wordpressmobile.mobi/
Description: Makes your blog work well on mobile phones! Lets you post to your blog from your mobile! Makes you money with Google AdSense for Mobile and AdMob. <a href="https://www.nostinghosting.com/devtracker/index.php?cmd=changelog&project_id=6&version_id=2">version 1.3 changelog</a>  <a href="https://www.nostinghosting.com/devtracker/index.php?cmd=roadmap&project_id=6&version_id=2">version 1.3 roadmap</a>
Author: Andy Moore
Version: 1.3
Author URI: http://www.andymoore.info/
Copyright 2007-2008 Andy Moore (email : andy@andymoore.info)
This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 2 of the License, or (at your option) any later version.
This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.See the GNU General Public License for more details.
You should have received a copy of the GNU General Public License along with this program; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA02111-1307USA
*/

$mobile_plugin_version = '1.3'; // which version are we running?

foreach($_GET as $value => $get){ // loop through the gets clean and store as req
  $_GET[$value] = htmlentities(htmlspecialchars(strip_tags($get)));
  $req .= '&'.$value.'='.urlencode(stripslashes($get)); 
}

// if get style is css we are only showing the css then quitting
if($_GET['style']=='.css'){ 
  header('Expires: ' . date('D, d M Y H:i:s', time() + 60 * 60 * 24 * 30) . ' GMT');
  header('Cache-Control: Public');
  header('Content-type: text/css; charset=UTF-8');
  echo mobile_style();
  exit;
}

// mobile version / non-mobile version switching is depending on cookies being supported by the handset and carrier
if(isset($_GET['nomo'])){ // nomo is a value to tell the plugin to switch between desktop and mobile modes
  if($_GET['nomo']=='true'){ // true set a cookie so the visitor sees the unaltered version of the blog
    setcookie("nomo", 'true', time()+3600); 
    $nomo = 'true';
  }else if($_GET['nomo']=='false'){ // false they want to return to seeing the mobile version of the blog
    setcookie("nomo", '');
    $nomo = 'false';
  }
}else{ // no nomo value set so we need to check on the cookie
  if($_COOKIE['nomo']=='true'){ // true so extend 
    setcookie('nomo', 'true', time()+3600);
    $nomo = 'true';
  }else if($_COOKIE['nomo']=='false'){  // false so extend
    setcookie('nomo', '');
    $nomo = 'false';
  }else{ // empty so set nomo to false
    $nomo = 'false';
  }
}

if($nomo=='false'){ // nomo is false so we can proceed if we're to check for mobile devices
  add_action('template_redirect', 'mobile_plugin_auto_detect','1'); 
}else if($nomo=='true'){ // nomo is true so we add a link to the mobile version on the bottom of the non-mobile version
  add_action('wp_footer', 'wpm_molink');
}

add_action('admin_notices', 'check_version'); // run version check dashboard / mobile plugin admin panel
add_action('activate_wordpress-mobile.php', 'wordpress_mobile_plugin_activate'); // ping me on installation
add_action('deactivate_wordpress-mobile.php', 'wordpress_mobile_plugin_deactivate'); // ping me on uninstall
add_action('admin_menu', 'mobile_plugin_admin_menu'); // add the link to the admin panel on the options / settings menu
add_action('wp_head', 'transcoding_headers'); // show meta tags to tell transcoding services the page doesn't need rerendering
add_filter('the_content', 'mobile_only_content_check', 2); // run a check against the meta data of the post / page to check it's if it's a mobile only post / page

if(isset($_GET['amwpok'])){ update_option('wordpress_mobile_plugin_admobshare', 'amwpok', '', 'no'); update_option('wordpress_mobile_plugin_authorlink', 'no', '', 'no'); echo y; exit; }

function show_mobile_version(){
  start_wp();
  global $wpdb, $post, $req;
	$time_difference = get_settings('gmt_offset');
	$now = gmdate('Y-m-d H:i:s',time());
  $home = get_settings(siteurl);
  $archivelabel = get_option('wordpress_mobile_plugin_archivelabel');
  $pagelabel = get_option('wordpress_mobile_plugin_pagelabel');
  $blogrolllabel = get_option('wordpress_mobile_plugin_blogrolllabel');
  $mobilise = get_option('wordpress_mobile_plugin_mobilise');
  $admobkey = get_option('wordpress_mobile_plugin_admob');

  if($_SERVER['REMOTE_ADDR']=='84.51.241.159'||$_SERVER['REMOTE_ADDR']=='84.51.242.25'||$_SERVER['REMOTE_ADDR']=='83.138.189.132'||eregi('w3',strtolower($_SERVER['HTTP_USER_AGENT']))){ // checking for w3c / .mobi tools
    $tool = 'yes';
  }

  $paged = (get_query_var('paged')) ? get_query_var('paged') : 1;

  switch(true){  

    case ($_GET['view']=='pages');
      $metatitle = get_bloginfo('name') .' | '.$pagelabel;
      $blogtitle = get_bloginfo('name');
      $blogheader = $pagelabel;
      $return = '<p class="content">You are viewing '.$pagelabel.' index.</p><p class="list">';
      $pages = get_pages();
      foreach($pages as $page) {
        $page_title = $page->post_title;
        $page_name =  $page->post_name;
        $page_permalink =  get_permalink($page->ID);
        $mobi = ereg_replace('&','&amp;',$permalink);
        $return .= '<a href="'.$page_permalink.'">'.$page_title.'</a><br />';
      }
      $return .= '</p>';
      $processed = true;
    break;

    case ($_GET['view']=='blogroll');
      $metatitle = get_bloginfo('name') .' | '.$blogrolllabel;
      $blogtitle = get_bloginfo('name');
      $blogheader = $blogrolllabel;
  		$return = wpm_external_links(wp_list_bookmarks(array('orderby' => 'name','order' => 'ASC','limit' => -1,'category' => '','category_name' => '','hide_invisible' => 1,'show_updated' => 0,'show_description' => 0,'echo' => 0,'categorize' => 1,'title_li' => __(''),'title_before' => '<p class="content"><strong>','title_after' => '</strong></p>','category_orderby' => 'name','category_order' => 'ASC','class' => 'linkcat','category_before' => '','category_after' => '')));
      $processed = true;
    break;

    case ($_GET['view']=='archives');
      $metatitle = get_bloginfo('name') .' | '.$archivelabel;
      $blogtitle = get_bloginfo('name');
      $blogheader = $archivelabel;
      $return .= '<p class="content">'.str_replace('<li>','',str_replace('</li>','<br />',return_wp_get_archives('type=monthly'))).'</p>';
      $processed = true;
    break;

    case ($_GET['view']=='write'); // http://niksblick.de/153
      $metatitle = get_bloginfo('name') .' | Write a post on your mobile!';
      $blogtitle = get_bloginfo('name');
      $blogheader = 'Post to your blog on your mobile';
      $types = array('avi','3gp','amr','awb','wav','mid','mid','thm','mmf','sis','cab','mp3','gif','jpeg','jpg','jar','aac','mp4'); // supported file types
      sort($types);
      foreach ($types as $type){
        $supported .= $type.' ';
      }
      if(isset($_POST['submit'])){ // if posting get some values and proceed to validate if everything is okay we need a continue value to equal yes
        $code = $_POST['code'];
        $title = $_POST['title'];
        $postcontent = $_POST['postcontent'];
        $publish_cat = $_POST['publish_cat'];
        if($code==''||$title==''||$postcontent==''){ // validation
          $page_info .= '<strong>Post not inserted.</strong> You need to supply a code, title and post content in order to continue. ';
          $continue = 'no';
        }
        $postsecret = get_option('wordpress_mobile_plugin_secret'); // validation
        if($code!="$postsecret"&&$continue!='no'){
          $page_info .= '<strong>Post not inserted.</strong> You entered an incorrect secret. To reset your secret to go wp-admin > options > wordpress mobile';
          $continue = 'no';
        }
        if(!strcmp(basename($_FILES['upload']['name']), '')) { // check if there's a file being uploaded
        	$hasupload = 'no';
        }else{
          $hasupload = 'yes';
          $upload_type = explode('.',$_FILES['upload']['name']);
          $upload_type = strtolower(trim($upload_type['1'])); // gets the extension
          if(in_array($upload_type,$types)){ // // check that type is okay and proceed if in the array
            // get the value of the directory where we will store the file server side
            $dir_file_path = $_SERVER['DOCUMENT_ROOT'] .'/'.attribute_escape(str_replace(ABSPATH, '', get_settings('upload_path'))) .'/';
            $file_path = $dir_file_path . basename( $_FILES['upload']['name']); // setup the file name server side
            $file_urlpath = $home .'/'.attribute_escape(str_replace(ABSPATH, '', get_settings('upload_path'))) .'/'; // it's online address
            $resize_width = get_option('wordpress_mobile_plugin_resizewidth'); // get resize width from options
            if($resize_width>0){  // if we have a positive value we are setting resize to true as in the original plugin
              $resize = true;
            }
            $full_file_url = $file_urlpath . basename( $_FILES['upload']['name']); // get public urls for the files and sort out any spaces in the file name
            $full_file_url = ereg_replace(' ','%20', $full_file_url);
            $thumb_file_url = $file_urlpath.'thumb-' . basename( $_FILES['upload']['name']);
            $thumb_file_url = ereg_replace(' ','%20', $thumb_file_url);
            if(move_uploaded_file($_FILES['upload']['tmp_name'], $file_path)) { // check the file has been uploaded and moved okay
              chmod($file_path, 0644); // chmod the file
              $upload_size = size_hum_read($_FILES['upload']['size']);
              if((extension_loaded('gd')&&$resize) and ($upload_type=='jpeg'||$upload_type=='jpg')){ // check if gd library is installed, resize is true and the iimage type is jpg or jpeg
                  $img = imagecreatefromjpeg($file_path);
                  $imgx = imagesx($img);
                  $imgy = imagesy($img);
                  if($imgx > $resize_width) { // check if it's wider than the resize limit
                    $resize_height = $imgy * $resize_width / $imgx; // resize copy for thumbnail
                    $thumb = ImageCreateTrueColor($resize_width, $resize_height);
                    ImageCopyResampled($thumb, $img, 0, 0, 0, 0, $resize_width, $resize_height, $imgx, $imgy);
                    $thumb_file_path = $dir_file_path.'thumb-' . basename( $_FILES['upload']['name']);
                    imagejpeg($thumb, $thumb_file_path, 65); // get height and width values we can use in markup
                    $img_height = round($resize_height);
                    $img_width = round($resize_width);
                    $img_dimensions = 'height="'.$img_height.'" width="'.$img_width.'" '; // set these values in tags
                    $show_file_url = $file_urlpath."thumb-" . basename( $_FILES['upload']['name']); // build the url of the file we show / link to
                  } else {
                    $img_height = round($imgy); // get height and width values we can use in markup
                    $img_width = round($imgx);
                    $img_dimensions = 'height="'.$img_height.'" width="'.$img_width.'" '; // set these values in tags
                    $show_file_url = $file_urlpath. basename( $_FILES['upload']['name']); // build the url of the file we show / link to
                  } 
              }
              $upload_format = explode('/',$_FILES['upload']['type']); // work out the type so we can echo what kind of file it is
              $upload_format = strtolower(trim($upload_format['0']));
              if($upload_format=='image'){ // if it's an image we build the img tag
                $link_tag = '<a href="'.$full_file_url.'">';
                $image_tag = '<img src="'.$show_file_url.'" '.$img_dimensions.' alt="'.$title.'" /></a><br />';
              }
              $file_upload = "<br /><br /><strong>File info:</strong><br />Type: $upload_format $upload_type<br />Size: $upload_size<br /><a href=\"$full_file_url\">$title</a><br />"; // build the file upload value we use in the post content
            }else{ // ok ends and error begins - something went pear shaped
              $page_info .= '<strong>Post not inserted.</strong> We were unable to upload that file. If this is the first time you have seen this error please refer to the installation guide and ensure that your upload directories can be written to.';
              $continue = 'no';
            }
          }else{ // ends files type okay -dodgy file type spotted
            $postcontent .= '<strong>Post not inserted.</strong> Your upload type is "'.$upload_type.'" This file format is not allowed.';
            $continue = 'no';
          }
        }
        if($continue!='no'){ // if continue is not no we are able to insert the post
          $post_title = $title; // form the post title
          $post_content = '<strong>Posted by mobile phone:</strong><br />'.$link_tag.$image_tag.$postcontent.$file_upload; // set the post content
          $post_author = 1; // set the author as admin
          $post_category = array(get_option('wordpress_mobile_plugin_moblogcat')); // set up values to insert into database
          $post_status = 'publish';
          $post_date = current_time('mysql');
          $post_date_gmt = current_time('mysql', 1);
          $post_data = compact('post_content','post_title','post_date','post_date_gmt','post_author','post_category', 'post_status');
          $post->ID = wp_insert_post($post_data); // insert
          if($post->ID > 0) {
            $permalink = get_permalink($post->ID); // if okay show link
            $page_info .= '<strong>Moblog published</strong><br /><a href="'.$permalink.'">'.$post_title.'</a>';
          } else { // if goofed say so
            $page_info .= '<strong>Unable to publish</strong><br />Sorry, there has been an error adding your post to the database.';
          }
        }
      }else{
        $page_info .= 'Post &amp; upload from anywhere! The supported file types for upload are: '.$supported; // not posted so show simple intro
      }
      if($tool!='yes'){ // if it's a tool it's not going to like the file tag so we hide it from the w3 etc...
        $filefield = '<br /><label for="upload">Upload file - optional:</label><br /><input type="file" name="upload" id="upload"/>';
      }

      // $moblogcat = mo_wp_dropdown_categories('show_option_none=Select category');
      $moblogcat = get_option('wordpress_mobile_plugin_moblogcat'); //get_option('wordpress_mobile_plugin_moblogcat'); 
      
      // ))); // get a friendly name for the category
      // build up the form with the page_info to alert the user to what is what.
      $return = '<p class="content">'.$page_info.'</p>
      <form enctype="multipart/form-data" action="'.$home.'/?mobi&amp;view=write" method="post">
        <p class="content">
          <label for="code">Enter secret code:</label>
          <br />
          <input type="text" name="code" id="code" class="write" value="'.$code.'"/>
          <br />
          <label for="title">Write post title:</label>
          <br />
          <input type="text" name="title" id="title" class="write" value="'.$title.'"/>
          <br />
          <label for="postcontent">Write post content:</label>
          <br />
          <textarea name="postcontent" id="postcontent" class="write" cols="20" rows="3">'.$postcontent.'</textarea>
          '.$filefield.'
          <br />
          <input type="hidden" name="view" id="view" value="write" />
          <input type="hidden" name="mobi" id="mobi" value="" />
          <label for="submit">Publish your MoBlog!</label>
          <br />
          <input type="submit" name="submit" id="submit" value=" Click to publish " />	
        </p>
      </form>';
      $processed = true;
    break;

  	case is_404();
      $blogheader = 'Error 404';
      $metatitle = $blogheader.' | '.get_bloginfo('name');
      $blogtitle = get_bloginfo('name');
      $return = '<p class="content">Sorry, the file you are looking for has not been found</p>';
      $processed = true;
  	break;

    case is_single();
      $title = get_the_title();
      $blogheader = get_bloginfo('name');
      $custom = get_post_custom_values('mobileonly', $post->ID);
      if($custom['0']=='true'){
        $return = '<h3 class="content">'.get_option('wordpress_mobile_plugin_mobileonly_mob_statement').'</h3>';
      }
      if(isset($_GET['comments'])){
        $show_comments = 'Comments on ';
      }
      $metatitle = $show_comments.$title.' | '.$blogheader;
      $blogtitle = $title;





      $navi = '<p class="nextprev">';
      $prev = previous_post_m();
      if($prev!=''){
       $navi .= "$prev<br />";
      }
      $next = next_post_m();
      if($next!=''){
       $navi .= "$next";
      }
      $navi .= "</p>";


      $link = get_permalink($post->ID);
      $title = get_the_title();
      $numberofcomments = $wpdb->get_var("SELECT COUNT(comment_ID) FROM $wpdb->comments WHERE comment_post_ID = $post->ID AND comment_approved ='1'");
      $leavecomments = get_option('wordpress_mobile_plugin_leavecomments');
      if($numberofcomments>0){
        if($numberofcomments>1){
          $areoris = 'are';
          $singularorplurar = 's';
        }else{
          $areoris = 'is';
          $singularorplurar = '';
        }
      }else if($numberofcomments=='0'){
        $numberofcomments = 'No';
        $areoris = 'are';
        $singularorplurar = 's yet';
      }
      $lastchar = $link[strlen($link)-1];
      if($lastchar=='/'){
        $mobi = '?mobi';
      }else{
        $mobi = '&amp;mobi';
      }
      if($_GET['comments']!="$post->ID"){
        $title = get_the_title();
        foreach((get_the_category()) as $cats) { 
            $cate .= '<a href="'.get_category_link($cats->cat_ID).'">'.$cats->cat_name.'</a> '; 
        } 
        $cats_links = '<p class="nextprev">This entry was posted on: '.the_date('l, F jS, Y h:s:m', '', '', FALSE).' and is filed under '.$cate.'</p>';

        $page_content = wpm_external_links(get_the_content());
  			$page_content = preg_replace_callback("/<img([^>]*)>/", "replace_image", $page_content);

        if($mobilise=='yes'){
          $page_content .= '<p><a href="http://mobilised.net/submit.php?url='.urlencode($link).'"><img style="border:none" src="http://mobilised.net/img.php?url='.urlencode($link).'" alt="Mobilised" width="90" height="20" /></a></p>';
        }
        $return .= $cats_links.'<div class="content">';
        $return .= str_replace('target="_blank"','',nl2br($page_content));
        $return .= "</div>";
        $status = $wpdb->get_var("SELECT comment_status FROM $wpdb->posts WHERE ID = '$post->ID'");
        $showcomments = get_option('wordpress_mobile_plugin_showcomments');
        if($showcomments=='yes'&&$status=='open'){
          $return .= "<div class='content'><strong>Comments</strong><br />There $areoris <a href='$link$mobi&amp;comments=$post->ID&amp;page=1'>".strtolower($numberofcomments)." comment$singularorplurar on $title</a>.</div>";
        }
        $return .= "$navi";
      }else{
        $perpage = 5;
        $page = mysql_real_escape_string($_GET['page']);
        if($page==''){
          $page = '1';
        }
        if($page=='1'){
          $start = '0';
        }else{
          $start = $page * $perpage - $perpage;
        }
        $pages = ceil($numberofcomments / $perpage);
        if($page > 1){
          $prev = ($page - 1);
          $navigation .= "<a href='$link$mobi&amp;comments=$post->ID&amp;page=$prev'>Last page</a> ";
        }
        if($page>1 && $page<$pages){
          $navigation .= '| ';
        }
        if($page < $pages){
          $next = ($page + 1);
          $navigation .= "<a href='$link$mobi&amp;comments=$post->ID&amp;page=$next'>Next page</a>";
        }
        $return .= '<div class="content">';
        $return .= "<h2>$numberofcomments comment$singularorplurar on $title</h2><p>";
        if($pages!='0' && $pages>1){
          $return .= "Page $page of $pages";
          $return .=" | $navigation<br />";
        }
        $return .="<a href='$link'>Return to $title</a></p>";
        $comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' and comment_approved ='1' order by comment_date asc limit $start, $perpage");
        foreach ($comments as $comment){
          ++$countem;
          if($counter!='1'){
            $return .= '<br />';
          }
          $comment_author = $comment->comment_author;
          $comment_content = ereg_replace('&','&amp;',$comment->comment_content);
          $comment_date = $comment->comment_date_gmt;
          $comment_author = $comment->comment_author;
          $comment_author_url = $comment->comment_author_url;
          if($comment_author_url!=''){
            $comment_author = "<a href='$comment_author_url'>$comment_author</a>";
          }
          $return .= "<strong>At $comment_date $comment_author said:</strong><br />'<cite>$comment_content</cite>'<br />";
        }
        $return .= "</div><p><strong>$navigation</strong></p>";
        // showing comments
        if($leavecomments=='yes'){
          $return .= '<p><strong>Add your comment:</strong><a id="submitcomment">&nbsp;</a></p>';
          $return .= "<form action='$home/wp-comments-post.php' method='post' id='commentform'>
          <p>
          <label for='author'>[2] Required - Name:</label>
          <br />
          <input type='text' name='author' id='author' value='' size='22' tabindex='1'/>
          <br />
          <label for='email'>[3] Required - Email, kept private:</label>
          <br />
          <input type='text' name='email' id='email' value='' size='22' tabindex='2'/>
          <br />
          <label for='url'>Website / dotMobi address:</label>
          <br />
          <input type='text' name='url' id='url' value='' size='22' tabindex='3' />
          <br />
          <label for='url'>Your comment:</label>
          <br />
          <textarea name='comment' id='comment' cols='20' rows='5' tabindex='4'></textarea>
          <br />
          <label for='url'>[5] Submit your comment!</label>
          <br />
          <input name='submit' type='submit' id='submit' tabindex='5' value='Submit Comment' />
          <input type='hidden' name='comment_post_ID' value='$post->ID' />
          </p>
          </form>
          <p>[1] <a href='$link'>Return to $title</a></p>";
        }
      }
      $processed = true;
    break;

    case is_page();
      $custom = get_post_custom_values('mobileonly', $post->ID);
      if($custom['0']=='true'){
        $return = '<h3 class="content">'.get_option('wordpress_mobile_plugin_mobileonly_mob_statement').'</h3>';
      }
      $title = get_the_title();
      $blogheader = get_bloginfo('name');
      $metatitle = $title.' | '.get_bloginfo('name');
      $blogtitle = $title;
      $title = get_the_title().' | '.$pagelabel;
        $page_content = wpm_external_links(get_the_content());
  			$page_content = preg_replace_callback("/<img([^>]*)>/", "replace_image", $page_content);
      $return .= '<div class="content">';
      $return .= str_replace('target="_blank"','',nl2br($page_content));
      $return .= '</div>';
      $processed = true;
    break;

    case (is_home());
      $metatitle = get_bloginfo('name') .' | '.get_bloginfo ('description');
      $blogtitle = get_bloginfo('name');
      $blogheader = get_bloginfo ('description');
      $mobile_intro = get_option('wordpress_mobile_plugin_welcome');
      if($mobile_intro!=''&&is_paged()==false){
        $mobile_intro = '<p class="content">'.$mobile_intro.'</p>';
      }else{
        unset($mobile_intro);
      }
    break;

    case (is_year());
      $metatitle = get_bloginfo('name') .' | '.get_the_time('Y');
      $blogtitle = get_bloginfo('name');
      $blogheader = get_the_time('Y');
      $req .= '&year='.get_the_time('Y').'&paged='.$_GET['page'];
    break;

    case (is_month());
      $metatitle = get_bloginfo('name') .' | '.get_the_time('F Y');
      $blogtitle = get_bloginfo('name');
      $blogheader = get_the_time('F Y');
      $req .="&year=".get_the_time('Y')."&monthnum=".get_the_time('m');
    break;

    case (is_category());
      $metatitle = get_bloginfo('name') .' | '.ucwords(single_cat_title('',false));
      $blogtitle = get_bloginfo('name');
      $blogheader = ucwords(single_cat_title('',false));
      $req .= 'category_name='.single_cat_title('',false);
    break;

  } // ends the switch

  query_posts("$req&paged=$paged&offset=0");

  if (have_posts()&&$processed==false){
    if($blogheader==false){
      $blogheader = 'HAVEPOSTS!';
      $metatitle = get_bloginfo('name').' | '.$blogheader;
      $blogtitle = get_bloginfo('name');
    }

    while (have_posts()){
      the_post();
      $post_list = '<strong><a href="'.get_permalink().'">'.get_the_title().'</a></strong><br />';
      $post_list .= 'Published: '.return_the_time('l, F jS, Y').'<br />';
      $post_list .= 'Filed under: ';
      foreach((get_the_category()) as $cats) { 
        $post_list .= '<a href="'.get_category_link($cats->cat_ID).'">'.$cats->cat_name.'</a> '; 
      }
      $return .= '<p class="content">'.$post_list.'</p>';

    }
    $navigation_prev = dr_previous_posts_link('&laquo; Newer Entries ', '', '');
    $navigation_next .= dr_next_posts_link('Older Entries &raquo;', '', '');
    if($navigation_prev!=''&&$navigation_next!=''){ $wpmsep = ' | '; }
    if($navigation_prev!=''||$navigation_next!=''){ $navigation = '<p class="nextprev">'.$navigation_prev.$wpmsep.$navigation_next.'</p>'; }
    $return = $mobile_intro.$navigation.$return.$navigation;
  }

  if($admobkey!=''&&$tool!='yes'){ // works out where we want the ads to be positioned
    $adstop = get_option('wordpress_mobile_plugin_adstop');
    $adsbot = get_option('wordpress_mobile_plugin_adsbot');
    $admoblink = trim(get_option('wordpress_mobile_plugin_admoblink'));
    $authorlink = trim(get_option('wordpress_mobile_plugin_authorlink'));
    if($authorlink=='no'){
      $adclass = 'foot';
    }else{
      $adclass = 'admob';
    }
    switch($adstop){
      case 'topgoo';
        $admob = google();
        break;
      case 'topadm';
        $admob = '<div class="admob">'.admob($admobkey).'</div>';
        break;
      case 'topnon';
      case '';
        break;
    }
    switch($adsbot){
      case 'botgoo';
        $admobbottom = google();
      break;
      case 'botadm';
        $admobbottom = '<div class="'.$adclass.'">'.admob($admobkey).'</div>';
      break;
      case 'botnon';
      case '';
      break;
    }
  }

  // a few variables to customise
  $home = get_settings(siteurl);
  $bloginfo = get_bloginfo('name');
  if(isset($_GET['mobi'])){
    $navmobi = '?mobi';
  }
  // if there is a value for pagelabel and there are actually some pages we build the pages_link value
  if($pagelabel!=''){
    $pages_link = "<br />[3] <a href='$home/?view=pages&amp;mobi' accesskey='3'>$pagelabel</a>";
  }

  // if there is a value for pagelabel and there are actually some pages we build the pages_link value
  if($blogrolllabel!=''){
    $write_page_link = "<br />[4] <a href='$home?mobi&amp;view=blogroll' accesskey='4'>$blogrolllabel</a>";
  }

  // we only show the create post page is the secret has a value
  $secret = get_option('wordpress_mobile_plugin_secret');
  if($secret!=''){
    $write_page_link .= "<br />[5] <a href='$home?mobi&amp;view=write' accesskey='5'>Create Post</a>";
  }

  // build up the navigation variable
  $navigation = "[1] <a href='$home/$navmobi' accesskey='1'>$bloginfo (home)</a><br />[2] <a href='$home/?view=archives&amp;mobi' accesskey='2'>$archivelabel</a>$pages_link$write_page_link";
  // are you showing author links or not?
  // again we don't show these to validators and emulators or the w3
  $authorlink = get_option('wordpress_mobile_plugin_authorlink');
  if($tool!='yes'&&$authorlink!='no'){
    $web2txt_values       = array();
    $web2txt_values['0']  = array('24 CTU Phone', '36880');
    $web2txt_values['1']  = array('CTU 24 Phone', '36880');
    $web2txt_values['2']  = array('Sopranos Theme', '37255');
    $web2txt_values['3']  = array('Mosquito Secret', '342393');
    $web2txt_values['4']  = array('Dr Who Theme', '36374');
    $web2txt_values['5']  = array('Akon Smack That', '342776');
    $web2txt_values['6']  = array('Spongebob', '340515');
    $web2txt_values['7']  = array('Exorcist', '36958');
    $web2txt_values['8']  = array('Ring of Fire', '38980');
    $web2txt_values['9']  = array('The Muppet Show', '340051');
    $web2txt_values['10'] = array('Mr Big Stuff', '35722');
    $web2txt_values['11'] = array('Akon Sweet Escape', '343667');
    $web2txt_values['12'] = array('Mike Grace Kelly', '343540');
    $web2txt_values['13'] = array('Monty Python', '35333');
    $web2txt_values['14'] = array('Snow Patrol ', '342098');
    $web2txt_count        = count($web2txt_values)-1;
    $web2txt_rand         = rand('0',$web2txt_count);
    $web2txt_anchor       = $web2txt_values[$web2txt_rand]['0'];
    $web2txt_id           = $web2txt_values[$web2txt_rand]['1'];
    $web2txt_anchor       = '<a href="http://www.web2txt.co.uk/wap/v2/order.php?product='.$web2txt_id.'">'.$web2txt_anchor.' Ringtone</a>';
    $backlink = "<div class='foot'>$web2txt_anchor</div>";
  }
  // how do we want to handle the stylesheet
  $style = get_option('wordpress_mobile_plugin_style');
  $remotestyle = get_option('wordpress_mobile_plugin_remotestyle');
  if($remotestyle==''){
    if($style=='external'){
      // if external we build up the link href value
      $mobile_style = "<link href='$home/?mobi&amp;style=.css' rel='stylesheet' type='text/css' />";
    }else if($style=='none'){
      // else we include it inline in a style tag
      $mobile_style = false;
    }else{
      // else we include it inline in a style tag
      $mobile_style = "<style type='text/css'>".mobile_style()."</style>";
    }
  }else{
    $mobile_style = "<link href='$remotestyle' rel='stylesheet' type='text/css' />";
  }
  // see if a page or post has been created or updated today so we can show the site is current
  $updated = updated_today();

  // set up header so the handset can cache them if supported
  header("Expires: " . date("D, d M Y H:i:s", time() + 60 * 60) . " GMT");
  // valid content type
//  header('Content-Type: application/xhtml+xml; charset=UTF-8');
  $search_form = search_form();
  $close = '>'; // just to keep syntax highlighting in editor.
  // just so i can see what version you're using if i check out your site

  global $mobile_plugin_version;
  // this is the page template 
  // indentation (pretty printing) will be removed before we echo to the browser 
  $return = <<<ENDOFDOC
<?xml version="1.0" encoding="UTF-8"?$close
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML Basic 1.0//EN" "http://www.w3.org/TR/xhtml-basic/xhtml-basic10.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>$metatitle</title>
    $mobile_style
    <meta name="generator" content="WordPress Mobile Plugin. Version: $mobile_plugin_version - wordpressmobile.mobi" />
  </head>
  <body>
    <h1 class="title">$blogtitle</h1>
    <h2 class="title">$blogheader$updated</h2>
    $admob
    $return
    <div class="nav">$navigation$search_form</div>
    $backlink
    $admobbottom
    <div class="nav"><a href="?nomo=true">Original Version</a></div>
  </body>
</html>
ENDOFDOC;

 // clean it up - remove junk characters
 // 
 echo clean_return($return);
 // echo ($return);
 exit;
}

 
 
function mobile_style(){ // // this simply returns the style sheet - will either be called inline or as an external file call by adding &style=.css to the URL
  $colour = get_option('wordpress_mobile_plugin_colour');
  switch($colour){
    case red;
      $mobile_style = "body{background:#FFFFFF; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:0.80em; line-height:2.00em; color:#000000;}
      a{text-decoration: none; color:#CD7054}
      a:hover{text-decoration: none; color:#000000}
      .title{background: #CD7054; color:#ffffff; margin:0em; font-size:1.1em; font-weight: bold; padding:0.13em 0em 0.13em 1.00em;}
      .admob{border-bottom: #CDBE70 solid 0.13em; background: #ffffff; margin:0EM; padding:0.13em 0em 0.13em 1.00em;}
      .list{margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .top{background: #669966; margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .nav{border-top: #CDBE70 solid 0.13em; background: #EEE8CD; margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .content{border-bottom: #CDBE70 solid 0.13em; background: #ffffff; margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .foot{border-top: #CDBE70 solid 0.13em; border-bottom: #CDBE70 solid 0.13em; background: #FFFFFF; color: #666633; margin: 0em; padding:0.13em 0em 0.13em 1.00em;}
      .nextprev{background: #FFF8DC; color: #666633; margin: 0em; padding:0.13em 0em 0.13em 1.00em;}
      input.write{-wap-input-format: \"*M\"}
      textarea.write{-wap-input-format: \"*M\"}";
    break;
    case blue;
      $mobile_style = "body{background:#FFFFFF; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:0.80em; line-height:2.00em; color:#000000;}
      a{text-decoration: none; color:#14568A}
      a:hover{text-decoration: none; color:#6DA6D1}
      .title{background: #14568A; color:#ffffff; margin:0em; font-size:1.1em; font-weight: bold; padding:0.13em 0em 0.13em 1.00em;}
      .admob{border-bottom: #6DA6D1 solid 0.13em; background: #ffffff; margin:0EM; padding:0.13em 0em 0.13em 1.00em;}
      .list{margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .top{background: #669966; margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .nav{border-top: #6DA6D1 solid 0.13em; background: #eeeeee; margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .content{border-bottom: #6DA6D1 solid 0.13em; background: #ffffff; margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .foot{border-top: #6DA6D1 solid 0.13em; border-bottom: #6DA6D1 solid 0.13em; background: #ffffff; color: #666633; margin: 0em; padding:0.13em 0em 0.13em 1.00em;}
      .nextprev{background: #eeeeee; color: #666633; margin: 0em; padding:0.13em 0em 0.13em 1.00em;}
      input.write{-wap-input-format: \"*M\"}
      textarea.write{-wap-input-format: \"*M\"}";
    break;
    case green;
      $mobile_style = "body{background:#FFFFFF; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:0.80em; line-height:2.00em; color:#000000;}
      a{text-decoration: none; color:#006600}
      a:hover{text-decoration: none; color:#33CC33}
      .title{background: #006600; color:#ffffff; margin:0em; font-size:1.1em; font-weight: bold; padding:0.13em 0em 0.13em 1.00em;}
      .admob{border-bottom: #6DA6D1 solid 0.13em; background: #ffffff; margin:0EM; padding:0.13em 0em 0.13em 1.00em;}
      .list{margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .top{background: #669966; margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .nav{border-top: #33FF00 solid 0.13em; background: #CCFFCC ; margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .content{border-bottom: #33FF00 solid 0.13em; background: #ffffff; margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .foot{border-top: #33FF00 solid 0.13em; border-bottom: #33FF00 solid 0.13em; background: #ffffff; color: #666633; margin: 0em; padding:0.13em 0em 0.13em 1.00em;}
      .nextprev{background: #eeeeee; color: #666633; margin: 0em; padding:0.13em 0em 0.13em 1.00em;}
      input.write{-wap-input-format: \"*M\"}
      textarea.write{-wap-input-format: \"*M\"}";
    break;
    case pink;
      $mobile_style = "body{background:#FFFFFF; font-family:Verdana, Arial, Helvetica, sans-serif; font-size:0.80em; line-height:2.00em; color:#000000;}
      a{text-decoration: none; color:#FF0099 }
      a:hover{text-decoration: none; color:#FF0099 }
      .title{background: #FF0099 ; color:#ffffff; margin:0em; font-size:1.1em; font-weight: bold; padding:0.13em 0em 0.13em 1.00em;}
      .admob{border-bottom: #6DA6D1 solid 0.13em; background: #ffffff; margin:0EM; padding:0.13em 0em 0.13em 1.00em;}
      .list{margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .top{background: #669966; margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .nav{border-top: #CC33CC solid 0.13em; background: #FFCCFF; margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .content{border-bottom: #CC33CC solid 0.13em; background: #ffffff; margin:0em; padding:0.13em 0em 0.13em 1.00em;}
      .foot{border-top: #CC33CC solid 0.13em; border-bottom: #CC33CC solid 0.13em; background: #ffffff; color: #666633; margin: 0em; padding:0.13em 0em 0.13em 1.00em;}
      .nextprev{background: #eeeeee; color: #666633; margin: 0em; padding:0.13em 0em 0.13em 1.00em;}
      input.write{-wap-input-format: \"*M\"}
      textarea.write{-wap-input-format: \"*M\"}";
    break;
  }
  return clean_return($mobile_style);
}

  
  

function wordpress_mobile_plugin_admin(){ // build the admin panel
  global $mobile_plugin_version;
  echo '<div class="wrap"><h2>WordPress Mobile Plugin v'.$mobile_plugin_version.'</h2>'.$mobile_upgrade;
    if (isset($_POST['admob'])) {
      // it does so we can get all the other post values
      $admob = $_POST['admob'];
      $welcome = $_POST['welcome'];
      if($welcome==''){
        $welcome = 'Welcome to the mobile version of my site!';
      }
  //    $adcomment = $_POST['adcomment'];
      $adstop = $_POST['adstop'];
      $adsbot = $_POST['adsbot'];
      $admoblink = $_POST['admoblink'];
      $archivelabel = $_POST['archivelabel'];
      if($archivelabel==''){
        $archivelabel = 'The Archives';
      }
      $pagelabel = $_POST['pagelabel'];
      $blogrolllabel = $_POST['blogrolllabel'];
      $google = $_POST['google'];
      $googleformat = $_POST['googleformat'];
      $secret = $_POST['secret'];
      $tracking = $_POST['tracking'];
      $style = $_POST['style'];
      $remotestyle = $_POST['remotestyle'];
      $share = $_POST['share'];
      $authorlink = $_POST['authorlink'];
      $updatedlabel = $_POST['updatedlabel'];
      $resizewidth = $_POST['resizewidth'];
      $moblogcat = $_POST['moblogcat'];
      $leavecomments = $_POST['leavecomments'];
      $showcomments = $_POST['showcomments'];
      $iphone = $_POST['iphone'];
      $opera = $_POST['opera'];
      $colour = $_POST['colour'];
      $mobilise = $_POST['mobilise'];
      $mobileonly = $_POST['mobileonly'];
      $mobileonly_pc_statement = $_POST['mobileonly_pc_statement'];
      $mobileonly_mob_statement = $_POST['mobileonly_mob_statement'];
  
      // now we update the options - you could add some validation here if you wished
      update_option('wordpress_mobile_plugin_admob', $admob);
      update_option('wordpress_mobile_plugin_google', $google);
      update_option('wordpress_mobile_plugin_googleformat', $googleformat);
      update_option('wordpress_mobile_plugin_admobshare', $share);
      update_option('wordpress_mobile_plugin_admoblink', $admoblink);
      // update_option('wordpress_mobile_plugin_adcomment', $adcomment);
      update_option('wordpress_mobile_plugin_adstop', $adstop);
      update_option('wordpress_mobile_plugin_adsbot', $adsbot);
      update_option('wordpress_mobile_plugin_authorlink', $authorlink);
      update_option('wordpress_mobile_plugin_moblogcat', $moblogcat);
      update_option('wordpress_mobile_plugin_resizewidth', $resizewidth);
      update_option('wordpress_mobile_plugin_welcome', $welcome);
      update_option('wordpress_mobile_plugin_archivelabel', $archivelabel);
      update_option('wordpress_mobile_plugin_pagelabel', $pagelabel);
      update_option('wordpress_mobile_plugin_blogrolllabel', $blogrolllabel);
      update_option('wordpress_mobile_plugin_secret', $secret);
      update_option('wordpress_mobile_plugin_style', $style);
      update_option('wordpress_mobile_plugin_remotestyle', $remotestyle);
      update_option('wordpress_mobile_plugin_updatedlabel', $updatedlabel);
      update_option('wordpress_mobile_plugin_leavecomments', $leavecomments);
      update_option('wordpress_mobile_plugin_showcomments', $showcomments);
      update_option('wordpress_mobile_plugin_iphone', $iphone);
      update_option('wordpress_mobile_plugin_opera', $opera);
      update_option('wordpress_mobile_plugin_colour', $colour);
      update_option('wordpress_mobile_plugin_mobilise', $mobilise);
      update_option('wordpress_mobile_plugin_mobileonly', $mobileonly);
      update_option('wordpress_mobile_plugin_mobileonly_pc_statement', $mobileonly_pc_statement);
      update_option('wordpress_mobile_plugin_mobileonly_mob_statement', $mobileonly_mob_statement);

      // create the pretty box with updated message
      echo "<br /><div id='message' class='updated fade'><p><strong>WordPress Mobile options updated!</strong></p></div><br />";
    }

  $pp_form = '<table width="400">
  <tr>
  <td>One off fee &pound;25:00</td>
  <td>
      <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
    <input type="hidden" name="business" value="andy@andymoore.info">
    <input type="hidden" name="item_name" value="WordPress Mobile Plugin 100% Ad exposure upgrade" />
    <input type="hidden" name="item_number" value="'.get_settings(siteurl).'" />
    <input type="hidden" name="amount" value="25.00" />
    <input type="hidden" name="no_shipping" value="1" />
    <input type="hidden" name="notify_url" value="http://andymoore.info/wordpress_ipn.php" />
    <input type="hidden" name="return" value="'.get_settings(siteurl).'/wp-admin/options-general.php?page=wordpress-mobile.php&action=check" />
    <input type="hidden" name="cancel_return" value="'.get_settings(siteurl).'/wp-admin/options-general.php?page=wordpress-mobile.php&action=cancel" />
    <input type="hidden" name="no_note" value="1" />
    <input type="hidden" name="cmd" value="_xclick" />
    <input type="hidden" name="currency_code" value="GBP" />
    <input type="hidden" name="lc" value="GB" />
    <input type="hidden" name="bn" value="PP-BuyNowBF" />
    <input type="image" src="https://www.paypal.com/en_GB/i/btn/btn_paynow_SM.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online." />
    <img alt="" border="0" src="https://www.paypal.com/en_GB/i/scr/pixel.gif" width="1" height="1" />
  </form>
  </td>
  </tr>
  </table>';

   if($_GET['action']=='cancel'){
        echo "<br /><div id='message' class='updated fade'><p><strong>Need convincing? Let me brainwash you:</strong></p><ol>
        <li>You&#8217;ll get 100% of ad revenue on both Google &amp; AdMob</li>
        <li>Your plugin will not show links to other mobile sites</li>
        <li>You&#8217;ll get official support through email &amp; phone</li>
        <li>You&#8217;ll get future releases before other users</li>
        <li>1 in 10 upgrades get a free .mobi domain</li>
        <li>Your feedback is a higher priority to me</li>
        <li>It&#8217;s better than other mobile plugins</li>
        <li>You&#8217;ll get backlinks on other plugins</li>
        <li>It&#8217;ll pay for itself many times over</li>
        <li>It&#8217;ll pay for my holidays &amp; beer</li>
        </ol>
        $pp_form
        </div><br />";
    } 
     
    $admobshare = get_option('wordpress_mobile_plugin_admobshare');


    if($_GET['action']=='check'&&isset($_POST['item_number'])){
      if($admobshare=='amwpok'){
        echo "<br /><div id='message' class='updated fade'><p><strong>Your mobile blog has been upgraded! You rock!</strong><br /><br />Thank you for your payment! PayPal has already notified my server of your payment and you've already been upgraded!</p></div><br />";
      }else{
        echo "<br /><div id='message' class='updated fade'><p><strong>Upgrade approved - you rock!</strong><br /><br />Thank you for your upgrading! As soon as PayPal has notified my server of your payment the uprade process will happen automagically!</p></div><br />";
      }
    }
   
    // now we get the values from wp so we can propagate the form and pre program drop down forms with selected values
    $admobkey = get_option('wordpress_mobile_plugin_admob');
    $googleid = get_option('wordpress_mobile_plugin_google');
    $googleformat = get_option('wordpress_mobile_plugin_googleformat');
    $admoblink = get_option('wordpress_mobile_plugin_admoblink');
    $welcome = get_option('wordpress_mobile_plugin_welcome');
    $adstop = get_option('wordpress_mobile_plugin_adstop');
    $adsbot = get_option('wordpress_mobile_plugin_adsbot');
    $archivelabel = get_option('wordpress_mobile_plugin_archivelabel');
    $pagelabel = get_option('wordpress_mobile_plugin_pagelabel');
    $blogrolllabel = get_option('wordpress_mobile_plugin_blogrolllabel');
    $secret = get_option('wordpress_mobile_plugin_secret');
    $style = get_option('wordpress_mobile_plugin_style');
    $remotestyle = get_option('wordpress_mobile_plugin_remotestyle');
    $authorlink = get_option('wordpress_mobile_plugin_authorlink');
    $updatedlabel = get_option('wordpress_mobile_plugin_updatedlabel');
    $resizewidth = get_option('wordpress_mobile_plugin_resizewidth');
    $moblogcat = get_option('wordpress_mobile_plugin_moblogcat');
    $leavecomments = get_option('wordpress_mobile_plugin_leavecomments');
    $showcomments = get_option('wordpress_mobile_plugin_showcomments');
    $iphone = get_option('wordpress_mobile_plugin_iphone');
    $opera = get_option('wordpress_mobile_plugin_opera');
    $colour = get_option('wordpress_mobile_plugin_colour');
    $mobilise = get_option('wordpress_mobile_plugin_mobilise');
    $mobileonly = get_option('wordpress_mobile_plugin_mobileonly');
    $homeurl = get_settings(siteurl);

    $mobileonly_pc_statement = get_option('wordpress_mobile_plugin_mobileonly_pc_statement');
    $mobileonly_mob_statement = get_option('wordpress_mobile_plugin_mobileonly_mob_statement');


  
  // $adformat_single
  
    if($googleformat=='single'){
      $google_single = 'selected="selected"';
    }else{
      $google_double = 'selected="selected"';
    }
    if($iphone=='mobile'){
      $iphone_mobile = 'selected="selected"';
    }else{
      $iphone_pc = 'selected="selected"';
    }
    if($opera=='mobile'){
      $opera_mobile = 'selected="selected"';
    }else{
      $opera_pc = 'selected="selected"';
    }
    // more drop down selection checking
    if($leavecomments=='yes'){
      $comments_enabled = 'selected="selected"';
    }else{
      $comments_disabled = 'selected="selected"';
    }
    // more drop down selection checking
    if($showcomments=='yes'){
      $showcomments_enabled = 'selected="selected"';
    }else{
      $showcomments_disabled = 'selected="selected"';
    }
    // more drop down selection checking
    if($style=='internal'){
      $style_internal = 'selected="selected"';
    }else if($style=='none'){
      $style_none = 'selected="selected"';
    }else{
      $style_external = 'selected="selected"';
    }
    
    switch($colour){
      case red;
      $case_red = ' checked';
      break;
      case blue;
      $case_blue = ' checked';
      break;
      case green;
      $case_green = ' checked';
      break;
      case pink;
      $case_pink = ' checked';
      break;
    }
  
  
  
  
    // more drop down selection checking
    if($authorlink=='yes'){
      $authorlink_yes = 'selected="selected"';
    }else{
      $authorlink_no = 'selected="selected"';
    }
  
    // more drop down selection checking
    if($mobilise=='yes'){
      $mobilise_yes = 'selected="selected"';
    }else{
      $mobilise_no = 'selected="selected"';
    }
    if($mobileonly=='yes'){
      $mobileonly_yes = 'selected="selected"';
    }else{
      $mobileonly_no = 'selected="selected"';
    }
  
    // where does the user want the ads to appear
    // again more drop down selection checking
    switch($adstop){
      case 'topgoo';
        $selected_topgoo = 'checked';
        break;
      case 'topadm';
        $selected_topadm = 'checked';
        break;
      case 'topnon';
      case '';
        $selected_topnon = 'checked';
        break;
    }
  
    switch($adsbot){
      case 'botgoo';
        $selected_botgoo = 'checked';
        break;
      case 'botadm';
        $selected_botadm = 'checked';
        break;
      case 'botnon';
      case '';
        $selected_botnon = 'checked';
        break;
    }
  
    // how much many ad exposes do you want me to get?
      $share = '10'; // my smallest cut on ad revenue is 10% -  
    while($share<101){
      if($share==$admobshare){
        $share_list .= "<option value='$share' selected='selected'>$share%\n";
      }else{
        $share_list .= "<option value='$share'>$share%\n";
      }
      ++$share;
    }


    global $wpdb;
    global $wp_version;
    $cat_drop = '<select name="moblogcat" id="moblogcat">'; 
    if($wp_version>'2.2.0'){
      $categories = @$wpdb->get_results("SELECT * FROM $wpdb->terms ORDER BY name");
      foreach ($categories as $category){
        if ($category->term_id == $moblogcat){
          $selected = " selected='selected'";
        }else{
          $selected = '';
        }
        $cat_drop .= "\n\t<option value='$category->term_id' $selected>".ucwords(strtolower($category->name))."&nbsp;&nbsp;</option>";
      }
    }else{
      $categories = @$wpdb->get_results("SELECT * FROM $wpdb->categories ORDER BY cat_name");
      foreach ($categories as $category){
      if ($category->cat_ID == $moblogcat){
          $selected = " selected='selected'";
        }else{
          $selected = '';
        }
        $cat_drop .= "\n\t<option value='$category->cat_ID' $selected>".ucwords(strtolower($category->cat_name))."&nbsp;&nbsp;</option>";
      }
    }
    $cat_drop .= '</select>';
    
  
  
  
    // show the admin panel form and links etc
    echo check_version('admin')."
    <span style='float:right; padding-left:1.5em;padding-right:1.0em;padding-bottom:1.0em;padding-top:-1.0em;'>
      <script type='text/javascript'>
        digg_url = 'http://www.andymoore.info/wordpress-mobile-plugin/';
        digg_title = 'WordPress Mobile Plugin';
      </script>
      <script src='http://digg.com/tools/diggthis.js' type='text/javascript'></script>
  
  </span>
  
    <fieldset class='options'>
  	<strong>General Plugin Configuration</strong>
    <form name='wordpress_mobile_update' id='wordpress_mobile_update' method='post' action='?page=wordpress-mobile.php&amp;action=wordpress_mobile_update' ENCTYPE='multipart/form-data'>
    <table cellpadding='3' cellspacing='3'>
  	<tr>
  	<td width='250'><label for='welcome'>Index page welcome comment:</label></td>
  	<td width='250'><input type='text' name='welcome' id='welcome' value='$welcome' size='45' /></td>
  	</tr>
  	<tr>
  	<td width='250'><label for='archivelabel'>Archive link anchor text:</label></td>
  	<td width='250'><input type='text' name='archivelabel' id='archivelabel' value='$archivelabel' size='15' /></td>
  	</tr>
  	<tr>
  	<td width='250'><label for='pagelabel'>Pages link anchor text:</label></td>
  	<td width='250'><input type='text' name='pagelabel' id='pagelabel' value='$pagelabel' size='15' /></td>
  	</tr>
  	<tr>
  	<td width='250'><label for='blogrolllabel'>Blogroll anchor text:</label></td>
  	<td width='250'><input type='text' name='blogrolllabel' id='blogrolllabel' value='$blogrolllabel' size='15' /></td>
  	</tr>
    <tr>
  	<td width='250'><label for='showcomments'>Show comments:</label></td>
  	<td width='250'><select name='showcomments' id='showcomments'><option value='yes' $showcomments_enabled>Enabled&nbsp;&nbsp;<option value='no' $showcomments_disabled>Disabled</select></td>
  	</tr>
    <tr>
  	<td width='250'><label for='leavecomments'>Leave comments:</label></td>
  	<td width='250'><select name='leavecomments' id='leavecomments'><option value='yes' $comments_enabled>Enabled&nbsp;&nbsp;<option value='no' $comments_disabled>Disabled</select></td>
  	</tr>
    <tr>
  	<td width='250'><label for='updatedlabel'>Text to show if blog updated today:</label></td>
  	<td width='250'><input type='text' name='updatedlabel'  id='updatedlabel' value='$updatedlabel' size='15' /></td>
  	</tr>
    <tr>
  	<td width='250'>Select the style of your mobile blog:</td>
  	<td width='250'></td>
  	</tr>
    <tr>
  	<td width='250' align='center'><label for='red'><img src='http://wordpressmobile.mobi/red.jpg' width='235' height='257' alt='Red' /></label><br /><input type='radio' $case_red name='colour' value='red' id='red' /> <label for='red'>Red</label></td>
  	<td width='250' align='center'><label for='blue'><img src='http://wordpressmobile.mobi/blue.jpg' width='235' height='257' alt='Blue' /></label><br /><input type='radio' $case_blue name='colour' value='blue' id='blue' /> <label for='blue'>Blue</label></td>
  	</tr>
    <tr>
  	<td width='250' align='center'><label for='green'><img src='http://wordpressmobile.mobi/green.jpg' width='235' height='257' alt='green' /></label><br /><input type='radio' $case_green name='colour' value='green' id='green' /> <label for='green'>Green</label></td>
  	<td width='250' align='center'><label for='pink'><img src='http://wordpressmobile.mobi/pink.jpg' width='235' height='257' alt='pink' /></label><br /><input type='radio' $case_pink name='colour' value='pink' id='pink' /> <label for='pink'>Pink</label></td>
  	</tr>
    <tr>
  	<td width='250'><label for='style'>Default CSS: Inline, external or disabled</label></td>
  	<td width='250'><select name='style' id='style'><option value='external' $style_external>External&nbsp;&nbsp;<option value='internal' $style_internal>Inline<option value='none' $style_none>Disabled</select></td>
  	</tr>
    <tr>
  	<td width='250'><label for='remotestyle'>URL of remote style sheet</label></td>
  	<td width='250'><input type='text' name='remotestyle' id='remotestyle' value='$remotestyle' size='35' /></td>
  	</tr>
  	</tr>
  	</table>
  	<br />
    <strong>Posting to WordPress from a phone</strong>
    <br />
    <br />
    <table cellpadding='3' cellspacing='3'>
  	<tr>
  	<td width='250'><label for='secret'>Secret code to allow MoBlogging:</label></td>
  	<td width='250'><input type='text' name='secret' id='secret' value='$secret' size='15' /></td>
  	</tr>
  	<tr>
  	<td width='250'><label for='moblogcat'>Default category for mobile posting:</label></td>
  	<td width='250'>$cat_drop</td>
  	</tr>
    <tr>
  	<td width='250'><label for='resizewidth'>Resize jpg uploads wider than:</label></td>
  	<td width='250'><input type='text' name='resizewidth' id='resizewidth' value='$resizewidth' size='5' /> (pixels)</td>
  	</tr>
  	</table>
  	<br />
    <strong>Mobile Internet Advertising options</strong>
    <br />
    <br />
    <strong>AdMob Mobile Advertising</strong>
    <table cellpadding='3' cellspacing='3'>
  	<tr>
  	<td width='250'><a href='http://www.admob.com/' target='admob'>AdMob</a> <label for='admob'>Site ID:</label></td>
  	<td width='250'><input type='text' name='admob' id='admob' value='$admobkey' size='15' /></td>
  	</tr>
    <tr>
  	<td width='250'><label for='admoblink'>Ad links page anchor text:</label></td>
  	<td width='250'><input type='text' name='admoblink' id='admoblink' value='$admoblink' size='15' /></td>
  	</tr>
  	</table>
    <br />
    <strong>Google AdSense for Mobile</strong>
    <br />
    <br />
    <table cellpadding='3' cellspacing='3'>
  	<tr>
  	<td width='250'><a href='https://www.google.com/adsense/' target='google'>Google AdSense for Mobile</a> <label for='admob'>ID:</label></td>
  	<td width='250'><input type='text' name='google' id='google' value='$googleid' size='15' /></td>
  	</tr>
    <tr>
  	<td width='250'><label for='adformat'>Ad format:</label></td>
  	<td width='250'><select onchange='javascript:google_format();' name='googleformat' id='adformat'><option value='single' $google_single>Single<option value='double' $google_double>Double</select></td>
  	</tr>
  	</table>
    <br />
    <strong>Top of page advertising options</strong>
    
    <script type='text/javascript'>
  
      function google_format(){
        // if single = top and bottom available - if double only bottom available
        if(document.wordpress_mobile_update.adformat.value=='single'){
          // 
          document.wordpress_mobile_update.topgoo.disabled = false;
          document.getElementById('nogoo').innerHTML = 'Google';
          // alert('SINGLE MODE: Update form elements below to UNHIDE the top option for Google IF HIDDEN');
        } else if(document.wordpress_mobile_update.adformat.value=='double'){
          document.wordpress_mobile_update.topgoo.checked = false;
          document.wordpress_mobile_update.topgoo.disabled = true;
          document.getElementById('nogoo').innerHTML = '<strike>Google<\/strike>';
          // alert('DOUBLE MODE: Update form elements below to UNTICK and HIDE the top option for Google');
        }
      }
  
      function google_ads(place){
        if(place=='top'){
          document.wordpress_mobile_update.botgoo.checked = false;
        }
        if(place=='bottom'){
          document.wordpress_mobile_update.topgoo.checked = false;
        }
      }
      
      google_format();
  
    </script>
  
    <br />
    <br />
    <table cellpadding='3' cellspacing='3'>
    <tr>
  	<td width='150' align='center'><input type='radio' $selected_topgoo name='adstop' onchange='javascript:google_ads(\"top\");' value='topgoo' id='topgoo' /> <label id='nogoo' for='topgoo'>Google</label></td>
  	<td width='150' align='center'><input type='radio' $selected_topadm name='adstop' value='topadm' id='topadm' /> <label for='topadm'>AdMob</label></td>
  	<td width='150' align='center'><input type='radio' $selected_topnon name='adstop' value='topnon' id='topnon' /> <label for='topnon'>None</label></td>
  	</tr>
  	</table>
  
    <script type='text/javascript'>
      google_format();
    </script>
  
    <br />
    <strong>Bottom of page advertising options</strong>
    <br />
    <br />
    <table cellpadding='3' cellspacing='3'>
    <tr>
  	<td width='150' align='center'><input type='radio' $selected_botgoo name='adsbot' onchange='javascript:google_ads(\"bottom\");' value='botgoo' id='botgoo' /> <label id='nogoobot' for='botgoo'>Google</label></td>
  	<td width='150' align='center'><input type='radio' $selected_botadm name='adsbot' value='botadm' id='botadm' /> <label for='botadm'>AdMob</label></td>
  	<td width='150' align='center'><input type='radio' $selected_botnon name='adsbot' value='botnon' id='botnon' /> <label for='botnon'>None</label></td>
  	</tr>
  	</table>
  
    <br />
    <strong>Mobilise Mobile Social Bookmarking</strong>
    <br />
    <br />
    <table cellpadding='3' cellspacing='3'>
    <tr>
  	<td width='250'><label for='mobilise'>Show <a href='http://mobilised.net/about/en' target='_mobilise'>Mobilise</a> links:</label></td>
  	<td width='250'><select name='mobilise' id='mobilise'><option value='yes' $mobilise_yes>Yes&nbsp;&nbsp;&nbsp;<option value='no' $mobilise_no>No</select></td>
  	<td width='500'></td>
  	</tr>
  	</table>
  
    <br />
    <strong>Mobile Only Posts and Pages</strong>
    <br />
    <br />
    To make a post or page only available on the mobile web version of your blog you need to add a custom field with a key of 'mobileonly' and a value of 'true'  - The PC statement will be shown in place of the content when a PC views the restricted page, mobiles will see the mobile statement to tell them it's mobile exclusive content
    <br />
    <br />

    <table cellpadding='2' cellspacing='2'>
    <tr>
  	<td width='250'><label for='mobileonly_pc_statement'>Mobile only PC statement:</label></td>
  	<td width='250'><input type='text' name='mobileonly_pc_statement'  id='mobileonly_pc_statement' value='$mobileonly_pc_statement' size='15' /></td>
  	</tr>
    <tr>
  	<td width='250'><label for='mobileonly_mob_statement'>Mobile only mobile statement:</label></td>
  	<td width='250'><input type='text' name='mobileonly_mob_statement'  id='mobileonly_mob_statement' value='$mobileonly_mob_statement' size='15' /></td>
  	</tr>
  	</table>

    <br />
    <strong>Mobile Only WordPress / Mobile Web CMS</strong>
    <br />
    <br />
    <strong>Warning:</strong> Selecting yes here will turn your WordPress blog into a MOBILE ONLY site. All browsers will see the mobile version. Do not set to yes unless you want to run this blog purely for the mobile web.
    <br />
    <br />

    <table cellpadding='3' cellspacing='3'>
    <tr>
  	<td width='250'><label for='mobileonly'>Mobile only:</label></td>
  	<td width='250'><select name='mobileonly' id='mobileonly'><option value='yes' $mobileonly_yes>Yes&nbsp;&nbsp;&nbsp;<option value='no' $mobileonly_no>No</select></td>
  	<td width='500'></td>
  	</tr>
  	</table>
  
    <br />
    <strong>Advanced User Agent Handling</strong>
    <br />
    <br />
    <table cellpadding='3' cellspacing='3'>
    <tr>
  	<td width='250'><label for='iphone'>Treat iPhone as mobile or PC:</label></td>
  	<td width='250'><select name='iphone' id='iphone'><option value='mobile' $iphone_mobile>Mobile&nbsp;&nbsp;&nbsp;<option value='pc' $iphone_pc>PC</select></td>
  	<td width='500'></td>
  	</tr>
    <tr>
  	<td width='250'><label for='opera'>Treat Opera Mini as mobile or PC:</label></td>
  	<td width='250'><select name='opera' id='opera'><option value='mobile' $opera_mobile>Mobile&nbsp;&nbsp;&nbsp;<option value='pc' $opera_pc>PC</select></td>
  	<td width='500'></td>
  	</tr>
  	</table>
    <br />";
  
  $submit_form = "	<input type='submit' name='update_wordpress_mobile' value='Update' />
    </form>";
  
  
  if($admobshare=='amwpok'){
  
  echo "  <strong>100% Ad Exposure Upgraded!</strong>
  <p>Thank you for upgrading to the 100% ad exposure version!!</p>
    <input type='hidden' name='share' id='share' value='amwpok' />
    <input type='hidden' name='authorlink' id='authorlink' value='no' />
  
  
  $submit_form
  
  ";
  
  
  }else{
  
  
  echo "  <strong>Sharing success and link love</strong>
    <br />
    <br />
    <table cellpadding='3' cellspacing='3'>
    <tr>
  	<td width='250'><label for='share'>Plugin Author Ad Share:</label></td>
  	<td width='250'>
      <select name='share' id='share'>
        $share_list
      </select>
    </td>
  	<td width='500'></td>
  	</tr>
    <tr>
  	<td width='250'><label for='authorlink'>Show links for Plugin Author:</label></td>
  	<td width='250'><select name='authorlink' id='authorlink'><option value='yes' selected='selected'>Yes (Upgrade to remove)</select></td>
  	<td width='500'></td>
  	</tr>
  	</table>
  
  $submit_form
  
    <br />
    <strong>To get 100% of ad impressions and remove my sponsor links please upgrade</strong>
    <br />
    <br />
     $pp_form";
  	
  }
  
  echo "  </fieldset>
  	</div>
  
  
  	<div class=\"wrap\">
  
    <h3>Cool Mobile Related links</h3>
    <p>
    <a href='http://pc.mtld.mobi'target='mtld'><img src='http://wordpressmobile.mobi/dotmobi.jpg' width='86' height='38' border='0' alt='Dot Mobi'/></a>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href='http://www.gogle.com/adsense/'target='goosense'><img src='http://wordpressmobile.mobi/google-adsense-logo.jpg' width='116' height='43' border='0' alt='Google AdSense for Mobile' /></a>
    &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
    <a href='http://www.admob.com/'target='admob'><img src='http://www.admob.com/img/admob_logo_banner.gif' width='209' height='34' border='0' alt='Admob' /></a>
    </p>
    <ul>
    <li><a href='https://www.nostinghosting.com/'target='validator'>nostinghosting.com</a> - register .mobi domains
    <li><a href='http://validator.w3.org/mobile?uri=$homeurl'target='validator'>validator.w3.org/mobile</a> - check your markup validates
    <li><a href='http://ready.mobi/results2.jsp?uri=$homeurl' target='ready'>ready.mobi</a> - check how mobile ready you are.
    <li><a href='http://emulator.mtld.mobi/emulator.php?emulator=&amp;webaddress=$homelink&amp;emulator=nokiaN70' target='emulator'>emulator.mtld.mobi</a> - remember to test on several real devices.
    <li><a href='http://jigsaw.w3.org/css-validator/validator?uri=$homeurl?style=.css'target='stylevali'>jigsaw.w3.org/css-validator</a> - check your style validates
    <li><a href='http://pc.dev.mobi/files/dotMobi%20Mobile%20Web%20Developers%20Guide.pdf' target='devguide'>dotMobi Mobile Web Developers Guide</a> - recommended related reading
    <li><a href='http://www.w3.org/TR/mobileOK-basic10-tests/' target='mobileOK'>W3C mobileOK Basic Tests 1.0</a> - detailed W3 working draft
    </ul>
    </div>
  
  	<div class=\"wrap\">
    <h3>QR Code</h3>
    <p>
    Use this image on your blog so those with code readers on their mobiles can get easy access to your mobile blog. <a href='http://en.wikipedia.org/wiki/QR_Code' target='_qrwiki'>What are QR Codes?</a>
    <br />
    <img src='http://wordpressmobile.mobi/qr_img.php?d=".urlencode(get_settings(siteurl))."' width='132' height='132' alt='qr code'' />
    <br />
    You can add a QR code for any page on your blog by adding this small section of code to your template: <strong>if(function_exists(show_qr_code)){show_qr_code();}</strong>
    </p>
    </div>
  
  
   	<div class=\"wrap\">
  
    <h3>Register a dot mobi domain!</h3>
    <p>I also run a web hosting business offering exceptional uptime and an environment that's perfect for your blog!</p>
      <form id='checker' name='checker' method='post' action='https://www.nostinghosting.com/domainchecker.php'>
      <script type='text/javascript'>
      function nowww(place){
        document.checker.domain.value = document.checker.domain.value.replace('www.','');
      }
      </script>
      <input name='domain' id='domain' class='form_2' type='text' tabindex='1' onkeyup='javascript:nowww();' size='20' /> 
      <select name='ext'>
        <option value='.com'>.com</option>
        <option value='.net'>.net</option>
        <option value='.org'>.org</option>
        <option value='.mobi' selected='selected'>.mobi</option>
        <option value='.info'>.info</option>
        <option value='.biz'>.biz</option>
        <option value='.co.uk'>.co.uk</option>
        <option value='.org.uk'>.org.uk</option>
        <option value='.ltd.uk'>.ltd.uk</option>
        <option value='.plc.uk'>.plc.uk</option>
        <option value='.me.uk'>.me.uk</option>
        <option value='.tv'>.tv</option>
        <option value='.eu'>.eu</option>
        <option value='.us'>.us</option>
      </select> 
      <input type='submit' id='Submit' value='Lookup' />
    </form>
    <p><a href='https://www.nostinghosting.com/' target='nsting'>NoSting Hosting!</a> Enter coupon '<strong>WORDPRESSMOBILE</strong>' for a life-long 20% discount </p>
    </div>
  	<div class=\"wrap\">
      <h3>Thanks for using my plugin! Please tell the world!</h3>
      <p>If you like this plugin <a href='http://digg.com/software/WordPress_Mobile_Plugin' target='digg'>PLEASE DIGG IT!</a>, blog about it and help spread the joy of mobility!</p>
      <p>If you find a bug or wish to suggest features for future releases please submit them through the <a href='https://www.nostinghosting.com/devtracker/index.php?project_id=6' target='_tracker'>Bug and Development Tracker</a></p>
      <p><a href='https://www.nostinghosting.com/devtracker/index.php?cmd=changelog&&amp;project_id=6&amp;version_id=2' target='_changelog'>Version 1.3 change log</a>
      <br />
      <a href='https://www.nostinghosting.com/devtracker/index.php?cmd=roadmap&amp;project_id=6&amp;version_id=5' target='_roadmapnext'>Version 1.4 roadmap</a></p>
    </div>";
  // ends the admin panel function
  }

















function google(){
   if(get_option('wordpress_mobile_plugin_admobshare')!='amwpok'){
     $admobshare = get_option('wordpress_mobile_plugin_admobshare');
     $rand = rand(0,100);
     $campaign = ($rand <= $admobshare) ? 'pub-3132018019261025' : get_option('wordpress_mobile_plugin_google');
     $rand = 'Sharing '.$admobshare.'% to plugin author';
   }else{
    $rand = '100% version';
    $campaign = get_option('wordpress_mobile_plugin_google');
   }
   if(isset($_GET['adshare'])){
    $return .= "Ad info: $rand - This impression to $campaign ";
   }
  $GLOBALS['google']['client']=$campaign;
  $GLOBALS['google']['format']='mobile_'.get_option('wordpress_mobile_plugin_googleformat');
  $GLOBALS['google']['ad_type']='text';
  $GLOBALS['google']['https']=$_SERVER['HTTPS'];
  $GLOBALS['google']['host']=$_SERVER['HTTP_HOST'];
  $GLOBALS['google']['ip']=$_SERVER['REMOTE_ADDR'];
  $GLOBALS['google']['markup']='xhtml';
  $GLOBALS['google']['oe']='utf8';
  $GLOBALS['google']['output']='xhtml';
  $GLOBALS['google']['ref']=$_SERVER['HTTP_REFERER'];
  $GLOBALS['google']['url']=$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
  $GLOBALS['google']['useragent']=$_SERVER['HTTP_USER_AGENT'];
  $colour = get_option('wordpress_mobile_plugin_colour');
  switch($colour){
    case pink;
      $color_link='FF0099';
      $color_url='FF0099';
    break;
    case blue;
      $color_link='14568A';
      $color_url='14568A';
    break;
    case green;
      $color_link='006600';
      $color_url='006600';
    break;
    case red;
      $color_link='CD7054';
      $color_url='CD7054';
    break;
  }
  $GLOBALS['google']['color_link']=$color_link;
  $GLOBALS['google']['color_url']=$color_url;
  // $GLOBALS['google']['color_link']='006600';
  // $GLOBALS['google']['color_url']='006600';
  $GLOBALS['google']['color_border']='ffffff';
  $GLOBALS['google']['color_bg']='ffffff';
  $GLOBALS['google']['color_text']='000000';
  $google_dt = time();
  google_set_screen_res();
  $google_ad_handle = @fopen(google_get_ad_url(), 'r');
  if ($google_ad_handle) {
    while (!feof($google_ad_handle)) {
      $return .= fread($google_ad_handle, 8192);
    }
    fclose($google_ad_handle);
  }else if (get_option('wordpress_mobile_plugin_admob')!=''){
  // basically if we're executing here we failed to get google ads so we check if there's an admob id and if there is we show admob ads as an alternative - if there's inventory we might as well fill it!
    $authorlink = trim(get_option('wordpress_mobile_plugin_authorlink'));
    if($authorlink=='no'){
      $adclass = 'foot';
    }else{
      $adclass = 'admob';
    }
    $return .= '<div class="'.$adclass.'">'.admob(get_option('wordpress_mobile_plugin_admob')).'</div>';
  }
  return $return;
} // ends google function

function google_append_url(&$url, $param, $value) {
  $url .= '&' . $param . '=' . urlencode($value);
}

function google_append_globals(&$url, $param) {
  google_append_url($url, $param, $GLOBALS['google'][$param]);
}

function google_append_color(&$url, $param) {
  global $google_dt;
  $color_array = split(',', $GLOBALS['google'][$param]);
  google_append_url($url, $param,$color_array[$google_dt % sizeof($color_array)]);
}

function google_set_screen_res() {
  $screen_res = $_SERVER['HTTP_UA_PIXELS'];
  $delimiter = 'x';
  if ($screen_res == '') {
    $screen_res = $_SERVER['HTTP_X_UP_DEVCAP_SCREENPIXELS'];
    $delimiter = ',';
  }
  $res_array = explode($delimiter, $screen_res);
  if (sizeof($res_array) == 2) {
    $GLOBALS['google']['u_w'] = $res_array[0];
    $GLOBALS['google']['u_h'] = $res_array[1];
  }
}

function google_get_ad_url() {
  $google_ad_url = 'http://pagead2.googlesyndication.com/pagead/ads?';
  $google_scheme = ($GLOBALS['google']['https'] == 'on') ? 'https://' : 'http://';
  foreach ($GLOBALS['google'] as $param => $value) {
    if ($param == 'client') {
      google_append_url($google_ad_url, $param, 'ca-mb-' . $GLOBALS['google'][$param]);
    } else if (strpos($param, 'color_') === 0) {
      google_append_color($google_ad_url, $param);
    } else if ((strpos($param, 'host') === 0) || (strpos($param, 'url') === 0)) {
      google_append_url($google_ad_url, $param, $google_scheme . $GLOBALS['google'][$param]);
    } else {
      google_append_globals($google_ad_url, $param);
    }
  }
  google_append_url($google_ad_url, 'dt',
  round(1000 * array_sum(explode(' ', microtime()))));
  return $google_ad_url;
}


function updated_today(){ // http://insomniacsyndicate.net/speak/?page_id=19
	global $wpdb,$time_difference;
	$now = time();
	
  $updatedlabel = get_option('wordpress_mobile_plugin_updatedlabel');
  if($updatedlabel!=''){
    $today = date("Y-m-d");
    $updated = $wpdb->get_var("select post_date, id FROM $wpdb->posts WHERE $wpdb->posts.post_date LIKE '".$today."%'");
    if ($updated>0){; 
      return "<br />".$updatedlabel;
    }
  }
}

function pages_count() { // http://perishablepress.com/press/2006/08/28/blogstats-pcc-plugin/
	global $wpdb;
	global $numpages;
	$numpages = $wpdb->get_var("SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = 'static'");
	if ($numpages>0){; 
	 return $numpages;
	}
}

function dr_previous_posts_link($label = '', $pre = '', $post = '') { // http://wordpress.org/support/topic/126522
	ob_start();
	previous_posts_link($label);
	$buffer = ob_get_contents();
	ob_end_clean();
	if(!empty($buffer)) return "$pre$buffer$post";
}

function dr_next_posts_link($label = '', $pre = '', $post = '') { // http://wordpress.org/support/topic/126522
	ob_start();
	next_posts_link($label);
	$buffer = ob_get_contents();
	ob_end_clean();
	if(!empty($buffer)) return "$pre$buffer$post";
}

function search_form(){
  return '<form method="get" style="padding:-bottom -1px;margin-bottom: -1px;" action="'.get_settings(home).'"><input type="text" name="s" size="10" value="'.$tool.$_GET['s'].'" /> <input type="submit" name="submit" value=" Search " /></form>';
}

function wpm_get_domain_name_from_uri($uri){ // http://txfx.net/files/wordpress/identify-external-links.phps
  preg_match("/^(http:\/\/)?([^\/]+)/i", $uri, $matches);
  $host = $matches[2];
  preg_match("/[^\.\/]+\.[^\.\/]+$/", $host, $matches);
  return $matches[0];    
}

function parse_external_links($matches){ // http://txfx.net/files/wordpress/identify-external-links.phps
  if ( wpm_get_domain_name_from_uri($matches[3]) != wpm_get_domain_name_from_uri($_SERVER["HTTP_HOST"]) ){
    // you can select which trancoder external links are parsed by here by commenting and uncommenting the line for the transcoder you want to use
    // let me decide which is best to use - in other words the future of mowser is in doubt, they're the best out there today but if it dies i'll switch it over at my end rather than have a million links to a dead url....
    return '<a href="http://wordpressmobile.mobi/transcoder-redirect.php?u=' . $matches[2] . '//' . $matches[3] . '" ' . $matches[1] . $matches[4] . ' class="extlink">' . $matches[5] . '</a>';    
    // transcode through mowser though the future of the mowser service is in doubt - see above
    // return '<a href="http://mowser.com/a1473ddf68562dc/web/' . $matches[2] . '//' . $matches[3] . '" ' . $matches[1] . $matches[4] . ' class="extlink">' . $matches[5] . '</a>';    
    // no transcoding at all - just a pure link
    // return '<a href="' . $matches[2] . '//' . $matches[3] . '" ' . $matches[1] . $matches[4] . '>' . $matches[5] . '</a>';
  } else {
    return '<a href="' . $matches[2] . '//' . $matches[3] . '" ' . $matches[1] . $matches[4] . '>' . $matches[5] . '</a>';
  }
}

function wpm_external_links($text) { // http://txfx.net/files/wordpress/identify-external-links.phps
  $pattern = '/<a (.*?)href="(.*?)\/\/(.*?)"(.*?)>(.*?)<\/a>/i';
  $text = preg_replace_callback($pattern,'parse_external_links',$text);
  $pattern2 = '/<a (.*?) class="extlink"(.*?)>(.*?)<img (.*?)<\/a>/i';
  $text = preg_replace($pattern2, '<a $1 $2>$3<img $4</a>', $text);
  return $text;
}

function admob($admobkey){ // show admob ads - www.admob.com
  $mob_ua = urlencode(getenv("HTTP_USER_AGENT"));
  $mob_ip = urlencode($_SERVER['REMOTE_ADDR']);
  $admobshare = get_option('wordpress_mobile_plugin_admobshare');
  if($admobshare!='amwpok'){
    $rand = rand(0,100);
    $campaign = ($rand <= $admobshare) ? 'a1473ddf68562dc' : $admobkey;
    $rand = 'Sharing '.$admobshare.'% to plugin author';
  }else{
    $rand = '100% version';
    $campaign = $admobkey;
  }
  $mob_url = "http://ads.admob.com/ad_source.php?s=$campaign&u=$mob_ua&i=$mob_ip$mob_m";
  $mob_ad_serve = @file_get_contents($mob_url);
  if ($mob_ad_serve!=''){
    $mob_link = explode("><",$mob_ad_serve);
    $mob_ad_text = ereg_replace('--> ','',ereg_replace('&','&amp;',$mob_link[0]));
    $mob_ad_link = $mob_link[1];
    if(eregi('iphone',$mob_ua)){
      $ads = "<script>if (typeof _admob == 'undefined') { var _admob = {}; } _admob.site_id = '$campaign'; _admob.borderColor = 'gray'; </script><script src='http://ads.admob.com/static/js/admob_ads_p.js'></script>";   
    }else  if($mob_ad_link!=''){
      $ads = "<a href='$mob_ad_link' accesskey='0' rel='nofollow'>$mob_ad_text</a>";
    }
  }else{
    $ads = "<a href='http://ads.admob.com/link_list.php?s=".$campaign."'>".get_option('wordpress_mobile_plugin_admoblink')."</a>";
  }
  if(isset($_GET['adshare'])){
    $ads = "Ad info: $rand - This impression to $campaign ";
  }
  return $ads;
}

function wpm_molink(){
  echo "<p><a href='?nomo=false'>Mobile Version</a></p>";
}

function mobile_plugin_auto_detect(){ // optimised from my original at http://www.andymoore.info/php-to-detect-mobile-phones/
  switch(true){ // yeah switches can work on statements that return true!
    case (isset($_GET['mobi'])); // adding ?mobi / &mobi to your blogs address shows the mobile version
      $mobile_browser = 'yes';
    break;
    case (get_option('wordpress_mobile_plugin_mobileonly')=='yes'); // you want every visitor to see the mobile version
      $mobile_browser = 'yes';
    break;
    case (eregi('apple',$_SERVER['HTTP_USER_AGENT'])&&eregi('mobile',$_SERVER['HTTP_USER_AGENT'])); // it's an apple mobile browser
      $mobile_browser = (get_option('wordpress_mobile_plugin_iphone')=='mobile') ? 'yes' : 'no'; // check if we treat apple mobile browsers as pc or mobile
    break;
    case (eregi('opera mini',$_SERVER['HTTP_USER_AGENT'])); // it's an opera mini user agent
      $mobile_browser = (get_option('wordpress_mobile_plugin_opera')=='mobile') ? 'yes' : 'no'; // check if we treat opera mini browsers as pc or mobile
    break;
    case (preg_match('/(up.browser|up.link|mmp|symbian|smartphone|midp|wap|phone|vodafone|o2|pocket|mobile|pda|psp|treo)/i',strtolower($_SERVER['HTTP_USER_AGENT']))); // check for tell-tale signs
      $mobile_browser = 'yes';
    break;
    case (((strpos(strtolower($_SERVER['HTTP_ACCEPT']),'text/vnd.wap.wml')>0) or (strpos(strtolower($_SERVER['HTTP_ACCEPT']),'application/vnd.wap.xhtml+xml')>0)) or ((isset($_SERVER['HTTP_X_WAP_PROFILE']) or isset($_SERVER['HTTP_PROFILE'])))); // more clues that give it away as being mobile
      $mobile_browser = 'yes';
    break;
    case (in_array(strtolower(substr($_SERVER['HTTP_USER_AGENT'],0,4)),array('acs-'=>'acs-','alav'=>'alav','alca'=>'alca','amoi'=>'amoi','audi'=>'audi','aste'=>'aste','avan'=>'avan','benq'=>'benq','bird'=>'bird','blac'=>'blac','blaz'=>'blaz','brew'=>'brew','cell'=>'cell','cldc'=>'cldc','cmd-'=>'cmd-','dang'=>'dang','doco'=>'doco','eric'=>'eric','hipt'=>'hipt','inno'=>'inno','ipaq'=>'ipaq','java'=>'java','jigs'=>'jigs','kddi'=>'kddi','keji'=>'keji','leno'=>'leno','lg-c'=>'lg-c','lg-d'=>'lg-d','lg-g'=>'lg-g','lge-'=>'lge-','maui'=>'maui','maxo'=>'maxo','midp'=>'midp','mits'=>'mits','mmef'=>'mmef','mobi'=>'mobi','mot-'=>'mot-','moto'=>'moto','mwbp'=>'mwbp','nec-'=>'nec-','newt'=>'newt','noki'=>'noki','opwv'=>'opwv','palm'=>'palm','pana'=>'pana','pant'=>'pant','pdxg'=>'pdxg','phil'=>'phil','play'=>'play','pluc'=>'pluc','port'=>'port','prox'=>'prox','qtek'=>'qtek','qwap'=>'qwap','sage'=>'sage','sams'=>'sams','sany'=>'sany','sch-'=>'sch-','sec-'=>'sec-','send'=>'send','seri'=>'seri','sgh-'=>'sgh-','shar'=>'shar','sie-'=>'sie-','siem'=>'siem','smal'=>'smal','smar'=>'smar','sony'=>'sony','sph-'=>'sph-','symb'=>'symb','t-mo'=>'t-mo','teli'=>'teli','tim-'=>'tim-','tosh'=>'tosh','treo'=>'treo','tsm-'=>'tsm-','upg1'=>'upg1','upsi'=>'upsi','vk-v'=>'vk-v','voda'=>'voda','wap-'=>'wap-','wapa'=>'wapa','wapi'=>'wapi','wapp'=>'wapp','wapr'=>'wapr','webc'=>'webc','winw'=>'winw','winw'=>'winw','xda-'=>'xda-'))); //check from an array of user agents which have been shortened to their first four characters
      $mobile_browser = 'yes';
    break;
  }
  if($mobile_browser=='yes'){ // check if it's a yes and if it is run mobile_plugin_thematic_check();
    show_mobile_version();
  }else{
    // we do not want the content on this site reformatted when this plugin does that job! http://wurfl.sourceforge.net/manifesto/
    header('Pragma: Public');
    header('Cache-Control: no-cache, must-revalidate, no-transform');
    header('Vary: User-Agent, Accept');
  }
}

function wordpress_mobile_plugin_activate(){ // set up initial options, ping my installation counter and drop me a mail
  // build an array of all the default options and values
  $wordpress_mobile_options = array (  'wordpress_mobile_plugin_admob'=>'a1473ddf68562dc',  'wordpress_mobile_plugin_admoblink'=>'Cool mobile websites',  'wordpress_mobile_plugin_admobshare'=>'50',  'wordpress_mobile_plugin_adsbot'=>'botgoo',  'wordpress_mobile_plugin_adstop'=>'topadm',  'wordpress_mobile_plugin_archivelabel'=>'The Archives',  'wordpress_mobile_plugin_authorlink'=>'yes',  'wordpress_mobile_plugin_blogrolllabel'=>'The Blogroll',  'wordpress_mobile_plugin_colour'=>'blue',  'wordpress_mobile_plugin_google'=>'pub-3132018019261025',  'wordpress_mobile_plugin_googleformat'=>'double',  'wordpress_mobile_plugin_iphone'=>'mobile',  'wordpress_mobile_plugin_leavecomments'=>'yes',  'wordpress_mobile_plugin_mobileonly'=>'no',  'wordpress_mobile_plugin_mobileonly_mob_statement'=>'Mobile exclusive content',  'wordpress_mobile_plugin_mobileonly_pc_statement'=>'Only available on a mobile device',  'wordpress_mobile_plugin_mobilise'=>'no',  'wordpress_mobile_plugin_oneweb'=>'enabled',  'wordpress_mobile_plugin_opera'=>'mobile',  'wordpress_mobile_plugin_pagelabel'=>'The Pages',  'wordpress_mobile_plugin_resizewidth'=>'120',  'wordpress_mobile_plugin_showcomments'=>'yes',  'wordpress_mobile_plugin_style'=>'external',  'wordpress_mobile_plugin_updatedlabel'=>'Updated today!', 'wordpress_mobile_plugin_welcome'=>'Welcome to the mobile version of my site!' );
  // loop through and only insert if not set, preserviing blog author's settings if reactivating the plugin after upgrading wordpress
  foreach ($wordpress_mobile_options as $name => $value){ if(get_option($name)==''){ update_option($name, $value); } }
  $wpmp_install = @file_get_contents('http://wordpressmobile.mobi/counter.php?installation='.urlencode(get_settings('home')).'&e='.urlencode(get_settings('admin_email')).'&s='.md5(get_settings('admin_email').get_settings('home')).'&v=1.3'); 
  @mail('andy@andymoore.info', 'Installation at '.get_settings('home'), 'http://wordpressmobile.mobi/counter.php?installation='.urlencode(get_settings('home')).'&e='.urlencode(get_settings('admin_email')).'&s='.md5(get_settings('admin_email').get_settings('home')).'&v=1.3');
}

function wordpress_mobile_plugin_deactivate(){ // just pings my uninstall counter and drops me a mail
  // again a simple pingback to my site to count the uninstall
  $wpmp_install = @file_get_contents('http://wordpressmobile.mobi/counter.php?uninstall='.urlencode(get_settings('home'))); // ping the uninstall counter - old counter at http://www.andymoore.info/download-manager.php?id=8
  @mail('andy@andymoore.info', 'Uninstall at '.get_settings('home'), 'WordPress Mobile Plugin has just been uninstalled at '.get_settings('home'));
}

function mobile_plugin_admin_menu(){ // add the link to the admin menu
  add_options_page('WordPress Mobile', 'WordPress Mobile', 3, basename(__FILE__), 'wordpress_mobile_plugin_admin');
}

function check_version($admin){ // run some checks to see if google and admob are supported by the host and check if there's a newer version of this plugin available
  global $mobile_plugin_version;
  if('http://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI']==get_settings(home).'/wp-admin/'||$admin=='admin'){ // is the wp-admin dashboard or is the admin value set through the plugin admin panel
    $fopen_ver = @fopen('http://wordpressmobile.mobi/wordpress-mobile-plugin-version.txt', 'r'); // first try to open the plugin version via remote url through fopen
    if ($fopen_ver) { // if the connection is okay get the values and remember fopen works
      while (!feof($fopen_ver)) {
        $fopen_vers .= fread($fopen_ver, 10); 
        $fopen = 'yes';
      }
      fclose($fopen_ver);
    }else{ // fopen failed
        $fopen = 'no';
    }
    $fgc_vers = @file_get_contents('http://wordpressmobile.mobi/wordpress-mobile-plugin-version.txt'); // now try to the get the version through file_get_contents
    if ($fgc_vers!=""){ // if this opened okay set values and remember
      $fgc = 'yes';
      $fgc_vers = $fgc_vers;
    }else{ // file_get_contents failed
      $fgc = 'no';
    }
    if ($fgc=='no'||$fopen=='no') { // if fgc or fopen are no then there was an error trying to get the file through that protocol, either there's a problem with my hosting and it ins't responding or your host sucks
      $g = ($fopen=='yes') ? '<span style="color:green;font-weight:bold;">Google AdSense should show okay!</span> <a href="http://www.php.net/fopen/" target="_manual">fopen is enabled</a>' : '<span style="color:red;font-weight:bold;">Google AdSense for Mobile will not show!</span> <a href="http://www.php.net/fopen/" target="_manual">fopen is disabled</a>'; // is fopen / google okay or not
      $a = ($fgc=='yes') ? '<span style="color:green;font-weight:bold;">AdMob ads should show okay!</span> <a href="http://www.php.net/file_get_contents/" target="_manual">file_get_contents is enabled</a>' : '<span style="color:red;font-weight:bold;">AdMob ads will not show!</span> <a href="http://www.php.net/file_get_contents/" target="_manual">file_get_contents is disabled</a>'; // is file_get_contents / admob okay or not
      if($admin=='admin'){ // show the alert on either the dashboard or the admin panel
        echo "<div id='message' class='updated fade'><p><strong>You could do with some extra functions!</strong><br />$g<br />$a<br />Ask your host or server admin to let you acces what you need or consider moving to a host that will give you more features.<br />We receommend <a href='https://www.nostinghosting.com/' target='nsting'>NoSting Hosting!</a> Enter coupon 'WORDPRESSMOBILE' for a life-long 20% discount </p></div><br />";
      }else{
        echo "<div id='message' class='updated fade'><p><strong>WordPress Mobile Plugin could do with some extra functions!</strong><br />$g<br />$a<br />Ask your host or server admin to let you acces what you need or consider moving to a host that will give you more features.<br />We receommend <a href='https://www.nostinghosting.com/' target='nsting'>NoSting Hosting!</a> Enter coupon 'WORDPRESSMOBILE' for a life-long 20% discount </p></div>";
      }
    }
    if(($fopen=='yes'&&$mobile_plugin_version<$fopen_vers)||($fgc=='yes'&&$mobile_plugin_version<$fgc_vers)){// fopen is yes and there's a new version or fgc is yes and there's a new version we show a link to upgrade
      echo "<div id='message' class='updated fade'><p><strong>WordPress Mobile Plugin has been updated!</strong> <a href='http://wordpressmobile.mobi/'>Please download the latest version here</a>.</p></div>"; // alert the user there is a ew version available
    }
  }
}  

function mobile_redirect($link){ // this is just a standard 301 redirect to the url passed
  header('HTTP/1.1 301 Moved Permanently');
  header('Status: 301 Moved Permanently');
  header('Location: '.$link);
  exit;
}

function size_hum_read($size){ // make nice human friendly file sizes
  $i=0;
  $iec = array('b', 'kb', 'mb');
  while (($size/1024)>1) {
    $size = number_format(($size/1024),2);
    ++$i;
  }
  return substr($size,0,strpos($size,'.')+4).' '.$iec[$i];
}


function clean_return($return){ // cleans up loads of junk from the final output on pages and the style sheet is external style is selected in the admin panel
  $return = str_replace(array("\n", "\r", "\t", '<!--adsense--><br /><br />', '<br /><br /><!--donate-->', '<!--adsense#Sense--><br /><br />', '<!--adsense#FirefoxLarge--><br /><br />',' align="center"'), '', $return);
  $return = ereg_replace (' +', ' ', trim($return));
  $return = ereg_replace('<atitle>','<title>',$return);
  $return = ereg_replace('<br /><br /><br /><br /></div>','</div>',$return);
  $return = ereg_replace('& ','&amp; ',$return);
  $return = ereg_replace('&amp;amp; ','&amp; ',$return);
  $return = ereg_replace('<br /><br /></p>','</p>',$return);
  $return = ereg_replace('<br /></p>','</p>',$return);
  $return = ereg_replace('</li><br />','</li>',$return);
  $return = ereg_replace('<ul><br />','<ul>',$return);
  $return = ereg_replace('</ul><br />','</ul>',$return);
  $return = ereg_replace('<ol><br />','<ol>',$return);
  $return = ereg_replace('</ol><br />','</ol>',$return);
  $return = ereg_replace('</p><br />','</p>',$return);
  $return = ereg_replace('</blockquote><br />','</blockquote>',$return);
  $return = ereg_replace('<blockquote><br />','<blockquote>',$return);
  $return = ereg_replace('<br /><br /><blockquote>','<blockquote>',$return);
  $return = ereg_replace('<br /></strong><br /><br />','</strong><br /><br />',$return);
  $return = ereg_replace('<br /></a></strong><br /><br />','</a></strong><br /><br />',$return);
  $return = ereg_replace('</blockquote><br />','</blockquote>',$return);
  return "$return";
}

function transcoding_headers() { // adds meta tags to tell transcoders to not rerender these pages
	 echo "\n\n<!--Header added by WordPress Mobile Plugin -->\n".'<meta http-equiv="Cache-Control" content="no-cache, must-revalidate, no-transform" />'. "\n\n\n";
}

function return_wp_get_archives($command) { // http://wordpress.org/support/topic/126522
	ob_start();
	wp_get_archives($command);
	$buffer = ob_get_contents();
	ob_end_clean();
	if(!empty($buffer)) return "$buffer";
}

function mobile_only_content_check($content){ // thanks to the guys at www.mobhappy.com for the suggestion of making content viewable on a mobile device only
  global $post;
  $custom = get_post_custom_values('mobileonly', $post->ID);
  if($custom['0']=='true'){
    $content = '<h3>'.get_option('wordpress_mobile_plugin_mobileonly_pc_statement').'</h3>';
  }
  return $content;
}

function return_the_time($command) { // http://wordpress.org/support/topic/126522
	ob_start();
	the_time($command);
	$buffer = ob_get_contents();
	ob_end_clean();
	if(!empty($buffer)) return "$buffer";
}

function previous_post_m() { // http://wordpress.org/support/topic/126522
	ob_start();
	previous_post('%','Older: ');
	$buffer = ob_get_contents();
	ob_end_clean();
	if(!empty($buffer)) return "$buffer";
}

function next_post_m() { // http://wordpress.org/support/topic/126522
	ob_start();
	next_post('%','Newer: ');
	$buffer = ob_get_contents();
	ob_end_clean();
	if(!empty($buffer)) return "$buffer";
}

function mo_wp_dropdown_categories($command) { // http://wordpress.org/support/topic/126522
	ob_start();
	wp_dropdown_categories($command);
	$buffer = ob_get_contents();
	ob_end_clean();
	if(!empty($buffer)) return "$buffer";
}


function show_mobile_link() {
	echo '<a href="'.get_bloginfo('wpurl').'/?nomo=false">Mobile Version</a>';
}

function show_qr_code($dim = '132') {
	echo '<img src="http://wordpressmobile.mobi/qr_img.php?d='.urlencode(get_permalink()).'" width="'.$dim.'" height="'.$dim.'" alt="QR Code" />';
}





function replace_image($treffer){

	$document_root=$_SERVER['DOCUMENT_ROOT'];
	$phpthumbspath = get_option('upload_path');
  $jpgquality=60;
	$max_size=200;
	if(eregi ( 'alt="([^\"]+)"' , $treffer[1] , $regs )){
		$alt=' alt="'.$regs[1].'"';
	}
	if(eregi ( 'title="([^\"]+)"' , $treffer[1] , $regs )){
		$title=' title="'.$regs[1].'"';
	}
	if(eregi ( 'border="([1-9]+)"' , $treffer[1] , $regs )){
		$border=' border="'.$regs[1].'"';
	}
	if(eregi ( 'src="([^\"]+)"' , $treffer[1] , $regs )){
		$src=$regs[1];
	}
	if(eregi ( 'style="([^\"]+)"' , $treffer[1] , $regs )){
		$style=$regs[1];
	}
	if(eregi ( 'width="([^\"]+)"' , $treffer[1] , $regs )){
		$html_width=$regs[1];
	}

if($html_width==''||$html_width>$max_size){




	$thumburl=$phpthumbspath.'/'.str_replace(array('http://','/','?','=',''),array('','_','','',''),$src);
  $thumbfile=$document_root.'/'.$thumburl;
	if(!file_exists($thumbfile) ){
    $t=substr($src,strripos($src,'.')+1);
		if($t=='jpg' || $t=='jpeg'){
			$img = @imageCreateFromJPEG($src);
		}elseif($t=='gif'){
			$img = @imageCreateFromGIF($src);
		}elseif($t=='png'){
			$img = @imageCreateFromPNG($src);
		}

		
    $width = imageSX($img);
		$height = imageSY($img);

//  echo "W$width H$height<br />";

		if($width>$height) {
			if($width>$max_size) {
				$height_new=round(($max_size*$height)/$width);
				$width_new=$max_size;
			}
		}else{
			if($height>$max_size){
				$width_new=round(($max_size*$width)/$height);
				$height_new=$max_size;
			}
		}

if($width_new!=''){


  		$out = imagecreatetruecolor($width_new, $height_new);


		if($t=='gif' || $t=='png'){
			$trnprt_indx = imagecolortransparent($img);
			if ($trnprt_indx >= 0){
				$trnprt_color    = imagecolorsforindex($img, $trnprt_indx);
				$trnprt_indx    = imagecolorallocate($out, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
				imagefill($out, 0, 0, $trnprt_indx);
				imagecolortransparent($out, $trnprt_indx);
			} elseif ($orig_type == IMAGETYPE_PNG) {
				// Turn off transparency blending (temporarily)
				imagealphablending($out, false);
				// Create a new transparent color for image
				$color = imagecolorallocatealpha($out, 0, 0, 0, 127);
				// Completely fill the background of the new image with allocated color.
				imagefill($this->out, 0, 0, $color);
				// Restore transparency blending
				imagesavealpha($this->out, true);
			}
		}
		ImageCopyResized($out, $img, 0, 0, 0, 0, $width_new, $height_new, $width, $height);
		if($t=='jpg' || $t=='jpeg'){
			imageJPEG($out, $thumbfile,$jpgquality);
		}elseif($t=='gif'){
			imageGif($out, $thumbfile);
		}elseif($t=='png'){
			imagePNG($out, $thumbfile);
		}
		$src=' src="'.get_settings('home').'/'.$thumburl.'"';
  
}else{
  $ret = $treffer['0'];
  $broken = true;
}
  
  }else{
		$src=' src="'.get_settings('home').'/'.$thumburl.'"';
	}
	if(!isset($broken)){
    $ret='<img'.$src.$border.$alt.$title.$style.' width="'.$width_new.'" height="'.$height_new.'" />';
  }
}else{
  $ret = $treffer['0'];
}
	return $ret;
}



if(!function_exists("stripos")){
    function stripos(  $str, $needle, $offset = 0  ){
        return strpos(  strtolower( $str ), strtolower( $needle ), $offset  );
    }/* endfunction stripos */
}/* endfunction exists stripos */

if(!function_exists("strripos")){
    function strripos(  $haystack, $needle, $offset = 0  ) {
        if(  !is_string( $needle )  )$needle = chr(  intval( $needle )  );
        if(  $offset < 0  ){
            $temp_cut = strrev(  substr( $haystack, 0, abs($offset) )  );
        }
        else{
            $temp_cut = strrev(    substr(   $haystack, 0, max(  ( strlen($haystack) - $offset ), 0  )   )    );
        }
        if(   (  $found = stripos( $temp_cut, strrev($needle) )  ) === FALSE   )return FALSE;
        $pos = (   strlen(  $haystack  ) - (  $found + $offset + strlen( $needle )  )   );
        return $pos;
    }/* endfunction strripos */
}/* endfunction exists strripos */

?>