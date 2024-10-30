<?php
	/*
	Plugin Name: Inline Ajax Page
	Plugin URI: http://anthologyoi.com/inap/
	Description: A simple to use plugin that allows you to use ajax to retrieve post and comment contents as well as the add comment box in-line for your users. This plugin also submits the comment box using AJAX. For those that don't have javascript it displays links as usual. This plugin does not require that you set any options, but is almost infinitely customizable. <strong>Admin panel is under presentation tab.</strong> Need Help? <a href="http://anthologyoi.com/inap/">Visit the Official INAP support thread</a> or try reading <a href="http://anthologyoi.com/inap/inline-ajax-page-readme/">the full documentation</a>.
	Author: Aaron Harun
	Version: 2.4.7
	Author URI: http://anthologyoi.com/

	Liscense:
	This Plugin is free for use, and may be modified, but may not be included in any bundled download without appropriate credits and links on the download page, nor may it or its derivitive works be distributed in such a way that it creates a profit. A commerical license removes these restrictions.

	*/

	//# Set everything up#//
$inap_integration='';
	$inapall = INAP::start_up();
	add_action('init', array('INAP','init'));

	if(!strpos($_SERVER['PHP_SELF'], '/wp-admin/')){
		if(!$inapall['posts_off']){
			$inaped_posts = array();
			INAP::post_filters();
			include('inap-functions-posts.php');
		}

		if(!$inapall['comments_off']){
			$inap_link_count = array();
			INAP::comment_filters();
			INAP::default_options();
			include('inap-functions-comments.php');

		}
		if(!$inapall['nav_off']){
			include('inap-functions-nav.php');
		}
	}

	$inap_disabled_posts = get_option('inap_disabled_posts');

	if($inapall['allow_custom'] == 1){
		$inap_use_custom = get_option('inap_use_custom');
	}


class INAP{

	function start_up(){
		$options = array();

		if($_GET['inap'] == 'test'){
			$options = get_option('inap_test');
		}else{
			$options = get_option('inap');
		}

		if(!$options){
			INAP::set_defaults();
		}

	return $options;
	}

	function init() {
		global $inapall;
		$currentLocale = get_locale();
		if(!empty($currentLocale)) {
			$moFile = dirname(__FILE__) . "/inap-" . $currentLocale . ".mo";
			if(@file_exists($moFile) && is_readable($moFile)) load_textdomain('inap', $moFile);
		}


		add_action('admin_menu', array('INAP','menu'));
		add_action('wp_head', array('INAP','print_header'));
		add_filter('dbx_page_advanced', array('INAP', 'customUI'));
		add_filter('dbx_post_advanced', array('INAP', 'customUI'));
		add_filter('publish_post', array('INAP', 'update_custom'));
		add_filter('save_post',    array('INAP', 'update_custom'));
		if($inapall['give_credit'])
			add_action('wp_footer',array('INAP','give_credit'));
		INAP::integrate();
	}

	function integrate(){
	global $inap_integration;
		//Fix a conflict with Postviews Plus
		if(function_exists('process_postviews')){
			remove_action('the_content', 'process_postviews');
			add_action('loop_start', 'process_postviews');
		}

		if(class_exists('identicon'))
			$inap_integration['identicon']=new identicon;
	}

	function give_credit(){
		echo '<a href="http://anthologyoi.com/" rel="external">AJAXed with INAP</a>';

	}

	function post_filters(){
	global $inapall;

		if($inapall['simple_posts'] == 1){
			add_filter('the_content', array('INAP_Post','filter'),-10,1);
			add_filter('the_excerpt', array('INAP_Post','filter'),-10,1);
		}

		add_action('inap_post',array('INAP_Post','inap_post'));
		add_action('inap_pages',array('INAP_Post','pages'));
		add_action('inap_paginate', array('INAP_Post','paginate'));
		add_action('go_to_post',array('INAP_Post','go_to_post'));
		add_action('inap_title', array('INAP_Post','title'));

		add_filter('the_content',array('INAP_Post','list_pages'));
		add_filter('the_excerpt',array('INAP_Post','list_pages'));
		add_filter('the_content',array('INAP_Post','embedded_post_tag'));
		add_filter('the_excerpt',array('INAP_Post','embedded_post_tag'));
	}

