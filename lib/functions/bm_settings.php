<?php

/**
 * theme page settings
 *
 * @global <type> $bm_administration
 * @return <type>
 */
function bm_adminSettings () {

	global $bm_administration;

	$featuredQuantityList = array ();
	$featuredQuantity = array ();
	$categoryList = array ();
	$pageList = array ();
	$navList = array ();
	$columnList = array ();
	$skinList = array ();
	$sizeList = array ();
	
	// not really required but reduces the number of sql queries on the actual site
	if (is_admin() && $bm_administration) {
	
		for ($i = 0; $i <= 20; $i ++) {
			$featuredQuantityList[] = array($i, $i);
		}
		
		for ($i = 0; $i < 4; $i ++) {
			$j = $i * 3;
			$featuredQuantity[] = array ($j, $j);
		}
		
		$navList = array (
			array (0, __('Off', BM_THEMENAME)),
			array (1, __('Page', BM_THEMENAME)),
			array (2, __('Category', BM_THEMENAME)),
		);
		
		foreach (wp_get_nav_menus () as $m) {
			$navList[] = array ($m->slug, __('Custom: ', BM_THEMENAME) . $m->name);
		}
		
		// font size list
		for ($i = 10; $i <= 16; $i ++) {
			$sizeList [] = array ($i, $i . 'px');
		}

		// complete list of categories
		$categoryList = bm_getCategories ();
		
		// complete of pages
		$pageListFull = get_pages();
		$pageList[] = array (-1, __('All Pages (default)', BM_THEMENAME));
		foreach ($pageListFull as $p) {
			$pageList[] = array ($p->ID, $p->post_title);
		}
		
		// column sizes
		$columnList = array(
			6 => array (6, '6:6 (content = 480px, sidebar = 480px)'),
			7 => array (7, '7:5 (content = 560px, sidebar = 400px)'),
			8 => array (8, '8:4 (content = 640px, sidebar = 320px)'),
			9 => array (9, '9:3 (content = 720px, sidebar = 240px)'),
			10 => array (10, '10:2 (content = 800px, sidebar = 160px)'),
		);
		
		// skin list
		$skinDirs = array (
			TEMPLATEPATH . '/custom/',
			STYLESHEETPATH . '/custom/',
		);
		
		$skinList[] = array('', __('Default', BM_THEMENAME));
		
		foreach ($skinDirs as $d) {
			if (file_exists ($d)) {
				if ($handle = opendir ($d)) {
					while (false !== ($file = readdir ($handle))) {
						if (strpos ($file, 'skin_') === 0) {
							$name = $file;
							$name = str_replace (array ('.css', 'skin_', '_', '-'), '', $name);
							$name = trim (ucwords ($name));
							
							$skinList[] = array('custom/' . $file, $name);
						}
					}

					closedir($handle);
				}
			}
		}
		
	}

	$sections = array(

		'basic' => array(
			'name' => __('Basic Blog Settings', BM_THEMENAME),
			'description' => '',
			'var' => 'basic',
			'fields' => array(
				'layoutColumnSize' => array(
					'name' => __('Column Sizes', BM_THEMENAME),
					'var' => 'layoutColumnSize',
					'default' => 9,
					'type' => 'select',
					'values' => (array) $columnList,
					'description' => __('The ratio of the content column sizes', BM_THEMENAME),
				),
				'googleAnalytics' => array(
					'name' => __('Google Analytics code', BM_THEMENAME),
					'var' => 'googleAnalytics',
					'type' => 'text',
					'description' => __('Optional Google Analytics ID - in the format UA-XXXXX-X', BM_THEMENAME),
				),
				'googleWebmasterTools' => array(
					'name' => __('Google Webmaster Tools', BM_THEMENAME),
					'var' => 'googleWebmasterTools',
					'type' => 'text',
					'description' => __('Optional Google Webmaster Tools verification ID. To find your id go to the webmaster verification page, click "Alternate methods", then "Add a meta tag" and then copy the content from the meta tag.', BM_THEMENAME),
				),
				'googlePlusPageId' => array(
					'name' => __('Google+ Website Page Id', BM_THEMENAME),
					'var' => 'googlePlusPageId',
					'type' => 'text',
					'description' => __('Page id for your Google+ page.', BM_THEMENAME),
				),
				'favicon' => array(
					'name' => __('Site Favicon', BM_THEMENAME),
					'var' => 'favicon',
					'type' => 'uploadImage',
					'previewWidth' => 16,
					'previewHeight' => 16,
					'actualWidth' => 16,
					'actualHeight' => 16,
					'default' => BM_BLOGPATH . '/images/favicon.gif',
					'description' => __('16x16 icon for display in bookmarks and the url bar', BM_THEMENAME),
				),
				'copyrightYear' => array(
					'name' => __('First Year of Copyright', BM_THEMENAME),
					'var' => 'copyrightYear',
					'type' => 'int',
					'default' => date ('Y'),
					'description' => __('The year you started your website. This will be placed before the current year, allowing the copyright to be updated dynamically', BM_THEMENAME),
				),				
				'TwitterAnywhere' => array(
					'name' => __('Twitter @Anywhere API Key', BM_THEMENAME),
					'var' => 'TwitterAnywhere',
					'type' => 'text',
					'default' => '',
					'description' => __('Enter your Twitter API Key to be able to use Twitters @anywhere hovercards on your blog. More info on the <a href="http://dev.twitter.com/anywhere">Twitter Developer site</a>', BM_THEMENAME),
				),
			),
		),
		
		'navigation' => array(
			'name' => __('Navigation settings', BM_THEMENAME),
			'description' => '',
			'var' => 'header',
			'fields' => array(
				'displayHome' => array(
					'name' => __('Display Homepage Link', BM_THEMENAME),
					'var' => 'displayHome',
					'default' => 1,
					'type' => 'checkbox',
					'description' => __('Display the homepage link in the pge navigation', BM_THEMENAME),
				),			
				'homeLink' => array(
					'name' => __('Homepage link', BM_THEMENAME),
					'var' => 'homeLink',
					'default' => home_url(),
					'type' => 'text',
					'description' => __('Where the homepage link in the header points too (leave blank for default homepage)', BM_THEMENAME),
				),			
				'navBeforeHeader' => array(
					'name' => __('Top Navigation', BM_THEMENAME),
					'var' => 'navBeforeHeader',
					'default' => 1,
					'type' => 'select',
					'values' => (array) $navList,
					'description' => __('The top navigation, above the header area will use this format. Create custom menus and they will automatically show up in the list.', BM_THEMENAME),
				),
				'navAfterHeader' => array(
					'name' => __('Bottom Navigation', BM_THEMENAME),
					'var' => 'navAfterHeader',
					'default' => 2,
					'type' => 'select',
					'values' => (array) $navList,
					'description' => __('The bottom navigation, under the header will use this format. Create custom menus and they will automatically show up in the list.', BM_THEMENAME),
				),
				'navFooter' => array(
					'name' => __('Footer Navigation', BM_THEMENAME),
					'var' => 'navFooter',
					'default' => '0',
					'type' => 'select',
					'values' => (array) $navList,
					'description' => __('Navigation in the site footer. Under the widgets, and above the copyright notices.', BM_THEMENAME),
				),
				'hidePages' => array(
					'name' => __('Hide pages from the navigation', BM_THEMENAME),
					'var' => 'hidePages',
					'type' => 'multiSelect',
					'values' => (array) $pageList,
					'default' => array(),
					'description' => __('Select the pages you want to hide. They will be hidden in all places that pages are listed (navigation, widgets etc)', BM_THEMENAME),
				),
				'hideCategories' => array(
					'name' => __('Hide Categories from the Navigation', BM_THEMENAME),
					'var' => 'hideCategories',
					'type' => 'multiSelect',
					'values' => $categoryList,
					'default' => array(0),
					'description' => __('Select the categories you want to hide. They will be hidden in all places that categories are listed (navigation, widgets etc)', BM_THEMENAME),
				),
			),
		),
		
		'header' => array(
			'name' => __('Header Settings', BM_THEMENAME),
			'description' => '',
			'var' => 'header',
			'fields' => array(
				'headerImage' => array(
					'name' => __('Header Image', BM_THEMENAME),
					'var' => 'headerImage',
					'default' => '',
					'type' => 'uploadImage',
					'previewWidth' => floor (HEADER_IMAGE_WIDTH / 3),
					'previewHeight' => floor (HEADER_IMAGE_HEIGHT / 3),
					'actualWidth' => HEADER_IMAGE_WIDTH,
					'actualHeight' => HEADER_IMAGE_HEIGHT,
					'description' => __('Custom header image. Defaut size 960 x 100 (height can be altered below), and will be automatically cropped if necessary', BM_THEMENAME),
				),
				'headerHeight' => array (
					'name' => __('Header Height', BM_THEMENAME),
					'var' => 'headerHeight',
					'type' => 'int',
					'default' => 100,
					'description' => __('Height of the header area, including header image', BM_THEMENAME),
				),
				'textHeader' => array(
					'name' => __('Display header as text', BM_THEMENAME),
					'var' => 'textHeader',
					'type' => 'checkbox',
					'default' => 1,
					'description' => __('Do you want to display the header as text? If unchecked the header will display as a logo image which can be overriden with css in a child theme', BM_THEMENAME),
				),
				'hideHeader' => array(
					'name' => __('Hide header title - only use background image', BM_THEMENAME),
					'var' => 'hideHeader',
					'type' => 'checkbox',
					'default' => 0,
					'description' => '',
				),
				/*
				array(
					'name' => __('Display header tag line', BM_THEMENAME),
					'var' => 'displayTagline',
					'type' => 'checkbox',
					'default' => 1,
					'description' => __('Do you want to show the tag line under the site heading?', BM_THEMENAME),
				),
				*/
			),
		),

		'footer' => array(
			'name' => __('Footer Settings', BM_THEMENAME),
			'description' => '',
			'var' => 'footer',
			'fields' => array(
				'hideFooter' => array(
					'name' => __('Hide Footer Widgets', BM_THEMENAME),
					'var' => 'hideFooter',
					'type' => 'checkbox',
					'default' => 0,
					'description' => __('Do you want to show the footer widgets?', BM_THEMENAME),
				),
				'hideCredits' => array(
					'name' => __('Hide Copyright Notice', BM_THEMENAME),
					'var' => 'hideCopyright',
					'type' => 'checkbox',
					'default' => 0,
					'description' => __('Do you want to show the copyright notice in the footer?', BM_THEMENAME),
				),
				'hideCopyright' => array(
					'name' => __('Hide Footer Credits', BM_THEMENAME),
					'var' => 'hideCredits',
					'type' => 'checkbox',
					'default' => 0,
					'description' => __('Do you want to show the Pro Theme Design credits in the footer? We love it when people keep our link, but want to give people the choice to show or hide things as they want.', BM_THEMENAME),
				),
				'extraFooter' => array(
					'name' => __('Extra Footer Content', BM_THEMENAME),
					'var' => 'extraFooter',
					'type' => 'textarea',
					'default' => '',
					'description' => __('Extra text to display in the footer. Will automatically add paragraphs.', BM_THEMENAME),
				),

			),
		),

		'seo' => array(
			'name' => __('Search Engine Optimization', BM_THEMENAME),
			'description' => '',
			'var' => 'seo',
			'fields' => array(
				'seoSiteName' => array(
					'name' => __('Display website name in page title', BM_THEMENAME),
					'var' => 'seoSiteName',
					'type' => 'select',
					'values' => array (
						array ('0', __('homepage and category pages', BM_THEMENAME)),
						array ('1', __('all pages', BM_THEMENAME)),
						array ('2', __('homepage only', BM_THEMENAME)),
					),
					'default' => 2,
					'description' => __('Select what to display in the website title', BM_THEMENAME),
				),

				'seoBreadcrumbs' => array(
					'name' => __('Display breadcrumbs', BM_THEMENAME),
					'var' => 'seoBreadcrumbs',
					'values' => array (
						array ('0', __('hide everywhere', BM_THEMENAME)),
						array ('1', __('hide on single post pages', BM_THEMENAME)),
						array ('3', __('show only on single post pages', BM_THEMENAME)),
						array ('2', __('show on all pages', BM_THEMENAME)),
					),
					'default' => 2,
					'type' => 'select',
					'description' => __('Breadcrumbs are great for both navigation and SEO but they are not always desirable so you can disable them here', BM_THEMENAME),
				),

				'seoOptimizeSlug' => array(
					'name' => __('Optimize site urls on post publish', BM_THEMENAME),
					'var' => 'seoOptimizeSlug',
					'type' => 'checkbox',
					'default' => 0,
					'description' => __('Remove short/ generic words from the post slug (url) to optimize keyword density for Google', BM_THEMENAME),
				),
			),
		),

		'fonts' => array (
			'name' => __('Fonts', BM_THEMENAME),
			'description' => __('Customise your theme with a wide selection of fonts. You can select your Google font on the <a href="http://www.google.com/webfonts">Google Webfonts Directory</a>. Note that Google Webfonts may add a little more to the page load time.', BM_THEMENAME),
			'var' => 'font',
			'fields' => array (
				'font_size' => array (
					'name' => __('Master Font Size', BM_THEMENAME),
					'var' => 'font_size',
					'default' => 12,
					'type' => 'select',
					'values' => (array) $sizeList,
					'description' => __('Use this to control all font sizes across the site', BM_THEMENAME),
				),
				'font_heading' => array (
					'name' => 'Heading Font',
					'var' => 'font_heading',
					'default' => 'georgia',
					'type' => 'font',
					'description' => __('The font to use for headings and titles across your website.', BM_THEMENAME),
				),
				'font_body' => array (
					'name' => 'Primary Font',
					'var' => 'font_body',
					'default' => 'arial',
					'type' => 'font',
					'description' => __('The primary font to use everywhere that isn\'t a heading or title', BM_THEMENAME),
				),

			)
		),

		'homepage' => array(
			'name' => __('Homepage Settings', BM_THEMENAME),
			'description' => __('Homepage specific settings. These control the content shown on the first page of your site.', BM_THEMENAME),
			'var' => 'home',
			'fields' => array(
				'featuredQuantity' => array(
					'name' => __('Large Featured Category Quantity', BM_THEMENAME),
					'var' => 'featuredQuantity',
					'type' => 'select',
					'values' => $featuredQuantityList,
					'default' => 1,
					'description' => __('Number of full posts to display on the homepage (excerpts will be shown otherwise - 0 to only show excerpts)', BM_THEMENAME),
				),
				/*
				'featuredContentDisplay' => array(
					'name' => __('Featured Content Display', BM_THEMENAME),
					'var' => 'featuredContentDisplay',
					'type' => 'checkbox',
					'default' => 1,
					'description' => __('Tick to display full post content, untick to show the excerpt', BM_THEMENAME),
				),
				*/
			),
		),
		
		'category' => array(
			'name' => __('Archive Settings', BM_THEMENAME),
			'description' => __('Archive page specific settings', BM_THEMENAME),
			'var' => 'category',
			'fields' => array(
				'displayChildCategories' => array(
					'name' => __('Display child categories', BM_THEMENAME),
					'var' => 'displayChildCategories',
					'type' => 'checkbox',
					'default' => 1,
					'description' => __('Do you want to display the child categories on the first page of each categories archives?', BM_THEMENAME),
				),
				'displayArchiveExcerpt' => array(
					'name' => __('Display archive as excerpt', BM_THEMENAME),
					'var' => 'displayArchiveExcerpt',
					'type' => 'checkbox',
					'default' => 1,
					'description' => __('Do you want to display the child categories on the first page of each categories archives?', BM_THEMENAME),
				),
			),
		),
		
		'thumbnail' => array(
			'name' => __('Thumbnail Settings', BM_THEMENAME),
			'description' => '',
			'var' => 'thumbnail',
			'fields' => array(
				'useThumbnails' => array(
					'name' => __('Use Thumbnail images', BM_THEMENAME),
					'var' => 'useThumbnails',
					'type' => 'checkbox',
					'default' => 1,
					'description' => __('Enable/ Disable Thumbnail images', BM_THEMENAME),
				),	
				'thumbnailWidth' => array(
					'name' => __('Thumbnail Width', BM_THEMENAME),
					'var' => 'thumbnailWidth',
					'type' => 'int',
					'default' => 128,
					'description' => __('Width of the thumbnail image in pixels. For use on the archives and homepage.', BM_THEMENAME),
				),
				'thumbnailHeight' => array(
					'name' => __('Thumbnail Height', BM_THEMENAME),
					'var' => 'thumbnailHeight',
					'type' => 'int',
					'default' => 80,
					'description' => __('Height of the thumbnail image in pixels. For use on the archives and homepage.', BM_THEMENAME),
				),
				'thumbnailQuality' => array(
					'name' => __('Thumbnail Quality', BM_THEMENAME),
					'var' => 'thumbnailQuality',
					'type' => 'int',
					'default' => 80,
					'description' => __('Compression level for the thumbnail images. Higher numbers = slower download times, lower numbers = worse quality.', BM_THEMENAME),
				),
				'sharpenThumbnail' => array(
					'name' => __('Sharpen Thumbnail', BM_THEMENAME),
					'var' => 'sharpenThumbnail',
					'type' => 'checkbox',
					'default' => 1,
					'description' => __('Make thumbnails look crisper', BM_THEMENAME),
				),
				'blackWhiteThumbnail' => array(
					'name' => __('Black and White Thumbnails', BM_THEMENAME),
					'var' => 'blackWhiteThumbnail',
					'type' => 'checkbox',
					'default' => 0,
					'description' => __('Convert the Thumbnail images to Black and White', BM_THEMENAME),
				),
				'thumbnailDefault' => array(
					'name' => __('Default Thumbnail', BM_THEMENAME),
					'var' => 'thumbnailDefault',
					'default' => '',
					'type' => 'uploadImage',
					'previewWidth' => 100,
					'previewHeight' => 100,
					'actualWidth' => 100,
					'actualHeight' => 100,
					'description' => __('Default TimThumb thumbnail image. Leave blank to hide thumbnails that don\'t have images', BM_THEMENAME),
				),
				'thumbnail_zc' => array (
					'var' => 'thumbnail_zc',
					'name' => __('Thumbnail Resize to Fit', BM_THEMENAME),
					'type' => 'checkbox',
					'default' => 0,
					'description' => __('Scale down the thumbnail to fit the bounding box. Add borders if required.', BM_THEMENAME),
				),
			),
		),
		
		'contact' => array(
			'name' => __('Contact Page Settings', BM_THEMENAME),
			'description' => __('Some general settings for your contact form', BM_THEMENAME),
			'var' => 'contact',
			'fields' => array(
				'email' => array(
					'name' => __('Email Address', BM_THEMENAME),
					'var' => 'email',
					'type' => 'text',
					'description' => __('The email address contact form messages will get sent to', BM_THEMENAME),
				),
				'emailSubject' => array(
					'name' => __('Email Subject', BM_THEMENAME),
					'var' => 'emailSubject',
					'type' => 'text',
					'default' => get_bloginfo('name') . ' : ' . __('Contact form', BM_THEMENAME),
					'description' => __('The subject for the emails that get sent to you via the contact form', BM_THEMENAME),
				),				
			),
		),

		'branding' => array(
			'name' => __('Admin Branding', BM_THEMENAME),
			'description' => __('customize the look and feel of the WordPress admin', BM_THEMENAME),
			'var' => 'branding',
			'fields' => array(
				'adminHeaderImage' => array(
					'name' => __('Admin Header Image', BM_THEMENAME),
					'var' => 'adminHeaderImage',
					'default' => '',
					'type' => 'uploadImage',
					'previewWidth' => 16,
					'previewHeight' => 16,
					'actualHeight' => 16,
					'actualWidth' => 16,
					'description' => __('Custom admin header image. Sized 16 x 16, and will be automatically resized if necessary', BM_THEMENAME),
				),	
				'adminFooterText' => array(
					'name' => __('Admin Footer Text', BM_THEMENAME),
					'var' => 'adminFooterText',
					'type' => 'text',
					'default' => '',
					'description' => __('Text to display in the WordPress footer', BM_THEMENAME),
				),
				'defaultGravatar' => array(
					'name' => __('Default Gravatar Image', BM_THEMENAME),
					'var' => 'defaultGravatar',
					'default' => '',
					'type' => 'uploadImage',
					'previewWidth' => 60,
					'previewHeight' => 60,
					'actualHeight' => 90,
					'actualWidth' => 90,
					'description' => __('Generic <a href="http://en.gravatar.com/">Gravatar</a> (avatar) image for use by users who do not have one set up. Size = 46px x 46px (does not get cropped automatically)', BM_THEMENAME),
				),
				'adminLoginImage' => array(
					'name' => __('Administration Login Image', BM_THEMENAME),
					'var' => 'adminLoginImage',
					'default' => '',
					'type' => 'uploadImage',
					'previewWidth' => 156,
					'previewHeight' => 45,
					'actualHeight' => 90,
					'actualWidth' => 312,
					'description' => __('Image to display on the login page', BM_THEMENAME),
				),
			),
		),

	);

	$type = 'hidden';
	if (count ($skinList) > 1) {
		$type = 'select';
	}

	$sections['basic']['fields']['skinStyle'] = array (
		'name' => __('Site Style', BM_THEMENAME),
		'var' => 'skinStyle',
		'type' => $type,
		'values' => $skinList,
		'default' => '',
		'description' => __('Select a custom style for your website. Styles can be found in the custom directory.', BM_THEMENAME),
	);
	
	return apply_filters ('bm_controlPanelOptions', $sections);
}


