<?php

/**
 * calculate the posts with the most comments from the last 6 months
 *
 * @global <type> $wpdb
 * @param <type> $args
 * @param <type> $displayComments
 * @param <type> $interval
 */
function bm_popularPosts ($args = array(), $displayComments = TRUE, $interval = '') {

	global $wpdb;
	
	$postCount = 5;
	
	$request = 'SELECT *
		FROM ' . $wpdb->posts . '
		WHERE ';
		
	if ($interval != '') {
		$request .= 'post_date>DATE_SUB(NOW(), ' . $interval . ') ';
	}
		
	$request .= 'post_status="publish"
			AND comment_count > 0
		ORDER BY comment_count DESC LIMIT 0, ' . $postCount;
			
	$posts = $wpdb->get_results ($request);

	if (count ($posts) >= 1) {
	
		$defaults = array (
			'title' => __('Popular Posts', BM_THEMENAME),
		);
	
		$args = bm_defaultArgs ($args, $defaults);
		
		foreach ($posts as $p) {
			wp_cache_add ($p->ID, $p, 'posts');
			$popularPosts[] = array(
				'ID' => $p->ID,
				'title' => stripslashes ($p->post_title),
				'url' => get_permalink($p->ID),
				'comment_count' => $p->comment_count,
				'post_content' => $p->post_content,
			);
		}
		
		echo $args['before_widget'] . $args['before_title'] . $args['title'] . $args['after_title'];
?>
	<div class="widgetInternalWrapper">
		<ul class="popularPosts postsList">
<?php
		$listClass = 'even';
		
		foreach ($popularPosts as $p) {
			if ($listClass == 'odd') {
				$listClass = 'even';
			} else {
				$listClass = 'odd';
			}

			$image = bm_postImage (38, 38, $p['ID'], $p['post_content']);
?>
			<li class="<?php echo $listClass; ?> clear">
				<?php echo $image; ?>
				<a href="<?php echo $p['url'];?>"><?php echo $p['title']; ?></a>
<?php
			if ($displayComments) {
?>
				<span class="commentsCount">(<?php printf (__('%d comments', BM_THEMENAME), $p['comment_count']); ?>)</span>
<?php
			}
?>
			</li>
<?php
		}
?>
		</ul>
	</div>
<?php
		echo $args['after_widget'];

	}

}


/**
 *
 * @global array $bm_mapList
 * @param <type> $args
 */
function bm_googleMaps ($args = array()) {

	global $bm_mapList;

	$defaults = array (
		'title' => __('Location', BM_THEMENAME),
		'location' => __('London, England', BM_THEMENAME),
		'zoomLevel' => 15,
		'mapType' => 'ROADMAP',
	);

	$args = bm_defaultArgs ($args, $defaults);
	
	$mapData = array (
		'location' => $args['location'],
		'mapId' => 'map_container_' . rand(10, 99),
		'zoomLevel' => $args['zoomLevel'],
		'mapType' => $args['mapType'],
	);
	
	$bm_mapList[] = $mapData;
	
	echo $args['before_widget'] . $args['before_title'] . $args['title'] . $args['after_title'];
?>
	<div class="widgetInternalWrapper">
		<div class="mapWrapper">
			<div id="<?php echo $mapData['mapId']; ?>" style="height:240px;"></div>
		</div>
	</div>
<?php
	echo $args['after_widget'];

}


/**
 *
 * @global array $bm_mapList
 * @return <type>
 */
function bm_googleMapsFooter () {

	global $bm_mapList;
	
	if (count ($bm_mapList) == 0) {
		return FALSE;
	}
	
?>
	<script type="text/javascript" src="http://maps.google.com/maps/api/js?sensor=false"></script>
	<script type="text/javascript">
	var geocoder;
	geocoder = new google.maps.Geocoder();

	function codeAddress(address, map) {
		if (geocoder) {
			geocoder.geocode( { 'address': address}, function(results, status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (status != google.maps.GeocoderStatus.ZERO_RESULTS) {
						map.setCenter(results[0].geometry.location);
					} else {
						alert("No results found");
					}
				} else {
					alert("Geocode was not successful for the following reason: " + status);
				}
			});
		}
	}
