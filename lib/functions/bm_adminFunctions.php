<?php

/**
 * Things to include in the administration head content
 *
 * @global <type> $bm_options
 */
function bm_adminHead() {

	global $bm_options;
	
?>
<script type="text/javascript">
var currentPopup;

jQuery(document).ready(function(){
	
	jQuery ('.bm_colorInput').focusout (function () {
		popup.hide ();
	});
	
	jQuery ('.bm_colorInput').click (function () {

		if (popup.is (':visible')) {
			popup.hide ();
		} else {
			popup.show ();			
		}
		
		position = jQuery (this).position ();
		popup.css ('top', (position.top + 175) + 'px');
		popup.css ('left', (position.left + 160) + 'px');
		
		f.linkTo (this);
		
	});
	
	// setup tabs
	jQuery ('#bm_adminTabs').tabs({
		fx: { opacity: 'toggle' }
	});
	
});

var fonts = <?php bm_listFonts (); ?>;

function bm_load_css (family) {
	
	if (!fonts[family]['loaded'] && fonts[family]['type'] > 0) {
		fonts[family]['loaded'] = true;
		fontfamily = fonts[family]['name'];
		if (fonts[family]['weight'] != undefined) {
			fontfamily = fontfamily + ':' + fonts[family]['weight'];
		}
		font_uri = 'http://fonts.googleapis.com/css?family=' + fontfamily.replace(' ', '+');
		jQuery("head").append('<link href="' + font_uri + '" rel="stylesheet" type="text/css">');
	}
	
}

function bm_font_family (family) {

	font_family = '';
	if (fonts[family]['type'] == 0) {
		font_family = fonts[family]['family'];
	} else {
		fontfamily = fonts[family]['name'];
		font_family = '"' + fontfamily + '", arial, serif';
	}

	return font_family;

}
</script>
<?php
	if (!empty($bm_options['adminHeaderImage'])) {
		$adminLogo = str_replace ('&amp;', '&', $bm_options['adminHeaderImage']);
?>
<style type="text/css">
	#header-logo {
		background-image: url(<?php echo $adminLogo; ?>) !important;
	}
</style>
<?php
	}
}


/**
 *
 */
function bm_listFonts () {

	$fonts = bm_fontSettings ();
	echo json_encode ($fonts);

}


/**
 * load the themes settings and set default values
 *
 * @param <type> $sections
 * @param <type> $settings
 * @return <type>
 */
function bm_loadSettings ($sections = null, $settings = null) {
	
	if ($sections == null) {
		return FALSE;
	}
	
	$options = array();	
	$tempOptions = (array) $settings;
	
	foreach ($sections as $s) {

		foreach ($s['fields'] as $f) {
			
			// if the database version of the content is empty
			if (!isset ($tempOptions[$f['var']]) || $tempOptions[$f['var']] === '' || $tempOptions[$f['var']] === NULL) {
			
				// use the default if it exists
				if (isset ($f['default'])) {
					$options[$f['var']] = $f['default'];
				} else {
					$options[$f['var']] = '';
				}

				// add any extra stuff that may be needed
				switch ($f['type']) {
				
					case 'uploadImage':
					
						$options[$f['var'] . 'Raw'] = $options[$f['var']];
						
						break;

					default:
						
						break;
				}
				
				
			// use the database saved version of the content
			} else {

				switch ($f['type']) {
				
					case 'uploadImage':
					
						if (isset ($f['actualWidth']) || isset ($f['actualHeight'])) {
							
							$thumbSettings = array ();
							
							if (isset ($f['actualWidth'])) {
								$thumbSettings[] = 'w=' . $f['actualWidth'];
							}
							
							if (isset ($f['actualHeight'])) {
								$thumbSettings[] = 'h=' . $f['actualHeight'];
							}
							
							$thumbSettings[] = 'src=' . urlencode (bm_muImageUploadPath ($tempOptions[$f['var']]));
							
							// set variable value
							$options[$f['var']] = BM_BLOGPATH . '/tools/timthumb.php?' . implode ('&amp;', $thumbSettings);
							
						} else {
						
							$options[$f['var']] = $tempOptions[$f['var']];
							
						}

						// store raw image path for use in admin, and anywhere else it's needed
						$options[$f['var'] . 'Raw'] = $tempOptions[$f['var']];

						break;
						
					default:
						
						if (is_array ($tempOptions[$f['var']])) {
							$options[$f['var']] = $tempOptions[$f['var']];
						} else {
							$options[$f['var']] = stripslashes ($tempOptions[$f['var']]);
						}
						
						break;
				}
				
			}
			
		}
		
	}
	
	return $options;

}


