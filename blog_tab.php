<?php
	global $bm_options;
	$image = bm_postImage ($bm_options['thumbnailWidth'], $bm_options['thumbnailHeight']);
?>
			<div id="post-<?php the_ID(); ?>" <?php post_class(); ?>>
				<h2 class="posttitle"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title(); ?></a></h2>
<?php
	if ($image) {
		echo $image;
	}
?>
				<p class="postmetadata"><em><?php _e('by','accumulo'); ?></em> <?php the_author_posts_link(); ?> <em><?php _e('on','accumulo'); ?></em> <?php the_time('M d, Y'); ?> 
					<span class="commentcount">(<?php comments_popup_link('0', '1', '%'); ?>) <?php _e('Comments','accumulo'); ?></span>
				</p>
				<div class="entry">
					<?php bm_excerpt (); ?>
				</div>
			</div>