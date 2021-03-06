=== FeedStats ===
Contributors: Bueltge, benjaminwittorf, inpsyde
Donate link: http://bueltge.de/wunschliste/
Tags: feed, statistics, stats, rss
Requires at least: 1.5
Tested up to: 3.5-RC4
Stable tag: 3.8.1

Simple statistictool for feeds.

== Description ==
Simple statistictool for feeds, by [Andres Nieto Porras](http://www.anieto2k.com "Andres Nieto Porras") and [Frank Bueltge](http://bueltge.de "Frank Bueltge").

_You can customize in options:_

* Amount of days that is supposed to be saved in the statistics.
* Minimum level of WordPress-user, who is allowed to see the statistics.
* Time of a stay/visit (1hour values 3600seconds is common but might be changed)
* Visitors onlinetime (5minutes value 300s is a recommendation)
* IP, that is supposed not to be saved, ex.: your own IP
* Statistics can be shown on the dashboard

The Plugin is in english language and have the german and traditional chinese translation in language-file. For traditional chinese translation give it a another icon for feedreaders in an button.

**Made by [Inpsyde](http://inpsyde.com) &middot; We love WordPress**

Have a look at the premium plugins in our [market](http://marketpress.com).

== Installation ==
1. Unpack the download-package
1. Upload all files to the `/wp-content/plugins/` directory, with folder `feedstats-de`
1. Activate the plugin through the 'Plugins' menu in WordPress
1. Got to 'Options' menu and configure the plugin

== Screenshots ==
1. Statistic-area in WordPress 3.4
1. Statistic-area in WordPress 2.7
1. Statistic-area in WordPress 2.5
1. configure-area
1. Statistic-area in WP 2.3 (Example) for 10 Days

== Other Notes ==
Use the follow function for write the statistic on your blog.

`<php feedstats_getfeeds(); ?>`

_Example:_

`<?php if (function_exists('feedstats_getfeeds')) : feedstats_getfeeds(); endif; ?>`

You can also use an Button with your average feedreaders. Use function `feedstats_getfeeds_button()` and format with CSS.

_Example:_

`<div id="feeds_button"><?php feedstats_getfeeds_button(); ?></div>`

`#feeds_button {
	width: 74px;
	height: 14px;
	text-align: left;
	font-size: 10px;
	padding: 1px 15px 15px 3px;
	color: #fff;
	background: url(wp-content/plugins/feedstats-de/images/feedstats-de.gif) no-repeat;
	margin-bottom: 2px;
}`
For use the default-icon


`#feeds_button {
	width: 74px;
	height: 14px;
	text-align: left;
	font-size: 10px;
	padding: 1px 15px 15px 3px;
	color: #fff;
	background: url(wp-content/plugins/feedstats-de/images/feedstats-de-zh_TW.gif) no-repeat;
	margin-bottom: 2px;
}`

for style-css in traditional Chinese (zh_TW) translation

`#feeds_button {
	width: 74px;
	height: 14px;
	text-align: left;
	font-size: 10px;
	padding: 1px 15px 15px 3px;
	color: #fff;
	background: url(wp-content/plugins/feedstats-de/images/feedstats-de-it_IT.gif) no-repeat;
	margin-bottom: 2px;
}`

for style-css in italien (it_IT) translation

= Acknowledgements =
* FeedReaderButton (gif) by [Christoph Schr&ouml;der](http://www.nasendackel.de "Christoph Schr&ouml;der")
* FeedReaderButton (gif - traditional Chinese (zh_TW)) and traditional Chinese translation by [Neil Lin](http://www.wmfield.idv.tw/485 "Neil Lin")
* FeedReaderButton (gif - italien (it_IT)) and italien translation by [Gianni Diurno](http://gidibao.net/ "Gianni Diurno")
* Turkish translation by [Baris Unver](http://beyn.org "Baris Unver")
* French translation by [burningHat](http://blog.burninghat.net/ "burningHat")
* Spanich translation by [fportero](http://www.tengotiempo.com "fportero")
* Thanks to Fat Cow for the belarussian language files.
* Ukrainian translation by [AzzePis](http://wordpress.co.ua/)
* Czech translation by [Lelkoun](http://lelkoun.cz)
* Polish translation by [Online Casino Bluebook](http://www.onlinecasinobluebook.com)
* Thanks for bulgarian language files to [Web Geek](http://webhostinggeeks.com/)
* Russian translation by [DachaDecor](http://dachadecor.ru)
* Thanks to [Brian Flores](http://www.inmotionhosting.com/) for serbian translation
* Thanks to [Pieter Carette](http://www.siteoptimo.com)
* Lithuanian translation files by [Vincent G](http://www.host1free.com)


= Licence =
Good news, this plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial blog. But if you enjoy this plugin, you can thank me and leave a [small donation](http://bueltge.de/wunschliste/ "Wishliste and Donate") for the time I've spent writing and supporting this plugin. And I really don't want to know how many hours of my life this plugin has already eaten ;)

= Translations =
The plugin comes with various translations, please refer to the [WordPress Codex](http://codex.wordpress.org/Installing_WordPress_in_Your_Language "Installing WordPress in Your Language") for more information about activating the translation. If you want to help to translate the plugin to your language, please have a look at the sitemap.pot file which contains all defintions and may be used with a [gettext](http://www.gnu.org/software/gettext/) editor like [Poedit](http://www.poedit.net/) (Windows).


== Changelog ==
= 3.8.1 (09/12/2012) =
* Fix for create new tables to track data with WP 3.4*

= v3.8.0 (13/11/2012) =
* Fix the different sources for newer WordPress version
* Different changes on source
* Enhance the IP check

= v3.7.3 (07/07/2011) =
* change capabilities object form int to string
* kill php warnings and notice

