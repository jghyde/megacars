<html>
<script type="text/javascript">
function removeApiKeyWarning(){
	    var removeDialog = jQuery("#remove_apikey_popup").dialog({                   
	        'dialogClass'   : 'wp-dialog',           
	        'modal'         : true,
	        'autoOpen'      : false, 
	        'closeOnEscape' : true,
	        'height': 250, 
	        'width': 450
	    });
	    removeDialog.dialog('open');
}
function confirmDelete(){
	jQuery("#remove_apikey_popup").dialog("close");
	jQuery("#remove_apikey_form").submit();
}
function dialogClose(){
	jQuery("#remove_apikey_popup").dialog("close");
}

</script>
<body>
	<?php require_once( plugin_dir_path( __FILE__ ) . '../account_functions.php'); ?>

	<?php if (empty($api_key)) : ?>
	<div style="background-color:#E0E0E0;padding-top: 1px;padding-left: 5px;padding-right: 5px;padding-bottom: 7px;">
		<table style="width:100%">
			<tr>
				<td style="width:7%;padding-left: 5px;padding-top: 15px;">
					<img src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/logo.png" alt="clickmeter logo"></img>
				</td>
				<td style="width:92%">
					<h1 style="margin-bottom: 5px">ClickMeter Link Shortener and Analytics‏</h1>
		<div>
			<span>You will be able to track all visits and shares of your articles. <br>To benefit from this plugin you need a ClickMeter Subscription:</span>
			<a style="padding-left:2px" href="http://support.clickmeter.com/home" target="_blank">Learn more</a>
			-
			<a href="http://clickmeter.com/pricing-and-signup" target="_blank">Subscibe for free</a>
		</div>
				</td>
				<td style="width:10%;padding-right: 10px;padding-top: 12px;">
					<a target="_blank" href="http://clickmeter.com/"><img src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/logo-powerdby.gif" alt="clickmeter logo"></img></a>
				</td>
			</tr>
		</table>

	</div>
	<br><br>
	<form action="" id="init_form" method="post">
		<p>Please enter your ClickMeter API key here to activate the plugin.</p>
		<table>
			<tr>
				<td style="padding-right:20px;">
					<input style="width:300px"type="text" name="API_key" value="<?php echo $api_key;?>"><span class="error"><?php echo $apikeyErr;?></span>
					<input type="hidden" value="false" name="startup_create_TP" id="startup_create_TP"/>
				</td>
				<td style="padding-right:20px">
					<div id="init_popup" style="display:none;">
					     <p>
					     	Do you want to enable tracking on all your posts (recommended)?<br> A tracking pixel for posts and pages will be created. This operation could take some minutes.
					     </p>
					     <center>
						     <input type="button" class="clickmeter-button" value="Yes" onclick="startupEnableTP()"/>
						     <input type="button" class="clickmeter-button-grey" value="No" style="padding-left:5px;" onclick="startupDisableTP()"/>
					     </center>
					</div>
					<!--<a href="#TB_inline?width=200&height=120&inlineId=init_popup" title="<center>Track all your posts and pages</center>" id="save_api_key" class="clickmeter_link thickbox">
						<span style="font-size: 12px; padding: 5px 5px;width:100px;" class="clickmeter-button">Save API key</span>
					</a>-->
					<input type="submit" style="padding: 5px 5px;width:150px;" class='clickmeter-button' value="Save API key">
				</td>
				<td><a target="_blank" href="http://my.clickmeter.com/api-key">Retrieve API-Key</a></td>
			</tr>
		</table>					
		<br>
	</form>
	<table style="width:50%">
		<tr>
			<td style="width: 60%"><h3>Don't have a ClickMeter account yet?</h3></td>
			<td style="width: 40%"><a class="clickmeter_link" target="blank" href="http://clickmeter.com/pricing-signup"><div style="padding: 5px 5px;width:30%;" class="clickmeter-button">Sign Up</div></a></td>
		</tr>
	</table>
