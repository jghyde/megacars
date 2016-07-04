<html>
<script type="text/javascript">

window.onload=function(){
	jQuery("input[id=redirection_link_name]").keyup(function() {
		var url_name = jQuery("#redirection_link_name").val();
	    var res = url_name.replace(/[^A-Za-z0-9_]/g, "-");
		jQuery("#resulting_link").text(res);
	});

	jQuery("#domain_list").change(function() {
		var selected_domain = this.options[this.selectedIndex].innerHTML;
		jQuery("#resulting_link_domain").text(selected_domain + "/");
	});

	jQuery("#redirection_link_url").keyup(function() {
		jQuery("#resulting_link_label").css('visibility', 'visible');
	});
	jQuery("#redirection_link_name").keyup(function() {
		jQuery("#resulting_link_label").css('visibility', 'visible');
	});

}

function cm_select_text() {
	jQuery(".cm_select").prev().select();
}
function copyToClipboard(){
	jQuery("#cm_dialog").dialog({height: 160, width: 450 });
}

</script>
<style type="text/css">
    textarea {
	    max-width:60%;
      	min-width:60%;
	}
</style>
<body>
<?php
	$api_key=WPClickmeter::get_option('clickmeter_api_key');
	$boGoVal = WPClickmeter::get_option('clickmeter_backOffice_key');

	//TRACKING LINK SETTINGS
	
	//DOMAINS
	$blog_name = get_site_url();
	$blog_name = substr($blog_name,7);
	$wordpress_domain_id = 1597;
	$clickmeter_default_domainId = WPClickmeter::get_option('clickmeter_default_domainId');
	$clickmeter_default_domainName = WPClickmeter::get_option('clickmeter_default_domainName');

	
	$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/domains?_expand=true', 'GET', NULL, $api_key);
	$clickmeter_domains = $json_output[entities];
	$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/domains?type=go&_expand=true', 'GET', NULL, $api_key);
	foreach ($json_output[entities] as $goDomain) {
		$clickmeter_domains[] = $goDomain;
	}
	$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/domains?type=personal&_expand=true', 'GET', NULL, $api_key);
	foreach ($json_output[entities] as $personalDomain) {
		$clickmeter_domains[] = $personalDomain;
	}
	$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/domains?type=dedicated&_expand=true', 'GET', NULL, $api_key);
	foreach ($json_output[entities] as $dedicated_domain) {
		$clickmeter_domains[] = $dedicated_domain;
	}

	//CAMPAIGNS
	$default_campaignId = WPClickmeter::get_option("clickmeter_default_campaignId_links");
	$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/groups?status=active&_expand=true', 'GET', NULL, $api_key);
	$groups = $json_output[entities];

	//CONVERSIONS
	$active_conversions = WPClickmeter::api_request('http://apiv2.clickmeter.com/conversions?status=active&_expand=true', 'GET', NULL, $api_key);
	$default_firstConv_links = WPClickmeter::get_option("clickmeter_default_firstConv_links");
	$default_secondConv_links = WPClickmeter::get_option("clickmeter_default_secondConv_links");

	//REDIRECTION TYPE
	$default_redirection_type = WPClickmeter::get_option("clickmeter_default_redirection_type");

	//LINK CLOAKING
	$link_cloak_flag = WPClickmeter::get_option("link_cloak_flag");