/**
 *
 * @global <type> $post
 * @return <type>
 */
function bm_postMetaSettings() {

	// access post meta settings with $

	global $post;
	
	$templateList = array();
	$cropPositionList = array();

	if (is_admin ()) {
		$templateList = bm_getTemplates ();

		$cropPositionList = array (
			array('c', __('Center (Default)', BM_THEMENAME)),
			array('t', __('Top', BM_THEMENAME)),
			array('b', __('Bottom', BM_THEMENAME)),
			array('l', __('Left', BM_THEMENAME)),
			array('r', __('Right', BM_THEMENAME)),
		);
	}
	
	// basic settings
	$meta = array(
		'basic' => array(
			'fields' => array(	
				'seo_title' => array (
					'var' => 'seo_title',
					'type' => 'text',
					'name' => 'SEO Custom Title',
					'default' => '',
					'description' => __('Custom <strong>&lt;title&gt;</strong> for the page (approximately 50 characters)', BM_THEMENAME),
				),
				'seo_breadcrumbTitle' => array (
					'var' => 'seo_breadcrumbTitle',
					'type' => 'text',
					'name' => 'SEO Breadcrumb Custom Title',
					'default' => '',
					'description' => __('Custom name for the breadcrumbs (will use post title if empty)', BM_THEMENAME),
				),
				'seo_description' => array (
					'var' => 'seo_description',
					'type' => 'textarea',
					'name' => 'SEO Custom Description',
					'default' => '',
					'description' => __('Custom meta description for the page (approximately 126 characters)', BM_THEMENAME),
				),
				'thumbnail_crop' => array (
					'var' => 'thumbnail_crop',
					'name' => 'Thumbnail Crop Position',
					'values' => $cropPositionList,
					'type' => 'select',
					'default' => 'c',
					'description' => __('How to crop the thumbnail image if needed', BM_THEMENAME),
				),
			),
		),
	);
	
	// content specific to posts
	if (empty ($post) || $post->post_type == 'post') {
		$meta['basic']['fields'] = $meta['basic']['fields'] + array (
			'post_template' => array (
				'var' => 'post_template',
				'type' => 'select',
				'name' => 'Post Template',
				'values' => $templateList,
				'default' => '',
				'description' => __('Select a custom post layout from the list above', BM_THEMENAME),
			),
		);
	}

	return apply_filters ('bm_postMetaSettings', $meta);
	
}


