<?php
	if ($_POST["action"] == "saveconfiguration") {
		if($_POST["inap_test"] == 1){
			INAP::update_options($_REQUEST['inap']);
			update_option('inap_test',$inapall);
			$message .= 'INAP test options updated. These settings will not go live until you save them.<br/>';
		}elseif($_POST["inap_test"] == 2){
			update_option('inap_test','');
			$message .= 'INAP test options deleted.<br/>';
			$inapall = get_option('inap');
		}else{
			INAP::update_options($_REQUEST['inap']);
			update_option('inap',$inapall);
			update_option('inap_test','');
			$message .= 'INAP options updated.<br/>';
		}
	}elseif($_POST["action"] == "restoredefaults"){

		$inapall = '';
		INAP::set_defaults();
		$message .= 'INAP settings restored to defaults. <br/>';

	}elseif($_POST["action"] == "restoreupdate" && $_POST['resop']){

		$options = trim($_POST['resop']);
		if( get_magic_quotes_gpc() ) {
			$options = trim(stripslashes($options));
		}
		$options = preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $options );
		$options = unserialize($options);
		$inapall = '';
		if(is_array($options)){
			INAP::update_options($options);
			update_option('inap_test',$inapall);
			$message .= 'Options successfully saved as test options. Please review them for consistancy before saving. <br />';
		}

	}

	$inap_test = get_option('inap_test');

	if(is_array($inap_test)){
		$inapall = $inap_test;
		$is_test= true;
		$message .= 'You are using the test options currently. You must save them as the live options or delete them to see the current live options. <br/> To view these settings append ?inap=test to any URL in your blog or click <a href="'.get_settings('siteurl').'?inap=test">here</a to view your homepage> ';
	}

	if($inapall['comment_template'] == '1'){
		$temp1 = 'checked="checked"'; 
	}elseif($inapall['comment_template'] == '2'){
		$temp2 = 'checked="checked"'; 
	}else{
		$temp0 = 'checked="checked"'; 
	}
	if($inapall['split_mode'] == '3'){
		$sm3 = 'checked="checked"'; 
	}elseif($inapall['split_mode'] == '2'){
		$sm2 = 'checked="checked"'; 
	}else{
		$sm1 = 'checked="checked"'; 
	}
	if($inapall['split_comments'] == '3'){
		$sc3 = 'checked="checked"';
	}elseif($inapall['split_comments'] == '2'){
		$sc2 = 'checked="checked"';
	}else{
		$sc1 = 'checked="checked"';
	}

	if($inapall['js_library'] == '3' || ($inapall['use_prototype'] == 1 && !$inapall['js_library'])){
		$jl3 = 'checked="checked"';
	}elseif($inapall['js_library'] == '2'){
		$jl2 = 'checked="checked"';
	}else{
		$jl1 = 'checked="checked"';
	}

	if($inapall['paginate_max_words_para'] == ''){
		if($sm2)
			$inapall['paginate_max_words_para'] = $inapall['line_break'];
		if($sm3)
			$inapall['paginate_max_words_para'] = $inapall['word_limit'];
	}
	if($inapall['max_words_para'] == ''){
		if($sm2)
			$inapall['max_words_para'] = $inapall['line_break'];
		if($sm3)
			$inapall['max_words_para'] = $inapall['word_limit'];
	}


		$texts = array('link_show_text','link_hide_text','link_embed_show_text','link_embed_hide_text','comment_open','comment_hide','addcomment_reply_open','addcomment_reply_hide','comment_tag','comment_all_tag','comment_body_tag','comment_title_tag','addcomment_open','pagelinks','addcomment_hide','live_preview_before','live_preview_after','allow_custom','read_more');

		foreach($texts as $name){
			$inapall[$name]= stripslashes(htmlspecialchars($inapall[$name],ENT_QUOTES));
		}

		$checkboxes = array('strip_excerpt','strip_html','use_fade','simple_posts','simple_comments','special_effects','comment_threaded','show_addcomments_single','show_comments_single','show_addcomments_page','show_comments_page','show_addcomments_home','show_comments_home','trimfeeds','live_preview','live_preview_smilies','live_preview_html','comment_order','inap_pages','hide_child_comments','hide_excerpt','allow_custom','paginate_mode','default_behavior','add_comments_tags','posts_off','comments_off','nav_off','give_credit','go_to_post','hide_old_child_comments');

		foreach($checkboxes as $name){
			if($inapall[$name]==1)
				$$name= 'checked="checked"';
		}
		if($inapall['comment_order'] == 'DESC'){ $comment_order= 'checked="checked"';}
if($message){
echo '<div class="updated"><p><strong>'.$message;
echo '</strong></p></div>';
}
?>

<script type="text/javascript" src="../wp-includes/js/dbx.js"></script>
<script type="text/javascript" src="../wp-includes/js/tw-sack.js"></script>
<script type="text/javascript">
				//<![CDATA[
				addLoadEvent( function() {
					var manager = new dbxManager('inap');
					
					//create new docking boxes group
					var advanced = new dbxGroup(
						'advancedstuff', 		// container ID [/-_a-zA-Z0-9/]
						'vertical', 		// orientation ['vertical'|'horizontal']
						'10', 			// drag threshold ['n' pixels]
						'yes',			// restrict drag movement to container axis ['yes'|'no']
						'0', 			// animate re-ordering [frames per transition, or '0' for no effect]
						'yes', 			// include open/close toggle buttons ['yes'|'no']
						'open', 		// default state ['open'|'closed']
						'open', 		// word for "open", as in "open this box"
						'close', 		// word for "close", as in "close this box"
						'click-down and drag to move this box', // sentence for "move this box" by mouse
						'click to %toggle% this box', // pattern-match sentence for "(open|close) this box" by mouse
						'use the arrow keys to move this box', // sentence for "move this box" by keyboard
						', or press the enter key to %toggle% it',  // pattern-match sentence-fragment for "(open|close) this box" by keyboard
						'%mytitle%  [%dbxtitle%]' // pattern-match syntax for title-attribute conflicts
						);
				});

		function toggle_help(id){
			var div = document.getElementById(id);
			if(div){
				if(div.style.display == 'none'){
					div.style.display = 'block';
				}else{
					div.style.display = 'none';
				}
			}
		}