</div>
<?php else : ?>
	<div style="background-color:#E0E0E0;padding-top: 5px;padding-left: 5px;padding-right: 5px;padding-bottom: 5px;">
		<table style="width:100%">
			<tr>
				<td style="width:7%;padding-left: 5px;">
					<img src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/logo.png" alt="clickmeter logo"></img>
				</td>
				<td style="width:92%"><h1>ClickMeter Link Shortener and Analytics‏</h1><h2>Welcome <a target='blank' href='http://my.clickmeter.com/go?val=<?php echo $boGoVal; ?>&returnUrl=%2Fmy-data'><?php echo $username?></a></h2>
				</td>
				<td style="width:10%;padding-right: 10px;">
					<a target="_blank" href="http://clickmeter.com/"><img src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/logo-powerdby.gif" alt="clickmeter logo"></img></a>
				</td>
			</tr>
		</table>
	</div>
	<hr>
	<div>
		<br>
		<h2>Your ClickMeter account info</h2>
		<span>Plan type: <strong><?php echo $plan_type ?></strong></span><br>
		<?php 

		if($used_datapoints<=($maximumDatapoints/100*50)) 
			echo '<span>Available datapoints: </span><span id="green_events"><strong>'.number_format($maximumDatapoints-$used_datapoints).'</strong></span><br>'; 
		if($used_datapoints>($maximumDatapoints/100*50) and $used_datapoints<=($maximumDatapoints/100*75))
			echo '<span>Available datapoints: </span><span id="orange_events"><strong>'.number_format($maximumDatapoints-$used_datapoints).'</strong></span><br>'; 
		if($used_datapoints>($maximumDatapoints/100*75)) 
			echo '<span>Available datapoints: </span><span id="red_events"><strong>'.number_format($maximumDatapoints-$used_datapoints).'</strong><span style="padding-left:20px">You\'re available datapoints are about to finish! If you want ClickMeter to keep working on your site, upgrade your plan!</span></span><br>';
		?>
		<?php 
		if($usedMonthlyEvents<=($monthlyEvents/100*50)) 
			echo '<span>Monthly events available: </span><span id="green_events"><strong>'.number_format($monthlyEvents - $usedMonthlyEvents).'</strong></span><br>'; 
		if($usedMonthlyEvents>($monthlyEvents/100*50) and $usedMonthlyEvents<=($monthlyEvents/100*75))
			echo '<span>Monthly events available: </span><span id="orange_events"><strong>'.number_format($monthlyEvents - $usedMonthlyEvents).'</strong></span><br>'; 
		if($usedMonthlyEvents>($monthlyEvents/100*75)) 
			echo '<span>Monthly events available: </span><span id="red_events"><strong>'.number_format($monthlyEvents - $usedMonthlyEvents).'</strong><span style="padding-left:20px">You\'re available events are about to finish! If you want to keep ClickMeter to keep working on your site, upgrade your plan!</span></span><br>';
		?>

		<form action="<?php echo esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php'), admin_url('admin.php'))); ?>" method="post" id="remove_apikey_form">
			<span style="padding-right:10px">Your API Key is: </span><span style="padding-right:20px"><strong><?php echo hideAPIKey($api_key); ?></strong></span>
			<input type="hidden" value="delete" name="API_key_delete">
			<div id="remove_apikey_popup" style="display:none;" title="Remove API-Key">
			     <p>
			     	You are going to remove this API-Key. Continue?
			     </p>
				<span class="error">ATTENTION!!! Removing API key will cause deletion of all data saved in your WordPress blog about ClickMeter’s plugin, and your posts will no more be tracked. Furthermore, tracking links created with WordPress domain will stop working.</span><br><br>
			     <center>
			     	<input type="button" class="clickmeter-button-grey" value="Yes" style="padding-right:5px;" onclick="confirmDelete()"/>
			     	<input type="button" class="clickmeter-button" value="No" onclick="dialogClose()"/>
			     </center>
			</div>
			<?php if($workInProgress!="true") : ?>
			<input title="Remove your API-Key" type="button" class="link_button" onclick="removeApiKeyWarning()" value="Remove API-Key">
			<?php else : ?>
				<div style="padding: 10px 0px 10px 0px"><span style="color:green;"><div class="spinner_wp"></div>A background operation is currently in execution. Wait some minutes, then click on ClickMeter menu to reload.</span></div>
			<?php endif; ?>
		</form>

		<br>
		<a class="clickmeter_link" target="blank" href="http://my.clickmeter.com/go?val=<?php echo $boGoVal; ?>&returnUrl=%2Fmy-plan"><span style="padding: 5px 5px;width:20%;" class="clickmeter-button">Upgrade your account</span></a>

	<input type="hidden" id="cm_available_datapoints" value="<?php echo $available_datapoints; ?>" />
	<input type="hidden" id="cm_already_created_pixel" value="<?php echo $already_created_pixel; ?>" />

	</div>	
	<br><hr>
	<div id="available_dp_dialog" style="display:none" title="Warning!">
		<p>You don't have enough datapoints available to complete this operation. </p><br>
		<center><a class="clickmeter_link" target="blank" href="http://my.clickmeter.com/go?val=<?php echo $boGoVal; ?>&returnUrl=%2Fmy-plan"><span style="padding: 5px 5px;width:20%;" class="clickmeter-button">Upgrade your account</span></a></center>
	</div>
	<h2>Track views on your posts and pages: <a style="display:none;cursor:pointer;" class="compress_section">[collapse-]</a><a style="cursor:pointer;" class="expand_section">[expand+]</a></h2>
	<div id="clickmeter_track_views" style="display:none;">
	<p>You will know how many times each post/page has been viewed, from where and when. <br>
		A new tracking pixel for each post/page will be created on your ClickMeter account and automatically placed in the html layer.</p>

	<form id="tracking_pixel_form" action="<?php echo esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php'), admin_url('admin.php'))); ?>" method="post">
		<table style="width:80%">
			<tr>
				<td style="padding-bottom:20px;">
					<span style="padding-right:10px;"><strong>Activate post's tracking?</strong></span>
					<?php if ($pixel_value==1) : ?>
					<span style="padding-right: 10px"><input type="radio" name="pixels_flags" checked="true" value="true">Yes</span>
					<input type="radio" name="pixels_flags" value="false">No
				<?php else : ?>
				<span style="padding-right: 10px"><input class type="radio" name="pixels_flags" value="true">Yes</span>
				<input type="radio" name="pixels_flags" checked="true" value="false">No
			<?php endif; ?>
		</td>
	</tr>
	<tr id="tracking_pixel_box">
		<td style="padding-bottom:20px;">
			<span style="padding-right:20px;"><strong>Posts you are currently tracking:</strong></span>
			<div style="float:right">
				<input type="button" class="link_button" onclick="selectAllTPPosts()" value="Select all"/>
				<input style="padding-left:10px" type="button" class="link_button" onclick="deselectAllTPPosts()" value="Deselect all"/>
			</div>
			<br>
			<div class="multiselect" id="multiselect_posts">
				<?php

                $posts = WPClickmeter::retrieve_id_title_posts();
                foreach ($posts as $post) {
                    if (in_array($post->ID, $inclusion_list)) {
						if (empty($post->post_title)){
							echo '<label class="multiselect-on"><input type="checkbox" name="included_list[]" checked value="' . $post->ID . '"/>' . strval($post->ID) . '</label>';
						}
						else {
                        	echo '<label class="multiselect-on"><input type="checkbox" name="included_list[]" checked value="' . $post->ID . '"/>' . $post->post_title . '</label>';
						}
                    } else{
                    	if (empty($post->post_title)){
                        	echo '<label><input type="checkbox" name="excluded_list[]" value="' . $post->ID . '"/>' . strval($post->ID) . '</label>';
						}
						else {
							echo '<label><input type="checkbox" name="excluded_list[]" value="' . $post->ID . '"/>' . $post->post_title . '</label>';
						}
                    }
                }
				?>
			</div>
		</td>

		<td style="padding-left:20px;padding-bottom:20px;">
			<span style="padding-right:20px;"><strong>Pages you are currently tracking:</strong></span>
			<div style="float:right">
				<input type="button" class="link_button" onclick="selectAllTPPages()" value="Select all"/>
				<input style="padding-left:10px" type="button" class="link_button" onclick="deselectAllTPPages()" value="Deselect all"/>
			</div>
			<br>
			<div class="multiselect" id="multiselect_pages">
                <?php
                $pages = WPClickmeter::retrieve_id_title_pages();
                foreach ($pages as $page) {
                    if (in_array($page->ID, $inclusion_list)) {
                        echo '<label class="multiselect-on"><input type="checkbox" name="included_list[]" checked value="' . $page->ID . '"/>' . $page->post_title . '</label>';
                    } elseif (in_array($page->ID, $exclusion_list)) {
                        echo '<label><input type="checkbox" name="excluded_list[]" value="' . $page->ID . '"/>' . $page->post_title . '</label>';
                    } else {
                        echo '<label><input type="checkbox" name="excluded_list[]" value="' . $page->ID . '"/>' . $page->post_title . '</label>';
                    }
                }
                ?>
			</div>
		</td>