/**
 *
 * @global <type> $bm_widgets
 * @return <type>
 */
function bm_widgetSettings () {

	global $bm_widgets;

	if (empty ($bm_widgets)) {
		
		// widgets = name, function, class
		$bm_widgets = array (
			'mainSidebar' => array (
				'name' => 'main sidebar',
				'size' => bm_width ('sidebar', 0, FALSE),
				'description' => __('Primary Right hand sidebar', BM_THEMENAME),
				'widgets' => array(
					array ('', '', 'post-author-details'),
					array ('', '', 'more-posts-by-this-author'),
					array ('', '', 'share-this-links'),
					array ('', '', 'popular-posts'),
					array (__('Categories', BM_THEMENAME), 'wp_list_categories', 'widget_categories', 'title_li=', '<ul>', '</ul>'),
				),
			),
			
			'centerHomepageColumn' => array (
				'name' => 'center homepage column',
				'description' => __('Displays on the homepage. Remove all widgets to hide the bar entirely', BM_THEMENAME),			
				'size' => 2,
				'widgets' => array(
					array(__('Pages', BM_THEMENAME), 'wp_list_pages', 'widget_pages', 'title_li=', '<ul>', '</ul>'),
					array(__('Recent Comments', BM_THEMENAME), 'recent-comments', 'recent-comments'),
				),
			),
			
			'footerContent' => array (
				'name' => 'footer content (3 items recommended)',
				'description' => __('Keep widgets to a multiple of three to keep things looking their best', BM_THEMENAME),			
				'id' => 'footer-content',
				'cols' => 3,
				'widgets' => array(
					array(__('Pages', BM_THEMENAME), 'wp_list_pages', 'widget_pages', 'title_li=', '<ul>', '</ul>'),
					array(__('Tags', BM_THEMENAME), 'wp_tag_cloud', 'wp-tags', 'number=30'),
				),
			),
		
			'homeSidebar' => array (
				'name' => 'home sidebar',
				'description' => __('Optional. Leave blank to use the "main sidebar" instead', BM_THEMENAME),
				'size' => bm_width ('sidebar', 0, FALSE),
				'widgets' => array(
				),
			),
		);
		
		if ( is_page() || is_admin() ) {

			$templateList = array(
				array (
					'template' => 'pageTemplate_3ColumnWidgets.php',
					'cols' => 3,
					'width' => 4,
					'name' => '3 Cols',
				),
				array (
					'template' => 'pageTemplate_2ColumnWidgets.php',
					'cols' => 2,
					'width' => 6,
					'name' => '2 Cols',
				),
				array (
					'template' => 'pageTemplate_4ColumnWidgets.php',
					'cols' => 4,
					'width' => 3,
					'name' => '4 Cols',
				),
				array (
					'template' => 'pageTemplate_2ColumnWidgetsCenterPage.php',
					'cols' => 2,
					'width' => 3,
					'name' => '2 Cols with Page Content Center',
				),
				array (
					'template' => 'pageTemplate_2ColumnWidgetsCenterContent.php',
					'cols' => 2,
					'width' => 3,
					'name' => '2 Cols with Blog Posts Center',
				),
			);
			$templateList = apply_filters( 'bm_widget_templates', $templateList );
			$tids = bm_getActiveTemplates( $templateList );
			$bm_widgets = array_merge( $bm_widgets, bm_createTemplateWidgets( $templateList, $tids ) );
			
		}
	}
	
	return apply_filters ('bm_widgetSettings', $bm_widgets);
	
}