//]]>

</script>
<style>
/* Second menu on the header */

ul#examplemenu {
margin-left: 0;
padding-left: 0;
white-space: nowrap;
}

#examplemenu li{
display: inline;
list-style-type: none;
margin:0 !important;
padding:0 !important;
}

#examplemenu a {
padding: 3px 10px;
border: solid 1px #FFF ;
	background: #467aa7 ;
}

#examplemenu a:link{
color: #fff;
text-decoration: none;
}

#examplemenu a:hover{
color: #fff;
background-color: #578bb8;
text-decoration: none;
font-weight:bold;
}

li * span{
border:1px solid silver;
background-color:#fafbfc;
color:#000;
width:95%;
line-height:1.5em;
margin:5px;
padding:5px;
}
 ul.def li{
display:block;
border:1px solid #BBBBBB;
background-color:#F0F8FF;
color:#000000;
margin:5px;
padding:5px;
}
 ul.def li.important{
border:2px solid #000;
}
}
ul.def li.importish{
border:2px solid #BBBBBB;
}


</style>
<div class="wrap">
<form method="post">
<h2>Edit Inap Configuration</h2>

<div id="advancedstuff" class="dbx-group" >
					
<div class="dbx-b-ox-wrapper">
	<fieldset id="overall-options-<?php echo date('W',time());?>" class="dbx-box">
		<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle"><?php _e('Overall Options','inap');?></h3></div>
			<div class="dbx-c-ontent-wrapper">
				<div class="dbx-content">
<p>Black bordered options are the most important.</p>
<div style="width:45%; float:left;">
<ul class="def"><li>
<input type="checkbox" value="1" <?php echo $posts_off;?> name="inap[posts_off]"> &laquo;&mdash; <?php _e('Disable all Post functionality?','inap');?><br />
<input type="checkbox" value="1" <?php echo $comment_off;?> name="inap[comments_off]"> &laquo;&mdash; <?php _e('Disable all Comment functionality?','inap');?><br />
<input type="checkbox" value="1" <?php echo $nav_off;?> name="inap[nav_off]"> &laquo;&mdash; <?php _e('Disable all navigation functionality?','inap');?>
</li></ul>
<ul class="def">
    <li class="important"><p>
		<input type="checkbox" value="1" <?php echo $simple_posts;?> name="inap[simple_posts]"> &laquo;&mdash; <?php _e('Use Simple Posts?','inap');?><a href="#" onclick="toggle_help('simple_posts'); return false;">[?]</a><span style="display:none;" id="simple_posts"><?php _e('"Simple Posts" allows you to continue using the_content and the_excerpt in your theme file, but the plugin will work as if you edited your theme files. This will also filter anything that applies the content and excerpt filters. This <em>may</em> cause undesirable effects with certain plugins; if you have any problems with it, you may want to edit your files as described in the instructions. ','inap');?></span>
   </p></li>
    <li><p>
		<input type="checkbox" value="1" <?php echo $simple_comments;?> name="inap[simple_comments]"> &laquo;&mdash; <?php _e('Use Simple Comments?','inap');?><a href="#" onclick="toggle_help('simple_comments'); return false;">[?]</a><span style="display:none;" id="simple_comments"><?php _e('Simple Comments will automatically replace comments_templates with the INAP comments. On single pages it replaces inap_comments with the open by default item checked; however, it does not work on the home page, nor does it show the show/hide link.','inap');?><br />
<?php _e('This options is not as powerful as simple posts, and will require that you still edit the theme files.','inap');?></span>
   </p></li>
    <li><p>
		<input type="checkbox" value="1" <?php echo $allow_custom;?> name="inap[allow_custom]"> &laquo;&mdash; <?php _e('Allow custom options?','inap');?><a href="#" onclick="toggle_help('allow_custom'); return false;">[?]</a><span style="display:none;" id="allow_custom"><?php _e('Occasionally certain posts will not fit with the flow of the rest of the site and don\'t fit with the general options used. Selecting the following option will allow you to use custom options on a post-by-post basis.','inap');?></span>
   </p></li>
    <li><p>
		<input type="checkbox" value="1" <?php echo $trimfeeds;?> name="inap[trimfeeds]"> &laquo;&mdash; <?php _e('Trm feeds also?','inap');?><a href="#" onclick="toggle_help('allow_custom'); return false;">[?]</a><span style="display:none;" id="allow_custom"><?php _e('If you do not use full post feeds, INAP can create an excerpt that is the same as the excerpt that would be used on your main page.','inap');?></span>
   </p></li>
