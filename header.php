<?php
	header('X-UA-Compatible: IE=EmulateIE7');
	global $bm_options;
	bm_preContent();
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" <?php language_attributes(); ?>>
<head profile="http://gmpg.org/xfn/11">
<meta http-equiv="Content-Type" content="<?php bloginfo('html_type'); ?>; charset=<?php bloginfo('charset'); ?>" />
<title><?php bm_title (); ?></title>
<?php
if (is_singular ()) {
	if (have_posts ()) {
		while (have_posts ()) {
			the_post ();
?>
<meta name="description" content="<?php the_excerpt_rss (); ?>" />
<?php
		}
	}
} elseif (is_home ()) { ?>
<meta name="description" content="<?php bloginfo ('description'); ?>" />
<?php
}

if(is_home () || is_single () || is_page ()) {
	echo '<meta name="robots" content="index,follow" />';
} else {
	echo '<meta name="robots" content="noindex,follow" />';
}

if (is_single ()) {
	global $primaryPostData;
	$primaryPostData['ID'] = $post->ID;
	$primaryPostData['author'] = $post->post_author;
}
?>
<link rel="stylesheet" href="<?php bloginfo ('stylesheet_url'); ?>" type="text/css" media="screen,projection" />
<link rel="stylesheet" href="<?php bloginfo ('template_url'); ?>/css/print.css" type="text/css" media="print" />
<link rel="shortcut icon" href="<?php bloginfo ('template_url'); ?>/images/favicon.ico" />
<link rel="alternate" type="application/rss+xml" title="<?php bloginfo ('name'); ?>" href="<?php bloginfo ('rss2_url'); ?>" />
<link rel="pingback" href="<?php bloginfo ('pingback_url'); ?>" />
<?php
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
	wp_head ();
?>

<link rel="stylesheet" href="<?php bloginfo('template_url'); ?>/css/responsive.css" type="text/css" media="all" />


</head>

<body <?php bm_bodyClass (); ?>>
<?php
	bm_doAction ('bm_pageTop');
?>
<div id="top">
<div class="wrapper masthead">
<div id="branding">
<?php
	if (is_home () && !is_paged ()) {
?>
    <div id="logo">
		<?php bloginfo ('name'); ?>
    </div>
    <h1 id="description">
		<?php bloginfo ('description'); ?>
    </h1>
<?php
	} else {
?>
    <div id="logo">
		<a href="<?php echo home_url (); ?>/" title="<?php _e('Home','accumulo'); ?>"><?php bloginfo ('name'); ?></a>
	</div>
    <div id="description">
		<?php bloginfo ('description'); ?>
    </div>
<?php
	}
?>
  </div><!--/branding-->
<?php
	bm_navigation ('navHeader');
?>
</div><!--/wrapper-->
</div><!--/top-->


<div id="main" class="clearfix">
<?php
	bm_banner ('header');
?>
