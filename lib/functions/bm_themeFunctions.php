<?php


/**
 * Do head content stuff
 * includes:
 *  - meta content
 *  - load styles
 *  - load scripts
 *  - display page title
 * 
 * @global $post
 * @global $bm_options
 * @global $bm_postMeta
 * @global $wp_query
 * @global type $primaryPostData 
 */
function bm_head () {

	global $post, $bm_options, $bm_postMeta, $wp_query;
	
	// meta tags
	// ---------
	$meta = array();
	$link = array();
	
	$meta['X-UA-Compatible'] = 'chrome=1';

	if (!class_exists ('All_in_One_SEO_Pack')) {

		if (is_single () || is_page ()) {
			if (!empty ($bm_postMeta['seo_description'])) {
				$meta['description'] = $bm_postMeta['seo_description'];
			} else {
				if (have_posts ()) {
					while (have_posts ()) {
						the_post ();
						$meta['description'] = bm_excerpt (25, TRUE, FALSE);
					}
				}
			}
		}
	
	}
	
	if ($bm_options['googleWebmasterTools'] != '') {
		$meta['google-site-verification'] = $bm_options['googleWebmasterTools'];
	}

	$meta['og:site_name'] = get_bloginfo ('name');
	$meta['og:medium'] = 'blog';
	$meta['og:title'] = bm_title (FALSE);
	
	$styles = bm_getStyles ();
	if (isset ($styles['responsive'])) {
		$meta['viewport'] = 'width=device-width initial-scale=1';
	}
	
	if (!empty ($bm_options ['googlePlusPageId'])) {
		$link['publisher'] = 'https://plus.google.com/' . $bm_options['googlePlusPageId'] . '/';		
	}

	// meta tags for facebook
	if (is_single()) {
		$meta['og:url'] = get_permalink ();
		
		$postimage = bm_getPostImage ();
		if ($postimage != '') {
			$link['og:image'] = $postimage['src'];
		}		
	}
	
	$meta = apply_filters ('bm_meta', $meta);
	$link = apply_filters ('bm_link', $link);
	
	foreach ($link as $rel => $href) {
?>
<link rel="<?php echo $rel; ?>" href="<?php echo $href; ?>" />
<?php
	}
	
	foreach ($meta as $name => $content) {
?>
<meta name="<?php echo $name; ?>" content="<?php echo $content; ?>" />
<?php
	}

	// header styles
	$header_styles = array ();
	$header_styles = apply_filters ('bm_headstyles', $header_styles);	
?>
<style type="text/css">
/* <![CDATA[ */
	<?php echo implode ($header_styles, "\n\t"); ?>
/* ]]> */
</style>
<?php

	if (is_single ()) {
		echo '<!-- ';
		trackback_rdf ();
		echo ' -->' . "\n";

		global $primaryPostData;
		$primaryPostData['ID'] = $post->ID;
		$primaryPostData['author'] = $post->post_author;
	}
?>
<link rel="search" type="application/opensearchdescription+xml" href="<?php echo trailingslashit (BM_SITEURL); ?>?opensearch=1" />
<?php

}


/**
 * put together custom styles according to theme parameters
 * 
 * @global array $bm_options
 * @param array $header_styles
 * @return array 
 */
function bm_headingStyles ($header_styles) {

	global $bm_options;

	$headerHeight = HEADER_IMAGE_HEIGHT;
	if (!empty ($bm_options['headerHeight'])) {
		$headerHeight = $bm_options['headerHeight'];
	}

	$header_styles[] = '#header {';
	$header_styles[] = 'height:' . $headerHeight . 'px;';

	if (!empty ($bm_options['headerImageRaw'])) {
		$headerImage = $bm_options['headerImageRaw'];

		$headerImage = BM_BLOGPATH . '/tools/timthumb.php?' . implode ('&', array (
			'src=' . urlencode (bm_muImageUploadPath ($headerImage)),
			'w=' . HEADER_IMAGE_WIDTH,
			'h=' . $headerHeight,
		));

		$header_styles[] = 'background-image: url(' . $headerImage . ');';
	}

	$header_styles[] = '}';

	return $header_styles;

}


/**
 * combine the header styles into something usable
 * 
 * @global array $bm_options
 * @param array $header_styles
 * @return array 
 */
function bm_fontStyles ($header_styles) {

	global $bm_options;

	$header_styles = array_merge ($header_styles, bm_displayFont ($bm_options['font_body'], 'html, body'));
	$header_styles = array_merge ($header_styles, bm_displayFont ($bm_options['font_heading'], 'body h1, body h2, body h3, body h4, body h5, body h6'));

	return $header_styles;

}


/**
 * Load font settings according to theme options
 * Could be a font from Google or a standard web font
 * 
 * @global array $bm_options
 * @param type $font_info
 * @param type $elements
 * @return array
 */
function bm_displayFont ($font_info, $elements) {

	$font = bm_fontSettings ();
	$styles = array ();

	if (isset ($font[$font_info])) {

		$f = $font[$font_info];
		$import = '';
		$font_size = 12;
		
		global $bm_options;
		
		if (!empty ($bm_options['font_size'])) {
			$font_size = (int) $bm_options['font_size'];
		}

		switch ($f['type']) {

			case FONT_GOOGLE:
				$f['family'] = '"' . $f['name'] . '", arial, serif';

				if (isset ($f['weight'])) {
					$f['name'] .= ':' . $f['weight'];
				}
				
				$protocol = is_ssl() ? 'https' : 'http';
				
				$import = '@import url(' . $protocol . '://fonts.googleapis.com/css?family=' . str_replace(' ', '+', $f['name']) . ');';
				break;

			default:
			case FONT_NORMAL:

				break;
		}

		if (!empty ($import)) {
			echo '<style>' . $import . '</style>' . "\n";
		}
		$styles[] = $elements . ' {';
		$styles[] = 'font-size:' . $font_size . 'px;';
		$styles[] = 'font-family:' . $f['family'] . ';';
		$styles[] = '}';
	}

	return $styles;
}


/**
 * Decide which css stylesheets to add to the page
 * 
 * @global $bm_options $bm_options
 * @return array
 */
function bm_getStyles () {

	global $bm_options;

	$styles = array (
		'template' => 'lib/styles/styles.css',
		'theme' => 'layout.css',
		'responsive' => 'lib/styles/responsive.css',
		'default' => 'style.css',
	);

	if (bm_locateFile ('custom/style.css') != '') {
		$styles['custom_style'] = 'custom/style.css';
	}

	if (!empty ($bm_options['skinStyle'])) {
		$styles['custom_skin'] = $bm_options['skinStyle'];
	}
	
	if (bm_is_bbPressPage ()) {
		$styles['bbpress'] = 'lib/styles/bbpress.css';
	}
	
	return apply_filters ('bm_displayStyles', $styles);

}


/**
 * Apply site styles including the default filtered styles
 * styles can be modified using filter: bm_displayStyles
 *
 * @global <type> $bm_options 
 */
function bm_displayStyles () {

	global $bm_options;

	$styles = bm_getStyles ();

	foreach ($styles as $s) {
		if ($s != '') {
			$filepath = bm_filePath ($s);
?>
	<link href="<?php echo $filepath . '?' . BM_THEMEVERSION; ?>" type="text/css" media="screen" rel="stylesheet" title="default" />
<?php
		}
	}
?>
	<link href="<?php echo BM_BLOGPATH; ?>/print.css" type="text/css" media="screen" rel="alternate stylesheet" title="Print Preview" />
<?php

}


/**
 * Get website statistics
 *
 * @global object $wpdb wordpress database object
 * @return array
 */
function bm_siteStats() {

	global $wpdb;

	// total posts
	$stats["post"] = $wpdb->get_var('SELECT COUNT(*) FROM $wpdb->posts WHERE post_status = "publish"');
	
	// comment count
	$stats['comment'] = $wpdb->get_var('SELECT COUNT(*) FROM $wpdb->comments WHERE comment_approved = "1"');
	
	// average comment count
	$stats['averageComments'] = round($stats['post'] / $stats['comment']);
	
	// trackback count
	$stats['trackback'] = $wpdb->get_var('SELECT COUNT(*) FROM $wpdb->comments WHERE comment_type = "pingback" OR comment_type = "trackback"');
	
	// akismet spams captured
	if(function_exists('akismet_count')) {
		$stats['akismet'] = akismet_count();
	}
	
	$stats = apply_filters('bm_siteStats', $stats);
	
	return $stats;

}


/**
 * Do footer content stuff
 *
 * @global array $bm_options global theme options
 */