/**
 *
 */
function bm_adminFooter () {

	$sections = bm_adminSettings ();
	
?>
<div id="bm_colorPopup">
	<div id="bm_colorPicker" class="bm_colorPicker"></div>
</div>

<script type="text/javascript">
	var f;
	var colorPicker;
	var popup;
	
	jQuery(document).ready(function() {
		colorPicker = jQuery ('#bm_colorPicker');
		popup = jQuery ('#bm_colorPopup');
		f = jQuery.farbtastic ('#bm_colorPicker');
		
		jQuery ('.bm_colorInput').each (function () {
			f.linkTo (this);
		});
<?php
	// include farbtastic if required
	foreach ($sections as $s) {
		foreach ($s['fields'] as $f) {
			switch ($f['type']) {

				// drag and drop lists
				case 'drag_drop_list':
				case 'drag drop list':
?>
		jQuery("#sortable_<?php echo $f['var']; ?>_1, #sortable_<?php echo $f['var']; ?>_2").sortable({
			connectWith: '.connectedSort_<?php echo $f['var']; ?>',
			forcePlaceholderSize: true,
			placeholder: 'moving',
			update: function(event, ui) {
				var list_order = [];
				jQuery("#sortable_<?php echo $f['var']; ?>_1 li").each(function() {
					list_order.push(jQuery(this).attr('cat'));
				});
				jQuery('#<?php echo $f['var']; ?>').val(list_order.join(','));
			}
		}).disableSelection();
<?php
					break;

				// font selection
				case 'font':
?>
		jQuery('#<?php echo $f['var']; ?>').change (function () {
			fontName = jQuery (this).val ();
			bm_load_css (fontName);
			jQuery ('#fontFrame_<?php echo $f['var']; ?>').css ('font-family', bm_font_family (fontName));
		});
		fontName = jQuery ('#<?php echo $f['var']; ?>').val ();
		bm_load_css (fontName);
		jQuery ('#fontFrame_<?php echo $f['var']; ?>').css ('font-family', bm_font_family (fontName));
<?php
					break;

			}
		}
	}
?>
	});
</script>
<?php

}


/**
 * Add a theme page to the menu
 * Also saves data when things are submitted to the site
 *
 * @global <type> $bm_administration
 * @return <type>
 */
