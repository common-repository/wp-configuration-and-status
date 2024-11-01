<!-- Third Tab content -->
<div id="klick_cs_tab_third">
	<div class="klick-cs-data-listing-wrap">
	<article class="klick-cs-data-listing-row"> 	
		<h1>.htaccess is a configuration file for use on web servers running the Apache Web Server software. When a .htaccess file is placed in a directory which is in turn 'loaded via the Apache Web Server', then the .htaccess file is detected and executed by the Apache Web Server software.</h1> <!-- Header tab-->
		<?php
		$fcontenthtaccess = $options->file_read(get_home_path() . '.htaccess');
		echo '<div class="klick-cs-info"><pre><pre>' . htmlentities($fcontenthtaccess) . '</pre></div>';
		?>
	</article>	
	</div>
</div>
