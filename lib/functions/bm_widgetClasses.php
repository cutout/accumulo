<?php
/**
 * 
 */
class bm_widget_popularPosts extends WP_Widget {

	/**
	 *
	 */
	function bm_widget_popularPosts() {
		parent::WP_Widget(false, 'Popular Posts');
	}

	/**
	 *
	 * @param array $args
	 * @param <type> $instance
	 */
	function widget($args, $instance) {
		$args['title'] = $instance['title'];
		bm_popularPosts($args);
	}

	/**
	 *
	 * @param <type> $new_instance
	 * @param <type> $old_instance
	 * @return <type>
	 */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/**
	 *
	 * @param <type> $instance
	 */
	function form ($instance) {
		$title = '';
		if (!empty ($instance)) {
			$title = esc_attr ($instance['title']);
		}
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', BM_THEMENAME); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
<?php
	}
 
}


/**
 * 
 */
class bm_widget_relatedPosts extends WP_Widget {

	/**
	 *
	 */
	function bm_widget_relatedPosts () {
		parent::WP_Widget (false, 'Related Posts (by tag)');
	}

	/**
	 *
	 * @param array $args
	 * @param <type> $instance
	 */
	function widget ($args, $instance) {
		$args['title'] = $instance['title'];
		bm_relatedPosts ($args);
	}

	/**
	 *
	 * @param <type> $new_instance
	 * @param <type> $old_instance
	 * @return <type>
	 */
	function update ($new_instance, $old_instance) {
		return $new_instance;
	}

	/**
	 *
	 * @param <type> $instance
	 */
	function form ($instance) {
		$title = '';
		if (!empty ($instance)) {
			$title = esc_attr ($instance['title']);
		}
?>
		<p><label for="<?php echo $this->get_field_id ('title'); ?>"><?php _e('Title:', BM_THEMENAME); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
<?php
	}
	
}


/**
 * 
 */
class bm_widget_rssFeeds extends WP_Widget {

	/**
	 *
	 */
	function bm_widget_rssFeeds () {
		parent::WP_Widget(false, 'Subscribe to feed links');
	}

	/**
	 *
	 * @param array $args
	 * @param <type> $instance
	 */
	function widget ($args, $instance) {
		$args['link_text'] = $instance['link_text'];
		bm_rssFeeds ($args);
	}

	/**
	 *
	 * @param <type> $new_instance
	 * @param <type> $old_instance
	 * @return <type>
	 */
	function update ($new_instance, $old_instance) {
		return $new_instance;
	}

	/**
	 *
	 * @param <type> $instance
	 */
	function form ($instance) {
		$link_text = '';
		if (!empty ($instance)) {
			$link_text = esc_attr ($instance['link_text']);
		}
?>
		<p><label for="<?php echo $this->get_field_id ('link_text'); ?>"><?php _e('Feed link Text:', BM_THEMENAME); ?> <input class="widefat" id="<?php echo $this->get_field_id('link_text'); ?>" name="<?php echo $this->get_field_name('link_text'); ?>" type="text" value="<?php echo $link_text; ?>" /></label></p>
<?php
	}
	
}


/**
 * 
 */
class bm_widget_categoryPosts extends WP_Widget {

	/**
	 *
	 */
	function bm_widget_categoryPosts () {
		parent::WP_Widget(false, 'More from this category');
	}

	/**
	 *
	 * @param array $args
	 * @param <type> $instance
	 */
	function widget ($args, $instance) {
		$args['title'] = $instance['title'];
		bm_categoryPosts ($args);
	}

	/**
	 *
	 * @param <type> $new_instance
	 * @param <type> $old_instance
	 * @return <type>
	 */
	function update ($new_instance, $old_instance) {
		return $new_instance;
	}

	/**
	 *
	 * @param <type> $instance
	 */
	function form( $instance) {
		$title = '';
		if (!empty ($instance)) {
			$title = esc_attr ($instance['title']);
		}
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', BM_THEMENAME); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
<?php
	}
	
}


/**
 * 
 */
class bm_widget_authorPosts extends WP_Widget {