</tr>
<tr id="include_default_pixel">
	<td>
		<span style="padding-right: 10px"><strong>Automatically track new posts/pages</strong></span>
	<?php if ($pixel_default_value==1) : ?>
		<span style="padding-right: 10px"><input type="radio" name="new_article_default" checked="true" value="true">Yes (default)</span>
		<input type="radio" name="new_article_default" value="false">No
	<?php else : ?>
		<span style="padding-right: 10px"><input class type="radio" name="new_article_default" value="true">Yes (default)</span>
		<input type="radio" name="new_article_default" checked="true" value="false">No
	<?php endif; ?>
</td>
</tr>
</table>
<br>
<?php if($workInProgress!="true") : ?>
	<input type="submit" style="padding: 5px 5px;width:120px;" onclick="changeSelectedStatus()" class='clickmeter-button' value="Save changes">
<?php else : ?>
	<div style="padding: 10px 0px 10px 0px"><span style="color:green;"><div class="spinner_wp"></div>A background operation is currently in execution. Wait some minutes, then click on ClickMeter menu to reload.</span></div>
<?php endif; ?>
</form>	
</div>


<br><hr>
<h2>Track conversions: <a style="display:none;cursor:pointer;" class="compress_section">[collapse-]</a><a style="cursor:pointer;" class="expand_section">[expand+]</a></h2>
<div id="clickmeter_track_conversions" style="display:none;">
<p>How many conversions were lead by your WordPress blog?</p><br>

