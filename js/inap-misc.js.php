<?php
function inap_the_base(){
    echo '/'.end(explode('/', str_replace(array('\\','/inap-misc.js.php','/js'),array('/',''),__FILE__)));
}
@require_once('../../../../wp-config.php');
cache_javascript_headers();
$home = get_settings('siteurl');
if (substr_count($_SERVER['HTTP_ACCEPT_ENCODING'], 'gzip')) ob_start("ob_gzhandler"); else ob_start();
include('inap-ajax.js.php');
?>
/*<script>*/
/*
Inline Ajax Page
(C) Anthologyoi.com
*/

var d=document;
var last_page = [];
var cur_page = [];
var last_show = [];
var mrc = '';
var force = 0;
var info = [];

function inap_request(the_id,the_type,show_text,hide_text,extras){
	if( the_id ){
		id =  the_id;}
	if( the_type ){
		type =  the_type;}
	if( hide_text ){
		hide =  hide_text;}
	if( show_text ){
		show =  show_text;}
		extra =  extras;

	where = 'post_'+type+'_'+id;

	dwhere = d.getElementById(where);

	if(type=='addcomment' && extra){
		if(extra.match(';')){
			info = extra.split(';');
		}
		if(isNaN(last_extra[id])){last_extra[id] = 1;}
		if(isNaN(info[0])){
			info[0] = last_extra[id];
			info[1] = 0;
		}
	}else if(type=='content'){
		if (last_page[id] === 0 || isNaN(last_page[id])){
			if(isNaN(extra)){cur_page[id] = 2;}else{cur_page[id] = extra;}
			last_page[id] = 1;
			force = 1;
		}else{
			if(isNaN(extra) && cur_page[id] == 1){extra = 2;}else if (isNaN(extra) && cur_page[id] == 2){extra = 1;}
			last_page[id] = cur_page[id];
			cur_page[id] =extra;
		}
	}

	if (dwhere.innerHTML.length === 0 || force ==1){

		<?php INAP_ajax::pick('request');?>

	}else{
		inap_toggle();
	}
}

function submit_form(the_id,the_type,show_text,hide_text,comment_hide){
	if( the_id ){
		id =  the_id;}
	if( the_type ){
		type =  the_type;}
	if( hide_text ){
		hide =  hide_text;}
	if( show_text ){
		show =  show_text;}
	if( comment_hide ){
		com_hide = comment_hide;}

		try{d.getElementById('submit_'+id).disabled = true;} catch(e){}

		base = d.getElementById('post_addcomment_'+id).getElementsByTagName('input');


	<?php if($inapall['js_library'] < 2){ ?>
		inaprequest = new sack('<?php echo $home;?>/wp-content/plugins<?php inap_the_base();?>/inap-get-info.php<?php echo $inapsuffix; ?>');
	<?php }else{?>
		var string = '';
	<?php }?>
		var x = base.length;
		var value = '';
		var name = '';
		for(i=0; i<x; i++){
			if(base[i].type != 'button'){
				if(base[i].type == 'text' || base[i].type == 'hidden' || base[i].type == 'password'){
						value =  base[i].value;
						name = base[i].name;
				}else if(base[i].type == 'checkbox'){
					if (base[i].checked) {
						value =  base[i].value;
						name = base[i].name;
					}
				}else if(base[i].type == 'select'){
						value =  base[i].value;
						name = base[i].name;
				}
		<?php if($inapall['js_library'] != 1){ ?>
				string += name+'='+value+'&';
		<?php }else{?>
				inaprequest.setVar(name, value);
		<?php }?>
			}
		}
		base = d.getElementById('post_addcomment_'+id).getElementsByTagName('textarea');
		x = base.length;

		for(i=0; i<x; i++){
			<?php if($inapall['js_library'] < 2){ ?>
				inaprequest.setVar(base[i].name, base[i].value);
			<?php }else{?>
				string += base[i].name+'='+base[i].value+'&';
			<?php }?>
		
		}

	<?php INAP_ajax::pick('submit');?>
}