?>
	<hr>
	<input id="cm_bogoval" type="hidden" value="<?php echo $boGoVal; ?>"/>
	<div id="tracking_link_form">
		<h2>Create new tracking link: </h2>
		<input type="hidden" id="clickmeter_default_domainId" value="<?php echo $clickmeter_default_domainId; ?>">
		<table style="width:80%">
			<tbody>
				<tr>
					<td style="width:20%"><span style="color:#6B6B6B;font-size: 15px;"><strong>Destination URL: </strong></span></td>
					<td style="width:80%">
						<span class="tooltip"><textarea style="height:27px" placeholder="http://" id="redirection_link_url" id="redirection_link_url"></textarea><span><img class="callout" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/callout_black.gif" />
							This is the link you want to track: the target URL where you want to send your visitors (eg. http://clickmeter.com).
						</span></span>
						<span style="display:none" class="error" id="error_url"></span>
					</td>
				</tr>
				<tr>
					<td style="width:20%"><span style="color:#6B6B6B;font-size: 15px;"><strong>Link name: </strong></span></td>
					<td style="width:80%">
						<span class="tooltip"><input type="text" style="width:60%" value="" id="redirection_link_name" id="redirection_link_name" maxlength="50"/>
						<span><img class="callout" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/callout_black.gif" />
							This is the part of the link that goes after the main domain (e.g. http://yourdomain.com/link-name).
						</span></span>
						<span style="display:none" class="error" id="error_fname"></span>
					</td>
				</tr>
			</tbody>
		</table>

		<p style="visibility: hidden;" id="resulting_link_label"><strong>Resulting link: </strong>http://<span id="resulting_link_domain"><?php echo $clickmeter_default_domainName . "/"; ?></span><span id="resulting_link"></span></p>

		<p><a style="display:none;cursor:pointer;" class="compress_section">Less options: [collapse-]</a><a style="cursor:pointer;" class="expand_section">More options: [expand+]</a></p>
		<div style="width:60%;display:none;border:solid;border-width: thin;border-color: #BDBDBD; padding:10px;">
		<form method="post" action="#">
			<span style="padding-right:5px;"><strong>Domain: </strong></span>
			<span class="tooltip">
			<select style="width: 50%;" id="domain_list" name="domain_list">
				<option value="<?php echo $wordpress_domain_id; ?>"><?php echo $blog_name; ?></option>';
				<?php foreach ($clickmeter_domains as $domain) {
					if($domain[id] == $clickmeter_default_domainId){
						echo '<option selected value="'.$domain[id].'">'.$domain[name].'</option>';	
					}else{
						echo '<option value="'.$domain[id].'">'.$domain[name].'</option>';	
					}
					
				}?>
			</select>
			<span><img class="callout" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/callout_black.gif" />
				Choose the domain for your tracking link.
			</span>
			</span>
		<br><br>
			<span style="padding-right:5px;"><strong>Campaign: </strong></span>
			<span class="tooltip">
			<select style="width: 50%;" id="campaign_list" name="campaign_list">
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
			</span>
			</span>
		<br><br>
			<span style="padding-right:5px;"><strong>First conversion: </strong></span>
			<span class="tooltip">
			<select style="width: 50%;" id="first_conversion_list" name="first_conversion_list">
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
			</span>
			</span>
		<br><br>
		<?php if($default_firstConv_links!=null && $default_firstConv_links!="none" ) : ?>
			<span style="padding-right:5px;"><strong>Second conversion: </strong></span>
			<span class="tooltip">
			<select style="width: 50%;" id="second_conversion_list" name="second_conversion_list">
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
			</span>
			</span>
			<br><br>
		<?php endif; ?>
			<span style="padding-right:5px;"><strong>Redirection type: </strong></span>
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
			</span></span>
			<br><br>
			<span style="padding-right:5px;"><strong>Enable link cloaking? </strong></span>
			<?php if ($link_cloak_flag==1) : ?>
			<span style="padding-right: 10px"><input type="radio" name="link_cloak_flag" checked="true" value="true">Yes</span>
			<input type="radio" name="link_cloak_flag" value="false">No (default)
			<?php else : ?>
			<span style="padding-right: 10px"><input class type="radio" name="link_cloak_flag" value="true">Yes</span>
			<input type="radio" name="link_cloak_flag" checked="true" value="false">No (default)
			<?php endif; ?>
			<span class="tooltip" style="padding-right:5px;"><img style="margin-left: 10px;" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/tooltip_icon.png"><span><img class="callout" src="/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/callout_black.gif" />
				URL cloaking allows you to hide the destination URL after someone clicks on the tracking link. If selected, this option will “encrypt” the destination URL in the source code too, so that it will not be easy to discover the affiliate ID if any.
			</span></span>
		</form>
		</div>

		<br>
		<input type="button" id="save_tracking_link" onclick="callAjax_check_redirection_trackinglink()" class="clickmeter-button" value="Create"/>
		<div id="creating_tl" style="padding: 5px 0px 5px 0px; display:none">
			<table>
				<tr><td><div class="spinner_cm"></div></td><td><span>Please wait some seconds.</span></td></tr>
			</table>
		</div>
	</div>

	<div id="tl_creation_success" style="padding: 20px;display:none;">
		<center>
			<h2><span style="margin: 4px;color:green;">Your tracking link has been successfully created!</span></h2><br>
			<strong><a id="created_tracking_link_code" target="_blank" href=""></a></strong><br>
				<a id="created_tracking_link_edit" style="text-decoration:underline" title="Edit tracking link on ClickMeter" target="blank" href="">Edit</a> |
				<a id="created_tracking_link_stats" style="text-decoration:underline" title="View tracking link stats on ClickMeter " target="blank" href="">Stats</a> |
				<a id="created_tracking_link_QR" style="text-decoration:underline" title="Get QR code" target="_blank" href="">QR</a> |
				<input title="Copy to clipboard" type="button" class="link_button" value="Copy" onclick="copyToClipboard()"/>				
		</center>
		<br><br>
		<center>
			<a class="clickmeter_link" href="<?php echo esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-list_tracking_links.php'), admin_url('admin.php'))); ?>"><span style="padding: 5px 5px;width:20%;" class="clickmeter-button">View Tracking Links list</span></a>
			<span style="padding:0 50px 0 50px"></span>
			<a class="clickmeter_link" href="<?php echo esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-new_tracking_link.php'), admin_url('admin.php'))); ?>"><span style="padding: 5px 5px;width:20%;" class="clickmeter-button">Create New Link</span></a>
		</center>
	</div>

	<div id="cm_dialog" style="display:none" title="Copy to clipboard">
		<p style="color:grey"><i>Select URL than type CTRL + C to copy</i></p>
    	<input id="cm_copy_tl" style="width: 80%;" type="text" value=""/>
    	<button type="button" class="cm_select" onclick="cm_select_text()">Select</button>
	</div>
	
</body>
</html>