<form id="conversion1_form" action="<?php echo esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php'), admin_url('admin.php'))); ?>" method="post">
	<?php if($workInProgress!="true") : ?>
	<select id="conversion1_type" name="conversion_type" style="width:200px;">
		<option value="null" selected>Choose conversion type</option>
		<optgroup label="Create new conversion">
			<option value="purchases">Purchases</option>
			<option value="subscriptions">Subscriptions</option>
			<option value="downloads">Downloads</option>
			<option value="other_conversions">Other conversions</option>
		</optgroup>
		<optgroup label="..or choose existing conversion">
			<?php 
			foreach ($active_conversions[entities] as $conversion) {
				echo '<option value="existing_conversion.'.$conversion[id].'">'.$conversion[name].'</option>';
			}
			?>
		</optgroup>
	</select>
	<span style="padding-left:10px; padding-right:5px;">Posts</span><input type="checkbox" checked name="conversion_target_list[]" value="post" id="conversion1_target_posts"/>
	<span style="padding-right:5px;">Pages</span><input type="checkbox" checked name="conversion_target_list[]" value="page" id="conversion1_target_pages"/>
	<span style="padding-left: 10px"><input type="submit" style="font-size: 13px; padding: 4px;width:120px;" class="clickmeter-button" value="Activate"></span>
	<span class="error conversion_err"></span>
	<span class="tooltip" style="padding-right:5px;"><img style="margin-left: 10px;" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/tooltip_icon.png"><span><img class="callout" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/callout_black.gif" />
		 Check the boxes to associate this conversions to all tracked Posts and/or Pages in this WordPress account.
	</span></span>

<?php else : ?>
	<div style="padding: 10px 0px 10px 0px"><span style="color:green;"><div class="spinner_wp"></div>A background operation is currently in execution. Wait some minutes, then click on ClickMeter menu to reload.</span></div>
