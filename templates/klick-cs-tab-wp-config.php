<!-- First Tab content -->
<div id="klick_cs_tab_first">
	<div class="klick-cs-data-listing-wrap">
	<article class="klick-cs-data-listing-row"> 	
		<h1> This is one of important file in your WordPress installation is the wp-config.php file. This file is located in the root of your WordPress file directory and contains your website's base configuration details, such as database connection </h1> <!-- Header tab-->
		<?php
		 $fcontent = $options->file_read(get_home_path() . 'wp-config.php');
		 echo '<div class="klick-cs-info"><pre>' . htmlentities($fcontent) . '</pre></div>';
		 echo "</pre>";
		?>
	</article>	
	</div>
</div>
