
jQuery(document).ready(function(){
	
	var document_url = document.URL;
	if(document_url.search("#tracking_link_settings")!=-1){
		jQuery("#tracking_link_settings").show();
	}
	if(document_url.search("#manage_404_errors")!=-1){
		jQuery("#manage_404_errors").show();
	}
    if(document_url.search("#manage_error_report")!=-1){
        jQuery("#manage_error_report").show();
    }
	if(document_url.search("#clickmeter_track_conversions")!=-1){
		jQuery("#clickmeter_track_conversions").show();
	}
	if(document_url.search("#clickmeter_track_views")!=-1){
		jQuery("#clickmeter_track_views").show();
	}		

	jQuery("#clickmeter_check_all").click(function(){
		var flag = jQuery(this).prop("checked");
		if(flag==false){
			jQuery("input[type=checkbox]").prop("checked", false); 	
		}else{
			jQuery("input[type=checkbox]").prop("checked", true); 	
		}
	});

	jQuery("#cm_apply_bulk_action").click(function(){
		var action_type = jQuery("[name=cm_bulk_actions]").val();
		if(action_type=="null") return;
		if(action_type == "bulk_tl_change_dest_url"){
			jQuery("#cm_bulk_edit_url").show();
		}else if(action_type == "bulk_tl_delete"){
			jQuery("#bulk_action_progress").show();
			var checked_options = [];
			jQuery("input[type=checkbox][name=tracking_link_rows]:checked").each(function(){
				checked_options.push(jQuery(this).val());
			});
			var post_data = {
		        action: action_type,
		        ids: checked_options
		    };

		    var timeout_val = 3600*1000;
			jQuery.ajax({
		        type:"post",
		        url: ajaxurl,
		        timeout: timeout_val,
		        data: post_data,
		        success:function(data) {
		            //alert("Execution terminated with success! ");
		            location.reload();
		        },
		        //error: function( jqXHR, textStatus, errorThrown ){
		          //  alert('OPS! Something went wrong' + textStatus + ": " + errorThrown);   
		        //}        
			});
		}
	});

	jQuery("#cm_bulk_edit_url_confirm").click(function(){
		jQuery("#cm_bulk_edit_url").hide();
		jQuery("#bulk_action_progress").show();
		var checked_options = [];
		jQuery("input[type=checkbox][name=tracking_link_rows]:checked").each(function(){
			checked_options.push(jQuery(this).val());
		});
		var newUrl = jQuery("#cm_dest_url").val();
		var pattern = /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|mailto:|ftp:\/\/|ftps:\/\/)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(.)*/;
		if(!pattern.test(newUrl)){
			//alert("Invalid URL.");
			jQuery("#error_url").text("*Error: Insert a valid URL.");
			jQuery("#error_url").show();
			jQuery("#cm_bulk_edit_url").show();
			jQuery("#bulk_action_progress").hide();
			return;
		}
		var post_data = {
	        action: "bulk_tl_change_dest_url",
	        ids: checked_options,
	        new_url: newUrl
	    };

	    var timeout_val = 3600*1000;
		jQuery.ajax({
	        type:"post",
	        url: ajaxurl,
	        timeout: timeout_val,
	        data: post_data,
	        success:function(data) {
	            //alert("Execution terminated with success! ");
	            location.reload();
	        },
	        //error: function( jqXHR, textStatus, errorThrown ){
	          //  alert('OPS! Something went wrong' + textStatus + ": " + errorThrown);   
	        //}        
		});
	});

	jQuery(document).ready( function(jQuery) {   
		jQuery('.openInNewWindow').parent().attr('target','_blank'); 
		jQuery('.cm_hidden').parent().attr('href','#'); 
	});

	jQuery(".expand_section").click(function(){
		jQuery(this).parent().next("div").show();
		jQuery(this).prev().show();
		jQuery(this).hide();
	});
	jQuery(".compress_section").click(function(){
		jQuery(this).parent().next("div").hide();
		jQuery(this).next().show();
		jQuery(this).hide();
	});

	//onload
	var checkedRadioButton = jQuery('input[type=radio][name=pixels_flags]:checked');
	if (checkedRadioButton.val() == 'true') {
		jQuery("#multiple_select_table_pixels").show();
	}
	else if (checkedRadioButton.val() == 'false') {
		jQuery("#tracking_pixel_box").hide();
		jQuery("#include_default_pixel").hide();
	}

	var track404flag = jQuery('input[type=radio][name=track_404_flag]:checked');
	if (track404flag.val() == 'true') {
		jQuery("#custom_404_page").show();
		jQuery("#custom_404_page_label").show();
	}
	else if (track404flag.val() == 'false') {
		jQuery("#custom_404_page").hide();
		jQuery("#custom_404_page_label").hide();
	}

	var conversionId1 = jQuery("#conversion1_id").val();
	if(conversionId1!=null && conversionId1!=undefined && conversionId1!=""){
		jQuery("#conversion1_created").show();
		jQuery("#conversion1_form").hide();
		jQuery("#conversion2_form").show();
	}else{
		jQuery("#conversion1_created").hide();
		jQuery("#conversion1_form").show();
		jQuery("#conversion2_form").hide();
	}
	var conversionId2 = jQuery("#conversion2_id").val();
	if(conversionId2!=null && conversionId2!=undefined && conversionId2!=""){
		jQuery("#conversion2_form").hide();
		jQuery("#conversion2_created").show();
	}else{
		jQuery("#conversion2_created").hide();
	}

	jQuery("#conversion1_form").submit(function(e){
		e.preventDefault();
		var selected_value = jQuery("#conversion1_type").val();
		var target_flag = true;
		if(jQuery("#conversion1_target_posts").prop("checked")==false && jQuery("#conversion1_target_pages").prop("checked")==false){
			target_flag = false;	
		}
		if(selected_value=="null"){
			jQuery(".conversion_err").fadeIn();
			jQuery(".conversion_err").text(" *Please choose a conversion type");
			jQuery(".conversion_err").fadeOut("slow");
		}else if(target_flag==false){
			jQuery(".conversion_err").fadeIn();
			jQuery(".conversion_err").text(" *Please choose a conversion target");
			jQuery(".conversion_err").fadeOut("slow");
		}else{
			this.submit();
		}
	});
	jQuery("#conversion2_form").submit(function(e){
		e.preventDefault();
		var selected_value = jQuery("#conversion2_type").val();
		var target_flag = true;
		if(jQuery("#conversion2_target_posts").prop("checked")==false && jQuery("#conversion2_target_pages").prop("checked")==false){
			target_flag = false;	
		}
		if(selected_value=="null"){
			jQuery(".conversion_err").fadeIn();
			jQuery(".conversion_err").text(" *Please choose a conversion type");
			jQuery(".conversion_err").fadeOut("slow");
		}else if(target_flag==false){
			jQuery(".conversion_err").fadeIn();
			jQuery(".conversion_err").text(" *Please choose a conversion target");
			jQuery(".conversion_err").fadeOut("slow");
		}else{
			this.submit();
		}
	});

	jQuery("#tracking_pixel_form").submit(function(e){
		e.preventDefault();
		var available_datapoints = jQuery("#cm_available_datapoints").val();
		var already_created_pixel = jQuery("#cm_already_created_pixel").val();
		var included_list = jQuery("[name='included_list[]']");
		if( (available_datapoints - (included_list.length - already_created_pixel))<0){
			jQuery("#available_dp_dialog").dialog({height: 160, width: 500 });
		}else{
			this.submit();
			//alert("submitted");
		}
	});
	

	//onchange
	jQuery('input[type=radio][name=pixels_flags]').change(function() {
		if (this.value == 'true') {
			jQuery("#tracking_pixel_box").show();
			jQuery("#include_default_pixel").show();
		}
		else if (this.value == 'false') {
			jQuery("#tracking_pixel_box").hide();
			jQuery("#include_default_pixel").hide();
		}
	});

	jQuery('input[type=radio][name=track_404_flag]').change(function() {
		if (this.value == 'true') {
			jQuery("#custom_404_page").show();
			jQuery("#custom_404_page_label").show();
		}
		else if (this.value == 'false') {
			jQuery("#custom_404_page").hide();
			jQuery("#custom_404_page_label").hide();
		}
	});
});


