<?php

/*
Plugin Name: GeSHi
Plugin URI: http://www.thedevproject.com/projects/wordpress-geshi-plugin/
Description: WordPress GeSHi plugin. Parses code tags with GeSHi inside page/post/comment content. Language default is PHP. You can specify the language with &lt;code lang="mysql"&gt;&lt;/code&gt;. You can change the GeSHi parameters through the $geshi global object in your themes functions.php file. GeSHi documentation can be found here: http://qbnz.com/highlighter/
Version: 0.1
Author: Dan Peverill
Author URI: http://www.thedevproject.com
*/

/*
    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 2 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

// Geshi source.
include (dirname(__FILE__) . "/geshi/geshi.php");

// Global object. Can alter in template.
$geshi = new GeSHi("", GESHI_DEFAULT_LANGUAGE);

// Plugin specific defaults.
$geshi->match_pattern       = "/<\s*code(\s+lang=\"([^\"]+)\"){0,1}\s*>(.+)<\s*\/\s*code\s*>/siU";
$geshi->matches             = array();
$geshi->default_lang        = "php";

// Filters.
// WordPress has some really jacked up filters, so to avoid WordPress screwing up the source code,
// we'll have to remove the code, allow WordPress to filter the post/comment/page, then re-add the parsed source code.
// This also has the side benefit of avoiding <br> and <p> tags being splattered all over our source.

add_filter("the_content", "geshi_remove_code", 1);
add_filter("the_content", "geshi_add_code", 1000);
add_filter("comment_text", "geshi_remove_code", 1);
add_filter("comment_text", "geshi_add_code", 1000);
add_filter('the_excerpt', 'geshi_excerpt_code', 1);


/**
* Code needs to be removed before WordPress filters it.
* @param string
* @return string
*/
function geshi_remove_code($content)
{
  global $geshi;
  
  $count = 0;
  while (preg_match($geshi->match_pattern, $content, $match))
  {
    $geshi->matches[] = $match;
    $content = str_replace($match[0], '<code id="'.$count.'"></code>', $content);
    $count++;
  }
  
  return $content;
}

/**
* WordPress is done with its wacked text filters, so we'll need to re-add the code.
* Note that the HTML has been escaped, so we need to unescape it before parsing with GeSHi.
* @param string
* @return string
*/
function geshi_add_code($content)
{
  global $geshi;
  
  $count = count($geshi->matches);
  for ($i = 0; $i < $count; $i++)
  {
    $match = $geshi->matches[$i];
  
    // Set language.
    if (isset($match[2]) && $match[2])
      $geshi->set_language($match[2]);
    
    if (!isset($match[2]) || !$match[2] || $geshi->error == GESHI_ERROR_NO_SUCH_LANG)
      $geshi->set_language($geshi->default_lang);
      
    // Parse code.
    $geshi->set_source(trim($match[3]));
    $content = str_replace('<code id="'.$i.'"></code>', $geshi->parse_code(), $content);
  }

  $geshi->matches = array();  // Clear matches for next round of content.
  
  return $content;
}

/**
* Removes the code blocks.
* @param string
* @return string.
*/
function geshi_excerpt_code($content)
{
  global $geshi;
  
  // Don't show code in an excerpt. 
  return preg_replace($geshi->match_pattern, "<em>[Code Snippet]</em>", $content);
}

?>