	function comment_filters(){
	global $inapall;
		if($inapall['simple_comments'] == 1){
			add_filter('comments_template', array('INAP_comments','filter'),1);
		}
		add_action('inap_comments', array('INAP_comments','div'));
		add_action('inap_comments_link', array('INAP_comments','links'));
		add_action('comment_post', array('INAP_comments','get_parent'));
		add_action('inap_addcomments', array('INAP_addcomments','div'));
		add_action('inap_addcomments_link', array('INAP_addcomments','links'));
		add_action('live_preview', array('INAP_addcomments','live_preview'));
		add_filter('preprocess_comment', array('INAP_addcomments','set_parent'));

	}

	function set_defaults(){
	global $inapall;
		$inapall[comment_open] = 'Show Posts Comments';
		$inapall[comment_hide] = 'Hide Post Comments';
		$inapall[addcomment_open] = 'Add a Comment';
		$inapall[addcomment_hide] = 'Cancel reply';
		$inapall[addcomment_reply_open] = 'Reply to %author';
		$inapall[addcomment_reply_hide] = 'Cancel reply';
		$inapall[closed_comments] = 'Comments are closed';
		$inapall[no_comments] = 'No Comments';
		$inapall[split_mode] = 1;
		$inapall[link_show_text] = 'Click to continue reading "%title"';
		$inapall[link_hide_text] = 'Hide "%title"';
		$inapall[link_embed_show_text] = 'Load "%title"';
		$inapall[link_embed_hide_text] = 'Hide "%title"';
		$inapall[js_library] = 1;
		$inapall[show_comments_single] = 1;
		$inapall[show_comments_page] = 1;
		$inapall[comment_all_tag] = 'div';
		$inapall[comment_tag] =  'div';
		$inapall[comment_title_tag] = 'span';
		$inapall[comment_body_tag] = 'div';
		$inapall[show_addcomments_page] = 1;
		$inapall[show_addcomments_single] = 1;
		$inapall[background_color] = '#FFFFFF';
		$inapall[simple_posts] = 1;
		$inapall[nav_off] = 1;
		$inapall[read_more] = 'Go straight to Post';

		INAP::update_options($inapall);
		update_option('inap',$inapall);
	}

	function default_options(){
	global $inapall;
		//if these options aren't set it can cause trouble wos we make sure they are.
		$inapall['comment_all_tag'] = ($inapall['comment_all_tag'] != '') ? $inapall['comment_all_tag'] : 'div';
		$inapall['comment_tag'] = ($inapall['comment_tag'] != '') ? $inapall['comment_tag'] : 'div';
		$inapall['comment_title_tag'] = ($inapall['comment_title_tag'] != '') ? $inapall['comment_title_tag'] : 'span';
		$inapall['comment_threaded_depth'] = (intval($inapall['comment_threaded_depth']) > 0) ? $inapall['comment_threaded_depth'] : 1;
		$inapall['comment_body_tag'] = ($inapall['comment_body_tag'] != '') ? $inapall['comment_body_tag'] : 'div';
	}

	function get_base(){
   		 return '/'.end(explode('/', str_replace(array('\\','/inap.php'),array('/',''),__FILE__)));
	}

	function print_header(){
	global $inapall,$wp_version;
		$home = get_settings('siteurl');

		if(function_exists('wp_register_script')){

			if($inapall['js_library'] == 3){
				$ajax = 'prototype';
				if($inapall['special_effects'] == 1){
					wp_print_scripts('scriptaculous-effects');
				}
			}elseif($inapall['js_library'] == 2){
				$ajax='jquery';

				if($wp_version == 2.1)
					wp_register_script('jquery', $home.'/wp-includes/js/jquery.js', false, '1');
			}else{
				$ajax='sack';
			}

			if($_GET['inap'] == 'test'){
				wp_register_script('inap-misc', $home.'/wp-content/plugins'.INAP::get_base().'/js/inap-misc-test.js.php', false, '1');
			}else{
				wp_register_script('inap-misc', $home.'/wp-content/plugins'.INAP::get_base().'/js/inap-misc.js.php', false, '1');
			}

			wp_print_scripts(array($ajax, 'inap-misc'));
		}

		if($inapall['do_effect'] == 'Fade'){
			?>
			<style type="text/css">
				.post_content > div{
					zoom:1;
					background-color:<?php echo $inapall[background_color];?>;
				}
			</style>
			<?php
		}
	}