//PIXEL SETTINGS FUNCTIONS
jQuery(function() {
     jQuery(".multiselect").multiselect();
});

//MULTISELECT FUNCTIONS
jQuery.fn.multiselect = function() {
    jQuery(this).each(function() {
        var checkboxes = jQuery(this).find("input:checkbox");
        checkboxes.each(function() {
            var checkbox = jQuery(this);
            /* Highlight pre-selected checkboxes
            if (checkbox.prop("checked")){
            	checkbox.parent().addClass("multiselect-on");
            	checkbox.attr("name", "included_posts_list");
            }*/
            // Highlight checkboxes that the user selects
            checkbox.click(function() {
                if (checkbox.prop("checked")){
                	checkbox.attr("name", "included_list[]");
                	checkbox.parent().addClass("multiselect-on");
                }
                else{
                	checkbox.attr("name", "excluded_list[]");
                	checkbox.parent().removeClass("multiselect-on");
                } 
            });
        });
    });
};

//POSTS CHECKBOX GROUP FUNCTIONS
function selectAllTPPosts (){
	jQuery("#multiselect_posts").children().addClass("multiselect-on");
	jQuery("#multiselect_posts").children().children().attr("name", "included_list[]");
	jQuery("#multiselect_posts").children().children().prop("checked", "checked");
}
function deselectAllTPPosts (){
	jQuery("#multiselect_posts").children().removeClass("multiselect-on");
	jQuery("#multiselect_posts").children().children().attr("name", "excluded_list[]");
	jQuery("#multiselect_posts").children().children().removeAttr("checked");
}

