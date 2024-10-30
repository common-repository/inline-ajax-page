<?php
class INAP_ajax{

	function pick($type){
	global $inapall;

		if($inapall['js_library'] == 3 || $inapall['use_scriptaculous'] == 1){
			INAP_ajax::prototype($type);
		}elseif($inapall['js_library'] == 2){
			INAP_ajax::jquery($type);
		}else{
			INAP_ajax::sack($type);
		} 
	}

	function sack($type){
	global $home,$inapsuffix;

		switch($type){
			case 'request':
	?>
			inaprequest = new sack('<?php echo $home;?>/wp-content/plugins<?php inap_the_base();?>/inap-get-info.php<?php echo $inapsuffix; ?>');
			inaprequest.method = 'POST';
			inaprequest.setVar('id', id);
			inaprequest.setVar('type', type);
			inaprequest.setVar('extra', extra);
			inaprequest.setVar('show', show_text);
			inaprequest.setVar('hide', hide_text);
			inaprequest.element = where;
			inaprequest.onLoading = inap_loading;
			inaprequest.onCompletion = inap_toggle;
			inaprequest.runAJAX();
			inaprequest = null;


	<?php
			break;
			case 'submit':
	?>

			inaprequest.setVar('type', 'submit_form');
			inaprequest.element = 'submit_form_'+id;
			inaprequest.method = 'POST';
			inaprequest.onLoading = inap_submiting;
			inaprequest.onCompletion = complete_submit;
			inaprequest.runAJAX();
			inaprequest = null;

	<?php
			break;
			case 'paged':
	?>
			inaprequest = new sack('<?php echo $home;?>/wp-content/plugins<?php inap_the_base();?>/inap-get-info.php<?php echo $inapsuffix; ?>');
			inaprequest.method = 'POST';
			inaprequest.setVar('type', 'paged');
			inaprequest.setVar('pagenum', page);
			inaprequest.setVar('what', what);
			inaprequest.element = 'inap_loop';
			inaprequest.onCompletion = complete_paged;
			inaprequest.runAJAX();
			inaprequest = null;


	<?php
			break;
		}
	}
	function jquery($type){
	global $home,$inapsuffix;

		switch($type){
			case 'request':
	?>

			inap_loading();
			jQuery("#"+where).load("<?php echo $home;?>/wp-content/plugins<?php inap_the_base();?>/inap-get-info.php<?php echo $inapsuffix; ?>",
	{id: id,type:type,extra:extra, show:show_text, hide:hide_text},
	inap_toggle);

	<?php
			break;
			case 'submit':
	?>
			inap_submiting();

			d.getElementById('submit_form_'+id).innerHTML = jQuery.ajax({
			type: "POST",
			url: "<?php echo $home;?>/wp-content/plugins<?php inap_the_base();?>/inap-get-info.php<?php echo $inapsuffix; ?>",
			data:  'type=submit_form&'+string+'comment_post_ID='+id,
			success:complete_submit,
			async:false
			}).responseText;
	<?php
			break;
			case 'paged'
	?>

			jQuery("#"+inap_loop).load("<?php echo $home;?>/wp-content/plugins<?php inap_the_base();?>/inap-get-info.php<?php echo $inapsuffix; ?>",
			{pagenum: page,type:type, type:paged, hide:hide_text},
			complete_paged);

	<?php
			break;
		}
	}
	function prototype($type){
	global $home,$inapsuffix;

		switch($type){
			case 'request'
	?>

	var inaprequest = new Ajax.Updater(where, '<?php echo $home;?>/wp-content/plugins<?php inap_the_base();?>/inap-get-info.php<?php echo $inapsuffix; ?>',
				{
					method: 'post',
					parameters: 'id='+id+'&type='+type+'&extra='+extra+'&show='+show_text+'&hide='+hide_text,
					onLoading:inap_loading,
					onComplete:inap_toggle
				});
			inaprequest = null;

	<?php
			break;
			case 'submit'
	?>
			var inaprequest = new Ajax.Updater(
				'submit_form_'+id,
				'<?php echo $home;?>/wp-content/plugins<?php inap_the_base();?>/inap-get-info.php<?php echo $inapsuffix; ?>',
				{
					method: 'post',
					parameters: 'type=submit_form&'+string+'comment_post_ID='+id,
					onLoading:inap_submiting,
					onComplete:complete_submit

				});
			inaprequest = null;


	<?php
			break;
			case 'paged':
	?>
			var inaprequest = new Ajax.Updater(
				'inap_loop',
				'<?php echo $home;?>/wp-content/plugins<?php inap_the_base();?>/inap-get-info.php<?php echo $inapsuffix; ?>',
				{
					method: 'post',
					parameters:'pagenum='+page+'&type='+paged,
					onComplete:complete_paged

				});
			inaprequest = null;


	<?php
			break;
		}
	}

}
?>