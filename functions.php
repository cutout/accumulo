<?php

// path based settings
define ('BM_BLOGPATH', get_template_directory_uri());
define ('BM_LIB', TEMPLATEPATH . '/lib/');
define ('BM_SITEURL', get_option ('siteurl'));
define ('BM_EDITTHEME', 'edit_theme_options');

define ('BM_JQUERY_VERSION', '1.4.4');

load_theme_textdomain('accumulo');

// basic theme properties
define ('BM_accumulo', 'Accumulo');
define ("MAGPIE_OUTPUT_ENCODING", "UTF-8");

include_once (TEMPLATEPATH . '/lib/init.php');

bm_define ('BM_THEMEVERSION', '1.5.3');
bm_define ('BM_DEBUG', FALSE);

// cache properties
define ('BM_CACHE_DIR', TEMPLATEPATH . '/tools/cache/');
define ('BM_CACHE_TIME', 60 * 15);

$tabsLoaded = 0;

define ('BM_TAB_COUNT', 10);

unregister_widget('bm_widget_googleMaps');


/**
 *
 * @global <type> $wp_meta_boxes
 */
function bm_widget_dashboardSetup() {

	global $wp_meta_boxes;

	unset ($wp_meta_boxes['dashboard']['normal']['core']['bm_dashboard']);
   
}

add_action ('wp_dashboard_setup', 'bm_widget_dashboardSetup');


/**
 *
 * @return array
 */
