<?php
	get_header();
?>

<div class="title-wrap">
	<div class="wrapper">
		<h1 class="pagetitle"><?php the_title(); ?></h1>
	</div>	
</div>
<div class="wrapper">
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
	get_sidebar ();?>
	
	</div>
<?php	
	get_footer();
?>