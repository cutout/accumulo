<?php get_header(); ?>

	<div id="content">

<h1 class="pagetitle"><?php _e('Search results for','accumulo'); ?> &#8216;<em><?php the_search_query() ?></em>&#8217;</h1>

	<?php if (have_posts()) : while (have_posts()) : the_post(); ?>
	
			<?php include (TEMPLATEPATH . '/loop.php'); ?>

		<?php endwhile; ?>
		
		<?php include (TEMPLATEPATH . '/pagination.php'); ?>
        
	<?php else : ?>
		<p><?php _e('Not Found','accumulo'); ?></p>
	<?php endif; ?>

</div><!--/content-->

<?php get_sidebar(); ?>

<?php get_footer(); ?>