<?php

class ClickmeterViews {
	
	static function base_path() {
		return plugin_dir_path(__FILE__);
	}

	private static function template_path($template) {
		return self::base_path() . $template . '.php';
	}

	static function enterKeyPage() {
		include self::template_path('/view/clickmeter-entry_key_page');
	}

	static function missingAPIKeyWarning() {
		$link = esc_url(add_query_arg(array('page' => 'clickmeter-link-shortener-and-analytics/view/clickmeter-account.php'), admin_url('admin.php'))); 
		#echo $link;
		include self::template_path('/view/clickmeter-missing_apikey_warning');
	}
	
	static function saveCompletedWarning() {
		include self::template_path('/view/clickmeter-save_completed_warning');
	}

	static function clickmeter_meta_box_callback($post) {
    	include self::template_path('/view/clickmeter-post_tags_meta_box');
	}

	static function clickmeter_meta_box_redirection_callback($post) {
    	include self::template_path('/view/clickmeter-post_redirection_meta_box');
	}
}

?>