	function process_text($link_text,$pid=''){
	global $id,$post;

		$link_text = stripslashes($link_text);
		if(!$pid){
			$link_text = str_replace('%count', get_comments_number($id),$link_text);
			$link_text = str_replace('%title', get_the_title($id),$link_text);
			$link_text = str_replace('%author',get_the_author(),$link_text);
			$link_text = str_replace('%date',the_date(null,null,null,false),$link_text);
			$link_text = str_replace('%time',get_the_time(),$link_text);
			$link_text = str_replace('%categories',INAP::get_a_category_list(),$link_text);
		}elseif(!is_numeric($pid)){
			$link_text = str_replace('%author',get_comment_author(),$link_text);
			$link_text = str_replace('%date',get_comment_date(),$link_text);
			$link_text = str_replace('%title', get_the_title($id),$link_text);
			$link_text = str_replace('%time',get_comment_time(),$link_text);
		}else{
			$embedded_post = get_post($pid);
			$link_text = str_replace('%count', get_comments_number($pid),$link_text);
			$link_text = str_replace('%title', get_the_title($pid),$link_text);
			$link_text = str_replace('%author',get_author_name($embedded_post->post_author),$link_text);
			$link_text = str_replace('%date', mysql2date(get_option('date_format'), $embedded_post->post_date),$link_text);
			$link_text = str_replace('%time', mysql2date('U', $embedded_post->post_date),$link_text);
			$link_text = str_replace('%categories','',$link_text);
		}

		$link_text = htmlspecialchars($link_text,ENT_QUOTES);

		//The following line is to help make javascript and php play nice.
		$link_text= str_replace("&#039;",'&#8217;',$link_text);

		//We do this otherwise it will double encode things.
		$link_text= str_replace('&#8217;','’',$link_text);
		$link_text= str_replace('&#8220;','“',$link_text);
		$link_text= str_replace('&#8221;','”',$link_text);

	return $link_text;
	}

	
	function get_a_category_list(){
		$categories = get_the_category();

		if (empty($categories))
			return __('Uncategorized');

		$separator = ',';
		$thelist = '';
			$i = 0;
			foreach ( $categories as $category ) {
				if ( 0 < $i )
					$thelist .=', ';
						$thelist .= $category->cat_name;
				++$i;
			}
	return $thelist;
	}


	function menu() {
		add_submenu_page('themes.php', 'INAP Managment', 'INAP Managment', 8,dirname(__FILE__).'/inap_admin.php');
	}

	function update_options($options){
	global $inapall;
		$checkboxes = array('strip_excerpt','strip_html','use_fade','simple_posts','simple_comments','special_effects','comment_threaded','show_addcomments_single','show_comments_single','show_addcomments_page','show_comments_page','show_addcomments_home','show_comments_home','trimfeeds','live_preview','live_preview_smilies','live_preview_html','comment_order','inap_pages','hide_child_comments', 'hide_excerpt','allow_custom','paginate_mode','default_behavior','add_comments_tags','posts_off','comments_off','nav_off','give_credit','go_to_post','hide_old_child_comments');

		foreach($checkboxes as $name){
			if(!$options[$name]){ $options[$name] = 0; }
		}

		if(!$options['comment_order']){ $options['comment_order'] = 'ASC'; }
		while (list($option, $value) = each($options)) {
			if( get_magic_quotes_gpc() ) {
			$value = stripslashes($value);
			}
			$inapall[$option] =$value;
		}
	return $inapall;
	}


	function update_custom(){
	global $inapall, $id,$post,$inap_use_custom,$inap_disabled_posts;
     	 if (!isset($id))
      	   $id = $_REQUEST['post_ID'];

		if (isset($_REQUEST['inapcustom'])) {
			$inapcustom =$_REQUEST['inapcustom'];


			if($inap_disabled_posts[$id] != $inapcustom['disable_inap']){
				$inap_disabled_posts[$id] = $inapcustom['disable_inap'];
				update_option('inap_disabled_posts',$inap_disabled_posts);
			}

			if($inapall['allow_custom'] == 1){
				$stored_custom = get_post_meta($id, 'inapcustom');

				$checkboxes = array('strip_excerpt','strip_html','hide_excerpt');

				foreach($checkboxes as $name){
					if(!$inapcustom[$name]){ $inapcustom[$name] = 0; }
				}

				if($inap_use_custom[$id] != $inapcustom['use_custom']){
						$inap_use_custom[$id] = $inapcustom['use_custom'];
						update_option('inap_use_custom',$inap_use_custom);
				}

				if(isset($stored_custom)){
					update_post_meta($id, 'inapcustom', $inapcustom);
				}else{
					add_post_meta($id, 'inapcustom', $inapcustom);
				}
			}
		}
	}

