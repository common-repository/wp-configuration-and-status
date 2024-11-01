<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Klick_Cs_No_Config')) return;

require_once(KLICK_CS_PLUGIN_MAIN_PATH . '/includes/class-klick-cs-abstract-notice.php');

/**
 * Class Klick_Cs_No_Config
 */
class Klick_Cs_No_Config extends Klick_Cs_Abstract_Notice {
	
	/**
	 * Klick_Cs_No_Config constructor
	 */
	public function __construct() {
		$this->notice_id = 'config-and-status-configure';
		$this->title = __('WP Configuration and Status plugin is installed but not configured', 'klick-cs');
		$this->klick_cs = "";
		$this->notice_text = __('Configure it Now', 'klick-cs');
		$this->image_url = '../images/our-more-plugins/cs.svg';
		$this->dismiss_time = 'dismiss-page-notice-until';
		$this->dismiss_interval = 30;
		$this->display_after_time = 0;
		$this->dismiss_type = 'dismiss';
		$this->dismiss_text = __('Hide Me!', 'klick-cs');
		$this->position = 'dashboard';
		$this->only_on_this_page = 'index.php';
		$this->button_link = KLICK_CS_PLUGIN_SETTING_PAGE;
		$this->button_text = __('Click here', 'klick-cs');
		$this->notice_template_file = 'main-dashboard-notices.php';
		$this->validity_function_param = 'wp-configuration-and-status/wp-configuration-and-status.php';
		$this->validity_function = 'is_plugin_configured';
	}
}