<?php
	$mapNumber = 0;
	foreach ($bm_mapList as $bm_map) {
		$mapNumber ++;
?>
	myOptions = {
		zoom: <?php echo $bm_map['zoomLevel']?>,
		center: new google.maps.LatLng(0, 0),
		disableDefaultUI: true,
		draggable: true,
		mapTypeId: google.maps.MapTypeId.<?php echo $bm_map['mapType'] ?>
	}
	map<?php echo $mapNumber; ?> = new google.maps.Map(document.getElementById("<?php echo $bm_map['mapId']; ?>"), myOptions);
	codeAddress("<?php echo $bm_map['location']; ?>", map<?php echo $mapNumber; ?>);
	
<?php
	}
?>
	</script>
<?php

}


/**
 *
 * @global <type> $post
 * @param <type> $args
 * @return <type>
 */
function bm_relatedPosts ($args = array()) {
	
	if (!is_single()) {
		return FALSE;
	}
	
	global $post;

	$tags = wp_get_post_tags ($post->ID);
	
	if ($tags) {
	
		$searchTags = array();
		foreach ($tags as $t) {
			$searchTags[] = $t->term_id;
		}
		
		$query = array(
			'tag__in' => (array) $searchTags,
			'post__not_in' => array ($post->ID),
			'showposts' => 5,
			'ignore_sticky_posts' => 1,
		);
		
		$bmWp = new WP_Query ($query);
		
		if ($bmWp->have_posts ()) {
		
			$defaults = array (
				'title' => __('Related Posts', BM_THEMENAME),
			);
		
			$args = bm_defaultArgs($args, $defaults);
			
			echo $args['before_widget'] . $args['before_title'] . $args['title'] . $args['after_title'];
?>
		<ul class="relatedPosts postsList">
<?php
			while ($bmWp->have_posts()) {
				$bmWp->the_post();
?>
			<li><a href="<?php the_permalink() ?>" rel="bookmark" title="<?php _e('Permanent Link to', BM_THEMENAME); ?> <?php the_title_attribute(); ?>"><?php the_title(); ?></a></li>
<?php
			}
?>
		</ul>
<?php
			echo $args['after_widget'];
		}	
	}

}


/**
 * get other posts from the current category
 *
 * @global  $wpdb
 * @global <type> $primaryPostData
 * @global <type> $bmIgnorePosts
 * @param <type> $args
 * @param <type> $postAmount
 */
function bm_categoryPosts ($args = array (), $postAmount = 4) {

	// make sure it's a single post
	if (is_single()) {
	
		global $wpdb, $primaryPostData, $bmIgnorePosts;
		
		$bm_cats = get_the_category ($primaryPostData['ID']);
		$bm_clist = '';
		
		foreach ($bm_cats as $bm_c) {
			if ($bm_clist != '') {
				$bm_clist .= ',';
			}
			$bm_clist .= $bm_c->term_id;
		}
		
		$sql = 'SELECT p.* FROM ' . $wpdb->posts . ' AS p
			INNER JOIN ' . $wpdb->term_relationships . ' AS t ON t.object_id = p.ID
			INNER JOIN ' . $wpdb->term_taxonomy . ' AS tt ON tt.term_taxonomy_id = t.term_taxonomy_id
			WHERE p.post_type = "post"
				AND tt.taxonomy = "category"
				AND tt.term_id IN (' . $bm_clist . ')
				AND p.post_type = "post"
				AND p.post_password = ""
				AND p.post_status = "publish"';
		if (count($bmIgnorePosts) >= 1) {
			$sql .= ' AND p.ID NOT IN(' . implode(',', $bmIgnorePosts) . ')';
		}
		$sql .= 'GROUP BY p.ID
			ORDER BY post_date DESC LIMIT ' . $postAmount;

		$posts = $wpdb->get_results($sql);
		
		if ($posts) {
		
			$defaults = array (
				'title' => __('More from this category', BM_THEMENAME),
			);
			
			$args = bm_defaultArgs($args, $defaults);
			
			echo $args['before_widget'] . $args['before_title'] . $args['title'] . $args['after_title'];
?>
	<ul class="categoryPosts postsList">
<?php	
			foreach ($posts as $p) {
				wp_cache_add ($p->ID, $p, 'posts');
				bm_ignorePost ($p->ID);
				$image = bm_postImage (38, 38, $p->ID, $p->post_content);
?>
		<li class="clear">
			<?php echo $image; ?>
			<a href="<?php echo get_permalink ($p->ID); ?>"><?php echo $p->post_title; ?></a>
		</li>
<?php
			}
			
			wp_reset_query();
?>
	</ul>
<?php
			echo $args['after_widget'];
			
		}
	}
	
}


