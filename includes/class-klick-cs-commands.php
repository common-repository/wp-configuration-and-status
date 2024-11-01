<?php 

if (!defined('KLICK_CS_PLUGIN_MAIN_PATH')) die('No direct access allowed');

/**
 * Commands available from control interface (e.g. wp-admin) are here
 * All public methods should either return the data, or a WP_Error with associated error code, message and error data
 */
/**
 * Sub commands for Ajax
 *
 */
class Klick_Cs_Commands {
	private $options;
	
	/**
	 * Constructor for Commands class
	 *
	 */
	public function __construct() {
		$this->options = Klick_Cs()->get_options();
	} 

	/**
	 * dis-miss button
	 *
	 * @param  Array 	$data an array of data UI form
	 *
	 * @return Array 	$status
	 */
	public function dismiss_page_notice_until($data) {
		
		return array(
			'status' => $this->options->dismiss_page_notice_until($data),
			);
	}

	/**
	 * dis-miss button
	 *
	 * @param  Array 	$data an array of data UI form
	 *
	 * @return Array 	$status
	 */
	public function dismiss_page_notice_until_forever($data) {
		
		return array(
			'status' => $this->options->dismiss_page_notice_until_forever($data),
			);
	}
}