function bm_footer () {

	global $bm_options;
	
?>
	<script type="text/javascript">
	/* <![CDATA[ */
		jQuery.noConflict ();
		
		jQuery (document).ready (function () {

			jQuery ('.toggle').click (function () {
				target = jQuery (this).attr ('target');
				jQuery (target).slideToggle (100);
			});

			jQuery ('#cat').change (function () {
				location.href = '<?php bm_homeLink(); ?>/?cat=' + jQuery (this).val ();
			});

			jQuery ('li.cat-item a, li.page_item a').each (function (){
				jQuery (this).removeAttr ('title');
			});

			jQuery ('textarea.htmlEditor').each (function () {
				edWrite (jQuery (this));
			});
			
			var commentsSpan = jQuery ('span.actions, span.commentDate').hide ();
			var commentsHoverTimeout;
			var actionsSpan;

			jQuery('#comments .commentList li').bind ('mouseover mouseleave', function (event) {
				event.stopPropagation ();
				clearTimeout (commentsHoverTimeout);
				actionsSpan = jQuery (this).find ('span.actions:first, span.commentDate:first');
				commentsSpan.not (actionsSpan).fadeOut (70);
				if (event.type == 'mouseover') {
					commentsHoverTimeout = setTimeout (function () {
						actionsSpan.stop (true, true).fadeIn (150);
					}, 250);
				} else {
					actionsSpan.stop (true, true).fadeOut (70);
				}
			});
			
<?php
	if (!is_home ()) {
?>
			jQuery ('#header').click (function() {
				location.href = '<?php bm_homeLink (); ?>';
			});
<?php
	}
	
	if (bm_is_bbPressPage ()) {
?>
			jQuery ('table.bbp-replies tr.bbp-reply-header:even').addClass ('odd');
<?php
	}
?>
		
			if (location.hash == '#printpreview') {
				printPreview ();
			}

<?php
	// Twitter Anywhere
	if ($bm_options['TwitterAnywhere'] != '') {
?>
			twttr.anywhere (function (T) {
				T ('#comments .commentList').linkifyUsers ();
				T ('#comments a.twitter_anywhere').hovercards ({
					infer: true
				});
			});
			
<?php
	}
	
	$scripts = bm_scriptSettings ();
	// make sure the superfish js is still being included
	if (isset ($scripts['superfish'])) {

		$dropDownSettings = array (
			'animation' => '{opacity:"show", height:"show"}',
			'speed' => '140',
			'dropShadows' => 'false',
		);

		$dropDownSettings = apply_filters ('bm_dropDownSettings', $dropDownSettings);

		$settings = array();
		foreach ($dropDownSettings as $k => $v) {
			$settings[] = "\t\t\t\t" . $k . ':' . $v;
		}

		$settingsContent = implode (",\n", $settings);
?>
			jQuery ('ul.nav').superfish({
<?php
		echo $settingsContent . "\n";
?>
			});
<?php
	}

	bm_doAction ('bm_documentReady');
?>
		});

<?php
	if ($bm_options['googleAnalytics'] != '') {

		$pushVars = array();

		// calculate analytics properties
		// page type
		$post_type = '';
		if (is_home () || is_front_page ()) {
			$post_type = 'home';
		} else if (is_archive ()) {
			$post_type = 'archive';
		} else if (is_search()) {
			$post_type = 'search';
		} else if (is_404 ()) {
			$post_type = '404';
		} else {
			$post_type = get_post_type ();
		}
		
		if ($post_type != '') {
			$pushVars[] = "['_setCustomVar', 1, 'post_type', '" . $post_type . "', 3]";
		}

		// category
		if (is_singular () && !is_front_page ()) {
			$cats = get_the_category ();
			if (isset ($cats[0])) {
				$pushVars[] = "['_setCustomVar', 2, 'category', '" . $cats[0]->slug . "', 3]";
			}
		}
?>
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', '<?php echo $bm_options['googleAnalytics']; ?>']);
		_gaq.push(<?php echo implode ($pushVars, ','); ?>);
		_gaq.push(['_trackPageview']);
		_gaq.push(['_trackPageLoadTime']);

		(function() {
			var ga = document.createElement('script');
			ga.type = 'text/javascript';
			ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0];
			s.parentNode.insertBefore(ga, s);
		})();
		
<?php
	}
?>

	/* ]]> */
	</script>
<?php
	
	if (BM_DEBUG) {
		bm_listHooks();
	}
}


/**
 * Insert adsense into a page
 * Limits ads to 3 per page (as per Adsense T&C)
 *
 * @param int $width width of the adsense unit
 * @param int $height height of the adsense unit
 * @param array $colours array of colours to use to decorate the unit
 * @param string $adsensePublisher Users publisher ID
 */
function bm_insertAdsense ($width = 300, $height = 250, $colours = array(), $adsensePublisher = '') {

	// calculate width based upon widget block size
	// load adsense id
	// embed adsense content
	// calculate number of ads and only display if less than allowed		
	
	global $bm_options, $bm_adsenseCount;

	if (isset ($bm_options['adsensePublisher'])) {
		$adsensePublisher = $bm_options['adsensePublisher'];
	}
	
	if (empty ($adsensePublisher)) {
		return FALSE;
	}

	$adsensePublisher = str_replace ('pub-', '', $adsensePublisher);
	
	$bm_adsenseCount ++;
	
	if ($bm_adsenseCount > 3) {
		return FALSE;
	}
	
	if ($colours == array ()) {
		$colours = array(
			'border' => 'ffffff',
			'bg' => 'ffffff',
			'link' => '2F3A47',
			'text' => '333333',
			'url' => '999999',
		);
	}

?>
	<div class="adsenseBlock no-print">
		<script type="text/javascript">
		<!--
			google_ad_client = "pub-<?php echo $adsensePublisher; ?>";
			google_ad_width = <?php echo $width; ?>;
			google_ad_height = <?php echo $height; ?>;
			google_ad_format = "<?php echo $width . 'x' . $height; ?>_as";
			google_ad_type = "text_image";
			google_ad_channel ="";
			google_color_border = "<?php echo $colours['border']; ?>";
			google_color_bg = "<?php echo $colours['bg']; ?>";
			google_color_link = "<?php echo $colours['link']; ?>";
			google_color_text = "<?php echo $colours['text']; ?>";
			google_color_url = "<?php echo $colours['url']; ?>";
		//-->
		</script>
		<script type="text/javascript" src="http://pagead2.googlesyndication.com/pagead/show_ads.js"></script>	
	</div>
<?php
	
}


/**
 * Custom loop function which uses templates to save from repeating the same code over and over
 *
 * @param string $template the template to use to display the loop contet. Should be placed in the elemental/includes directory
 * @param string $query a custom wordpress query. Leave blank to show the default wordpress content
 * @param boolean $ignore a flag to label whether the post should be displayed multiple times on the page or not
 * @return boolean was the operation successful or not?
 */
function bm_loop ($template = '', $query = '', $ignore = TRUE, $before = '', $after = '', $properties = array ()) {

	global $bm_options, $wp_query;
	
	if ($template == '') {
		$template = 'index';
	}

	$template = apply_filters ('bm_loopTemplate', bm_locateTemplate ($template));
	
	if ($template != '') {
	
		$returnVal = FALSE;
		$count = 0;
		$customCount = 0;
		
		if ($query == '') {
			$bmWp = $wp_query;
		} else {
			$bmWp = new WP_Query ($query);
		}
		
		echo $before;

		if ($bmWp->have_posts ()) {
			while ($bmWp->have_posts ()) {
				$count ++;
				$postExtraClass = 'postCount-' . $count . ' postIsOdd-' . bm_isOdd ($count);
				$lastPost = FALSE;

				if ($count == $bmWp->post_count) {
					$lastPost = TRUE;
				}

				$bmWp->the_post ();
				
				if ($ignore == TRUE) {
					bm_ignorePost ($bmWp->post->ID);
				}
				include ($template);
			}

			$returnVal = $bmWp;
		}
			
		echo $after;
		
	}
	
	return $returnVal;	
	
}


/**
 * Display a custom image archive
 */
function bm_displayImageArchive () {

	$args = array (
		'showposts' => 12,
		'post_type' => 'attachment',
		'post_mime_type' => 'image',
		'post_status' => 'inherit',
	);

	$result = bm_loop ('gallery', bm_pagedQuery($args));

	echo '<div class="column span-' . bm_width ('content', 0, FALSE) . '">';
	bm_numberedPages (9, $result);
	echo '</div>';

}


/**
 *
 * @global <type> $wp_query
 * @param <type> $defaultQuery
 * @return <type>
 */
function bm_pagedQuery ($defaultQuery = 'showposts=10') {

	global $wp_query;

	if (is_array($defaultQuery)) {
		$newQuery = array();
		foreach ($defaultQuery as $k => $v) {
			$newQuery[] = $k . '=' . $v;
		}
		$defaultQuery = implode('&', $newQuery);
	}

	$query = '';

	if (!empty($wp_query->queried_object)) {
		$query = get_post_meta ($wp_query->queried_object->ID, 'query', true);
	}

	if ($query == '') {
		$query = $defaultQuery;
	}

	if (empty($wp_query->query_vars ['paged'])) {
		if (!empty($wp_query->query_vars ['page'])) {
			$paged = (int) $wp_query->query_vars ['page'];
		}
	} else {
		$paged = (int) $wp_query->query_vars ['paged'];
	}
	
	if (empty($paged) || $paged == 0) {
		$paged = 1;
	}
	
	$query .= '&paged=' . $paged;
	
	return $query;
	
}


/**
 * Action to perform before any content is displayed
 */