/**
 * get other posts from the current author
 *
 * @global  $wpdb
 * @global  $primaryPostData
 * @global  $bmIgnorePosts
 * @param <type> $args
 * @param <type> $postAmount
 */
function bm_authorPosts($args = array(), $postAmount = 4) {

	// make sure it's a single post
	if (is_single()) {
	
		global $wpdb, $primaryPostData, $bmIgnorePosts;
			
		$sql = 'SELECT * FROM ' . $wpdb->posts . '
			WHERE post_type = "post"
				AND post_status = "publish"
				AND post_author = ' . $primaryPostData['author'];
				
		if (count($bmIgnorePosts) >= 1) {
			$sql .= ' AND ID NOT IN(' . implode(',', $bmIgnorePosts) . ')';
		}
		
		$sql .= ' ORDER BY post_date DESC LIMIT 0, ' . $postAmount;

		$posts = $wpdb->get_results($sql);

		if ($posts) {
		
			$defaults = array (
				'title' => __('More from this author', BM_THEMENAME),
			);
			
			$args = bm_defaultArgs($args, $defaults);
			
			echo $args['before_widget'] . $args['before_title'] . $args['title'] . $args['after_title'];
?>
	<ul class="authorPosts postsList">
<?php
			foreach ($posts as $p) {
				wp_cache_add ($p->ID, $p, 'posts');
				bm_ignorePost ($p->ID);
				$image = bm_postImage (38, 38, $p->ID, $p->post_content);
?>
		<li class="clear">
			<?php echo $image; ?>
			<a href="<?php echo get_permalink ($p->ID); ?>"><?php echo $p->post_title; ?></a>
		</li>
<?php
			}
			wp_reset_query();			
?>
	</ul>
<?php
			echo $args['after_widget'];
			
		}
	}
}


/**
 * display author details for the current post
 *
 * @global  $post
 * @param <type> $args
 */
function bm_postDetails ($args = array()) {

	global $bm_options;

	// make sure it's a single post
	if (is_singular()) {
	
		global $post;

		if (!empty ($post) && $post->post_author != '') {
		
			$args = bm_defaultArgs ($args);
			
			echo $args['before_widget'];
?>
	<div id="postDetails">
<?php
			if (!empty ($args['title'])) {
				echo $args['before_title'] . $args['title'] . $args['after_title'];
			}
?>
		<a href="<?php echo get_author_posts_url($post->post_author); ?>" class="authorLink"><?php echo get_avatar($post->post_author, 38, $bm_options['defaultGravatarRaw']); ?></a>
		<ul>
			<li class="postDetailsAuthor"><?php _e('Posted by', BM_THEMENAME); ?> <?php the_author_posts_link(); ?>
<?php
			$twitter = get_the_author_meta('twitter', $post->post_author);
			if ($twitter != '') {
?>
				 <a href="<?php echo esc_url('http://twitter.com/' . $twitter); ?>" class="twitter">[<?php _e('Twitter', BM_THEMENAME); ?>]</a></li>
<?php
			}
?>			
			</li>
<?php
			if (isset ($args['showDate']) && $args['showDate'] == true) {
?>
			<li class="postDetailsDate"><?php bm_theDate(); ?></li>
<?php
			}
?>
			<li class="postDetailsCommentsFeed"><?php post_comments_feed_link(); ?></li>
<?php
			the_tags( '<li class="postDetailsTags">' . __('Tags : ', BM_THEMENAME), ', ', '</li>');
?>
		</ul>
	</div>
<?php
			echo $args['after_widget'];
		}
		
	}
}


/**
 * social links for current post
 *
 * @param array args previously specified arguments
 */
function bm_sharePost($args = array()) {
	
	$defaults = array (
		'title' => __('Share This', BM_THEMENAME),
	);
	
	$args = bm_defaultArgs($args, $defaults);
	
	echo $args['before_widget'] . $args['before_title'] . $args['title'] . $args['after_title'];
	
	bm_socialLinks();

	echo $args['after_widget'];

}


/**
 * post your latest Tweets on your sidebar
 *
 * @param array args previously specified arguments
 * @param string searchQuery
 */
