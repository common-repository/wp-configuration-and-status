/**
 * Send an action via admin-ajax.php
 * 
 * @param {string} action - the action to send
 * @param * data - data to send
 * @param Callback [callback] - will be called with the results
 * @param {boolean} [json_parse=true] - JSON parse the results
 */
var klick_cs_send_command = function (action, data, callback, json_parse) {
	json_parse = ('undefined' === typeof json_parse) ? true : json_parse;
	var ajax_data = {
		action: 'klick_cs_ajax',
		subaction: action,
		nonce: klick_cs_ajax_nonce,
		data: data
	};
	jQuery.post(ajaxurl, ajax_data, function (response) {
		
		if (json_parse) {
			try {
				var resp = JSON.parse(response);
			} catch (e) {
				console.log(e);
				console.log(response);
				return;
			}
		} else {
			var resp = response;
		}
		
		if ('undefined' !== typeof callback) callback(resp);
	});
}

/**
 * When DOM ready
 * 
 */
jQuery(document).ready(function ($) {
	klick_cs = klick_cs(klick_cs_send_command);
});

/**
 * Function for sending communications
 * 
 * @callable sendcommandCallable
 * @param {string} action - the action to send
 * @param * data - data to send
 * @param Callback [callback] - will be called with the results
 * @param {boolean} [json_parse=true] - JSON parse the results
 */
/**
 * Main klick_cs
 * 
 * @param {sendcommandCallable} send_command
 */
var klick_cs = function (klick_cs_send_command) {
	var $ = jQuery;

	/**
	 * Proceses the tab click handler
	 *
	 * @return void
	 */
	$('#klick_cs_nav_tab_wrapper .nav-tab').click(function (e) {
		e.preventDefault();
		
		var clicked_tab_id = $(this).attr('id');
	
		if (!clicked_tab_id) { return; }
		if ('klick_cs_nav_tab_' != clicked_tab_id.substring(0, 17)) { return; }
		
		var clicked_tab_id = clicked_tab_id.substring(17);

		$('#klick_cs_nav_tab_wrapper .nav-tab:not(#klick_cs_nav_tab_' + clicked_tab_id + ')').removeClass('nav-tab-active');
		$(this).addClass('nav-tab-active');

		$('.klick-cs-nav-tab-contents:not(#klick_cs_nav_tab_contents_' + clicked_tab_id + ')').hide();
		$('#klick_cs_nav_tab_contents_' + clicked_tab_id).show();
	});


}
