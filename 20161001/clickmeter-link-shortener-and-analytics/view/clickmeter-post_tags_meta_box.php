<html>
<script type="text/javascript">

jQuery("input[name=post_title]").keyup(function() {
	var title = jQuery("input[name=post_title]").val();
	jQuery("#clickmeter_post_title").attr('value', title);
});

function cm_select_text() {
	jQuery(".cm_select").prev().select();
}
function copyToClipboard(){
	var copyDialog = jQuery("#cm_dialog").dialog({                   
        'dialogClass'   : 'wp-dialog',           
        'modal'         : true,
        'autoOpen'      : false, 
        'closeOnEscape' : true,
        'height': 160, 
        'width': 450
    });
    copyDialog.dialog('open');
}

function deleteTLWarning(){
	    var deleteDialog = jQuery("#dialog_delete").dialog({                   
	        'dialogClass'   : 'wp-dialog',           
	        'modal'         : true,
	        'autoOpen'      : false, 
	        'closeOnEscape' : true,
	        'height': 160, 
	        'width': 450
	    });
	    deleteDialog.dialog('open');
}
function confirmTLDelete(){
	jQuery("#dialog_delete").dialog("close");
	jQuery("#cm_delete_form").submit();
}
function dialogClose(){
	jQuery("#dialog_delete").dialog("close");
}

</script>

<body>
	<?php
	$api_key=WPClickmeter::get_option('clickmeter_api_key');
	$tracking_link_id;
	$tracking_link="";
	$post_id = $_GET["post"];
	$post = get_post($post_id);
	$post_title = $post->post_title;
	$permalink = get_permalink($post_id);
	$group_id_TP = WPClickmeter::get_option('clickmeter_TPcampaign_id');
	$group_id_TL = WPClickmeter::get_option('clickmeter_TLcampaign_id');

	//get information about this post's tracking link if exist
	$link_data = WPClickmeter::get_link($post_id);
	if($link_data!=null){
		$tracking_link_id = $link_data[tracking_link_id];
        $tracking_link_domain = $link_data[domain_id];
        $tracking_link_campaign = $link_data[campaign_id];
        $tracking_link_name = $link_data[link_name];
        $tracking_link_rid = $link_data[link_rid];
	}

	if($tracking_link_id!=null){
		$this_post_link = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/'.$tracking_link_id,'GET', null, $api_key);
		if($this_post_link[errors]!=null || $this_post_link[status]==3){
			WPClickmeter::delete_link($post_id, $tracking_link_id);
			$tracking_link_id = null;
		} 
	}

	//get information about this post's tracking pixel if exist
	$tracking_pixel_id = null;
	$pixel_data = WPClickmeter::get_pixel($post_id);
	if($pixel_data!=null) $tracking_pixel_id = $pixel_data[pixel_id];

	if($tracking_pixel_id!=null){
		$this_post_pixel = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/'.$tracking_pixel_id,'GET', null, $api_key);
		if($this_post_pixel[errors]!=null  || $this_post_pixel[status]==3){
			WPClickmeter::delete_pixel($post_id);
			$tracking_pixel_id = null;
		} 
	}

	//Get domain data
	$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/domains/'.$tracking_link_domain, 'GET', NULL, $api_key);
	$tracking_link_domain_name = $json_output[name];
	$tracking_link .= $tracking_link_domain_name.'/'. $tracking_link_rid ."-". $tracking_link_name;

	$default_domainId = WPClickmeter::get_option("clickmeter_default_domainId");
	if($default_domainId == null) $default_domainId = $clickmeter_domains[0][id];
	$default_domainName = WPClickmeter::get_option("clickmeter_default_domainName");
	if($default_domainName == null) $default_domainName = $clickmeter_domains[0][name];
	
	//Tracking link delete management
	if($_POST["tracking_link_delete"]=="delete"){
		$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/'.$tracking_link_id, 'DELETE', NULL, $api_key);
		WPClickmeter::delete_link($post_id, $tracking_link_id);
		$tracking_link_id = NULL;
	}

	//Get statistics about view and conversions
	$views = 0;
	$conversions_count = 0;
	$conversion1enabled = false;
	$conversion2enabled = false;
	if($tracking_pixel_id != NULL){
        $plan_type = WPClickmeter::get_option( 'clickmeter_plan_type');
        if($plan_type == "Small") {
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/'.$tracking_pixel_id.'/aggregated?hourly=false&timeframe=last30', 'GET', NULL, $api_key);
        }
        else{
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/' . $tracking_pixel_id . '/aggregated?hourly=false&timeframe=beginning', 'GET', NULL, $api_key);
        }
		if(!empty($json_output)){
			$views = $json_output[totalViews];
		}
        if($plan_type != "Small") {
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/' . $tracking_pixel_id . '/aggregated?hourly=true&timeframe=last90', 'GET', NULL, $api_key);
        }
		if(!empty($json_output) && array_key_exists("convertedClicks", $json_output)){
			$conversions_count = $json_output[convertedClicks];
		}

		$conversion1_id = WPClickmeter::get_option('clickmeter_conversionId1');
		$conversion2_id = WPClickmeter::get_option('clickmeter_conversionId2');
		if ($conversion1_id!=null){
			$conversion1enabled = true;
			$conversion1_name = WPClickmeter::get_option("clickmeter_conversionName1");
			$pos = strrpos($conversion1_name, "-");
			$conversion1_name = substr($conversion1_name, $pos+1, strlen($conversion1_name));
			$conversion1_name = ucfirst ( $conversion1_name );
		} 
		if ($conversion2_id!=null){
			$conversion2enabled = true;
			$conversion2_name = WPClickmeter::get_option("clickmeter_conversionName2");
			$pos = strrpos($conversion2_name, "-");
			$conversion2_name = substr($conversion2_name, $pos+1, strlen($conversion2_name));
			$conversion2_name = ucfirst ( $conversion2_name );
		} 
	}

	$boGoVal = WPClickmeter::get_option('clickmeter_backOffice_key');

	//add_thickbox();

	?>
	