	/**
	 *
	 */
	function bm_widget_authorPosts () {
		parent::WP_Widget (false, 'More from this author');
	}

	/**
	 *
	 * @param array $args
	 * @param <type> $instance
	 */
	function widget ($args, $instance) {
		$args['title'] = $instance['title'];
		bm_authorPosts ($args);
	}

	/**
	 *
	 * @param <type> $new_instance
	 * @param <type> $old_instance
	 * @return <type>
	 */
	function update ($new_instance, $old_instance) {
		return $new_instance;
	}

	/**
	 *
	 * @param <type> $instance
	 */
	function form ($instance) {
		$title = '';
		if (!empty ($instance)) {
			$title = esc_attr ($instance['title']);
		}
?>
		<p><label for="<?php echo $this->get_field_id ('title'); ?>"><?php _e('Title:', BM_THEMENAME); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
<?php
	}
	
}


/**
 *
 */
class bm_widget_googleMaps extends WP_Widget {

	/**
	 *
	 */
	function bm_widget_googleMaps() {
		parent::WP_Widget(false, 'Map your location');
	}

	/**
	 *
	 * @param <type> $args
	 * @param <type> $instance
	 */
	function widget($args, $instance) {
		$args['title'] = $instance['title'];
		$args['location'] = $instance['location'];
		$args['zoomLevel'] = $instance['zoomLevel'];
		$args['mapType'] = $instance['mapType'];
		bm_googleMaps ($args);
	}

	/**
	 *
	 * @param <type> $new_instance
	 * @param <type> $old_instance
	 * @return <type>
	 */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/**
	 *
	 * @param <type> $instance
	 */
	function form ($instance) {

		$title = '';
		$location = '';
		$zoomLevel = 14;
		$mapType = 'HYBRID';
		
		if (!empty ($instance)) {
			$title = esc_attr ($instance['title']);
			$location = esc_attr ($instance['location']);
			$zoomLevel = esc_attr ($instance['zoomLevel']);
			$mapType = esc_attr ($instance['mapType']);
		}
		
		$zoomLevels = array (
			array('1', '1 - from space'),
			array('2', '2'),
			array('3', '3'),
			array('4', '4'),
			array('5', '5'),
			array('6', '6'),
			array('7', '7'),
			array('8', '8'),
			array('9', '9'),
			array('10', '10'),
			array('11', '11'),
			array('12', '12'),
			array('13', '13'),
			array('14', '14'),
			array('15', '15'),
			array('16', '16'),
			array('17', '17'),
			array('18', '18 - really close up'),
		);
		$mapTypes = array (
			array('ROADMAP','Roadmap'),
			array('SATELLITE','Satellite'),
			array('HYBRID','Satellite and Road'),
			array('TERRAIN','Terrain'),
		);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', BM_THEMENAME); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		
		<p><label for="<?php echo $this->get_field_id('location'); ?>"><?php _e('Location:', BM_THEMENAME); ?> <input class="widefat" id="<?php echo $this->get_field_id('location'); ?>" name="<?php echo $this->get_field_name('location'); ?>" type="text" value="<?php echo $location; ?>" /></label></p>
		
		<p><label for="<?php echo $this->get_field_id('zoomLevel'); ?>"><?php _e('Zoom Amount:', BM_THEMENAME); ?>
			<select class="widefat" id="<?php echo $this->get_field_id('zoomLevel'); ?>" name="<?php echo $this->get_field_name('zoomLevel'); ?>">
<?php	
		foreach ($zoomLevels as $z) {
			$select = '';
			if ($z[0] == $zoomLevel) {
				$select = ' selected="true"';
			}
?>
				<option value="<?php echo $z[0]; ?>"<?php echo $select; ?>><?php echo $z[1]; ?></option>
<?php
		}
?>
			</select></label>
		</p>
		<p><label for="<?php echo $this->get_field_id('mapType'); ?>"><?php _e('Type of Map view:', BM_THEMENAME); ?>
			<select class="widefat" id="<?php echo $this->get_field_id('mapType'); ?>" name="<?php echo $this->get_field_name('mapType'); ?>">
<?php	
		foreach ($mapTypes as $m) {
			$select = '';
			if ($m[0] == $mapType) {
				$select = ' selected="true"';
			}
?>
				<option value="<?php echo $m[0]; ?>" <?php echo $select; ?>><?php echo $m[1]; ?></option>
<?php
		}
?>
			</select></label>
		</p>
<?php
	}
	
}