function bm_preContent() {
	
	bm_doAction('bm_preContent');
	
}


/**
 *
 * @global <type> $template
 */
function bm_wordpressTemplate() {

	global $template;
	echo basename($template, '.php');
	
}


/**
 * load a template
 * uses custom filter 'bm_loadTemplate' to allow name modification
 *
 * @param string $template name of template file to load
 */
function bm_loadTemplate ($template) {

	$template = apply_filters('bm_loadTemplate_' . $template, bm_locateTemplate ($template));

	if ($template != '') {
		include ($template);
	}
	
}


/**
 * locate a template file (check in templates directory)
 * 
 * @param string $template name of template file to load
 */
function bm_locateTemplate ($template, $includePath = 'includes/', $includeExtension = '.php') {

	$template = str_replace ($includeExtension, '', $template);
	return locate_template (array ($includePath . $template . $includeExtension));

}


/**
 * locate a file (check in templates directory)
 * 
 * @param string $filename name of file to find
 * @return <type>
 */
function bm_locateFile ($filename) {

	$located = '';

	if (file_exists (STYLESHEETPATH . '/' . $filename)) {
		$located = STYLESHEETPATH . '/' . $filename;
	} else if (file_exists (TEMPLATEPATH . '/' . $filename)) {
		$located = TEMPLATEPATH . '/' . $filename;
	}

	return $located;

}


/**
 *
 * @param <type> $filename
 * @return <type>
 */
function bm_filePath ($filename) {

	$located = '';

	if (file_exists (STYLESHEETPATH . '/' . $filename)) {
		$located = get_stylesheet_directory_uri() . '/' . $filename;
	} else if (file_exists (TEMPLATEPATH . '/' . $filename)) {
		$located = BM_BLOGPATH . '/' . $filename;
	}

	return $located;

}


/**
 * Get details for search results page
 *
 * @global <type> $posts_per_page
 * @global <type> $paged
 * @global <type> $wp_query
 * @return <type>
 */
function bm_searchresults () {

	if (is_search()) {
		
		global $posts_per_page, $paged;
		global $wp_query;
		
		$numposts = $wp_query->found_posts;
		
		if (empty ($paged)) {
			$paged = 1;
		}
		
		$result['start'] = ($posts_per_page * $paged) - $posts_per_page + 1;
		$result['numpages'] = ceil ($numposts / $posts_per_page);		
		$result['searchTerms'] = $wp_query->query_vars['search_terms'];
		$result['query'] = $wp_query->query_vars['s'];
		$result['searchTermsLinks'] = '';
		
		// search terms
		if (count ($result['searchTerms']) >= 1) {
			
			$tempTerms = array ();
			foreach ($result['searchTerms'] as $st) {
				$tempTerms[] = '<a href="' . BM_SITEURL . '/?s=' . urlencode ($st) . '">' . $st . '</a>';
			}
			
			$result['searchTermsLinks'] = implode (', ', $tempTerms);
			
		}
		
		$result = apply_filters ('bm_searchResults', $result);
		
		return $result;
		
	}
	
	return FALSE;

}


/**
 * work out proper page heading fot SEO purposes
 *
 */
function bm_pageHeading() {
	
	if (is_home () && !is_paged ()) {
		$heading = '<h1>' . get_bloginfo ('name') . '</h1>';
	} else {
		$heading = '<h3><a href="' . bm_homeLink (false) . '">' . get_bloginfo ('name') . '</a></h3>';
	}
	
	$heading = apply_filters ('bm_siteHeading', $heading);
	
	echo $heading;
	
}

/**
 * Calculate breadcrumbs for any page on the site
 *
 * @param boolean $display display or return the results as an array?
 * @param string $seperator the text to place between the crumbs when returned as a string
 * @return array list of name, url pairs to use to create breadcrumbs
 */
function bm_simpleBreadcrumbs ($display = TRUE, $separator = '<b>&rsaquo;</b>', $before = '<p id="breadcrumbs" itemprop="breadcrumb">', $after = '</p>') {

	// don't display breadcrumbs on the homepage
	if (is_front_page ()) {
		return false;
	}
	
	global $wp_query, $post, $bm_crumbLinks, $bm_postMeta;	

	if (isset ($post) && is_single ()) {
		// if possible get breadcrumbs from cache
		$crumbs = wp_cache_get ($post->ID, 'bm_breadcrumbs');
	}


	// no breadcrumbs in cache so generate them
	if (empty ($crumbs)) {
		
		// always a home link
		$bm_crumbLinks[] = array (
			__('Home', BM_THEMENAME),
			bm_homeLink (false)
		);

		// add breadcrumbs for custom post types
		$post_type = get_post_type ($post);
		
		// bbPress
		if (bm_is_bbPressPage ()) {
			
			// Forum archive
			$bm_crumbLinks[] = array (
				bbp_get_forum_archive_title (),
				get_post_type_archive_link (bbp_get_forum_post_type ()),
			);
			
			if (bbp_is_single_topic ()) {
				$bm_crumbLinks[] = array (
					bbp_get_forum_title (),
					bbp_get_forum_permalink (),
				);
			}
			
			//var_dump (bbp_is_single_topic());
			
		// other custom post types
		} else if (!in_array ($post_type, array ('post', 'page', 'attachment', ''))) {
			$post_type_object = get_post_type_object ($post_type);
			if (isset ($post_type_object->labels->name)) {
				$bm_crumbLinks[] = array (
					$post_type_object->labels->name,
					get_post_type_archive_link ($post_type),
				);
			}
		}
		
		// is it the front page?
		if (is_home () || is_front_page ()) {
		
			// turn off url on home link to keep it as text
			$links[0][1] = '';
		
		// a page?
		} else if (is_page ()) {
		
			$post = $wp_query->get_queried_object ();
			if ($post->post_parent == 0) {
			
				$bm_crumbLinks[] = array(
					get_the_title (),
					get_permalink (),
				);

			} else {
			
				// Reverse the order so it's oldest to newest
				if (isset ($post->ancestors)) {
					$ancestors = array_reverse ($post->ancestors);
				} else {
					$ancestors[] = $post->post_parent;
				}

				// Add the current Page to the ancestors list (as we need it's title too)
				$ancestors[] = $post->ID;

				foreach ($ancestors as $ancestor) {
					$bm_crumbLinks[] = array (
						strip_tags (get_the_title ($ancestor)),
						get_permalink ($ancestor),
					);
				}
				
			}
			
		// anything else?
		} else {
		
			if (is_attachment ()) {
				$bm_crumbLinks[] = array(
					get_the_title ($post->post_parent),
					get_permalink ($post->post_parent),
				);
			} else if (is_single ()) {
				$cats = get_the_category ();
				if (isset ($cats[0])) {
					$cat = $cats[0];
					bm_get_category_parents ($cat->term_id);
				}
			}

			if (is_category ()) {
				$cat = (int) get_query_var ('cat');
				bm_get_category_parents ($cat);
			} else if (is_tag ()) {
				$bm_crumbLinks[] = array (
					single_cat_title ('', FALSE),
				);
			} elseif (is_date ()) {
				$day = (int) $wp_query->query_vars['day'];
				$month = (int) $wp_query->query_vars['monthnum'];
				$year = (int) $wp_query->query_vars['year'];

				if ($month != 0) {
					$title = single_month_title (' ', FALSE);
				} else {
					$title = $year;
				}
				
				$bm_crumbLinks[] = array (
					$title,
					bm_getDateArchiveLink ($year, $month, $day),
				);
			} elseif (is_post_type_archive ()) {
				// do nothing - it was taken care of earlier
			} elseif (is_author ()) {
				$curauth = $wp_query->get_queried_object ();
				$bm_crumbLinks[] = $curauth->display_name;
			} elseif (is_search ()) {
				$bm_crumbLinks[] = sprintf (__('Search : ', BM_THEMENAME), get_search_query ());
			} elseif (is_404 ()) {
				$bm_crumbLinks[] = __('404 Page not found', BM_THEMENAME);
			} else {
				$title = get_the_title ();
				if (!empty ($bm_postMeta['seo_breadcrumbTitle'])) {
					$title = $bm_postMeta['seo_breadcrumbTitle'];
				}
				$bm_crumbLinks[] = array (
					$title,
					get_permalink(),
				);
			}
			
		}
		
		if (!empty ($wp_query->query_vars['paged'])) {
			$bm_crumbLinks[] = sprintf (__('Page %d', BM_THEMENAME), $wp_query->query_vars['paged']);
		}

		if (!empty ($wp_query->query_vars['page'])) {
			$bm_crumbLinks[] = sprintf (__('Page %d', BM_THEMENAME), $wp_query->query_vars['page']);
		}
		
		$bm_crumbLinks = apply_filters ('bm_crumbLinks', $bm_crumbLinks);

		$count = 0;
		$crumbs = array ();
		
		foreach ($bm_crumbLinks as $link) {
			$count ++;
			$link = (array) $link;
			
			$htmlClass = 'breadcrumbLevel_' . $count;
			
			if ($link[0] != '') {
				if ($count != count ($bm_crumbLinks)) {
					$crumbs[] = '<a href="' . $link[1] . '" class="' . $htmlClass . '">' . $link[0] . '</a>';
				} else {
					$crumbs[] = '<strong class="' . $htmlClass . '">' . $link[0] . '</strong>';
				}
			}
		}

		if (isset ($post) && is_single ()) {
			wp_cache_add ($post->ID, $crumbs, 'bm_breadcrumbs');
		}

	}

	// flip stuff for localisation (rtl specifically)
	$separator = '<b>&rsaquo;</b>';
	if ( is_rtl() ) {
		$separator = '<b>&lsaquo;</b>';
		$crumbs = array_reverse( $crumbs );
	}

	if ($crumbs) {
		if ($display) {
			echo $before . implode (' ' . $separator . ' ', $crumbs) . $after;
		} else {
			return $crumbs;
		}
	}

	return FALSE;

}