<?php if ($post->post_status == "auto-draft") : //CREATE NEW POST BOX?>
	<p>After your post is created, this box will contain reports about visits.</p>
	<input type="hidden" id="clickmeter_save_post" name="clickmeter_save_post" value="true">
	
<?php elseif($tracking_link_id!=null) : //UPDATE POST BOX WITH TRACKING LINK?>
	<?php
		if($tracking_pixel_id!=null){
			echo '<p><div class="dashicons dashicons-visibility" style="color:#888;padding-right:4px;"></div>Visits: <strong>'.$views.'</strong> (<a title="View visits details" target="blank" href="http://my.clickmeter.com/go?val='.$boGoVal.'&returnUrl=%2FLinks%3FlinkId%3D'.$tracking_pixel_id.'">Stats</a>)</p>';
			echo '<p><div class="dashicons dashicons-cart" style="color:#888;padding-right:4px;"></div>Conversions: <strong>'.$conversions_count.'</strong></p>';
		}else{
			echo '<p>Views on this posts are not tracked. Activate tracking in the <a target="_blank" href="'.esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-account.php#clickmeter_track_views'), admin_url('admin.php'))).'">settings page</a></p>';
		}
	 ?>
	<hr>
	<p style="margin: 4px;">Tracking Link for this post:</p>
	<?php 
		$tl_label = $tracking_link;
		if(strlen($tl_label)>30){
			$tl_label = substr($tl_label, 0, 30).'...';
		}
	?>
	<strong><a id="clickmeter_tracking_code" target="_blank" href="<?php echo 'http://'.$tracking_link ?>"><?php echo $tl_label ?></a></strong><br>
	<input type="hidden" id="tracking_link_id" name="tracking_link_id" value="<?php echo $tracking_link_id ?>">
	<input type="hidden" id="boGoVal" name="boGoVal" value="<?php echo $boGoVal ?>">
	<input type="hidden" id="tracking_pixel_id" name="tracking_pixel_id" value="<?php echo $tracking_pixel_id ?>">
	<input type="hidden" id="tracking_link_name" name="tracking_link_name" value="<?php echo $tracking_link_name ?>">
	<input type="hidden" id="tracking_link_campaign" name="tracking_link_campaign" value="<?php echo $tracking_link_campaign ?>">
	<input type="hidden" id="domainId" name="domainId" value="<?php echo $tracking_link_domain ?>">
	<input type="hidden" id="clickmeter_update_post" name="clickmeter_update_post" value="true">
	<input type="hidden" id="clickmeter_settings_link" name="clickmeter_settings_link" value="<?php echo esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-account.php'), admin_url('admin.php'))); ?>">
	<form action="" method="post"></form>
	<form id="cm_delete_form" action="" method="post">
		<input type="hidden" value="delete" name="tracking_link_delete">
		<input title="Delete tracking link" type="button" class="link_button" style="color:#a00" onclick="deleteTLWarning()" value="Delete"> |
		<a title="Edit tracking link on ClickMeter" target="blank" href="http://my.clickmeter.com/go?val=<?php echo $boGoVal ?>&returnUrl=%2Flinks%2Fedit%2F<?php echo $tracking_link_id ?>">Edit</a> |
		<a title="View tracking link stats on ClickMeter" target="blank" href="http://my.clickmeter.com/go?val=<?php echo $boGoVal ?>&returnUrl=%2FLinks%3FlinkId%3D<?php echo $tracking_link_id ?>">Stats</a> |
		<a style="text-decoration:underline" title="Get QR code" target="_blank" href="http://<?php echo $tracking_link ?>.qr">QR</a> |
		<input title="Copy to clipboard" type="button" class="link_button" value="Copy" onclick="copyToClipboard()"/> |
		<a title="Send an email with this tracking link" target="blank" href="mailto:?subject=<?php echo $post_title; ?>&body=I’d like to share this article with you: http://<?php echo $tracking_link; ?>">Email</a>
	</form>	
	<div id="cm_dialog" style="display:none" title="Copy to clipboard">
		<p style="color:grey"><i>Select URL than type CTRL + C to copy</i></p>
    	<input style="width: 80%;" type="text" value="<?php echo $tracking_link; ?>"/>
    	<button type="button" class="cm_select" onclick="cm_select_text()">Select</button>
	</div>
	<div id="dialog_delete" style="display:none" title="Delete Tracking Link">
		<p>You are going to remove this Tracking Link. Continue?</p>
	    <center>
	     	<input type="button" class="clickmeter-button-grey" value="Yes" style="padding-right:5px;" onclick="confirmTLDelete()"/>
	     	<input type="button" class="clickmeter-button" value="No" onclick="dialogClose()"/>
		</center>
	</div>
	
