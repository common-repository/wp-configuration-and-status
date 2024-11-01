<!-- Second Tab content -->
<div id="klick_cs_tab_second">
	<div class="klick-cs-data-listing-wrap">
	<article class="klick-cs-data-listing-row"> 
		<h1>This is display to view the current PHP information for your server</h1> <!-- Header tab-->
		<?php
			$fcontentini = $options->file_read(php_ini_loaded_file());
			echo '<div class="klick-cs-info"><pre><pre>' . htmlentities($fcontentini) . '</pre>';
		 	echo "</pre>";
		?>
	</article>	
	</div>
</div>