/**
 *
 * @global <type> $wp_query
 * @global <type> $bm_options
 * @param <type> $before
 * @param <type> $after 
 */
function bm_categoryDescription ($before = '<div id="categoryDescription">', $after = '</div>') {

	global $wp_query, $bm_options;

	if ($bm_options['displayChildCategories'] == 1) {
		if (is_category () && $wp_query->query_vars['paged'] <= 1) {
		
			$description = category_description ();
			
			$args = array (
				'style'              => 'none',
				'use_desc_for_title' => 1,
				'child_of'           => $wp_query->queried_object->term_id,
				'current_category'   => 1,
				'title_li'           => '',
				'number'             => NULL,
				'echo'               => 0,
				'depth'              => -1,
			);
			
			$categoryJump = wp_list_categories ($args);
			
			if ($categoryJump == __('No categories', BM_THEMENAME)) {
				$categoryJump = '';
			}
			
			if ($description != '' || $categoryJump != '') {
			
				echo $before;
							
				if ($description != '') {
					echo $description;
				}
				
				if ($categoryJump != '') {
					$categories = explode ('<br />', $categoryJump);
					array_pop ($categories);
					if (count ($categories) > 1) {
						$last = array_pop ($categories);
						$categoryJump = implode (', ', $categories) . ' ' . __('and', BM_THEMENAME) . ' ' . $last;
					} else {
						$categoryJump = implode (', ', $categories);
					}
					echo '<p class="subCategories">' . single_cat_title ('', false) . ' ' . __('sub categories', BM_THEMENAME) . ' : ' . $categoryJump . '</p>';
				}
				
				echo $after;
			
			}
		}
	}
	
}


/**
 *
 * @global <type> $bm_options
 * @param <type> $position
 */
function bm_navigation ($position) {

	global $bm_options;
	
	bm_doAction ('bm_navigationBefore' . ucfirst ($position));
	
	if (isset ($bm_options[$position])) {
		switch ((string) $bm_options[$position]) {

			// page
			case '1':
				bm_headerPageNavigation ();
				break;

			// category
			case '2':
				bm_headerCategoryNavigation ();
				break;

			case '0':
				break;

			default:
				if (!empty ($bm_options[$position])) {
					$args = array (
						'menu' => $bm_options[$position],
						'menu_class' => 'nav',
					);
					wp_nav_menu ($args);
				}
				break;

		}
	}
	
	bm_doAction ('bm_navigationAfter' . ucfirst ($position));

}


/**
 * Display Social bookmarking links
 * sites include
 * - Twitter
 * - StumbleUpon
 * - Digg
 * - Delicious
 * - facebook
 *
 * @global <type> $post
 * @global <type> $bm_options
 * @return <type>
 */
function bm_socialLinks() {

	global $post, $bm_options;

	$socialLinks = array(
		array(
			'class' => 'twitter',
			'name' => __('Twitter', BM_THEMENAME),
			'path' => 'http://twitter.com/home?status=' . urlencode (__('Currently reading', BM_THEMENAME)) . '+[THE_TINYTITLE]+-+[THE_SHORT_PERMALINK]',
		),
		
		array(
			'class' => 'stumble',
			'name' => __('Stumble It', BM_THEMENAME),
			'path' => 'http://www.stumbleupon.com/submit?url=[THE_PERMALINK]',
		),

		array(
			'class' => 'digg',
			'name' => __('Digg', BM_THEMENAME),
			'path' => 'http://digg.com/submit?phase=2&amp;url=[THE_PERMALINK]&amp;title=[THE_TITLE]',
		),

		array(
			'class' => 'facebook',
			'name' => __('Facebook', BM_THEMENAME),
			'path' => 'http://www.facebook.com/sharer.php?u=[THE_SHORT_PERMALINK]&amp;t=[THE_TITLE]',
		),

	);
	
	$socialLinks = apply_filters ('bm_socialLinks', $socialLinks);
	$callback = apply_filters ('bm_shortUrl', 'bm_getTinyUrl');
	
	if (is_single () && !empty ($post)) {
	
		$insertPermalink = urlencode (get_permalink ($post->ID));
		$insertShortPermalink = urlencode (call_user_func ($callback, get_permalink ($post->ID)));
		$insertTitle = htmlspecialchars (urlencode (get_the_title ()));
		
	} else {
	
		$insertPermalink = urlencode (bm_homeLink (false));
		$insertShortPermalink = urlencode (call_user_func ($callback, bm_homeLink (false)));
		$insertTitle = htmlspecialchars (urlencode (bm_title (FALSE)));
		
	}
	
	if (strlen ($insertTitle) > 90) {
		$insertTinyTitle = substr ($insertTitle, 0, 90) . ' ...';
	} else {
		$insertTinyTitle = $insertTitle;
	}
	
?>
	<ul class="socialLinks">
<?php
	foreach ($socialLinks as $sl) {
	
		$sl['path'] = str_replace ('[THE_PERMALINK]', $insertPermalink, $sl['path']);
		$sl['path'] = str_replace ('[THE_SHORT_PERMALINK]', $insertShortPermalink, $sl['path']);
		$sl['path'] = str_replace ('[THE_TITLE]', $insertTitle, $sl['path']);
		$sl['path'] = str_replace ('[THE_TINYTITLE]', $insertTinyTitle, $sl['path']);
?>
		<li class="<?php echo strtolower ($sl['class']); ?>">
			<a href="<?php echo $sl['path']; ?>" rel="nofollow" target="_blank"><?php echo $sl['name']; ?></a>
		</li>
<?php	
	}
?>
	</ul>
<?php

	return TRUE;
	
}


/**
 * Display the header navigation
 *
 * @global <type> $bm_options
 * @param <type> $displayHome
 */
function bm_headerPageNavigation ($displayHome = true) {

	global $bm_options;

	// work out what to highlight
	if (is_home ()) {
		$highlight = 'page_item current_page_item';
	} else {
		$highlight = 'page_item';	
	}
	
	// exclude pages from menu
	$bm_query = array (
		'sort_column' => 'menu_order',
		'depth' => '4',
		'title_li' => '',
	);

	if (isset ($bm_options['excludePages'])) {
		$bm_query['exclude'] = $bm_options['excludePages'];
	}
	
	if (isset ($bm_options['includePages'])) {
		$bm_query['include'] = $bm_options['includePages'];
	}
	
	$bm_query = apply_filters ('bm_headerPageNavigation', $bm_query);
?>
	<ul class="nav">
<?php
	if ($bm_options['displayHome'] == 1) {
?>
		<li class="<?php echo $highlight; ?>"><a href="<?php bm_homeLink (); ?>" rel="nofollow"><?php _e('Home', BM_THEMENAME); ?></a></li>
<?php
	}
	
	wp_list_pages ($bm_query);
?>
	</ul>
<?php
}


/**
 *
 * @global <type> $bm_options
 * @param <type> $display
 * @return <type>
 */
function bm_homeLink ($display = true) {

	global $bm_options;
	
	if ($bm_options['homeLink'] != '') {
		$homeLink = $bm_options['homeLink'];
	} else {
		$homeLink = home_url ();
	}
	
	if ($display) {
		echo $homeLink;	
	} else {
		return $homeLink;
	}	

	
}


/**
 * print out category navigation that supports drop down menus
 *
 * @global <type> $bm_options
 */
function bm_headerCategoryNavigation () {

	global $bm_options;
	
	// exclude pages from menu
	$bm_query = array(
		'depth' => 4,
		'title_li' => '',
		'exclude' => implode (',', $bm_options['hideCategories']),
	);
	
	$bm_query = apply_filters ('bm_headerCategoryNavigation', $bm_query);
?>
	<ul class="nav">
<?php
	wp_list_categories ($bm_query);
?>
	</ul>
<?php
}


/**
 *
 * @param <type> $firstCategory
 * @param <type> $classList
 */
function bm_catClassList ($firstCategory, &$classList) {

	$cat_parent = (int) $firstCategory->category_parent;

	for ($i = 0; $i <= 5; $i++) {
		if ($cat_parent > 0) {
			$category = get_category ($cat_parent);
			if (!is_wp_error ($category)) {
				$classList[] = 'category-' . $category->term_id;
				$classList[] = 'category-' . $category->slug;
				$cat_parent = $category->category_parent;
			}
		}
	}

	return $classList;

}


