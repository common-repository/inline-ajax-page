// <script>(to trick editors into using javascript syntax)
var delay = 0;
function inap_cleanhtml(text){

<?php
	if($inapall['live_preview_no_tags']){
		$badtags = explode(' ', $inapall['live_preview_no_tags']);
		if(count($badtags) > 0){
			?>

		text = text.replace(/<(\s*<?php foreach($badtags as $badtag){ echo $badtag.'|'; }?>@%@%@)/g, '&lt;$1');
			<?php


		}
	}
?>

return text;
}
<?php
 if($inapall['live_preview_smilies'] == 1){

	if(function_exists('csm_convert')){
		global $wpdb, $table_prefix;
			
			// Get emoticons from DB, order by length
			$result = $wpdb->get_results("SELECT * FROM `{$table_prefix}smileys` ORDER BY length(Emot) DESC");

			// Find and Replace
			foreach ( $result as $object ) {
				$smilies .= "'".$object->Emot."',";
				$files .= "'".$object->File."',";
			}		
			echo 'var smilies = ['.$smilies.'];';
			echo 'var smiliesfiles = ['.$files.'];';
			echo 'var smiliesalt = ['.$smilies.'];';
		$smilie_url = get_option("csm_path").'/';
	}else{

		$smilie_url = 'smilies/icon_';
?>
	var smilies = ['\\:mrgreen\\:', '\\:neutral\\:', '\\:twisted\\:', '\\:arrow\\:', '\\:shock\\:', '\\:smile\\:', '\\:\\?\\?\\?\\:', '\\:cool\\:', '\\:evil\\:', '\\:grin\\:', '\\:idea\\:', '\\:oops\\:', '\\:razz\\:', '\\:roll\\:', '\\:wink\\:', ':cry:', '\\:eek\\:', '\\:lol\\:', '\\:mad\\:', '\\:sad\\:', '8-?\\)', '8-?O', '\\:-?\\(', '\\:-?\\)', '\\:-\\?\\?', '\\:-?D', '\\:-?P', '\\:-?o', '\\:-?x', '\\:-?\\|', ';-?\\)', '\\:\\!\\:', '\\:\\?\\:'];
	var smiliesfiles = ['mrgreen', 'neutral', 'twisted', 'arrow', 'eek', 'smile', 'confused', 'cool', 'evil', 'biggrin', 'idea', 'redface', 'razz', 'rolleyes', 'wink', 'cry', 'surprised', 'lol', 'mad', 'sad', 'cool', 'eek', 'sad', 'smile', 'confused', 'biggrin', 'razz', 'surprised', 'mad', 'neutral', 'wink', 'exclaim', 'question'];
	var smiliesalt = [':mrgreen:', ':neutral:', ':twisted:', ':arrow:', ':shock:', ':smile:', ':???:', ':cool:', ':evil:', ':grin:', ':idea:', ':oops:', ':razz:', ':roll:', ':wink:', ':cry:', ':eek:', ':lol:', ':mad:', ':sad:', '8-)', '8-O', ':-(', ':-)', ':-?', ':-D', ':-P', ':-o', ':-x', ':-|', ';-)', ':!:', ':?:'];

<?php
}
?>

	var smiliescount;
	var x = smiliescount = smilies.length;
	var smil_reg = [];

	while(x--){
			smilies[x] = smilies[x].replace(/([\\\^\$*+[\]?{}.=!:(|)])/g,"\\$1");
			smil_reg[x] = new RegExp('(>|\\s|^)'+smilies[x]+'(\\s|$|<)', "gm");
	}

	function inap_convertsmilies(text){
	var x = smiliescount;
		while(x--){
			if(text.match(smil_reg[x])){
				text = text.replace(smil_reg[x],'$1<img src="<?php echo $home;?>/wp-includes/images/<?php echo $smilie_url;?>'+smiliesfiles[x]+'.gif" alt="'+smiliesalt[x]+'" class="wp-smiley" />$2');
			}
		}
	return text;
	}

<?php } ?>

		var characters = new Array('---', ' -- ', '--', 'xn&#8211;', '\\.\\.\\.', '\\s\\(tm\\)','(\\d+)"',"(\\d+)'","(\\w)('{2}|\")",'(`{2}|")(\\w)',"(\\w)'","'([\\s.]|\\w)");
		var replacements = new Array('&#8212;', ' &#8212; ', '&#8211;', 'xn--', '&#8230;',' &#8482;','$1&#8243;', '$1&#8242;','$1&#8221;','&#8220;$2','$1&#8216;','&#8217;$1');
		var char_regex = [];
		var charcount =characters.length;
		for(x=0; x< charcount; x++){
			char_regex[x] = new RegExp(characters[x], "g")
		}