function bm_addThemePage () {

	if (defined ('BM_CUSTOM_ADMIN') && BM_CUSTOM_ADMIN == FALSE) {
		return FALSE;
	}
	
	global $bm_administration;
	$bm_administration = TRUE;
	
	$options = array();
	
	$pageList = array(
		basename (__FILE__),
		'admin-actions',
		'import-export',
	);
	
	add_option ('bm_actions', '', NULL, TRUE);
	add_option ('bm_options', '', NULL, TRUE);

	if (isset($_GET['page']) && in_array ($_GET['page'], $pageList) && $_POST != array ()) {
	
		switch ($_POST['bmAction']) {
		
			// save settings
			case 'saveOptions':
				
				if (check_admin_referer ('bm_options-options')) {
				
					$sections = bm_adminSettings ();
					$options = bm_processSettings ($sections);
					update_option ('bm_options', $options);
					
					// goto theme edit page
					header ('Location:admin.php?page=bm_adminFunctions.php&saved=true');
					die();
					
				}

				header('Location:admin.php?page=bm_adminFunctions.php');
				die();
				
				break;
			
			// save actions values
			case 'saveActions':
			
				if (check_admin_referer ('bm_options-options')) {
				
					$actions = bm_actionSettings ();
					
					foreach ($actions as $action) {
					
						$testVal = $_POST[$action['var']];
					
						if ($testVal != '') {
							$options[$action['var']] = $testVal;
						}
					
					}
					
					update_option ('bm_actions', $options);
				
					header ('Location:admin.php?page=admin-actions&saved=true');
					die();
				
				}
			
				header ('Location:admin.php?page=admin-actions');
				die();
				
				break;
			
			// reset theme to default settings
			case 'reset':
			
				delete_option ('bm_options');
				delete_option ('bm_actions');
				
				// goto theme edit page
				header('Location:admin.php?page=bm_adminFunctions.php&reset=true');
				die();
				
				break;
			
			// reset loaded widgets
			case 'resetWidgets':
			
				delete_option('sidebars_widgets');
				
				// goto theme edit page
				header('Location:admin.php?page=bm_adminFunctions.php&reset=true');
				die();
			
				break;

			// upload theme settings
			case 'uploadSettings':

				if (check_admin_referer ('bm_options-options')) {

					$upload = $_FILES['uploadedfile'];

					if ($upload['error'] > 0) {
						header ('Location:admin.php?page=import-export&error=' . $upload['error']);
					}

					$home_url = home_url ();
					$bm_options = file_get_contents ($upload['tmp_name']);

					$bm_options = base64_decode ($bm_options);
					$bm_options = str_replace ('[HOME_URL]', $home_url, $bm_options);
					$bm_options = unserialize ($bm_options);

					update_option ('bm_options', $bm_options['options']);
					update_option ('bm_actions', $bm_options['actions']);

					header ('Location:admin.php?page=import-export&success=1');

					die();

				}


				break;
			
			// do nothing
			default:
				
				die();

		}
	}

	//add_theme_page(Page Title, Menu Title, user level / capability, file, function name, icon url);
	$page = add_menu_page (
		ucwords (BM_THEMENAME),
		ucwords (BM_THEMENAME),
		BM_EDITTHEME,
		basename (__FILE__),
		'bm_themePage',
		BM_BLOGPATH . '/lib/styles/images/favicon.gif'
	);
	
	add_submenu_page (
		basename (__FILE__),
		BM_THEMENAME,
		__('Options', BM_THEMENAME),
		BM_EDITTHEME, basename(__FILE__),
		'bm_themePage'
	);

	// make sure the options can be displayed!
	if (!defined('THEME_DEMO') && function_exists ('bm_actionSettings')) {
		add_submenu_page(
			basename (__FILE__),
			__('Edit Action Properties', BM_THEMENAME),
			__('Extras', BM_THEMENAME),
			BM_EDITTHEME,
			'admin-actions',
			'bm_actionsPage'
		);
	}

	/*
	add_submenu_page (
		basename(__FILE__),
		__('Edit Custom Settings', BM_THEMENAME),
		__('Custom Settings', BM_THEMENAME),
		BM_EDITTHEME,
		'custom-settings',
		'bm_customSettings'
	);
	 */

	add_submenu_page (
		basename(__FILE__),
		__('Import & Export', BM_THEMENAME),
		__('Import & Export', BM_THEMENAME),
		BM_EDITTHEME,
		'import-export',
		'bm_importExport'
	);

	add_action ('admin_head', 'bm_adminHead');
	add_action ('admin_footer-' . $page, 'bm_adminFooter');

	// enqueue admin scripts
	wp_enqueue_script ('farbtastic');
	wp_enqueue_script ('jquery-ui-core');
	wp_enqueue_script ('jquery-ui-sortable');
	wp_enqueue_script ('jquery-ui-draggable');
	wp_enqueue_script ('jquery-ui-droppable');
	wp_enqueue_script ('jquery-ui-tabs');

	// enqueue admin styles
	wp_enqueue_style ('farbtastic');
	wp_enqueue_style ('bm_adminStyles', BM_BLOGPATH . '/lib/styles/admin.css');
	
}


