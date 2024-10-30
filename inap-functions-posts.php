<?php
class INAP_Post {

	// this processes which options will be used from custom options and which ones are globals.
	function get_real_options(){
		global $id, $inapall,$inap_options,$inap_use_custom,$inap_disabled_posts;

		if(!is_array($inap_options[$id])){
		$cable_ops = array('link_show_text','link_hide_text','link_embed_show_text','link_embed_hide_text','strip_html','strip_excerpt','hide_excerpt','paginate_mode','split_mode','max_words_para','afterpages','page_sep','afterpage','pagelinks','beforepage','beforepages','go_to_post','read_more');
		$inapcustom = get_post_meta($id, 'inapcustom',true);
		if($inap_disabled_posts[$id] ==1 || $inapall['allow_custom'] == 0 || $inap_use_custom[$id] == 0 || !isset($inapcustom)){
			foreach($cable_ops as $op){
				$options[$op] = $inapall[$op];
			}
		}else{
			foreach($cable_ops as $op){
					$options[$op] = $inapcustom[$op];
			}
		}
			return $options;
		}else{
			return $inap_options[$id];
		}
	}


	function inap_post(){
	global $inapall,$inap_disabled_posts,$id;

		if($inap_disabled_posts[$id] == 1){
			the_content();
		}else{

			$output = INAP_Post::filter('');

			if ($inapall['default_behavior'] =='excerpt'){
				$output = apply_filters('the_excerpt', $output);
			}else{
				$output = apply_filters('the_content', $output.chr(13));
				
			}

			echo '<div id="post_excerpt_'.$id.'" class="post_excerpt" >'.$output.'</div>';

			if(!$inapall['inap_pages'])
				echo INAP_Post::pages();
		}
	}




	function filter($default_content=''){
	global $inapall,$inaped_posts,$content,$inap_disabled_posts, $inap_options;
	global $id, $post, $page, $pages;

		$inap_options[$id] = INAP_post::get_real_options();

		if ( !empty($post->post_password) && stripslashes($_COOKIE['wp-postpass_'.COOKIEHASH]) != $post->post_password ) {	// and it doesn't match the cookie
					$pages[0] = get_the_password_form();
		}elseif(is_page() || $inap_disabled_posts[$id] ==1 || (is_single() && !$inap_options[$id]['paginate_mode']) || (is_feed() && !$inapall['trimfeeds'])){
			if($default_content != ''){ 
				return $default_content;
			}else{
				return $post->content;
			}
		}else{

			if(!is_numeric($inaped_posts[$id]))
				INAP_Post::paginate();
			// Start the output.
			$output = '<div id="post_content_'.$id.'" class="post_content">'."\n".chr(13)."\n".$pages[0]."\n".chr(13)."\n".'</div>';

			if(!$inapall['inap_pages'])
				$output .= INAP_Post::pages();
			return $output;
		}
	}

	function paginate(){
	global $pages,$inapall,$inaped_posts,$post,$content,$id,$inap_options;
	global $is_page, $is_single;
	$pages = '';

		$inap_options[$id] = INAP_post::get_real_options();

		$content = $post->post_content;
		if($inap_options[$id]['strip_html'] == 1){
			$content = strip_tags($content);
		}

		$content = force_balance_tags($content);

		$output = INAP_Post::create_pages();
		$count = count($output);

		if($is_single || $count== 1){
				if($split_mode != 2){
					$pages[0] =  force_balance_tags(implode(' ',$output));
				}else{
					$pages[0] =  force_balance_tags(implode("\n".chr(13)."\n",$output));
				}

		}elseif(!$inap_options[$id]['paginate_mode'] || $count == 2){
			$pages[0] = force_balance_tags($output[0]);
				if($inap_options[$id]['strip_excerpt']){
					unset($output[0]);
				}
			if($split_mode != 2){
				$pages[1] =  force_balance_tags(implode(' ',$output));
			}else{
				$pages[1] =  force_balance_tags(implode("\n".chr(13)."\n",$output));
			}
			if($inap_options[$id]['split_mode'] == 3){
				$pages[0] .= '...';
			}
		}else{
			foreach($output as $page){

				if($inap_options[$id]['split_mode'] == 3 && $i < $count-1){
					$pages[] = force_balance_tags($page).'...';
				}else{
					$pages[] = force_balance_tags($page);
				}
				$i++;
			}

		}
	}

