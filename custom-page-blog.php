<?php
/*
Template Name: Blog
*/

get_header();

?>
<div class="title-wrap">
	<div class="wrapper">
		<h1 class="pagetitle"><?php the_title(); ?></h1>
	</div>	
</div>

<div class="wrapper bg-page">
<div class="col-content">
<?php
	$page = (get_query_var ('paged')) ? get_query_var ('paged') : 1;
	$query = new WP_Query ('paged=' . $page);

	if ($query->have_posts ()) {
		while ($query->have_posts ()) {
			$query->the_post ();
			include ('blog_tab.php');
		}

		include (TEMPLATEPATH . '/pagination.php');
	} else {
?>
	<p><?php _e('Not Found','accumulo'); ?></p>
<?php
	}
?>
</div>
<?php get_sidebar(); ?>
</div>

<?php 
	get_footer();
?>