<?php endif; ?>
</form>
<div id="conversion1_created">
	<input id="conversion1_id" type="hidden" value="<?php echo $conversion1_id; ?>"/>
	<p>Copy the conversion code snippet and paste it inside the body tag of the confirmation page (also called thank you page or sign up completion page).</p>
	<table>
		<tr>
			<td><strong><span style="padding-right:20px">
				<?php 
					echo $conversion1_name; 
					if(sizeof($conversion1_target)==1){
						if(in_array("page", $conversion1_target)) echo " (Pages only)";
						elseif(in_array("post", $conversion1_target)) echo " (Posts only)";
					}
				?>
			</span></strong></td>
			<td><a style="padding-right:20px" target="_blank" href="http://my.clickmeter.com/go?val=<?php echo $boGoVal; ?>&returnUrl=%2FConversions%2FConversionConfirm%3FconversionId%3D<?php echo $conversion1_id; ?>%26codeView%3Dtrue">View conversion code</a></td>
			<td>
				<form action="<?php echo esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php'), admin_url('admin.php'))); ?>" method="post" id="first_conv_form" >
					<input type="hidden" value="1" name="conversion_delete">
					<div id="remove_firstconv_popup" style="display:none;">
					     <p>
					     	You are going to delete this conversion. Continue?
					     </p>
					     <center>
					     	<input type="button" class="clickmeter-button" value="Yes" style="padding-right:5px;border: 1px solid #8B8686;background-image: -webkit-linear-gradient(top, #8B8686, #8B8686);" onclick="confirmDeleteFirstConv()"/>
					     	<input type="button" class="clickmeter-button" value="No" onclick="tb_remove()"/>
					     </center>
					</div>
					<?php if($workInProgress!="true") : ?>
						<?php if($conversion2_id==null) : ?>
							<a href="#TB_inline?width=200&height=100&inlineId=remove_firstconv_popup" title="<center><strong>Remove conversion</strong></center>" id="remove_secondconv" class="thickbox">Remove</a>
						<?php endif; ?>
					<?php else : ?>
						<div style="padding: 10px 0px 10px 0px"><span style="color:green;"><div class="spinner_wp"></div>A background operation is currently in execution. Wait some minutes, then click on ClickMeter menu to reload.</span></div>
					<?php endif; ?>
				</form>
			</td>
		</tr>
	</table>
</div><br>
<form id="conversion2_form" action="<?php echo esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php'), admin_url('admin.php'))); ?>" method="post">
<?php if($workInProgress!="true") : ?>
	<select id="conversion2_type" name="conversion_type" style="width:200px;">
		<option value="null" selected>Choose conversion type</option>
		<optgroup label="Create new conversion">
			<option value="purchases">Purchases</option>
			<option value="subscriptions">Subscriptions</option>
			<option value="downloads">Downloads</option>
			<option value="other_conversions">Other conversions</option>
		</optgroup>
		<optgroup label="..or choose existing conversion">
			<?php 
			foreach ($active_conversions[entities] as $conversion) {
				if($conversion[id] != $conversion1_id) echo '<option value="existing_conversion.'.$conversion[id].'">'.$conversion[name].'</option>';
			}
			?>
		</optgroup>
	</select>
	<span style="padding-left:10px;padding-right:5px;">Posts</span><input type="checkbox" checked name="conversion_target_list[]" id="conversion2_target_posts" value="post"/>
	<span style="padding-right:5px;">Pages</span><input type="checkbox" checked name="conversion_target_list[]" id="conversion2_target_pages" value="page"/>
	<span style="padding-left: 10px"><input type="submit" style="font-size: 13px; padding: 4px;width:120px;" class="clickmeter-button" value="Activate"></span>
	<span class="error conversion_err"></span>
	<span class="tooltip" style="padding-right:5px;"><img style="margin-left: 10px;" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/tooltip_icon.png"><span><img class="callout" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/callout_black.gif" />
		 Check the boxes to associate this conversions to all tracked Posts and/or Pages in this WordPress account.
	</span></span>
<?php else : ?>
	<div style="padding: 10px 0px 10px 0px"><span style="color:green;"><div class="spinner_wp"></div>A background operation is currently in execution. Wait some minutes, then click on ClickMeter menu to reload.</span></div>
