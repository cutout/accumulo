<?php

// global variables
$bmIgnorePosts 		= array();
$bm_postMeta		= array();
$bm_crumbLinks 		= array();
$primaryPostData 	= array();
$bm_property 		= NULL;
$bm_administration 	= FALSE;
$bm_mapList 		= array();
$bmCategoryList 	= array();
$bm_adsenseCount 	= 0;

// includes
include_once(BM_LIB . 'functions/bm_utilities.php');
bm_define('BM_THEMENAME', 'Accumulo');
include_once(BM_LIB . 'functions/bm_themeFunctions.php');
include_once(BM_LIB . 'functions/bm_adminFunctions.php');
include_once(BM_LIB . 'functions/bm_widgets.php');
include_once(BM_LIB . 'functions/bm_widgetClasses.php');
include_once(BM_LIB . 'functions/bm_formHelpers.php');
include_once(BM_LIB . 'functions/bm_actions.php');

// custom header stuff
// define('HEADER_TEXTCOLOR', '');
//bm_define('HEADER_IMAGE', '%s/lib/styles/images/logo.png' );
// bm_define('HEADER_IMAGE', '' );
bm_define('HEADER_IMAGE_WIDTH', 960);
bm_define('HEADER_IMAGE_HEIGHT', 100);
// define('HEADER_IMG_DIR', BM_THEMENAME);
// define('NO_HEADER_TEXT', true);

// add_custom_image_header('bm_adminHeaderStyle', 'bm_adminHeaderStyle');
// function bm_adminHeaderStyle () {}

// if not admin then javascript - required

$bm_options = bm_loadSettings(apply_filters('bm_controlPanelOptions', bm_adminSettings()), get_option('bm_options'));
$bm_actions = bm_loadActions();

load_theme_textdomain('elemental');

define ('THUMB_TYPE_FEATURED_IMAGE', 1);
define ('THUMB_TYPE_CUSTOM_FIELD', 2);
define ('THUMB_TYPE_POST_CONTENT', 3);
define ('THUMB_TYPE_ATTACHMENT', 4);
define ('THUMB_TYPE_YOUTUBE', 5);

define ('FONT_NORMAL', 0);
define ('FONT_GOOGLE', 1);

add_theme_support ('post-thumbnails');

/**
 * 
 */
function bm_addHeader() {
	//bm_uploadCustomImage('custom header', 'headers');
	bm_addThemePage();
	//bm_addActionsPage();
}

?>