/**
 * Calculate a series of body classes for styling pages
 *
 * @return string space separated list of classes
 */
function bm_bodyClass () {

	global $post, $template, $bm_options, $wp_query;
	
	$classList = array ();
	
	if (!is_home ()) {
		$classList[] = 'interior';
	}

	if (is_category ()) {
		$classList = bm_catClassList ($wp_query->queried_object, $classList);
	}
	
	if (is_single ()) {
		$classList[] = 'singlePost';
		$classList[] = 'singlePost-' . $post->ID;
		$classList[] = 'author-' . $post->post_author;
		
		$categories = get_the_category ();
		if (isset ($categories[0])) {
			$classList[] = 'category-' . $categories[0]->term_id;
			$classList[] = 'category-' . $categories[0]->slug;
			$classList = bm_catClassList ($categories[0], $classList);
		}
	}
	if (is_year () || is_month ()) {
		$classList[] = 'archive';
		$classList[] = single_month_title ('', FALSE);
	}
	if (is_search()) {
		$classList[] = 'search';
	}
	if (is_author ()) {
		$classList[] = 'author';
		$classList[] = 'author-' . $post->post_author;
	}
	if (is_404 ()) {
		$classList[] = '404';
	}
	if (is_attachment ()) {
		$classList[] = 'attachment';
		$classList[] = 'attachment-' . $post->ID;
	}
	if ($bm_options['textHeader'] == 1) {
		$classList[] = 'header-text';
	} else {
		$classList[] = 'header-image';
	}
	
	if (isset ($bm_option['displayHeaderAd']) && $bm_options['displayHeaderAd'] == 1) {
		$classList[] = 'header-advert';		
	}
	
	$templateName = strtolower (basename ($template, '.php'));
	$replaceArray = array (
		'template',
		'single',
		'_',
		'-',
	);
	$templateName = str_replace ($replaceArray, '', $templateName);
	if ($templateName != '') {
		$classList[] = 'template-' . $templateName;
	}

	if (isset ($bm_options['layoutColumnSize'])) {
		$classList[] = 'cols-' . $bm_options['layoutColumnSize'];
	}

	$classList[] = 'theme-' . strtolower (BM_THEMENAME);
	$classList[] = 'currentMonth-' . date ('m');
	$classList[] = 'hideHeader-' . ((int) $bm_options['hideHeader']);
	
	$classList = apply_filters ('bm_bodyClass', $classList);
	
	body_class ($classList);
	
}


/**
 * Display an SEO optimised title
 *
 * @global <type> $bm_options
 * @global <type> $bm_postMeta
 * @param <type> $display
 * @return <type>
 */
function bm_title ($display = TRUE) {

	// in case all in one seo is being used
	if (function_exists ('seo_title_tag')) {
		seo_title_tag ();
		return;
	}

	global $bm_options, $bm_postMeta;

	$titleOutput = array();

	if (isset ($bm_postMeta['seo_title']) && $bm_postMeta['seo_title'] != '') {
		$titleOutput[] = $bm_postMeta['seo_title'];
	} else {
		$titleOutput[] = wp_title ('', FALSE);
	}
	
	if ((is_home () || is_front_page ()) && $bm_options['seoSiteName'] == 2 || $bm_options['seoSiteName'] == 1 || ((is_home () || is_archive () || is_front_page ()) && $bm_options['seoSiteName'] == 0)) {
		$titleOutput[] = get_bloginfo ('name');
	}
	
	if (is_home () || is_front_page ()) {
		$titleOutput[] = get_bloginfo('description');
	}
	
	if (get_query_var ('paged')) {
		$titleOutput[] = sprintf (__('Page %d', BM_THEMENAME), get_query_var ('paged'));
	}

	// remove empty values
	$titleOutput = array_filter ($titleOutput);
	
	// put it all together
	$title = trim (strip_tags (implode (' &rsaquo; ', apply_filters ('bm_title', $titleOutput))));
	
	if ($display) {
		echo $title;
	} else {
		return $title;
	}

}


/**
 * Display blog Description
 *
 * @global <type> $bm_options
 */
function bm_blogDescription () {

	global $bm_options;
	
	if (isset($bm_options['displayTagline']) && $bm_options['displayTagline'] == 1) {
?>
	<p class="blogDescription"><?php bloginfo ('description'); ?></p>
<?php
	}
	
}


/**
 * list wordpress comments with paging
 */
function bm_listComments () {

	$args = array (
		'avatar_size' => 56,
		'callback' => 'bm_commentLayout',
		'type' => 'comment',
	);
	$args = apply_filters ('bm_listComments', $args);
	wp_list_comments ($args);
	
}


/**
 * list wordpress trackbacks with paging
 *
 * @global <type> $wp_query
 */
function bm_listTrackbacks() {

	global $wp_query;
	
	foreach ($wp_query->comments as $c) {
		switch ($c->comment_type) {
		
			case 'pingback':
			case 'trackback':
				$faviconDomain = bm_getFavicon ($c->comment_author_url);
?>
		<li>
			<img src="<?php echo $faviconDomain; ?>" width="16" alt="<?php printf (__('Favicon for %s', BM_THEMENAME), $faviconDomain); ?>" height="16" class="commentFavicon" />
			<a href="<?php echo $c->comment_author_url; ?>"><?php echo $c->comment_author; ?></a>
		</li>
<?php
				break;
				
			default:
			
				break;
		}
	}

}


/**
 * does this post have any trackbacks/ pingbacks?
 *
 * @return <type>
 */
function bm_hasTrackbacks() {
	
	return bm_countComments (array ('trackback', 'pingback')) > 0;

}


/**
 * count how many comments there are of the specified type
 *
 * @global <type> $wp_query
 * @param <type> $comment_type
 * @return <type>
 */
function bm_countComments ($comment_type = array()) {

	global $wp_query;
	
	$count = 0;
	
	if ($comment_type == array ()) {
		$comment_type = array ('');
	}

	if ($wp_query->comments) {
		foreach ($wp_query->comments as $c) {
			if (in_array ($c->comment_type, $comment_type)) {
				$count ++;
			}
		}
	}
	
	return $count;

}


/**
 * print an excerpt with a custom length
 *
 * @global <type> $post
 * @param int length length of the post
 * @param <type> $stripTags
 * @param <type> $display
 * @param <type> $allowedTags
 * @param <type> $content
 * @return <type>
 */
function bm_excerpt ($length = 50, $stripTags = FALSE, $display = TRUE, $allowedTags = '<p>', $content = '') {
	
	// grab the content and remove [shortcodes]
	if ($content == '') {
		global $post;
		$content = $post->post_excerpt;
	}
	
	if ($content == '') {
		$content = get_the_content ('', 0, '');
		$content = strip_shortcodes ($content);
	}

	// split content into array
	$words = explode (' ', $content, $length + 1);
	
	// remove last element (which contains all content > than specified length)
	if (count ($words) > $length) {
		array_pop ($words);
		$words[] = '...';
	}
	
	//$content = apply_filters('the_excerpt', $content);
	
	// stick everything back together again
	$content = implode (' ', $words);
	
	// make it all look good and remove unwelcome html
	if ($stripTags) {
		// remove everything
		$content = strip_tags ($content);
		$content = str_replace ("\n", ' ', $content);
		$content = str_replace ("\r", ' ', $content);
		$content = str_replace ("\t", '', $content);
		$content = trim ($content);
	} else {
		// leave the nice tags
		$content = apply_filters ('the_content', $content);
		$content = strip_tags ($content, $allowedTags);
	}
	
	$content = apply_filters ('bm_excerpt', $content);
	
	if ($display) {
		echo $content;
	} else {
		return $content;
	}
	
}


/**
 * Print a numbered list of the pages available in an archive
 *
 * @global <type> $wp_query
 * @param int $pageCount the number of pages to display in the list
 * @param <type> $query
 * @return <type>
 */
function bm_numberedPages ($pageCount = 6, $query = null) {

	if ($query === false) {
		return false;
	}

	if ($query == null) {
		global $wp_query;
		$query = $wp_query;
	}

	if ($query->max_num_pages <= 1) {
		return;
	}

	$pageStart = 1;
	$paged = $query->query_vars['paged'];
	
	// set current page if on the first page
	if ($paged == null) {
		$paged = 1;
	}
	
	// work out if page start is halfway through the current visible pages and if so move it accordingly
	if ($paged > floor ($pageCount / 2)) {
		$pageStart = $paged	- floor ($pageCount / 2);
	}

	if ($pageStart < 1) {
		$pageStart = 1;
	}

	// make sure page start is 
	if ($pageStart + $pageCount > $query->max_num_pages) {
		$pageCount = $query->max_num_pages - $pageStart;
	}
	
?>
	<div class="bm_numberedPages clear">
<?php
	echo '<span class="pageData">' . sprintf (__('Page : %1$d / %2$d', BM_THEMENAME), $paged, $query->max_num_pages) . '</span>';

	if ($paged != 1) {
?>
		<a href="<?php echo get_pagenum_link (1); ?>" class="numberedPage pageNumber_first"><span>&lsaquo; <?php _e('First', BM_THEMENAME); ?></span></a>
<?php
	}
	
	for ($p = $pageStart; $p <= $pageStart + $pageCount; $p ++) {
		
		if ($p == $paged) {
?>
		<span class="numberedPage pageNumber_<?php echo $p; ?> currentPage"><?php echo $p; ?></span>
<?php
		} else {
?>
		<a href="<?php echo get_pagenum_link ($p); ?>" class="numberedPage pageNumber_<?php echo $p; ?>"><span><?php echo $p; ?></span></a>
<?php
		}
		
	}
	
	if ($paged != $query->max_num_pages) {
?>
		<a href="<?php echo get_pagenum_link ($query->max_num_pages); ?>" class="numberedPage pageNumber_first"><span><?php _e('Last', BM_THEMENAME); ?> &rsaquo;</span></a>
<?php
	}
?>
	</div>
<?php	
}