/*
// Direct translation of wptexturize from php to javascript
// Cleaned and optimized for speed and actual usage.*/
function  inap_js_wptexturize(text) {
	var next = true;
	var output = '';
	var curl = '';
		text = text.replace(/(<[^>]*>)/g, '@%@%@$1@%@%@');
 		var textarr = text.split('@%@%@');
 		var stop = textarr.length;
		var i = 0;
	while (stop > i) {
		curl = textarr[i];
			if (curl.charAt(0) != '<' && next) { // If it's not a tag

				var x = charcount;
				while(x--){
					if(curl.match(char_regex[x])){
						curl = curl.replace(char_regex[x],replacements[x]);
					}
				}
			} else if ( curl.indexOf('<code') == 0 || curl.indexOf('<pre') == 0) {
				next = false;
			} else {
				next = true;
			}
		curl = curl.replace('/&([^#])(?![a-zA-Z1-4]{1,8};)/g', '&#038;$1');
		output += curl;
		i++;
	}
return output;

}
<?php
/*
// originally from: /wp-includes/js/tinymce/plugins/wordpress/editor_plugin.js
// Modified to compress size.
// If you want your users to use tables uncomment the next line:*/
/*$rest .='table|thead|tfoot|tbody|tr|td|th|div|';*/
$rest .= 'dl|dd|dt|ul|ol|li|pre|blockquote|p|h[1-6]';
?>
function inap_js_wpautop(pee) {

       pee = pee + "\n\n";
       pee = pee.replace(/<br \/>\s*<br \/>/gi, "\n\n");
       pee = pee.replace(/(<(?:<?php echo $rest;?>)[^>]*>)/gi, "\n$1");
       pee = pee.replace(/(<\/(?:<?php echo $rest;?>)>)/gi, "$1\n\n");
       pee = pee.replace(/\r\n|\r/g, "\n");
       pee = pee.replace(/\n\s*\n+/g, "\n\n");
       pee = pee.replace(/([\s\S]+?)\n\n/gm, '<p>$1 </p>\n');
       pee = pee.replace(/<p>\s*?<\/p>/gi, '');
       pee = pee.replace(/(<p>)*\s*(<\/?(?:<?php echo $rest;?>|hr)[^>]*>)\s*(<\/p>)*/gi, "$2");
       pee = pee.replace(/<p>(<li.+?)<\/p>/i, "$1");
       pee = pee.replace(/<p><blockquote([^>]*)>/gi, "<blockquote$1><p>");
       pee = pee.replace(/<\/blockquote><\/p>/gi, '</p></blockquote>');
       pee = pee.replace(/\s*\n/gi, " <br />\n");
       pee = pee.replace(/(<\/?(?:<?php echo $rest;?>)[^>]*>)\s*<br \/>/gi, "$1");
       pee = pee.replace(/'<br \/>(\s*<\/?(?:p|li|div|dl|dd|dt|th|pre|td|ul|ol)>)/gi, '$1');
       pee = pee.replace(/^((?:&nbsp;)*)\s/gm, '$1&nbsp;');

     return pee;
}

function inap_update_preview(pid){
	var comment = '';
		try{	comment = document.getElementById('comment_'+pid).value;}catch(e){}
		if(comment != ''){
			comment = inap_js_wpautop(comment);
			comment = inap_js_wptexturize(comment);
			comment = inap_cleanhtml(comment);
			<?php if($inapall['live_preview_smilies'] == 1){ echo "comment = inap_convertsmilies(comment);"; }?>
				<?php if($inapall['live_preview_html'] == 1){ echo "comment = comment.replace(/</g, '&lt;'); comment = comment.replace(/>/g, '&gt;');"; }?>
			document.getElementById('add_comment_live_preview_'+pid).innerHTML = comment;
		}

}

function inap_live_preview(pid) {
	if(delay >= 0){
		inap_update_preview(pid);
		delay = 0;
	}else{
		delay++;
	}
}
