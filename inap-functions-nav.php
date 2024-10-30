<?php

Class INAP_nav{

	function next_posts($label='Next Page &raquo;', $max_page=0) {
	global $paged, $wpdb, $wp_query;
		if ( !$max_page ) {
			$max_page = $wp_query->max_num_pages;
		}
		if ( !$paged )
			$paged = 1;
		$nextpage = intval($paged) + 1;
		if ( (! is_single()) && (empty($paged) || $nextpage <= $max_page) ) {
			echo '<a href="';
			next_posts($max_page);
			echo '" onclick="inap_paged(\''.($nextpage).'\'); return false;">'. preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label) .'</a>';
		}
	}


	function previous_posts($label='&laquo; Previous Page') {
	global $paged;
		if ( (!is_single())	&& ($paged > 1) ) {
			echo '<a href="';
			previous_posts();
			echo '" onclick="inap_paged(\''.($paged-1).'\'); return false;">'. preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $label) .'</a>';
		}
	}

	function posts_nav($sep=' &#8212; ', $prelabel='&laquo; Previous Page', $nxtlabel='Next Page &raquo;') {
	global $wp_query;
		if ( !is_singular() ) {
			$max_num_pages = $wp_query->max_num_pages;
			$paged = get_query_var('paged');

			//only have sep if there's both prev and next results
			if ($paged < 2 || $paged >= $max_num_pages) {
				$sep = '';
			}

			if ( $max_num_pages > 1 ) {
				INAP_nav::previous_posts($prelabel);
				echo preg_replace('/&([^#])(?![a-z]{1,8};)/', '&#038;$1', $sep);
				INAP_nav::next_posts($nxtlabel);
			}
		}
	}

	function previous_post($format='&laquo; %link', $link='%title', $in_same_cat = false, $excluded_categories = '') {
		if ( is_attachment() )
			$post = & get_post($GLOBALS['post']->post_parent);
		else
			$post = get_previous_post($in_same_cat, $excluded_categories);

		if ( !$post )
			return;

		$title = apply_filters('the_title', $post->post_title, $post);
		$string = '<a href="'.get_permalink($post->ID).'" onclick="inap_paged(\''.$post->ID.'\',\'single\'); return false;">';
		$link = str_replace('%title', $title, $link);
		$link = $pre . $string . $link . '</a>';

		$format = str_replace('%link', $link, $format);

	echo $format;
	}

	function next_post($format='%link &raquo;', $link='%title', $in_same_cat = false, $excluded_categories = '') {
		$post = get_next_post($in_same_cat, $excluded_categories);

		if ( !$post )
			return;

		$title = apply_filters('the_title', $post->post_title, $post);
		$string = '<a href="'.get_permalink($post->ID).'" onclick="inap_paged(\''.$post->ID.'\',\'single\'); return false;">';
		$link = str_replace('%title', $title, $link);
		$link = $string . $link . '</a>';
		$format = str_replace('%link', $link, $format);

	echo $format;
	}

}
//Grandfathering it in.

	function inap_posts_nav_link($sep=' &#8212; ', $prelabel='&laquo; Previous Page', $nxtlabel='Next Page &raquo;') {
		INAP_nav::posts_nav($sep, $prelabel, $nxtlabel);
	}


	function inap_previous_posts_link($label='&laquo; Previous Page') {
		INAP_nav::previous_posts($label);
	}

	function inap_next_posts_link($label='Next Page &raquo;', $max_page=0) {
		INAP_nav::next_posts($label, $max_page);
	}

	function inap_previous_post_link($format='&laquo; %link', $link='%title', $in_same_cat=false, $excluded_categories = '') {
		INAP_nav::previous_post($format, $link, $in_same_cat, $excluded_categories);
	}

	function inap_next_post_link($format='%link &raquo;', $link='%title', $in_same_cat = false, $excluded_categories = '') {
		INAP_nav::next_post($format, $link, $in_same_cat, $excluded_categories);
	}
?>