<?php endif; ?>
</form>
<div id="conversion2_created">
	<input id="conversion2_id" type="hidden" value="<?php echo $conversion2_id; ?>"/>
	<table>
		<tr>
			<td><strong><span style="padding-right:20px">
				<?php 
					echo $conversion2_name; 
					if(sizeof($conversion2_target)==1){
						if(in_array("page", $conversion2_target)) echo " (Pages only)";
						elseif(in_array("post", $conversion2_target)) echo " (Posts only)";
					}
					?>
			</span></strong></td>
			<td><a style="padding-right:20px" target="_blank" href="http://my.clickmeter.com/go?val=<?php echo $boGoVal; ?>&returnUrl=%2FConversions%2FConversionConfirm%3FconversionId%3D<?php echo $conversion2_id; ?>%26codeView%3Dtrue">View conversion code</a></td>
			<td>
				<form action="<?php echo esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php'), admin_url('admin.php'))); ?>" method="post" id="second_conv_form" >
					<input type="hidden" value="2" name="conversion_delete">
					<div id="remove_secondconv_popup" style="display:none;">
					     <p>
					     	You are going to delete this conversion. Continue?
					     </p>
					     <center>
					     	<input type="button" class="clickmeter-button" value="Yes" style="padding-right:5px;border: 1px solid #8B8686;background-image: -webkit-linear-gradient(top, #8B8686, #8B8686);" onclick="confirmDeleteSecondConv()"/>
					     	<input type="button" class="clickmeter-button" value="No" onclick="tb_remove()"/>
					     </center>
					</div>
					<?php if($workInProgress!="true") : ?>
					<a href="#TB_inline?width=200&height=100&inlineId=remove_secondconv_popup" title="<center><strong>Remove conversion</strong></center>" id="remove_secondconv" class="thickbox">Remove</a>
					<?php else : ?>
						<div style="padding: 10px 0px 10px 0px"><span style="color:green;"><div class="spinner_wp"></div>A background operation is currently in execution. Wait some minutes, then click on ClickMeter menu to reload.</span></div>
					<?php endif; ?>
				</form>
			</td>
		</tr>
	</table>
</div>
</div>

<br><hr>
<h2>Default short tracking links settings: <a style="display:none;cursor:pointer;" class="compress_section">[collapse-]</a><a style="cursor:pointer;" class="expand_section">[expand+]</a></h2>
<div id="tracking_link_settings" style="display:none;">
<p>These settings will be used as default to create new short tracking links. You can change them anytime.</p><br>
<form method="post" action="#tracking_link_settings">
	<span style="padding-right:5px;"><strong>Default domain: </strong></span>
	<span class="tooltip">
	<select style="width: 30%;" id="domain_list" name="domain_list">
		<option value="<?php echo $wordpress_domain_id; ?>"><?php echo $blog_name; ?></option>';
		<?php foreach ($clickmeter_domains as $domain) {
			if($domain[id] == $default_domainId){
				echo '<option selected value="'.$domain[id].'">'.$domain[name].'</option>';	
			}else{
				echo '<option value="'.$domain[id].'">'.$domain[name].'</option>';	
			}
		}?>
	</select>
	<span><img class="callout" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/callout_black.gif" />
		Choose the domain for your tracking link.
	</span></span>
<br><br>
	<span style="padding-right:5px;"><strong>Default campaign: </strong></span>
	<span class="tooltip">
	<select style="width: 30%;" id="campaign_list" name="campaign_list">
		<?php foreach ($groups as $campaign) {
			if($campaign[id] == $default_campaignId){
				if($default_campaignId == get_option('clickmeter_TLcampaign_id')){
					echo '<option selected value="'.$campaign[id].'">'.$campaign[name].' (default)'.'</option>';		
				}else{
					echo '<option selected value="'.$campaign[id].'">'.$campaign[name].'</option>';	
				}
			}else{
				echo '<option value="'.$campaign[id].'">'.$campaign[name].'</option>';	
			}
			
		}?>
	</select>
	<span><img class="callout" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/callout_black.gif" />
		Organize your links in campaigns (groups of links). Choose which campaign this tracking link belongs to.
	</span></span>