/**
 * Display the theme settings page
 * 
 * add theme settings and changes to the system in a dynamic way
 * keeps things nice and generic whilst also making them flexible for future expansion
 *
 * @global <type> $bm_options
 */
function bm_themePage() {

	// display saved message
	if (!empty ($_GET['saved'])) {
		echo '<div id="message" class="updated fade"><p><strong>' . __('Settings saved', BM_THEMENAME) . '</strong></p></div>';
	}
	
	// display reset message
	if (!empty ($_GET['reset'])) {
		echo '<div id="message" class="updated fade"><p><strong>' . __('Settings reset', BM_THEMENAME) . '</strong></p></div>';
	}
	
	$sections = bm_adminSettings ();
	
	global $bm_options;
?>
<div class="wrap">
	<form method="post" enctype="multipart/form-data" class="bm_form">
<?php
	bm_adminTitle (__('Theme Options', BM_THEMENAME));
	echo '<div class="submitMain">';
	bm_input ('save', 'submit', '', __('Save Settings', BM_THEMENAME));
	echo '</div>';

	$count = 0;
	echo '<div id="bm_adminTabs">' . "\n";
	echo '<ul class="tabNav">';
	foreach ($sections as $s) {
		$count ++;
		$tabClass = '';
		if (isset ($s['class'])) {
			$tabClass = ' class="' . $s['class'] . '"';
		}
		echo '<li' . $tabClass . '><a href="#bmtab-' . $count . '">' . ucwords ($s['name']) . '</a></li>' . "\n";
	}
	echo '</ul>' . "\n";

	$count = 0;
	foreach ($sections as $s) {
		$count ++;
		echo '<div id="bmtab-' . $count . '" class="bm_fieldset">' . "\n";
		if ($s['description'] != '') {
			echo '<p class="settings_description">' . $s['description'] . '</p>';
		}
		bm_writeFields ($s['fields'], $bm_options);
		bm_input ('save', 'submit', '', __('Save Settings', BM_THEMENAME));
		echo '</div>' . "\n";
	}
	
	echo '<div style="clear:both;"></div>';
	echo '</div>';
	
	settings_fields('bm_options');
?>
		<input type="hidden" name="bmAction" value="saveOptions" />
		<input type="hidden" name="MAX_FILE_SIZE" value="100000" />
	</form>
	
	<form method="post">
		<h2><?php _e('Reset', BM_THEMENAME); ?></h2>
		<p><?php _e('If, for some crazy reason, you should wish to reset your themes properties then you can use the reset button below to free up the database info about this theme.', BM_THEMENAME); ?></p>
		<p class="submit"><a href="<?php echo home_url (); ?>/?download_settings" class="button">Backup Theme Settings</a> then <input type="submit" value="<?php _e('Reset', BM_THEMENAME); ?>" /></p>
		<input type="hidden" name="bmAction" value="reset" />
	</form>
</div>
	
	<!--
	<form method="post">
		<input type="hidden" name="bmAction" value="resetWidgets" />
		<p class="submit"><input type="submit" value="Reset widget settings" /></p>
	</form>
	-->
	<div class="clear" />
</div>
<?php

}


/**
 *
 * @param <type> $title
 */
function bm_adminTitle ($title = '') {

	if ($title != '') {
?>
	<div id="icon-options-general" class="icon32"><br></div>
	<h2><?php echo ucwords(BM_THEMENAME); ?>: <?php _e($title, BM_THEMENAME); ?></h2>
<?php
	}
?>
	<p class="supportLinks">
		<a href="http://accounts.prothemedesign.com/" target="_blank"><?php _e('Pro Theme Design Account', BM_THEMENAME); ?></a> &bull;
		<a href="http://prothemedesign.com/support/" target="_blank"><?php _e('Theme Support', BM_THEMENAME); ?></a> &bull; 
		<a href="http://prothemedesign.com/r/forum/" target="_blank"><?php _e('Pro Theme Design Forum', BM_THEMENAME); ?></a> &bull;
		<a href="http://prothemedesign.com/products/" target="_blank"><?php _e('More Themes', BM_THEMENAME); ?></a>
	</p>
<?php

}