/**
 *
 * @return <type>
 */
function bm_actionSettings() {

	$actions = array (
		'bm_pageTop' => array(
			'name' => __('Page Top', BM_THEMENAME),
			'var' => 'bm_pageTop',
			'description' => __('Before any content is displayed', BM_THEMENAME),
		),
		'bm_pageBottom' => array(
			'name' => __('Page Bottom', BM_THEMENAME),
			'var' => 'bm_pageBottom',
			'description' => __('After all content has been displayed', BM_THEMENAME),
		),
		'bm_headerBefore' => array(
			'name' => __('Before Header', BM_THEMENAME),
			'var' => 'bm_headerBefore',
			'description' => __('Before the header content', BM_THEMENAME),
		),
		'bm_headerAfter' => array(
			'name' => __('After Header', BM_THEMENAME),
			'var' => 'bm_headerAfter',
			'description' => __('After the header content', BM_THEMENAME),
		),
		'bm_main_sidebar_before' => array(
			'name' => __('Before Main Sidebar', BM_THEMENAME),
			'var' => 'bm_main_sidebar_before',
			'description' => __('At the top of the primary sidebar column', BM_THEMENAME),
		),
		'bm_main_sidebar_after' => array(
			'name' => __('After Main Sidebar', BM_THEMENAME),
			'var' => 'bm_main_sidebar_after',
			'description' => __('Underneath the primary sidebar content', BM_THEMENAME),
		),
		'bm_footer_content_before' => array(
			'name' => __('Before Footer Content', BM_THEMENAME),
			'var' => 'bm_footer_content_before',
			'description' => __('Top of the footer content', BM_THEMENAME),
		),
		'bm_footer_content_after' => array(
			'name' => __('After Footer Content', BM_THEMENAME),
			'var' => 'bm_footer_content_after',
			'description' => __('under the footer content', BM_THEMENAME),
		),
		'bm_center_homepage_column_before' => array(
			'name' => __('Before Center Homepage Column', BM_THEMENAME),
			'var' => 'bm_center_homepage_column_before',
			'description' => '',
		),
		'bm_center_homepage_column_after' => array(
			'name' => __('After Center Homepage Column', BM_THEMENAME),
			'var' => 'bm_center_homepage_column_after',
			'description' => '',
		),
		'bm_contentTop' => array(
			'name' => __('Before Main page content', BM_THEMENAME),
			'var' => 'bm_contentTop',
			'description' => '',
		),
		'bm_contentBottom' => array(
			'name' => __('After Main page content', BM_THEMENAME),
			'var' => 'bm_contentBottom',
			'description' => '',
		),
		'bm_afterAttachment' => array(
			'name' => __('After Attachment content', BM_THEMENAME),
			'var' => 'bm_afterAttachment',
			'description' => '',
		),

	);
	
	return apply_filters('bm_actionSettings', $actions);
	
}


