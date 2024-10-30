<?php
	// this line is WordPress' motor, do not delete it.
	$comment_author = (isset($_COOKIE['comment_author_' . COOKIEHASH])) ? trim($_COOKIE['comment_author_'. COOKIEHASH]) : '';
	$comment_author_email = (isset($_COOKIE['comment_author_email_'. COOKIEHASH])) ? trim($_COOKIE['comment_author_email_'. COOKIEHASH]) : '';
	$comment_author_url = (isset($_COOKIE['comment_author_url_'. COOKIEHASH])) ? trim($_COOKIE['comment_author_url_'. COOKIEHASH]) : '';

	echo '<hr/>';

	if ('open' == $post->comment_status) {
		if (! is_user_logged_in() && get_option('comment_registration')){
			$link = '<a href="' . get_option('siteurl') . '/wp-login.php">' . __('Login') . '</a>';
			if ( get_option('users_can_register') )
				$link .= ' or <a href="' . get_option('siteurl') . '/wp-login.php?action=register">' . __('Register') . '</a>';
			die( __('Sorry, you must '.$link.' to post a comment.'));
		}
	}

	function inap_the_base(){
   		 echo '/'.end(explode('/', str_replace(array('\\','/inap-add-comments.php'),array('/',''),__FILE__)));

	}



/**
This entire form is editable just like any other add comment form; however, the following elements and IDs are required:
<span id="submit_form_<?php echo $id;?>"
<textarea name="comment" id="comment_<?php echo $id;?>"
<input type="hidden" name="comment_post_parent" id="comment_post_parent_<?php echo $id; ?>"
<input type="hidden" name="comment_post_ID"  value="<?php echo $id; ?>" />
<input name="submit" type="submit" id="submit_<?php echo $id;?>"

To ensure XHTML validity, always give new elements an ID that ends with "<?php echo $id;?>". This will ensure they are always unique.
**/
?>

<h2>Leave a reply</h2>

<div style="width:99%;">
	<div id="submit_form_<?php echo $id;?>" style="float:left;width:80%;"></div>
	<div style="float:right;width:15%; text-align:right;"><a href="#respond" onclick="inap_request(<?php echo $id;?>,'addcomment','<?php echo INAP::process_text($inapall['addcomment_open']);?>','<?php echo INAP::process_text($inapall['addcomment_hide']);?>'); return false;" rel="nofollow"><img src="<?php echo get_option('siteurl'); ?>/wp-content/plugins<?php inap_the_base();?>/close_normal.gif" alt="Click here to close" /></a></div>
</div>

<form  id="commentform-<?php echo $id;?>" class="comment_form" action="<?php echo get_option('siteurl'); ?>/wp-comments-post.php" method="post" onsubmit="submit_form(<?php echo $id;?>,'addcomment','<?php echo INAP::process_text($inapall['addcomment_open']);?>','<?php echo INAP::process_text($inapall['addcomment_hide']);?>','<?php echo INAP::process_text($inapall['comment_hide']);?>'); return false;">

<?php if ( $user_ID ) { global $user_identity;?>

<p>Logged in as <a href="<?php echo get_option('siteurl'); ?>/wp-admin/profile.php"><?php echo $user_identity; ?></a>.</p>

<?php } else { ?>

<p><input type="text" name="author" id="author_<?php echo $id;?>" value="<?php echo $comment_author; ?>" size="22" tabindex="1" />
<label for="author_<?php echo $id;?>"><small><?php _e('Name');?> <?php if ($req) _e('(required)'); ?></small></label></p>

<p><input type="text" name="email" id="email_<?php echo $id;?>" value="<?php echo $comment_author_email; ?>" size="22" tabindex="2" />
<label for="email_<?php echo $id;?>"><small><?php _e('Mail');?> <?php _e('(will not be published)');?> <?php if ($req) _e('(required)'); ?></small></label></p>

<p><input type="text" name="url" id="url_<?php echo $id;?>" value="<?php echo $comment_author_url; ?>" size="22" tabindex="3" />
<label for="url_<?php echo $id;?>"><small><?php _e('Website');?></small></label></p>

<?php } ?>

<?php if(function_exists('display_cryptographp')){?>

	<p><?php display_cryptographp(); ?></p>

<?php } ?>

<?php
if(function_exists('show_subscription_checkbox')){
	show_subscription_checkbox();
} ?>

<?php if($inapall['add_comments_tags'] == 1){?>
<p id="quicktags_<?php echo $id;?>">
<input id="ed_strong_<?php echo $id;?>" accesskey="b" class="ed_button" onclick="edInsertTag(<?php echo $id;?>, 0);" value="b" type="button" />
<input id="ed_em_<?php echo $id;?>" accesskey="i" class="ed_button" onclick="edInsertTag(<?php echo $id;?>, 1);" value="i" type="button" />
<input id="ed_link_<?php echo $id;?>" accesskey="a" class="ed_button" onclick="edInsertLink(<?php echo $id;?>, 2);" value="link" type="button" />
<input id="ed_block_<?php echo $id;?>" accesskey="q" class="ed_button" onclick="edInsertTag(<?php echo $id;?>, 3);" value="b-quote" type="button" />
<input id="ed_img_<?php echo $id;?>" accesskey="m" class="ed_button" onclick="edInsertImage(<?php echo $id;?>);" value="img" type="button" />
<input id="ed_ul_<?php echo $id;?>" accesskey="u" class="ed_button" onclick="edInsertTag(<?php echo $id;?>, 5);" value="ul" type="button" />
<input id="ed_ol_<?php echo $id;?>" accesskey="o" class="ed_button" onclick="edInsertTag(<?php echo $id;?>, 6);" value="ol" type="button" />
<input id="ed_li_<?php echo $id;?>" accesskey="l" class="ed_button" onclick="edInsertTag(<?php echo $id;?>, 7);" value="li" type="button" />
<input id="ed_code_<?php echo $id;?>" accesskey="c" class="ed_button" onclick="edInsertTag(<?php echo $id;?>, 8);" value="code" type="button" />
<input id="ed_quote_<?php echo $id;?>" accesskey="" class="ed_button" onclick="edInsertTag(<?php echo $id;?>, 9);" value="Quote" type="button" />
<input type="button" id="ed_close_<?php echo $id;?>" class="ed_button" onclick="edCloseAllTags(<?php echo $id;?>);" title="<?php _e('Close all open tags');?>" value="<?php _e('Close Tags');?>" />
</p>
<?php } ?>

<p><label for="comment_<?php echo $id;?>">Comment:</label><textarea name="comment" id="comment_<?php echo $id;?>" style="width:95%;" rows="10" cols="10" tabindex="4"
<?php if($inapall['live_preview'] == 1){echo 'onkeyup="inap_live_preview('.$id.')"';} ?> > </textarea></p>
<p><input name="submit" type="submit" class="submit" id="submit_<?php echo $id;?>" tabindex="5" value="<?php _e('Submit Comment');?>" />
<input type="hidden" name="comment_post_ID"  value="<?php echo $id; ?>" />
<?php if($inapall['comment_threaded']){?>
<input type="hidden" name="comment_post_parent" id="comment_post_parent_<?php echo $id; ?>" />
<?php } ?>
</p>
<?php do_action('comment_form', $post->ID); ?>
</form>
<?php do_action('live_preview'); ?>