/**
 * Display a form to use for searching the website
 *
 * @global <type> $wp_query
 * @param <type> $query
 */
function bm_themeSearch ($query = '') {
	
	$searchQuery = get_search_query();
	
	$searchButton = apply_filters ('bm_searchImage', BM_BLOGPATH . '/lib/styles/images/magnify.png');
	$searchLocation = trailingslashit (apply_filters ('bm_searchPath', bm_homeLink (false)));
	
	$searchBox = '
	<form class="searchform clear" action="' . $searchLocation . '" method="get">
		<input type="text" class="text searchtext" name="s" value="' . $searchQuery . '" />
		<input type="image" class="searchsubmit" src="' .  $searchButton . '" />
	</form>';
	
	echo apply_filters ('bm_themeSearch', $searchBox);
	
}


/**
 * Get an image from the current posts content and display it on the homepage
 *
 * @global <type> $bm_options
 * @param int $width the width to display the image
 * @param int $height the height to display the image
 * @param <type> $id
 * @param <type> $content
 * @param <type> $title
 * @return <type>
 */
function bm_postImage ($width = 0, $height = 0, $id = -1, $content = '', $title = '') {

	global $bm_options;

	if (!isset ($bm_options['useThumbnails'])) {
		return FALSE;
	}
	
	if ($bm_options['useThumbnails'] > 0) {

		$theImageDetails = bm_getPostImage ($id, $content, $title);
		$theImageSrc = $theImageDetails['src'];

		// if src found, then create a new img tag
		if (!empty ($theImageSrc)) {
			
			$altText = '';
			
			if ($theImageDetails['alt'] != '') {
				$altText = $theImageDetails['alt'];
			}
			
			$thumbOptions = array();
			$postMeta = get_post_meta ($theImageDetails['id'], 'bm_postSettings', true);

			if ($width > 0) {
				$thumbOptions[] = 'w=' . $width;
				$width = ' width="' . $width . '"';
			} else {
				$width = '';
			}
			if ($height > 0) {
				$thumbOptions[] = 'h=' . $height;
				$height = ' height="' . $height . '"';
			} else {
				$height = '';
			}

			$thumbOptions[] = 'src=' . urlencode ($theImageSrc);
			$thumbOptions[] = 'q=' . $bm_options['thumbnailQuality'];
			$thumbOptions[] = 's=' . $bm_options['sharpenThumbnail'];

			if (!empty ($postMeta['thumbnail_crop'])) {
				$thumbOptions[] = 'a=' . $postMeta['thumbnail_crop'];
			}

			if ($bm_options['thumbnail_zc']) {
				$thumbOptions[] = 'zc=2';
			}

			if ($bm_options['blackWhiteThumbnail'] == 1) {
				$thumbOptions[] = 'f=2';
			}
			
			$imagePath = BM_BLOGPATH . '/tools/timthumb.php?' . implode ('&amp;', $thumbOptions);
			$theImage = '<img src="' . $imagePath . '" ' . $width . $height . ' alt="' . $altText . '" class="timthumb" />';
			
			$theImage = apply_filters ('bm_theThumbnailImage', $theImage);
			
			return $theImage;
			
		}
		
	}
	
	return FALSE;

}


/**
 * get the first image associated with a post
 *
 * @global <type> $post
 * @global <type> $bm_options
 * @param <type> $id
 * @param <type> $content
 * @param <type> $title
 * @return <type>
 */
function bm_getPostImage ($id = -1, $content = '', $title = '') {

	// get id if it's missing
	if ($id < 0 || empty ($content)) {
		global $post;
		$id = $post->ID;
		$content = $post->post_content;
		$title = get_the_title();
	}

	// default type
	$type = 0;
	
	global $bm_options;

	// see if the thumbnail value for the current post is in the WordPress cache
	if ($ret = wp_cache_get ($id, 'bm_thumbnail')) {
		return $ret;
	}
	
	$theImageSrc = '';
	$theImageId = null;

	// featured image
	if ($theImageSrc == '') {
		$imgId = (int) get_post_thumbnail_id ($id);
		
		if ($imgId > 0) {
			$theImageId = $imgId;
			$theImage = wp_get_attachment_image_src ($imgId, 'large');
			$theImageSrc = $theImage[0];

			$type = THUMB_TYPE_FEATURED_IMAGE;
		}
	}

	// custom fields
	if ($theImageSrc == '') {
		$imageArray = array(
			'Image',
			'image'
		);

		foreach ($imageArray as $image) {
			$values = get_post_custom_values ($image, $id);

			if (!empty ($values[0])) {
				$theImageSrc = $values[0];
				$type = THUMB_TYPE_CUSTOM_FIELD;
				break;
			}
		}
	}
	
	// regex on post content
	if ($theImageSrc == '') {
		if ($content != '') {
			preg_match_all ('|<img.*?src=[\'"](.*?)[\'"].*?>|i', $content, $matches);

			$imageCount = count ($matches);

			// needs to be a loop
			// Notice the += 2 in the for iterator
			// 0 = full image html tag
			// 1 = image path selected
			if ($imageCount >= 1) {
				for ($i = 1; $i <= $imageCount; $i += 2) {
					if (isset ($matches[$i][0])) {
						$theImageSrc = $matches[$i][0];
						$type = THUMB_TYPE_POST_CONTENT;
						break;
					}
				}
			}
		}
	}
	
	// post attachments
	if ($theImageSrc == '') {
		$values = get_children (array(
			'post_parent' => $id,
			'post_type' => 'attachment',
			'post_mime_type' => 'image',
			'order' => 'ASC',
			'orderby' => 'menu_order'
		));

		if ($values) {
			foreach ($values as $childId => $attachment) {
				// add check for image post_mime_type (jpg, gif, png)
				$theImageId = $childId;
				$theImageSrc = wp_get_attachment_image_src ($childId, 'large');
				$theImageSrc = $theImageSrc[0];
				$type = THUMB_TYPE_ATTACHMENT;
				break;
			}
		}
	}

	// youtube video link/ embed
	if ($theImageSrc == '') {
		if ($content != '') {
			$youtubeArray = array (
				array ('|youtube\.com/embed/([%&=#\w-]*)|i'),
				array ('|youtube\.com/v/([%&=#\w-]*)?|i'),
				array ('|youtube\.com/watch\?v=([%&=#\w-]*)|i'),
			);
			
			foreach ($youtubeArray as $a) {
				preg_match ($a[0], $content, $matches);

				if (isset ($matches[1])) {
					$theImageSrc = 'http://img.youtube.com/vi/' . $matches[1] . '/0.jpg';
					$type = THUMB_TYPE_YOUTUBE;
					break;
				}
			}
		}
	}

	// add default if no image is found and default is specified
	if ($theImageSrc == '' && !empty ($bm_options['thumbnailDefaultRaw'])) {
		$theImageSrc = $bm_options['thumbnailDefaultRaw'];
	}

	if ($type != THUMB_TYPE_YOUTUBE) {
		$theImageSrc = preg_replace ('#\?.*#', '', $theImageSrc);
	}

	// return values
	$ret = array (
		'id' => $id,
		'imageId' => $theImageId,
		'src' => bm_muImageUploadPath ($theImageSrc),
		'src_raw' => $theImageSrc,
		'alt' => sprintf (__('Thumbnail : %s', BM_THEMENAME), $title),
		'type' => $type,
	);
	
	$ret = apply_filters ('bm_getPostImage', $ret);

	// WordPress cache
	if ($id > 0) {
		wp_cache_add ($id, $ret, 'bm_thumbnail');
	}

	return $ret;

}


/**
 * get the title for the current archive page
 *
 * @global <type> $wp_query
 */
