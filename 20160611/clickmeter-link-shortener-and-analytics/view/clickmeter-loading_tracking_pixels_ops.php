<?php
	$api_key=WPClickmeter::get_option('clickmeter_api_key');
	$blog_name = get_site_url();
	$blog_name = substr($blog_name,7);

	$conversion_block_size = 5;

	function log_event($action, $params){
		$lastOP_log = array($action);
		$lastOP_log[] = $params;
		WPClickmeter::store_option("clickmeter_lastOP_log", $lastOP_log);
	}

	if($_POST["continue_execution"]=="true"){
		$lastOP_log = WPClickmeter::get_option("clickmeter_lastOP_log");
		$last_action = $lastOP_log[0];
		$last_params_array = $lastOP_log[1];

		if($last_action=="API_key_delete"){
			echo '<script>window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&API_key_delete=true&post_offset='.$last_params_array[0].'"); </script>';
		}elseif($last_action=="delete_pixels_in_post_contents"){
            echo '<script>window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&delete_pixels_in_post_contents=true&included_offset='.$last_params_array[0].'&excluded_offset='.$last_params_array[1].'"); </script>';
        }
        elseif($last_action=="pixels_flags_true"){
			echo '<script>window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&pixels_flags=true&included_offset='.$last_params_array[0].'&excluded_offset='.$last_params_array[1].'"); </script>';
		}elseif($last_action=="pixels_flags_false"){
			echo '<script>window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&pixels_flags=false&post_offset='.$last_params_array[0].'"); </script>';
		}elseif($last_action=="associate_conversion"){
			echo '<script>window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&associate_conversion=true&post_offset='.$last_params_array[0].'&conversionToAssociate='.$last_params_array[1].'"); </script>';	
		}elseif($last_action=="conversion_delete"){
			echo '<script>window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&conversion_delete=true&post_offset='.$last_params_array[0].'&conversionToDelete='.$last_params_array[1].'"); </script>';	
		}

	} elseif($_POST["restart_execution"]=="true"){
		$lastOP_log = WPClickmeter::get_option("clickmeter_lastOP_log");
		$last_action = $lastOP_log[0];
		$last_params_array = $lastOP_log[1];

		if($last_action=="API_key_delete"){
			echo '<script>window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&API_key_delete=true"); </script>';
		}elseif($last_action=="pixels_flags_true"){
			echo '<script>window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&pixels_flags=true"); </script>';
		}elseif($last_action=="pixels_flags_false"){
			echo '<script>window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&pixels_flags=false"); </script>';
		}elseif($last_action=="associate_conversion"){
			echo '<script>window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&associate_conversion=true&conversionToAssociate='.$last_params_array[1].'"); </script>';	
		}elseif($last_action=="conversion_delete"){
			echo '<script>window.location.replace("?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&conversion_delete=true&conversionToDelete='.$last_params_array[1].'"); </script>';	
		}

	} elseif($_GET["startup_create_TP"]=="true"){
		WPClickmeter::store_option("clickmeter_workinprogress_flag", "inprogress");
		$complete_inclusion_list = WPClickmeter::get_option( 'clickmeter_inclusion_list');
		$total_included = sizeof($complete_inclusion_list);
		$start_inclusion_index = 0;
		if(isset($_GET["included_offset"])) $start_inclusion_index = $_GET["included_offset"];
		$total_percentage = intval(($start_inclusion_index/$total_included)*100);

		if($start_inclusion_index <= $total_included){
			$end_inclusion_index = $start_inclusion_index + DATAPOINT_BLOCK_SIZE;
			if($end_inclusion_index > $total_included) $end_inclusion_index = $total_included;
			for($start_inclusion_index;$start_inclusion_index<$end_inclusion_index;$start_inclusion_index++){
				if($complete_inclusion_list[$start_inclusion_index]!=null) $current_inclusion_list[] = $complete_inclusion_list[$start_inclusion_index];
			}
		}
		
		if($_GET["included_offset"] >= $total_included){
			//WPClickmeter::store_option("clickmeter_debug", "startup_create_TP");
			WPClickmeter::store_option("clickmeter_workinprogress_flag", "completed");
			$flag="completed";
		}else{
			log_event("startup_create_TP", array($end_inclusion_index));
			echo '<script>callAjaxTP_init_creation('.json_encode($current_inclusion_list).','.$end_inclusion_index.');</script>';
		}
	} elseif($_POST["API_key_delete"]!=NULL || $_GET["API_key_delete"]=="true"){

        //delete all tracking pixel from pages
		$count_posts = wp_count_posts();
		$total_excluded = $count_posts -> publish;
		$private = 'private';
		if (isset($count_posts -> $private)) {
			$total_excluded += $count_posts -> $private;
		}
		$total_excluded += $count_posts -> future;

        $start_exclusion_index = 0;
        if(isset($_GET["post_offset"])) $start_exclusion_index = $_GET["post_offset"];
        $total_percentage = intval(($start_exclusion_index/$total_excluded)*100);

        $end_exclusion_index = $start_exclusion_index + DATAPOINT_BLOCK_SIZE;
        $post_ids_array = WPClickmeter::retrieve_ids_posts(DATAPOINT_BLOCK_SIZE, $start_exclusion_index);

        WPClickmeter::store_option("clickmeter_current_exclusion_list", $post_ids_array);

		if($_GET["post_offset"] >= $total_excluded){
			//WPClickmeter::store_option("clickmeter_debug", "API_key_delete");
			WPClickmeter::store_option("clickmeter_workinprogress_flag", "completed");
			global $wpdb;
			$options_table = $wpdb->prefix . 'clickmeter_options';
			$wpdb->query( "TRUNCATE TABLE $options_table" );
			$pixel_table_name = $wpdb->prefix . 'clickmeter_tracking_pixels';
			$wpdb->query( "TRUNCATE TABLE $pixel_table_name" );
			$link_table_name = $wpdb->prefix . 'clickmeter_tracking_links';
			$wpdb->query( "TRUNCATE TABLE $link_table_name" );
			$flag="completed";

		}else{
			WPClickmeter::store_option("clickmeter_workinprogress_flag", "inprogress");
			log_event("API_key_delete", array($end_exclusion_index));
			echo '<script>callAjaxTP_delete('.$end_exclusion_index.');</script>';
		}
	}elseif($_GET["delete_pixels_in_post_contents"]=="true"){

        //delete all tracking pixel from pages
		$count_posts = wp_count_posts();
		$total_excluded = $count_posts -> publish;
		$private = 'private';
		if (isset($count_posts -> $private)) {
			$total_excluded += $count_posts -> $private;
		}
		$total_excluded += $count_posts -> future;


        $start_exclusion_index = 0;
        if(isset($_GET["post_offset"])) $start_exclusion_index = $_GET["post_offset"];
        $total_percentage = intval(($start_exclusion_index/$total_excluded)*100);

        $end_exclusion_index = $start_exclusion_index + DATAPOINT_BLOCK_SIZE;

        $post_ids_array = WPClickmeter::retrieve_ids_posts(DATAPOINT_BLOCK_SIZE, $start_exclusion_index);

        WPClickmeter::store_option("clickmeter_current_exclusion_list", $post_ids_array);

        if($_GET["post_offset"] >= $total_excluded){
            WPClickmeter::store_option("clickmeter_workinprogress_flag", "completed");
            $flag="completed";
        }else{
            WPClickmeter::store_option("clickmeter_workinprogress_flag", "inprogress");
            log_event("delete_pixels_in_post_contents", array($end_exclusion_index));
            echo '<script>callAjaxPixels_delete('.$end_exclusion_index.');</script>';
        }
    } elseif($_POST["pixels_flags"]=="true" || $_GET["pixels_flags"]=="true"){
		if($_POST["pixels_flags"]=="true"){
			//store settings
			WPClickmeter::store_option( 'clickmeter_pixel_flag', 1 );
			if($_POST["excluded_list"]!=NULL)
				WPClickmeter::store_option( 'clickmeter_exclusion_list', $_POST["excluded_list"]);
			if($_POST["excluded_list"]==NULL and $_POST["included_list"]!=NULL)
				WPClickmeter::store_option( 'clickmeter_exclusion_list', array());

			if($_POST["included_list"]!=NULL)
				WPClickmeter::store_option( 'clickmeter_inclusion_list', $_POST["included_list"]);
			if($_POST["included_list"]==NULL and $_POST["excluded_list"]!=NULL)
				WPClickmeter::store_option( 'clickmeter_inclusion_list', array());

			if($_POST["new_article_default"]=="true"){
				WPClickmeter::store_option('clickmeter_pixel_new_articles', 1 );
			}
			if($_POST["new_article_default"]=="false"){
				WPClickmeter::store_option('clickmeter_pixel_new_articles', 0 );
			}
		}

		$complete_exclusion_list = WPClickmeter::get_option( 'clickmeter_exclusion_list');
		$complete_inclusion_list = WPClickmeter::get_option( 'clickmeter_inclusion_list');

		$total_excluded = sizeof($complete_exclusion_list);
		$total_included = sizeof($complete_inclusion_list);

		$start_exclusion_index = 0;
		if(isset($_GET["excluded_offset"])) $start_exclusion_index = $_GET["excluded_offset"];	
		$start_inclusion_index = 0;
		if(isset($_GET["included_offset"])) $start_inclusion_index = $_GET["included_offset"];

		if($total_excluded<$total_included){
			$total_percentage = intval(($start_inclusion_index/$total_included)*100);
		}else{
			$total_percentage = intval(($start_exclusion_index/$total_excluded)*100);
		}

		if($start_exclusion_index <= $total_excluded){
			$end_exclusion_index = $start_exclusion_index + DATAPOINT_BLOCK_SIZE;
			if($end_exclusion_index > $total_excluded) $end_exclusion_index = $total_excluded;
			for($start_exclusion_index;$start_exclusion_index<$end_exclusion_index;$start_exclusion_index++){
				if($complete_exclusion_list[$start_exclusion_index]!=null) $current_exclusion_list[] = $complete_exclusion_list[$start_exclusion_index];
			}
		}
		if($start_inclusion_index <= $total_included){
			$end_inclusion_index = $start_inclusion_index + DATAPOINT_BLOCK_SIZE;
			if($end_inclusion_index > $total_included) $end_inclusion_index = $total_included;
			for($start_inclusion_index;$start_inclusion_index<$end_inclusion_index;$start_inclusion_index++){
				if($complete_inclusion_list[$start_inclusion_index]!=null) $current_inclusion_list[] = $complete_inclusion_list[$start_inclusion_index];
			}
		}

		if($_GET["included_offset"] >= $total_included && $_GET["excluded_offset"] >= $total_excluded){
			//WPClickmeter::store_option("clickmeter_debug", "pixels_flags_true");
			WPClickmeter::store_option("clickmeter_workinprogress_flag", "completed");
			$flag="completed";
		}else{
			WPClickmeter::store_option("clickmeter_workinprogress_flag", "inprogress");
			log_event("pixels_flags_true", array($end_inclusion_index, $end_exclusion_index));
			echo '<script>callAjaxTP_savechanges('.json_encode($current_inclusion_list).','.json_encode($current_exclusion_list).','.$end_inclusion_index.','.$end_exclusion_index.');</script>';
		}
		
	} elseif($_POST["pixels_flags"]=="false" || $_GET["pixels_flags"]=="false"){
		if(isset($_POST["pixels_flags"])){
			WPClickmeter::store_option( 'clickmeter_pixel_flag', 0 );
			WPClickmeter::store_option('clickmeter_pixel_new_articles', 0 );
			WPClickmeter::store_option( 'clickmeter_inclusion_list', array());
			WPClickmeter::store_option( 'clickmeter_exclusion_list', array());

            $exclusion_list = WPClickmeter::retrieve_ids_posts();
            WPClickmeter::store_option( 'clickmeter_exclusion_list', $exclusion_list);

        }

		$complete_exclusion_list = WPClickmeter::get_option( 'clickmeter_exclusion_list');
		$total_excluded = sizeof($complete_exclusion_list);

		$start_exclusion_index = 0;
		if(isset($_GET["post_offset"])) $start_exclusion_index = $_GET["post_offset"];
		$total_percentage = intval(($start_exclusion_index/$total_excluded)*100);

		if($start_exclusion_index <= $total_excluded){
			$end_exclusion_index = $start_exclusion_index + DATAPOINT_BLOCK_SIZE;
			if($end_exclusion_index > $total_excluded) $end_exclusion_index = $total_excluded;
			for($start_exclusion_index;$start_exclusion_index<$end_exclusion_index;$start_exclusion_index++){
				if($complete_exclusion_list[$start_exclusion_index]!=null) $current_exclusion_list[] = $complete_exclusion_list[$start_exclusion_index];
			}
		}

		if($_GET["post_offset"] >= $total_excluded){
			//WPClickmeter::store_option("clickmeter_debug", "pixels_flags_false");
			WPClickmeter::store_option("clickmeter_workinprogress_flag", "completed");
			$flag="completed";
		}else{
			WPClickmeter::store_option("clickmeter_workinprogress_flag", "inprogress");
			log_event("pixels_flags_false", array($end_exclusion_index));
			echo '<script>callAjaxTP_savechanges_remove('.json_encode($current_exclusion_list).','.$end_exclusion_index.');</script>';
		}
	} elseif($_POST["conversion_type"]!=null || $_GET["associate_conversion"]=="true"){
		$conversion1_id = WPClickmeter::get_option('clickmeter_conversionId1');
		$conversion2_id = WPClickmeter::get_option('clickmeter_conversionId2');

		$postID_list = WPClickmeter::retrieve_ids_posts();

		$total_postID_list = sizeof($postID_list);
		$start_index = 0;
		if(isset($_GET["post_offset"])) $start_index = $_GET["post_offset"];	
		$total_percentage = intval(($start_index/$total_postID_list)*100);

		if($start_index <= $total_postID_list){
			$end_index = $start_index + $conversion_block_size;
			if($end_index > $total_postID_list) $end_index = $total_postID_list;
			for($start_index;$start_index<$end_index;$start_index++){
				if($postID_list[$start_index]!=null) $current_post_list[] = $postID_list[$start_index];
			}
		}

		$conversion_type= $_POST["conversion_type"];
		if($_GET["associate_conversion"]=="true"){
			$conversionToAssociate = $_GET["conversionToAssociate"];

			if($_GET["post_offset"] >= $total_postID_list){
				//WPClickmeter::store_option("clickmeter_debug", "associate_conversion");
				WPClickmeter::store_option("clickmeter_workinprogress_flag", "completed");
				$flag="completed";
			}else{
				log_event("associate_conversion", array($end_index, $conversionToAssociate));
				echo '<script>callAjax_associate_conversion('.json_encode($current_post_list).','.$conversionToAssociate.','.$end_index.');</script>';	
			}
		}else{
			$conversion_target = $_POST["conversion_target_list"];
			WPClickmeter::store_option('clickmeter_lastconversion_target', $conversion_target);
			if(preg_match("/existing_conversion/", $conversion_type)){
				//ASSOCIATE EXISTING CONVERSION
				preg_match("/[0-9]*$/",$conversion_type, $match);
				$conversionToAssociate = "";
				if(!empty($match)) $conversionToAssociate = $match[0];
				//GET CONV DATA
				$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/conversions/'.$conversionToAssociate, 'GET', NULL, $api_key);
				$conversion_name = $json_output[name];

				if($conversion1_id!=null){
					WPClickmeter::store_option("clickmeter_conversionId2", $conversionToAssociate);
					WPClickmeter::store_option("clickmeter_conversionName2", $conversion_name);
					WPClickmeter::store_option("clickmeter_conversionTarget2", $conversion_target);
				}
				else{
					WPClickmeter::store_option("clickmeter_conversionId1", $conversionToAssociate);
					WPClickmeter::store_option("clickmeter_conversionName1", $conversion_name);
					WPClickmeter::store_option("clickmeter_conversionTarget1", $conversion_target);
				}
				WPClickmeter::store_option("clickmeter_workinprogress_flag", "inprogress");
				log_event("associate_conversion", array($end_index, $conversionToAssociate));
				echo '<script>callAjax_associate_conversion('.json_encode($current_post_list).','.$conversionToAssociate.','.$end_index.');</script>';	
			}else{
				//CREATE CONVERSION CODE
				$body=array('description'=>'Conversion code created from my ClickMeter WP plugin - '.date("Y/m/d"),'name'=> $blog_name.'-'.$conversion_type );
				$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/conversions','POST', json_encode($body), $api_key);
				$conversionId = $json_output[id];
				$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/conversions/'.$conversionId, 'GET', NULL, $api_key);
				$conversion_name = $json_output[name];

				if($conversion1_id!=null){
					WPClickmeter::store_option("clickmeter_conversionId2", $conversionId);
					WPClickmeter::store_option("clickmeter_conversionName2", $conversion_name);
					WPClickmeter::store_option("clickmeter_conversionTarget2", $conversion_target);
				}
				else{
					WPClickmeter::store_option("clickmeter_conversionId1", $conversionId);
					WPClickmeter::store_option("clickmeter_conversionName1", $conversion_name);
					WPClickmeter::store_option("clickmeter_conversionTarget1", $conversion_target);
				}
				WPClickmeter::store_option("clickmeter_workinprogress_flag", "inprogress");
				log_event("associate_conversion", array($end_index, $conversionId));
				echo '<script>callAjax_associate_conversion('.json_encode($current_post_list).','.$conversionId.','.$end_index.');</script>';	
			}
		}
	} elseif($_POST["conversion_delete"]!=NULL || $_GET["conversion_delete"]=="true"){

        $postID_list = WPClickmeter::retrieve_ids_posts();

		$total_postID_list = sizeof($postID_list);
		$start_index = 0;
		if(isset($_GET["post_offset"])) $start_index = $_GET["post_offset"];	
		$total_percentage = intval(($start_index/$total_postID_list)*100);

		if($start_index <= $total_postID_list){
			$end_index = $start_index + $conversion_block_size;
			if($end_index > $total_postID_list) $end_index = $total_postID_list;
			for($start_index;$start_index<$end_index;$start_index++){
				if($postID_list[$start_index]!=null) $current_post_list[] = $postID_list[$start_index];
			}
		}

		if($_POST["conversion_delete"] == "1"){
			$conversion1_id = WPClickmeter::get_option('clickmeter_conversionId1');
			WPClickmeter::store_option("clickmeter_conversionId1", "");
			if($conversion1_id == WPClickmeter::get_option("clickmeter_default_firstConv_links")) WPClickmeter::store_option("clickmeter_default_firstConv_links", "");
			WPClickmeter::store_option("clickmeter_workinprogress_flag", "inprogress");
			log_event("conversion_delete", array($end_index, $conversion1_id));
			echo '<script>callAjax_delete_conversion('.json_encode($current_post_list).','.$conversion1_id.','.$end_index.');</script>';
		}elseif($_POST["conversion_delete"] == "2"){
			$conversion2_id = WPClickmeter::get_option('clickmeter_conversionId2');
			WPClickmeter::store_option("clickmeter_conversionId2", "");
			if($conversion2_id == WPClickmeter::get_option("clickmeter_default_secondConv_links")) WPClickmeter::store_option("clickmeter_default_secondConv_links", "");
			WPClickmeter::store_option("clickmeter_workinprogress_flag", "inprogress");
			log_event("conversion_delete", array($end_index, $conversion2_id));
			echo '<script>callAjax_delete_conversion('.json_encode($current_post_list).','.$conversion2_id.','.$end_index.');</script>';
		}else{
			$conversionToDelete = $_GET["conversionToDelete"];
			if($_GET["post_offset"] >= $total_postID_list){
				WPClickmeter::store_option("clickmeter_workinprogress_flag", "completed");
				$flag="completed";
			}else{
				log_event("conversion_delete", array($end_index, $conversionToDelete));
				echo '<script>callAjax_delete_conversion('.json_encode($current_post_list).','.$conversionToDelete.','.$end_index.');</script>';	
			}
		}
	} elseif($_GET["error_flag"]=="true" || WPClickmeter::get_option("clickmeter_workinprogress_flag")=="error"){
		WPClickmeter::store_option("clickmeter_workinprogress_flag", "error");
		$flag = "error";
		$action = $_GET["action"];
		if(isset($_GET["timestamp"])) WPClickmeter::store_option("clickmeter_lastError_message", $_GET["error_message"]);
		$error_message = WPClickmeter::get_option("clickmeter_lastError_message").".";

		$subject = 'restart execution';
		$content = WPClickmeter::trace_variable($action, "action");
		$content = $content . WPClickmeter::trace_variable($error_message, "error_message");
		WPClickmeter::to_email($subject, $content);

		if(isset($_GET["timestamp"])) WPClickmeter::store_option("clickmeter_lastError_date", $_GET["timestamp"]);
		$error_timestamp = "[".WPClickmeter::get_option("clickmeter_lastError_date")."] - ";
	} else{
		$flag = "interrupted";
	}