//PAGES CHECKBOX GROUP FUNCTIONS
function selectAllTPPages (){
	jQuery("#multiselect_pages").children().addClass("multiselect-on");
	jQuery("#multiselect_pages").children().children().attr("name", "included_list[]");
	jQuery("#multiselect_pages").children().children().prop("checked", "checked");
}
function deselectAllTPPages (){
	jQuery("#multiselect_pages").children().removeClass("multiselect-on");
	jQuery("#multiselect_pages").children().children().attr("name", "excluded_list[]");
	jQuery("#multiselect_pages").children().children().removeAttr("checked");
}

function changeSelectedStatus(){
	jQuery("[name='excluded_list[]']").prop("checked", "checked");
	jQuery("[name='excluded_list[]']").prop("checked", "checked");
}


//INIT FUNCTIONS
function startupEnableTP(){
	jQuery("#init_form").submit();
}
function startupDisableTP(){
	jQuery("#startup_create_TP").attr('value', "false");
	jQuery("#init_form").submit();
}
function init_createTP(){
	jQuery("#init_createTP_form").submit();
}

function callAjaxTP_init_creation(inclusionList, endInclusionIndex){
	var post_data = {
        action: 'TP_init_creation',
        inclusion_list: inclusionList
    };

    var timeout_val = 3600*1000;
	jQuery.ajax({
        type:"post",
        url: ajaxurl,
        timeout: timeout_val,
        data: post_data,
        success:function(data) {
            //alert("Execution terminated with success! ");
			window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&startup_create_TP=true&included_offset="+endInclusionIndex);    
        },
        error: function( jqXHR, textStatus, errorThrown ){
          	//alert('OPS! Something went wrong' + textStatus + ": " + errorThrown);
          	var timestamp = getTimestamp();
        	window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&error_flag=true&timestamp="+timestamp+"&error_message="+textStatus + ": " + errorThrown+"&action="+post_data.action);
        }
	});
}