function bm_archiveTitle () {

	global $wp_query;

	$title = '';

	if (is_category ()) {

		$title = sprintf (__('Archive for the %s Category', BM_THEMENAME), '&#8216;' . single_cat_title ('', FALSE) . '&#8217;');

	} elseif (is_tag ()) {

		$title = sprintf (__('Posts Tagged %s', BM_THEMENAME), '&#8216;' . single_tag_title ('', FALSE) . '&#8217;');

	} elseif (is_date ()) {
		
		$title = sprintf (__('Archive for %s', BM_THEMENAME), single_month_title (' ', FALSE));
			
	} elseif (is_author ()) {
	
		$curauth = $wp_query->get_queried_object ();
		$title = sprintf (__('Author Archive : %s', BM_THEMENAME), $curauth->display_name);

	} elseif (is_post_type_archive ()) {

		$title = sprintf (__('Archive for %s', BM_THEMENAME), post_type_archive_title ('', FALSE));
		
	} elseif (isset ($_GET['paged']) && !empty ($_GET['paged'])) {
	
		$title = __('Blog Archives', BM_THEMENAME);

	}
	
	$title = apply_filters ('bm_theArchiveTitle', $title);

	if (!empty ($title)) {
?>
	<h1><?php echo $title; ?></h1>
<?php
	}

}


/**
 * generic message for 404 pages
 * Saves adding the same content to many pages
 */
function bm_404 () {

?>
	<div class="error message message_404">
		<h2><?php _e('404 Page not found', BM_THEMENAME); ?></h2>
		<p><?php _e('Uh oh - you\'ve "found" something that doesn\'t exist', BM_THEMENAME); ?></p>
	</div>
<?php

}


/**
 * do a 404 header and check file types
 * if bad file type then die properly else continue and print 404 message
 */
function bm_404Response () {

	if (!is_404 ()) {
		return true;
	}

	header ($_SERVER['SERVER_PROTOCOL'] . ' 404 Not Found');
	
	if (!empty ($_SERVER['REQUEST_URI'])) {
		$fileExtension = strtolower (pathinfo ($_SERVER['REQUEST_URI'], PATHINFO_EXTENSION));
	} else {
		$fileExtension = '';
	}
	
	$badFileTypes = array(
		'css',
		'txt',
		'jpg',
		'gif',
		'rar',
		'zip',
		'png',
		'bmp',
		'tar',
		'doc',
		'xml',
		'js',
	);
	
	$badFileTypes = apply_filters ('bm_404BadFileTypes', $badFileTypes);
	
	if (in_array ($fileExtension, $badFileTypes)) {
		bm_404 ();
		die ();
	}

}


/**
 * get all of the details for the authors on the blog
 * Most useful for multi author blogs
 *
 * @global <type> $wpdb
 * @return <type>
 */
function bm_listAuthors () {

	global $wpdb;
		
	$query = 'SELECT u.ID, u.display_name, u.user_login, u.user_nicename
			FROM ' . $wpdb->users . ' as u
			LEFT JOIN ' . $wpdb->usermeta . ' as m on u.ID = m.user_id
			WHERE m.meta_key = "' . $wpdb->prefix . 'user_level" and m.meta_value > 0';
			
	$authors = $wpdb->get_results ($query);
	
	$ret = array();
	
	// loop through all authors
	foreach ($authors as $author) {
		
		$bmWp = new WP_Query();
		$bmWp->query ('posts_per_page=4&author=' . $author->ID);
		
		$posts = array();
		
		// grab authors latest posts
		if ($bmWp->have_posts ()) {
			while ($bmWp->have_posts ()) {
				$bmWp->the_post ();
				$posts[] = array(
					'title' => get_the_title (),
					'excerpt' => get_the_excerpt (),
					'permalink' => get_permalink (),
				);
			}
		}
		
		// set author properties
		$ret[] = array(
			'id' => $author->ID,			
			'name' => $author->display_name,
			'username' => $author->user_login,
			'authorPageLink' => get_author_posts_url ($author->ID, $author->user_nicename),
			'posts' => $posts,
		);		
		
	}
	
	$ret = apply_filters ('bm_listAuthors', $ret);
	
	return $ret;
	
}


/**
 * do different sidebars on different pages
 */
function bm_sidebar () {
	
	$useSidebar = 'main-sidebar';
	
	bm_ignorePostReset ();

	// if homepage use home sidebar if it has some widgets available
	if (is_home ()) {
		if (bm_sidebarHasWidgets ('home-sidebar')) {
			$useSidebar = 'home-sidebar';
		}
	}
	
	if (is_single ()) {
		$useSidebar = 'main-sidebar';
	}
	
	if (is_archive ()) {
		$useSidebar = 'main-sidebar';
	}
	
	bm_dynamicSidebar ($useSidebar);

}


/**
 *
 * @param <type> $sidebarName
 * @return <type> 
 */
function bm_sidebarHasWidgets ($sidebarName = '') {

	if ($sidebarName == '') {
		return FALSE;
	}
	
	$sidebars_widgets = wp_get_sidebars_widgets ();
	
	if (isset ($sidebars_widgets[$sidebarName])) {
		return (boolean) count ($sidebars_widgets[$sidebarName]) > 0;
	} else {
		return false;
	}
	
}


/**
 * replacement for dynamic_sidebar that adds built in default widgets
 *
 * @param string bar the name of the bar to display
 */
function bm_dynamicSidebar ($bar) {

	$action = str_replace ('-', '_', $bar);
	
	bm_doAction ('bm_' . $action . '_before');

	$sidebar = bm_getSidebar ($bar);

	if ( is_active_sidebar( $bar ) ) {

		if (isset ($sidebar['cols'])) {
			bm_resetWidgetClass ($sidebar['cols']);
		}
		// sidebar does not work so do default stuff
		if (!dynamic_sidebar ($bar)) {

			if (empty ($sidebar['size'])) {
				$sidebar['size'] = 4;
			}
			if ( ! empty( $sidebar['widgets'] ) ) {
				foreach ($sidebar['widgets'] as $widget) {
					bm_displayWidget ($widget, $bar);
				}				
			}

		}

	}

	bm_resetWidgetClass ();
	bm_doAction ('bm_' . $action . '_after');

}


/**
 *
 * @param <type> $bar
 * @return <type>
 */
function bm_getSidebar ($bar) {

	$widgetBars = bm_widgetSettings ();
	$bar = strtolower ($bar);
	
	if (!empty ($widgetBars)) {
		foreach ($widgetBars as $sidebar) {

			$idName = bm_widgetId ($sidebar);

			if ($idName == $bar) {
				return $sidebar;
			}

		}
	} else {
		return false;
	}

}


/**
 * custom widget display system
 * 
 * @param string widget the name of the function to call for this widget
 * @param integer size the column size for the current widgets wrapper
 */
function bm_displayWidget ($widget, $widgetBar) {

	global $wp_registered_sidebars;
	
	if (!isset ($wp_registered_sidebars[$widgetBar])) {
		return false;
	}
	
	$args = $wp_registered_sidebars[$widgetBar];
	
	$args['before_widget'] = sprintf ($args['before_widget'], $widget[2], $widget[2]);

	switch ($widget[2]) {
	
		case 'post-author-details':
			bm_postDetails ($args);
			break;
			
		case 'more-posts-by-this-author':
			bm_authorPosts ($args);
			break;
			
		case 'share-this-links':
			bm_sharePost ($args);
			break;
			
		case 'popular-posts':
			bm_popularPosts ($args);
			break;
			
		case 'wp_list_categories':
		case 'wp_list_pages':
			bm_customWidget ($args, $widget, '<ul>', '</ul>');
			break;
			
		default:
			bm_customWidget ($args, $widget);
			break;
	
	}

}


/**
 * display content in the footer of the theme. Can be modified via filter
 *
 * @global <type> $bm_options
 */
function bm_footerContent () {

	global $bm_options;

	bm_navigation ('navFooter');
	
	$currentYear = (integer) date ('Y');
	$copyrightYear = (integer) $bm_options['copyrightYear'];
	
	if ($copyrightYear < $currentYear) {
		$copyrightYear .= ' - ' . $currentYear;
	}
	
	// default settings
	$footerContent['copyright'] = sprintf (__('&copy; %1$s, %2$s, All Rights Reserved', BM_THEMENAME), get_bloginfo ('name'), $copyrightYear) . '. <a rel="nofollow" class="subscribeNow" href="' . get_bloginfo('rss2_url') . '">' . __('Subscribe to Free updates!', BM_THEMENAME) . '</a>';
	$footerContent['credits'] = '<a href="http://prothemedesign.com/themes/elemental/">Elemental Theme</a> by <a href="http://prothemedesign.com/"><strong>Pro Theme Design</strong></a>';

	$footerContent = apply_filters ('bm_footerContent', $footerContent);

	// display extra footer content
	if (!empty ($bm_options['extraFooter'])) {
		echo wpautop ($bm_options['extraFooter']);
	}

	// hide copyright?
	if ($bm_options['hideCopyright'] == 0) {
		echo '<p>' . $footerContent['copyright'] . '</p>';
	}

	// hide credits?
	if ($bm_options['hideCredits'] == 0) {
		echo '<p>' . $footerContent['credits'] . '</p>';
	}

	bm_googleMapsFooter ();
	bm_doAction ('bm_pageBottom');
	
}


/**
 * select a different template for a single post
 *
 * @global <type> $post
 * @global <type> $bm_postMeta
 * @param <type> $template
 * @return <type>
 */