</ul>
</div>
<div style="width:49%; border-left:2px black solid; padding-left: 2%; float:left; ">
<p>
This plugin is about 120k characters long, has over 3000 words of documentation, and I have spent over 250 hours to program, maintain, and triage it. Have you found In-line Ajax Page useful? If so, isn't it worth a little bit of time or money?
</p>
 <strong>Even a small donation helps. </strong>(You wouldn't beleive how much it takes to keep one of these grasshoppers fed.) But if circumstances make a monetary donation impossible, <em>links, referrals and comments are appreciated</em>.<br/>
      <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_xclick&business=admin%40anthologyoi%2ecom&item_name=INAP_Donation&no_shipping=0&cn=Optional%20Notes&tax=0&currency_code=USD&lc=US&bn=PP%2dDonationsBF&charset=UTF%2d8"><img src='http://anthologyoi.com/wp-content/uploads/2007/04/lame_bug_paypal.png' alt='Donate With Paypal' /></a> <a href="http://www.amazon.com/gp/registry/wishlist/2RL5QA7UD1SK4/"><img src='http://anthologyoi.com/wp-content/uploads/2007/04/lame_bug_wishlist.png' title="Used is as good as new." alt='Donate from my Amazon Wish List' /></a>				<!--<a href="http://zme.amazon.com/exec/varzea/pay/T2E6ERM3N3HNG4"><img src='http://anthologyoi.com/wp-content/uploads/2007/04/lame_bug_amazon.png' alt='Donate with Amazon Honor System' /></a>-->
<br/>
Or if you can't donate money selecting the following checkbox will append a small link into your wordpress footer to AnthologyOI.com, please consider using it. <input type="checkbox" value="1" <?php echo $give_credit;?> name="inap[give_credit]">
</div>
<?php require_once(ABSPATH . WPINC . '/rss.php');?>
<?php get_rss ('http://lo/aoiv2/?post_name=inline-ajax-page-changelog',5);?>
</div>
		</div>
	</fieldset>
</div>

<div class="dbx-b-ox-wrapper">
	<fieldset id="post-options" class="dbx-box">
		<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle"><?php _e('Post Options','inap');?></h3></div>
			<div class="dbx-c-ontent-wrapper">
				<div class="dbx-content">

 The following texts are the default texts displayed when a post has more than one page. You can use the tags %title, %author, %date, %categories, %count (comment count) and %time to show their respective data in the following textboxes.
<ul class="def">
    <li class="important"><p>
		<?php _e('Show text (for posts): ','inap');?><input type="text" value="<?php echo $inapall['link_show_text'];?>" name="inap[link_show_text]">
		<?php _e('Hide text (for posts): ','inap');?><input type="text" value="<?php echo $inapall['link_hide_text'];?>" name="inap[link_hide_text]">
   </p></li>
    <li><p>
		<?php _e('Show text (for embedded posts): ','inap');?>	<input type="text" value="<?php echo $inapall['link_embed_show_text'];?>" name="inap[link_embed_show_text]">
		<?php _e('Hide text (for embedded posts): ','inap');?> <input type="text" value="<?php echo $inapall['link_embed_hide_text'];?>" name="inap[link_embed_hide_text]">
   </p></li>
<li><p>
	<input type="checkbox" value="1" <?php echo $go_to_post;?> name="inap[go_to_post]"> &laquo;&mdash; <?php _e('Automatically use "go straight to post" link?','inap');?><a href="#" onclick="toggle_help('go_to_post'); return false;">[?]</a><span style="display:none;" id="go_to_post"><?php _e('','inap');?>This will automatically append a "go straight to post" link that does not trigger the show/hide javascript after the show/hide links on a post. If this checkbox is not checked you can still manually add a "go straight to post" link by using &lt;?php do_action('go_to_post');?&gt; in your template.</span>
	<br/> <?php _e('Go straight to post text: ','inap');?> <input type="text" value="<?php echo $inapall['read_more'];?>" name="inap[read_more]">
</p>
</li>
</ul>
<ul class="def">
	<li class="important"><p><?php _e('Use the following method to create excerpts or pages.','inap');?><br/>
		<label><input type="radio" value="1" <?php echo $sm1;?> name="inap[split_mode]"/> &laquo;&mdash; <?php _e('Only on more or nextpage tags.','inap');?></label><a href="#" onclick="toggle_help('split_mode1'); return false;">[?]</a><span style="display:none;" id="split_mode1">This option will only split a post when you specifically set a &lt;!--more--&gt; or &lt;!--nextpage--&gt; tag.</span><br/>
		<label> <input type="radio" value ="2" <?php echo $sm2;?> name="inap[split_mode]"/> &laquo;&mdash; <?php _e('By paragraphs (unless more tag): ','inap');?></label><a href="#" onclick="toggle_help('split_mode2'); return false;">[?]</a><span style="display:none;" id="split_mode2">This is a dumb method, so its use isn't recommended because it does not differentiate between a 10 word paragraph and a 400 word one.  By default a more tag will always overrule this option.</span><br/>
		<label><input type="radio" value ="3" <?php echo $sm3;?> name="inap[split_mode]"/> &laquo;&mdash; <?php _e('By word count ','inap');?></label><a href="#" onclick="toggle_help('split_mode3'); return false;">[?]</a><span style="display:none;" id="split_mode3">This is you best option to have a uniform look and feel. By default a more tag will always overrule this option.</span><br/>

		<?php _e('Show ','inap');?>
		<input type="text" size="4" value="<?php echo $inapall['max_words_para'];?>" name="inap[max_words_para]"> &laquo;&mdash; <?php _e('words or paragraphs.','inap');?><a href="#" onclick="toggle_help('max_words_para'); return false;">[?]</a><span style="display:none;" id="max_words_para">Whether this is used to determine paragraphs or words will depend on your split mode, so if you change your split mode don't forget to change this also.</span>
   </p></li>
    <li><p>
		<input type="checkbox" value="1" <?php echo $strip_excerpt;?> name="inap[strip_excerpt]"> &laquo;&mdash; <?php _e('Strip excerpt?','inap');?>   <a href="#" onclick="toggle_help('strip_excerpt'); return false;">[?]</a><span style="display:none;" id="strip_excerpt">If you have a simple show/hide without pages it will remove the excerpt from the second page. It has no effect when you specifically set an excerpt nor when using pagination.</span></p></li>
    <li><p>
		<input type="checkbox" value="1" <?php echo $hide_excerpt;?> name="inap[hide_excerpt]"> &laquo;&mdash; <?php _e('Hide default excerpt? ','inap');?>
    <a href="#" onclick="toggle_help('hide_excerpt'); return false;">[?]</a><span style="display:none;" id="hide_excerpt">Normally, INAP automatically uses a explicitly set excerpt, if it exists; selecting this option disables this behavior.</span></p></li>
	<li><p>
		<input type="checkbox" value="1" <?php echo $strip_html;?> name="inap[strip_html]"> &laquo;&mdash; <?php _e('Strip HTML in preview?','inap');?>
    <a href="#" onclick="toggle_help('strip_html'); return false;">[?]</a><span style="display:none;" id="strip_html"> If HTML is stripped, links, text decoration, images and all other HTML will be removed, and may cause for mating errors or other undesired behavior.</span></p></li>
</ul>
<!--<?php _e('The following option does not effect this plugin directly, but is used to allow other plugins to interact with the post. Unless you know that a plugin you use requires that posts be filtered as excerpts, you should always leave this option unchecked.','inap');?><ul class="def"><li><p><?php _e('Filter post preview as','inap');?>
	<label><input type="check" value="excerpt" <?php echo $default_behavior;?> name="inap[default_behavior]"/> &laquo;&mdash; <?php _e('an excerpt rather than as content.','inap');?> </label>
<a href="#" onclick="toggle_help(''); return false;">[?]</a><span style="display:none;" id=""></span></p></li></ul>-->

<ul class="def">
    <li><p>
		<input type="checkbox" value="1" <?php echo $inap_pages;?> name="inap[inap_pages]"> &laquo;&mdash; <?php _e('Use inap_pages function for post show/hide links?','inap');?>
    <a href="#" onclick="toggle_help('inap_pages'); return false;">[?]</a><span style="display:none;" id="inap_pages"> If this option is checked no link will be shown unless you add do_action(\'inap_pages\'); or inap_title() inside your theme. Not recommended for beginners.</span></p></li>
</ul>

By default INAP only creates two pages, a short preview and everything else; however, the pagination option allows you to create additional pages to further split the post.
<ul class="def">
    <li><p>
		<label><input type="checkbox" value="1" <?php echo $paginate_mode;?> name="inap[paginate_mode]"/> &laquo;&mdash;<?php _e('Use Pagination? ','inap');?></label>
		<?php _e(' Each page after the excerpt should be ','inap');?>
		<input type="text" size="4" value="<?php echo $inapall['paginate_max_words_para'];?>" name="inap[paginate_word_limit]"> <?php _e(' words or paragraphs (based on the split mode) long.','inap');?>
    <a href="#" onclick="toggle_help('paginate_mode'); return false;">[?]</a><span style="display:none;" style="display:none;" id="paginate_mode"> Pagination will further divide a large post into smaller chunks, so after the excerpt the rest of the post is also paged.</span></p></li>
</ul>
<?php _e('The following options will style your page links (if you have any). Each may contain XHTML','inap');?>
<ul class="def">
    <li>Combined the example options with CSS styling yield: Go to <ul id="examplemenu" style="display:inline !important;"> <li><a>Page 1 </a></li><li><a>Page 2 </a></li><li><a>Page 3 </a></li></ul><p>
		<input type="text" size="4" value="<?php echo $inapall['beforepages'];?>" name="inap[beforepages]"> &laquo;&mdash; <?php _e(' Before page list.','inap');?><a href="#" onclick="toggle_help('beforepages'); return false;">[?]</a>

		<input type="text" size="4" value="<?php echo $inapall['beforepage'];?>" name="inap[beforepage]"> &laquo;&mdash; <?php _e('Before individual page links.','inap');?><a href="#" onclick="toggle_help('beforepage'); return false;">[?]</a>

	<input type="text" size="4" value="<?php echo $inapall['pagelinks'];?>" name="inap[pagelinks]"> &laquo;&mdash; <?php _e('Before individual page links.','inap');?><a href="#" onclick="toggle_help('pagelinks'); return false;">[?]</a>

<span style="display:none;" id="beforepages">(Go to &lt;ul&gt;)</span>
<span style="display:none;" id="beforepage">(&lt;li&gt;)</span>
<span style="display:none;" id="pagelinks">A % sign will be replaced with the page number (Page % )</span>

</p>
<p>
		<input type="text" size="4" value="<?php echo $inapall['afterpage'];?>" name="inap[afterpage]"> &laquo;&mdash; <?php _e('After individual page links','inap');?><a href="#" onclick="toggle_help('afterpage'); return false;">[?]</a>

		<input type="text" size="4" value="<?php echo $inapall['page_sep'];?>" name="inap[page_sep]"> &laquo;&mdash; <?php _e('Separator between individual page links','inap');?>

		<input type="text" size="4" value="<?php echo $inapall['afterpages'];?>" name="inap[afterpages]"> &laquo;&mdash; <?php _e('After the page list','inap');?><a href="#" onclick="toggle_help('afterpages'); return false;">[?]</a>
<span style="display:none;" id="afterpage">(&lt;li&gt;)</span>
<span style="display:none;" id="afterpages">(&lt;/ul&gt;)</span>
</p></li>
			</div>
		</div>
	</fieldset>
</div>

<div class="dbx-b-ox-wrapper">
	<fieldset id="comment-options" class="dbx-box">
		<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle"><?php _e('Comment Options','inap');?></h3></div>
			<div class="dbx-c-ontent-wrapper">
				<div class="dbx-content">
The following texts are for comments. You can use the tags %title, %author, %date, and %time to show their respective data in the following textboxes.
    <ul class="def">
 <li class="important"><p>
		<?php _e('Show text:','inap');?>
		<input type="text" value="<?php echo $inapall['comment_open'];?>" name="inap[comment_open]"> 
		<?php _e('Hide text:','inap');?>
		<input type="text" value="<?php echo $inapall['comment_hide'];?>" name="inap[comment_hide]">
 </p></li>
  <li class="important"><p>
		<?php _e('No Comments Text:','inap');?>
		<input type="text" value="<?php echo $inapall['no_comments'];?>" name="inap[no_comments]"> 
		<?php _e('Comments are Closed text:','inap');?>
		<input type="text" value="<?php echo $inapall['closed_comments'];?>" name="inap[closed_comments]"> 
</p></li>
</ul>
<?php _e('On pages and single posts you may have the comments open by default. This will allow the user to refresh the comments and post using AJAX, but allows search engines and users without Javascript to see the comments by default.','inap');?>
<ul class="def">
 <li class="important"><p>
		<input type="checkbox" value="1" <?php echo $show_comments_single;?> name="inap[show_comments_single]">&laquo;&mdash; <?php _e('Have Comments open by default on single post pages?','inap');?><br/>
		<input type="checkbox" value="1" <?php echo $show_comments_page;?> name="inap[show_comments_page]">&laquo;&mdash; <?php _e('Have Comments open by default on pages?','inap');?></p></li>
			</ul>
<?php _e("Comments work best when used with INAP's default template; however it can be changed with some restrictions:if you set it to use the theme's default comment template then the add comment box will be disabled and the show/hide comments link will work for both add comments and comments, and you will no longer be able to use AJAX comments.",'inap');?>
<ul class="def">
 <li><p>
<label><input type="radio" value="0" <?php echo $temp0;?> name="inap[comment_template]"/> &laquo;&mdash; <?php _e('Use the default template for INAP that is built into the plugin.','inap');?> </label><a href="#" onclick="toggle_help('comment_template1'); return false;">[?]</a><span style="display:none;" id="comment_template1"> (This can be customized below.)</span><br/>
<label><input type="radio" value ="1" <?php echo $temp1;?> name="inap[comment_template]"/> &laquo;&mdash; <?php _e('Use your themes default comment template. ','inap');?></label> <a href="#" onclick="toggle_help('comment_template2'); return false;">[?]</a><span style="display:none;" id="comment_template2">(must be named comments.php and be a standard template)</span><br/>
<label><input type="radio" value ="2" <?php echo $temp2;?> name="inap[comment_template]"/> &laquo;&mdash; <?php _e('Use a custom inap comment template for your theme. ','inap');?></label> <a href="#" onclick="toggle_help('comment_template3'); return false;">[?]</a><span style="display:none;" id="comment_template3">(This will be named inap-comments.php and must reside inside your default theme folder.)</span></p></li>
</ul>
<?php _e('These options only work if you use the default template that is built into INAP.','inap');?>
<ul class="def">
 <li class="important"><p>

		<input type="checkbox" value="1" <?php echo $comment_threaded;?> name="inap[comment_threaded]">&laquo;&mdash; <?php _e('Use Threaded Comments ','inap');?><?php _e(' and thread comments ','inap');?>	<input type="text" size="4" value="<?php echo $inapall['comment_threaded_depth'];?>" name="inap[comment_threaded_depth]"> &laquo;&mdash;<?php _e(' replies deep. ','inap');?>
 <a href="#" onclick="toggle_help('comment_threaded_depth'); return false;">[?]</a><span style="display:none;" id="comment_threaded_depth">Depth is the number of child replies that will be shown before they are no longer nested inside of each other. (A number of 1 will thread comments, but will show them without nesting which is useful for older posts.)</span></p></li>
 <li><p>
		<input type="checkbox" value="1" <?php echo $hide_child_comments;?> name="inap[hide_child_comments]">&laquo;&mdash; <?php _e('Hide child comments?','inap');?>
 <a href="#" onclick="toggle_help('hide_child_comments'); return false;">[?]</a><span style="display:none;" id="hide_child_comments">if child comments are hidden then the reader will have to click the top parent to load all child comments below it.</span></p></li>
 <li><p>
		<input type="checkbox" value="1" <?php echo $hide_old_child_comments;?> name="inap[hide_old_child_comments]">&laquo;&mdash; <?php _e('Hide child comments more than than ','inap');?> <input type="text" size="4" name="inap[old_comment_days]" value="<?php echo $inapall['old_comment_days'];?>"> days old.
 <a href="#" onclick="toggle_help('hide_old_child_comments'); return false;">[?]</a><span style="display:none;" id="hide_old_child_comments">If the top most parent comment is over a certain numbers of days old, its child comments will be hidden.</span></p></li>
 <li><p>
		<input type="checkbox" value="DESC" <?php echo $comment_order;?> name="inap[comment_order]">&laquo;&mdash; <?php _e('Show newest comments first?','inap');?>
</p></li>
 <li class="importish"><p>
		<?php _e('Separate Pingbacks/trackbacks from comments:','inap');?><br />
		<label><input type="radio" value="1" <?php echo $sc1;?> name="inap[split_comments]"/> &laquo;&mdash; <?php _e(' Do not separate comments from trackbacks.','inap');?></label><br/>
		<label><input type="radio" value ="2" <?php echo $sc2;?> name="inap[split_comments]"/> &laquo;&mdash; <?php _e(' Separate trackbacks from comments. (this will move the trackbacks to the bottom of the comment list.)','inap');?></label><br/>
		<label><input type="radio" value ="3" <?php echo $sc3;?> name="inap[split_comments]"/> &laquo;&mdash; <?php _e(' Do not show Trackbacks (trackbacks will not show up at all when the comments are loaded through INAP.)','inap');?></label>
</p></li>
</ul>
<?php _e("If you need the default template to fit in specifically with your theme, then you may set the specific elements here. Use 'ol' not '&lt;ol&gt;'. This is not the nicest way to do it, but certain classes and IDs have to be used to allow the comments to display correctly.",'inap');?>
<ul class="def">
 <li><p>
		<?php _e('Tag before/after all comments:','inap');?>
		<input type="text" value="<?php echo $inapall['comment_all_tag'];?>" name="inap[comment_all_tag]"><a href="#" onclick="toggle_help('comment_all_tag'); return false;">[?]</a><span style="display:none;" id="comment_all_tag"> Suggested tags for this level: ol or div. To have indentation without using an ordered list (to fit in with your theme.) Use divs and add the following to your style.css file. <code>.post_comments div * > div {padding-left:1em;}</code></span><br/>

		<?php _e('Tag before/after each comment:','inap');?>
		<input type="text" value="<?php echo $inapall['comment_tag'];?>" name="inap[comment_tag]"><a href="#" onclick="toggle_help('comment_tag'); return false;">[?]</a><span style="display:none;" id="comment_tag"> Suggested tags for this level: li or div. </span><br/>

		<?php _e('Tag before/after Title Bar:','inap');?>
		<input type="text" value="<?php echo $inapall['comment_title_tag'];?>" name="inap[comment_title_tag]">
 <a href="#" onclick="toggle_help('comment_title_tag'); return false;">[?]</a><span style="display:none;" id="comment_title_tag">Suggested tags for this level: p, div or span</span></p></li>

<!-- <li><p>
		<?php _e('Tag before/after Body):','inap');?>
		<input type="text" value="<?php echo $inapall['comment_body_tag'];?>" name="inap[comment_body_tag]">&laquo;&mdash; <?php _e("(html tag such as 'div', 'p' or 'span')",'inap');?>
</p></li>-->
</ul>

			</div>
		</div>
	</fieldset>
</div>

<div class="dbx-b-ox-wrapper">
	<fieldset id="add-comments-options" class="dbx-box">
		<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle"><?php _e('Add Comment Box Options','inap');?></h3></div>
			<div class="dbx-c-ontent-wrapper">
				<div class="dbx-content">
    <ul class="def">
 <li class="important"><p>

		<input type="text" value="<?php echo $inapall['addcomment_open'];?>" name="inap[addcomment_open]"> &laquo;&mdash; <?php _e(' Show text (for add comments)','inap');?>
		<input type="text" value="<?php echo $inapall['addcomment_hide'];?>" name="inap[addcomment_hide]"> &laquo;&mdash; <?php _e('Hide text (for add comments)','inap');?>

</p></li>
 <li class="importish"><p>
		<?php _e('Show text for replies to comments:','inap');?>
		<input type="text" value="<?php echo $inapall['addcomment_reply_open'];?>" name="inap[addcomment_reply_open]"> 

		<?php _e('Hide text for replies to comments:','inap');?>
		<input type="text" value="<?php echo $inapall['addcomment_reply_hide'];?>" name="inap[addcomment_reply_hide]"> 
</p></li>
</ul>
<?php _e('If the following options are selected then on all single posts the add comment box will already be open when the page loads, so users don\'t have to look for it. It still can be hidden and moved.','inap');?>
<ul class="def">
 <li class="important"><p>

		<input type="checkbox" value="1" <?php echo $show_addcomments_single;?> name="inap[show_addcomments_single]"> &laquo;&mdash; <?php _e('Have Add Comment Box open by default on single post pages?','inap');?><br/>
		<input type="checkbox" value="1" <?php echo $show_addcomments_page;?> name="inap[show_addcomments_page]"> &laquo;&mdash; <?php _e('Have Add Comment Box open by default on pages?','inap');?>
</p></li>
 <li class="important"><p>
		<input type="checkbox" value="1" <?php echo $add_comments_tags;?> name="inap[add_comments_tags]"> &laquo;&mdash; <?php _e('Show buttons above add comment box to add html tags such as &lt;strong&gt; and &lt;code&gt;','inap');?>
</p></li>
 <li  class="importish"><p>

		<input type="checkbox" value="1" <?php echo $live_preview;?> name="inap[live_preview]"> &laquo;&mdash; <?php _e('Show live preview as user types a comment?','inap');?><br/>
		<input type="checkbox" value="1" <?php echo $live_preview_html;?> name="inap[live_preview_html]"> &laquo;&mdash; <?php _e('Don\'t show any html in the live preview. Kinda defeats the purpose of the live preview.','inap');?><br/>
	Do not display the following tags (leave them as code such as &lt;strong&gt;)<input type="text" value="<?php echo $inapall['live_preview_no_tags'];?>" name="inap[live_preview_no_tags]"> &laquo;&mdash; <?php _e('Use just the tag names separated with spaces. (form table input strong etc.) ','inap');?><br/>
		<input type="checkbox" value="1" <?php echo $live_preview_smilies;?> name="inap[live_preview_smilies]"> &laquo;&mdash; <?php _e('Automatically convert smilies to images? (May cause a small amount of lag for fast typers)','inap');?>
</p>
</li>
</ul>
<?php _e('By default INAP does not style or give a title to the live comment preview.','inap');?>
<ul class="def">
    <li><p>
		<?php _e('Text or XHTML before live comment preview.','inap');?>
		<input type="text" size="35" value="<?php echo $inapall['live_preview_before'];?>" name="inap[live_preview_before]"> &laquo;&mdash; <?php _e('You may use XHTML code or text. Example: "&lt;p>You say:&lt;/p>" ','inap');?><br/>
		<?php _e('Text or XHTML after live comment preview.','inap');?>
		<input type="text" size="35" value="<?php echo $inapall['live_preview_after'];?>" name="inap[live_preview_after]"> &laquo;&mdash; <?php _e('You may use XHTML code or text. ','inap');?>

   </p></li>
</ul>
			</div>
		</div>
	</fieldset>
</div>

<div class="dbx-b-ox-wrapper">
	<fieldset id="effects-options" class="dbx-box">
		<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle"><?php _e('Ajax Options','inap');?> <strong><?php _e('Optional','inap');?></strong></h3></div>
			<div class="dbx-c-ontent-wrapper">
				<div class="dbx-content">

<ul class="def">
 <li class="important"><p>
		<?php _e('Select your JS Library:','inap');?><br />
		<label><input type="radio" value="1" <?php echo $jl1;?> name="inap[js_library]"/> &laquo;&mdash; <?php _e('Use TWSack Library ','inap');?></label><a href="#" onclick="toggle_help('js_library1'); return false;">[?]</a><span style="display:none;" id="js_library1">(5KB, default, no comment or add comment effects)</span><br/>
		<label><input type="radio" value ="2" <?php echo $jl2;?> name="inap[js_library]"/> &laquo;&mdash; <?php _e('Use JQuery ','inap');?></label><a href="#" onclick="toggle_help('js_library2'); return false;">[?]</a><span style="display:none;" id="js_library2">(19KB, includes effects)</span><br/>
		<label><input type="radio" value ="3" <?php echo $jl3;?> name="inap[js_library]"/> &laquo;&mdash; <?php _e('Use Prototype.js ','inap');?></label>
 <a href="#" onclick="toggle_help('js_library3'); return false;">[?]</a><span style="display:none;" id="js_library3">(55KB, Not suggested, needs Scriptaculous for effects )</span></p></li>
			</ul>
			</div>
		</div>
	</fieldset>
</div>
<div class="dbx-b-ox-wrapper">
	<fieldset id="scriptaculous-options" class="dbx-box">
		<div class="dbx-h-andle-wrapper"><h3 class="dbx-handle"><?php _e('Effects Options','inap');?> <strong><?php _e('Optional','inap');?></strong></h3></div>
			<div class="dbx-c-ontent-wrapper">
				<div class="dbx-content">


<?php _e('The following effects are built into the plugin and only work on posts. There is no other way to have effects on posts. These effects will add no extra javascript to your webpage. If you would like another effect added or have an idea for an effect, please make a suggest on the <a href="http://anthologyoi.com/inap/">official INAP support thread</a>.','inap');?>
<ul class="def">
 <li class="important"><p>
		<select name="inap[do_effect]">
			<option selected="selected" value="<?php echo $inapall['do_effect'];?>"><?php echo $inapall['do_effect'];?></option>

			<option value="">None</option>
			<option value="Expand">Expand</option>
			<option value="SlideUp">SlideUp</option>
			<option value="Fade">Fade</option>
			<option value="ScrollLeft">ScrollLeft</option>
		</select> &laquo;&mdash; <?php _e('Which built in Post effect do you want? .','inap');?>
 <a href="#" onclick="toggle_help('do_effect'); return false;">[?]</a><span style="display:none;" id="do_effect">Slide Up scrolls the container up and down, Scroll Left pushes all of the text to the left and pulls it back to the right, Expand stretches and shrinks the text (not the container) horizontally and also vertically (if no child element has a set line height)</span></p></li>
 <li><p>
					<?php _e('What is the background color of your posts?','inap');?>
					<input type="text" value="<?php echo $inapall['background_color'];?>" name="inap[background_color]">
 <a href="#" onclick="toggle_help('background_color'); return false;">[?]</a><span style="display:none;" id="background_color"><?php _e(' (In hex format (#FFFFFF))(YAIEF) Due to a bug in IE you must set a background color for your posts if you want to use the fade effect. This only needs to be set if your post backgrounds aren\'t white.','inap');?></span></p></li>
 </ul>

 <ul class="def">
 <li class="importish"><p>
		<input type="checkbox" value="1" <?php echo $special_effects;?> name="inap[special_effects]"> &laquo;&mdash; <?php _e('Use special effects? ','inap');?>
 <a href="#" onclick="toggle_help('special_effects'); return false;">[?]</a><span style="display:none;" id="special_effects">The ajax library you are using will determine which effects you can use.</span></p></li>
</ul>
<?php _e('The Following options are for the Scriptaculous library and require you to use the Prototype.js Library.','inap');?>
<ul class="def">
 <li><p>Scriptaculous show comments/add comments effect
		<select name="inap[scriptaculous_show]">
			<option selected="selected" value="<?php echo $inapall['scriptaculous_show'];?>"><?php echo $inapall['scriptaculous_show'];?></option>
			<option value="SlideDown">Slide Down</option>
			<option value="BlindDown">Blind Down</option>
			<option value="Appear">Appear</option>
			<option value="Grow">Grow</option>
		</select> and hide effect 

		<select name="inap[scriptaculous_hide]">
			<option selected="selected" value="<?php echo $inapall['scriptaculous_hide'];?>"><?php echo $inapall['scriptaculous_hide'];?></option>
			<option value="SlideUp">Slide Up</option>
			<option value="BlindUp">Blind Up</option>
			<option value="Fade">Fade out</option>
			<option value="Shrink">Shrink</option>
			<option value="Puff">Puff</option>
			<option value="SwitchOff">Switch Off</option>
			<option value="DropOut">Drop Out</option>
			<option value="Squish">Squish</option>
			<option value="Fold">Fold</option>
		</select>
</p></li>
			</ul>
<?php _e('The Following options are for the JQuery library and require you to use the JQuery Library.','inap');?>
<ul class="def">
 <li><p><?php _e('JQuery show comments/add comments effect','inap');?>

		<select name="inap[jquery_show]">
			<option selected="selected" value="<?php echo $inapall['jquery_show'];?>"><?php echo $inapall['jquery_show'];?></option>
			<option value="slideDown-Slow">Slide Down Slow</option>
			<option value="slideDown-Fast">Slide Down Fast</option>
			<option value="slideDown-Normal">Slide Down Normal</option>
			<option value="fadeIn-Slow">Fade In Slow</option>
			<option value="fadeIn-Fast">Fade In Fast</option>
			<option value="fadeIn-Normal">Fade In Normal</option>
			<option value="show-Slow">Show Slow</option>
			<option value="show-Fast">Show Fast</option>
			<option value="show-Normal">Show Normal</option>
		</select>
		and hide effect
		<select name="inap[jquery_hide]">
			<option selected="selected" value="<?php echo $inapall['jquery_hide'];?>"><?php echo $inapall['jquery_hide'];?></option>
			<option value="slideUp-Slow">Slide Up Slow</option>
			<option value="slideUp-Fast">Slide Up Fast</option>
			<option value="slideUp-Normal">Slide Up Normal</option>
			<option value="fadeOut-Slow">Fade Out Slow</option>
			<option value="fadeOut-Fast">Fade Out Fast</option>
			<option value="fadeOut-Normal">Fade Out Normal</option>
			<option value="hide-Slow">Hide Slow</option>
			<option value="hide-Fast">Hide Fast</option>
			<option value="hide-Normal">Hide Normal</option>
		</select>
</p></li>
			</ul>
			</div>
		</div>
	</fieldset>
</div>


</div>



			<p><input type="radio" name="inap_test" <?php if($is_test==true){ echo 'checked="checked"';}?> value="1"> Save these options as a test (will not be publically visible but you won't be able to edit the actual blog settings.)</p>
			<p><input type="radio" name="inap_test" value="2">Delete test settings (will revert admin panel options to the live settings.)</p>
			<p><input type="radio" name="inap_test" <?php if($is_test==FALSE){ echo 'checked="checked"';}?> value="3">Save current settings as live settings (will change publicily visible settings.)</p>
			<input type="hidden" name="action" value="saveconfiguration">
			<input type="submit" value="Save" style="width:100%;" >
		</form>
<br /><br />
<ul  class="def"><li>
<a href="#" onclick="toggle_help('restore'); return false;">Show Restore default settings button</a>
<div style="display:none;" id="restore">
<form method="post">
<input type="hidden" name="action" value="restoredefaults">
<ul class="def">
<li>
<p><input type="checkbox" name="restore" value="1"> Confirm restore? <input type="submit" value="Restore Settings" style="width:50%;" ></p>
</li>
</ul>

</form>
</div>
</li><li>
<a href="#" onclick="toggle_help('inap_options'); return false;">Show all current saved INAP options</a><span style="display:none;" id="inap_options">The following are your INAP <em>TEST</em> options:
		<?php
			$options  = get_option('inap_test');
			$save = array();
			if(is_array($options)){
				while(list($key, $val) = each($options)){
					if($val) //anything not set won't need to be reset;
      					$save[$key] = stripslashes(htmlspecialchars($val,ENT_QUOTES));
				}
					echo '<textarea style="width:75%;">'.htmlentities(serialize($save),ENT_QUOTES).'</textarea>';
			}
		?>

</p>
<p>The following are your INAP <em>SAVED</em> options: <br/>
		<?php
			$options  = get_option('inap');
			$save = array();
			if(is_array($options)){
				while(list($key, $val) = each($options)){
					if($val) //anything not set won't need to be reset;
           $save[$key] = stripslashes(htmlspecialchars($val,ENT_QUOTES));
				}
					echo '<textarea style="width:75%;">'.htmlentities(serialize($save),ENT_QUOTES).'</textarea>';
			}
		?>
</p></span>

</li>

<li>
<a href="#" onclick="toggle_help('restorefrmsite'); return false;">Restore options from a backup. </a>
<div style="display:none;" id="restorefrmsite">(Will be stored as test options, you will have to manually save them as regular options.)
<form method="post">
<input type="hidden" name="action" value="restoreupdate">
<ul class="def">
<li>
<p><textarea style="width:100%;" name="resop"></textarea> </p>
<p><input type="submit" value="Revert Settings" style="width:50%;" ></p>
</li>
</ul>
</form>
</div>

</li>
</ul>
</div>