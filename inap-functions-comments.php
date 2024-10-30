<?php


function inap_trynodie($error){
header("HTTP/1.1 200 OK");
		$err = 'ERROR: ';

	preg_match('@<p>(.*?)</p>@', $error,$errs);
	return $err.$errs[1];
}

class INAP_comments{

	function div(){
	global $id, $post,$inapall;
		if ($post->comment_count > 0|| comments_open()){
			if ( (is_single() && $inapall['show_comments_single'] == 1) || (is_page() && $inapall['show_comments_page'] == 1 )){
				if (!empty($post->post_password)) { // if there's a password
					if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
						echo "\n".'<div id="post_comments_'.$id.'" class="post_comments">';
						?>

						<p class="nocomments">This post is password protected. Enter the password to view comments.</p>

						<?php
						echo '</div>';
						return;
					}
				}
					include('inap-comments.php');
			}else{
				echo "\n".'<div id="post_comments_'.$id.'" class="post_comments" style="display:none;"></div>';
			}
		}
	}

	function links($comment_open='Show Posts Comments', $comment_hide='Hide Post Comments'){
		global $id,$post,$inapall;
			
		if ($inapall['comment_open'] != ''){
			$comment_open = INAP::process_text($inapall['comment_open']);
		}
		if ($inapall['comment_hide'] != ''){
			$comment_hide = INAP::process_text($inapall['comment_hide']);
		}

		if( !$comment_open )
			$comment_open='Show Posts Comments';

		$show = $comment_open;
		// this is the most cheating way to do it, but it makes it Soooo easy.
		if ( (is_single() && $inapall['show_comments_single'] == 1) || (is_page() && $inapall['show_comments_page'] == 1 )){
			$show = $comment_hide;
		}

		
		if ($post->comment_count == 0 && comments_open() && $inapall['comment_template'] != 1){

			echo "\n".'<span id="post_comments_none_'.$id.'" class="post_comments_link">';

			if($inapall['no_comments'] == ''){
				_e('No Comments','inap');
			}else{
				echo INAP::process_text($inapall['no_comments']);
			}

			echo '</span> ';

			echo "\n".'<a id="post_comments_link_'.$id.'" class="post_comments_link" href="'.get_permalink($id).'#comments" onclick="inap_request(\''.$id.'\',\'comments\',\''.htmlspecialchars("$comment_open", ENT_QUOTES).'\',\''.htmlspecialchars("$comment_hide",ENT_QUOTES).'\'); return false;" rel="nofollow">&nbsp;</a>';

		}elseif($post->comment_count == 0&& !comments_open()){

				if($inapall['no_comments'] == ''){
					_e('No Comments','inap');
				}else{
					echo INAP::process_text($inapall['no_comments']);
				}

		}else{

			echo "\n".'<a href="'.get_permalink($id).'#comments" class="post_comments_link" id="post_comments_link_'.$id.'" onclick="inap_request(\''.$id.'\',\'comments\',\''.htmlspecialchars("$comment_open", ENT_QUOTES).'\',\''.htmlspecialchars("$comment_hide",ENT_QUOTES).'\'); return false;" rel="nofollow">'.$show.'</a>';

		}
	}


	function filter($i){
	global $id, $post,$inapall;
		return(ABSPATH . 'wp-content/plugins/'.INAP::get_base().'/inap-comments.php');
	}


	function get_parent($id) {
		global $wpdb;
		$comment_parent = $_POST['comment_post_parent'];
		
		if(is_numeric($parent) && $comment_parent) {
			$result = $wpdb->query("UPDATE $wpdb->comments SET comment_parent = '$comment_parent' WHERE comment_ID = '$id'");
		}
	}

	function thread($comment_parent_id=0,$depth=0, $continue=0) {
		global $post, $wpdb, $id, $comment,$comments,$inapall;

		$parent = "AND comment_parent='$comment_parent_id'";
		if($comment_parent_id && !is_numeric($comment_parent_id)){
			$parent = $comment_parent_id = '';
		}

		if(!$comment_parent_id){
			$order = $inapall['comment_order'];
		}

		$comments[$comment_parent_id+1] =  $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$post->ID' AND comment_approved = '1' $parent ORDER BY comment_date $order");

		if(count($comments[$comment_parent_id+1]) >0){

		if($inapall['hide_old_child_comments'] == 1){
			if(strtotime($comment->comment_date_gmt) < (time()-(60*60*24*$inapall['old_comment_days'])) ){
				$old=1;
			}
		}

			if(($inapall['hide_child_comments'] == 1 || $old == 1) && $depth == 1 && $continue != 1){
				echo "\n".'<a id="post_comment_children_link_'.$comment->comment_ID.'" class="post_comments_link" href="'.get_permalink($id).'#comments" onclick="inap_request(\''.$comment->comment_ID.'\',\'comment_children\',\'Show Replies\',\'Hide Replies\',\''.$id.'\'); return false;" rel="nofollow">Show Replies to this Comment</a>';
				echo "\n".'<div id="post_comment_children_'.$comment->comment_ID.'" class="post_comments" style="display:none;"></div>';
			}else{
				if($depth == $inapall['comment_threaded_depth']){$depth = -2;}elseif($depth < $inapall['comment_threaded_depth'] && $depth > -1 ){$depth++;}else{$depth = -1;}
				INAP_comments::template($comment_parent_id+1,$depth);
			}
		}
	}

	function print_comment($alt='',$depth=0){
	global $inapall,$comment;

		
		$auth = ($comment->comment_author_email == get_the_author_email()) ? 'authorcomment' : '';
		echo "\n\n".'<'.$inapall['comment_tag'].' class= "comment '.$alt.'" id="comment-'.$comment->comment_ID.'">';

			if($auth)
				echo '<div class="'.$auth.'">';

			echo '<'.$inapall['comment_title_tag'].' class="commentbar ">';
				INAP_comments::avatar();
			echo '<cite>';
				comment_author_link();
			echo '</cite>';
			_e(' posted the following on ','inap');
				comment_date('F jS, Y');
			echo ' at ';
				comment_time();
				edit_comment_link('e','','');
			echo '</'.$inapall['comment_title_tag'].'>';
				comment_text();

		if($auth)
			echo '</div>';
		if($inapall['comment_threaded']==1){
			INAP_addcomments::links($comment->comment_ID);
			if($depth < 0){echo '</'.$inapall['comment_tag'].'>';}
			INAP_comments::thread($comment->comment_ID,$depth);
		}

		if($depth > -1){echo '</'.$inapall['comment_tag'].'>'."\n\n";}

	}


	function print_trackback($alt=''){
	global $inapall,$comment;

		echo '<'.$inapall['comment_tag'].' ';
			if($alt || $auth)
				echo 'class= "'.$alt.'"';
			echo ' id="comment-'.$comment->comment_ID.'">';

			echo '<'.$inapall['comment_title_tag'].' class="commentbar ">';

				if ('trackback' == $comment->comment_type){
					_e('Trackback');
				} elseif ( 'pingback' == $comment->comment_type) {
					_e('Pingback');
				}

			echo ' from '; comment_author_link();
			echo '</'.$inapall['comment_title_tag'].'>';
			comment_text();
		echo '</'.$inapall['comment_tag'].'>';

	}

	function template($comment_parent_id,$depth=0){
	global $inapall, $comments,$comment;

		if($comment_parent_id){
			$the_comments = $comments[$comment_parent_id];
		}else{
			$the_comments = $comments;
		}

		if(count($the_comments) >0){

		if($depth != -1){
		echo '<'.$inapall['comment_all_tag'].' class="comments">';
		}

			// This block is used if the comments are split in any way.
			if($inapall['split_comments'] != 0){
				foreach ($the_comments as $comment){
					$alt = ($cn % 2 == 0) ? 'alt' : '';
					$auth = ($comment->comment_author_email == get_the_author_email()) ? 'authorcomment' : '';

					if ($comment->comment_type == '') {
						INAP_comments::print_comment($alt,$depth);
						$cn++;
					}
				}
				if($inapall['split_comments'] == 2){

			/**Editable Text Below.**/
					echo '<h3 style="text-align:center;">';
						_e('Trackbacks and Pingbacks','inap');
					echo '</h3>';
			/**Editable Text Above.**/

					foreach ($the_comments as $comment){
						$alt = ($cn % 2 == 0) ? 'alt' : '';
						if ('trackback' == $comment->comment_type || 'pingback' == $comment->comment_type) {
							INAP_comments::print_trackback($alt);
						}
						$cn++;
					}
				}

				
			}else{
			//this block is used if all comments are shown.

				foreach ($the_comments as $comment){
					$alt = ($cn % 2 == 0) ? 'alt' : '';
						if ('trackback' == $comment->comment_type || 'pingback' == $comment->comment_type) {
								INAP_comments::print_trackback($alt);
						}else{
								INAP_comments::print_comment($alt,$depth);
						}
					$cn++;

				}


			}
	if($depth != -1){echo '</'.$inapall['comment_all_tag'].'>';}

		}
	}



	function avatar(){
	global $inap_integration, $comment;
 		if(isset($inap_integration['identicon'])){
 			echo $inap_integration['identicon']->identicon_build($comment->comment_author_email, $comment->comment_author);
 		}elseif(function_exists('myavatars')){
			myavatars();
		}elseif(function_exists('monsterid_build_monster')){
			monsterid_build_monster($comment->comment_author_email, $comment->comment_author);
		}
	}
}

