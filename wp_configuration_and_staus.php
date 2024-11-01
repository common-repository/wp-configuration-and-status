<?php
/**
Plugin Name: WP Configuration and Status
Description: WordPress configuration and Status helps you manage those configurations and customisations.
Version: 0.0.3
Author: klick on it
Author URI: http://klick-on-it.com
License: GPLv2 or later
Text Domain: klick-cs
 */

/*
This plugin developed by klick-on-it.com
*/

/*
Copyright 2017 klick on it (http://klick-on-it.com)

This program is free software; you can redistribute it and/or modify
it under the terms of the GNU General Public License (Version 3 - GPLv3)
as published by the Free Software Foundation.

This program is distributed in the hope that it will be useful,
but WITHOUT ANY WARRANTY; without even the implied warranty of
MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
GNU General Public License for more details.

You should have received a copy of the GNU General Public License
along with this program; if not, write to the Free Software
Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307  USA
*/

if (!defined('ABSPATH')) die('No direct access allowed');

if (!class_exists('Klick_Cs')) :
define('KLICK_CS_VERSION', '0.0.1');
define('KLICK_CS_PLUGIN_URL', plugin_dir_url(__FILE__));
define('KLICK_CS_PLUGIN_MAIN_PATH', plugin_dir_path(__FILE__));
define('KLICK_CS_PLUGIN_SETTING_PAGE', admin_url() . 'admin.php?page=klick_cs');

class Klick_Cs {

	protected static $_instance = null;

	protected static $_options_instance = null;

	protected static $_notifier_instance = null;

	protected static $_logger_instance = null;

	protected static $_dashboard_instance = null;
	
	/**
	 * Constructor for main plugin class
	 */
	public function __construct() {
		
		register_activation_hook(__FILE__, array($this, 'klick_cs_activation_actions'));

		register_deactivation_hook(__FILE__, array($this, 'klick_cs_deactivation_actions'));

		add_action('wp_ajax_klick_cs_ajax', array($this, 'klick_cs_ajax_handler'));
		
		add_action('admin_menu', array($this, 'init_dashboard'));
		
		add_action('plugins_loaded', array($this, 'setup_translation'));
		
		add_action('plugins_loaded', array($this, 'setup_loggers'));

		add_action( 'wp_footer', array($this, 'klick_cs_ui_scripts'));

		add_action('wp_head', array($this, 'klick_cs_ui_css'));

	}