/**
 *
 * @return <type> 
 */
function bm_fontSettings () {

	$fonts = array (
		'arial' => array (
			'name' => 'Arial (sans serif)',
			'type' => FONT_NORMAL,
			'family' => 'arial',
		),
		'tahoma' => array (
			'name' => 'Tahoma/ Geneva (sans serif)',
			'type' => FONT_NORMAL,
			'family' => 'Geneva, Tahoma, Verdana, sans-serif',
		),
		'verdana' => array (
			'name' => 'Verdana (sans serif)',
			'type' => FONT_NORMAL,
			'family' => 'Verdana, Geneva, sans-serif',
		),
		'helvetica' => array (
			'name' => 'Helvetica (sans serif)',
			'type' => FONT_NORMAL,
			'family' => '"Helvetica Neue", Helvetica, Arial, sans-serif',
		),
		'gillsans' => array (
			'name' => 'Gill Sans (sans serif)',
			'type' => FONT_NORMAL,
			'family' => '"Gill Sans", "Gill Sans MT", GillSans, Calibri, "Trebuchet MS", sans-serif',
		),
		'trebuchet' => array (
			'name' => 'Trebuchet (sans serif)',
			'type' => FONT_NORMAL,
			'family' => '"Trebuchet MS", "Lucida Grande", "Lucida Sans Unicode", "Lucida Sans", Tahoma, sans-serif',
		),
		'calibri' => array (
			'name' => 'Calibri (sans serif)',
			'type' => FONT_NORMAL,
			'family' => 'Calibri, Candara, Segoe, "Segoe UI", Optima, Arial, sans-serif',
		),
		'franklingothic' => array (
			'name' => 'Franklin Gothic (sans serif)',
			'type' => FONT_NORMAL,
			'family' => '"Franklin Gothic Medium", Arial, sans-serif',
		),
		'georgia' => array (
			'name' => 'Georgia (serif)',
			'type' => FONT_NORMAL,
			'family' => 'Cambria, Georgia, serif',
		),
		'garamond' => array (
			'name' => 'Garamond (serif)',
			'type' => FONT_NORMAL,
			'family' => 'Garamond, Baskerville, "Baskerville Old Face", "Hoefler Text", "Times New Roman", serif',
		),
		'palantino' => array (
			'name' => 'Palantino (serif)',
			'type' => FONT_NORMAL,
			'family' => 'Palatino, "Palatino Linotype", "Palatino LT STD", "Book Antiqua", Georgia, serif',
		),
		'bodoni' => array (
			'name' => 'Bodoni (serif)',
			'type' => FONT_NORMAL,
			'family' => '"Bodoni MT", Didot, "Didot LT STD", "Hoefler Text", Garamond, "Times New Roman", serif',
		),
		'bookantiqua' => array (
			'name' => 'Book Antiqua (serif)',
			'type' => FONT_NORMAL,
			'family' => '"Book Antiqua", Palatino, "Palatino Linotype", "Palatino LT STD", Georgia, serif',
		),
	);
	
	$google_fonts = unserialize  ( file_get_contents (BM_LIB . 'data/theme_fonts.txt'));
	
	foreach ($google_fonts as $f) {
		$fonts[str_replace (' ', '', strtolower ($f['font-name']))] = array (
			'name' => $f['font-name'],
			'type' => FONT_GOOGLE,
		);
	}

	return apply_filters('bm_fonts', $fonts);

}