<br><br>
	<span style="padding-right:5px;"><strong>Default first conversion: </strong></span>
	<span class="tooltip">
	<select style="width: 30%;" id="first_conversion_list" name="first_conversion_list">
		<?php
		if($default_firstConv_links == "none") {
			echo '<option selected value="none">None</option>';
		}else{
			echo '<option value="none">None</option>';
		}
		foreach ($active_conversions[entities] as $conv) {
			if($conv[id] == $default_firstConv_links){
				echo '<option selected value="'.$conv[id].'">'.$conv[name].'</option>';	
			}else{
				echo '<option value="'.$conv[id].'">'.$conv[name].'</option>';	
			}
		}?>
	</select>
	<span><img class="callout" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/callout_black.gif" />
		Associate this tracking link to a conversion goal.
	</span></span>
<br><br>
<?php if($default_firstConv_links!=null && $default_firstConv_links!="none" ) : ?>
	<span style="padding-right:5px;"><strong>Default second conversion: </strong></span>
	<span class="tooltip">
	<select style="width: 30%;" id="second_conversion_list" name="second_conversion_list">
		<?php 
			if($default_secondConv_links == "none") {
				echo '<option selected value="none">None</option>';
			}else{
				echo '<option value="none">None</option>';
			}
			foreach ($active_conversions[entities] as $conv) {
				if($conv[id] == $default_secondConv_links){
					echo '<option selected value="'.$conv[id].'">'.$conv[name].'</option>';	
				}else{
					if($conv[id] != $default_firstConv_links) echo '<option value="'.$conv[id].'">'.$conv[name].'</option>';
				}
			}?>
	</select>
	<span><img class="callout" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/callout_black.gif" />
		Associate this tracking link to a conversion goal.
	</span></span>
	<br><br>
<?php endif; ?>
	<span style="padding-right:5px;"><strong>Default redirection type: </strong></span>
	<span class="tooltip">
	<select style="width: 30%;" id="redirection_type_list" name="redirection_type_list">
		<?php
			if($default_redirection_type == "301") {
				echo '<option selected value="301">301 (permanent)</option>';
				echo '<option value="307">307 (temporary)</option>';
			}elseif($default_redirection_type == "307"){
				echo '<option value="301">301 (permanent)</option>';
				echo '<option selected value="307">307 (temporary)</option>';
			}else{
				echo '<option value="301">301 (permanent)</option>';
				echo '<option value="307">307 (temporary)</option>';
			}
		?>
	</select>
	<span><img class="callout" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/callout_black.gif" />
		Choose to redirect your customers to different destination URLs depending on different conditions.
	</span>
	</span>
	<br><br>
	<span style="padding-right:5px;"><strong>Enable link cloaking by default? </strong></span>
	<?php if ($link_cloak_flag==1) : ?>
	<span style="padding-right: 10px"><input type="radio" name="link_cloak_flag" checked="true" value="true">Yes</span>
	<input type="radio" name="link_cloak_flag" value="false">No (default)
	<?php else : ?>
	<span style="padding-right: 10px"><input class type="radio" name="link_cloak_flag" value="true">Yes</span>
	<input type="radio" name="link_cloak_flag" checked="true" value="false">No (default)
	<?php endif; ?>
	<span class="tooltip" style="padding-right:5px;"><img style="margin-left: 10px;" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/tooltip_icon.png">
		<span><img class="callout" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/callout_black.gif" />
				URL cloaking allows you to hide the destination URL after someone clicks on the tracking link. If selected, this option will “encrypt” the destination URL in the source code too, so that it will not be easy to discover the affiliate ID if any.
		</span></span>
	<br><br>

	<?php if($workInProgress!="true") : ?>
		<input type="submit" style="padding: 5px 5px;width:120px;" class='clickmeter-button' value="Save changes">
	<?php else : ?>
		<div style="padding: 10px 0px 10px 0px"><span style="color:green;"><div class="spinner_wp"></div>A background operation is currently in execution. Wait some minutes, then click on ClickMeter menu to reload.</span></div>
	<?php endif; ?>
</form>
</div>