?>

<style>
.spinner_cm {
	background: url('/wp-content/plugins/clickmeter-link-shortener-and-analytics/img/spinner.gif') no-repeat;
	background-size: 50px 50px;
	display: block;
	width: 50px;
	height: 50px;
	padding:5px;
}
</style>

<html>
<body>
	<div style="background-color:#E0E0E0;padding-top: 5px;padding-left: 5px;padding-right: 5px;padding-bottom: 5px;">
		<h1>ClickMeter Link Shortener and Analytics‏</h1>
	</div>
	<br><br><br><br>
	<?php if($flag == "interrupted") : ?>
	<center>
		<h2>A previous operation has been interrupted!</h2><br>
		<table>
			<tr>
				<td>
					<form action="" method="post">
						<input type="hidden" name="continue_execution" value="false"/>
						<!--<input type="submit" style="font-size: 15px;margin: 20px;padding: 15px;width:180px;" class='clickmeter-button-grey' value="Rollback changes"> -->
					</form>
				</td>
				<td>
					<form action="" method="post">
						<input type="hidden" name="continue_execution" value="true"/>
						<input type="submit" style="font-size: 15px;padding: 15px;width:180px;" class='clickmeter-button' value="Continue execution"> 
					</form>
				</td>
			</tr>
		</table>
	</center>
	<?php elseif($flag == "completed") : ?>
		<center>
			<h2 style="color:green;">Operation completed!</h2>
			<h3>Click on the button below to get back on plugin's main page.</h3><br><br>
			<a class="clickmeter_link" href="?page=clickmeter-link-shortener-and-analytics/view/clickmeter-account.php"><span style="font-size: 15px;padding: 15px;width:180px;" class="clickmeter-button">Continue</span></a>
		</center>
	<?php elseif($flag == "error") : ?>
	<center>
		<h2 style="color:red;">Oops! An error occured during execution.</h2>
		<p><span><?php echo $error_timestamp . $error_message; ?></span><span> Please try later or contact our <a target='_blank' href='mailto:support@clickmeter.com?subject=Error message from WordPress plugin'>support</a>.</span></p><br>
		<form action="" method="post">
			<input type="hidden" name="restart_execution" value="true"/>
			<input type="submit" style="font-size: 15px;padding: 15px;width:180px;" class='clickmeter-button' value="Restart execution"> 
		</form>
	</center>
	<?php else : ?>
	<center>
		<div class="spinner_cm"></div>
		<h2>An operation is currently in execution. Please wait.</h2>
		<p>Depending on the number of posts/pages affected by this operation, it may takes several minutes (hours) to complete. <a style="padding:5px" href="?page=clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php">Stop and continue later.</a></p>
		<br>
		<?php 
			if($total_percentage <= 2) $total_percentage = 2;
			if($total_percentage == 100) $total_percentage = 98;
		?>
		<div style="width:80%" class="progress">
		  <div class="progress-bar"  role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: <?php echo $total_percentage . '%'; ?>">
		    <?php echo $total_percentage . '%'; ?>
		  </div>
		</div>
	</center>

	<?php endif; ?>
</body>
</html>
