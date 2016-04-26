=== WassUp ===
Contributors: michelem, helened
Donate link: https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=michele%40befree%2eit&item_name=WassUp&no_shipping=0&no_note=1&tax=0&currency_code=EUR&lc=IT&bn=PP%2dDonationsBF&charset=UTF%2d8
Tags: tracker, tracking, statistics, analyze, web, realtime, stats, ajax, visitors, visits, online users, details, seo, admin, spy, visitors, widgets, widget, sidebar, monitor, stalker, detector, webmaster, tool, geolocation, chart, google!charts, spammers, exploits, injection, security, useragent, browser, spider, detection, pageviews 
Requires at least: 2.2
Tested up to: 3.0.2
Stable tag: 1.8.1

Analyze your visitors traffic with real-time statistics, a lot of chronological information, charts, a sidebar widget.

== Description ==

WassUp is a Wordpress plugin to track your visitors in real-time.  It has a very readable and fancy admin console to keep track of your visitors that gives you a detailed view into almost everything your users are doing on your site. It is very useful for SEO or statistics maniacs. 

The aim of WassUp is the knowledge of what your visitors do when they surf your site.  It is not intended to show grouped statistics over preset time periods like visitors per day, pageviews per months, and so on (there are many others tools to better gain that, like Google Analytics). Instead, WassUp provides flexible, easy-to-read views into your visitors data that is customizable by time periods, visitor types, search keywords, and much more.

DISCLAIMER: Use at your own risk. No warranty expressed or implied is provided.

= WassUp comes with 4 admin submenus for viewing your visitors' activities and for customizing those views =
* There is a fancy "Visitors Details" view with search capability, view filters, plus a chart and top ten summary, that allows you to see almost everything about your visitors and what they do on your site.
* There is an ajax "Spy" view (like Digg Spy) that lets you monitor and geolocate your visitors, live. 
* There is a "Current Visitors Online" view that shows a summary of your online visitors in real-time.
* There is an "Options" view with lots of customizable settings for WassUp. 

There is a nice Dashboard widget that shows a line chart of hits over time (24 hours default) and a count of current visitors online and their latest activities.

There is also a useful sidebar Widget that shows the count of current visitors online (default) and that can display recent searched terms, recent external referrers, top browsers, and top OSes. The widget is fully customizable.

WassUp works with two anti-spam functions to detect and omit (if you want) referrers spammers and comment spammers. It also detects and records unauthorized users' login attempts, script injection, and other exploit attempts.  Please note that WassUp just records exploit attempts. It does not block them or otherwise protect your site. You need a separate security plugin for that.

For people with database space problem, WassUp has a few options to manage the database table growth: you can empty it; you can delete old records automatically; and you can set a warning notice for when it exceeds a preset size limit.

= WassUp gives a chronology of your site's visits with a lot of information for each single user session: = 
* ip / hostname
* referrer
* spider
* search engines used
* keywords
* SERP (search engine result page)
* operating system / language / browser
* pages viewed (chronologically and per user session)
* complete user agent
* name of user logged in
* name of comment's author
* spam and hack attempts

= Wassup has a nice admin console with flexible view filters and options: =
* records by time period
* record count per page
* records by entry type (spider, users logged in, comment authors, search engine, referrer)
* search by keyword
* expand/collapse informations (with ajax support)
* show/hide chart (Google!chart)
* top ten charts with aggregate data (top queries, requests, os, browsers)

= There are many options to customize how WassUp tracks and displays data: =
* Screen refresh minutes
* Screen resolution (browser width)
* User permission levels 
* Enable/Disable recording (tracking)
* Record or not users logged in
* Record or not spiders and bots
* Record or not exploit attempts
* Record or not comment spammers
* Record or not referrer spammers
* IPs to exclude from recording
* Domains to exclude from referrers
* Email alert for table growth

DISCLAIMER: Use at your own risk. No warranty expressed or implied is provided.

== Frequently Asked Questions ==

= How do I add WassUp's chart to my admin dashboard? =
Go to Wassup >> Wassup-Options submenu, select [General Setup] tab and check "Display small chart in dashboard", then click [Save Settings] button. 

= My Wordpress theme is not widget ready. Is it possible to add WassUp Widget to my site? =
Yes. Simply insert the template tag, "wassup_sidebar()", into your theme's "sidebar.php" file.

= How do I exclude a visitor from being recorded? =
Go to Wassup >> Wassup-Options, select [Statistics Recording] tab and enter the IP address or username to be excluded in the appropriate text area.

= How do I stop (temporarily) WassUp from recording new visits on my site? =
Go to Wassup >> Wassup-Options, select [Statistics Recording] tab and uncheck "Enable/disable recording", then click [Save Settings] button.

= Can Wassup record visits on a web site that is not Wordpress? =
No. Wassup is a Wordpress-only plugin and requires at least Wordpress 2.2 to work.

= After installing WP Supercache (or other caching plugin), Wassup shows very few visits, why is that? =
WassUp is incompatible with WP Supercache, WP Cache, Hyper Cache, or any page-based caching plugin. WassUp cannot generate accurate statistics with these plugins. 

