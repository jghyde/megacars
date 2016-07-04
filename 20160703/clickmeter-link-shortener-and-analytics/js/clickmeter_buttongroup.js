var boGoVal;
var ERROR_MESSAGES = {
	500: 'Ops! Something went wrong'
};

function getErrorMessage(status){
	var errorMessage = ERROR_MESSAGES[status];
	if (!errorMessage){
		errorMessage = ERROR_MESSAGES[500];
	}
	return errorMessage;
}

(function() {
		tinymce.PluginManager.add('clickmeter_buttongroup', function( editor, url ) {
        editor.addButton( 'clickmeter_buttongroup', {
            title: 'Tracking Link',
            type: "menubutton",
            icon: "icon link-icon",
			menu: [
	        	{
	        		text: 'Insert/Edit Short Tracking Link',
	        		onclick: function() {
		            	var selected_content = editor.selection.getContent();
		            	//var boGoVal = jQuery("#boGoVal").val();
		            	var link_html = "";
		            	var post_title = jQuery("#title").val();
		            	var selection_type = editor.selection.getNode().nodeName;
		            	if(selection_type == 'A'){
		            		link_html = editor.selection.getNode().outerHTML;
		            		//alert(link_html);
							var myhtml = jQuery.parseHTML( link_html );
							var href_value = myhtml[0].href;
							//alert(href_value);
							if(myhtml[0].name == "clickmeter_link"){
								var tracking_link_id = myhtml[0].id;
			            		editor.windowManager.open({
								    title: 'Edit Tracking Link',
								    url: url + '/clickmeter-testmodalpop.php',
								    width: 300,
								    height: 120
								}, {
								    arg1: url,
								    arg2: href_value,
								    arg3: tracking_link_id,
								    arg4: boGoVal
								});
								return;					
							}
		            	}if(selection_type != 'BODY'){
		            		var href_value = "http://";
		            		if(selection_type == 'A'){
		            			link_html = editor.selection.getNode().outerHTML;
		            			var myhtml = jQuery.parseHTML( link_html );
		            			selected_content = myhtml[0].text;
								href_value = myhtml[0].href;
		            		}
		            		var button_reference = this;
						    editor.windowManager.open( {
						        title: 'Insert New Short Tracking Link',
					        	width : 500,
			   					height : 250,
						        body: [
						        {
						            type: 'label',
						            name: 'instructions',
						            text: 'Enter the destination URL',
						            style: 'font-style: italic;font-size:13px;'
						        },
						        {
						            type: 'textbox',
						            name: 'url',
						            label: 'URL',
						            value: href_value
						        },			        
						        {
						            type: 'textbox',
						            name: 'friendly_name',
						            label: 'Title',
						            value: selected_content
						        },
						        {
						            type: 'checkbox',
						            name: 'new_window',
						            label: 'Open link in a new window/tab',
						      		checked: true
						        },
						        {
						            type: 'checkbox',
						            name: 'cloak_link',
						            label: 'Cloak Tracking Link'
						        },
						        {
						            type: 'checkbox',
						            name: 'no_follow',
						            label: 'Add nofollow attribute',
						      		checked: false
						        },
						        //{
						        //    type: 'label',
						        //    name: 'settings_link',
						        //    text: 'View default settings',
						        //    style: 'padding-top: 6px;font-size:13px;	cursor: pointer;text-decoration: underline;color:#0074a2;line-height: 1.4em;font-size: 13px;',
					             //	onclick: function(e) {
					             //		window.open(jQuery("#clickmeter_settings_link").val() + "#tracking_link_settings");
						        //	}
						        //}
						        ],
						        onsubmit: function(e) {
						        	e.preventDefault();
						        	var dest_url = e.data.url;
						        	var pattern = /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|mailto:|ftp:\/\/|ftps:\/\/)[a-z0-9@]+([\-\.]{1}[a-z0-9@]+)*\.[a-z]{2,5}(.)*/;
						        	if(!pattern.test(dest_url)){
						        		alert("Invalid URL.");
						        	}else if(e.data.friendly_name == ""){
						        		alert("Title cannot be empty.");
						        	}else{
						        		button_reference.hide();
						            	var data = {
									        action: 'create_tl',
									        friendly_name: e.data.friendly_name,
									        url: dest_url,
									        cloak_link: e.data.cloak_link,
									        tag_name: post_title,
									        post_id: jQuery("#post_ID").val()
									    };
									    editor.windowManager.close();
								    	editor.windowManager.open({
								    		title: 'Creating Tracking Link',
										    url: url + '/clickmeter-loading_popup.php',
								        	width: 250,
									    	height: 100
										});

										jQuery.ajax({
											type:"post",
											url: ajaxurl,
											data: data,
											success:function(response) {
												var obj = jQuery.parseJSON(response);
												boGoVal = obj.bogoval;
												//TAG TARGET
												var target_flag = e.data.new_window;
												var target_value = "";
												if(target_flag==true) target_value="_blank";
												else target_value="_top";
												//TAG NOFOLLOW
												var nofollow_flag = e.data.no_follow;
												var nofollow_value = "";
												if(nofollow_flag==true) nofollow_value="nofollow";
												if(selection_type == 'A'){
													editor.selection.getNode().outerHTML = '<a name="clickmeter_link" title="'+e.data.friendly_name+'" id="'+obj.created_link_id+'" rel="'+nofollow_value+'" target="'+target_value+'" href="'+obj.trackingCode+'">'+selected_content+'</a>';
												}else{
													editor.selection.setContent('<a name="clickmeter_link" title="'+e.data.friendly_name+'" id="'+obj.created_link_id+'" rel="'+nofollow_value+'" target="'+target_value+'" href="'+obj.trackingCode+'">'+selected_content+'</a>');
												}

												editor.windowManager.close();
												editor.windowManager.open({
													title: "Operation Complete",
													width: 380,
													height: 100,
													body: [
														{
															type: 'label',
															name: 'success_label',
															text: 'Your Tracking Link has been successfully created!',
															style: 'color: green;font-size:15px;'
														}],
													onsubmit: function(e) {
														button_reference.show();
														editor.windowManager.close();
													}
												});
											},
											error: function(error){
												var errorMessage = getErrorMessage(error.status);
												alert(errorMessage);
												button_reference.show();
												editor.windowManager.close();
											}
										});
						        	}
						        }
					   		});
					    }
					}
	        	},
	        	{
	        		text: 'Replace all links with Short Tracking Links',
		            onclick: function() {
		                var button_reference = this;
		                editor.windowManager.open( {
	                        title: 'Replace all links with Short Tracking Links',
	                        width : 500,
	                        height : 80,
	                        body: [
	                        {
	                            type: 'label',
	                            name: 'instructions',
	                            text: 'Do you want to replace all links in this page with tracking links?',
	                            style: 'font-size:15px;'
	                        },
	                        ],
	                        onsubmit: function(e) {
	                            e.preventDefault();
	                            button_reference.hide();
	                            var post_title = jQuery("#title").val();
	                            var content = editor.getContent({format : 'html'});
	                            var myhtml = jQuery.parseHTML( "<div>" + content + "</div>" );
	                            //alert(content);
	                            var links = jQuery(myhtml).find("a");
	                            var links_list = {};
	                            for (index = 0; index < links.length; ++index) {
	                                if(links[index].name != "clickmeter_link"){
										var pattern = /^(http:\/\/www\.|https:\/\/www\.|http:\/\/|https:\/\/|mailto:|ftp:\/\/|ftps:\/\/)[a-z0-9@]+([\-\.]{1}[a-z0-9@]+)*\.[a-z]{2,5}(.)*/;
										if(!pattern.test(links[index].href)){
											editor.setContent(jQuery(myhtml).html());
											alert('Invalid URL: ' + links[index].href);
											button_reference.show();
											editor.windowManager.close();
											return;
										}
	                                    var newLink = {};
	                                    newLink[0] = links[index].text;
										newLink[1] = links[index].href;
										newLink[3] = false; // replace flag
	                                    links_list[index] = newLink;
	                                }
	                            }
	                            var data = {
	                                action: 'create_batch_tl',
	                                new_links_list: links_list,
	                                tag_name: post_title,
	                                post_id: jQuery("#post_ID").val()
	                            };

	                            editor.windowManager.close();
	                            editor.windowManager.open({
	                                title: 'Creating Tracking Links',
	                                url: url + '/clickmeter-loading_popup.php',
	                                width: 250,
	                                height: 100
	                            });
								jQuery.ajax({
									type:"post",
									url: ajaxurl,
									data: data,
									success:function(response) {
										var obj_list = jQuery.parseJSON(response);
										for (var key in obj_list) {
											for (index = 0; index < links.length; ++index) {
												if(links[index].href === obj_list[key].entityData.typeTL.url){
													links[index].href = obj_list[key].entityData.trackingCode;
													links[index].id = obj_list[key].entityData.id;
													links[index].name = "clickmeter_link";
													links[index].replace = true;
												}
											}
										}
										var notReplacedLinks = [];
										for (index = 0; index < links.length; index++){
											if (!links[index].replace && (notReplacedLinks.indexOf(links[index].href) === -1)){
												notReplacedLinks.push(links[index].href);
											}
										}
										editor.setContent(jQuery(myhtml).html());
										var message;
										if (notReplacedLinks.length > 0){
											message = 'Links have been replaced expect the followings: \n';
											for (var i=0; i<notReplacedLinks.length; i++){
												message += '- ' + notReplacedLinks[i] + '\n';
											}
										}
										else {
											message = "All links have been replaced!";
										}
										alert(message);
										button_reference.show();
										editor.windowManager.close();
									},
									error: function(error){
										var errorMessage = getErrorMessage(error.status);
										alert(errorMessage);
										button_reference.show();
										editor.windowManager.close();
									}
								});
	                        }
		                });
					}
	        	}
        	]	
        });
    });
})();