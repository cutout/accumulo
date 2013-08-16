<?php
/*
Template Name: Archives
*/

get_header();

?>

<div id="content">

	<h1 class="pagetitle"><?php the_title(); ?></h1>
    
	<div class="post">
    <div class="entry">
	
	<?php
        add_filter('post_limits', 'my_post_limit');
        global $myOffset;
        $myOffset = 0;
        global $postsperpage;
        $postsperpage = 20; //Number of posts per page
        $temp = $wp_query;
        $wp_query= null;
        $wp_query = new WP_Query();
        $wp_query->query('ignore_sticky_posts=1&orderby=post_date&order=DESC&offset='.$myOffset.'&showposts='.$postsperpage.'&paged='.$paged);
		$previous_year = false;
		$previous_month = false;
		$ul_open = false;

		while ($wp_query->have_posts()) {
			$wp_query->the_post();

			setup_postdata($post);
			$year = mysql2date('Y', $post->post_date);
			$month = mysql2date('n', $post->post_date);
			$day = mysql2date('j', $post->post_date);

			if ($year != $previous_year || $month != $previous_month) {
				if ($ul_open == true) {
?>
            </ul>
<?php
				}
?>            
            <h2><?php the_time('F Y'); ?></h2>
            <ul id="archive-list">
<?php
				$ul_open = true;
			}

			$previous_year = $year;
			$previous_month = $month;
?>     
                <li><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></li>
<?php
		}
?>
            </ul>
<?php
	include (TEMPLATEPATH . '/pagination.php');
	$wp_query = null;
	$wp_query = $temp;
    remove_filter('post_limits', 'my_post_limit');
?>
	</div><!--/entry-->
    </div><!--/post-->
</div><!--/content-->

<?php
	get_sidebar();
	get_footer();
?>