//CREATE TRACKING PIXEL FUNCTIONS
function callAjaxTP_savechanges(inclusionList, exclusionList, endInclusionIndex, endExclusionIndex){
	//alert(exclusionList);
	//alert(inclusionList);
    var post_data = {
        action: 'TP_savechanges',
        exclusion_list: exclusionList,
        inclusion_list: inclusionList
    };

    var timeout_val = 3600*1000;
	jQuery.ajax({
	        type:"post",
	        url: ajaxurl,
	        timeout: timeout_val,
	        data: post_data,
	        success:function(data) {
	            //alert("Execution terminated with success! ");
				window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&pixels_flags=true&included_offset="+endInclusionIndex+"&excluded_offset="+endExclusionIndex+"");    
	        },
	        error: function( jqXHR, textStatus, errorThrown ){
	          	//alert('OPS! Something went wrong' + textStatus + ": " + errorThrown);
	          	var timestamp = getTimestamp();
	        	window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&error_flag=true&timestamp="+timestamp+"&error_message="+textStatus + ": " + errorThrown+"&action="+post_data.action);
	        }        
	});
}

//REMOVE TRACKING PIXEL FUNCTIONS
function callAjaxTP_savechanges_remove(exclusionList, endPostIndex){
	//alert(exclusionList);
    var post_data = {
        action: 'TP_savechanges', 
        exclusion_list: exclusionList
    };

    var timeout_val = 3600*1000;
	jQuery.ajax({
	        type:"post",
	        url: ajaxurl,
	        timeout: timeout_val,
	        data: post_data,
	        success:function(data) {
	            //alert("Execution terminated with success! ");
				window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&pixels_flags=false&post_offset="+endPostIndex);    
	        },
	        error: function( jqXHR, textStatus, errorThrown ){
	          	//alert('OPS! Something went wrong' + textStatus + ": " + errorThrown);
	          	var timestamp = getTimestamp();
	        	window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&error_flag=true&timestamp="+timestamp+"&error_message="+textStatus + ": " + errorThrown+"&action="+post_data.action);
	        }        
	});
}


//REMOVE APIKEY FUNCTIONS
function callAjaxTP_delete(endPostIndex){
	var post_data = {
        action: 'TP_delete_apikey'
    };

    var timeout_val = 3600*1000;
	jQuery.ajax({
        type:"post",
        url: ajaxurl,
        timeout: timeout_val,
        data: post_data,
        success:function(data) {
            //alert("Execution terminated with success! ");
			window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&API_key_delete=true&post_offset="+endPostIndex);    
        },
        error: function( jqXHR, textStatus, errorThrown ){
          	//alert('OPS! Something went wrong' + textStatus + ": " + errorThrown);
          	var timestamp = getTimestamp();
        	window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&error_flag=true&timestamp="+timestamp+"&error_message="+textStatus + ": " + errorThrown+"&action="+post_data.action);
        }           
	});
}

function callAjaxPixels_delete(endPostIndex){

    var post_data = {
        action: 'TP_delete_pixels'
    };

    var timeout_val = 3600*1000;
    jQuery.ajax({
        type:"post",
        url: ajaxurl,
        timeout: timeout_val,
        data: post_data,
        success:function(data) {
            //alert("Execution terminated with success! ");
            window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&delete_pixels_in_post_contents=true&post_offset="+endPostIndex);
        },
        error: function( jqXHR, textStatus, errorThrown ){
            //alert('OPS! Something went wrong' + textStatus + ": " + errorThrown);
            var timestamp = getTimestamp();
            window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&error_flag=true&timestamp="+timestamp+"&error_message="+textStatus + ": " + errorThrown+"&action="+post_data.action);
        }
    });
}

