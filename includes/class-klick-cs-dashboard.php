<?php

if (!defined('ABSPATH')) die('No direct access allowed');

if (class_exists('Klick_Cs_Dashboard')) return;

/**
 * Class Klick_Cs_Dashboard
 */
class Klick_Cs_Dashboard {

	/**
	 * Klick_Cs_Dashboard constructor
	 */
	public function __construct() {
	}

	/**
	 * Initalize menu and submenu
	 */
	public function init_menu(){

		$capability_required = Klick_Cs()->capability_required();

		if (!current_user_can($capability_required)) return;

		$enqueue_version = (defined('WP_DEBUG') && WP_DEBUG) ? KLICK_CS_VERSION . '.' . time() : KLICK_CS_VERSION;
		$min_or_not = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

		// Register and enqueue script
		wp_enqueue_script( 'jquery' );
		wp_register_script( "klick_cs_script", KLICK_CS_PLUGIN_URL . 'js/klick-cs' . $min_or_not . '.js', array('jquery'), $enqueue_version);
		wp_enqueue_script( 'klick_cs_script' );

		// Register and enqueue style
		wp_enqueue_style('klick_cs_css', KLICK_CS_PLUGIN_URL . 'css/klick-cs' . $min_or_not . '.css', array(), $enqueue_version);   
		wp_enqueue_style('klick_cs_notices_css', KLICK_CS_PLUGIN_URL . 'css/klick-cs-notices' . $min_or_not . '.css', array(), $enqueue_version);

		$icon = KLICK_CS_PLUGIN_URL . "/images/small_icon.png";
		add_options_page('WP Configuration and Status', 'WP Configuration and Status', $capability_required, 'klick_cs', array($this, 'klick_cs_tab_view'),$icon);

		// Define hook and function to render admin notice
		add_action('all_admin_notices', array($this, 'show_admin_dashboard_notice'));

	}
	
	/**
	 * Initlize script and localize script
	 *
	 * @return void
	 */
	public function init_user_end(){

		$enqueue_version = (defined('WP_DEBUG') && WP_DEBUG) ? KLICK_CS_VERSION . '.' . time() : KLICK_CS_VERSION;
		$min_or_not = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

		// Register and enqueue script
		wp_enqueue_script( 'jquery' );
		wp_register_script("klick_cs_ui_script", KLICK_CS_PLUGIN_URL . 'js/klick-cs-ui' . $min_or_not . '.js', array('jquery'), $enqueue_version);
		wp_enqueue_script( 'klick_cs_ui_script' );

		// Define localize script to get localize string
		wp_localize_script('klick_cs_ui_script', 'klick_cs', array(
			'ajaxurl' => admin_url('admin-ajax.php', 'relative'),
			'klick_cs_ajax_nonce' => wp_create_nonce('klick_cs_ajax_nonce'),
			'klick_cs_plugin_url' => KLICK_CS_PLUGIN_URL,
		));
	}

	/**
	 * Renders css at user side
	 *
	 * @return void
	 */
	public function init_user_css(){

		$enqueue_version = (defined('WP_DEBUG') && WP_DEBUG) ? KLICK_CS_VERSION . '.' . time() : KLICK_CS_VERSION;
		$min_or_not = (defined('SCRIPT_DEBUG') && SCRIPT_DEBUG) ? '' : '.min';

		// Register and enqueue style
		wp_enqueue_style('klick_cs_ui_css', KLICK_CS_PLUGIN_URL . 'css/klick-cs-ui' . $min_or_not . '.css', array(), $enqueue_version);   
	}

	/**
	 * Renders Notice at main WP dashboard
	 *
	 * @return void
	 */
	public function show_admin_dashboard_notice(){
		Klick_Cs()->get_notifier()->do_notice('dashboard');
	}
	