	/**
	 * Instantiate Klick_Cs if needed
	 *
	 * @return object Klick_Cs
	 */
	public static function instance() {
		if (empty(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

	/**
	 * Instantiate Klick_Cs_Options if needed
	 *
	 * @return object Klick_Cs_Options
	 */
	public static function get_options() {
		if (empty(self::$_options_instance)) {
			if (!class_exists('Klick_Cs_Options')) include_once(KLICK_CS_PLUGIN_MAIN_PATH . '/includes/class-klick-cs-options.php');
			self::$_options_instance = new Klick_Cs_Options();
		}
		return self::$_options_instance;
	}
	
	/**
	 * Instantiate Klick_Cs_Dashboard if needed
	 *
	 * @return object Klick_Cs_Dashboard
	 */
	public static function get_dashboard() {
		if (empty(self::$_dashboard_instance)) {
			if (!class_exists('Klick_Cs_Dashboard')) include_once(KLICK_CS_PLUGIN_MAIN_PATH . '/includes/class-klick-cs-dashboard.php');
			self::$_dashboard_instance = new Klick_Cs_Dashboard();
		}
		return self::$_dashboard_instance;
	}
	
	/**
	 * Instantiate Klick_Cs_Logger if needed
	 *
	 * @return object Klick_Cs_Logger
	 */
	public static function get_logger() {
		if (empty(self::$_logger_instance)) {
			if (!class_exists('Klick_Cs_Logger')) include_once(KLICK_CS_PLUGIN_MAIN_PATH . '/includes/class-klick-cs-logger.php');
			self::$_logger_instance = new Klick_Cs_Logger();
		}
		return self::$_logger_instance;
	}
	
	/**
	 * Instantiate Klick_Cs_Notifier if needed
	 *
	 * @return object Klick_Cs_Notifier
	 */
	public static function get_notifier() {
		if (empty(self::$_notifier_instance)) {
			include_once(KLICK_CS_PLUGIN_MAIN_PATH . '/includes/class-klick-cs-notifier.php');
			self::$_notifier_instance = new Klick_Cs_Notifier();
		}
		return self::$_notifier_instance;
	}
	
	/**
	 * Establish Capability
	 *
	 * @return string
	 */
	public function capability_required() {
		return apply_filters('klick_cs_capability_required', 'manage_options');
	}
	
	/**
	 * Init dashboard with menu and layout
	 *
	 * @return void
	 */
	public function init_dashboard() {
		$dashboard = $this->get_dashboard();
		$dashboard->init_menu();
		load_plugin_textdomain('klick-cs', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

	/**
	 * To enqueue js at user side
	 *
	 * @return void
	 */
	public function klick_cs_ui_scripts(){
		$dashboard = $this->get_dashboard();
		$dashboard->init_user_end();
	}

	/**
	 * To enqueue css at user side
	 *
	 * @return void
	 */
	public function klick_cs_ui_css(){
		$dashboard = $this->get_dashboard();
		$dashboard->init_user_css();
	}

	/**
	 * Perform post plugin loaded setup
	 *
	 * @return void
	 */
	public function setup_translation() {
		load_plugin_textdomain('klick-cs', false, dirname(plugin_basename(__FILE__)) . '/languages');
	}

	/**
	 * Creates an array of loggers, Activate and Adds
	 *
	 * @return void
	 */
	public function setup_loggers() {
		
		$logger = $this->get_logger();

		$loggers = $logger->klick_cs_get_loggers();
		
		$logger->activate_logs($loggers);
		
		$logger->add_loggers($loggers);
	}
	
	/**
	 * Ajax Handler
	 */
	public function klick_cs_ajax_handler() {

		$nonce = empty($_POST['nonce']) ? '' : $_POST['nonce'];

		if (!wp_verify_nonce($nonce, 'klick_cs_ajax_nonce') || empty($_POST['subaction'])) die('Security check');
		
		$parsed_data = array();
		$data = array();
		
		$subaction = sanitize_key($_POST['subaction']);
		
		$post_data = isset($_POST['data']) ? $_POST['data'] : null;
		
		parse_str($post_data, $parsed_data); // convert string to array
		
		switch ($subaction) {
			// Add more cases here if you add subaction in plugin
			default:
				error_log("Klick_Cs_Commands: ajax_handler: no such sub-action (" . esc_html($subaction) . ")");
				die('No such sub-action/command');
		}
		
		$results = array();
		
		// Get sub-action class
		if (!class_exists('Klick_Cs_Commands')) include_once(KLICK_CS_PLUGIN_MAIN_PATH . 'includes/class-klick-cs-commands.php');

		$commands = new Klick_Cs_Commands();

		if (!method_exists($commands, $subaction)) {
			error_log("Klick_Cs_Commands: ajax_handler: no such sub-action (" . esc_html($subaction) . ")");
			die('No such sub-action/command');
		} else {
			$results = call_user_func(array($commands, $subaction), $data);

			if (is_wp_error($results)) {
				$results = array(
					'result' => false,
					'error_code' => $results->get_error_code(),
					'error_message' => $results->get_error_message(),
					'error_data' => $results->get_error_data(),
					);
			}
		}
		
		echo json_encode($results);
		die;
	}

	/**
	 * Plugin activation actions.
	 *
	 * @return void
	 */
	public function klick_cs_activation_actions(){
		$this->get_options()->set_default_options();
	}

	/**
	 * Plugin deactivation actions.
	 *
	 * @return void
	 */
	public function klick_cs_deactivation_actions(){
		$this->get_options()->delete_all_options();
	}
}

register_uninstall_hook(__FILE__,'klick_cs_uninstall_option');

/**
 * Delete data when uninstall
 *
 * @return void
 */
function klick_cs_uninstall_option(){
	Klick_Cs()->get_options()->delete_all_options();
}

/**
 * Instantiates the main plugin class
 *
 * @return instance
 */
function Klick_Cs(){
     return Klick_Cs::instance();
}

endif;

$GLOBALS['Klick_Cs'] = Klick_Cs();