/**
 * 
 */
class bm_widget_postDetails extends WP_Widget {

	/**
	 *
	 */
	function bm_widget_postDetails() {
		parent::WP_Widget(false, 'Post Author Details');
	}

	/**
	 *
	 * @param <type> $args
	 * @param <type> $instance
	 */
	function widget($args, $instance) {
		
		$display = false;

		if (empty ($instance['display'])) {
			$instance['display'] = -1;
		}
		
		switch ($instance['display']) {
			// post
			case 0:
				if (is_single () || is_attachment ()) {
					$display = true;
				}
				break;
				
			// page
			case 2:
				if (is_page ()) {
					$display = true;
				}
				break;
			
			// everywhere
			case 1:			
			default:
				$display = true;
				
				break;
		}
		
		if ($display) {
			$args['title'] = $instance['title'];
			$args['showDate'] = $instance['showDate'];
			bm_postDetails ($args);
		}
		
	}

	/**
	 *
	 * @param <type> $new_instance
	 * @param <type> $old_instance
	 * @return <type>
	 */
	function update($new_instance, $old_instance) {
		$new_instance['showDate'] = (bool) $new_instance['showDate'];
		$new_instance['display'] = (int) $new_instance['display'];
		return $new_instance;
	}

	/**
	 *
	 * @param <type> $instance 
	 */
	function form($instance) {
		$title = '';
		$showDate = TRUE;
		$showDateExtra = '';
		$display = 0;

		if (!empty ($instance)) {
			$title = esc_attr ($instance['title']);
			$showDate = esc_attr($instance['showDate']);
			$display = esc_attr($instance['display']);
		}

		if ($showDate == true || $showDate == 1) {
			$showDateExtra = 'checked="true"';
		}
		
		$displayValue = array (
			array (0, __('Posts', BM_THEMENAME)),
			array (1, __('Everywhere', BM_THEMENAME)),
			array (2, __('Pages', BM_THEMENAME)),
		);
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', BM_THEMENAME); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id('showDate'); ?>"><?php _e('Display date:', BM_THEMENAME); ?> <input type="checkbox" id="<?php echo $this->get_field_id('showDate'); ?>" name="<?php echo $this->get_field_name('showDate'); ?>" <?php echo $showDateExtra; ?> /></label></p>
		<p><label for="<?php echo $this->get_field_id('display'); ?>"><?php _e('Display on:', BM_THEMENAME); ?><?php bm_select($this->get_field_name('display'), $displayValue, $display); ?></label></p>
<?php
	}
	
}


/**
 * 
 */
class bm_widget_sharePost extends WP_Widget {

	/**
	 *
	 */
	function bm_widget_sharePost() {
		parent::WP_Widget(false, 'Share This');
	}

	/**
	 *
	 * @param array $args
	 * @param <type> $instance
	 */
	function widget($args, $instance) {
		$args['title'] = $instance['title'];
		bm_sharePost ($args);
	}

	/**
	 *
	 * @param <type> $new_instance
	 * @param <type> $old_instance
	 * @return <type>
	 */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/**
	 *
	 * @param <type> $instance
	 */
	function form($instance) {
		$title = '';
		if (!empty ($instance)) {
			$title = esc_attr ($instance['title']);
		}
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', BM_THEMENAME); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
<?php
	}
	
}


/**
 * 
 */
class bm_widget_twitter extends WP_Widget {

	/**
	 *
	 */
	function bm_widget_twitter() {
		parent::WP_Widget(false, 'Twitter updates');
	}

	/**
	 *
	 * @param <type> $args
	 * @param <type> $instance
	 */
	function widget($args, $instance) {

		$args['username'] = $instance['username'];

		if ($args['username'] != '') {
			$args['title'] = $instance['title'] . ' [ <a href="http://twitter.com/' . $instance['username'] . '" alt="Tweets for ' . $instance['username'] . '">&rsaquo;</a> ]';
			$args['count'] = $instance['count'];
			
			bm_twitter ($args);
		}
		
	}