//CREATE TRACKING LINK
function callAjax_create_trackinglink(){
	jQuery("#get_tracking_link").hide();
	jQuery("#creating_tl").show();

	var post_data = {
	    action: 'create_tl',
	    friendly_name: jQuery("#clickmeter_post_title").val(),
	    url: jQuery("#post_url").val(),
	    post_id: jQuery("#cm_post_id").val()
	};

	var timeout_val = 3600*1000;
	jQuery.ajax({
        type:"post",
        url: ajaxurl,
        timeout: timeout_val,
        data: post_data,
        success:function(data) {
			jQuery("#creating_tl").hide();
			jQuery("#tl_creation_success").show();
			jQuery("#get_tracking_link_button").hide();
            //alert("Execution terminated with success! ");
        },
        //error: function( jqXHR, textStatus, errorThrown ){
          //  alert('OPS! Something went wrong' + textStatus + ": " + errorThrown);   
        //}        
	});
}

//CHECK REDIRECTION LINK EXISTENCE
function callAjax_check_redirection_trackinglink(){
	jQuery(".error").hide();
	jQuery("#save_tracking_link").hide();
	jQuery("#creating_tl").show();
	var dest_url = jQuery("#redirection_link_url").val();
	var pattern = /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|mailto:|ftp:\/\/|ftps:\/\/)[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(.)*/;
	if(!pattern.test(dest_url)){
		//alert("Invalid URL.");
		jQuery("#error_url").text("*Error: Insert a valid URL.");
		jQuery("#error_url").show();
		jQuery("#creating_tl").hide();
		jQuery("#save_tracking_link").show();
		return;
	}
	if(jQuery("#redirection_link_name").val()=="" || 
		jQuery("#redirection_link_name").val()==null || 
		jQuery("#redirection_link_name").val()==undefined){
		
		//alert("Empty name.");
		jQuery("#error_fname").text("*Error: Friendly name cannot be empty.");
		jQuery("#error_fname").show();
		jQuery("#creating_tl").hide();
		jQuery("#save_tracking_link").show();
		return;
	}

	var clickmeter_default_domainId = jQuery("#domain_list").val();
	if(clickmeter_default_domainId == "1597"){
		var post_data = {
		    action: 'check_redirect_link',
		    link_name: jQuery("#redirection_link_name").val()
		};

		jQuery.post(ajaxurl, post_data, function(response) {
			if(response == "is_tracking_link"){
				//alert('Error: You already have a tracking link with the inserted name.');
				jQuery("#error_fname").text("*Error: You already have a tracking link with the inserted name.");
				jQuery("#error_fname").show();
				jQuery("#creating_tl").hide();
				jQuery("#save_tracking_link").show();	
				return;
			}else if(response == "is_post"){
				//alert('Error: Inserted name have to be different from any post/page title.');
				jQuery("#error_fname").text("*Error: Inserted name have to be different from any post/page title.");
				jQuery("#error_fname").show();
				jQuery("#creating_tl").hide();
				jQuery("#save_tracking_link").show();
				return;
			}
			//alert("check passed!");
			// jQuery("#creating_tl").hide();
			// jQuery("#save_tracking_link").show();
			callAjax_create_custom_trackinglink("1");
		});
	}else{
		callAjax_create_custom_trackinglink("0");
	}
}

