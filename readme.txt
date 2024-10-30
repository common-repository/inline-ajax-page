=== Inline Ajax Page ===
Contributors: Aaron Harun
Donate link: http://anthologyoi.com/about/donate/
Tags: ajax, inline posts, inline comments, add comment, ajax pages, posts, comments, comment
Requires at least: 2.1
Tested up to: 2.2.1
Stable tag: 2.4.7

INAP uses AJAX to replace excerpts with the full posts, paginate posts, load comments and submit comments. Comes with a extremely powerful Admin Panel.

== Description ==

_This plugin has had over 30,000 views and 10,000 Downloads since March 16, 2007._

_Thank You all for your support!_

_However, INAP has been retired and has been replaced by [AJAXed Wordpress](http://anthologyoi.com/awp)._

_INAP is no longer supported._


== Old Information ==
Inline Ajax Page (INAP) is an extremely powerful plugin that allows you to harness the power of AJAX to improve your user's experience. INAP is not only able to load posts, comments and the add comment box inline, but can also submit comments, paginate posts, paginate your homepage, display a live comment preview. Other than a few minor theme edits when you first install the plugin, nothing has to be changed to test the power of INAP. All options can be controlled directly from the Administration Panel which allows you to customize nearly every aspect of INAP.


The plugin displays an excerpt of your post (the same way Wordpress does when you use the_excerpt or  <!–more–> tag), but rather than making the user go to another page to read the remainder of the post, this plugin uses Ajax to download and display the rest of it when the user clicks a link to read more. Afterwards the post’s content can then be hidden and the user can continue reading your other posts. It uses a similar method to display comments and the add comment box and to submit comments. 

== Install ==

Basic Installation (If you are new to Wordpress you should probably use this.):

1. Download INAP, unzip and upload the INAP folder and its contents to your Wordpress plugins folder (/wp-content/plugins/), and activate the plugin in Wordpress.
1. To display posts in-line Open the Admin Panel and Check the box that says “Use Options” and then check the box that says “Simple Posts” in the Admin panel.
1. Open your index.php (or post.php depending on your theme) in your themes folder.
1. To display comments in-line add `<?php do_action('inap_comments'); ?>` where you want the comments to appear when they are loaded and add `<?php do_action('inap_comments_link');?>` where you want the show/hide comments link to appear.
1. To display the add comment box in-line add `<?php do_action('inap_addcomments'); ?>` where you want the add comment box to appear and add `<?php do_action('inap_addcomments_link');?>` where you want the show/hide add comment box link to appear.
1. Save the file and upload it to your current theme folder. This process can be repeated with your theme's single.php, page.php etc.

If you are upgading from a version prior to 2.4 you must first restore default options in the admin panel before continuing.
== Setup Examples ==

These examples will help guide you through setting options in the Admin panel, but most settings in the Admin panel give a detailed description.

* To display the post as a long excerpt(split mode 3 with a 200 word limit), but treats the post as content and removes the excerpt. It also uses the title of the post in the continue reading link. In the Admin panel:
1. Set the Show Text to: Continue Reading “%title%”
1. Select Split Preview by Word Count.
1. Set Maximum number of words to 200.
1. Check the Strip Excerpt option
* To have your posts display the way I do:
1. Check Simple Posts
1. Set your Show text for posts to: Continue reading “%title”
1. Set your Hide text for posts to : hide “%title”
1. Set Split Preview to "by word count"
1. Check Show HTML
1. Set your Post Effect to ScrollLeft