function inap_paged(page,what){

	<?php INAP_ajax::pick('paged');?>
}
var last_extra = [];


function inap_toggle(){
var winHeight = window.innerHeight;
	if(!winHeight){
		//yet another IE fix
		winHeight = d.documentElement.clientHeight;
	}



	setTimeout("try{d.getElementById('throbber'+type+id).parentNode.removeChild(d.getElementById('throbber'+type+id));}catch(e){}",100);

 	if (dwhere.style.display == 'none' || force==1 || (last_extra[id] != info[0] && type == 'addcomment')){
 		style1 = 'block';
 		style2 = 'none';
		link =hide;
		var style= '_hide';
 	}else{
 		 style1 = 'none';
 		 style2 = 'block';
 		 link =show;
		var style = '';
 	}

	if(type == 'content'){
		inap_toggle_content();

		var l = d.getElementById('post_content_link'+'_'+id);
		if(show == 'off'){
			d.getElementById('post_page_'+cur_page[id]+'_'+id+'_link').style.fontWeight = 'bold';
			if(last_page[id] !=cur_page[id] ){
			d.getElementById('post_page_'+last_page[id]+'_'+id+'_link').style.fontWeight = 'normal';}
		}else{

			if(l.firstChild.data == hide){
				l.firstChild.data = show;
				l.className = 'post_content_link';
			}else{
				l.firstChild.data = hide;
				l.className = 'post_content_link_hide';
			}

		}
	}else if(type == 'addcomment'){

		inap_toggle_addcomment();

		if(info[0]){
			if(style1 == 'none'){
				d.getElementById('post_addcomment_link'+info[0]+'_'+id).firstChild.data = show;
			}else{
				try{
					if(last_show[id]){
					d.getElementById('post_addcomment_link'+last_extra[id]+'_'+id).firstChild.data = last_show[id];}
				}catch(e){}
				d.getElementById('post_addcomment_link'+info[0]+'_'+id).firstChild.data = hide;
			}
			last_show[id] = show;
			last_extra[id] = info[0];
		}
	}else{
		if(d.getElementById('post_'+type+'_link'+'_'+id)){
			d.getElementById('post_'+type+'_link'+'_'+id).className = 'post_' + type + '_link' + style;
			d.getElementById('post_'+type+'_link'+'_'+id).firstChild.data = link;
		}
		if(!force || ( force && d.getElementById(where).style.display !== "block")){
			inap_effects_pick();
		}
	}


 		force=0;
	if(type == 'comments' && mrc > 1){
		setTimeout("location.href= '#comment-'+mrc;",100);
	}
		if(dwhere.offsetTop > 0){
			window.parent.scrollTo(0,dwhere.offsetTop - winHeight/4);
		}
}

function inap_toggle_addcomment(){

		<?php if($inapall['comment_threaded']){?>
			try{d.getElementById('comment_post_parent_'+id).value = info[1];}catch(e){}
		<?php }?>

	if(style1 == 'block' && force===0){
		dwhere.style.display = 'none';
		d.getElementById('post_addcomment_link'+info[0]+'_'+id).parentNode.insertBefore(dwhere, d.getElementById('post_addcomment_link'+info[0]+'_'+id).nextSibling);

		if(dwhere.style.display != 'block'){
	//	d.getElementById('post_addcomment_link'+info[0]+'_'+id).firstChild.data = show;
			inap_effects_pick();
		}
	}else{
		style1 = 'none';
		inap_effects_pick();
	}
try{d.getElementById('submit_'+id).disabled = false;} catch(e){}
}

function inap_toggle_content(){
	var q = [];
	q[0] = 'show::post_page_'+cur_page[id]+'_'+id;
	AOI_eff.start('post_page_'+last_page[id]+'_'+id, {'mode': 'hide', 'eff': '<?php echo $inapall['do_effect'];?>', 'queue': q} );
}