//CREATE REDIRECTION TRACKING LINK
function callAjax_create_custom_trackinglink(redirection_flag){
	var post_data = {
	    action: 'create_tl',
	    friendly_name: jQuery("#redirection_link_name").val(),
	    url: jQuery("#redirection_link_url").val(),
	    domain_id: jQuery("#domain_list").val(),
	    camapaign_id: jQuery("#campaign_list").val(),
	    first_conversion_id: jQuery("#first_conversion_list").val(),
	    second_conversion_id: jQuery("#second_conversion_list").val(),
	    post_id: "0000",
	    is_redirection_link: redirection_flag,
	    redirection_type: jQuery("#redirection_type_list").val(),
	    cloak_link: jQuery("[name=link_cloak_flag]:checked").val()
	};

	var timeout_val = 3600*1000;
	jQuery.ajax({
        type:"post",
        url: ajaxurl,
        timeout: timeout_val,
        data: post_data,
        success:function(response) {
        	var bogoval = jQuery("#cm_bogoval").val();
			var obj = jQuery.parseJSON(response);
			jQuery("#created_tracking_link_code").text(obj.alternative_url);
			jQuery("#created_tracking_link_code").attr("href", obj.alternative_url);
			jQuery("#created_tracking_link_edit").attr("href", "http://my.clickmeter.com/go?val="+bogoval+"&returnUrl=%2Flinks%2Fedit%2F"+ obj.created_link_id);
			jQuery("#created_tracking_link_stats").attr("href", "http://my.clickmeter.com/go?val="+bogoval+"&returnUrl=%2FLinks%3FlinkId%3D"+obj.created_link_id);
			jQuery("#created_tracking_link_QR").attr("href", obj.trackingCode+".qr");
			jQuery("#cm_copy_tl").val(obj.alternative_url);

        	jQuery("#creating_tl").hide();
        	jQuery(".error").hide();
			jQuery("#save_tracking_link").show();
			jQuery("#tracking_link_form").hide();
			jQuery("#tl_creation_success").show();

            //alert("Execution terminated with success! ");
        },
        //error: function( jqXHR, textStatus, errorThrown ){
          //  alert('OPS! Something went wrong' + textStatus + ": " + errorThrown);   
        //}        
	});
}

//ASSOCIATE CONVERSION FUNCTION
function callAjax_associate_conversion(postList, conversionToAssociate, endIndex){
	var post_data = {
        action: 'TP_associate_conversion',
        conversion_id: conversionToAssociate,
        post_list: postList
    };

	var timeout_val = 3600*1000;
	jQuery.ajax({
        type:"post",
        url: ajaxurl,
        timeout: timeout_val,
        data: post_data,
        success:function(data) {
            //alert("Execution terminated with success! ");
			window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&associate_conversion=true&post_offset="+endIndex+"&conversionToAssociate="+conversionToAssociate);    
        },
        error: function( jqXHR, textStatus, errorThrown ){
          	//alert('OPS! Something went wrong' + textStatus + ": " + errorThrown);
          	var timestamp = getTimestamp();
        	window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&error_flag=true&timestamp="+timestamp+"&error_message="+textStatus + ": " + errorThrown+"&action="+post_data.action);
        }      
	});
}

//DELETE CONVERSION FUNCTIONS
function confirmDeleteFirstConv(){
	jQuery("#first_conv_form").submit();
}

function confirmDeleteSecondConv(){
	jQuery("#second_conv_form").submit();
}

function callAjax_delete_conversion(postList, conversionToDelete, endIndex){
	var post_data = {
        action: 'TP_delete_conversion',
        conversion_id: conversionToDelete,
        post_list: postList
    };

	var timeout_val = 3600*1000;
	jQuery.ajax({
        type:"post",
        url: ajaxurl,
        timeout: timeout_val,
        data: post_data,
        success:function(data) {
            //alert("Execution terminated with success! ");
			window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&conversion_delete=true&post_offset="+endIndex+"&conversionToDelete="+conversionToDelete);
        },
        error: function( jqXHR, textStatus, errorThrown ){
          	//alert('OPS! Something went wrong' + textStatus + ": " + errorThrown);
          	var timestamp = getTimestamp();
        	window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&error_flag=true&timestamp="+timestamp+"&error_message="+textStatus + ": " + errorThrown+"&action="+post_data.action);
        }     
	});
}

function getTimestamp(){
	var today = new Date();
	var dd = today.getDate();
	var MM = today.getMonth()+1;
	var yyyy = today.getFullYear();
	var HH = today.getHours();
	var mm = today.getMinutes();
	var ss = today.getSeconds();
	if(dd<10) dd='0'+dd;
	if(MM<10) MM='0'+MM;
	if(HH<10) HH='0'+HH;
	if(mm<10) mm='0'+mm;
	if(ss<10) ss='0'+ss;
	timestamp = yyyy+'/'+MM+'/'+dd+" "+HH+":"+mm+":"+ss;
	return timestamp;
}