	/**
	 * Renders tabs page with template
	 *
	 * @return void
	 */
	public function klick_cs_tab_view() { 

		$capability_required = Klick_Cs()->capability_required();

		if (!current_user_can($capability_required)) {
			echo "Permission denied.";
			return;
		}

		?>
		<br>
		<?php
		
		// Define tabs, set default/active tab
		$tabs = $this->get_tabs();
		
		$active_tab = apply_filters('klick_cs_admin_default_tab', 'wp-config');
		
		echo '<div id="klick_cs_tab_wrap" class="klick-cs-tab-wrap">';

		$this->include_template('klick-cs-tabs-header.php', false, array('active_tab' => $active_tab, 'tabs' => $tabs));

		$tab_data = array();
			
		foreach ($tabs as $tab_id => $tab_description) {

			echo '<div class="klick-cs-nav-tab-contents" id="klick_cs_nav_tab_contents_' . $tab_id . '" ' . (($tab_id == $active_tab) ? '' : 'style="display:none;"') . '>';
			
			do_action('klick_cs_admin_tab_render_begin', $active_tab);
			
			$tab_data[$tab_id] = isset($tab_data[$tab_description])? $tab_data[$tab_description]:array();
			
			$this->include_template('klick-cs-tab-' . $tab_id . '.php',false, array('data' => $tab_data[$tab_id]));

			echo '</div>';
		}
		
		do_action('klick_cs_admin_tab_render_end', $active_tab);
		
		echo '</div>';
	}
	
	/**
	 * Set tab names
	 *
	 * @return array
	 */
	public function get_tabs() {
		return apply_filters('klick_cs_admin_page_tabs', array('wp-config' => '<span class="dashicons dashicons-wordpress-alt"></span> ' . __('wp-config', 'klick-cs'), 'php-info' => '<span class="dashicons dashicons-info"></span> ' . __('php.info', 'klick-cs'), 'htaccess' => '<span class="dashicons dashicons-admin-tools"></span> ' . __('htaccess', 'klick-cs'), 'our-other-plugins' => __('Our other Plugins', 'klick-cs'), 'change-log' => __('Change Log', 'klick-cs')));
	}
	
	/**
	 * Brings in templates
	 *
	 * @return void
	 */
	public function include_template($path, $return_instead_of_echo, $extract_these = array()) {
		if ($return_instead_of_echo) ob_start();

		if (preg_match('#^([^/]+)/(.*)$#', $path, $matches)) {
			$prefix = $matches[1];
			$suffix = $matches[2];
			if (isset(Klick_Cs()->template_directories[$prefix])) {
				$template_file = Klick_Cs()->template_directories[$prefix] . '/' . $suffix;
			}
		}
		
		if (!isset($template_file)) {
			$template_file = KLICK_CS_PLUGIN_MAIN_PATH . '/templates/' . $path;
		}

		$template_file = apply_filters('klick_cs_template', $template_file, $path);

		do_action('klick_cs_before_template', $path, $template_file, $return_instead_of_echo, $extract_these);

		if (!file_exists($template_file)) {
			error_log("Klick: template not found: " . $template_file);
		} else {
			extract($extract_these);

			// Defines the vars used in included template file
			$klick_cs = Klick_Cs();
			$options = Klick_Cs()->get_options();
			$dashboard = $this;
			include $template_file;
		}

		do_action('klick_cs_after_template', $path, $template_file, $return_instead_of_echo, $extract_these);

		if ($return_instead_of_echo) return ob_get_clean();
	}

	/**
	 * 
	 * This function can be update to suit any URL as longs as the URL is passed
	 *
	 * @param string $url   URL to be check to see if it an klickonit match.
	 * @param string $text  Text to be entered within the href a tags.
	 * @param string $html  Any specific HTMl to be added.
	 * @param string $class Specify a class for the href.
	 */
	public function klick_cs_url($url, $text, $html = null, $class = null) {
		// Check if the URL is klickonit.
		if (false !== strpos($url, '//klick-on-it.com')) {
			// Apply filters.
			$url = apply_filters('klick_cs_klick_on_it_com', $url);
		}
		// Return URL - check if there is HTMl such as Images.
		if (!empty($html)) {
			echo '<a ' . $class . ' href="' . esc_attr($url) . '">' . $html . '</a>';
		} else {
			echo '<a ' . $class . ' href="' . esc_attr($url) . '">' . htmlspecialchars($text) . '</a>';
		}
	}
}
