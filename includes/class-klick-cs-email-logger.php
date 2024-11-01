<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Klick_Cs_EMAIL_Logger')) return;

/**
 * Class Klick_Cs_EMAIL_Logger
 */
class Klick_Cs_EMAIL_Logger extends Klick_Cs_Abstract_Logger {

	public $id = "url";

	public $additiona_params = array();

	/**
	 * Klick_Cs_EMAIL_Logger constructor
	 */
	public function __construct() {
	}

	/**
	 * Returns logger description
	 *
	 * @return string|void
	 */
	public function get_description() {
		return __('Log events into Url address', 'klick-cs');
	}
	
	/**
	 * Log message with any level
	 *
	 * @param  mixed  $level
	 * @param  string $message
	 *
	 * @return void
	 */
	public function log($level, $message) {

		if (!$this->is_enabled()) return false;
		
		$message = 'From url[' . $level . '] : ' . $message;
		$this-> send_mail($level, $message);
	}

	/**
	 * Sends mail
	 *
	 * @param  mixed  $level
	 * @param  string $message
	 *
	 * @return void
	 */
	public function send_mail($level, $message) {
		wp_mail($this->additiona_params['url'], $level,$message);	
	}
}