<?php else : //UPDATE POST BOX NO TRACKING LINK?>
	<?php
		if($tracking_pixel_id!=null){
			echo '<p><div class="dashicons dashicons-visibility" style="color:#888;padding-right:4px;"></div>Visits: <strong>'.$views.'</strong> (<a title="View visits details" target="blank" href="http://my.clickmeter.com/go?val='.$boGoVal.'&returnUrl=%2FLinks%3FlinkId%3D'.$tracking_pixel_id.'">Stats</a>)</p>';
			echo '<p><div class="dashicons dashicons-cart" style="color:#888;padding-right:4px;"></div>Conversions: <strong>'.$conversions_count.'</strong></p>';
			echo '<input type="hidden" id="tracking_pixel_id" name="tracking_pixel_id" value="'.$tracking_pixel_id.'">';
		}else{
			echo '<p>Views on this posts are not tracked. Activate tracking in the <a target="_blank" href="'.esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-account.php#clickmeter_track_views'), admin_url('admin.php'))).'">settings page</a></p>';
		}
	 ?>
	<hr>
	<input type="hidden" id="domainId" name="domainId" value="<?php echo $default_domainId ?>">
	<input type="hidden" id="boGoVal" name="boGoVal" value="<?php echo $boGoVal ?>">
	<input type="hidden" id="post_url" value="<?php echo $permalink ?>" name="post_url">
	<input type="hidden" id="cm_post_id" value="<?php echo $post_id ?>" name="cm_post_id">
	<input type="hidden" id="clickmeter_post_title" value="<?php echo $post_title ?>" name="clickmeter_post_title">
	<input type="hidden" id="clickmeter_update_post" name="clickmeter_update_post" value="true">
	<input type="hidden" id="clickmeter_settings_link" name="clickmeter_settings_link" value="<?php echo esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-account.php'), admin_url('admin.php'))); ?>">
	<div id="get_tracking_link_button">
		<input type="button" onclick="callAjax_create_trackinglink()" id="get_tracking_link" class="clickmeter-button-wpstyle" value="Get Tracking Link"/>
		<div id="creating_tl" style="padding: 5px 0px 5px 0px; display:none">
			<table>
				<tr><td><div class="spinner_cm"></div></td><td><span>Please wait some seconds</span></td></tr>
			</table>
		</div>
	</div>
	<p id="tl_creation_success" style="margin: 4px;display:none">Creation successfull. Reload this page or click update to access the tracking link.</p>

<?php endif; ?>
</body>
</html>