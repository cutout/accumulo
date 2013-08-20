	<form method="get" class="searchform" action="<?php echo home_url(); ?>/">
		<input type="text" value="<?php the_search_query(); ?>" name="s" class="searchfield" placeholder="Search..." /><input type="image" 
		src="<?php echo get_template_directory_uri(); ?>/images/magnify.png" class="searchsubmit" />
	</form>