= Is there any caching plugin that works with WassUp? =
[WP Widget Cache](http://wordpress.org/extend/plugins/wp-widget-cache/) is the only caching plugin verified to work with WassUp. However, some WassUp users have reported success using [DB Cache Reloaded](http://wordpress.org/extend/plugins/db-cache-reloaded/) and [Quick Cache](http://wordpress.org/extend/plugins/quick-cache/) plugins with WassUp.

= Why does WassUp stats show more page views than actual pages clicked by a user? =
"Phantom" page views can occur when a user's browser does automatic feed retrieval, link pre-fetching, or a page refresh. WassUp tracks these because they are valid requests from that user's browser and are indistinguishable from user link clicks.
   
= How do I uninstall WassUp? =
To completely remove WassUp's settings and tables from Wordpress, go to Wassup >> Wassup-Options, select [Uninstall] tab, check the "Uninstall" box, click the "[Save Settings]" button, then deactivate WassUp in "Plugins".

Visit [http://www.wpwp.org](http://trac.wpwp.org/wiki/Documentation) for more FAQs.

== Screenshots ==

1. Wassup visitor details view.
2. Wassup spy view.

You can find more screenshots at [http://www.wpwp.org](http://www.wpwp.org)

== Installation ==

= Installation: =

1. Download the plugin, WassUp (Real-Time Visitors Tracking), to your local computer
1. Unpack this plugin's zip or gz file with your preferred unzip/untar program or use the command line: `tar xzvf wassup.tar.gz` (linux)
1. Upload the entire "wassup" directory to your `wp-content/plugins` directory on your host server
1. Navigate to your site's Wordpress admin >> Plugins page
1. Activate WassUp plugin

OR you can install it using Wordpress automatic install by going to Plugins >> Add New >> and type "WassUp" plugin name

= Upgrading: =

1. Check your current visitors count. If your site is busy, don't upgrade!
1. Deactivate WassUp plugin in Wordpress admin >> Plugins page
1. Delete "wassup" directory from `wp-content/plugins/` on your host server
1. Download and unzip the new "WassUp" file to your local computer
1. Upload the entire "wassup" directory to your `wp-content/plugins` directory on your host server
1. Navigate to your site's Wordpress admin >> Plugins page 
1. Activate WassUp plugin

OR you can upgrade it using Wordpress automatic upgrade in Wordpress admin >> Plugins page.

IMPORTANT: Never upgrade WassUp when your site is busy. Interrupting WassUp in the midst of recording visitor data could cause a permanent lock or corruption of WassUp table that prevents any further recording.  If your site is always busy, I recommend that you temporarily "disable recording" in WassUp >> Wassup-Options, [Statistics Recording] Tab, beforehand, or put your site into "maintenance mode" prior to WassUp upgrade. 

= Usage: =

When you activate this plugin (as described in "Installation"), it works "as is". You don't have anything to do. Wait for visitors to hit your site and start seeing details (click the dashboard and go to WassUp page)

IMPORTANT: WassUp is incompatible with page-based caching plugins such as "WP Super-Cache". 

== Changelog ==

= 1.8.1 =
= Urgent bugfix and code improvements upgrade =
* fixed `set_time_limit` warning triggered when PHP is in safe_mode.
* fixed `preg.match` compilation warning in 'wGetSpamRef' function.
* disabled display of PHP warnings and other non-fatal errors within WassUp tracker function.
* added important new upgrade information to `readme.txt` Installation instructions.
* added a time period to "top ten" report heading.
* minor style changes in wassup css.
 
= 1.8 =
= Important compatibility, feature and performance improvement upgrade =
* new table, "wassup_meta", for data caching and extended tracking.
* new web service, [freegeoip.net](http://freegeoip.net), for IP Geolocation. Thanks to [@AlexandreFiori](http://twitter.com/alexandrefiori) for giving us access to his API.
* updated admin interface and menus.
* updated browser, OS, and search engine detection.
* improved security and performance.
* improved compatibility with Wordpress 3.0-3.0.1 and security plugins. 
  NOTE: Before installing "Wordpress Firewall", existing Wassup users must wait 2 full days after completing the upgrade to 1.8 for the old 'wassup_screen_res' cookie to expire in returning visitor's browsers.
* miscellaneous code improvements and bug fixes.

= 1.7.2.1 =
= Critical security and bug fix upgrade =
* disabled page reload triggered by WassUp screen resolution tracking.
* fixed a security loophole found in main.php module.

= 1.7.2 =
= Important feature and performance improvement upgrade =
* new clickable refresh timer in "Visitor Details" submenu.
* initial sample record added to WassUp table for new installs.
* improved browser, OS, and search engine detection.
* code changes for better Wordpress integration.
* WassUp Widget localized for language translation.
* more language translations added.

...
== Upgrade Notice ==

= 1.8.1 =
* Urgent bug fix upgrade plus code improvements. This upgrade is required for WassUp 1.8 users. Read plugin `Installation` instructions for important new upgrade information.

== Infos ==

You could find every informations and much more at [http://www.wpwp.org](http://www.wpwp.org) - [http://trac.wpwp.org](http://trac.wpwp.org/wiki/Documentation) - [http://www.wpwp.org/forums](http://www.wpwp.org/forums/)

Credits to: [Jquery](http://www.jquery.com) for the amazing Ajax framework, [FAMFAMFAM](http://www.famfamfam.com/) for the flags icons and a big thanks to [Helene D.](http://www.techfromhel.com/) for her help to improve WassUp!

