<?php

/**
 *
 * @return type 
 */
function bm_loadActions () {

	return get_option ('bm_actions');

}


/**
 *
 * @global  $bm_actions
 */
function bm_registerCustomActions () {

	global $bm_actions;

	if ($bm_actions) {
		foreach ($bm_actions as $action => $value) {
			if ($value != '') {
				add_action($action, 'bm_displayCustomAction');
			}
		}
	}

}


/**
 *
 * @param <type> $action
 * @return <type>
 */
function bm_doAction ($action = '') {

	if ($action == '') {
		return FALSE;
	}

	if (BM_DEBUG) {
		echo '<div class="actionWrapper">Action = ' . $action . '</div>';
	}

	do_action($action, $action);

}


/**
 *
 * @global <type> $bm_actions
 * @param <type> $name
 */
function bm_displayCustomAction ($name) {

	global $bm_actions;

	$val = stripslashes ($bm_actions[$name]);

	// make sure there is something in there
	if ($val != '') {
		ob_start ();
		eval('?>' . $val . '<?php ');
		ob_end_flush ();
	}

}


/**
 *
 * @global <type> $bm_options
 * @global <type> $bm_actions 
 */
function bm_init () {

	// apply get requests
	if (isset ($_GET['preview_font'])) {
		bm_previewFont ($_GET['preview_font']);
	}

	if (isset ($_GET['download_settings'])) {
		bm_downloadSettings ();
	}

	bm_sendMessage ();

	// if not admin then javascript - required
	if ( is_single() ) {
		wp_enqueue_script ('comment-reply');
	}

	bm_loadScripts ();

	// filters
	//add_filter ('posts_where', 'bm_postStrip');						// remove duplicate posts from homepage
	add_filter ('single_template', 'bm_singleTemplate');			// set custom post template type
	add_filter ('page_template', 'bm_singleTemplate');
	add_filter ('wp_list_pages_excludes', 'bm_excludePages');
	add_filter ('gallery_style', 'bm_removeGalleryStyle');			// remove default gallery style to replace with own visuals
	add_filter ('name_save_pre', 'bm_seoSlugs', 0);
	add_filter ('the_content', 'bm_formatContent');
	add_filter ('the_password_form', 'bm_passwordForm');
	add_filter ('comment_text', 'bm_makeUrlClickable');
	add_filter ('the_content_more_link', 'bm_moreLink', 10, 2);
	add_filter ('admin_footer_text', 'bm_adminFooterText');
	add_filter ('wp_list_categories', 'bm_adjustCatClass');
	add_filter ('wp_list_pages', 'bm_adjustListClass');
	add_filter ('wp_nav_menu', 'bm_adjustMenuClass');
	add_filter ('dynamic_sidebar_params', 'bm_modWidgetClass');
	add_filter ('bm_headstyles', 'bm_headingStyles');
	add_filter ('bm_headstyles', 'bm_fontStyles');
	add_filter ('bbp_no_breadcrumb', 'bm_bbp_true');

	// actions
	add_action ('admin_menu', 'bm_addHeader');
	add_action ('admin_menu', 'bm_metaCreate');
	add_action ('template_redirect', 'bm_404Response');				// custom 404 for non cms content (images etc) should reduce server load
	add_action ('template_redirect', 'bm_searchRedirect');			// redirect to results page if there's only 1 search result
	add_action ('save_post', 'bm_metaSave');
	add_action ('wp_head', 'bm_registerCustomActions');
	add_action ('bm_contentTop', 'bm_contentTop');
	add_action ('comment_post', 'bm_saveTwitterUser');
	add_action ('login_head', 'bm_customLogin');
	add_action ('bbp_enqueue_scripts', 'bm_bbp_styles', 100);

	// shortcodes
	add_shortcode ('thumb', 'bm_shortcode_timThumb');
	add_shortcode ('timthumb', 'bm_shortcode_timThumb');
	add_shortcode ('tinyurl', 'bm_shortcode_tinyUrl');

	add_theme_support ('menus');
	add_theme_support ('automatic-feed-links');
	
}


/**
 * 
 * @global type $bm_options
 * @global type $bm_actions
 */
function bm_after_setup_theme() {
	
	global $bm_options, $bm_actions;

	$bm_options = bm_loadSettings (bm_adminSettings (), get_option ('bm_options'));
	$bm_actions = get_option ('bm_actions');

	bm_openSearch();

}

add_action( 'after_setup_theme', 'bm_after_setup_theme' );
add_action( 'init', 'bm_init' );
add_action( 'wp', 'bm_registerSidebars' );
//add_action( 'admin_menu', 'bm_registerSidebars' );
if ( is_admin() ) {
	add_action( 'widgets_init', 'bm_registerSidebars' );	
}