function bm_adminSettings () {

	global $bm_administration;

	$categoryList = array();
	$pageList = array();
	$navList = array();

	$timeoutList = array (
		array(5, '5 minutes (may slow down your site)'),
		array(10, '10 minutes'),
		array(20, '20 minutes'),
		array(30, '30 minutes (recommended)'),
		array(45, '45 minutes'),
		array(60, '1 hour'),
		array(180, '3 hours'),
		array(360, '6 hours'),
		array(1440, '1 day'),
		array(2880, '2 days'),
	);
	
	$blogtabs = array (
		array(0, 'none'),
		array(1, 'tab 1'),
		array(2, 'tab 2'),
		array(3, 'tab 3'),
		array(4, 'tab 4'),
		array(5, 'tab 5'),
		array(6, 'tab 6'),
		array(7, 'tab 7'),
		array(8, 'tab 8'),
		array(9, 'tab 9'),
		array(10, 'tab 10'),
	);
	
	// not really required but reduces the number of sql queries on the actual site
	if (is_admin() && $bm_administration) {
		
		$navList = array (
			array (0, __('Off', BM_THEMENAME)),
			array (1, __('Page', BM_THEMENAME)),
			array (2, __('Category', BM_THEMENAME)),
		);

		foreach (wp_get_nav_menus () as $m) {
			$navList[] = array ($m->slug, __('Custom: ', BM_THEMENAME) . $m->name);
		}
		
		// complete list of categories
		$categoryList = bm_getCategories ();
		
		// complete of pages
		$pageListFull = get_pages();
		$pageList[] = array (-1, __('All Pages (default)', BM_THEMENAME));
		foreach ($pageListFull as $p) {
			$pageList[] = array ($p->ID, $p->post_title);
		}

		
	}

	$sections = array (
		'tabs' => array(
			'name' => __('Tabs', BM_accumulo),
			'description' => __('Enter the homepage tab names. Leave blank to hide the tab', BM_accumulo),
			'var' => 'tabs',
			'fields' => array(
				'tabName1' => array(
					'name' => __('Tab 1 name', BM_accumulo),
					'var' => 'tabName1',
					'type' => 'text',
					'default' => '',
					'description' => '',
				),
				'tabName2' => array(
					'name' => __('Tab 2 name', BM_accumulo),
					'var' => 'tabName2',
					'type' => 'text',
					'default' => '',
					'description' => '',
				),
				'tabName3' => array(
					'name' => __('Tab 3 name', BM_accumulo),
					'var' => 'tabName3',
					'type' => 'text',
					'default' => '',
					'description' => '',
				),
				'tabName4' => array(
					'name' => __('Tab 4 name', BM_accumulo),
					'var' => 'tabName4',
					'type' => 'text',
					'default' => '',
					'description' => '',
				),
				'tabName5' => array(
					'name' => __('Tab 5 name', BM_accumulo),
					'var' => 'tabName5',
					'type' => 'text',
					'default' => '',
					'description' => '',
				),
				'tabName6' => array(
					'name' => __('Tab 6 name', BM_accumulo),
					'var' => 'tabName6',
					'type' => 'text',
					'default' => '',
					'description' => '',
				),
				'tabName7' => array(
					'name' => __('Tab 7 name', BM_accumulo),
					'var' => 'tabName7',
					'type' => 'text',
					'default' => '',
					'description' => '',
				),
				'tabName8' => array(
					'name' => __('Tab 8 name', BM_accumulo),
					'var' => 'tabName8',
					'type' => 'text',
					'default' => '',
					'description' => '',
				),
				'tabName9' => array(
					'name' => __('Tab 9 name', BM_accumulo),
					'var' => 'tabName9',
					'type' => 'text',
					'default' => '',
					'description' => '',
				),
				'tabName10' => array(
					'name' => __('Tab 10 name', BM_accumulo),
					'var' => 'tabName10',
					'type' => 'text',
					'default' => '',
					'description' => '',
				),
			),
		),
		
		'settings' => array(
			'name' => __('Theme Settings', BM_accumulo),
			'description' => '',
			'var' => 'settings',
			'fields' => array(
				'blogtab' => array(
					'name' => __('Blog tab', BM_THEMENAME),
					'var' => 'blogtab',
					'default' => 0,
					'type' => 'select',
					'values' => (array) $blogtabs,
					'description' => __('An option to display a single tab in a blog format. Will use the <strong>Main Sidebar widgets</strong> rather than the tabs widgets', BM_THEMENAME),
				),
				'cacheTimeout' => array(
					'name' => __('RSS Feed Cache Timeout', BM_THEMENAME),
					'var' => 'cacheTimeout',
					'default' => 30,
					'type' => 'select',
					'values' => (array) $timeoutList,
					'description' => __('The amount of minutes the cache should be stored for. <strong>Note</strong>: You may need to wait up to 24 hours for this change to take effect', BM_THEMENAME),
				),
				'feedUpdate' => array(
					'name' => __('RSS Feed Updates', BM_THEMENAME),
					'var' => 'feedUpdate',
					'default' => 4,
					'type' => 'select',
					'values' => array (array (1,1), array (2,2), array (3,3), array (4,4), array (5,5), array (6,6), array (7,7), array (8,8), array (9,9), array (10,10)),
					'description' => __('The maximum number of feeds to update on each page load. The lower the number the faster the page loads (and the less often the feeds update). Experiment for best results', BM_THEMENAME),
				),
				'feedLinkTarget' => array(
					'name' => __('Open Feed links in a new window', BM_THEMENAME),
					'var' => 'feedLinkTarget',
					'default' => 0,
					'type' => 'checkbox',
					'description' => __('Should feed links from the Accumulo RSS Widget open in a new browser window?', BM_THEMENAME),
				),
				'textHeader' => array(
					'name' => __('Display header as text', BM_THEMENAME),
					'var' => 'textHeader',
					'type' => 'hidden',
					'default' => true,
					'description' => __('Do you want to display the header as text? If unchecked the header will display as a logo image which can be overriden with css in a child theme', BM_THEMENAME),
				),
				'hideHeader' => array(
					'name' => __('Hide header title - only use background image', BM_THEMENAME),
					'var' => 'hideHeader',
					'type' => 'hidden',
					'default' => false,
					'description' => '',
				),
				'TwitterAnywhere' => array(
					'name' => __('Twitter @Anywhere API Key', BM_THEMENAME),
					'var' => 'TwitterAnywhere',
					'type' => 'text',
					'default' => '',
					'description' => __('Enter your Twitter API Key to be able to use Twitters @anywhere hovercards on your blog. More info on the <a href="http://dev.twitter.com/anywhere">Twitter Developer site</a>', BM_THEMENAME),
				),
				'blogFooter' => array(
					'name' => __('Blog Tab Footer', BM_THEMENAME),
					'var' => 'blogTabFooter',
					'type' => 'textarea',
					'default' => '',
					'description' => __('Custom code to display in the footer of the blog tab. Can include html.', BM_THEMENAME),
				),
			),
		),
		
		'adsense' => array (
			'name' => __('Advertising Properties', BM_THEMENAME),
			'description' => '',
			'var' => 'adsense',
			'fields' => array(
				array(
					'name' => __('Adsense Publisher ID', BM_THEMENAME),
					'var' => 'adsensePublisher',
					'type' => 'text',
					'default' => '',
					'description' => __('Your adsense publisher id, the default is set to mine so make sure you change it!', BM_THEMENAME),
				),

				array(
					'name' => __('Display Header 728 x 90 ad block', BM_THEMENAME),
					'var' => 'displayHeaderAd',
					'type' => 'checkbox',
					'default' => 1,
					'description' => '',
				),
				
				array(
					'name' => __('Display Footer 728 x 90 ad block', BM_THEMENAME),
					'var' => 'displayFooterAd',
					'type' => 'checkbox',
					'default' => 1,
					'description' => '',
				),

				array(
					'name' => __('Custom code to override the 728 x 90 Header ad block', BM_THEMENAME),
					'var' => 'adOverrideHeader',
					'type' => 'textarea',
					'default' => '',
					'description' => __('Use this to add your own advertising to the 728 x 90 Header ad block (overrides the default adsense)', BM_THEMENAME),
				),
				
				array(
					'name' => __('Custom code to override the 728 x 90 Footer ad block', BM_THEMENAME),
					'var' => 'adOverrideFooter',
					'type' => 'textarea',
					'default' => '',
					'description' => __('Use this to add your own advertising to the 728 x 90 Footer ad block (overrides the default adsense)', BM_THEMENAME),
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
				'navHeader' => array(
					'name' => __('Navigation', BM_THEMENAME),
					'var' => 'navHeader',
					'default' => 1,
					'type' => 'select',
					'values' => (array) $navList,
					'description' => __('The navigation, in the header area will use this format. Create custom menus and they will automatically show up in the list.', BM_THEMENAME),
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

				'seoOptimizeSlug' => array(
					'name' => __('Optimize site urls on post publish', BM_THEMENAME),
					'var' => 'seoOptimizeSlug',
					'type' => 'checkbox',
					'default' => 0,
					'description' => __('Remove short/ generic words from the post slug (url) to optimize keyword density for Google', BM_THEMENAME),
				),
				'dofollow' => array(
					'name' => __('dofollow or nofollow links', BM_THEMENAME),
					'var' => 'dofollow',
					'default' => 1,
					'type' => 'checkbox',
					'description' => __('Should the site use dofollow links? If you\'re not sure what this means then the default will be fine', BM_THEMENAME),
				),
			),
		),
		
		'fonts' => array (
			'name' => __('Fonts', BM_THEMENAME),
			'description' => __('Customise your theme with a wide selection of fonts', BM_THEMENAME),
			'var' => 'font',
			'fields' => array (
				'font_heading' => array (
					'name' => 'Heading Font',
					'var' => 'font_heading',
					'default' => 'trebuchet',
					'type' => 'font',
					'description' => __('The font to use for headings and titles across your website.', BM_THEMENAME),
				),
				'font_body' => array (
					'name' => 'Primary Font',
					'var' => 'font_body',
					'default' => 'franklingothic',
					'type' => 'font',
					'description' => __('The primary font to use everywhere that isn\'t a heading or title', BM_THEMENAME),
				),

			)
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
					'default' => 120,
					'description' => __('Width of the thumbnail image in pixels. For use on the archives and homepage.', BM_THEMENAME),
				),
				'thumbnailHeight' => array(
					'name' => __('Thumbnail Height', BM_THEMENAME),
					'var' => 'thumbnailHeight',
					'type' => 'int',
					'default' => 100,
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
					'name' => 'Thumbnail Reisze to Fit',
					'type' => 'checkbox',
					'default' => 0,
					'description' => __('Scale down the thumbnail to fit the bounding box. Add borders if required.', BM_THEMENAME),
				),
			),
		),
		'analytics' => array(
			'name' => __('Analytics', BM_accumulo),
			'description' => '',
			'var' => 'analytics',
			'fields' => array(
				'googleAnalytics' => array(
					'name' => __('Google Analytics code', BM_THEMENAME),
					'var' => 'googleAnalytics',
					'type' => 'text',
					'description' => __('Optional Google Analytics ID (in the format UA-XXXXX-X)', BM_THEMENAME),
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
					'previewWidth' => 32,
					'previewHeight' => 32,
					'actualHeight' => 32,
					'actualWidth' => 32,
					'description' => __('Custom admin header image. Sized 32 x 32, and will be automatically resized if necessary', BM_THEMENAME),
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
			),
		),	
	);
		
	return $sections;
	
}


/**
 *
 * @global $bm_options $bm_options
 * @param type $position
 * @return type 
 */
function bm_banner ($position) {
	
	global $bm_options;
	
	// don't display stuff if set to be hidden
	if ($position == 'header' && $bm_options['displayHeaderAd'] == 0) {
		return;
	}
	
	if ($position == 'footer' && $bm_options['displayFooterAd'] == 0) {
		return;
	}
	
	// display override ads
	if ($bm_options['adOverrideHeader'] != '' && $position == 'header') {
		echo '<div class="banner">';
		echo $bm_options['adOverrideHeader'];
		echo '</div>';
		return;
	}
	
	if ($bm_options['adOverrideFooter'] != '' && $position == 'footer') {
		echo '<div class="banner">';
		echo $bm_options['adOverrideFooter'];
		echo '</div>';
		return;
	}
	
	// still here? Let's show some adsense
	if ($bm_options['adsensePublisher'] != '') {
		echo '<div class="banner">';
		bm_insertAdsense (728, 90);
		echo '</div>';		
	}
	
}


/**
 *
 * @global  $bm_options
 * @return string
 */
function bm_widgetSettings () {

	global $bm_options;
	
	// widgets = name, function, class
	$widgets = array (
		'mainSidebar' => array (
			'name' => 'main sidebar',
			'widgets' => array(
				array('', '', 'post-author-details'),
				array('', '', 'more-posts-by-this-author'),
				array('', '', 'share-this-links'),
				array('', '', 'popular-posts'),
				array(__('Categories', BM_accumulo), 'wp_list_categories', 'widget_categories', 'title_li=', '<ul>', '</ul>'),		
			),
			'cols' => 1,
		),
		'bottomWidgets' => array (
			'name' => 'bottom widgets',
			'widgets' => array(
				array(__('Pages', BM_accumulo), 'wp_list_pages', 'widget_pages', 'title_li=', '<ul>', '</ul>'),
				array(__('Tags', BM_accumulo), 'wp_tag_cloud', 'wp-tags', 'number=30'),
			),
			'cols' => 4,
		),
	);

	// custom widgets
	for ($i = 1; $i <= BM_TAB_COUNT; $i++) {
		if (isset($bm_options['tabName' . $i]) && $bm_options['tabName' . $i] != '') {
			$widgets[] = array (
				'name' => 'home tab ' . $i,
				'descriptions' => __('Widgets for', BM_accumulo) . ' ' . $bm_options['tabName' . $i],
				'widgets' => array (),
				'cols' => 3,
			);
		}
	}
	
	return $widgets;
	
}


/**
 * contents of the homepage tabs
 *
 * @param <type> $rssTabId
 */
function bm_tabContents ($rssTabId = null) {

	$maxTabs = BM_TAB_COUNT;
	
	// make sure the values are allowed else just display the homepage
	if ($rssTabId > 0 && $rssTabId <= $maxTabs) {
		bm_define ('DONOTCACHEPAGE', true);
		bm_displayTabContent ($rssTabId);
		die();
	}
	
	// die if trying to force in invalid content
	if (!empty($rssTabId)) {
		die();
	}
	
}


/**
 *
 * @global <type> $bm_options
 * @param <type> $id
 */
function bm_displayTabContent ($id) {

	global $bm_options;

	bm_registerSidebars();

	if ($bm_options['blogtab'] == $id) {

		if (have_posts()) {
?>
		<div id="tabBlogContent">
<?php
			while (have_posts()) {
				the_post();
				include ('blog_tab.php');
			}

			if (!empty ($bm_options ['blogTabFooter'])) {
				echo $bm_options ['blogTabFooter'];
			}
?>
		</div>
<?php

			get_sidebar();
		}
		
	} else {
		bm_resetWidgetClass ();
		$tabName = 'home-tab-' . $id;
		bm_dynamicSidebar ($tabName);
	}

}


/**
 *
 * @global $bm_options $bm_options
 */
function bm_doHomepageTabs () {

	global $bm_options;

	$id = ' id="first"';
	$tabList = '';
	$paneList = '';
	$tabContent = '';
	
	for ($i = 1; $i <= BM_TAB_COUNT; $i ++) {
		// display tab if tab has a name and if widgets are added to tab
		if (!empty($bm_options['tabName' . $i]) && (bm_sidebarHasWidgets('home-tab-' . $i) || ($bm_options['blogtab'] == $i))) {
		
			// capture tab contents for first tab
			$tabContents = '';
			if ($id != '') {
			
				ob_start();
				bm_displayTabContent($i);
				$tabContents = ob_get_contents();
				$tabContent .= '<div style="clear:both;"></div>';
				ob_end_clean();
				
			}
			
			$tabList .= '<li' . $id . ' class="main-tab-' . $i . '"><a class="ui-tabs" href="#tab' . $i . '" tab="' . BM_SITEURL . '/?rssTab=' . $i . '"><span>' . $bm_options['tabName' . $i] . '</span></a></li>';
			$paneList .= '<div style="display:block" id="item-' . $i . '" class="pane">' . $tabContents . '</div>';
			
			$id = '';
			
		}
	}
?>
	<ul id="tabber" class="ui-tabs-nav">
		<?php echo $tabList; ?>
	</ul>
	
	<div class="panes"> 
		<?php echo $paneList; ?>
	</div>
<?php

}


/**
 * new rss widget
 * 
 */
class bm_widget_rssNew extends WP_Widget {

	function bm_widget_rssNew() {
		parent::WP_Widget(false, 'RSS Feed');
	}

	function widget($args, $instance) {
	
		global $bm_options, $tabsLoaded;
		
		if (empty ($instance['feed_url'])) {
			_e('Feed Url is empty', BM_THEMENAME);
			return false;
		}

		$cachename = 'rssWidget_' . md5 ($instance['feed_url']);
		
		echo '<!-- cache timeout = ' . $bm_options['cacheTimeout'] . ' -->' . "\r";
		echo '<!-- feed url = ' . $instance['feed_url'] . ' -->' . "\r";
		
		$content = bm_cacheGetLimit ($cachename, 60 * ((int) $bm_options['cacheTimeout']));
		
		if ($content['status'] == -1) {
			$tabsLoaded ++;
		}
		
		// cache for half an hour
		if ($content['content'] == FALSE || ($content['status'] == -1 && $tabsLoaded <= $bm_options['feedUpdate'])) {
		
			$rss = fetch_feed ($instance['feed_url']);
			
			$target = '';
			if ($bm_options['feedLinkTarget'] == 1) {
				$target = ' target="accumulo" ';
			}

			if (!isset($instance['quantity'])) {
				$instance['quantity'] = 5;
			}
			
			$dofollow = '';
			if ($bm_options['dofollow']) {
				$dofollow = ' rel="nofollow"';
			}
			
			ob_start ();
			
			$url = $instance['website_url'];
			if ($url == '') {
				$url = $instance['feed_url'];
			}
			
			$faviconUrl = '';
			$faviconUrl = $instance['favicon'];
			
			if (empty ($instance['favicon']) && !empty ($instance['website_url'])) {
				$faviconUrl = parse_url ($instance['website_url']);
				$faviconUrl = 'http://www.google.com/s2/favicons?domain=' . $faviconUrl['host'];
			}
			
			if ($faviconUrl != '') {
				echo '<img class="favicon" width="16" height="16" src="' . $faviconUrl . '" alt="favicon" />';
			}
			
			echo '<a href="' . esc_url ($url) . '"' . $target . '>' . $instance['title'] . '</a>';
			echo $args['after_title'];

			if (isset ($rss->errors)) {
			
				echo __('Error Fetching Feed', BM_THEMENAME);
				
			} else {
			
				$maxitems = $rss->get_item_quantity ($instance['quantity']);
				$rss_items = $rss->get_items (0, $maxitems);
				
?>
	<ul class="feed-item">
<?php
				if (count ($rss_items) > 0) {
					foreach ($rss_items as $item) {
						$class = '';
						if ($item->get_description() != '' && strlen($item->get_description()) > 5) {
							$class = ' class="tt"';
						}
?>
			<li>
				<a href="<?php echo $item->get_permalink (); ?>"<?php echo $target . $dofollow . $class; ?>><?php echo $item->get_title (); ?></a>
<?php
						if ($item->get_description () != '' && strlen($item->get_description()) > 5) {
							$image = bm_feedImage ($item->get_content ());
?>
				<div class="tooltip">
					<div class="tooltipContainer">
<?php
							if ($image != '') {
								echo $image;
							}
							bm_excerpt (25, TRUE, TRUE, '<p>', $item->get_description ());
?>
					</div>
				</div>
<?php
						}
?>
			</li>
<?php
					}
				} else {
?>
<?php
				}
?>
    </ul>
<?php
			}

			$content['content'] = ob_get_contents ();
			ob_end_clean();			
			
			// if no errors cache data
			if (!isset ($rss->errors)) {
				bm_cachePut ($cachename, $content['content']);
			}
		}
		
		echo $args['before_widget'] . $args['before_title'];
		echo $content['content'];
		echo $args['after_widget'];

	}

	function update ($new_instance, $old_instance) {
	
		$new_instance['quantity'] = (int) $new_instance['quantity'];
		return $new_instance;

	}
 
	function form ($instance) {

		if (empty ($instance)) {
			$instance = array (
				'title' => '',
				'feed_url' => '',
				'website_url' => '',
				'favicon' => '',
				'quantity' => 0,
			);
		}
	
		$title = esc_attr ($instance['title']);
		$feed_url = esc_attr ($instance['feed_url']);
		$website_url = esc_url (esc_attr ($instance['website_url']));
		$favicon = esc_url (esc_attr ($instance['favicon']));
		$quantity = (int) $instance['quantity'];

		if ($quantity == 0) {
			$quantity = 5;
		}

		$quantityValues = array (
			array (3, 3),
			array (4, 4),
			array (5, 5),
			array (6, 6),
			array (7, 7),
			array (8, 8),
			array (9, 9),
			array (10, 10),
		);
		
?>
		<p><label for="<?php echo $this->get_field_id ('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id ('title'); ?>" name="<?php echo $this->get_field_name ('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id ('website_url'); ?>"><?php _e('Website URL:'); ?> <input class="widefat" id="<?php echo $this->get_field_id ('website_url'); ?>" name="<?php echo $this->get_field_name ('website_url'); ?>" type="text" value="<?php echo $website_url; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id ('feed_url'); ?>"><?php _e('Feed URL:'); ?> <input class="widefat" id="<?php echo $this->get_field_id ('feed_url'); ?>" name="<?php echo $this->get_field_name ('feed_url'); ?>" type="text" value="<?php echo $feed_url; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id ('favicon'); ?>"><?php _e('Favicon:'); ?> <input class="widefat" id="<?php echo $this->get_field_id ('favicon'); ?>" name="<?php echo $this->get_field_name ('favicon'); ?>" type="text" value="<?php echo $favicon; ?>" /></label></p>		
		<p><label for="<?php echo $this->get_field_id ('quantity'); ?>"><?php _e('Quantity:'); ?><?php bm_select($this->get_field_name ('quantity'), $quantityValues, $quantity); ?></label></p>		
<?php

	}
	
}


/**
 * new rss summary widget
 * 
 */
class bm_widget_rssSummary extends WP_Widget {

	function bm_widget_rssSummary () {
		parent::WP_Widget (false, 'RSS Feed Summary');
	}

	function widget ($args, $instance) {
	
		global $bm_options, $tabsLoaded;
		
		if (empty ($instance['feed_url'])) {
			_e('Feed Url is empty', BM_THEMENAME);
			return false;
		}

		$cachename = 'rssWidget_' . md5 ($instance['feed_url']);
		
		echo '<!-- cache timeout = ' . $bm_options['cacheTimeout'] . ' -->' . "\r";
		echo '<!-- feed url = ' . $instance['feed_url'] . ' -->' . "\r";
		
		$content = bm_cacheGetLimit ($cachename, 60 * ((int) $bm_options['cacheTimeout']));
		
		if ($content['status'] == -1) {
			$tabsLoaded ++;
		}
		
		// cache for half an hour
		if ($content['content'] == FALSE || ($content['status'] == -1 && $tabsLoaded <= $bm_options['feedUpdate'])) {
		
			$rss = fetch_feed ($instance['feed_url']);
			
			$target = '';
			if ($bm_options['feedLinkTarget'] == 1) {
				$target = ' target="accumulo" ';
			}

			if (!isset ($instance['quantity'])) {
				$instance['quantity'] = 3;
			}
			
			$dofollow = '';
			if ($bm_options['dofollow']) {
				$dofollow = ' rel="nofollow"';
			}
			
			ob_start ();
			
			$url = $instance['website_url'];
			if ($url == '') {
				$url = $instance['feed_url'];
			}
			
			$faviconUrl = '';
			$faviconUrl = $instance['favicon'];
			
			if (empty ($instance['favicon']) && !empty ($instance['website_url'])) {
				$faviconUrl = parse_url ($instance['website_url']);
				$faviconUrl = 'http://www.google.com/s2/favicons?domain=' . $faviconUrl['host'];
			}
			
			if ($faviconUrl != '') {
				echo '<img class="favicon" width="16" height="16" src="' . $faviconUrl . '" alt="favicon" />';
			}
			
			echo '<a href="' . esc_url ($url) . '"' . $target . '>' . $instance['title'] . '</a>';
			echo $args['after_title'];

			if (isset ($rss->errors)) {
			
				echo __('Error Fetching Feed', BM_THEMENAME);
				
			} else {
			
				$maxitems = $rss->get_item_quantity ($instance['quantity']);
				$rss_items = $rss->get_items (0, $maxitems);
				
				
				if (count ($rss_items) > 0) {
					echo '<ul>';
					foreach ($rss_items as $item) {
						
						$image = bm_feedImage ($item->get_content ());
						
?>
			<li class="article">
				<h3><a href="<?php echo $item->get_permalink (); ?>"<?php echo $target . $dofollow; ?>><?php echo $item->get_title (); ?></a></h3>
<?php
						if ($item->get_description () != '') {
							if ($image != '') {
								echo $image;
							}
							echo '<p>';
							bm_excerpt (40, TRUE, TRUE, '<p>', $item->get_description ());
							echo '</p>';
						}
?>
			</li>
<?php
					}
					echo '</ul>';
				}
			}

			$content['content'] = ob_get_contents ();
			ob_end_clean();			
			
			// if no errors cache data
			if (!isset ($rss->errors)) {
				bm_cachePut ($cachename, $content['content']);
			}
		}
		
		echo $args['before_widget'] . $args['before_title'];
		echo $content['content'];
		echo $args['after_widget'];

	}

	function update ($new_instance, $old_instance) {
	
		$new_instance['quantity'] = (int) $new_instance['quantity'];
		return $new_instance;

	}
 
	function form ($instance) {

		if (empty ($instance)) {
			$instance = array (
				'title' => '',
				'feed_url' => '',
				'website_url' => '',
				'favicon' => '',
				'quantity' => 0,
			);
		}
	
		$title = esc_attr ($instance['title']);
		$feed_url = esc_attr ($instance['feed_url']);
		$website_url = esc_url (esc_attr ($instance['website_url']));
		$favicon = esc_url (esc_attr ($instance['favicon']));
		$quantity = (int) $instance['quantity'];

		if ($quantity == 0) {
			$quantity = 2;
		}

		$quantityValues = array (
			array (1, 1),
			array (2, 2),
			array (3, 3),
			array (4, 4),
			array (5, 5),
		);
		
?>
		<p><label for="<?php echo $this->get_field_id ('title'); ?>"><?php _e('Title:'); ?> <input class="widefat" id="<?php echo $this->get_field_id ('title'); ?>" name="<?php echo $this->get_field_name ('title'); ?>" type="text" value="<?php echo $title; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id ('website_url'); ?>"><?php _e('Website URL:'); ?> <input class="widefat" id="<?php echo $this->get_field_id ('website_url'); ?>" name="<?php echo $this->get_field_name ('website_url'); ?>" type="text" value="<?php echo $website_url; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id ('feed_url'); ?>"><?php _e('Feed URL:'); ?> <input class="widefat" id="<?php echo $this->get_field_id ('feed_url'); ?>" name="<?php echo $this->get_field_name ('feed_url'); ?>" type="text" value="<?php echo $feed_url; ?>" /></label></p>
		<p><label for="<?php echo $this->get_field_id ('favicon'); ?>"><?php _e('Favicon:'); ?> <input class="widefat" id="<?php echo $this->get_field_id ('favicon'); ?>" name="<?php echo $this->get_field_name ('favicon'); ?>" type="text" value="<?php echo $favicon; ?>" /></label></p>		
		<p><label for="<?php echo $this->get_field_id ('quantity'); ?>"><?php _e('Quantity:'); ?><?php bm_select($this->get_field_name ('quantity'), $quantityValues, $quantity); ?></label></p>		
<?php

	}
	
}


/**
 *
 * @param type $content
 * @return type 
 */
function bm_feedImage ($content) {
	
	$theImageSrc = '';

	// get images from content
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
	
	if (
		strpos ($theImageSrc, 'feedburner') !== false ||
		strpos ($theImageSrc, '.gif') !== false ||
		strpos ($theImageSrc, 'googleusercontent') !== false || 
		strpos ($theImageSrc, 'doubleclick') !== false
	) {
		$theImageSrc = '';
	}
	
	if ($theImageSrc == '') {
		return '';
	}
	
	$thumbOptions = array (
		'w=75',
		'h=60',
		'src=' . esc_url( $theImageSrc ),
	);
	
	$imagePath = BM_BLOGPATH . '/tools/timthumb.php?' . implode ('&amp;', $thumbOptions);
	
	return '<img src="' . $imagePath . '" width="75" height="60" title="thumbnail" />';
	
}

/**
 *
 * @param <type> $id
 * @param <type> $expires
 * @return <type>
 */
function bm_cacheGetLimit ($id, $expires = 0) {

	/*
		status = 1		: ok
		status = 0		: error
		status = -1			: expired
	*/

	$status = 0;
	$content = FALSE;

	if ($expires == 0) {
		$expires = BM_CACHE_TIME;
	}
	
	// add on 10 percent of the expire time to add some randomness
	// will mean all caches for the same thing do not expire at the same time
	$expires = $expires + ceil (rand (1, ($expires / 10)));

	$filename = bm_cacheName ($id);
	$filenameExists = file_exists ($filename);
	
	if ($filenameExists) {
		$age = (time() - filemtime ($filename));
		echo '<!-- age = ' . $age . ' : expires = ' . $expires . ' -->';

		// load content
		$data = file_get_contents ($filename);
		$content = unserialize ($data);
		
		// if cache has not expired
		if ($age < $expires) {
			$status = 1;
		} else {
			$status = -1;
		}
	}
	
	return array(
		'content' => $content,
		'status' => $status,
	);
	
}


/**
 *
 * @global $bm_options $bm_options
 * @return <type>
 */
function bm_cacheLifetime () {

	global $bm_options;
	return 60 * ((int) $bm_options['cacheTimeout']);
	
}


/**
 *
 */
function bm_javascript() {
?>
<script type="text/javascript">

	jQuery(document).ready(function(){

		jQuery("li.cat-item a").each(function(){
			jQuery(this).removeAttr('title');
		});

		jQuery("li.page_item a").each(function(){
			jQuery(this).removeAttr('title');
		});
		
		//set the link
		jQuery('#arrow-top').topLink({
			min: 200,
			fadeSpeed: 500
		});
		
		//smoothscroll
		jQuery('#arrow-top').click(function(e) {
			e.preventDefault();
		});

		jQuery('a[href*=#]').click(function() {
			if (location.pathname.replace(/^\//,'') == this.pathname.replace(/^\//,'') && location.hostname == this.hostname) {
			
				var $target = jQuery(this.hash);
				$target = $target.length && $target || jQuery('[name=' + this.hash.slice(1) +']');
				
				if ($target.length) {
					var targetOffset = $target.offset().top;
					jQuery('html,body').animate({scrollTop: targetOffset}, 1000);
					return false;
				}
			}
		});
	});
	
	//plugin
	jQuery.fn.topLink = function(settings) {
		settings = jQuery.extend({
			min: 1,
			fadeSpeed: 200
		}, settings);
		return this.each(function() {
			//listen for scroll
			var el = jQuery(this);
			el.hide(); //in case the user forgot
			jQuery(window).scroll(function() {
				if (jQuery(window).scrollTop() >= settings.min) {
					el.fadeIn(settings.fadeSpeed);
				} else {
					el.fadeOut(settings.fadeSpeed);
				}
			});
		});
	};
	
	jQuery(function () {
		jQuery('a.ui-tabs').hover(function() {
			jQuery(this).fadeTo("fast", 1);
		}, function() {
			jQuery(this).fadeTo("fast", .75);
		});
	});


</script>
<?php

}


/**
 *
 */
function bm_registerScripts () {

	wp_enqueue_script ('jqueryTools', BM_BLOGPATH . '/scripts/jquery.tools.min.js', array ('jquery'), '1.2.6');
	
}


/**
 *
 * @param <type> $ulclass
 * @return <type>
 */
function add_menuclass ($ulclass) {

	return preg_replace('/<ul>/', '<ul id="nav">', $ulclass, 1);
	
}


/**
 *
 * @global int $paged
 * @global <type> $myOffset
 * @global <type> $postsperpage
 * @param string $limit
 * @return string
 */
function my_post_limit($limit) {

	global $paged, $myOffset, $postsperpage;
	if (empty($paged)) {
		$paged = 1;
	}
	$pgstrt = ((intval($paged) -1) * $postsperpage) + $myOffset . ', ';
	$limit = 'LIMIT '.$pgstrt.$postsperpage;
	
	return $limit;
	
}


/**
 *
 */
function search_excerpt_highlight() {

	$excerpt = get_the_excerpt();
	$keys = implode('|', explode(' ', get_search_query()));
	$excerpt = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $excerpt);
	
	echo '<p>' . $excerpt . '</p>';

}


/**
 *
 */
function search_title_highlight() {

	$title = get_the_title();
	$keys = implode('|', explode(' ', get_search_query()));
	$title = preg_replace('/(' . $keys .')/iu', '<strong class="search-highlight">\0</strong>', $title);
	
	echo $title;

}


/**
 *
 * @param <type> $comment
 * @param <type> $args
 * @param <type> $depth
 */
function mytheme_comment($comment, $args, $depth) {

	$GLOBALS['comment'] = $comment;
?>
	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID() ?>">
		<div id="comment-<?php comment_ID(); ?>">
			<div class="comment-author vcard">
				<?php echo get_avatar($comment,$size='48',$default='<path_to_url>' ); ?>
				<div class="commentmetadata">
					<?php printf(__('<cite class="fn">%s</cite>'), get_comment_author_link()) ?>
					<div class="comment-date">
						<a href="<?php echo htmlspecialchars( get_comment_link( $comment->comment_ID ) ) ?>">
							<?php printf(__('%1$s &bull; %2$s'), get_comment_date(),  get_comment_time()) ?>
						</a>
						<?php edit_comment_link(__('(Edit)','accumulo')) ?> 
					</div>
				</div>
			</div>
<?php
	if ($comment->comment_approved == '0') {
?>
			<em><?php _e('Your comment is awaiting moderation.','accumulo') ?></em>
			<br />
<?php }	comment_text(); ?>
			<p class="reply">
<?php
	comment_reply_link(
		array_merge( $args, array(
			'depth' => $depth, 
			'reply_text' => __('Reply','accumulo'), 
			'login_text' => __('Log in to reply','accumulo'),				
			'max_depth' => $args['max_depth'])
		)
	);
?> 
			</p>
		</div>
<?php
}
		

/**
 *
 * @param string $classes
 * @return string
 */
function comment_add_microid($classes) {
	$c_email=get_comment_author_email();
	$c_url=get_comment_author_url();
	if (!empty($c_email) && !empty($c_url)) {
		$microid = 'microid-mailto+http:sha1:' . sha1(sha1('mailto:'.$c_email).sha1($c_url));
		$classes[] = $microid;
	}
	return $classes;	
}


/**
 *
 * @global $bm_options $bm_options
 * @return <type>
 */
function bm_feedCacheTimeout() {

	global $bm_options;
	return $bm_options['cacheTimeout'] * 60;
	
}


/**
 *
 */
function bm_unregisterWidgets() {

	unregister_widget('WP_Widget_RSS');
	
}


/**
 * 
 */
function bm_updateAllFeeds() {

	global $wp_registered_widgets;

	foreach ($wp_registered_widgets as $k => $w) {
		if (strstr($k, 'bm_rssnew') !== FALSE) {
			
			$settings = $w['callback'][0]->get_settings();
			$id = $w['params'][0]['number'];
			
			if (isset($settings[$id]['feed_url'])) {
				$feed = $settings[$id]['feed_url'];
				$cachename = 'rssWidget_' . md5($feed);

				fetch_feed($feed);
				bm_cacheKill ($cachename);
			}
		}
	}

}


/**
 * 
 */
function bm_rssTab () {

	if (isset ($_GET['rssTab'])) {
		$rssTabId = (int) $_GET['rssTab'];
		bm_tabContents ($rssTabId);
		die ();
	}

}



if (!wp_next_scheduled ('bm_daily_event')) {
	wp_schedule_event (time (), 'daily', 'bm_daily_event');
}

register_widget ('bm_widget_rssNew');
register_widget ('bm_widget_rssSummary');

remove_filter ('the_content', 'wptexturize');
remove_filter ('the_title', 'wptexturize');

add_action ('bm_daily_event', 'bm_cronDaily');
add_action ('switch_theme', 'bm_deactivation');
add_action ('widgets_init', 'bm_unregisterWidgets');
add_action ('template_redirect', 'bm_rssTab');
add_action ('init', 'bm_registerScripts');

add_filter ('wp_feed_cache_transient_lifetime', 'bm_feedCacheTimeout');
add_filter ('wp_page_menu','add_menuclass');
add_filter ('wp_head','bm_javascript');
add_filter ('wp_feed_cache_transient_lifetime', 'bm_cacheLifetime');
add_filter ('dynamic_sidebar_params', 'bm_modWidgetClass');
add_filter ('comment_class','comment_add_microid');


/**
 *
 */
function bm_deactivation () {
	wp_clear_scheduled_hook ('bm_daily_event');
}


/**
 * 
 */
function bm_cronDaily () {
	bm_updateAllFeeds ();
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


/**
 *
 * @global $bm_options $bm_options
 * @global string $blog_id 
 */
function bm_accumulo_admin_footer () {

	$message = array ();

	global $bm_options, $blog_id;

	if (empty ($blog_id)) {
		$blog_id = null;
	}

	// check to see if tabs are setup

	$tabsSet = false;
	for ($i = 1; $i <= BM_TAB_COUNT; $i ++) {
		if (!empty ($bm_options['tabName' . $i])) {
			$tabsSet = true;
			break;
		}
	}

	if (!$tabsSet) {
		$message[] = '<a href="' . get_admin_url ($blog_id, 'admin.php?page=bm_adminFunctions.php') . '">' . __('Set tab names in the Accumulo theme admin', BM_THEMENAME) . '</a>';
	}

	// check if widgets have been added

	$tabsSet = false;
	for ($i = 1; $i <= BM_TAB_COUNT; $i ++) {
		if (!empty ($bm_options['tabName' . $i])) {
			$tabName = 'home-tab-' . $i;
			if (bm_sidebarHasWidgets ($tabName)) {
				$tabsSet = true;
				break;
			}
		}
	}

	if (!$tabsSet) {
		$message[] = '<a href="' . get_admin_url ($blog_id, 'widgets.php') . '">' . __('Add widgets to your tabs', BM_THEMENAME) . '</a>';
	}

	// blog tab
	if ($bm_options['blogtab'] > 0 && empty ($bm_options['tabName' . $bm_options['blogtab']])) {
		$message[] = '<a href="' . get_admin_url ($blog_id, 'admin.php?page=bm_adminFunctions.php') . '">' . __('For your blog tab to display you must give your blog tab a name', BM_THEMENAME) . '</a>';
	}

	// output the messages

	if (!empty($message)) {
		$outputMessage = '';

		$outputMessage .= '<strong>' . __('Accumulo setup instructions', BM_THEMENAME) . '</strong>';
		$outputMessage .= '<ol style="margin-left:18px;">';

		foreach ($message as $m) {
			$outputMessage .= '<li>' . $m . '</li>';
		}

		$outputMessage .= '</ol>';
?>
	<script>
		jQuery(document).ready(function() {
			jQuery('<div style="background:#efe; padding:0.5em 8px 0.3em 8px; border:1px solid #CDC; border-radius:4px; -webkit-border-radius:4px; -moz-border-radius:4px;" class="fade"></div>').html('<?php echo $outputMessage; ?>').insertAfter('#wpbody-content .wrap h2:eq(0)');
		});
	</script>
<?php
	}
}

add_action ('admin_footer', 'bm_accumulo_admin_footer');
add_filter ('bm_headstyles', 'bm_fontStyles');