	function customUI(){
		global $inapall, $id,$post,$inap_disabled_posts;
		$id = $post->ID;
		if($inapall['allow_custom'] == 1){
			$inapcustom = get_post_meta($id, 'inapcustom',true);
			if( $inapcustom['use_custom'] == 1){ $uc = 'checked="checked"'; }else{$inapcustom = $inapall;}


			$texts = array('link_show_text','link_hide_text','link_embed_show_text','link_embed_hide_text','afterpages','page_sep','afterpage','pagelinks','beforepage','beforepages');
			foreach($texts as $name){
				$inapcustom[$name]= stripslashes(htmlspecialchars($inapcustom[$name],ENT_QUOTES));
			}
			if($inapcustom['split_mode'] == '3'){
				$sm3 = 'checked="checked"'; 
			}elseif($inapcustom['split_mode'] == '2'){
				$sm2 = 'checked="checked"'; 
			}else{
				$sm1 = 'checked="checked"'; 
			}
			if($inapcustom['paginate_mode'] == '1'){
				$fp1 = 'checked="checked"'; 
			}else{
				$fp0 = 'checked="checked"'; 
			}
			if($inapcustom['hide_excerpt'] == 1){ $he = 'checked="checked"'; }
			if($inapcustom['strip_excerpt'] == 1){ $se = 'checked="checked"'; }
			if($inapcustom['strip_html'] == 1){ $sh = 'checked="checked"'; }
		}

		if($inap_disabled_posts[$id] == 1){ $dI = 'checked="checked"'; }
?>

<div class="dbx-b-ox-wrapper">
<fieldset id="postexcerpt" class="dbx-box">
<div class="dbx-h-andle-wrapper">
<h3 class="dbx-handle">Optional Excerpt</h3>
</div>
<div class="dbx-c-ontent-wrapper">
<div class="dbx-content"><textarea rows="1" cols="40" name="excerpt" tabindex="6" id="excerpt"></textarea></div>

</div>
</fieldset>
</div>


<div class="dbx-b-ox-wrapper">
			<fieldset id="inapUIdiv" class="dbx-box">
<div class="dbx-h-andle-wrapper">
			<h3 class="dbx-handle">Custom INAP options for this post</h3></div>
<div class="dbx-c-ontent-wrapper">
			<div id="inapUI" class="dbx-content">
			<input type="hidden" value="1" name="inapcustom[check]">
<ul>
    <li><p>
		<input type="checkbox" value="1" <?php echo $dI;?> name="inapcustom[disable_inap]"> &laquo;&mdash; <?php _e('Disable INAP for this post?','inap');?>
    </p></li>
</ul>

<?php if($inapall['allow_custom'] == 1){ ?>
	<?php _e('The following settings will overrule any INAP settings set in the managment page. Full explanations are in the admin panel.','inap');?><br />
	<ul>
		<li><p>
			<input type="checkbox" value="1" <?php echo $uc;?> name="inapcustom[use_custom]"> &laquo; <?php _e('Use custom options on this post?','inap');?>
		</p></li>
	</ul>
<ul>
<li><p><?php _e('Split preview by: ','inap');?>
		<label><input type="radio" value="1" <?php echo $sm1;?> name="inapcustom[split_mode]"/> &laquo; <?php _e('Only on more or nextpage tags ','inap');?></label>
		<label> <input type="radio" value ="2" <?php echo $sm2;?> name="inapcustom[split_mode]"/> &laquo; <?php _e('By paragraphs (unless more tag) ','inap');?></label>
		<label><input type="radio" value ="3" <?php echo $sm3;?> name="inapcustom[split_mode]"/> &laquo; <?php _e('By word count ','inap');?></label>
		<?php _e('and show ','inap');?>
		<input type="text" size="4" value="<?php echo $inapcustom['max_words_para'];?>" name="inapcustom[max_words_para]">
		<?php _e('words or paragraphs for the excerpt.','inap');?>
    </p></li>
    <li><p>
		<input type="checkbox" value="1" <?php echo $se;?> name="inapcustom[strip_excerpt]"> &laquo; <?php _e('Strip excerpt?','inap');?>
		<input type="checkbox" value="1" <?php echo $he;?> name="inapcustom[hide_excerpt]"> &laquo; <?php _e('Hide default excerpt?','inap');?>
		<input type="checkbox" value="1" <?php echo $sh;?> name="inapcustom[strip_html]"> &laquo; <?php _e('Strip HTML in excerpt?','inap');?>
    </p></li>
 <li><p>
		<input type="text" value="<?php echo $inapcustom['link_show_text'];?>" name="inapcustom[link_show_text]"> &laquo;&mdash; <?php _e('Show text.','inap');?>
		<input type="text" value="<?php echo $inapcustom['link_hide_text'];?>" name="inapcustom[link_hide_text]"> &laquo;&mdash; <?php _e('Hide text.','inap');?>
		<input type="text" value="<?php echo $inapcustom['link_embed_show_text'];?>" name="inapcustom[link_embed_show_text]"> &laquo;&mdash; <?php _e('Show text (for embedded posts).','inap');?>
		<input type="text" value="<?php echo $inapcustom['link_embed_hide_text'];?>" name="inapcustom[link_embed_hide_text]"> &laquo;&mdash; <?php _e('Hide text (for embedded posts).','inap');?>
    </p></li>
</ul>
<ul>
    <li><p>
		<?php _e('Pagination: ','inap');?> 
		<label><input type="radio" value="0" <?php echo $fp0;?> name="inapcustom[paginate_mode]"/> &laquo;<?php _e('Off  ','inap');?></label>
		<label><input type="radio" value="1" <?php echo $fp1;?> name="inapcustom[paginate_mode]"/> &laquo;<?php _e('On  ','inap');?></label>
		<?php _e(' and show ','inap');?>
		<input type="text" size="4" value="<?php echo $inapcustom['paginate_word_limit'];?>" name="inap[paginate_word_limit]"> <?php _e('words or paragraphs per page.','inap');?>
    </p></li>
If you are using pagination you can use the following to create a custom layout for the links.
<li><p>
		<input type="text" size="4" value="<?php echo $inapcustom['beforepages'];?>" name="inapcustom[beforepages]"> &laquo;&mdash; <?php _e(' Before page list.','inap');?>

		<input type="text" size="4" value="<?php echo $inapcustom['beforepage'];?>" name="inapcustom[beforepage]"> &laquo;&mdash; <?php _e('Before individual page links.','inap');?>

	<input type="text" size="4" value="<?php echo $inapcustom['pagelinks'];?>" name="inapcustom[pagelinks]"> &laquo;&mdash; <?php _e('Before individual page links.','inap');?></p>
<p>		<input type="text" size="4" value="<?php echo $inapcustom['afterpage'];?>" name="inapcustom[afterpage]"> &laquo;&mdash; <?php _e('After individual page links','inap');?>
		<input type="text" size="4" value="<?php echo $inapcustom['page_sep'];?>" name="inapcustom[page_sep]"> &laquo;&mdash; <?php _e('Separator between individual page links','inap');?>
		<input type="text" size="4" value="<?php echo $inapcustom['afterpages'];?>" name="inapcustom[afterpages]"> &laquo;&mdash; <?php _e('After the page list','inap');?>
</p></li>

</ul>
<?php } ?></div>
			</div>
			</fieldset></div>
<?php
	}




//
// 	To integrate with the WP-PostViews plugin
//
	function process_postviews() {
	global $id;
		$post_views = get_post_custom($post_id);
		$post_views = intval($post_views['views'][0]);
		if(empty($_COOKIE[USER_COOKIE])) {	
			if($post_views > 0) {
				update_post_meta($id, 'views', ($post_views+1));
			} else {
				add_post_meta($id, 'views', 1, true);
			}
		}
	}

}
?>