/**
 *
 * @global <type> $bm_actions
 */
function bm_actionsPage () {
	
	// display saved message
	if (!empty($_GET['saved'])) {
		echo '<div id="message" class="updated fade"><p><strong>' . __('Settings saved', BM_THEMENAME) . '</strong></p></div>';
	}
?>
	<div class="wrap">
		<?php bm_adminTitle (__('Action Settings', BM_THEMENAME)); ?>
		<p style="clear:both;"><em><strong>Note:</strong> Elemental includes a variety of extra content areas made possible with "action hooks". Below are some editable regions that are placed throughout the theme for you to insert HTML or PHP code.</em></p>
		<div id="bm_adminTabs">
			<form method="post">
				<div class="bm_fieldset">
					<p class="bm_fieldsetTitle">
						<strong><?php _e('Edit site actions', BM_THEMENAME); ?></strong>
					</p>

					<div class="bm_fieldsetWrapper">
					<table width="100%" cellspacing="2" cellpadding="5"  class="editform form-table">
<?php
	$settings = bm_actionSettings ();
	global $bm_actions;
	
	foreach ($settings as $action) {
		bm_th ($action['name'], $action['var']);

		$content = '';
		if (isset ($bm_actions[$action['var']])) {
			$content = $bm_actions[$action['var']];
		}

		bm_input ($action['var'], 'textarea', '', stripslashes ($content));
		
		if ($action['description'] != '') {
			$action['description'] .= '<br /><br />';
		}
		$action['description'] .= '<em>' . __('Hook name', BM_THEMENAME) . ' = ' . $action['var'] . '</em>';
		bm_cth ($action['description']);
		
		register_setting ('bm_options', $action['var'], '');
	}
?>
					</table>
					</div>
					<?php bm_input ('save', 'submit', '', __('Save Settings', BM_THEMENAME)); ?>
				</div>
				<?php settings_fields ('bm_options'); ?>
				<input type="hidden" name="bmAction" value="saveActions" />
			</form>
		</div>
	</div>
<?php

}


/**
 *
 */
function bm_importExport () {

		// display saved message
	if (!empty ($_GET['error'])) {

		echo '<div id="message" class="updated fade"><p><strong>';
		
		switch ((int) $_GET['error']) {

			case UPLOAD_ERR_INI_SIZE:
				echo __('File exceeds allowed upload size', BM_THEMENAME);
				break;

			case UPLOAD_ERR_NO_FILE:
				echo __('No file was uploaded', BM_THEMENAME);
				break;

			case UPLOAD_ERR_NO_TMP_DIR:
				echo __('The file was not uploaded because there is no temp directory. For more help check with your web host.', BM_THEMENAME);
				break;

			case UPLOAD_ERR_CANT_WRITE:
				echo __('The file could not be saved to disk. For more help check with your web host.', BM_THEMENAME);
				break;

			default:
				echo __('Error uploading settings file', BM_THEMENAME);
		}

		echo '</strong></p></div>';

	}

	if (!empty ($_GET['success'])) {
		echo '<div id="message" class="updated fade"><p><strong>';
		echo __('Settings uploaded successfully', BM_THEMENAME);
		echo '</strong></p></div>';
	}

?>
	<div class="wrap">
		<?php bm_adminTitle (__('Import / Export Theme Settings', BM_THEMENAME)); ?>
		<div class="bm_block_50 bm_clear">
			<h3>Export</h3>
			<p>Backup your themes settings. Note that widgets and navigation bars are not backed-up.</p>
			<a href="<?php echo home_url (); ?>/?download_settings" class="button">Backup Theme Settings</a>
		</div>
		<div class="bm_block_50">
			<h3>Import</h3>
			<p></p>
			<form enctype="multipart/form-data" method="POST">
				<input type="hidden" name="MAX_FILE_SIZE" value="50000" />
				<input name="uploadedfile" type="file" />
				<?php bm_input ('save', 'submit', '', __('Upload Settings', BM_THEMENAME)); ?>
				<input type="hidden" name="bmAction" value="uploadSettings" />
				<?php settings_fields('bm_options'); ?>
			</form>
		</div>
	</div>
<?php

}