function bm_twitter ($args = array(), $searchQuery = '') {
	
	$defaults = array (
		'title' => __('Twitter Updates', BM_THEMENAME),
		'count' => 5,
		'username' => '',
	);
		
	$args = bm_defaultArgs ($args, $defaults);

	if (!empty ($args['username'])) {
		$args['username'] = str_replace ('http://twitter.com/', '', $args['username']);
		$args['username'] = str_replace ('@', '', $args['username']);
		$args['username'] = str_replace ('/', '', $args['username']);
	} else {
		return false;
	}

	if ($searchQuery == '') {
		$searchQuery = 'q=' . urlencode ('from:' . $args['username']);
	}
	
	$requestPath = 'http://search.twitter.com/search.json?' . $searchQuery . '&rpp=' . $args['count'];
	
	echo '<!-- twitter request url : ' . $requestPath . ' -->';
	
	$content = bm_getContent ($requestPath, 180, 'twitter');
	
	if ($content) {
		
		$content = json_decode ($content);
		
		if (count ($content->results) > 0) {
	
			echo $args['before_widget'] . $args['before_title'] . $args['title'] . $args['after_title'];	
?>
	<div class="twitterWidget">
		<div class="widgetInternalWrapper">
			<ul class="twitter_update_list">
<?php
			$listClass = 'even';
			foreach ($content->results as $tweet) {
				if ($listClass == 'odd') {
					$listClass = 'even';
				} else {
					$listClass = 'odd';
				}
				
				$tweet->text = make_clickable ($tweet->text);
				$tweet->text = preg_replace ('/(@([_a-z0-9\-]+))/i','<a href="http://twitter.com/$2" class="tw_mention">$1</a>', $tweet->text);
				$tweet->text = preg_replace ('/(#([_a-z0-9\-]+))/i','<a href="http://twitter.com/search?q=%23$2" class="tw_hashtag">$1</a>', $tweet->text);

				$tweet->created_at = bm_timeSince (strtotime ($tweet->created_at), FALSE, 1);
?>
				<li class="<?php echo $listClass; ?>">
					<div class="tweetContent">
						<a href="http://www.twitter.com/<?php echo $tweet->from_user; ?>/status/<?php echo (string) $tweet->id_str; ?>/" class="timthumb profile_image_url">
							<img src="<?php echo $tweet->profile_image_url; ?>" width="38" height="38" alt="<?php echo $tweet->from_user; ?>" />
						</a>
						<p class="tweetText"><?php echo $tweet->text; ?></p>
						<p class="tweetDate"><a href="http://www.twitter.com/<?php echo $tweet->from_user; ?>/status/<?php echo $tweet->id_str; ?>/"><?php echo $tweet->created_at; ?></a></p>
					</div>
				</li>
<?php
			}
?>
			</ul>
		</div>
	</div>
<?php
			echo $args['after_widget'];
		}
		
	}

}


/**
 * add "subscribe to blog" links to the sidebar
 * 
 * @param array args previously specified arguments
 */
function bm_rssFeeds ($args = array()) {

	$defaults = array (
		'link_text' => __('Subscribe to updates', BM_THEMENAME),
	);
	
	$args = bm_defaultArgs ($args, $defaults);
	
	echo $args['before_widget'];	
?>
	<a class="rss_feed" href="<?php bloginfo ('rss2_url'); ?>"><?php echo $args['link_text']; ?></a>
<?php
	echo $args['after_widget'];

}


/**
 * list posts tagged as set to publish in the future
 *
 * @param array args previously specified arguments
 * @param int postAmount the amount of posts to display
 */
function bm_upcomingPosts ($args = array(), $postAmount = 5) {
	
	global $wpdb;
	
	$sql = 'SELECT * FROM ' . $wpdb->posts . '
		WHERE post_type = "post" AND post_status = "future"
		ORDER BY post_date DESC LIMIT ' . $postAmount;
		
	$posts = $wpdb->get_results($sql);
	
	if (count ($posts) > 0) {

		$defaults = array (
			'title' => __('Upcoming Posts', BM_THEMENAME),
		);
		
		$args = bm_defaultArgs($args, $defaults);
		
		echo $args['before_widget'] . $args['before_title'] . $args['title'] . $args['after_title'];	
?>
	<ul class="upcomingPosts postsList">
<?php	
		foreach ($posts as $p) {
			$image = bm_postImage (38, 38, $p->ID, $p->post_content);
?>
		<li class="clear">
			<?php echo $image; ?>
			<a href="<?php echo get_permalink ($p->ID); ?>"><?php echo $p->post_title; ?></a>
		</li>
<?php
		}	
?>
	</ul>
<?php
		echo $args['after_widget'];
		
	}
	
}