function bm_singleTemplate ($template) {

	global $post, $bm_postMeta;
	
	$bm_postMeta = get_post_meta ($post->ID, 'bm_postSettings', true);
	
	if (is_single()) {
	
		$postTemplate = '';
		if (isset ($bm_postMeta['post_template'])) {
			$postTemplate = $bm_postMeta['post_template'];
		}
		
		if ($postTemplate != '') {
			$postTemplate = locate_template (array ($postTemplate));
			
			if (file_exists ($postTemplate)) {
				$template = $postTemplate;
			}
		}
		
	}
	
	return $template;
	
}


/**
 * apply search word highlighting to specified content on search pages
 *
 * @param <type> $content
 * @return <type>
 */
function bm_searchHighlight ($content) {
	
	if (is_search ()) {
		$keys = str_replace ('|', ' ', get_search_query ());
		$content = preg_replace ('/(' . $keys . ')/iu', '<strong class="search-excerpt">\0</strong>', $content);
	}
	
	return $content;

}


/**
 * remove default gallery styles
 *
 * @param <type> $cssStyles
 * @return <type>
 */
function bm_removeGalleryStyle ($cssStyles) {

	$newStyles = '
		border:1px solid #EEE;
		padding:5px;
		background:#FFF;
	';
	
	$cssStyles = str_replace ('border: 2px solid #cfcfcf;', $newStyles, $cssStyles);

	return $cssStyles;
	
}


/**
 * get details for the current attachment
 *
 * @global <type> $post
 * @return <type>
 */
function bm_attachmentDetails () {

	global $post;

	$ret['image'] = wp_get_attachment_link ($post->ID, array (860, 1000));
	$postDetails = get_post ($post->ID);
	
	if (isset ($postDetails->iconsize) && $postDetails->iconsize[0] <= 256) {
		$ret['classname'] = 'smallAttachment attachment';
	} else {
		$ret['classname'] = 'attachment';
	}
	
	$ret = apply_filters ('bm_attachmentDetails', $ret);
	
	return $ret;
	
}


/**
 *
 * @global <type> $month
 * @global <type> $wpdb
 * @global <type> $wp_version
 */
function bm_displayArchives ($columns = 3) {

	global $month, $wpdb, $wp_version;

	$sql = 'SELECT
			DISTINCT YEAR(post_date) AS year,
			MONTH(post_date) AS month,
			count(ID) as posts
		FROM ' . $wpdb->posts . '
		WHERE post_status="publish"
			AND post_type="post"
			AND post_password=""
		GROUP BY YEAR(post_date),
			MONTH(post_date)
		ORDER BY post_date DESC';
		
	$archiveSummary = $wpdb->get_results ($sql);
	$count = 0;

	if ($archiveSummary) {
	
		foreach ($archiveSummary as $date) {
		
			unset ($bmWp);
			$bmWp = new WP_Query ('year=' . $date->year . '&monthnum=' . zeroise ($date->month, 2) . '&posts_per_page=-1');

			if ($bmWp->have_posts ()) {

				$url = get_month_link ($date->year, $date->month);
				$text = $month[zeroise ($date->month, 2)] . ' ' . $date->year;
				$classes = 'column span-4';
				$count ++;

				if ($count > $columns) {
					$classes .= ' clearfix';
					$count = 1;
				}

				echo '<div class="' . $classes . '">';
				echo get_archives_link ($url, $text, '', '<h2>', '</h2>');
				echo '<ul class="postspermonth">';
				
				while ($bmWp->have_posts ()) {
					$bmWp->the_post ();
					echo '<li><a href="' . get_permalink ($bmWp->post) . '" title="' . esc_html ($text, 1) . '">' . wptexturize ($bmWp->post->post_title) . '</a></li>';
				}
				
				echo '</ul>';
				echo '</div>';
				
			}

		}
	}

}


/**
 *
 * @param <type> $content
 * @return <type>
 */
function bm_formatContent ($content) {

	$pattern = '/<p( .*)?( class="(.*)")??( .*)?>((<[^>]*>|\s)*)((&quot;|&#8220;|&#8216;|&lsquo;|&ldquo;|\')?[A-Z])/U';
	$replacement = '<p$1 class="first-child $3"$4>$5$7';
	
	return preg_replace ($pattern, $replacement, $content, 1);
	
}


/**
 *
 */
function bm_theDate () {

	the_time (get_option ('date_format'));
	
}


/**
 *
 */
function bm_linkPages () {

	wp_link_pages (array (
		'before' => '<p class="bm_numberedPages"><strong>' . __('Pages', BM_THEMENAME) . '</strong> ',
		'after' => '</p>',
		'next_or_number' => 'number',
		'pagelink' => '<span class="current">%</span>',
		)
	);
	
}


/**
 *
 * @global <type> $bm_options
 * @param <type> $position
 * @param <type> $modifier
 * @param <type> $display
 * @return <type>
 */
function bm_width ($position, $modifier = 0, $display = TRUE) {

	global $bm_options;
	
	$width = 0;

	$columns = apply_filters ('bm_columnSize', $bm_options['layoutColumnSize']);
	
	switch ($position) {
		
		case 'content':
			$width = $columns + $modifier;
			break;

		case 'sidebar':
			$width = 12 - $columns + $modifier;
			break;
	}
	
	if ( is_page() ) {
		if ( get_page_template_slug( get_queried_object_id() ) == 'pageTemplate_2ColumnWidgetsCenterPage.php' ) {
			$width = 6;		
		}
	}
	
	if ($display) {
		echo $width;
	} else {
		return $width;
	}
	
}


/**
 *
 * @global <type> $bm_options
 * @param <type> $oldText
 * @return <type>
 */
function bm_adminFooterText ($oldText) {
	
	global $bm_options;
	
	if (!empty ($bm_options['adminFooterText'])) {
		return $bm_options['adminFooterText'];
	}
	
	return $oldText;
	
} 


/**
 *
 * @global  $bm_options 
 */
function bm_openSearch () {

	if (isset ($_GET['opensearch'])) {

		global $bm_options;
		header ("Content-Type:text/xml");
		echo '<?xml version="1.0"?>';
?>
<OpenSearchDescription xmlns="http://a9.com/-/spec/opensearch/1.1/" xmlns:moz="http://www.mozilla.org/2006/browser/search/">
<ShortName><?php echo bm_title (); ?></ShortName>
<Description><?php printf (__('Look it up at %s', BM_THEMENAME), bm_title (false)); ?></Description>
<Image height="16" width="16" type="image/x-icon"><?php echo $bm_options['faviconRaw']; ?></Image>
<Url type="text/html" method="get" template="<?php echo trailingslashit (BM_SITEURL); ?>?s={searchTerms}"/>
</OpenSearchDescription>
<?php
		die();

	}

}


/**
 *
 * @param <type> $term 
 */
function bm_termList ($term = 'post_tag') {

	// order by count to get only the most popular tags/ categories
	$properties = array (
		'orderby' => 'count',
		'order' => 'DESC',
		'number' => 100,
	);
	$tags = get_terms ($term, $properties);

	if (is_wp_error ($tags)) {
		return false;
	}

	// defaults
	$max = 0;
	$sortList = array ();
	$first_letter = '';

	// process the results
	foreach ($tags as $t) {
		if ($t->count > $max) {
			$max = $t->count;
		}
		$t->lowername = strtolower (trim ($t->name));
		$sortList[] = $t->lowername;
	}

	// sort the results alphabetically
	sort ($sortList, SORT_STRING);

?>
	<ul class="tagsRanking">
<?php
	foreach ($sortList as $s) {
		$t = null;
		foreach ($tags as $tt) {
			if ($s == $tt->lowername) {
				$t = $tt;
				break;
			}
		}

		if ($t != null) {
			$percentage = ceil (($t->count / $max) * 100);

			
			if ($first_letter != $t->lowername[0]) {
				$first_letter = $t->lowername[0];
?>
		<li class="letter"><?php echo $first_letter; ?></li>
<?php
			}
?>
		<li class="tag">
			<span style="width:<?php echo $percentage; ?>%"><?php echo $percentage; ?>%</span>
			<em><?php echo $t->count; ?></em>
			<a href="<?php echo get_term_link ($t, 'post_tag'); ?>"><?php echo $t->name; ?></a>
		</li>
<?php
		}
	}
?>
	</ul>
<?php

}


/**
 * Remove the default bbPress website styles
 */
function bm_bbp_styles () {
	
	wp_dequeue_style ('twentyten-rtl');
	wp_dequeue_style ('twentyten');
	wp_dequeue_style ('bbp-twentyten-bbpress');
	wp_dequeue_style ('bbpress-style');
	
}


/**
 * Remove breadcrumbs from bbPress
 * @param type $param
 * @return type 
 */
function bm_bbp_true ($param) {
	return true;
}


/**
 *
 * @global  $wp_query 
 */
function bm_searchRedirect () {
	if (is_search ()) {
		global $wp_query;
		if ($wp_query->post_count == 1) {
			wp_redirect (get_permalink ($wp_query->posts['0']->ID));
		}
	}
}  
