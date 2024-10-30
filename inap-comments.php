<?php
	global $wpdb, $inapall;
	$comments =  $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE comment_post_ID = '$id' AND comment_approved = '1' ORDER BY comment_date $inapall[comment_order]");

	if(!$_POST['type']){
	echo "\n".'<div id="post_comments_'.$id.'" class="post_comments">';
	$end = '</div>';
	}
	if ( file_exists( TEMPLATEPATH . '/comments.php') && $inapall['comment_template'] == 1){

		include(TEMPLATEPATH . '/comments.php');

	}elseif (file_exists( TEMPLATEPATH . '/inap-comments.php') && $inapall['comment_template'] == 2){

		include(TEMPLATEPATH . '/inap-comments.php');

	}elseif($inapall['comment_threaded'] == true){

		INAP_comments::thread();

	}else{

		INAP_comments::thread('none');

	}
	echo $end;

?>