/**
 * display any number of custom widgets
 *
 * @param array args default widget arguments
 * @param array widget an array containing the widgets data 
 */
function bm_customWidget ($args, $widget) {
	
	$callback = $widget[1];
	
	if (is_callable($callback)) {
	
		// set some paramters if neccessary
		if (isset ($widget[3])) {
			$param = $widget[3];
		} else {
			$param = '';
		}
	
		extract($args);
		echo $before_widget;
		echo $before_title . $widget[0] . $after_title;
		
		if (isset ($widget[4])) {
			echo $widget[4];
		}
		call_user_func ($callback, $param);
		if (isset($widget[5])) {
			echo $widget[5];
		}
		
		echo $after_widget;

	}
	
}


/**
 * add a new widget bar to the theme
 *
 * @param array widget an array containing the widgets data
 */
function bm_registerWidgetbar( $widget ) {

	if (!isset($widget['name'])) {
		return FALSE;
	}

	if (!isset ($widget['size'])) {
		$widget['size'] = 4;
	}
	
	$widget['id'] = bm_widgetId ($widget);
	
	$defaults = array (
		'before_widget' => '<div id="%1$s" class="widget %2$s column span-' . $widget['size'] . '">',
		'before_title' => '<div class="clear"><h3 class="widgettitle"><span>',
		'after_title' => '</span></h3></div>',
		'after_widget' => '</div>',
		'description' => '',
	);
	
	$widgetProperties = $widget;
	unset($widgetProperties['widgets']);
	
	$widget = array_merge ($defaults, $widgetProperties);
	
	register_sidebar( $widget );
	
	return true;

}


/**
 * format a widget bars id based upon it's name or previously specified id
 *
 * @param <type> $widget
 * @return <type>
 */
function bm_widgetId ($widget) {
	
	if (!isset ($widget['id'])) {
		$widget['id'] = $widget['name'];
	}

	$idName = $widget['id'];
	$idName = strtolower ($idName);
	$idName = str_replace (' ', '-', $idName);

	return $idName;
	
}


/**
 *
 * @global  $wpdb
 * @param <type> $pageTemplates
 * @return <type>
 */
function bm_getActiveTemplates ($pageTemplates) {

	if (!empty ($pageTemplates)) {
		
		global $wpdb;
		
		$pageTemplates = (array) $pageTemplates;
		$templates = array();
		
		foreach ($pageTemplates as $p) {
			$templates[] = $p['template'];
		}
		
		//$sql = 'SELECT post_id, meta_value as post_template FROM ' . $wpdb->postmeta . ' where meta_key = "_wp_page_template" AND post_status="publish" AND meta_value IN ("' . implode('","', $templates) . '")';
		$sql = 'SELECT post_id, meta_value as post_template FROM ' . $wpdb->postmeta . ' where meta_key = "_wp_page_template" AND meta_value IN ("' . implode('","', $templates) . '")';

		$result = $wpdb->get_results ($sql);
		
		if ($result) {
			return $result;
		}

	}
	
	return FALSE;
	
}

/**
 *
 * @param <type> $templateList
 * @param <type> $postids
 * @return array
 */
function bm_createTemplateWidgets ($templateList, $postids) {
		
	$postids = (array) $postids;
	$widgets = array ();
	
	if (!empty ($postids)) {
		foreach ($postids as $p) {

			if (!empty ($p)) {

				foreach ($templateList as $t) {
					if ($t['template'] == $p->post_template) {
						for ($i = 1; $i <= $t['cols']; $i ++) {
							$id = 'customColumns-' . $i . '-p' . $p->post_id;
							$widgets[$id] = array (
								'name' => $i . ': ' . $t['name'] . ' (id: ' . $p->post_id . ')',
								'size' => $t['width'],
								'id' => $id,
								'description' => '',
								'widgets' => array(),
							);
						}
					}
				}

			}
		
		}
	}
	
	return $widgets;

}


/**
 *
 * @return <type>
 */
function bm_registerSidebars() {
	
	// turn on new widgets
	$widgets = bm_widgetSettings();
	
	if ( count( $widgets ) > 0 ) {
		foreach ( $widgets as $widget ) {
			bm_registerWidgetbar( $widget );
		}
	}
	
	return TRUE;

}