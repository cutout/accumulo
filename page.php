<?php
	get_header();
?>
	<div id="content">
<?php
	if (have_posts()) {
		while (have_posts()) {
			the_post();
			include (TEMPLATEPATH . '/loop.php');
			comments_template();
		}
	} else {
?>
		<p><?php _e('Not Found','accumulo'); ?></p>
<?php
	}
?>        
    </div>
<?php
	get_sidebar ();
	get_footer();
?>