class INAP_addcomments{

	function div(){
	global $id, $post,$inapall,$user_ID;
			if($inapall['comment_template'] == 1){
				return;
			}
		if ( comments_open() ) {
			if ( (is_single() && $inapall['show_addcomments_single'] == 1) || (is_page() && $inapall['show_addcomments_page'] == 1 )){
				echo "\n".'<div id="post_addcomment_'.$id.'" class="add_comments">';
				if (!empty($post->post_password)) { // if there's a password
					if ($_COOKIE['wp-postpass_' . COOKIEHASH] != $post->post_password) {  // and it doesn't match the cookie
						?>

						<p class="nocomments">This post is password protected. Enter the password to view comments.</p>

						<?php
      echo '</div>';
						return;
					}
				}
					include('inap-add-comments.php');
				echo '</div>';
			}else{
				echo "\n".'<div id="post_addcomment_'.$id.'" class="add_comments" style="display:none;"></div>';
			}
		}
	}


	function links($extra=''){
	global $id,$inapall,$inap_link_count;
	global $is_page, $is_single;


		if(!$inap_link_count[$id] && $extra){
			$inap_link_count[$id] = 10;
		}elseif(!$inap_link_count[$id]){
			$inap_link_count[$id] = 1;
		}

		if($inapall['comment_template'] == 1){
			return;
		}

		if($extra==''){
			if ($inapall['addcomment_open'] != ''){
				$addcomment_open = INAP::process_text($inapall['addcomment_open']);
			}
			if ($inapall['addcomment_hide'] != ''){
				$addcomment_hide = INAP::process_text($inapall['addcomment_hide']);
			}
		}else{
			if ($inapall['addcomment_reply_open'] != ''){
				$addcomment_open = INAP::process_text($inapall['addcomment_reply_open'],'comment');
			}
			if ($inapall['addcomment_reply_hide'] != ''){
				$addcomment_hide = INAP::process_text($inapall['addcomment_reply_hide'],'comment');
			}
		}

		if( !$addcomment_open )
			$addcomment_open='Add Comments';
		if( !$addcomment_hide )
			$addcomment_hide='Hide Comments';

		$show2 = $addcomment_open;
		$show = $addcomment_open;

		if ( ($is_single && $inapall['show_addcomments_single'] == 1) || ($is_page && $inapall['show_addcomments_page'] == 1 )){
			$show2 = $addcomment_hide;
		}
		
		if ( comments_open() ) {
			echo "\n".'<a href="'.get_permalink($id).'#respond" class="post_addcomment_link" id="post_addcomment_link'.$inap_link_count[$id].'_'.$id.'" onclick="inap_request(\''.$id.'\',\'addcomment\',\''.htmlspecialchars("$addcomment_open", ENT_QUOTES).'\',\''.htmlspecialchars("$addcomment_hide",ENT_QUOTES).'\',\''.$inap_link_count[$id];
		
		if($extra){
			echo ';'.$extra.'\'); return false;" rel="nofollow">'.$show.'</a>';
		}else{
			echo ';\'); return false;" rel="nofollow">'.$show2.'</a>';
		}

		$inap_link_count[$id]++;
		}else{
  				if($inapall['closed_comments'] == ''){
					_e('Comments are closed','inap');
				}else{
					echo INAP::process_text($inapall['closed_comments']);
				}
		}
	}

