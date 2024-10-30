<?php
	require_once('../../../wp-config.php');
	nocache_headers();

	if($_REQUEST['type'] == 'comment_children'){
		$id = $_REQUEST['extra'];
		$parent_post = $_POST['id'];
	}else{
		$id = $_POST['id'];
	}		$inap_disabled_posts = get_option('inap_disabled_posts');

	$post = get_post($id);
	header('Content-Type: text/html; charset='.get_option('blog_charset'));

	if (!empty($post->post_password) && $_COOKIE['wp-postpass_'. COOKIEHASH] != $post->post_password) {
		echo get_the_password_form();
	} else {
		if($_REQUEST['type'] == "content"){

			if($inapall['use_fade'] == 1){
				$style = "zoom:1; background-color:$inapall[background_color];";
			}
			do_action('inap_paginate');

	// we remove the filter so the function doesn't call itself
			if($inapall['simple_posts'] == 1){
				remove_filter('the_content', array('INAP_Post','filter'),-10,1);
				add_filter('the_content',array('INAP_Post','break_content'),99999);
			}

			if($_REQUEST['extra'] == 'embed'){
				$output .= '<div id="post_page_1_'.$id.'" class="post_page" style="'.$style.'">';
				$output .= '</div>';

				if(count($pages) > 1){
					unset($pages[0]);
				}

				$pages[0] = implode(' ',$pages);
				$output .= '<div id="post_page_2_'.$id.'" class="post_page" style="display:none; '.$style.'">';
				$output .= "\n".chr(13)."\n".$pages[0]."\n".chr(13)."\n";
				$output .= '</div>';

			}else{
				$output .= '<div id="post_page_1_'.$id.'" class="post_page" style="'.$style.'">';
				$output .= "\n".chr(13)."\n".$pages[0]."\n".chr(13)."\n";
				$output .= '</div>';
				unset($pages[0]);


				$i = 2;
				foreach($pages as $page){

					$page = preg_replace("/\n*<!--title=(.*?)-->\n*/",'',$page);
					$output .= '<div id="post_page_'.$i.'_'.$id.'" class="post_page" style="display:none; '.$style.'">';
					$output .= "\n".chr(13)."\n".$page."\n".chr(13)."\n";
					$output .= '</div>';
				$i++;
				}
			}

			echo apply_filters('the_content',$output.'@$%@$$%##$%#$%#$');

			// For compatibility with Statraq
				global $p;
				$p=$id;

			//For compatibility with WP-PostViews
			if(function_exists('process_postviews') && !is_page() && !is_single()) {
				INAP::process_postviews();
			}

		}elseif($_REQUEST['type'] == "paged"){


			if($_REQUEST['what'] == "single"){
				$id = (int) $_REQUEST['pagenum'];
				unset($GLOBALS['wp_query']);
				$GLOBALS['wp_query'] =& new WP_Query();
				$GLOBALS['wp_query']->query('p='.$id);

				$site_menu = file_get_contents(TEMPLATEPATH . '/single.php',1);
				preg_match('@<!--inap_loop-->([\S\s]*)<!--inap_loop-->@',$site_menu,$match);
				eval("?>$match[0]");

			}else{

				ob_start();
				global $paged;
					$paged = (int) $_REQUEST['pagenum'];
					if($paged){
						unset($GLOBALS['wp_query']);
						$GLOBALS['wp_query'] =& new WP_Query();
						$GLOBALS['wp_query']->query('paged='.$paged);
						include(TEMPLATEPATH . '/index.php');
					}
				$buffer = ob_get_contents();
				ob_end_clean();
				//echo $buffer;
				preg_match('@<!--inap_loop-->([\S\s]*)<!--inap_loop-->@',$buffer,$match);
				echo str_replace('http://'.$_SERVER['HTTP_HOST'].$_SERVER['PHP_SELF'] ,get_settings('siteurl'),$match[0]);

			}
		}elseif($_REQUEST['type'] == "comments"){

			include ("inap-comments.php");

		}elseif($_REQUEST['type'] == "addcomment"){

				include ("inap-add-comments.php");


		}elseif($_REQUEST['type'] == "submit_form"){

			inap_get_info_submit_form();

		}elseif($_REQUEST['type'] == "comment_children"){
		global $comments;
			$comments =  $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$id' AND comment_approved = '1' ORDER BY comment_date $inapall[comment_order]");
			INAP_comments::thread($parent_post,1,1);
		}

	}





function inap_get_info_submit_form(){
global $wpdb, $post,$id,$inapall;
add_filter('comment_post_redirect', 'inap_get_info_remove_redirect');

		ob_start("inap_trynodie"); 
			require_once('../../../wp-comments-post.php');
		ob_end_clean();

	 _e('Comment submitted','inap');
}

function inap_get_info_remove_redirect(){
return;
}
 ?>




