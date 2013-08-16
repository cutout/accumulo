<?php
	get_header ();
?>
	<div id="content">
<?php
	if (have_posts ()) {
		$post = $posts[0];
		if (is_category ()) {
?>	
		<h1 class="pagetitle"><?php _e('Category Archive for','accumulo'); ?> &#8216;<?php single_cat_title (); ?>&#8217; <a class="rss" href="<?php echo get_category_feed_link('category_rss2_url'); ?>feed/"><img src="<?php bloginfo('template_url'); ?>/images/rss.png" alt="rss" id="icon-rss" /></a></h1>
<?php
		} elseif (is_tag ()) {
?>
		<h1 class="pagetitle"><?php _e('Tag Archive for','accumulo'); ?> &#8216;<?php single_tag_title (); ?>&#8217;</h1>
<?php
		} elseif (is_day ()) {
?>
		<h1 class="pagetitle"><?php _e('Archive for','accumulo'); ?> <?php the_time ('F jS, Y'); ?></h1>
<?php
		} elseif (is_month ()) {
?>
		<h1 class="pagetitle"><?php _e('Archive for','accumulo'); ?> <?php the_time ('F Y'); ?></h1>
<?php
		} elseif (is_year ()) {
?>
		<h1 class="pagetitle"><?php _e('Archive for','accumulo'); ?> <?php the_time ('Y'); ?></h1>
<?php
		} elseif (isset ($_GET['paged']) && !empty ($_GET['paged'])) {
?>
		<h1 class="pagetitle"><?php _e('Blog Archives','accumulo'); ?></h1>
<?php
		}
		while (have_posts()) {
			the_post();
			include (TEMPLATEPATH . '/blog_tab.php');
		}
		include (TEMPLATEPATH . '/pagination.php');
	} else {
?>
		<h2><?php _e('Not Found','accumulo'); ?></h2>
<?php
	}
?>
	</div><!--/content-->
<?php
	get_sidebar ();
	get_footer ();
?>