function inap_effects_pick(){

	<?php if($inapall['special_effects'] && $inapall['js_library'] == '3' && $inapall['scriptaculous_show'] && $inapall['scriptaculous_hide']){?>
			
		if(style1 == 'block'){
			Effect.<?php echo $inapall['scriptaculous_show'];?>(where);
		}else{
			Effect.<?php echo $inapall['scriptaculous_hide'];?>(where);
		}
	<?php }elseif($inapall['jquery_show'] && $inapall['js_library'] == '2' && $inapall['jquery_hide']  && $inapall['special_effects'] == 1){?>

	<?php
		$show_effect = explode('-',$inapall['jquery_show']);
		$hide_effect = explode('-',$inapall['jquery_hide']);
	?>
		if(style1 == 'block'){
			jQuery('#'+where).<?php echo $show_effect[0];?>("<?php echo $show_effect[1];?>");
		}else{
			jQuery('#'+where).<?php echo $hide_effect[0];?>("<?php echo $hide_effect[1];?>");
		}
	<?php }else{?>
		AOI_eff.start(where, {'eff': '<?php echo $inapall['do_effect'];?>'});
	<?php }?>

}
function inap_loading(){

	var img = d.createElement('img');
	img.src="<?php echo $home;?>/wp-content/plugins<?php inap_the_base();?>/js/throbber.gif";
	img.alt="Please hold now loading";
	img.id = "throbber"+type+id;
	if(cur_page[id] && show == 'off'){
		try{d.getElementById('post_page_'+cur_page[id]+'_'+id+'_link').appendChild(img);}catch(e){}
	}else{
		try{d.getElementById('post_'+type+'_link_'+id).appendChild(img);}catch(e){}
	}
}

function inap_submiting(){
	var img = d.createElement('img');
	img.src="<?php echo $home;?>/wp-content/plugins<?php inap_the_base();?>/js/throbber.gif";
	img.alt="Please hold now loading";
	img.id = "throbbersubmit"+id;
		try{d.getElementById('submit_'+id).appendChild(img);}catch(e){}
}

function complete_submit(){
	setTimeout("complete_submit_2()",5);
}

function complete_submit_2(){
/*if there was an error the message is really long...so:*/
	if(d.getElementById('submit_form_'+id).innerHTML.substring( 0, 6 ) == 'ERROR:'){
		var re = new RegExp('<p>(.*?)</p>','gmi');
		
		var m = re.exec(d.getElementById('submit_form_'+id).innerHTML);
		if(m){
		d.getElementById('submit_form_'+id).innerHTML = m[1];}
	}else{
		var message = d.getElementById('submit_form_'+id).innerHTML.split('-');
		d.getElementById('submit_form_'+id).innerHTML = message[0];
		mrc = message[1];

		try{
			if (d.getElementById('post_comments_none_'+id).style.display != 'none'){
				d.getElementById('post_comments_none_'+id).style.display = 'none';
				d.getElementById('post_comments_link_'+id).innerHTML = 'Show Comments';
			}
		}catch(e){ var string ='';}

			type = 'addcomment';
			inap_request();

// Set up for the reload.
		d.getElementById('post_comments_'+id).parentNode.insertBefore(d.getElementById('post_addcomment_'+id), d.getElementById('post_comments_'+id));
		info[1] = 0;
		force = 1;
		try{
			var i = d.getElementById('post_comments_'+id).innerHTML;
			hide = com_hide;
			type = 'comments';
			inap_request();
		}catch(e){}

	}

	try{d.getElementById('submit_'+id).disabled = false;} catch(e){}
	try{d.getElementById('throbbersubmit'+id).parentNode.removeChild(d.getElementById('throbbersubmit'+id));}catch(e){}

}

function complete_paged(){
	window.parent.scrollTo(0,d.getElementById('inap_loop').offsetTop);
}


<?php

		echo '// start effects';
		include('inap-effects.js.php');
	

	if($inapall['live_preview'] == 1){
		echo '// start live preview';
		include('inap-live-preview.js.php');
	}
	if($inapall['add_comments_tags'] == 1){
		echo '// start quicktags';
		include('inap-quicktags.js');
	}
   ob_flush();

?>
