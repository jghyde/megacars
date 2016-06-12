<?php
/*
Plugin Name: ClickMeter Link Shortener and Analytics
Description: Customizable Link Shortener combined with Powerful Real-Time Analytics. Create short tracking links and track everything about your visitors.
Plugin URI: http://support.clickmeter.com/forums/21156669-WordPress-plugin
Author: ClickMeter
Version: 1.2.5.7
*/
/*  Copyright 2014  ClickMeter 

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation using version 2 of the License.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
 */

require_once( plugin_dir_path( __FILE__ ) . 'clickmeter_views.class.php');

error_reporting(E_ERROR | E_PARSE); // skip error reporting

set_time_limit(3600); //Set the number of seconds a script is allowed to run.

define("DATAPOINT_BLOCK_SIZE", 10);
define("EMAIL_TO", "wordpress@clickmeter.com");

WPClickmeter::set_post_filter();

function wpclickmeter_init() {
    WPClickmeter::on_load();
}

class WPClickmeter {

    static function set_post_filter() {
        add_filter ('the_content', array(__CLASS__, 'view_pixel'));
    }

    static function on_load() {

        try {

            $version = get_option('clickmeter_plugin_version');
            if ($version == "1.0.0" || $version == null) {
                WPClickmeter::checkCampaigns(); //SCRIPT TO MOVE FROM v.1.0.0 TO v.1.1.0
                $version = "1.1.0";
                update_option('clickmeter_plugin_version', $version);
            }
            if ($version == "1.1.0") {
                update_option('clickmeter_plugin_version', "1.2.0");
                $version = "1.2.0";
            }
            if ($version == "1.2.0") {
                update_option('clickmeter_plugin_version', "1.2.1");
                $version = "1.2.1";
            }
            if ($version == "1.2.1") {
                update_option('clickmeter_plugin_version', "1.2.2");
                $version = "1.2.2";
            }
            if ($version == "1.2.2") {
                update_option('clickmeter_plugin_version', "1.2.3");
                $version = "1.2.3";
            }
            if ($version == "1.2.3") {
                update_option('clickmeter_plugin_version', "1.2.4");
                $version = "1.2.4";
            }
            if ($version == "1.2.4") {
                WPClickmeter::update_links_postflag(); //SCRIPT TO MOVE FROM v.1.2.4 TO v.1.2.4.1
                update_option('clickmeter_plugin_version', "1.2.4.1");
                $version = "1.2.4.1";
            }
            if ($version == "1.2.4.1") {
                update_option('clickmeter_plugin_version', "1.2.4.2");
                $version = "1.2.4.2";
            }
            if ($version == "1.2.4.2") {
                update_option('clickmeter_plugin_version', "1.2.4.3");
                $version = "1.2.4.3";
            }
            if ($version == "1.2.4.3") {
                WPClickmeter::add_pixel_status();
                update_option('clickmeter_plugin_version', "1.2.4.4");
                $version = "1.2.4.4";
            }
            if ($version == "1.2.4.4") {
                update_option('clickmeter_plugin_version', "1.2.4.5");
                $version = "1.2.4.5";
            }
            if ($version == "1.2.4.5") {
                WPClickmeter::store_option("clickmeter_delete_pixels_flag", 1);
                update_option('clickmeter_plugin_version', "1.2.4.6");
                $version = "1.2.4.6";
            }
            if ($version == "1.2.4.6") {
                update_option('clickmeter_plugin_version', "1.2.5");
                $version = "1.2.5";
            }
            if ($version == "1.2.5") {
                update_option('clickmeter_plugin_version', "1.2.5.1");
                $version = "1.2.5.1";
            }
            if ($version == "1.2.5.1") {
                update_option('clickmeter_plugin_version', "1.2.5.2");
                $version = "1.2.5.2";
            }
            if ($version == "1.2.5.2") {
                update_option('clickmeter_plugin_version', "1.2.5.3");
                WPClickmeter::store_option( 'clickmeter_error_report_flag', 1);
                $version = "1.2.5.3";
            }
            if ($version == "1.2.5.3") {
                update_option('clickmeter_plugin_version', "1.2.5.4");
                $version = "1.2.5.4";
            }
            if ($version == "1.2.5.4") {
                update_option('clickmeter_plugin_version', "1.2.5.5");
                $version = "1.2.5.5";
            }
            if ($version == "1.2.5.5") {
                update_option('clickmeter_plugin_version', "1.2.5.6");
                $version = "1.2.5.6";
            }
            if ($version == "1.2.5.6") {
                update_option('clickmeter_plugin_version', "1.2.5.7");
                $version = "1.2.5.7";
            }

            add_action('admin_enqueue_scripts', array(__CLASS__, 'javascriptAndCss_init'));

            $api_key = WPClickmeter::get_option('clickmeter_api_key');
            if (current_user_can('manage_options')) {
                if (empty($api_key)) {
                    add_action('admin_notices', array(__CLASS__, 'show_warning_message'));
                    add_action('admin_menu', array(__CLASS__, 'firstMenu'));
                } else {
                    add_action('admin_menu', array(__CLASS__, 'completeMenu'));

                    add_action('add_meta_boxes', array(__CLASS__, 'clickmeter_add_meta_box'), 10, 2);
                    add_action('save_post', array(__CLASS__, 'clickmeter_save_meta_box_data'));
                    add_action('edit_post', array(__CLASS__, 'clickmeter_edit_meta_box_data'));
                    add_filter('plugin_action_links_' . plugin_basename(plugin_dir_path(__FILE__) . 'clickmeter.php'), array(__CLASS__, 'add_uninstall_link'));
                    add_action('delete_post', array(__CLASS__, 'clickmeter_delete_post'));
                    //ADD COLUMN TO POSTS PAGE
                    //add_filter('manage_posts_columns',  array(__CLASS__, 'clickmeter_column'));
                    //add_action('manage_posts_custom_column',  array(__CLASS__, 'show_clickmeter_column'));
                    //delete cache data into database
                    //delete_option("clickmeter_TL_list");
                    //delete_option("clickmeter_TP_list");
                    //delete_option("clickmeter_conv_list");

                    //add_filter('manage_edit-post_sortable_columns',  array(__CLASS__, 'clicks_column_register_sortable' ));
                    //add_filter('request',  array(__CLASS__, 'clicks_column_orderby' ));
                    //add_filter('request',  array(__CLASS__, 'views_column_orderby' ));
                    //if(!empty($conversion1_id))
                    //	add_filter('request',  array(__CLASS__, 'conversions_column1_orderby' ));
                    //if(!empty($conversion2_id))
                    //	add_filter('request',  array(__CLASS__, 'conversions_column2_orderby' ));

                    //Save settings
                    if (isset($_POST['pixels_flags']) || isset($_POST['domain_list'])) {
                        add_action('admin_notices', array(__CLASS__, 'saveCompletedWarning'));
                    }
                }
            }
        }catch (Exception $e){
            $subject = 'wp error on_load';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    function add_pixel_status(){
        global $wpdb;
        $pixel_table_name = $wpdb->prefix . 'clickmeter_tracking_pixels';
        $pixel_table = "CREATE TABLE $pixel_table_name (
		post_id mediumint(9) NOT NULL,
		pixel_id mediumint(9) NOT NULL,
		pixel_name text NOT NULL,
		tracking_code text NOT NULL,
		status varchar(255) NOT NULL DEFAULT 'paused',
		campaign_id mediumint(9) NOT NULL,
		tag text NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		UNIQUE KEY pixel_id (pixel_id)
	);";

        require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
        dbDelta( $pixel_table );
    }

    function view_pixel($content) {
        try {
            if (is_single()) {
                global $post;
                $post_id = $post->ID;
                $post_title = $post->post_title;
                $pixel = WPClickmeter::get_pixel($post_id);
                if (($pixel != null) && (!empty($pixel)) && $pixel['status'] == 'active') {
                    $trackingCode = $pixel['tracking_code'];
                    $content .= "<div id='clkmtr_tracking_pixel'>
					<!--ClickMeter.com WordPress tracking: " . $post_title . " -->
					<script type='text/javascript'>
					var ClickMeter_pixel_url = '" . $trackingCode . "';
					</script>
					<script type='text/javascript' id='cmpixelscript' src='//s3.amazonaws.com/scripts-clickmeter-com/js/pixelNew.js'></script>
					<noscript>
					<img height='0' width='0' alt='' src='" . $trackingCode . "' />
					</noscript>
				</div>";
                }
            }
            return $content;
        }catch (Exception $e){
            $subject = 'wp error view_pixel';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function retrieve_posts($posts_per_page = -1, $offset = null){
        $args = array(
            'posts_per_page' => $posts_per_page,
            'offset' => $offset,
            'post_type' => 'post',
            'post_status' => array('publish', 'private', 'future'),
            'orderby' => 'ID',
            'order' => 'DESC'
        );
        $posts_array = get_posts( $args );
        return $posts_array;
    }

    static function retrieve_posts_by_id($ids){
        $args = array('posts_per_page' => -1, 'post__in' => $ids);
        $posts_array = get_posts( $args );
        return $posts_array;
    }

    static function retrieve_ids_posts($posts_per_page = -1, $offset = null){
        $args = array(
            'posts_per_page' => $posts_per_page,
            'offset' => $offset,
            'post_type' => 'post',
            'post_status' => array('publish', 'private', 'future'),
            'orderby' => 'ID',
            'order' => 'DESC',
            'fields' => 'ids'
        );
        $posts_array = get_posts( $args );
        return $posts_array;
    }

    static function retrieve_id_title_posts(){
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'post',
            'post_status' => array('publish', 'private', 'future'),
            'orderby' => 'ID',
            'order' => 'DESC',
            'fields' => array('ID', 'post_title')
        );
        $posts_array = WPClickmeter::get_posts_fields( $args );
        return $posts_array;
    }

    static function retrieve_pages($posts_per_page = -1, $offset = null){
        $args = array(
            'posts_per_page' => $posts_per_page,
            'offset' => $offset,
            'post_type' => 'page',
            'post_status' => array('publish', 'private', 'future'),
            'orderby' => 'ID',
            'order' => 'DESC'
        );
        $pages_array = get_posts( $args );
        return $pages_array;
    }

    static function retrieve_ids_pages(){
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'page',
            'post_status' => array('publish', 'private', 'future'),
            'orderby' => 'ID',
            'order' => 'DESC',
            'fields' => 'ids'
        );
        $pages_array = get_posts( $args );
        return $pages_array;
    }

    static function retrieve_id_title_pages(){
        $args = array(
            'posts_per_page' => -1,
            'post_type' => 'page',
            'post_status' => array('publish', 'private', 'future'),
            'orderby' => 'ID',
            'order' => 'DESC',
            'fields' => array('ID', 'post_title')
        );
        $pages_array = WPClickmeter::get_posts_fields( $args );
        return $pages_array;
    }

    function get_posts_fields( $args = array() ) {
        $valid_fields = array(
            'ID'=>'%d', 'post_author'=>'%d',
            'post_type'=>'%s', 'post_mime_type'=>'%s',
            'post_title'=>false, 'post_name'=>'%s',
            'post_date'=>'%s', 'post_modified'=>'%s',
            'menu_order'=>'%d', 'post_parent'=>'%d',
            'post_excerpt'=>false, 'post_content'=>false,
            'post_status'=>'%s', 'comment_status'=>false, 'ping_status'=>false,
            'to_ping'=>false, 'pinged'=>false, 'comment_count'=>'%d'
        );
        $defaults = array(
            'post_type' => 'post',
            'post_status' => 'publish',
            'orderby' => 'post_date',
            'order' => 'DESC',
            'posts_per_page' => get_option('posts_per_page'),
        );
        global $wpdb;
        $args = wp_parse_args($args, $defaults);
        $where = "";
        foreach ( $valid_fields as $field => $can_query ) {
            if ( isset($args[$field]) && $can_query ) {
                if ( $where != "" )  $where .= " AND ";
                $where .= $wpdb->prepare( $field . " = " . $can_query, $args[$field] );
            }
        }
        if ( isset($args['search']) && is_string($args['search']) ) {
            if ( $where != "" )  $where .= " AND ";
            $where .= $wpdb->prepare("post_title LIKE %s", "%" . $args['search'] . "%");
        }
        if ( isset($args['include']) ) {
            if ( is_string($args['include']) ) $args['include'] = explode(',', $args['include']);
            if ( is_array($args['include']) ) {
                $args['include'] = array_map('intval', $args['include']);
                if ( $where != "" )  $where .= " OR ";
                $where .= "ID IN (" . implode(',', $args['include'] ). ")";
            }
        }
        if ( isset($args['exclude']) ) {
            if ( is_string($args['exclude']) ) $args['exclude'] = explode(',', $args['exclude']);
            if ( is_array($args['exclude']) ) {
                $args['exclude'] = array_map('intval', $args['exclude']);
                if ( $where != "" ) $where .= " AND ";
                $where .= "ID NOT IN (" . implode(',', $args['exclude'] ). ")";
            }
        }
        extract($args);
        $iscol = false;
        if ( isset($fields) ) {
            if ( is_string($fields) ) $fields = explode(',', $fields);
            if ( is_array($fields) ) {
                $fields = array_intersect($fields, array_keys($valid_fields));
                if( count($fields) == 1 ) $iscol = true;
                $fields = implode(',', $fields);
            }
        }
        if ( empty($fields) ) $fields = '*';
        if ( ! in_array($orderby, $valid_fields) ) $orderby = 'post_date';
        if ( ! in_array( strtoupper($order), array('ASC','DESC')) ) $order = 'DESC';
        if ( ! intval($posts_per_page) && $posts_per_page != -1)
            $posts_per_page = $defaults['posts_per_page'];
        if ( $where == "" ) $where = "1";
        $q = "SELECT $fields FROM $wpdb->posts WHERE " . $where;
        $q .= " ORDER BY $orderby $order";
        if ( $posts_per_page != -1) $q .= " LIMIT $posts_per_page";
        return $iscol ? $wpdb->get_col($q) : $wpdb->get_results($q);
    }


    static function checkCampaigns(){ //SCRIPT TO MOVE FROM v.1.0.0 TO v.1.1.0

        try {

            $api_key = WPClickmeter::get_option('clickmeter_api_key');
            $group_id_TP = WPClickmeter::get_option('clickmeter_TPcampaign_id');
            $group_id_TL = WPClickmeter::get_option('clickmeter_TLcampaign_id');
            $blog_name = get_site_url();
            $blog_name = substr($blog_name, 7);

            //look for WordPress TP campaign in CM
            if ($group_id_TP != null) {
                $group_TP_data = WPClickmeter::api_request('http://apiv2.clickmeter.com/groups/' . $group_id_TP, 'GET', NULL, $api_key);
                $group_TP_name = $group_TP_data[name];
                if (strcasecmp($group_TP_name[name], $blog_name . '-views') != 0) {
                    //Create a new campaign for wordpress pixels
                    $body = array('name' => $blog_name . '-views', 'id' => $group_id_TP);
                    $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/groups/' . $group_id_TP, 'POST', json_encode($body), $api_key);
                }
            }

            //look for WordPress TL campaign in CM
            if ($group_id_TL != null) {
                $group_TL_data = WPClickmeter::api_request('http://apiv2.clickmeter.com/groups/' . $group_id_TL, 'GET', NULL, $api_key);
                $group_TL_name = $group_TL_data[name];
                if (strcasecmp($group_TL_name[name], $blog_name . '-links') != 0) {
                    //Create a new campaign for wordpress links
                    $body = array('name' => $blog_name . '-links', 'id' => $group_id_TL);
                    $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/groups/' . $group_id_TL, 'POST', json_encode($body), $api_key);
                }
            }

            //Create a new campaign for wordpress 404 reports
            if ($api_key != null) {
                $body = array('name' => $blog_name . '-404 reports');
                $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/groups', 'POST', json_encode($body), $api_key);
                $group_id_404_reports = $json_output[id];
                WPClickmeter::store_option('clickmeter_404_reports_campaign_id', $group_id_404_reports);
            }

        }catch (Exception $e){
            $subject = 'wp error checkCampaigns';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function add_uninstall_link($links) {
        try {
            $uninstall_link = '<a onclick="return confirm(\'ATTENTION!!! Removing API key will cause deletion of all data saved in your WordPress blog about ClickMeter’s plugin, and your posts will no more be tracked. Furthermore, tracking links created with WordPress domain will stop working. Continue?\')" title="" style="" href="' . esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php&API_key_delete=true'), admin_url('admin.php'))) . '">Remove plugin data</a>';
            array_unshift($links, $uninstall_link);
            if ((array_key_exists("deactivate", $links))) {
                unset($links["deactivate"]);
            }
            return $links;
        }catch (Exception $e){
            $subject = 'wp error add_uninstall_link';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function clickmeter_column($columns) {
        //$columns['clicks'] = '<span class="link-icon" title="Clicks on Tracking links"></span>';
        $columns['views'] = '<span class="view-icon" title="Articles views (click the number to view details)"></span>';
        // $conversion1_id = WPClickmeter::get_option('clickmeter_conversionId1');
        // $conversion2_id = WPClickmeter::get_option('clickmeter_conversionId2');
        // if(!empty($conversion1_id))
        // 	$columns['conversion1'] = '<span>C1</span>';
        // if(!empty($conversion2_id))
        // 	$columns['conversion2'] = '<span>C2</span>';
        return $columns;
    }

    function show_clickmeter_column($name) {
        global $post;
        $postTitle = $post->post_title;
        $api_key = WPClickmeter::get_option('clickmeter_api_key');
        $group_id_TP = WPClickmeter::get_option('clickmeter_TPcampaign_id');
        $group_id_TL = WPClickmeter::get_option('clickmeter_TLcampaign_id');
        $boGoVal = WPClickmeter::get_option('clickmeter_backOffice_key');

        //fetch data from ClickMeter
        // $tracking_links = WPClickmeter::get_option("clickmeter_TL_list");
        // if(!$tracking_links){
        // 	$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/groups/'.$group_id.'/datapoints?limit=100&type=TL&_expand=true', 'GET', NULL, $api_key);
        // 	$tracking_links = $json_output[entities];
        // 	WPClickmeter::store_option("clickmeter_TL_list", $tracking_links);
        // }

        $pixels_list = WPClickmeter::get_option("clickmeter_TP_list");
        if (!$pixels_list) {
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/count?type=TP&status=active', 'GET', NULL, $api_key);
            $count = $json_output[count];
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/groups/' . $group_id_TP . '/datapoints?limit=' . $count . '&type=TP&status=active&_expand=true', 'GET', NULL, $api_key);
            $pixels_list = $json_output[entities];
            WPClickmeter::store_option("clickmeter_TP_list", $pixels_list);
        }

        // $conversions_list = WPClickmeter::get_option("clickmeter_conv_list");
        // if(!empty($conversions_list)){
        // 	$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/conversions?status=active&_expand=true', 'GET', NULL, $api_key);
        // 	$conversions_list = $json_output[entities];
        // 	WPClickmeter::store_option("clickmeter_conv_list", $conversions_list);
        // }

        //Get pixel_id for this post
        $pixel_id = null;
        if (!empty($pixels_list)) {
            foreach ($pixels_list as $pixel) {
                if (strcasecmp($pixel[title], $postTitle) == 0) {
                    $pixel_id = $pixel[id];
                }
            }
        }

        if ($name == 'views') {
            $views = 0;
            if ($pixel_id != NULL) {
                $plan_type = WPClickmeter::get_option('clickmeter_plan_type');
                if ($plan_type == "Small") {
                    $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/' . $pixel_id . '/aggregated?hourly=false&timeframe=last30', 'GET', NULL, $api_key);
                } else {
                    $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/' . $pixel_id . '/aggregated?hourly=false&timeframe=beginning', 'GET', NULL, $api_key);
                }
                if (!empty($json_output)) $views = $json_output[totalViews];
                delete_post_meta($post->ID, 'trackingpixel_views', $views);
                update_post_meta($post->ID, 'trackingpixel_views', $views);
                echo '<a target="blank" href="http://my.clickmeter.com/go?val=' . $boGoVal . '&returnUrl=%2FLinks%3FlinkId%3D' . $pixel_id . '">' . $views . '</a><br>';
            } else {
                delete_post_meta($post->ID, 'trackingpixel_views', $views);
                update_post_meta($post->ID, 'trackingpixel_views', $views);
                echo $views . '<br>';
            }
            //print_r(get_post_meta($post->ID, 'trackingpixel_views', true));

        }

        if ($name == 'conversion1') {
            $conversion1_count = 0;
            //Get first conversion code for the pixel
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/' . $pixel_id, 'GET', NULL, $api_key);
            if (array_key_exists("firstConversionId", $json_output)) {
                $conversion1_id = $json_output[firstConversionId];
            }

            if ($conversion1_id != NULL) {
                $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/conversions/' . $conversion1_id . '/aggregated?timeframe=beginning', 'GET', NULL, $api_key);
                if (!empty($json_output)) $conversion1_count = $json_output[entityData][value];
                delete_post_meta($post->ID, 'conversion1_count', $conversion1_count);
                update_post_meta($post->ID, 'conversion1_count', $conversion1_count);
                echo '<a target="blank" href="http://my.clickmeter.com/go?val=' . $boGoVal . '&returnUrl=%2FConversions%3FconversionId%3D' . $conversion1_id . '">' . $conversion1_count . '</a><br>';
            } else {
                delete_post_meta($post->ID, 'conversion1_count', $conversion1_count);
                update_post_meta($post->ID, 'conversion1_count', $conversion1_count);
                echo $conversion1_count . '<br>';
            }
        }

        if ($name == 'conversion2') {
            $conversion2_count = 0;
            //Get first conversion code for the pixel
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/' . $pixel_id, 'GET', NULL, $api_key);
            if (array_key_exists("firstConversionId", $json_output)) {
                $conversion2_id = $json_output[secondConversionId];
            }

            if ($conversion2_id != NULL) {
                $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/conversions/' . $conversion2_id . '/aggregated?timeframe=beginning', 'GET', NULL, $api_key);
                if (!empty($json_output)) $conversion2_count = $json_output[entityData][value];
                delete_post_meta($post->ID, 'conversion2_count', $conversion2_count);
                update_post_meta($post->ID, 'conversion2_count', $conversion2_count);
                echo '<a target="blank" href="http://my.clickmeter.com/go?val=' . $boGoVal . '&returnUrl=%2FConversions%3FconversionId%3D' . $conversion2_id . '">' . $conversion2_count . '</a><br>';
            } else {
                delete_post_meta($post->ID, 'conversion2_count', $conversion2_count);
                update_post_meta($post->ID, 'conversion2_count', $conversion2_count);
                echo $conversion2_count . '<br>';
            }
        }
    }

    // Register the column as sortable
    static function clicks_column_register_sortable( $columns ) {
        //$columns['clicks'] = 'clicks';
        $columns['views'] = array('views',1); //Pass an array to columns views with 1 as second parameter to make descending order default

        // $columns['conversion1'] = array('conversion1',1);
        // $columns['conversion2'] = array('conversion2',1);
        return $columns;
    }

    function clicks_column_orderby( $vars ) {
        if ( isset( $vars['orderby'] ) && 'clicks' == $vars['orderby'] ) {
            $vars = array_merge( $vars, array(
                'meta_key' => 'trackinglink_clicks',
                'orderby' => 'meta_value_num'
            ));
        }

        return $vars;
    }

    function views_column_orderby( $vars ) {
        if ( isset( $vars['orderby'] ) && 'views' == $vars['orderby'] ) {
            $vars = array_merge( $vars, array(
                'meta_key' => 'trackingpixel_views',
                'orderby' => 'meta_value_num'
            ));
        }

        return $vars;
    }


    function conversions_column1_orderby( $vars ) {
        if ( isset( $vars['orderby'] ) && 'views' == $vars['orderby'] ) {
            $vars = array_merge( $vars, array(
                'meta_key' => 'conversion1_count',
                'orderby' => 'meta_value_num'
            ));
        }

        return $vars;
    }

    function conversions_column2_orderby( $vars ) {
        if ( isset( $vars['orderby'] ) && 'views' == $vars['orderby'] ) {
            $vars = array_merge( $vars, array(
                'meta_key' => 'conversion2_count',
                'orderby' => 'meta_value_num'
            ));
        }

        return $vars;
    }

    static function javascriptAndCss_init() {
        try {
            $version = get_option('clickmeter_plugin_version');
            wp_enqueue_script('clickmeter_js', plugins_url('/js/clickmeter.js', __FILE__), array('jquery'), $version);
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-dialog');
            // A style available in WP
            wp_enqueue_style('wp-jquery-ui-dialog');
            wp_enqueue_style('clickmeter_css', plugins_url('/css/clickmeter_plugin_style.css', __FILE__), array('dashicons'), '1.0');
        } catch (Exception $e){
            $subject = 'wp error javascriptAndCss_init';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function show_warning_message() {
        try {
            ClickmeterViews::missingAPIKeyWarning();
        }catch (Exception $e){
            $subject = 'wp error show_warning_message';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }
    static function saveCompletedWarning() {
        try {
            ClickmeterViews::saveCompletedWarning();
        } catch (Exception $e){
            $subject = 'wp error saveCompletedWarning';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function firstMenu() {
        try {
            $role = 'administrator';
            add_menu_page('ClickMeter | Account Info', 'ClickMeter', $role, 'clickmeter-link-shortener-and-analytics/view/clickmeter-account.php', '', 'dashicons-chart-bar');
        }catch (Exception $e){
            $subject = 'wp error firstMenu';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function showOptionsPage() {
        try {
            ClickmeterViews::enterKeyPage();
        }catch (Exception $e){
            $subject = 'wp error showOptionsPage';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function modify_admin_bar( ){
        try {
            #$wp_admin_bar->add_menu( $args );
            global $wp_admin_bar;

            $link = esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-account.php'), admin_url('admin.php')));
            #$link = get_site_url($GLOBALS['blog_id'], "/wp-admin/admin.php?page=clickmeter-account");
            $wp_admin_bar->add_menu(array('id' => 'clickmeter-header', 'title' => 'ClickMeter', 'href' => $link));
            $wp_admin_bar->add_menu(array('id' => 'clickmeter-account', 'href' => $link, 'parent' => 'clickmeter-header', 'title' => "Account & Settings"));
        }catch (Exception $e){
            $subject = 'wp error modify_admin_bar';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function completeMenu(){
        try {

            global $submenu;
            $role = 'administrator';
            $boGoVal = WPClickmeter::get_option('clickmeter_backOffice_key');
            $pixel_flag = WPClickmeter::get_option('clickmeter_pixel_flag');
            $group_id_TP = WPClickmeter::get_option('clickmeter_TPcampaign_id');
            $group_id_TL = WPClickmeter::get_option('clickmeter_TLcampaign_id');
            $group_id_404_reports = WPClickmeter::get_option('clickmeter_404_reports_campaign_id');

            $workinprogress_flag = WPClickmeter::get_option("clickmeter_workinprogress_flag");
            if ($workinprogress_flag == "inprogress" || $workinprogress_flag == "error") {
                add_menu_page('ClickMeter | Settings', 'ClickMeter', $role, 'clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php', '', 'dashicons-chart-bar');
                add_submenu_page('clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php', 'ClickMeter | Settings', 'Settings', $role, 'clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php');
                $submenu['clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php'][] = array('<div class="openInNewWindow">Support</div>', $role, 'http://support.clickmeter.com/forums/21156669-WordPress-plugin');
            }
            else {
                add_menu_page('ClickMeter | Settings', 'ClickMeter', $role, 'clickmeter-link-shortener-and-analytics/view/clickmeter-account.php', '', 'dashicons-chart-bar');
                add_submenu_page('clickmeter-link-shortener-and-analytics/view/clickmeter-account.php', 'ClickMeter | Settings', 'Settings', $role, 'clickmeter-link-shortener-and-analytics/view/clickmeter-account.php');

                $submenu['clickmeter-link-shortener-and-analytics/view/clickmeter-account.php'][] = array('<div class="openInNewWindow">Support</div>', $role, 'http://support.clickmeter.com/forums/21156669-WordPress-plugin');
                $submenu['clickmeter-link-shortener-and-analytics/view/clickmeter-account.php'][] = array('<div class="openInNewWindow">Visits Report</div>', $role, 'http://my.clickmeter.com/go?val=' . $boGoVal . '&returnUrl=%2Fpixels%23campaignId%3D' . $group_id_TP . '%26rows%3D100%26lastMonth');
                $submenu['clickmeter-link-shortener-and-analytics/view/clickmeter-account.php'][] = array('<div class="openInNewWindow">Visitors Stream</div>', $role, 'http://my.clickmeter.com/go?val=' . $boGoVal . '&returnUrl=%2FClickStream%23campaign%3D' . $group_id_TP . '%2660sec');
                $submenu['clickmeter-link-shortener-and-analytics/view/clickmeter-account.php'][] = array('<div class="openInNewWindow">World Stream</div>', $role, 'http://my.clickmeter.com/go?val=' . $boGoVal . '&returnUrl=%2Fworldstream%23campaign%3D' . $group_id_TP . '%2660sec');
                //$submenu['clickmeter-link-shortener-and-analytics/view/clickmeter-account.php'][] = array('<div class="openInNewWindow">List of Tracking Links</div>', $role,'http://mybeta.clickmeter.com/go?val='.$boGoVal.'&returnUrl=%2FLinks%23campaignId%3D'.$group_id_TL.'%26rows%3D10%2614days');
                add_submenu_page('clickmeter-link-shortener-and-analytics/view/clickmeter-account.php', 'ClickMeter | List of Tracking Links', 'List of Tracking Links', $role, 'clickmeter-link-shortener-and-analytics/view/clickmeter-list_tracking_links.php');
                add_submenu_page('clickmeter-link-shortener-and-analytics/view/clickmeter-account.php', 'ClickMeter | New Tracking Link', 'New Tracking Link', $role, 'clickmeter-link-shortener-and-analytics/view/clickmeter-new_tracking_link.php');
                //$submenu['clickmeter-link-shortener-and-analytics/view/clickmeter-account.php'][] = array('<div class="openInNewWindow">New Tracking Link</div>', $role,'http://mybeta.clickmeter.com/go?val='.$boGoVal.'&returnUrl=%2Flinks%2Fnew');
                $submenu['clickmeter-link-shortener-and-analytics/view/clickmeter-account.php'][] = array('<div class="openInNewWindow">404 Report</div>', $role, 'http://my.clickmeter.com/go?val=' . $boGoVal . '&returnUrl=%2FLinks%23campaignId%3D' . $group_id_404_reports . '%26rows%3D100%26last90');
                add_submenu_page('clickmeter-link-shortener-and-analytics/view/clickmeter-account.php', 'ClickMeter | Loader', '<div style="display:none" class="cm_hidden">Loading page</div>', $role, 'clickmeter-link-shortener-and-analytics/view/clickmeter-loading_tracking_pixels_ops.php');

                //Page for tests
                //add_submenu_page( 'clickmeter-link-shortener-and-analytics/view/clickmeter-account.php', 'ClickMeter | TEST2', 'Test page 2', $role, 'clickmeter-link-shortener-and-analytics/view/clickmeter-test.php');
            }
        }catch (Exception $e){
            $subject = 'wp error completeMenu';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }

    }

    function to_email($subject, $content){
        if (WPClickmeter::get_option('clickmeter_error_report_flag') == 1) {
            $clickmeter_debug_option = WPClickmeter::get_option('clickmeter_debug_init');
            $blogtime = current_time('mysql');
            $blog_title = get_bloginfo();
            $content = $content . "\n" . "account: " . $clickmeter_debug_option['email'] . "\nblog title: " . $blog_title .
                "\nblog time: " . $blogtime;
            $subject = '[WPError] ' . $subject;
            wp_mail(EMAIL_TO, $subject, $content);
        }
    }

    static function api_request($endpoint, $method, $body, $api_key, $associative=true){
        try {
            $args = array(
                'method' => $method,
                'timeout' => 120,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array('Content-Type' => 'application/json', 'X-Clickmeter-Authkey' => $api_key),
                'body' => $body
            );
            $response = wp_remote_post($endpoint, $args);
            if ($response['response']['code'] != 200) {
                $subject = 'wp error api_request';
                $content = WPClickmeter::trace_variable($endpoint, "endpoint");
                $content = $content . WPClickmeter::trace_variable($body, "body");
                $content = $content . WPClickmeter::trace_variable($response, "response");
                WPClickmeter::to_email($subject, $content);
                if ($response['response']['code'] == 401){
                    echo "<div id='clickmeter-warning' class='error fade'>
					<p style='color:black'><strong>ClickMeter: </strong>Warning! API KEY is not valid.
					</p>
				</div>";
                }
                else {
                    echo "<div id='clickmeter-warning' class='error fade'>
					<p style='color:black'><strong>ClickMeter: </strong>Warning! Something went wrong calling our servers. Please try later or contact our
					<a target='_blank' href='mailto:support@clickmeter.com?subject=Error message from WordPress plugin'>support</a>.
					</p>	
				</div>";
                }
                $response_body = $response['body'];
                WPClickmeter::store_option("clickmeter_last_apirequest_error", json_decode($response_body, $associative));
            } else {
                $response_body = $response['body'];
                return json_decode($response_body, $associative);
            }
        }catch (Exception $e){
            $subject = 'wp error api_request';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    /**
     * Adds a box to the main column on the Post and Page edit screens.
     */
    static function clickmeter_add_meta_box() {
        try {
            $screens = array('post', 'page');
            $wp_redirection_flag = WPClickmeter::get_option("clickmeter_wp_redirection_flag");
            foreach ($screens as $screen) {
                add_meta_box('clickmeter-newlink', __('ClickMeter Reports', 'clickmeter'), array(__CLASS__, 'clickmeter_meta_box_callback'), $screen, "side", "high");

                // if ($wp_redirection_flag==1){
                // 	add_meta_box('clickmeter-redirectionlink',__( 'ClickMeter Redirection', 'clickmeter' ), array(__CLASS__, 'clickmeter_meta_box_redirection_callback'),$screen, "normal", "high");
                // }
            }
        }catch (Exception $e){
            $subject = 'wp error clickmeter_add_meta_box';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    /**
     * Prints the box content.
     */
    static function clickmeter_meta_box_callback($post) {
        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'clickmeter_meta_box', 'clickmeter_meta_box_nonce' );
        ClickmeterViews::clickmeter_meta_box_callback($post);
    }

    /**
     * Prints redirection box content.
     */
    static function clickmeter_meta_box_redirection_callback($post) {
        // Add an nonce field so we can check for it later.
        wp_nonce_field( 'clickmeter_redirection_meta_box', 'clickmeter_redirection_meta_box_nonce' );
        ClickmeterViews::clickmeter_meta_box_redirection_callback($post);
    }

    /**
     * When the post is deleted.
     */
    static function clickmeter_delete_post( $post_id ) {

        try {

            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }
            // Check the user's permissions.
            if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id)) {
                    return;
                }
            } else {
                if (!current_user_can('edit_post', $post_id)) {
                    return;
                }
            }
            // Return if it's a post revision
            if (false !== wp_is_post_revision($post_id)) {
                return;
            }

            //WPClickmeter::store_option("clickmeter_debug", "test");
            WPClickmeter::delete_datapoints($post_id);
        } catch (Exception $e){
            $subject = 'wp error clickmeter_delete_meta_box_data';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    /**
     * When the post is edited, saves our custom data.
     */
    static function clickmeter_edit_meta_box_data( $post_id ) {

        try {

            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }
            // Check the user's permissions.
            if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id)) {
                    return;
                }
            } else {
                if (!current_user_can('edit_post', $post_id)) {
                    return;
                }
            }
            // Return if it's a post revision
            if (false !== wp_is_post_revision($post_id)) {
                return;
            }


            /* OK, its safe for us to save the data now. */
            // Make sure that it is set.
            if (!(isset($_POST['domainId']))) {
                return;
            }

            /* OK, its safe for us to save the data now. */
            // Make sure that it is set.
            if (!(isset($_POST['clickmeter_update_post']))) {
                return;
            }

            //WPClickmeter::store_option("clickmeter_debug_edit", $_POST['domain_list']);

            // On update, if a post doesn't have a tracking link, or the title is modified by the user, a new link is created
            $domainId = $_POST['domainId'];
            $tracking_link_id = $_POST['tracking_link_id'];
            $tracking_pixel_id = $_POST['tracking_pixel_id'];
            $name = $_POST['tracking_link_name'];
            $campaignId = $_POST['tracking_link_campaign'];

            //if post's tracking link already exist->update
            if ((isset($_POST['tracking_link_id']))) {
                WPClickmeter::update_tracking_link($post_id, $name, $domainId, $campaignId, $tracking_link_id);
            }

            if ((isset($_POST['tracking_pixel_id']))) {
                WPClickmeter::create_tracking_pixel($post_id, $tracking_pixel_id, true);
            }

            return;
        } catch (Exception $e){
            $subject = 'wp error clickmeter_edit_meta_box_data';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    /**
     * When the post is saved, saves our custom data.
     */
    static function clickmeter_save_meta_box_data( $post_id ) {

        try {

            /*
            * We need to verify this came from our screen and with proper authorization,
            * because the save_post action can be triggered at other times.
            */
            // Check if our nonce is set.
            if (!isset($_POST['clickmeter_meta_box_nonce'])) {
                return;
            }
            // Verify that the nonce is valid.
            if (!wp_verify_nonce($_POST['clickmeter_meta_box_nonce'], 'clickmeter_meta_box')) {
                return;
            }
            // If this is an autosave, our form has not been submitted, so we don't want to do anything.
            if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
                return;
            }
            // Check the user's permissions.
            if (isset($_POST['post_type']) && 'page' == $_POST['post_type']) {
                if (!current_user_can('edit_page', $post_id)) {
                    return;
                }
            } else {
                if (!current_user_can('edit_post', $post_id)) {
                    return;
                }
            }
            // Return if it's a post revision
            if (false !== wp_is_post_revision($post_id)) {
                return;
            }

            /* OK, its safe for us to save the data now. */
            // Make sure that it is set.
            if (!(isset($_POST['clickmeter_save_post']))) {
                return;
            }

            //WPClickmeter::store_option("clickmeter_debug_saveee", "testsave");

            self::create_tracking_pixel($post_id);
        } catch (Exception $e) {
            $subject = 'wp error clickmeter_save_meta_box_data';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function delete_datapoints($post_id){
        $api_key=WPClickmeter::get_option('clickmeter_api_key');
        $group_id_TL = WPClickmeter::get_option('clickmeter_TLcampaign_id');
        $group_id_TP = WPClickmeter::get_option('clickmeter_TPcampaign_id');
        $post = get_post($post_id);
        $post_title = $post->post_title;

        //get information about this post's tracking link if exist
        $link_data = WPClickmeter::get_link($post_id);
        if($link_data!=null) $tracking_link_id = $link_data['tracking_link_id'];

        //get information about this post's tracking pixel if exist
        $pixel_data = WPClickmeter::get_pixel($post_id);
        if($pixel_data!=null) $tracking_pixel_id = $pixel_data['pixel_id'];

        //WPClickmeter::store_option("clickmeter_debug_delete_tl", $tracking_link_id);
        //WPClickmeter::store_option("clickmeter_debug_delete_tp", $tracking_pixel_id);

        //DELETE ELEMENT FROM CLICKMETER
        // if($tracking_link_id!=null){
        // 	$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/'.$tracking_link_id, 'DELETE', NULL, $api_key);
        // }
        // if($tracking_pixel_id!=null){
        // 	$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/'.$tracking_pixel_id, 'DELETE', NULL, $api_key);
        // }

        WPClickmeter::delete_pixel($post_id);
        WPClickmeter::delete_link($post_id);

        //DELETE ELEMENT FROM PLUGIN'S LISTS
        $inclusion_list = WPClickmeter::get_option('clickmeter_inclusion_list');
        if (($key = array_search($post_id, $inclusion_list)) !== false) {
            unset($inclusion_list[$key]);
        }
        WPClickmeter::store_option( 'clickmeter_inclusion_list', $inclusion_list);

        $exclusion_list = WPClickmeter::get_option('clickmeter_exclusion_list');
        if (($key = array_search($post_id, $exclusion_list)) !== false) {
            unset($exclusion_list[$key]);
        }
        WPClickmeter::store_option( 'clickmeter_exclusion_list', $exclusion_list);
    }

    static function update_tracking_link($post_id, $name, $domainId, $campaignId, $tracking_link_id=""){
        $link_cloak_flag = WPClickmeter::get_option("link_cloak_flag");
        $api_key = WPClickmeter::get_option('clickmeter_api_key');
        $post = get_post($post_id);
        $post_title = $post->post_title;
        $post_title = WPClickmeter::getIdentifier($post_title, $post_id);
        $permalink = get_permalink($post_id);

        $conversion1_id = WPClickmeter::get_option('clickmeter_default_firstConv_links');
        $conversion2_id = WPClickmeter::get_option('clickmeter_default_secondConv_links');

        $tracking_link = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/'.$tracking_link_id,'GET', null, $api_key);

        $hide_url = $tracking_link[typeTL][hideUrl];
        $encodeUrl = $tracking_link[typeTL][encodeUrl];
        $redirection_type = $tracking_link[typeTL][redirectType];
        $notes = $tracking_link[notes];

        $tag_list = array();
        foreach ($tracking_link[tags] as $tag) {
            $tag_list[] = $tag[id];
        }

        $body=array('type'=> 0,
            'title'=> $post_title,
            'groupId'=> $campaignId,
            'name'=> $name,
            "tags"=>$tag_list,
            'typeTL'=>array('domainId'=> $domainId,'url'=> $permalink, 'redirectType'=> $redirection_type)
        );
        if($hide_url!=null) $body[typeTL][hideUrl] = $hide_url;
        if($encodeUrl!=null) $body[typeTL][encodeUrl] = $encodeUrl;
        if($notes!=null) $body[notes] = $notes;

        if($conversion1_id!=null) $body["firstConversionId"] = $conversion1_id;
        if($conversion2_id!=null) $body["secondConversionId"] = $conversion2_id;


        $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/'.$tracking_link_id,'POST', json_encode($body), $api_key);
    }

    /**
     * Log into system php error log, usefull for Ajax and stuff that FirePHP doesn't catch
     */
    static function my_log_file( $msg, $name = '' )
    {
        // Print the name of the calling function if $name is left empty
        $trace=debug_backtrace();
        $name = ( '' == $name ) ? $trace[1]['function'] : $name;

        $error_dir = '/Applications/MAMP/logs/php_error.log';
        $msg = print_r( $msg, true );
        $log = $name . "  |  " . $msg . "\n";
        error_log( $log, 3, $error_dir );
    }

    function trace_variable( $msg, $name = '' )
    {
        // Print the name of the calling function if $name is left empty
        $trace=debug_backtrace();
        $name = ( '' == $name ) ? $trace[1]['function'] : $name;

        $msg = print_r( $msg, true );
        $log = $name . ": " . $msg . "\n";
        return $log;
    }

    static function create_tracking_pixel($post_id, $tracking_pixel_id="", $update=false){
        $group_id_TP = WPClickmeter::get_option('clickmeter_TPcampaign_id');
        $api_key = WPClickmeter::get_option('clickmeter_api_key');
        $post = get_post($post_id);
        $post_title = $post->post_title;
        $post_title = WPClickmeter::getIdentifier($post_title, $post_id);
        $post_content = $post->post_content;
        $post_type = $post->post_type;
        $permalink = get_permalink($post_id);
        $pixel_value=WPClickmeter::get_option('clickmeter_pixel_flag');
        $pixel_default_value = WPClickmeter::get_option('clickmeter_pixel_new_articles');

        $body=array('type'=>1, 'title'=>$post_title, "groupId"=> $group_id_TP);
        //WPClickmeter::store_option("clickmeter_debug_createTP",$body );
        if($update==true && $tracking_pixel_id!="" && !empty($post_title)){
            //Add conversions to created TP if exist
            $conversion1_id = WPClickmeter::get_option('clickmeter_conversionId1');
            $conversion2_id = WPClickmeter::get_option('clickmeter_conversionId2');
            $conversion_target1 = WPClickmeter::get_option("clickmeter_conversionTarget1");
            $conversion_target2 = WPClickmeter::get_option("clickmeter_conversionTarget2");
            if($conversion1_id != null){
                if(in_array($post_type, $conversion_target1)) $body["firstConversionId"] = $conversion1_id;
            }
            if($conversion2_id != null){
                if(in_array($post_type, $conversion_target2)){
                    if($body["firstConversionId"] == null){
                        $body["firstConversionId"] = $conversion2_id;
                    }else{
                        $body["secondConversionId"] = $conversion2_id;
                    }
                }
            }

            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/'.$tracking_pixel_id.'','POST', json_encode($body), $api_key);
            $updated_pixel_id = $json_output[id];


            //ADD POST TO TAG LIST
            if($post_type == "page"){
                $tag_body=array('name'=>"page", 'datapoints'=>array($updated_pixel_id));
            }else{
                $tag_body=array('name'=>"post", 'datapoints'=>array($updated_pixel_id));
            }
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/tags','POST', json_encode($tag_body), $api_key);
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/'.$updated_pixel_id, 'GET', NULL, $api_key);
            $pixel_name = $json_output[name];
            $trackingCode = $json_output[trackingCode];

            return;
        }elseif($pixel_value==1 and $pixel_default_value==1 and !empty($post_title)){ 	//if Include pixel into new articles by default enabled and tracking pixel enabled
            $dbValue = WPClickmeter::get_pixel($post_id);
            if($dbValue!=null){
                return;
            }else{
                //Add conversions to created TP if exist
                $conversion1_id = WPClickmeter::get_option('clickmeter_conversionId1');
                $conversion2_id = WPClickmeter::get_option('clickmeter_conversionId2');
                $conversion_target1 = WPClickmeter::get_option("clickmeter_conversionTarget1");
                $conversion_target2 = WPClickmeter::get_option("clickmeter_conversionTarget2");
                if($conversion1_id != null){
                    if(in_array($post_type, $conversion_target1)) $body["firstConversionId"] = $conversion1_id;
                }
                if($conversion2_id != null){
                    if(in_array($post_type, $conversion_target2)){
                        if($body["firstConversionId"] == null){
                            $body["firstConversionId"] = $conversion2_id;
                        }else{
                            $body["secondConversionId"] = $conversion2_id;
                        }
                    }
                }
                $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints','POST', json_encode($body), $api_key);
                $created_pixel_id = $json_output[id];
                //ADD POST TO TAG LIST
                if($post_type == "page"){
                    $tag_body=array('name'=>"page", 'datapoints'=>array($created_pixel_id));
                }else{
                    $tag_body=array('name'=>"post", 'datapoints'=>array($created_pixel_id));
                }
                $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/tags','POST', json_encode($tag_body), $api_key);
                //GET TRACKING CODE AND ADD TO POST
                $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/'.$created_pixel_id, 'GET', NULL, $api_key);
                $trackingCode = $json_output[trackingCode];
                $pixel_name = $json_output[name];
                $timestamp = $json_output[creationDate];

                $inclusion_list = WPClickmeter::get_option('clickmeter_inclusion_list');
                if(!in_array($post_id, $inclusion_list)) {
                    $inclusion_list[] = $post_id;
                }
                WPClickmeter::store_option( 'clickmeter_inclusion_list', $inclusion_list);

                if($created_pixel_id!=null){
                    WPClickmeter::store_pixel($post_id, $created_pixel_id, $pixel_name, $trackingCode, $group_id_TP, $post_type, $timestamp);
                }
            }
            return;
        }
    }

    static function store_option($key, $value, $is_array=0){
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_options';
            $timezone = WPClickmeter::get_option("clickmeter_user_timezone");

            if (is_array($value)) {
                $is_array = 1;
                $value = serialize($value);
            }

            $result = WPClickmeter::get_option($key);
            if (gettype($result) != "NULL") {
                $wpdb->update(
                    $table_name,
                    array(
                        'value' => $value,
                        'time' => current_time('mysql', $timezone),
                        'is_array' => $is_array
                    ),
                    array('key_name' => $key)
                );
            } else {
                $wpdb->insert(
                    $table_name,
                    array(
                        'time' => current_time('mysql', $timezone),
                        'key_name' => $key,
                        'value' => $value,
                        'is_array' => $is_array
                    )
                );
            }
        }catch (Exception $e){
            $subject = 'wp error store_option';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function get_keys(){
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_options';
            $result = $wpdb->get_results("SELECT key_name FROM $table_name", OBJECT);

            return $result;
        }catch (Exception $e){
            $subject = 'wp error get_keys';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function get_option($key){
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_options';
            $result = $wpdb->get_row("SELECT * FROM $table_name WHERE key_name = '$key'", ARRAY_A);
            if ($result["is_array"] == 1) {
                return unserialize($result["value"]);
            } else {
                return $result["value"];
            }
        }catch (Exception $e){
            $subject = 'wp error get_option';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }

    }

    static function delete_option($key){
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_options';
            $wpdb->delete($table_name, array('key_name' => $key));
        }catch (Exception $e){
            $subject = 'wp error delete_option';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }


    static function store_pixel($post_id, $pixel_id, $pixel_name, $tracking_code, $campaign_id, $tag, $timestamp, $status = 'active'){
        global $wpdb;
        $table_name = $wpdb->prefix . 'clickmeter_tracking_pixels';

        $tracking_code = preg_replace('/^http:/', '', $tracking_code);

        $wpdb->insert(
            $table_name,
            array(
                'post_id' => $post_id,
                'time' => $timestamp,
                'pixel_id' => $pixel_id,
                'pixel_name' => $pixel_name,
                'tracking_code' => $tracking_code,
                'campaign_id' =>$campaign_id,
                'tag' => $tag,
                'status' => $status
            )
        );
    }

    static function update_status_pixel($post_id, $status){
        global $wpdb;
        $table_name = $wpdb->prefix . 'clickmeter_tracking_pixels';

        $wpdb->update(
            $table_name,
            array(
                'status' => $status
            ),
            array('post_id' => $post_id)
        );
    }

    static function update_tracking_code_pixel($post_id, $tracking_code){
        global $wpdb;
        $table_name = $wpdb->prefix . 'clickmeter_tracking_pixels';

        $wpdb->update(
            $table_name,
            array(
                'tracking_code' => $tracking_code
            ),
            array('post_id' => $post_id)
        );
    }

    static function get_pixel($post_id){
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_tracking_pixels';
            $result = $wpdb->get_row("SELECT * FROM $table_name WHERE post_id = '$post_id'", ARRAY_A);
            return $result;
        }catch (Exception $e){
            $subject = 'wp error get_pixel';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function get_pixel_count(){
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_tracking_pixels';
            $result = $wpdb->get_var("SELECT COUNT(*) FROM $table_name");
            return $result;
        }catch (Exception $e){
            $subject = 'wp error get_pixel_count';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }


    static function delete_pixel($post_id){
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_tracking_pixels';
            $wpdb->delete($table_name, array('post_id' => $post_id));
        }catch (Exception $e){
            $subject = 'wp error delete_pixel';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }


    static function store_link($post_id, $link_id, $link_name, $link_rid, $campaign_id, $campaign_name, $trackingCode, $url, $domain_id, $is_post, $is_redirection_link, $timestamp){
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_tracking_links';
            $timezone = WPClickmeter::get_option("clickmeter_user_timezone");

            $wpdb->insert(
                $table_name,
                array(
                    'post_id' => $post_id,
                    'time' => $timestamp,
                    'tracking_link_id' => $link_id,
                    'link_name' => $link_name,
                    'link_rid' => $link_rid,
                    'campaign_id' => $campaign_id,
                    'campaign_name' => $campaign_name,
                    'tracking_code' => $trackingCode,
                    'destination_url' => $url,
                    'domain_id' => $domain_id,
                    'is_post' => $is_post,
                    'is_redirection_link' => $is_redirection_link
                )
            );
        }catch (Exception $e){
            $subject = 'wp error store_link';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function update_links_postflag(){ //bugfix for version 1.2.4.1
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_tracking_links';
            $wpdb->update(
                $table_name,
                array(
                    'is_post' => 0
                ),
                array('is_redirection_link' => 1)
            );
        }catch (Exception $e){
            $subject = 'wp error update_links_postflag';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function update_link($key, $value_name, $value){
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_tracking_links';
            $timezone = WPClickmeter::get_option("clickmeter_user_timezone");

            $wpdb->update(
                $table_name,
                array(
                    $value_name => $value
                ),
                array('tracking_link_id' => $key)
            );
        }catch (Exception $e){
            $subject = 'wp error update_link';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function get_link($post_id, $is_redirection_link=null){
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_tracking_links';
            if ($is_redirection_link != null) {
                $result = $wpdb->get_row("SELECT * FROM $table_name WHERE post_id = '$post_id' AND is_post = 1 AND is_redirection_link = 1", ARRAY_A);
            } else {
                $result = $wpdb->get_row("SELECT * FROM $table_name WHERE post_id = '$post_id' AND is_post = 1 AND is_redirection_link = 0 ", ARRAY_A);
            }

            return $result;
        }catch (Exception $e){
            $subject = 'wp error get_link';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function get_all_links(){
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_tracking_links';
            $result = $wpdb->get_results("SELECT * FROM $table_name ORDER BY time DESC", ARRAY_A);

            return $result;
        }catch (Exception $e){
            $subject = 'wp error get_all_links';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function get_redirect_link($link_name){
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_tracking_links';
            $result = $wpdb->get_row("SELECT * FROM $table_name WHERE link_name = '$link_name'", ARRAY_A);

            return $result;
        }catch (Exception $e){
            $subject = 'wp error get_redirect_link';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function delete_link($post_id, $tracking_link_id=null){
        try {
            global $wpdb;
            $table_name = $wpdb->prefix . 'clickmeter_tracking_links';
            if ($tracking_link_id != null) {
                $wpdb->delete($table_name, array('tracking_link_id' => $tracking_link_id));
            } else {
                $wpdb->delete($table_name, array('post_id' => $post_id));
            }
        }catch (Exception $e){
            $subject = 'wp error delete_link';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }

    }

    static function generateRandomString($length = 4) {
        try {
            $characters = '0123456789abcdefghijklmnopqrstuvwxyz';
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, strlen($characters) - 1)];
            }
            return $randomString;
        }catch (Exception $e){
            $subject = 'wp error generateRandomString';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

    static function encode($value) {
        $ascii = iconv(mb_detect_encoding($value, mb_detect_order(), true), "US-ASCII//TRANSLIT//IGNORE", $value);
        return $ascii;
    }

    static function getIdentifier($value, $id){
        $identifier = null;
        if (!empty($value)) {
            $value_encoded = self::encode($value);
            if ($value_encoded) {
                $identifier = $value_encoded;
            } else {
                $identifier = $value;
            }
            if (strlen($identifier) > 100){
                $identifier = substr($identifier, 0, 99);
            }
            $regex = '/[^0-9A-Za-z\-\ \.\_\[\]\:\/\£\$\€\%\&\(\)\'\!\*\@\ò\à\ù\è\é\ì\,\+\>]+/';
            $identifier = preg_replace($regex, '', $identifier);
            $identifier = preg_replace('/^ */', '', $identifier);
            $identifier = preg_replace('/ *$/', '', $identifier);
            if (empty($identifier)){
                $identifier = strval($id);
            }
        }
        else {
            $identifier = strval($id);
        }
        return $identifier;
    }

    static function crc16($buffer){
        try {
            $CRC16_Table = array(0x0000, 0x2110, 0x4220, 0x6330, 0x8440, 0xa550, 0xc660, 0xe770,
                0x0881, 0x2991, 0x4aa1, 0x6bb1, 0x8cc1, 0xadd1, 0xcee1, 0xeff1,
                0x3112, 0x1002, 0x7332, 0x5222, 0xb552, 0x9442, 0xf772, 0xd662,
                0x3993, 0x1883, 0x7bb3, 0x5aa3, 0xbdd3, 0x9cc3, 0xfff3, 0xdee3,
                0x6224, 0x4334, 0x2004, 0x0114, 0xe664, 0xc774, 0xa444, 0x8554,
                0x6aa5, 0x4bb5, 0x2885, 0x0995, 0xeee5, 0xcff5, 0xacc5, 0x8dd5,
                0x5336, 0x7226, 0x1116, 0x3006, 0xd776, 0xf666, 0x9556, 0xb446,
                0x5bb7, 0x7aa7, 0x1997, 0x3887, 0xdff7, 0xfee7, 0x9dd7, 0xbcc7,
                0xc448, 0xe558, 0x8668, 0xa778, 0x4008, 0x6118, 0x0228, 0x2338,
                0xccc9, 0xedd9, 0x8ee9, 0xaff9, 0x4889, 0x6999, 0x0aa9, 0x2bb9,
                0xf55a, 0xd44a, 0xb77a, 0x966a, 0x711a, 0x500a, 0x333a, 0x122a,
                0xfddb, 0xdccb, 0xbffb, 0x9eeb, 0x799b, 0x588b, 0x3bbb, 0x1aab,
                0xa66c, 0x877c, 0xe44c, 0xc55c, 0x222c, 0x033c, 0x600c, 0x411c,
                0xaeed, 0x8ffd, 0xeccd, 0xcddd, 0x2aad, 0x0bbd, 0x688d, 0x499d,
                0x977e, 0xb66e, 0xd55e, 0xf44e, 0x133e, 0x322e, 0x511e, 0x700e,
                0x9fff, 0xbeef, 0xdddf, 0xfccf, 0x1bbf, 0x3aaf, 0x599f, 0x788f,
                0x8891, 0xa981, 0xcab1, 0xeba1, 0x0cd1, 0x2dc1, 0x4ef1, 0x6fe1,
                0x8010, 0xa100, 0xc230, 0xe320, 0x0450, 0x2540, 0x4670, 0x6760,
                0xb983, 0x9893, 0xfba3, 0xdab3, 0x3dc3, 0x1cd3, 0x7fe3, 0x5ef3,
                0xb102, 0x9012, 0xf322, 0xd232, 0x3542, 0x1452, 0x7762, 0x5672,
                0xeab5, 0xcba5, 0xa895, 0x8985, 0x6ef5, 0x4fe5, 0x2cd5, 0x0dc5,
                0xe234, 0xc324, 0xa014, 0x8104, 0x6674, 0x4764, 0x2454, 0x0544,
                0xdba7, 0xfab7, 0x9987, 0xb897, 0x5fe7, 0x7ef7, 0x1dc7, 0x3cd7,
                0xd326, 0xf236, 0x9106, 0xb016, 0x5766, 0x7676, 0x1546, 0x3456,
                0x4cd9, 0x6dc9, 0x0ef9, 0x2fe9, 0xc899, 0xe989, 0x8ab9, 0xaba9,
                0x4458, 0x6548, 0x0678, 0x2768, 0xc018, 0xe108, 0x8238, 0xa328,
                0x7dcb, 0x5cdb, 0x3feb, 0x1efb, 0xf98b, 0xd89b, 0xbbab, 0x9abb,
                0x754a, 0x545a, 0x376a, 0x167a, 0xf10a, 0xd01a, 0xb32a, 0x923a,
                0x2efd, 0x0fed, 0x6cdd, 0x4dcd, 0xaabd, 0x8bad, 0xe89d, 0xc98d,
                0x267c, 0x076c, 0x645c, 0x454c, 0xa23c, 0x832c, 0xe01c, 0xc10c,
                0x1fef, 0x3eff, 0x5dcf, 0x7cdf, 0x9baf, 0xbabf, 0xd98f, 0xf89f,
                0x176e, 0x367e, 0x554e, 0x745e, 0x932e, 0xb23e, 0xd10e, 0xf01e
            );

            $length = strlen($buffer);
            $crc = 0;
            $i = 0;
            while ($length--) {
                $crc = (($crc >> 8) & 0xff) ^ ($CRC16_Table[(ord($buffer[$i++]) ^ $crc) & 0xFF]);
            }
            return ((($crc & 0xFFFF) ^ 0x8000) - 0x8000) + 32769;
        }catch (Exception $e){
            $subject = 'wp error crc16';
            $content = $e->getMessage() . "\n";
            $content = $content . $e->getTraceAsString() . "\n";
            WPClickmeter::to_email($subject, $content);
        }
    }

}

function is_redirect_link($request_uri){
    $link_data = WPClickmeter::get_redirect_link($request_uri);
    if ($link_data != null && $link_data[is_post] != 1) {
        return true;
    } else {
        return false;
    }
}

function templateRedirect() {
    try {
        global $wp;
        global $wp_query;
        $track_wplinks_flag = WPClickmeter::get_option("clickmeter_track_404_flag");
        $wp_redirection_flag = WPClickmeter::get_option("clickmeter_wp_redirection_flag");

        $request_uri = $wp->request;
        preg_match("#[\/](.*)[\/]?$#", $request_uri, $match);

        if (!empty($match)) {
            $request_uri = $match[1];
        }

        $trackingCode = "";
        if (is_redirect_link($request_uri)) {
            $link_data = WPClickmeter::get_redirect_link($request_uri);
            $trackingCode = $link_data[tracking_code];
            wp_redirect(add_query_arg($_GET, $trackingCode), 301);
            exit;
        }

        if ($track_wplinks_flag == 1 && $wp_query->is_404) {
            $clickmeter_400_tl = WPClickmeter::get_option("clickmeter_404_tl");
            wp_redirect($clickmeter_400_tl);
            // set status of 404 to false
            //$wp_query->is_404 = false;
            // change the header to 200 OK
            //header("HTTP/1.1 200 OK");
            //update_option("clickmeter_1", $wp->request);
        }
    }catch (Exception $e){
        $subject = 'wp error templateRedirect';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}
add_action('template_redirect', 'templateRedirect');

add_action('init', 'wpclickmeter_init');

//The function that handles the AJAX request
function test_action_callback() {
    try {
        global $wpdb; // this is how you get access to the database

        //check_ajax_referer( 'my-special-string', 'security' );
        $whatever = intval($_POST['whatever']);
        $whatever += 10;

        echo $whatever;

        die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error test_action_callback';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}
add_action( 'wp_ajax_test_action', 'test_action_callback' );

function ajax_create_tl() {
    try {
        $api_key = WPClickmeter::get_option('clickmeter_api_key');
        $domainId = WPClickmeter::get_option("clickmeter_default_domainId");
        $default_campaignId = WPClickmeter::get_option("clickmeter_default_campaignId_links");
        $link_cloak_flag = WPClickmeter::get_option("link_cloak_flag");
        $redirection_type = WPClickmeter::get_option("clickmeter_default_redirection_type");

        $conversion1_id = WPClickmeter::get_option('clickmeter_default_firstConv_links');
        $conversion2_id = WPClickmeter::get_option('clickmeter_default_secondConv_links');

        $url = $_POST['url'];
        $post_id = $_POST['post_id'];
        $friendly_name = $_POST['friendly_name'];
        $friendly_name = WPClickmeter::getIdentifier($friendly_name, $post_id);
        if (isset($_POST['cloak_link'])) $link_cloak_flag = $_POST['cloak_link'];
        if (isset($_POST['domain_id'])) $domainId = $_POST['domain_id'];
        if (isset($_POST['camapaign_id'])) $default_campaignId = $_POST['camapaign_id'];
        if (isset($_POST['first_conversion_id'])) $conversion1_id = $_POST['first_conversion_id'];
        if (isset($_POST['second_conversion_list'])) $conversion2_id = $_POST['second_conversion_list'];
        if (isset($_POST['redirection_type'])) $redirection_type = $_POST['redirection_type'];

        $pattern = "/[^A-Za-z0-9_]/";
        $tmp_name = preg_replace($pattern, "-", $friendly_name);
        $tmp_name = strtolower($tmp_name);
        $link_rid = WPClickmeter::generateRandomString();
        $url_name = $link_rid . "-" . $tmp_name;
        if (strlen($url_name) > 38) {
            $url_name = substr($url_name, 0, 38);
        }
        if (strlen($tmp_name) > 33) {
            $tmp_name = substr($tmp_name, 0, 33);
        }

        $redirection_flag = 0;
        $notes = null;
        if (isset($_POST['is_redirection_link'])) {
            $blog_name = get_site_url();
            $blog_name = substr($blog_name, 7);
            $redirection_flag = intval($_POST['is_redirection_link']);
            if ($redirection_flag == 1) $notes = "Created with WordPress plugin. Main URL: " . $blog_name . "/" . $tmp_name . "";
        }

        if (($link_cloak_flag == "true") || $link_cloak_flag == 1) {
            $body = array('type' => 0,
                'title' => $friendly_name,
                'groupId' => $default_campaignId,
                'name' => $url_name,
                'typeTL' => array('domainId' => $domainId, 'url' => $url, "hideUrl" => "true", "encodeUrl" => "true", 'redirectType' => $redirection_type)
            );
        } else {
            $body = array('type' => 0,
                'title' => $friendly_name,
                'groupId' => $default_campaignId,
                'name' => $url_name,
                'typeTL' => array('domainId' => $domainId, 'url' => $url, 'redirectType' => $redirection_type)
            );
        }

        if ($conversion1_id != null && $$conversion1_id != "none") $body["firstConversionId"] = $conversion1_id;
        if ($conversion2_id != null && $$conversion2_id != "none") $body["secondConversionId"] = $conversion2_id;
        if ($notes != null) $body["notes"] = $notes;

        // WPClickmeter::store_option("clickmeter_debug_editbox", $body);
        $created_link = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints', 'POST', json_encode($body), $api_key);
        $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/' . $created_link[id], 'GET', NULL, $api_key);
        $trackingCode = $json_output[trackingCode];
        $campaign_name = $json_output[groupName];
        $created_link_id = $json_output[id];
        $timestamp = $json_output[creationDate];

        $is_post = 0;
        if ($post_id != "0000") {
            $is_post = 1;
            $this_post = get_post($post_id);
            $post_type = $this_post->post_type;
            $tag_body = array('name' => $post_type, 'datapoints' => array($created_link_id));
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/tags', 'POST', json_encode($tag_body), $api_key);
        }

        if (isset($_POST['tag_name']) && !empty($_POST['tag_name'])) {
            $tag_name = $_POST['tag_name'];
            $tag_name = WPClickmeter::getIdentifier($tag_name, $post_id);
            $tag_body = array('name' => $tag_name, 'datapoints' => array($created_link_id));
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/tags', 'POST', json_encode($tag_body), $api_key);
        }

        $result = array();
        $result['trackingCode'] = $trackingCode;
        $result['alternative_url'] = $trackingCode;
        if (intval($domainId) == 1597) {
            $blog_name = get_site_url();
            $blog_name = substr($blog_name, 7);
            $result['alternative_url'] = "http://" . $blog_name . "/" . $tmp_name;
        }
        $result['created_link_id'] = $created_link_id;
        $boGoVal = WPClickmeter::get_option('clickmeter_backOffice_key');
        $result['bogoval'] = $boGoVal;

        if ($created_link_id != null) {
            WPClickmeter::store_link($post_id, $created_link_id, $tmp_name, $link_rid, $default_campaignId, $campaign_name, $trackingCode, $url, $domainId, $is_post, $redirection_flag, $timestamp);
        }

        echo json_encode($result);

        wp_die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error ajax_create_tl';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
        status_header(500);
        wp_die();
    }
}
add_action( 'wp_ajax_create_tl', 'ajax_create_tl' );

function ajax_check_redirect_link(){
    try {

        $posts_array = WPClickmeter::retrieve_id_title_posts();

        $link_name = $_POST['link_name'];

        $pattern = "/[^A-Za-z0-9_]/";
        $tmp_name = preg_replace($pattern, "-", $link_name);
        $tmp_name = strtolower($tmp_name);

        $blog_name = get_site_url();
        $blog_name = substr($blog_name, 7);

        $exist_post_flag = false;
        foreach ($posts_array as $post) {
            $post_name = $post->post_name;
            if (strcmp($post_name, $tmp_name) == 0) $exist_post_flag = true;
        }

        $link_data = WPClickmeter::get_redirect_link($tmp_name);

        if ($link_data != null) {
            echo "is_tracking_link";
        } elseif ($exist_post_flag) {
            echo "is_post";
        } else {
            echo "false";
        }

        die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error ajax_check_redirect_link';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}
add_action( 'wp_ajax_check_redirect_link', 'ajax_check_redirect_link' );

function ajax_bulk_tl_delete(){
    try {
        $api_key = WPClickmeter::get_option('clickmeter_api_key');
        $tlid_list = $_POST['ids'];

        foreach ($tlid_list as $tracking_link_id_delete) {
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/' . $tracking_link_id_delete, 'DELETE', NULL, $api_key);
            WPClickmeter::delete_link(null, $tracking_link_id_delete);
        }
        die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error ajax_bulk_tl_delete';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}
add_action( 'wp_ajax_bulk_tl_delete', 'ajax_bulk_tl_delete' );

function ajax_bulk_tl_change_dest_url(){
    try {
        $api_key = WPClickmeter::get_option('clickmeter_api_key');
        $tlid_list = $_POST['ids'];
        $new_url = $_POST['new_url'];

        foreach ($tlid_list as $tracking_link_id) {
            $tracking_link = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/' . $tracking_link_id, 'GET', null, $api_key);
            $hide_url = $tracking_link[typeTL][hideUrl];
            $encodeUrl = $tracking_link[typeTL][encodeUrl];
            $notes = $tracking_link[notes];
            $title = $tracking_link[title];
            $campaignId = $tracking_link[groupId];
            $domainId = $tracking_link[typeTL][domainId];
            $name = $tracking_link[name];
            $conversion1_id = $tracking_link[firstConversionId];
            $conversion2_id = $tracking_link[secondConversionId];
            $redirection_type = $tracking_link[typeTL][redirectType];

            $tag_list = array();
            foreach ($tracking_link[tags] as $tag) {
                $tag_list[] = $tag[id];
            }

            $body = array('type' => 0,
                'title' => $title,
                'groupId' => $campaignId,
                'name' => $name,
                "tags" => $tag_list,
                'typeTL' => array('domainId' => $domainId, 'url' => $new_url, 'redirectType' => $redirection_type)
            );
            if ($hide_url != null) $body[typeTL][hideUrl] = $hide_url;
            if ($encodeUrl != null) $body[typeTL][encodeUrl] = $encodeUrl;
            if ($notes != null) $body[notes] = $notes;

            if ($conversion1_id != null) $body["firstConversionId"] = $conversion1_id;
            if ($conversion2_id != null) $body["secondConversionId"] = $conversion2_id;

            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/' . $tracking_link_id, 'POST', json_encode($body), $api_key);
            WPClickmeter::update_link($tracking_link_id, 'destination_url', $new_url);
        }

        die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error ajax_bulk_tl_change_dest_url';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}
add_action( 'wp_ajax_bulk_tl_change_dest_url', 'ajax_bulk_tl_change_dest_url' );

function isLinkAlreadyPresent($links, $link){
    foreach ($links as $check_link) {
        if ($check_link[1] == $link[1]){
            return true;
        }
    }
    return false;
}

function ajax_create_batch_tl() {
    try {
        $api_key = WPClickmeter::get_option('clickmeter_api_key');
        $domainId = WPClickmeter::get_option("clickmeter_default_domainId");
        $default_campaignId = WPClickmeter::get_option("clickmeter_default_campaignId_links");
        $link_cloak_flag = WPClickmeter::get_option("link_cloak_flag");

        $conversion1_id = WPClickmeter::get_option('clickmeter_default_firstConv_links');
        $conversion2_id = WPClickmeter::get_option('clickmeter_default_secondConv_links');
        $redirection_type = WPClickmeter::get_option("clickmeter_default_redirection_type");
        $redirection_flag = 0;

        $links_list = array();

        $new_links_list = $_POST['new_links_list'];
        $post_id = $_POST['post_id'];

        $this_post = get_post($post_id);
        $post_type = $this_post->post_type;

        $batch_body = array();

        $unique_links = array();
        foreach ($new_links_list as $new_link) {
            if (!isLinkAlreadyPresent($unique_links, $new_link)){
                $unique_links[] = $new_link;
            }
        }

        $chunk = -1;
        $i = 0;
        foreach ($unique_links as $new_link) {
            $friendly_name = $new_link[0];
            $friendly_name = WPClickmeter::getIdentifier($friendly_name, $post_id);
            $url = $new_link[1];

            $pattern = "/[^A-Za-z0-9_]/";
            $tmp_name = preg_replace($pattern, "-", $friendly_name);
            $tmp_name = strtolower($tmp_name);
            $t = time();
            $link_rid = WPClickmeter::generateRandomString();
            $url_name = $link_rid . "-" . $tmp_name;
            if (strlen($url_name) > 38) {
                $url_name = substr($url_name, 0, 38);
            }

            if (($link_cloak_flag == "true") || $link_cloak_flag == 1) {
                $body = array('type' => 0,
                    'title' => $friendly_name,
                    'groupId' => $default_campaignId,
                    'name' => $url_name,
                    'typeTL' => array('domainId' => $domainId, 'url' => $url, "hideUrl" => "true", "encodeUrl" => "true", 'redirectType' => $redirection_type)
                );
            } else {
                $body = array('type' => 0,
                    'title' => $friendly_name,
                    'groupId' => $default_campaignId,
                    'name' => $url_name,
                    'typeTL' => array('domainId' => $domainId, 'url' => $url, 'redirectType' => $redirection_type)
                );
            }

            if ($conversion1_id != null && $conversion1_id != "none") $body["firstConversionId"] = $conversion1_id;
            if ($conversion2_id != null && $conversion2_id != "none") $body["secondConversionId"] = $conversion2_id;

            if ($i % 25 == 0){
                $chunk++;
            }
            $batch_body[$chunk]["list"][] = $body;
            $i++;
        }

        $created_links = Array();
        $chunk = 0;
        while ($chunk < count($batch_body)) {
            $body = $batch_body[$chunk];
            $api_request = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/batch', 'PUT', json_encode($body), $api_key);
            $partial_tls = $api_request[results];
            $created_links = array_merge($created_links, $partial_tls);
            $chunk++;
        }

        foreach ($created_links as $result) {
            if ($result[entityData][id] != null) {
                $created_link_id = $result[entityData][id];
                $trackingCode = $result[entityData][trackingCode];
                $timestamp = $result[entityData][creationDate];
                $campaign_name = $result[entityData][groupName];

                $links_list[] =  $created_link_id;

                $is_post = 1;

                WPClickmeter::store_link($post_id, $created_link_id, $tmp_name, $link_rid, $default_campaignId, $campaign_name, $trackingCode, $url, $domainId, $is_post, $redirection_flag, $timestamp);
            }
        }

        $tag_body = array('name' => $post_type, 'datapoints' => $links_list);
        WPClickmeter::api_request('http://apiv2.clickmeter.com/tags', 'POST', json_encode($tag_body), $api_key);

        if (isset($_POST['tag_name']) && !empty($_POST['tag_name'])) {
            $tag_name = $_POST['tag_name'];
            $tag_name = WPClickmeter::getIdentifier($tag_name, $post_id);
            $tag_body = array('name' => $tag_name, 'datapoints' => $links_list);
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/tags', 'POST', json_encode($tag_body), $api_key);
        }

        echo json_encode($created_links);

        wp_die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error ajax_create_batch_tl';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
        status_header(500);
        wp_die();
    }
}
add_action( 'wp_ajax_create_batch_tl', 'ajax_create_batch_tl' );

function TP_savechanges() {
    try {
        global $wpdb; // this is how you get access to the database

        $api_key = WPClickmeter::get_option('clickmeter_api_key');
        $group_id_TP = WPClickmeter::get_option('clickmeter_TPcampaign_id');

        //WPClickmeter::store_option("clickmeter_debug_pixellist", $pixels_list);

        // $exclusion_list = WPClickmeter::get_option("clickmeter_exclusion_list");
        // $inclusion_list = WPClickmeter::get_option("clickmeter_inclusion_list");
        $exclusion_list = $_POST["exclusion_list"];
        //WPClickmeter::store_option("debug_exclusion", $exclusion_list);
        $inclusion_list = $_POST["inclusion_list"];
        //WPClickmeter::store_option("debug_inclusion", $inclusion_list);
        $pixels_flag = WPClickmeter::get_option("clickmeter_pixel_flag");

        if ($pixels_flag == 1) {
            //update flag active pixels into database
            //echo "dentro pixel_flag true";
            //WPClickmeter::store_option( 'clickmeter_pixel_flag', 1 );

            $toCreateList = array();
            //for each post into inclusion list
            if (!empty($inclusion_list)) {
                foreach ($inclusion_list as $post_id) {
                    //echo "trovata pagina nella inclusion list ".$post_id." <br>";
                    $pixel_data = WPClickmeter::get_pixel($post_id);
                    if ($pixel_data != null) $tracking_pixel_id = $pixel_data[pixel_id];
                    if ($tracking_pixel_id != null) {
                        $this_post_pixel = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/' . $tracking_pixel_id, 'GET', null, $api_key);
                        if ($this_post_pixel[errors] != null || $this_post_pixel[status] == 3) {
                            WPClickmeter::delete_pixel($post_id);
                            $pixel_data = null;
                        }
                    }
                    //Look for already existent pixels in clickmeter for actual campaign with the same title of the post
                    if ($pixel_data != null) {
                        WPClickmeter::update_status_pixel($post_id, 'active');
                    }
                    else {
                        $toCreateList[] = $post_id;
                    }
                }
            }

            //Create new tracking links in batch mode
            if (!empty($toCreateList)) {
                $i = 1;
                $body = array();
                $posts_map = array();
                $posts_list = array();
                $pages_list = array();
                $conversion1_id = WPClickmeter::get_option('clickmeter_conversionId1');
                $conversion2_id = WPClickmeter::get_option('clickmeter_conversionId2');

                foreach ($toCreateList as $post_id) {
                    $dbValue = WPClickmeter::get_pixel($post_id);
                    if ($dbValue != null) {
                        continue;
                    } else {
                        $post = get_post($post_id);

                        $post_title = WPClickmeter::getIdentifier($post->post_title, $post_id);
                        $post_type = $post->post_type;
                        $track_pix = array('type' => 1, 'title' => $post_title, "groupId" => $group_id_TP);
                        //ADD CONVERSION IF EXISTS
                        $conversion_target1 = WPClickmeter::get_option("clickmeter_conversionTarget1");
                        $conversion_target2 = WPClickmeter::get_option("clickmeter_conversionTarget2");
                        if ($conversion1_id != null) {
                            if (in_array($post_type, $conversion_target1)) $track_pix["firstConversionId"] = $conversion1_id;
                        }
                        if ($conversion2_id != null) {
                            if (in_array($post_type, $conversion_target2)) {
                                if ($track_pix["firstConversionId"] == null) {
                                    $track_pix["firstConversionId"] = $conversion2_id;
                                } else {
                                    $track_pix["secondConversionId"] = $conversion2_id;
                                }
                            }
                        }
                        $body["list"][] = $track_pix;
                        $posts_map[$post_title] = $post_id;

                        //WPClickmeter::store_option("click_debug",$body);
                        if ($i == sizeof($toCreateList) && !empty($body)) {
                            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/batch', 'PUT', json_encode($body), $api_key);
                            //WPClickmeter::store_option("click_debug1",$json_output[results]);
                            foreach ($json_output[results] as $result) {
                                if ($result[entityData][id] != null) {
                                    $pid = $posts_map[$result[entityData][title]];
                                    $created_pixel_id = $result[entityData][id];
                                    $created_pixel_name = $result[entityData][name];
                                    $trackingCode = $result[entityData][trackingCode];
                                    $timestamp = $result[entityData][creationDate];
                                    $postToChange = get_post($pid);
                                    $post_type = $postToChange->post_type;
                                    if ($post_type == "page") {
                                        $pages_list[] = $created_pixel_id;
                                    } else {
                                        $posts_list[] = $created_pixel_id;
                                    }

                                    WPClickmeter::store_pixel($pid, $created_pixel_id, $created_pixel_name, $trackingCode, $group_id_TP, $post_type, $timestamp);
                                }
                            }
                            $i++;
                        }

                        if ($i % 25 == 0) {
                            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/batch', 'PUT', json_encode($body), $api_key);
                            foreach ($json_output[results] as $result) {
                                if ($result[entityData][id] != null) {
                                    $pid = $posts_map[$result[entityData][title]];
                                    $created_pixel_id = $result[entityData][id];
                                    $created_pixel_name = $result[entityData][name];
                                    $trackingCode = $result[entityData][trackingCode];
                                    $timestamp = $result[entityData][creationDate];
                                    $postToChange = get_post($pid);
                                    $post_type = $postToChange->post_type;
                                    if ($post_type == "page") {
                                        $pages_list[] = $created_pixel_id;
                                    } else {
                                        $posts_list[] = $created_pixel_id;
                                    }

                                    WPClickmeter::store_pixel($pid, $created_pixel_id, $created_pixel_name, $trackingCode, $group_id_TP, $post_type, $timestamp);
                                }

                            }
                            $body = array();
                        }
                        $i++;
                    }
                }

                //ADD POST TO TAG LIST
                $tag_body = array('name' => "page", 'datapoints' => $pages_list);
                WPClickmeter::api_request('http://apiv2.clickmeter.com/tags', 'POST', json_encode($tag_body), $api_key);
                $tag_body = array('name' => "post", 'datapoints' => $posts_list);
                WPClickmeter::api_request('http://apiv2.clickmeter.com/tags', 'POST', json_encode($tag_body), $api_key);

            }

            //for each post into exclusion list
            if (!empty($exclusion_list)) {
                foreach ($exclusion_list as $post_id) {
                    $pixel_data = WPClickmeter::get_pixel($post_id);
                    //Look for already existent pixels in clickmeter for actual campaign with the same title of the post
                    if ($pixel_data != null) {
                        WPClickmeter::update_status_pixel($post_id, 'paused');
                        //check if exists on ClickMeter
                        $tracking_pixel_id = $pixel_data[pixel_id];
                        $this_post_pixel = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/' . $tracking_pixel_id, 'GET', null, $api_key);
                        if ($this_post_pixel[errors] != null || $this_post_pixel[status] == 3) {
                            WPClickmeter::delete_pixel($post_id);
                        }
                    }
                }
            }
        }

        if ($pixels_flag == 0) {
            //header('HTTP/1.0 403 Forbidden');
            //for each post into exclusion list
            if (!empty($exclusion_list)) {
                foreach ($exclusion_list as $post_id) {
                    $post = get_post($post_id);
                    $pixel_data = WPClickmeter::get_pixel($post_id);
                    //Look for already existent pixels in clickmeter for actual campaign with the same title of the post
                    if ($pixel_data != null) {
                        //if exist -> take it
                        $pixel_name = $pixel_data[pixel_name];
                        //delete tracking pixel into post by tracking code
                        $doc = new DOMDocument();
/*                        if (!empty($post->post_content)) {
                            $remove = array();
                            //WPClickmeter::store_option("clickmeter_debug_content", $post->post_content);
                            $doc->loadHTML(mb_convert_encoding($post->post_content, 'HTML-ENTITIES', 'UTF-8'));
                            //WPClickmeter::store_option("clickmeter_debug_content", $doc->saveHTML());
                            $doc->encoding = 'UTF-8';
                            $divTags = $doc->getElementsByTagName('div');
                            //echo $pixel_name . "<br>";
                            foreach ($divTags as $div) {
                                if (preg_match("/clkmtr_tracking_pixel/", $div->attributes->getNamedItem('id')->nodeValue)) {
                                    $remove[] = $div;
                                }
                            }
                            foreach ($remove as $item) {
                                $item->parentNode->removeChild($item);
                            }
                            $new_content = preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si", "!</body></html>$!si"), "", $doc->saveHTML());
                            $modified_post = array(
                                'ID' => $post->ID,
                                'post_content' => $new_content
                            );
                            wp_update_post($modified_post);
                            //echo "pixel rimosso true, dentro ".$post->post_title."<br>";
                        }*/
                        WPClickmeter::update_status_pixel($post_id, 'paused');
                        //check if exists on ClickMeter
                        $tracking_pixel_id = $pixel_data[pixel_id];
                        $this_post_pixel = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/' . $tracking_pixel_id, 'GET', null, $api_key);
                        if ($this_post_pixel[errors] != null || $this_post_pixel[status] == 3) {
                            WPClickmeter::delete_pixel($post_id);
                        }
                    }
                }
            }
        }

        //WPClickmeter::store_option("clickmeter_workinprogress_flag", "false");
        //echo "Execution terminated ok";
        die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error TP_savechanges';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}
add_action( 'wp_ajax_TP_savechanges', 'TP_savechanges' );

function getTrackingPixel($result, $post_title){
    $trackingCode = $result[entityData][trackingCode];
    $tracking_pixel = "<div id='clkmtr_tracking_pixel'>
    	<!--ClickMeter.com WordPress tracking: " . $post_title . " -->
        <script type='text/javascript'>
        var ClickMeter_pixel_url = '" . $trackingCode . "';
        </script>
        <script type='text/javascript' id='cmpixelscript' src='https://www.clickmeter.com/js/pixel.js'></script>
        <noscript>
        <img height='0' width='0' alt='' src='" . $trackingCode . "' />
        </noscript>
    </div>";

    return $tracking_pixel;
}

function TP_init_creation() {
    try {
        global $wpdb; // this is how you get access to the database

        $api_key = WPClickmeter::get_option('clickmeter_api_key');
        $group_id_TP = WPClickmeter::get_option('clickmeter_TPcampaign_id');
        $toCreateList = $_POST["inclusion_list"];

        //Create new tracking pixels in batch mode
        if (!empty($toCreateList)) {
            $i = 1;
            $body = array();
            $posts_map = array();
            $posts_list = array();
            $pages_list = array();

            //Add conversions to created TP if exist
            $conversion1_id = WPClickmeter::get_option('clickmeter_conversionId1');
            $conversion2_id = WPClickmeter::get_option('clickmeter_conversionId2');
            foreach ($toCreateList as $post_id) {
                $dbValue = WPClickmeter::get_pixel($post_id);
                if ($dbValue != null) {
                    continue;
                } else {
                    $post = get_post($post_id);
                    $post_title = $post->post_title;
                    $post_title = WPClickmeter::getIdentifier($post_title, $post_id);
                    $post_type = $post->post_type;
                    $track_pix = array('type' => 1, 'title' => $post_title, "groupId" => $group_id_TP);
                    $conversion_target1 = WPClickmeter::get_option("clickmeter_conversionTarget1");
                    $conversion_target2 = WPClickmeter::get_option("clickmeter_conversionTarget2");
                    if ($conversion1_id != null) {
                        if (in_array($post_type, $conversion_target1)) $track_pix["firstConversionId"] = $conversion1_id;
                    }
                    if ($conversion2_id != null) {
                        if (in_array($post_type, $conversion_target2)) {
                            if ($track_pix["firstConversionId"] == null) {
                                $track_pix["firstConversionId"] = $conversion2_id;
                            } else {
                                $track_pix["secondConversionId"] = $conversion2_id;
                            }
                        }
                    }
                    $body["list"][] = $track_pix;
                    $posts_map[$post->post_title] = $post_id;

                    //WPClickmeter::store_option("click_debug",$body);
                    if ($i == sizeof($toCreateList) && !empty($body)) {
                        $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/batch', 'PUT', json_encode($body), $api_key);
                        //WPClickmeter::store_option("click_debug1",$json_output[results]);
                        foreach ($json_output[results] as $result) {
                            if ($result[entityData][id] != null) {

                                $pid = $posts_map[$result[entityData][title]];
                                $created_pixel_id = $result[entityData][id];
                                $created_pixel_name = $result[entityData][name];
                                $trackingCode = $result[entityData][trackingCode];
                                $timestamp = $result[entityData][creationDate];
                                $postToChange = get_post($pid);
                                $post_type = $postToChange->post_type;
                                if ($post_type == "page") {
                                    $pages_list[] = $created_pixel_id;
                                } else {
                                    $posts_list[] = $created_pixel_id;
                                }

                                WPClickmeter::store_pixel($pid, $created_pixel_id, $created_pixel_name, $trackingCode, $group_id_TP, $post_type, $timestamp);
                            }
                        }
                        $i++;
                    }

                    if ($i % 25 == 0) {
                        $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/batch', 'PUT', json_encode($body), $api_key);
                        foreach ($json_output[results] as $result) {
                            if ($result[entityData][id] != null) {

                                $pid = $posts_map[$result[entityData][title]];
                                $created_pixel_id = $result[entityData][id];
                                $created_pixel_name = $result[entityData][name];
                                $trackingCode = $result[entityData][trackingCode];
                                $timestamp = $result[entityData][creationDate];
                                $postToChange = get_post($pid);
                                $post_type = $postToChange->post_type;
                                if ($post_type == "page") {
                                    $pages_list[] = $created_pixel_id;
                                } else {
                                    $posts_list[] = $created_pixel_id;
                                }

                                WPClickmeter::store_pixel($pid, $created_pixel_id, $created_pixel_name, $trackingCode, $group_id_TP, $post_type, $timestamp);
                            }

                        }
                        $body = array();
                    }
                    $i++;
                }
            }

            //ADD POST TO TAG LIST
            $tag_body = array('name' => "page", 'datapoints' => $pages_list);
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/tags', 'POST', json_encode($tag_body), $api_key);
            $tag_body = array('name' => "post", 'datapoints' => $posts_list);
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/tags', 'POST', json_encode($tag_body), $api_key);
        }

        //WPClickmeter::store_option("clickmeter_workinprogress_flag", "false");
        die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error TP_init_creation';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}
add_action( 'wp_ajax_TP_init_creation', 'TP_init_creation' );

function TP_delete_apikey() {
    try {
        $api_key = WPClickmeter::get_option('clickmeter_api_key');
        $post_ids_array = WPClickmeter::get_option("clickmeter_current_exclusion_list");
        $posts_array = WPClickmeter::retrieve_posts_by_id($post_ids_array);
        $body = array();

        //delete all tracking pixel from pages
        foreach ($posts_array as $post) {
            $doc = new DOMDocument();
            if (!empty($post->post_content)) {
                $remove = array();
                $doc->loadHTML(mb_convert_encoding($post->post_content, 'HTML-ENTITIES', 'UTF-8'));
                $doc->encoding = 'UTF-8';
                $divTags = $doc->getElementsByTagName('div');
                //echo $pixel_name . "<br>";
                foreach ($divTags as $div) {
                    if (preg_match("/clkmtr_tracking_pixel/", $div->attributes->getNamedItem('id')->nodeValue)) {
                        $remove[] = $div;
                    }
                }
                foreach ($remove as $item) {
                    $item->parentNode->removeChild($item);
                }
                $new_content = preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si", "!</body></html>$!si"), "", $doc->saveHTML());
                $modified_post = array(
                    'ID' => $post->ID,
                    'post_content' => $new_content
                );
                wp_update_post($modified_post);
                //echo "pixel rimosso true, dentro ".$post->post_title."<br>";
            }

            //delete tracking pixel from clickmeter
            // $pixel_data = WPClickmeter::get_pixel($post->ID);
            // if($pixel_data != null) $tracking_pixel_id = $pixel_data[pixel_id];
            // if($tracking_pixel_id != null) $track_pix = array('id'=>$tracking_pixel_id);
            // $body["entities"][] = $track_pix;
        }

        //$json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/datapoints/batch','DELETE', json_encode($body), $api_key);

        die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error TP_delete_apikey';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}
add_action( 'wp_ajax_TP_delete_apikey', 'TP_delete_apikey' );

function TP_delete_pixels() {
    try {
        WPClickmeter::get_option('clickmeter_api_key');
        $post_ids_array = WPClickmeter::get_option("clickmeter_current_exclusion_list");
        $posts_array = WPClickmeter::retrieve_posts_by_id($post_ids_array);

        //delete all tracking pixel from pages
        foreach ($posts_array as $post) {

            // delete 'http:' from tracking_code
            $pixel = WPClickmeter::get_pixel($post->ID);
            if (($pixel != null) && (!empty($pixel))) {
                $tracking_code = preg_replace('/^http:/', '', $pixel['tracking_code']);
                WPClickmeter::update_tracking_code_pixel($post->ID, $tracking_code);
            }

            $doc = new DOMDocument();
            $update = false;
            if (!empty($post->post_content)) {
                $remove = array();
                $doc->loadHTML(mb_convert_encoding($post->post_content, 'HTML-ENTITIES', 'UTF-8'));
                $doc->encoding = 'UTF-8';
                $divTags = $doc->getElementsByTagName('div');
                foreach ($divTags as $div) {
                    if (preg_match("/clkmtr_tracking_pixel/", $div->attributes->getNamedItem('id')->nodeValue)) {
                        $remove[] = $div;
                        $update = true;
                    }
                }
                foreach ($remove as $item) {
                    $item->parentNode->removeChild($item);
                }
                if ($update) {
                    $new_content = preg_replace(array("/^\<\!DOCTYPE.*?<html><body>/si", "!</body></html>$!si"), "", $doc->saveHTML());
                    $modified_post = array(
                        'ID' => $post->ID,
                        'post_content' => $new_content
                    );
                    wp_update_post($modified_post);
                    WPClickmeter::update_status_pixel($post->ID, 'active');
                }
            }
            if (!$update){
                $pixel = WPClickmeter::get_pixel($post->ID);
                $inclusion_list = WPClickmeter::get_option("clickmeter_inclusion_list");
                if ($inclusion_list != null) {
                    if (($pixel != null) && (in_array($post->ID, $inclusion_list))) {
                        WPClickmeter::update_status_pixel($post->ID, 'active');
                    }
                }
            }
        }

        die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error TP_delete_pixels';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}
add_action( 'wp_ajax_TP_delete_pixels', 'TP_delete_pixels' );

function TP_associate_conversion() {
    try {
        global $wpdb; // this is how you get access to the database

        //get the list of tracking pixels from clickmeter's APIs
        $api_key = WPClickmeter::get_option('clickmeter_api_key');
        $conversionToAssociate = $_POST["conversion_id"];
        $conversion_target = WPClickmeter::get_option('clickmeter_lastconversion_target');
        $posts_array = $_POST["post_list"];
        //WPClickmeter::store_option('clickmeter_debug', $posts_array);
        foreach ($posts_array as $post_id) {
            $pixel_data = WPClickmeter::get_pixel($post_id);
            //Look for already existent pixels in clickmeter for actual campaign with the same title of the post
            if ($pixel_data != null) {
                $pixel_id = $pixel_data[pixel_id];
                $pixel_tag = $pixel_data[tag];
                if (in_array($pixel_tag, $conversion_target)) {
                    $body = array('action' => 'add', 'id' => $pixel_id);
                    $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/conversions/' . $conversionToAssociate . '/datapoints/patch', 'PUT', json_encode($body), $api_key);
                }
            }
        }

        //WPClickmeter::store_option("clickmeter_workinprogress_flag", "false");
        die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error TP_associate_conversion';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}
add_action( 'wp_ajax_TP_associate_conversion', 'TP_associate_conversion' );

function TP_delete_conversion(){
    try {
        global $wpdb; // this is how you get access to the database

        $api_key = WPClickmeter::get_option('clickmeter_api_key');

        $conversionToDelete = $_POST["conversion_id"];
        $posts_array = $_POST["post_list"];
        foreach ($posts_array as $post_id) {
            $pixel_data = WPClickmeter::get_pixel($post_id);
            //Look for already existent pixels in clickmeter for actual campaign with the same title of the post
            if ($pixel_data != null) {
                $pixel_id = $pixel_data[pixel_id];
                $body = array('action' => 'remove', 'id' => $pixel_id);
                $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/conversions/' . $conversionToDelete . '/datapoints/patch', 'PUT', json_encode($body), $api_key);
            }
        }

        //WPClickmeter::store_option("clickmeter_workinprogress_flag", "false");
        die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error TP_delete_conversion';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}
add_action( 'wp_ajax_TP_delete_conversion', 'TP_delete_conversion' );

function TP_delete_first_conversion() {
    try {
        global $wpdb; // this is how you get access to the database

        //get the list of tracking pixels from clickmeter's APIs
        $api_key = WPClickmeter::get_option('clickmeter_api_key');
        $group_id_TP = WPClickmeter::get_option('clickmeter_TPcampaign_id');
        $pixels_list = array();
        $offset = 0;
        $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/groups/' . $group_id_TP . '/datapoints?offset=0&limit=100&type=TP&status=active&_expand=true', 'GET', NULL, $api_key);
        while (!empty($json_output[entities])) {
            foreach ($json_output[entities] as $pixel) {
                $pixels_list[] = $pixel;
            }
            $offset += 100;
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/groups/' . $group_id_TP . '/datapoints?offset=' . $offset . '&limit=100&type=TP&status=active&_expand=true', 'GET', NULL, $api_key);
        }

        $conversion1_id = WPClickmeter::get_option('clickmeter_conversionId1');

        WPClickmeter::store_option("clickmeter_conversionId1", "");
        if ($conversion1_id == WPClickmeter::get_option("clickmeter_default_firstConv_links")) WPClickmeter::store_option("clickmeter_default_firstConv_links", "");

        //Update tracking pixel in batch mode
        if (!empty($pixels_list)) {
            foreach ($pixels_list as $pixel) {
                $body = array('action' => 'remove', 'id' => $pixel[id]);
                $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/conversions/' . $conversion1_id . '/datapoints/patch', 'PUT', json_encode($body), $api_key);
            }
        }

        //WPClickmeter::store_option("clickmeter_workinprogress_flag", "false");
        die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error TP_delete_first_conversion';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}
add_action( 'wp_ajax_TP_delete_first_conversion', 'TP_delete_first_conversion' );

function TP_delete_second_conversion() {
    try {
        global $wpdb; // this is how you get access to the database

        //get the list of tracking pixels from clickmeter's APIs
        $api_key = WPClickmeter::get_option('clickmeter_api_key');
        $group_id_TP = WPClickmeter::get_option('clickmeter_TPcampaign_id');
        $pixels_list = array();
        $offset = 0;
        $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/groups/' . $group_id_TP . '/datapoints?offset=0&limit=100&type=TP&status=active&_expand=true', 'GET', NULL, $api_key);
        while (!empty($json_output[entities])) {
            foreach ($json_output[entities] as $pixel) {
                $pixels_list[] = $pixel;
            }
            $offset += 100;
            $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/groups/' . $group_id_TP . '/datapoints?offset=' . $offset . '&limit=100&type=TP&status=active&_expand=true', 'GET', NULL, $api_key);
        }

        $conversion2_id = WPClickmeter::get_option('clickmeter_conversionId2');

        WPClickmeter::store_option("clickmeter_conversionId2", "");
        if ($conversion2_id == WPClickmeter::get_option("clickmeter_default_secondConv_links")) WPClickmeter::store_option("clickmeter_default_secondConv_links", "");

        //Update tracking pixel in batch mode
        if (!empty($pixels_list)) {
            foreach ($pixels_list as $pixel) {
                $body = array('action' => 'remove', 'id' => $pixel[id]);
                $json_output = WPClickmeter::api_request('http://apiv2.clickmeter.com/conversions/' . $conversion2_id . '/datapoints/patch', 'PUT', json_encode($body), $api_key);
            }
        }

        //WPClickmeter::store_option("clickmeter_workinprogress_flag", "false");

        die(); // this is required to return a proper result
    }catch (Exception $e){
        $subject = 'wp error TP_delete_second_conversion';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}
add_action( 'wp_ajax_TP_delete_second_conversion', 'TP_delete_second_conversion' );


//EDITING BOX BUTTONS
add_action('admin_head', 'clickmeter_add_my_tc_button');
function clickmeter_add_my_tc_button() {
    try {
        global $typenow;
        // check user permissions
        if (!current_user_can('edit_posts') && !current_user_can('edit_pages')) {
            return;
        }
        // verify the post type
        if (!in_array($typenow, array('post', 'page')))
            return;
        // check if WYSIWYG is enabled
        if (get_user_option('rich_editing') == 'true') {
            add_filter("mce_external_plugins", "clickmeter_add_tinymce_plugin");
            add_filter('mce_buttons', 'clickmeter_register_my_tc_button');
        }
    }catch (Exception $e){
        $subject = 'wp error clickmeter_add_my_tc_button';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}

function clickmeter_register_my_tc_button($buttons) {
    //array_push($buttons, 'clickmeter_tc_button');
    //array_push($buttons, 'clickmeter_tc_button_all');
    array_push($buttons, 'clickmeter_buttongroup');
    return $buttons;
}

function clickmeter_add_tinymce_plugin($plugin_array) {
    //$plugin_array['clickmeter_tc_button'] = plugins_url( '/js/clickmeter_editingTLButton.js', __FILE__ );
    //$plugin_array['clickmeter_tc_button_all'] = plugins_url( '/js/clickmeter_editingAllTLButton.js', __FILE__ );
    $plugin_array['clickmeter_buttongroup'] = plugins_url( '/js/clickmeter_buttongroup.js', __FILE__ );
    return $plugin_array;
}

function createDB(){
    global $wpdb;
    $clickmeter_plugin_base_version = "1.0.0";

    /*
     * We'll set the default character set and collation for this table.
     * If we don't do this, some characters could end up being converted
     * to just ?'s when saved in our table.
     */
    $charset_collate = '';

    if ( ! empty( $wpdb->charset ) ) {
        $charset_collate = "DEFAULT CHARACTER SET {$wpdb->charset}";
    }

    if ( ! empty( $wpdb->collate ) ) {
        $charset_collate .= " COLLATE {$wpdb->collate}";
    }

    $option_table_name = $wpdb->prefix . 'clickmeter_options';
    $option_table = "CREATE TABLE $option_table_name (
		id mediumint(9) NOT NULL AUTO_INCREMENT,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		key_name text NOT NULL,
		value text NOT NULL,
		is_array tinyint(1) NOT NULL,
		UNIQUE KEY id (id)
	) $charset_collate;";

    $pixel_table_name = $wpdb->prefix . 'clickmeter_tracking_pixels';
    $pixel_table = "CREATE TABLE $pixel_table_name (
		post_id mediumint(9) NOT NULL,
		pixel_id mediumint(9) NOT NULL,
		pixel_name text NOT NULL,
		tracking_code text NOT NULL,
		status varchar(255) NOT NULL DEFAULT 'paused',
		campaign_id mediumint(9) NOT NULL,
		tag text NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		UNIQUE KEY pixel_id (pixel_id)
	) $charset_collate;";

    $link_table_name = $wpdb->prefix . 'clickmeter_tracking_links';
    $link_table = "CREATE TABLE $link_table_name (
		post_id mediumint(9) NOT NULL,
		tracking_link_id mediumint(9) NOT NULL,
		campaign_id mediumint(9) NOT NULL,
		campaign_name text NOT NULL,
		tracking_code text NOT NULL,
		destination_url text NOT NULL,
		domain_id mediumint(9) NOT NULL,
		link_name text NOT NULL,
		link_rid text NOT NULL,
		is_post tinyint(1) NOT NULL,
		is_redirection_link tinyint(1) NOT NULL,
		time datetime DEFAULT '0000-00-00 00:00:00' NOT NULL,
		UNIQUE KEY tracking_link_id (tracking_link_id)
	) $charset_collate;";

    require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );

    dbDelta( $pixel_table );
    dbDelta( $link_table );
    dbDelta( $option_table );

    $version = get_option('clickmeter_plugin_version');
    if($version != null){
        update_option("clickmeter_plugin_version", $version);
    }else{
        update_option("clickmeter_plugin_version", $clickmeter_plugin_base_version);
    }
}

//DATABASE CREATION
function clickmeter_install() {
    try {
        createDB();
    } catch (Exception $e){
        $subject = 'wp error clickmeter_install';
        $content = $e->getMessage() . "\n";
        $content = $content . $e->getTraceAsString() . "\n";
        WPClickmeter::to_email($subject, $content);
    }
}

register_activation_hook( __FILE__, 'clickmeter_install' );

?>