	function create_pages($split_mode=1, $word_limit=300){
	global $pages,$inapall,$inaped_posts,$post,$content,$id,$inap_options;
	global $is_page, $is_single;
	$k = $y = $n = $x = 0;


		$split_limit = ($inap_options[$id]['max_words_para'])? $inap_options[$id]['max_words_para']: $word_limit;
		$split_mode = ($inap_options[$id]['split_mode'])? $inap_options[$id]['split_mode']: $split_mode;
		$split_limit2 = ($inap_options[$id]['paginate_max_words_para'] > 0)? $inap_options[$id]['paginate_max_words_para']: $word_limit;

		if($split_mode == 1 ||strpos($content, '<!--nextpage-->') != false || strpos($content, '<!--more-->') != false || $is_page || $is_single){

			$output = preg_split('/\S*\<\!--(nextpage|more)--\>\S*/', $content);

		}elseif($split_mode == 2){

			$lines = preg_split('/(\n'.chr(13).'\n+)/', $content,-1, PREG_SPLIT_DELIM_CAPTURE);
			$i = count($lines);
			if($i > $split_limit){
				for($y=$n; $y < $i; $y++){
					$output[$k] .= $lines[$y];
					$y++;
					$output[$k] .= $lines[$y];
					$x++;

					if($x>=$line_break){
					$split_limit = $split_limit2;
						$x=0;
						$k++;

					}
				}
			}else{
				$output[0]=$content;
			}

		}elseif($split_mode == 3){
		$words = explode(' ', $content);
			if (count($words) > $split_limit) {
				$i = count($words);
				for($y=$n; $y <=$i; $y++){
						$output[$k] .= ' '.$words[$y];
					$x++;
					if($x >=$split_limit && ($i-$y >= $split_limit/2 || $k == 0)){
					$split_limit = $split_limit2;
						$x=0;
						$k++;
					}
				}
			}else{
				$output[0]=$content;
			}

		}

		if($inap_options[$id]['hide_excerpt'] == 1){
			if($post->post_excerpt != '' && !is_single() && !is_page()){
				array_unshift($output, $post->post_excerpt);
			}
		}
		$inaped_posts[$id] =1;
		return array_values(array_diff($output, array("","\n")));

}

	// Ensures that things added to the end of the content are not repeated.
	function break_content($content){

		if(function_exists('wp_lightboxJS_replace')|| function_exists('wp_lightbox_plus')){
			$content = preg_replace('@(<a[^>]*?rel="lightbox")@', '${1} onclick="showLightbox(this); return false;"', $content);
		}


		$content = explode('@$%@$$%##$%#$%#$',$content);
		return $content[0];
	}

//modified version of wp_link_pages
	function pages(){
	global $id,$inapall,$inaped_posts, $pages,$inap_disabled_posts,$inap_options;
	global $is_page, $is_single;
	$k = 1; 
		$inap_options[$id] = INAP_post::get_real_options();

		if($inap_disabled_posts[$id] == 1 || $is_page)
			return;
		
		if($inap_options[$id]['link_show_text']){
			$link_show_text=$inap_options[$id]['link_show_text'];
		}

		if($inap_options[$id]['link_hide_text']){
			$link_hide_text=$inap_options[$id]['link_hide_text'];
		}

		if(!$link_show_text){
			$link_show_text='Click to continue reading';
		}

		$link_show_text =  INAP::process_text($link_show_text);
		$link_hide_text =  INAP::process_text($link_hide_text);

	if(!is_numeric($inaped_posts[$id])){
		if($is_single && !$inap_options[$id]['paginate_mode']){
			$pages[0] = $content;
		}else{
			INAP_Post::paginate();
		}
	}

		
		if(!$inap_options[$id]['pagelinks']){
			$inap_options['pagelinks'] = '%';
		}

		$numpages = count($pages);
		if ( $numpages >2 ) {
		
			$output = $inap_options[$id]['beforepages'];

				for ( $i = 1; $i <= $numpages; $i++ ) {

					preg_match('/<!--title=(.*?)-->/',$pages[$i-1],$matches);
					if($matches[1]){
					
						$j = htmlspecialchars($matches[1],ENT_QUOTES);
						
					}else{
					
						$j = str_replace('%',"$k",$inap_options[$id]['pagelinks']);
						
					}
					if(!$is_single || !$is_page){
						$ref = 'rel="nofollow"';
					}
					$output .= ' '.$inap_options[$id]['beforepage'];
					
						if ( '' == get_option('permalink_structure') ){
							$output .= '<a href="' . get_permalink() . '&amp;page=' . $i . '" onclick="inap_request(\''.$id.'\',\'content\',\'off\',\'on\',\''.$i.'\'); return false;" '.$ref.'  class="post_content_link" id="post_page_'.$i.'_'.$id.'_link">';
						}else{
							$output .= '<a href="' . trailingslashit(get_permalink()) . $i . '/" onclick="inap_request(\''.$id.'\',\'content\',\'off\',\'on\',\''.$i.'\'); return false;" '.$ref.' class="post_content_link" id="post_page_'.$i.'_'.$id.'_link">';
						}
					
					$output .= $j.'</a>'.$inap_options[$id]['afterpage'];
						if($i != $numpages)
							$output .= $inap_options[$id]['page_sep'];
						$k++;
				}
				
				$output .= $inap_options['afterpages'];
				
		}elseif($numpages == 2){
			$output.= '<a id="post_content_link_'.$id.'" href="'.get_permalink($id).'" onclick="inap_request(\''.$id.'\',\'content\',\''.$link_show_text.'\',\''.$link_hide_text.'\'); return false;" class="post_content_link">'.stripslashes($link_show_text).'</a>';
		}

		if($inapall['go_to_post'] == 1 && $inapall['read_more'] != ''){
				$output.= '<br />'.INAP_post::go_to_post();
		}

		if(!$inapall['inap_pages']){
			return $output;
		}else{
			echo $output;
		}

	}

