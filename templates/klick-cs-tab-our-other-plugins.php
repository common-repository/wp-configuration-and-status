<!-- Second Tab content -->
<div id="klick_cs_tab_fourth">
	<h1><img src="<?php echo KLICK_CS_PLUGIN_URL . '/images/klick-on-it.png'; ?>"> <?php _e('Our new and upcoming plugins', 'klick-cs'); ?></h1>
	<br>
	<ul class="klick-cs-plugin-list">
		<?php 
		// Array (plugin logos and links)
		$logo_links = array(
			array('link_name' => '404-page-management','link_logo' => '404'),
			array('link_name' => 'advance-plugin-search','link_logo' => 'APS'),
			array('link_name' => 'configuration-and-status','link_logo' => 'CS'),
			array('link_name' => 'cron-viewer-and-manager','link_logo' => 'CVM'),
			array('link_name' => 'make-my-own-shortcodes','link_logo' => 'MOS'),
			array('link_name' => 'new-install-setup','link_logo' => 'NIS'),
			array('link_name' => 'plugin-template','link_logo' => 'PT'),
			array('link_name' => 'statistics-and-analytics','link_logo' => 'SA'),
			array('link_name' => 'short-link-and-traffic','link_logo' => 'SLT'),
			array('link_name' => 'user-activity-logger','link_logo' => '404'),
			array('link_name' => 'wordpress-reset','link_logo' => 'WPR'),
		);
		$logo_with_link = "";

		// iternate through $logo_links
		foreach ($logo_links as $key => $value) { ?>
			<li>
				<img src="<?php echo KLICK_CS_PLUGIN_URL . '/images/our-more-plugins/' . $value['link_logo'] . '.svg';?>">
				<h4><a href="http://klick-on-it.com/wordpress-plugins/<?php echo $value['link_name']; ?>"><?php _e('Visit here', 'klick-cs'); ?></a></h4>
			</li>	
		<?php } ?>
	</ul>
</div>
