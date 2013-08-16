<?php
	$rowCount = $count % 3;
	$count ++;
?>
	<li class="feed-item item-position-<?php echo $count; ?> item-row-position-<?php echo $rowCount; ?>">
		<h3><img src="/images/<?php $key="favicon"; echo get_post_meta($post->ID, $key, true); ?>" alt="favicon" /> <a href="<?php $key="siteurl"; echo get_post_meta($post->ID, $key, true); ?>"><?php the_title(); ?></a></h3>
<?php
	$key="feedurl";
	wp_rss(get_post_meta($post->ID, $key, true), 5);
	if(function_exists('the_ratings')) {
		the_ratings();
	}
?>
    </li>