<br><hr>
<h2>Track and Manage 404 errors: <a style="display:none;cursor:pointer;" class="compress_section">[collapse-]</a><a style="cursor:pointer;" class="expand_section">[expand+]</a></h2>
<div id="manage_404_errors" style="display:none;">
<p>In case some visitor will try to access a wrong page (404 - Page Not Found) on your WordPress site. You can track it and redirect him to a specific landing (destination) page.</p><br>
<form method="post" action="#manage_404_errors">
	<span style="padding-right: 10px"><strong>Enable link tracking for your WordPress 404 errors?</strong></span>
	<?php if ($track_404_flag==1) : ?>
	<span style="padding-right: 10px"><input type="radio" name="track_404_flag" checked="true" value="true">Yes (default)</span>
	<input type="radio" name="track_404_flag" value="false">No
	<?php else : ?>
	<span style="padding-right: 10px"><input class type="radio" name="track_404_flag" value="true">Yes (default)</span>
	<input type="radio" name="track_404_flag" checked="true" value="false">No
	<?php endif; ?>
	<br><br>
	<?php if ($url_404!=null) : ?>
	<span style="padding-right:5px;" id="custom_404_page_label"><strong>Destination URL: </strong></span>
	<input style="width:30%;" type="text" id="custom_404_page" name="custom_404_page" value="<?php echo $url_404; ?>">
	<?php else : ?>
	<span style="padding-right:5px;" id="custom_404_page_label"><strong>Destination URL: </strong></span>
	<input style="width:30%;" type="text" id="custom_404_page" name="custom_404_page" value="http://clickmeter.com/404">
	<?php endif; ?>
	<br><br>
	<?php if($workInProgress!="true") : ?>
		<input type="submit" style="padding: 5px 5px;width:120px;" class='clickmeter-button' value="Save changes">
	<?php else : ?>
		<div style="padding: 10px 0px 10px 0px"><span style="color:green;"><div class="spinner_wp"></div>A background operation is currently in execution. Wait some minutes, then click on ClickMeter menu to reload.</span></div>
	<?php endif; ?>
</form>	
</div>
        <br><hr>
        <h2>Authorize bugs monitor: <a style="display:none;cursor:pointer;" class="compress_section">[collapse-]</a><a style="cursor:pointer;" class="expand_section">[expand+]</a></h2>
        <div id="manage_error_report" style="display:none;">
            <form method="post" action="#manage_error_report">
                <span style="padding-right: 10px"><strong>Send ClickMeter info about application bugs (it helps to optimize your user experience)</strong></span>
                <?php if ($error_report_flag==1) : ?>
                    <span style="padding-right: 10px"><input type="radio" name="error_report_flag" checked="true" value="true">Yes (default)</span>
                    <input type="radio" name="error_report_flag" value="false">No
                <?php else : ?>
                    <span style="padding-right: 10px"><input class type="radio" name="error_report_flag" value="true">Yes (default)</span>
                    <input type="radio" name="error_report_flag" checked="true" value="false">No
                <?php endif; ?>
                <br><br>
                <?php if($workInProgress!="true") : ?>
                    <input type="submit" style="padding: 5px 5px;width:120px;" class='clickmeter-button' value="Save changes">
                <?php else : ?>
                    <div style="padding: 10px 0px 10px 0px"><span style="color:green;"><div class="spinner_wp"></div>A background operation is currently in execution. Wait some minutes, then click on ClickMeter menu to reload.</span></div>
                <?php endif; ?>
            </form>
        </div>

<?php endif; ?>
<br>
<hr><br><br><br>
<p><i>If you're finding ClickMeter Link Shortener and Analytics‏ useful, please <a target="_blank" href="http://wordpress.org/support/view/plugin-reviews/clickmeter-link-shortener-and-analytics">rate it on the plugin's WordPress page</a>.</i></p>
</body>
<footer><!--ClickMeter.com page views tracking: WordPress Plugin Usage --> <script type='text/javascript'> var ClickMeter_pixel_url = 'http://9nl.cc/tpu5'; </script> <script type='text/javascript' id='cmpixelscript' src='https://www.clickmeter.com/js/pixel.js'></script> <noscript> <img height='0' width='0' alt='' src='http://9nl.cc/tpu5' /> </noscript></footer>
</html>