	function live_preview($return=0){
	global $inapall,$id;

		if($inapall['live_preview'] == 1){
			if($inapall['live_preview_nocomment']){
				$preview .= $inapall['live_preview_before'];
				$preview .= '<div id="add_comment_live_preview_'.$id.'" class="add_comment_live_preview main_border">Go ahead and start typing.</div>';
				$preview .= $inapall['live_preview_after'];
			}else{
				$preview .= '<'.$inapall['comment_all_tag'].'>';
				$preview .= '<'.$inapall['comment_tag'].'>';
				$preview .= '<'.$inapall['comment_title_tag'].' class="commentbar ">';
				$preview .= __('You will post the following soon ','inap');
				//	myavatars();
				$preview .= '</'.$inapall['comment_title_tag'].'>';
				$preview .= '<'.$inapall['comment_body_tag'].' id="add_comment_live_preview_'.$id.'">Go ahead and start typing.</'.$inapall['comment_body_tag'].'>';
				$preview .= '</'.$inapall['comment_tag'].'>';
				$preview .= '</'.$inapall['comment_all_tag'].'>';
			}
			if($return == 1){
				return $preview;
			}else{
				echo $preview;
			}
		}

	}

	function set_parent($commentdata){
		$comment_post_parent = (int) $_POST['comment_post_parent'];
		if(is_numeric($comment_post_parent) && empty($commentdata['comment_parent'])){
			$commentdata['comment_parent'] = $comment_post_parent;
		}
		return $commentdata;
	}


}
?>