	function go_to_post(){
	global $inapall;

		if( $inapall['read_more'] != ''){
				return '<a href="'.get_permalink($id).'"  class="post_content_more">'.stripslashes(INAP::process_text($inapall['read_more'])).'</a>';
		}

	}

	function list_pages($content){

	return preg_replace_callback('!\[mychildren\]!ims', array('INAP_post','embed_pages'), $content);
	}


	function embedded_post_tag($content){
	return preg_replace_callback('!\[embed=([0-9]*)\]!ims', array('INAP_post','embedded_post'), $content);
	}

	function embed_pages(){
		global $id;
	//wp_list_pages('sort_column=post_title&child_of='.$id.'&depth=1&title_li=');

	$r = array('depth' => 0, 'show_date' => '', 'date_format' => get_option('date_format'),
		'child_of' => $id, 'exclude' => '', 'title_li' =>'', 'echo' => 1, 'authors' => '');
	
	// Query pages.
	$pages = get_pages($r);

		foreach($pages as $page){
			if($page->post_status != 'draft' && $page->post_status !='future'){
			$output .= apply_filters('the_title', '<h3>'.$page->post_title.'</h3>');
			$output .= INAP_post::embedded_post($page->ID);}
		}
		return $output;
	}

	function embedded_post($pid){
		global $inapall,$inaped_posts,$wpdb,$id,$inap_options;
		if(!is_array($pid)){
			$return = 1;
			$pid = $pid;
		}elseif(is_array($pid)){
			$return = 1;
			$pid = $pid[1];
		}
		if($id != $pid){

			$good_post = $wpdb->get_var("SELECT ID FROM $wpdb->posts WHERE post_status != 'draft' && post_status !='future' && ID = '$pid'");

			if($good_post){
				$link_show_text =  INAP::process_text($inap_options[$id]['link_embed_show_text'],$pid);
				$link_hide_text =	INAP::process_text($inap_options[$id]['link_embed_hide_text'],$pid);
				$link_show_text= str_replace("&#039;",'&#8217;',$link_show_text);
				$link_hide_text= str_replace("&#039;",'&#8217;',$link_hide_text);

				$add = 	'<span id="post_content_'.$pid.'" class="post_content"></span>'.
							'<a id="post_content_link_'.$pid.'" href="'.get_permalink($pid).'" onclick="inap_request(\''.$pid.'\',\'content\',\''.$link_show_text.'\',\''.$link_hide_text.'\',\'embed\');  return false;" class="post_content_link">'.stripslashes($link_show_text).'</a>';
				if($return){
					return $add;
				}else{
					echo $add;
				}
			}
		}

	}

	function title(){
			global $inapall, $id, $post;
		$title = the_title('','',false);
		echo 'onclick="inap_request(\''.$id.'\',\'content\',\''.$title.'\',\''.$title.'\'); return false;" id="post_content_link_'.$id.'"';
	}


}

?>