/**
 *
 */
function bm_customSettings () {
	
	// display saved message
	if ($_GET['saved']) {
		echo '<div id="message" class="updated fade"><p><strong>' . __('Settings saved', BM_THEMENAME) . '</strong></p></div>';
	}
?>
	<div class="wrap">
		<?php bm_adminTitle (__('Custom Settings', BM_THEMENAME)); ?>
		<div id="bm_adminTabs">
			<ul class="tabNav">
				<li><a href="#bmtab-0">Custom CSS</a></li>
				<li><a href="#bmtab-1">Custom PHP</a></li>
			</ul>

			<form method="post">
				<div id="bmtab-0" class="bm_fieldset">
					<p class="bm_fieldsetTitle">custom/style.css</p>
					<div class="bm_fieldsetWrapper">
						<table width="100%" cellspacing="2" cellpadding="5"  class="editform form-table">
<?php
		bm_th ('Edit CSS', 'customCSS');
		$value = '';
		$file = bm_locateTemplate ('style', 'custom/', '.css');
		if (!empty($file)) {
			$value = file_get_contents ($file);
		}
		bm_input ('customCSS', 'textarea', '', $value);
		bm_cth ();
?>
						</table>
					</div>
				</div>
				<div id="bmtab-1" class="bm_fieldset">
					<p class="bm_fieldsetTitle">custom/functions.php</p>
					<div class="bm_fieldsetWrapper">
						<table width="100%" cellspacing="2" cellpadding="5"  class="editform form-table">
<?php
		bm_th ('Edit PHP', 'customPHP');
		$value = '';
		$file = bm_locateTemplate ('functions', 'custom/');
		if (!empty($file)) {
			$value = file_get_contents ($file);
		}
		bm_input ('customPHP', 'textarea', '', $value);
		bm_cth ();
?>
						</table>
					</div>
				</div>
			</form>
		</div>
	</div>
<?php
}


/**
 *
 * @global <type> $post
 */
function bm_metaPostTemplate () {
	
	global $post;

	$currentValues = get_post_meta ($post->ID, 'bm_postSettings', true);
	$meta_boxes = apply_filters ('bm_postMetaSettings', bm_postMetaSettings ());
	echo '<input type="hidden" name="bm_postMetaSettingsSave" value="true" />';
	foreach ($meta_boxes as $m) {
		bm_writeFields ($m['fields'], $currentValues, 2);
	}
	
}


/**
 *
 * @global <type> $post
 * @param <type> $post_id
 * @return <type>
 */
function bm_metaSave ($post_id) {

	if (function_exists ('bm_postMetaSettings')) {
		global $post;

		if (isset ($_POST['bm_postMetaSettingsSave'])) {
			$bm_postMeta = apply_filters ('bm_postMetaSettings', bm_postMetaSettings ());
		
			update_post_meta ($post_id, 'bm_postSettings', bm_processSettings ($bm_postMeta));
		}
	}
	
	return $post_id;
	
}


/**
 *
 */
function bm_metaCreate () {

	if (function_exists ('bm_postMetaSettings')) {
		add_meta_box ('bm_meta', __('Custom Post Details', BM_THEMENAME), 'bm_metaPostTemplate', 'post');
		add_meta_box ('bm_meta', __('Custom Page Details', BM_THEMENAME), 'bm_metaPostTemplate', 'page');
	}
}


/**
 *
 * @param <type> $contactMethods
 * @return <type>
 */
function bm_authorContact ($contactMethods) {

	$contactMethods['twitter'] = 'Twitter Username';
	
	return $contactMethods;
	
}

add_filter ('user_contactmethods', 'bm_authorContact', 10, 1);

?>