	/**
	 *
	 * @param <type> $new_instance
	 * @param <type> $old_instance
	 * @return <type>
	 */
	function update($new_instance, $old_instance) {

		$new_instance['count'] = (int) esc_attr($new_instance['count']);
		
		if ($new_instance['count'] < 0 || $new_instance['count'] == '') {
			$new_instance['count'] = 1;
		}
		if ($new_instance['count'] > 10) {
			$new_instance['count'] = 10;
		}

		$new_instance['username'] = str_replace('http://twitter.com/', '', $new_instance['username']);
		$new_instance['username'] = str_replace('@', '', $new_instance['username']);
		$new_instance['username'] = str_replace('/', '', $new_instance['username']);
		
		return $new_instance;
		
	}

	/**
	 *
	 * @param <type> $instance
	 */
	function form($instance) {
	
		$instance = wp_parse_args((array) $instance, array('username' => '', 'count' => 5, 'title' => ''));		
		$username = esc_attr($instance['username']);
		$count = esc_attr($instance['count']);
		$title = esc_attr($instance['title']);
?>
	<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', BM_THEMENAME); ?><input class="widefat" id="<?php echo $this->get_field_name('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
	
	<p><label for="<?php echo $this->get_field_id('username'); ?>"><?php _e('Twitter username:', BM_THEMENAME); ?><input name="<?php echo $this->get_field_name('username'); ?>" id="<?php echo $this->get_field_id('username'); ?>" type="text" value="<?php echo $username; ?>" class="widefat" /></label></p>
	
	<p><label for="<?php echo $this->get_field_id('count'); ?>"><?php _e('Number of items to display:', BM_THEMENAME); ?><input name="<?php echo $this->get_field_name('count'); ?>" id="<?php echo $this->get_field_id('count'); ?>" type="text" value="<?php echo $count; ?>" class="widefat" style="width:35px; text-align:center;" /></label></p>
<?php

	}
	
}


/**
 *
 */
class bm_widget_upcomingPosts extends WP_Widget {

	/**
	 * 
	 */
	function bm_widget_upcomingPosts() {
		parent::WP_Widget(false, 'Upcoming Posts');
	}

	/**
	 *
	 * @param array $args
	 * @param <type> $instance
	 */
	function widget($args, $instance) {
		$args['title'] = $instance['title'];
		bm_upcomingPosts ($args);
	}

	/**
	 *
	 * @param <type> $new_instance
	 * @param <type> $old_instance
	 * @return <type>
	 */
	function update($new_instance, $old_instance) {
		return $new_instance;
	}

	/**
	 *
	 * @param <type> $instance
	 */
	function form($instance) {
		$title = '';
		if (!empty ($instance)) {
			$title = esc_attr ($instance['title']);
		}
?>
		<p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:', BM_THEMENAME); ?> <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
<?php
	}
	
}


/**
 * 
 */
class bm_widget_printPreview extends WP_Widget {

	/**
	 *
	 */
	function bm_widget_printPreview() {
		parent::WP_Widget(false, 'Print Preview');
	}

	/**
	 *
	 * @param <type> $args
	 * @param <type> $instance 
	 */
	function widget($args, $instance) {
		if (is_single()) {
			$args = bm_defaultArgs($args);
			
			extract($args);
			echo $before_widget;
?>
		<a onclick="printPreview();" href="#printpreview">Print Preview</a>
<?php
			echo $after_widget;
		}
	}
	
}


register_widget('bm_widget_popularPosts');
register_widget('bm_widget_printPreview');
register_widget('bm_widget_relatedPosts');
register_widget('bm_widget_categoryPosts');
register_widget('bm_widget_authorPosts');
register_widget('bm_widget_postDetails');
register_widget('bm_widget_sharePost');
register_widget('bm_widget_twitter');
register_widget('bm_widget_upcomingPosts');
register_widget('bm_widget_rssFeeds');
register_widget('bm_widget_googleMaps');