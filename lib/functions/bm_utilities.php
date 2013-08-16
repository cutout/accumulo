<?php

/**
 * add a post id to the ignore list for future query_posts
 *
 * @global array $bmIgnorePosts list of posts to ignore
 * @param <type> $id
 */
function bm_ignorePost ($id) {

	if (!is_page()) {
		global $bmIgnorePosts;
		$bmIgnorePosts[] = $id;
	}
	
}


/**
 * reset the ignore post list
 *
 * @global array $bmIgnorePosts list of posts to ignore
 */
function bm_ignorePostReset () {

	global $bmIgnorePosts;
	$bmIgnorePosts = array();
	
}


/**
 * load a special plugin from the elemental plugins directory
 * 
 * @param <type> $plugin 
 */
function bm_loadPlugin ($plugin) {

	global $bm_plugins;

	$plugin = strtolower ($plugin);
	$plugin = str_replace (' ', '-', $plugin);

	if (!in_array ($plugin, $bm_plugins)) {

		if (!@include_once (BM_LIB . 'plugins/' . $plugin . '.php')) {
			bm_adminMessage (sprintf (__('Elemental Plugin does not exist : %s', BM_THEMENAME), $plugin));
			die ();
		} else {
			$bm_plugins[] = $plugin;
		}

	}

}


/**
 *
 * @global <type> $bmCategoryList
 * @global <type> $bmMultiSelectList
 * @param <type> $params
 * @return <type>
 */
function bm_getCategories ($params = 'hide_empty=0') {

	global $bmCategoryList, $bmMultiSelectList;
	
	if ($bmCategoryList == NULL) {

		// complete list of categories
		$bmCategoryList = get_terms ('category', $params);

		// function goes crazy if there's too many categories
		if (count ($bmCategoryList) < 50) {
			$temp = array ();
			$temp2 = array ();
			$sorted_keys = array ();
			$count = 0;

			// get the highest level of items
			foreach ($bmCategoryList as $c) {
				if ((int) $c->parent == 0) {
					$temp[] = $c;
				}
			}
			
			// no point sorting if there's no children!
			if (count($bmCategoryList) != count($temp)) {

				// put a small limit on it so it doesn't endlessly loop
				// 5 should be plenty for 99% of sites
				while ($count < 5) {

					$count ++;

					foreach ($temp as $t) {
						$temp2[] = $t;
						foreach ($bmCategoryList as $c) {
							if (!in_array ($c->parent, $sorted_keys)) {
								if ($c->parent == $t->term_id) {
									$temp2[] = $c;
								}
							}
						}
						$sorted_keys[$t->term_id] = $t->term_id;
					}

					$temp = $temp2;
					$temp2 = array ();

					if (count ($bmCategoryList) == count ($temp)) {
						$count = 999;
					}

				}

				$bmCategoryList = $temp;
			}
		}

		// sort out name and indentation
		$bmMultiSelectList[] = array (0, __('All categories (default)', BM_THEMENAME));
		foreach ($bmCategoryList as $c) {
			$name = bm_categoryPrefix ($c->parent, $bmCategoryList) . $c->name;
			$bmMultiSelectList[] = array ($c->term_id, $name);
		}
	}
	
	return $bmMultiSelectList;
	
}


/**
 * 
 * @param <type> $id
 * @param <type> $categories
 * @param string $prefix
 * @return string 
 */
function bm_categoryPrefix ($id, $categories, $prefix = '') {

	if ($id != '0') {
		$prefix .= '&nbsp;&nbsp;&nbsp;';

		foreach ($categories as $c) {

			if ($c->term_id == $id) {
				if (!empty ($c->parent)) {
					return bm_categoryPrefix ($c->parent, $categories, $prefix);
					break;
				}
			}

		}
	}

	return $prefix;
	
}


/**
 * get the data about an individual category from a list of loaded categories
 * 
 * @global <type> $bmCategoryList
 * @param <type> $id
 * @return <type>
 */
function bm_categoryDetails ($id) {
	
	bm_getCategories ();
	
	global $bmCategoryList;

	foreach ($bmCategoryList as $cat) {
		if ($cat->term_id == $id) {
			return $cat;
		}
	}
	
}


/**
 * get the a list of post templates
 * 
 * @return <type>
 */
function bm_getTemplates () {

	$theme = wp_get_theme();
	$templates = $theme['Template Files'];
	
	// set default
	$templateList = array(
		array('', __('Right Sidebar (default)', BM_THEMENAME)),
	);

	// find all the others
	if (is_array ($templates)) {
		foreach ($templates as $template) {
			if (strpos ($template, WP_CONTENT_DIR) === false) {
				// not found
				$data = file_get_contents (WP_CONTENT_DIR . $template);
			} else {
				// found
				$data = file_get_contents ($template);
			}
			
			preg_match ('|Post Template Name:(.*)$|mi', $data, $name);
			if (!empty ($name)) {
				$name = $name[1];
				$templateList[$name] = array(
					basename ($template),
					trim ($name),
				);
			}
		}
	}
	
	return $templateList;

}


/**
 * remove the posts from query_posts
 *
 * @global array $bmIgnorePosts
 * @global <type> $wpdb
 * @param string $where
 * @return string 
 */
function bm_postStrip ($where) {

	global $bmIgnorePosts, $wpdb;

	if (strpos ($where, 'topic') === false) {
	
		if (count ($bmIgnorePosts) > 0) {
			$where .= ' AND ' . $wpdb->posts . '.ID NOT IN(' . implode (',', $bmIgnorePosts) . ') ';
		}
		
	}
	
	return $where;
	
}


/**
 * Convert a url to a tiny url
 *
 * @param string $url the url to convert to a tiny url
 */
function bm_getTinyUrl ($url) {
	
	return bm_getContent ('http://tinyurl.com/api-create.php?url=' . $url, 9999999, 'tinyUrl');
	
}


/**
 * get the text/ html from the specified domain
 * 
 * @param string $url the url to load the content from
 * @param int $cacheTime the maximum age of the cached content before it's refreshed
 * @param string $prefix an optional prefix to make the cache files individual
 * @return string
 */
function bm_getContent ($url, $cacheTime = 60, $prefix = 'getContent') {
	
	$cachename = $prefix . '_' . md5 ($url . $cacheTime);
	$content = '';
	
	if (!$content = bm_cacheGet ($cachename, $cacheTime)) {

		$request = new WP_Http;
		$result = $request->request ($url);
		
		if (is_wp_error ($result)) {
			bm_adminMessage (__('Error connecting to remote host. Please try again later.', BM_THEMENAME));
		} else {
			$content = $result['body'];
		}
		
		bm_cachePut ($cachename, $content);
		
	}

	if ($content != '') {
		return (string) $content;
	} else {
		return FALSE;
	}
	
}


/**
 * Get the parents of a category and update the bm_crumblinks array with the result
 *
 * @param int $id the id of the category
 */
function bm_get_category_parents ($id) {

	global $bm_crumbLinks;

	$parent = &get_category ($id);
	
	if (!is_wp_error ($parent)) {
		if ($parent->parent && ($parent->parent != $parent->term_id)) {
			bm_get_category_parents ($parent->parent);
		}
	}

	$bm_crumbLinks[] = array($parent->name, get_category_link ($parent->term_id));
	
	return true;
	
}


/**
 * convert a string into a nice clean name
 *
 * @param <type> $cleanNameString
 * @return <type>
 */
function bm_cleanName ($cleanNameString) {

	$cleanNameString = str_replace ('-', ' ', $cleanNameString);
	$cleanNameString = str_replace ('_', ' ', $cleanNameString);
	$cleanNameString = str_replace ('.php', '', $cleanNameString);
	$cleanNameString = trim ($cleanNameString);

	return $cleanNameString;

}


/**
 * work out the layout of a comment
 *
 * @global <type> $post
 * @global  $bm_options
 * @global <type> $post
 * @param <type> $comment
 * @param <type> $args
 * @param <type> $depth
 */
function bm_commentLayout ($comment, $args, $depth) {

	$GLOBALS['comment'] = $comment;
	
?>
	<li <?php comment_class (); ?> id="li-comment-<?php comment_ID (); ?>">
<?php
	
	switch($comment->comment_type) {
		
		// trackbacks and pingbacks
		case 'trackback':
		case 'pingback':
?>
			<div class="comment-trackback">
				<img src="<?php echo bm_getFavicon (get_comment_author_url ()); ?>" width="16" height="16" class="commentFavicon" alt="*" />
				<?php echo get_comment_author_link (); ?>
			</div>
<?php
			break;
			
		// normal comment
		default:
		
			global $post, $bm_options;
			
			$twittername = '';
			$commentAuthorUrl = get_comment_author_url ();

			if ($bm_options['TwitterAnywhere'] != '') {
				// calculate twitter info if required
				if ($comment->user_id > 0) {
					$auth = get_userdata ($comment->user_id);
				}
				
				if (isset ($auth->twitter)) {
					$twittername = $auth->twitter;
				} else {
					$twitter_user_array = get_comment_meta (get_comment_ID (), 'twitter_user');
					if (isset ($twitter_user_array[0])) {
						$twittername = $twitter_user_array[0];
					}
				}
			}
			
			$commentDate = get_comment_date ();			
			
			if ($comment->comment_approved == 0) {
?>
		<p class="success"><?php _e('Your comment is awaiting moderation', BM_THEMENAME); ?></p>
<?php
			}
?>
		<div id="comment-<?php comment_ID (); ?>" class="commentWrapper">
<?php
			// can (should?) add default as well
			if ($commentAuthorUrl != '') {
?>
				<a href="<?php echo $commentAuthorUrl; ?>" class="gravatar">
<?php
			}

			echo get_avatar ($comment->comment_author_email, $args['avatar_size'], $bm_options['defaultGravatarRaw']);
			
			if ($commentAuthorUrl != '') {
?>
				</a>
<?php
			}
			if ($comment->user_id > 0) {
?>
				<a href="<?php echo get_author_posts_url ($comment->user_id, $comment->comment_author); ?>" class="comment_author_profile"><?php _e('Profile', BM_THEMENAME); ?></a>
<?php
			}
?>
			<div class="comment-author comment-meta commentmetadata">
				<cite><?php echo get_comment_author_link (); ?></cite>
<?php
			if ($twittername != '') {
?>
				<a class="twitter_anywhere" href="http://twitter.com/<?php echo $twittername; ?>" rel="external nofollow">@<?php echo $twittername; ?></a>
<?php
			}
?>
				<span class="commentDate">
					<a href="<?php echo get_comment_link ($comment->comment_ID); ?>" rel="nofollow"><?php echo $commentDate; ?></a>
				</span>

				<span class="actions">
<?php
			$replySettings = array(
				'reply_text' => __('Reply &rsaquo;'),
				'depth' => $depth,
				'max_depth' => $args['max_depth'],
			);
			
			global $post;
			comment_reply_link (array_merge ($args, $replySettings));
			
			if (current_user_can ('edit_post', $post->ID)) {
				echo '<a href="' . admin_url ('comment.php?action=cdc&c=' . get_comment_id ()) . '">' . __('Delete &rsaquo;', BM_THEMENAME) . '</a> ';
				echo '<a href="' . admin_url ('comment.php?action=cdc&dt=spam&c=' . get_comment_id ()) . '">' . __('Spam &rsaquo;', BM_THEMENAME) . '</a>';
			}
			
			edit_comment_link (__('Edit &rsaquo;', BM_THEMENAME), ' ', '');
?>
				</span>
			</div>
			<?php comment_text (); ?>
		</div>
<?php
	}

}


/**
 *
 * @return <type>
 */
function bm_getCurrentTwitterUser () {
	
	$comment_twitterUser = '';
	
	if (isset ($_COOKIE['comment_twitteruser_' . COOKIEHASH])) {
		$comment_twitterUser = $_COOKIE['comment_twitteruser_' . COOKIEHASH];
		$comment_twitterUser = stripslashes ($comment_twitterUser);
		$comment_twitterUser = esc_attr ($comment_twitterUser);
	}
	
	return $comment_twitterUser;
	
}


/**
 * 
 * @global <type> $user
 * @param <type> $post_id
 */
function bm_saveTwitterUser ($post_id) {

	global $user;
	
	// only save the twitter details for users who are not logged in
	if (!is_user_logged_in ()) {
	
		// if an account name is specified
		if (isset($_POST['twitter_username'])) {
			// save cooke for...
			$comment_cookie_lifetime = apply_filters('comment_cookie_lifetime', 30000000);
			// sanitize username
			$twitter_user = esc_html ($_POST['twitter_username']);
			$twitter_user = str_replace ('http://twitter.com/', '', $twitter_user);
			$twitter_user = str_replace ('@', '', $twitter_user);
			// save cookie to use in comment form
			setcookie ('comment_twitteruser_' . COOKIEHASH, $twitter_user, time () + $comment_cookie_lifetime, COOKIEPATH, COOKIE_DOMAIN);
			// save name to db
			add_comment_meta ($post_id, 'twitter_user', $twitter_user, true);
		}
	
	}
	
}


/**
 * set default arguments for widgets
 *
 * @param <type> $args
 * @param <type> $defaults
 * @return <type>
 */
function bm_defaultArgs ($args = array (), $defaults = array ()) {

	if ($args == array ()) {
		$args = array (
			'before_widget' => '',
			'before_title' => '<div class="clear"><h3 class="widgettitle"><span>',
			'after_title' => '</span></h3></div>',
			'after_widget' => '',
		);
	}
	
	if ($defaults != array()) {
		foreach ($defaults as $key => $value) {
			if (! isset ($args[$key]) || $args[$key] == '') {
				$args[$key] = $value;
			}
		}
	}

	return apply_filters ('bm_widgetDefaultArguments', $args);
	
}


/**
 * is a number odd or not
 *
 * @param <type> $number
 * @return <type>
 */
function bm_isOdd ($number) {

	if ($number & 1) {
		return 'odd';
	} else {
		return 'even';
	}

}


/**
 * echo the 'time since' a certain time
 *
 * @param <type> $original
 * @param <type> $echo
 * @return <type>
 */
function bm_timeSince ($original, $echo = FALSE, $gmt = 0) {

	$timeSince = sprintf (__('about %s ago', BM_THEMENAME), human_time_diff ($original, current_time ('timestamp', $gmt)));

	if ($echo) {
		echo $timeSince;
	} else {
		return $timeSince;
	}
	
}


/**
 * filter to hide pages from public display
 *
 * @global <type> $bm_options
 * @param <type> $args
 * @return <type>
 */
function bm_excludePages ($args = array()) {

	global $bm_options;
	
	if ($bm_options['hidePages'] == NULL) {
		return $args;
	} else {
		return array_merge($args, $bm_options['hidePages']);
	}

}


/**
 * Based on the seo slugs plugin by Andrei Mikrukov 2007
 * Download the original here - http://wordpress.org/extend/plugins/seo-slugs/
 *
 * @global <type> $bm_options
 * @param <type> $slug
 * @return <type>
 */
function bm_seoSlugs ($slug = '') {

	global $bm_options;
	
	if ($bm_options['seoOptimizeSlug'] == 0) {
		return $slug;
	}

	if ($slug != '') {
		return $slug;
	}
	
	$seoSlugs = array (
		"a", "able", "about", "above", "abroad", "according", "accordingly", "across", "actually", "adj", "after", "afterwards", "again", "against", "ago", "ahead", "ain't", "all", "allow", "allows", "almost", "alone", "along", "alongside", "already", "also", "although", "always", "am", "amid", "amidst", "among", "amongst", "an", "and", "another", "any", "anybody", "anyhow", "anyone", "anything", "anyway", "anyways", "anywhere", "apart", "appear", "appreciate", "appropriate", "are", "aren't", "around", "as", "a's", "aside", "ask", "asking", "associated", "at", "available", "away", "awfully", "b", "back", "backward", "backwards", "be", "became", "because", "become", "becomes", "becoming", "been", "before", "beforehand", "begin", "behind", "being", "believe", "below", "beside", "besides", "best", "better", "between", "beyond", "both", "brief", "but", "by", "c", "came", "can", "cannot", "cant", "can't", "caption", "cause", "causes", "certain", "certainly", "changes", "clearly", "c'mon", "co", "co.", "com", "come", "comes", "concerning", "consequently", "consider", "considering", "contain", "containing", "contains", "corresponding", "could", "couldn't", "course", "c's", "currently", "d", "dare", "daren't", "definitely", "described", "despite", "did", "didn't", "different", "directly", "do", "does", "doesn't", "doing", "done", "don't", "down", "downwards", "during", "e", "each", "edu", "eg", "eight", "eighty", "either", "else", "elsewhere", "end", "ending", "enough", "entirely", "especially", "et", "etc", "even", "ever", "evermore", "every", "everybody", "everyone", "everything", "everywhere", "ex", "exactly", "example", "except", "f", "fairly", "far", "farther", "few", "fewer", "fifth", "first", "five", "followed", "following", "follows", "for", "forever", "former", "formerly", "forth", "forward", "found", "four", "from", "further", "furthermore", "g", "get", "gets", "getting", "given", "gives", "go", "goes", "going", "gone", "got", "gotten", "greetings", "h", "had", "hadn't", "half", "happens", "hardly", "has", "hasn't", "have", "haven't", "having", "he", "he'd", "he'll", "hello", "help", "hence", "her", "here", "hereafter", "hereby", "herein", "here's", "hereupon", "hers", "herself", "he's", "hi", "him", "himself", "his", "hither", "hopefully", "how", "howbeit", "however", "hundred", "i", "i'd", "ie", "if", "ignored", "i'll", "i'm", "immediate", "in", "inasmuch", "inc", "inc.", "indeed", "indicate", "indicated", "indicates", "inner", "inside", "insofar", "instead", "into", "inward", "is", "isn't", "it", "it'd", "it'll", "its", "it's", "itself", "i've", "j", "just", "k", "keep", "keeps", "kept", "know", "known", "knows", "l", "last", "lately", "later", "latter", "latterly", "least", "less", "lest", "let", "let's", "like", "liked", "likely", "likewise", "little", "look", "looking", "looks", "low", "lower", "ltd", "m", "made", "mainly", "make", "makes", "many", "may", "maybe", "mayn't", "me", "mean", "meantime", "meanwhile", "merely", "might", "mightn't", "mine", "minus", "miss", "more", "moreover", "most", "mostly", "mr", "mrs", "much", "must", "mustn't", "my", "myself", "n", "name", "namely", "nd", "near", "nearly", "necessary", "need", "needn't", "needs", "neither", "never", "neverf", "neverless", "nevertheless", "new", "next", "nine", "ninety", "no", "nobody", "non", "none", "nonetheless", "noone", "no-one", "nor", "normally", "not", "nothing", "notwithstanding", "novel", "now", "nowhere", "o", "obviously", "of", "off", "often", "oh", "ok", "okay", "old", "on", "once", "one", "ones", "one's", "only", "onto", "opposite", "or", "other", "others", "otherwise", "ought", "oughtn't", "our", "ours", "ourselves", "out", "outside", "over", "overall", "own", "p", "particular", "particularly", "past", "per", "perhaps", "placed", "please", "plus", "possible", "presumably", "probably", "provided", "provides", "q", "que", "quite", "qv", "r", "rather", "rd", "re", "really", "reasonably", "recent", "recently", "regarding", "regardless", "regards", "relatively", "respectively", "right", "round", "s", "said", "same", "saw", "say", "saying", "says", "second", "secondly", "see", "seeing", "seem", "seemed", "seeming", "seems", "seen", "self", "selves", "sensible", "sent", "serious", "seriously", "seven", "several", "shall", "shan't", "she", "she'd", "she'll", "she's", "should", "shouldn't", "since", "six", "so", "some", "somebody", "someday", "somehow", "someone", "something", "sometime", "sometimes", "somewhat", "somewhere", "soon", "sorry", "specified", "specify", "specifying", "still", "sub", "such", "sup", "sure", "t", "take", "taken", "taking", "tell", "tends", "th", "than", "thank", "thanks", "thanx", "that", "that'll", "thats", "that's", "that've", "the", "their", "theirs", "them", "themselves", "then", "thence", "there", "thereafter", "thereby", "there'd", "therefore", "therein", "there'll", "there're", "theres", "there's", "thereupon", "there've", "these", "they", "they'd", "they'll", "they're", "they've", "thing", "things", "think", "third", "thirty", "this", "thorough", "thoroughly", "those", "though", "three", "through", "throughout", "thru", "thus", "till", "to", "together", "too", "took", "toward", "towards", "tried", "tries", "truly", "try", "trying", "t's", "twice", "two", "u", "un", "under", "underneath", "undoing", "unfortunately", "unless", "unlike", "unlikely", "until", "unto", "up", "upon", "upwards", "us", "use", "used", "useful", "uses", "using", "usually", "v", "value", "various", "versus", "very", "via", "viz", "vs", "w", "want", "wants", "was", "wasn't", "way", "we", "we'd", "welcome", "well", "we'll", "went", "were", "we're", "weren't", "we've", "what", "whatever", "what'll", "what's", "what've", "when", "whence", "whenever", "where", "whereafter", "whereas", "whereby", "wherein", "where's", "whereupon", "wherever", "whether", "which", "whichever", "while", "whilst", "whither", "who", "who'd", "whoever", "whole", "who'll", "whom", "whomever", "who's", "whose", "why", "will", "willing", "wish", "with", "within", "without", "wonder", "won't", "would", "wouldn't", "x", "y", "yes", "yet", "you", "you'd", "you'll", "your", "you're", "yours", "yourself", "yourselves", "you've", "z", "zero"
	);

	$seo_slug = strtolower (stripslashes($_POST['post_title']));

	$seo_slug = preg_replace ('/&.+?;/', '', $seo_slug);
	$seo_slug = preg_replace ('/[^a-zA-Z0-9 \']/', '', $seo_slug);
	$seo_slug_array = array_diff (split(' ', $seo_slug), $seoSlugs);
	$seo_slug = implode('-', $seo_slug_array);

	return $seo_slug;
	
}


/**
 * tidy up the search url
 */
function bm_niceSearch () {

    if (is_search ()) {
		if (isset ($_GET['x']) && isset ($_GET['y'])) {
        	wp_redirect (get_bloginfo ('home') . '?s=' . str_replace (' ', '+', str_replace ('%20', '+', get_query_var ('s'))));
        	die();		
		}
    }
    
}

/**
 *
 * @return <type>
 */
function bm_scriptSettings () {
	
	global $bm_options;

	$scripts = array ();

	// javascript - optional
	$scripts = array (
		'hoverIntent' => array (
			'hoverIntent',
			BM_BLOGPATH . '/scripts/hoverIntent.js',
		),
		'superfish' => array (
			'superfish',
			BM_BLOGPATH . '/scripts/superfish.js',
		),
		'printPreview' => array (
			'printPreview',
			BM_BLOGPATH . '/scripts/printPreview.js',
		),
		'htmlEditor' => array (
			'htmlEditor',
			BM_BLOGPATH . '/scripts/htmlEditor.js'
		),
	);
	
	if ($bm_options['TwitterAnywhere'] != '') {
		$scripts['twitterAnywhere'] = array (
			'twitterAnywhere',
			'http://platform.twitter.com/anywhere.js?id=' . $bm_options['TwitterAnywhere'] . '&v=1'
		);
	}
	
	return apply_filters ('bm_loadScripts', $scripts);
	
}


/**
 * que up all of the javascripts to display on the site (in the footer)
 */
function bm_loadScripts () {

	if (!is_admin ()) {
		$scripts = (array) bm_scriptSettings ();

		if (count ($scripts) > 0) {
			foreach ($scripts as $script) {
				wp_enqueue_script ($script[0], $script[1], array (), false, true);
			}
		}
	}
	
}


/**
 * define a property if it hasn't been defined already
 *
 * @param <type> $name
 * @param <type> $value
 * @return <type>
 */
function bm_define ($name = '', $value = '') {

	if (empty ($name) || $value === '') {
		return FALSE;
	}

	if (!defined ($name)) {
		define ($name, $value);
	}
	
	return TRUE;

}


/**
 *
 * @param <type> $output
 * @return <type>
 */
function bm_passwordForm ($output) {

	return str_replace ('<form', '<form class="bm_passWordProtectForm"', $output);

}


/**
 *
 * @param <type> $ret
 * @return <type>
 */
function bm_shrinkUrl ($ret) {

	$maxLength = 55;
	$theLength = strlen ($ret);
	
	if ($theLength > $maxLength) {
		$urlInfo = parse_url ($ret);
		$deleteChars = $maxLength - strlen ($urlInfo['host']) - 10; // the 10 counts for the additional characters http:// /..
		$ret = 'http://' . $urlInfo['host'] . '/...' . substr ($ret, -$deleteChars);
	}
	
	return $ret;
}


/**
 *
 * @param <type> $ret
 * @return <type>
 */
function bm_makeUrlClickable ($ret) {

	$ret = ' ' . $ret . ' ';
	$ret = preg_replace ("/([\s>])(https?):\/\/([^\s<>{}()]+[^\s.,<>{}()])/ie", "'\\1<a href=\'\\2://\\3\'>'.bm_shrinkUrl('\\2://\\3').'</a>'", $ret);
	$ret = preg_replace ("/(\s)www\.([a-z0-9\-]+)\.([a-z0-9\-.\~]+)((?:\/[^ <>{}()\n\r]*[^., <>{}()\n\r]?)?)/ie", "'\\1<a href=\'http://www.\\2.\\3\\4\'>'.bm_shrinkUrl('www.\\2.\\3\\4').'</a>'", $ret);
	$ret = preg_replace ("/(\s)([a-z0-9\-_.]+)@([^,< \n\r]+)/ie", "'\\1<a href=\"mailto:$2@$3\">'.bm_shrinkUrl('$2@$3').'</a>'", $ret);
	$ret = trim ($ret);
	
	return $ret;
	
}


/**
 *
 * @param <type> $more_link
 * @param <type> $more_link_text
 * @return <type>
 */
function bm_moreLink ($more_link, $more_link_text) {

	return str_replace ($more_link_text, __('Continue reading &rsaquo;', BM_THEMENAME), $more_link);
	
}


/**
 * cachePut
 * Save Cache files to cache directory
 *
 * @param string $id cache key - unique string for saving and retrieving cache data
 * @param mixed $data data to save to cache
 * @return boolean
 */
function bm_cachePut ($id = '', $data = NULL) {
	
	// make sure required values are visible
	if ($id == '' || $data == NULL) {
		return FALSE;
	}
	
	if ($handle = @fopen (bm_cacheName ($id), 'w')) {
	
		fwrite ($handle, serialize ($data));
		fclose ($handle);
		
		return true;
		
	} else {
	
		echo '<!-- error: can not open file -->';
		
	}
	
	return false;
	
}


/**
 * cacheGet
 * Retreive the cache using the unique key specifying an expiration age so that the cache can be refreshed
 *
 * @param string $id cache key - unique string for saving and retrieving cache data
 * @param integer $expires time in seconds that the cache file can live for before being refreshed
 * @return array
 */
function bm_cacheGet ($id = '', $expires = 0) {

	if ($expires == 0) {
		$expires = BM_CACHE_TIME;
	}
	
	// add on random 10 percent of the expire time to add some randomness
	// will mean all caches on one page for same time frame do not expire at the same time
	$expires = $expires + ceil (rand (1, ($expires / 10)));

	$filename = bm_cacheName ($id);
	$filenameExists = file_exists ($filename);
	
	if ($filenameExists) {
		$age = (time () - filemtime ($filename));
		if ($age < $expires) {
			$data = file_get_contents ($filename);
			return unserialize ($data);
		}
	}
	
	return FALSE;
	
}


/**
 * cacheName
 * Convert unique key into unique filename to save the cache to
 *
 * @param string $id cache key - unique string for saving and retrieving cache data
 * @return string
 */
function bm_cacheName ($id = '') {

	$id = strtolower ($id);
	$id = str_replace (' ', '_', $id);

	return BM_CACHE_DIR . $id . '_cache.txt';
	
}


/**
 * cacheKill
 * delete the cache file for a specified cache key
 * 
 * @param string $id cache key - unique string for saving and retrieving cache data
 */
function bm_cacheKill ($id = '') {
	
	$filename = bm_cacheName ($id);
	
	if (file_exists ($filename)) {
		unlink ($filename);
	}
	
}


/**
 *
 * @global <type> $wp_filter
 * @return <type>
 */
function bm_listHooks () {
	
	global $wp_filter;
	$hook = $wp_filter;
	ksort ($hook);

	echo '<pre>';
	foreach ($hook as $tag => $priority) {
		echo "<br />&gt;&gt;&gt;&gt;&gt;\t<strong>$tag</strong><br />";
		ksort ($priority);
		foreach ($priority as $priority => $function) {
			echo $priority;
			foreach ($function as $name => $properties) {
				echo "\t$name<br />";
			}
		}
	}
	echo '</pre>';

	return;
}


/**
 *
 * @param <type> $versionCurrent
 * @param <type> $versionNew
 * @return <type>
 */
function bm_versionGreater ($versionCurrent, $versionNew) {

	if ($versionCurrent != $versionNew) {
	
		$versionC1 = explode ('.', $versionCurrent);
		$versionC2 = explode ('.', $versionNew);
				
		// if major version is higher
		// 1 > 0
		if ((int) $versionC2[0] > (int) $versionC1[0]) {
		
			return TRUE;
			
		// if major versions are the same
		} else if ((int) $versionC2[0] == (int) $versionC1[0]) {
		
			// if minor version is higher
			if ((int) $versionC2[1] > (int) $versionC1[1]) {
			
				return TRUE;
				
			}
			
		}
		
	}
	
	return FALSE;

}


/**
 *
 * @global <type> $wp_rewrite
 * @param <type> $year
 * @param <type> $month
 * @param <type> $day
 * @return <type>
 */
function bm_getDateArchiveLink ($year = 0, $month = 0, $day = 0) {

	global $wp_rewrite;

	if ($day == 0 && $month == 0) {
		$link = $wp_rewrite->get_year_permastruct ();
	} else if ($day == 0) {
		$link = $wp_rewrite->get_month_permastruct ();
	} else {
		$link = $wp_rewrite->get_day_permastruct ();
	}
	
	if (!empty ($link)) {
		$link = str_replace ('%year%', $year, $link);
		$link = str_replace ('%monthnum%', zeroise (intval ($month), 2), $link);
		$link = str_replace ('%day%', zeroise (intval ($day), 2), $link);
		$link = user_trailingslashit ($link, 'day');
	} else {
		$link = '/?m=' . $year . zeroise ($month, 2) . zeroise ($day, 2);
	}
	
	return apply_filters ('bm_getDateArchiveLink', home_url () . $link, $year, $month, $day);
	
}


/**
 *
 * @param <type> $name
 * @param <type> $unique
 */
function bm_addNonce ($name, $unique = null) {

	$nonceName = 'nonce_' . $name;
	$unique = $name;
?>
	<input type="hidden" name="<?php echo $nonceName; ?>" id="<?php echo $nonceName; ?>" value="<?php echo wp_create_nonce($unique); ?>" />
<?php
}


/**
 *
 * @param <type> $name
 * @param <type> $unique
 * @return <type>
 */
function bm_checkNonce ($name, $unique = null) {

	$unique = $name;

	if (isset ($_POST['nonce_' . $name])) {
		if (!wp_verify_nonce ($_POST['nonce_' . $name], $unique)) {
			return false;
		}
	}

	return true;
	
}


/**
 *
 * @param <type> $sections
 * @return <type>
 */
function bm_processSettings ($sections = null) {

	if ($sections == null) {
		return FALSE;
	}
	
	foreach ($sections as $s) {
		foreach ($s['fields'] as $f) {
		
			if (isset ($f['type']) && isset ($f['var'])) {
			
				switch ($f['type']) {
				
					case 'checkbox':
					
						if (isset ($_POST[$f['var']])) {
							$options[$f['var']] = 1;
						} else {
							$options[$f['var']] = 0;
						}
						
						break;
						
					case 'upload':
					case 'uploadImage':

						$uploadFile = 'file_' . $f['var'];
						
						// new image
						if (!empty ($_FILES[$uploadFile]['name'])) {
						
							$upload = wp_handle_upload ($_FILES[$uploadFile], array('test_form' => false));
							
							if (!isset ($upload['error'])) {
								$options[$f['var']] = $upload['url'];
							} else {
								echo $upload['error'];
								echo '<br />' . __('Please press back and try again', BM_THEMENAME);
								die();
							}
							
						}
						
						if (!isset ($options[$f['var']])) {
							$options[$f['var']] = $_POST[$f['var']];
						}
						
						break;

					case 'drag_drop_list':
					case 'drag drop list':

						$options[$f['var']] = explode (',', $_POST[$f['var']]);
						break;						

					// convert possible strings into ints
					case 'int':
					case 'integer':

						$options[$f['var']] = (int) $_POST[$f['var']];
						break;

					case 'multiSelect':
					case 'multi_select':
					case 'multiselect':
					default:

						if (isset ($_POST[$f['var']])) {
							$options[$f['var']] = $_POST[$f['var']];
						}
						break;
					
				}
				
			}
			
		}
		
	}
	
	return $options;

}


/**
 * shortcode for resizing images dynamically using built in TimThumb
 *
 * @param <type> $atts
 * @param <type> $content
 * @param <type> $code
 * @return <type>
 */
function bm_shortcode_timThumb ($atts, $content = null, $code = '') {
	
	if ($content != null) {
	
		$params = array ();
		
		// if wpmu then do the jiggy widget
		$content = bm_muImageUploadPath ($content);
		
		$params[] = 'src=' . urlencode (esc_url ($content));
		$params[] = 's=1';
		
		if (isset ($atts['height'])) {
			$params[] = 'h=' . $atts['height'];
		}

		if (isset ($atts['width'])) {
			$params[] = 'w=' . $atts['width'];
		}

		if (isset ($atts['align'])) {
			$params[] = 'a=' . $atts['align'];
		}

		if (isset ($atts['filters'])) {
			$params[] = 'f=' . $atts['filters'];
		}
		
	    return '<div class="timThumbThumbnail"><img src="' . BM_BLOGPATH . '/tools/timthumb.php?' . implode ('&amp;', $params) . '" alt="" /></div>';
		
	}
	
	return '';
	
}


/**
 * shortcode for inserting a tinyurl into your blog posts
 *
 * @param <type> $atts
 * @param <type> $content
 * @param <type> $code
 * @return <type>
 */
function bm_shortcode_tinyUrl ($atts, $content = null, $code = '') {

	if (!empty ($atts['url'])) {
		
		$url = '';
		$target = '';
	
		if ($content == null) {
			$content = $atts['url'];
		}
	
		$url = bm_getTinyUrl ($atts['url']);
		
		if (!empty ($atts['target'])) {
			$target = ' target="' . $atts['target'] . '" ';
		}
		
		return '<a href="' . $url . '" class="tinyurl"' . $target . '>' . $content . '</a>';
		
	}
		
	return '';
	
}


/**
 * display a captcha image (and set all required settings)
 *
 */
function bm_captchaImagePath () {
	
	echo BM_BLOGPATH . '/tools/captcha.php';
	
}

 
/**
 * check the specified captcha value matches the session captcha value
 *
 * @global <type> $_SESSION
 * @param <type> $captcha
 * @return <type>
 */
function bm_captchaValid ($captcha) {

	global $_SESSION;
	
	if (session_id () == '') {
		session_start ();
	}
	
	if ($_SESSION['captcha'] == md5 (strtolower ($captcha))) {
		return TRUE;
	}
	
	return FALSE;
	
}


/**
 * send an email using the email form
 *
 * @global <type> $_POST
 * @global <type> $_SERVER
 * @global <type> $bm_options
 */
function bm_sendMessage () {
	
	global $_POST, $_SERVER;
	
	if (isset($_POST['bmAction']) && $_POST['bmAction'] == 'sendMessage') {
	
		$referer = $_SERVER['HTTP_REFERER'];
		
		$name = (string) bm_makeSafe ($_POST['bmName']);
		$email = (string) bm_makeSafe ($_POST['bmEmail']);
		$website = (string) bm_makeSafe ($_POST['bmWebsite']);
		$subject = (string) stripslashes (bm_makeSafe ($_POST['bmSubject']));
		$message = (string) stripslashes (bm_makeSafe ($_POST['bmMessage']));
		$captcha = (string) $_POST['bmCaptcha'];
		
		$error = array ();
		
		if ($name == '') {
			$error[] = __('Name can not be empty', BM_THEMENAME);
		}

		if ($email == '' || !ereg('^([a-zA-Z0-9_\-\.]+)@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.)|(([a-zA-Z0-9\-]+\.)+))([a-zA-Z]{2,4}|[0-9]{1,3})(\]?)',$email)) {
			$error[] = __('Please enter a valid email address', BM_THEMENAME);
		}
		
		if ($message == '') {
			$error[] = __('Please enter a message', BM_THEMENAME);
		}
		
		if ($captcha == '' || strlen($captcha) < 5) {
			$error[] = __('Please complete the captcha code', BM_THEMENAME);
		}
		
		$content['comment_author'] = $name;
		$content['comment_author_email'] = $email;
		$content['comment_author_url'] = $website;
		$content['comment_content'] = $message;
		
		if (bm_checkSpam ($content)) {
			$subject = 'SPAM : ' . $subject;
			$error[] = __('Akismet picked up your message as spam', BM_THEMENAME);
		}

		
		if (!empty ($error)) {
?>
<html>
	<head>
		<title></title>
		<link href="<?php echo BM_BLOGPATH; ?>/lib/styles/styles.css" rel="stylesheet" type="text/css" />
		<style>
			.message {
				background:#fff;
				margin:20px;
				border:1px solid #eee;
			}
			li {
				list-style-type:disc;
				padding:0;
			}
			ul {
				margin-left:20px;
			}
		</style>
	</head>
	<body>
		<div class="column span-5 message">
			<h3><?php _e('Email Not Sent', BM_THEMENAME); ?></h3>
			<ul>
<?php
			foreach ($error as $e) {
				echo '<li>' . $e . '</li>' . "\n";
			}
?>
			</ul>
			<p>&laquo; <a href="<?php echo $referer; ?>"><?php _e('return to contact page', BM_THEMENAME); ?></a></p>
		</div>
	</body>
</html>
<?php
	
		} else {
		
			session_start ();
			if (bm_captchaValid ($captcha)) {

				$bm_options = bm_loadSettings (bm_adminSettings (), get_option ('bm_options'));
			
				$admin_email = $bm_options['email'];
				$admin_subject = $bm_options['emailSubject'];
				if ($subject != '') {
					$admin_subject .= ' : ' . $subject;
				}
				
				$headers = array();
				$headers[] = 'MIME-Version:1.0';
				$headers[] = 'Content-type:text/html; charset=\'' . get_option ('blog_charset') . '\'; format=flowed';
				$headers[] = 'From:' . $email;
				
				$body = '<strong>' . $name . ' - ' . $email . '</strong><br />';
				if ($website != '') {
					$body .= '<strong>' . $website . '</strong><br />';
				}
				$body .= '
<strong>Message:</strong><br /><blockquote>' . nl2br ($message) . '</blockquote>
<hr />
<strong>Remote Address:</strong> ' . $_SERVER['REMOTE_ADDR'] . '<br />
<strong>Browser:</strong> ' . $_SERVER['HTTP_USER_AGENT'];

				$result = wp_mail ($admin_email, $admin_subject, $body, $headers);
				if ($result) {
					setcookie ('messageSent', 1, time () + 30);
				} else {
					setcookie ('messageSent', -1, time () + 30);
				}
				
			} else {
				setcookie ('messageSent', -2, time () + 30);
			}
			
			header ('Location:' . $referer);
		
		}
		
		die();
		
	}
}


/**
 * strip illegal characters from an input string
 *
 * @param <type> $variable
 * @return <type>
 */
function bm_makeSafe ($variable) {

	// Attempt to defend against header injections:
	$badStrings = array(
		'Content-Type:',
		'content-type:',
		'MIME-Version:',
		'mime-version:',
		'Content-Transfer-Encoding:',
		'bcc:',
		'cc:',
		'to:',
		'\n',
		'\r',
	);

	$variable = stripslashes ($variable);

	// Loop through each value and test if it contains
	// one of the $badStrings:
	foreach ($badStrings as $bString) {
		$variable = str_replace ($bString, '', $variable);
	}

	$variable = addslashes (trim ($variable));

	return $variable;

}


/**
 * work out the path to the image if WordPress mu is used
 *
 * @global <type> $blog_id
 * @param <type> $theImageSrc
 * @return <type>
 */
function bm_muImageUploadPath ($theImageSrc) {

	// if wpmu then do the jiggy widget
	$remote_url = parse_url ($theImageSrc);

	if (empty ($remote_url['host'])) {
		$remote_url['host'] = '';
	}

	if ($remote_url['host'] == $_SERVER['SERVER_NAME']) {
		global $blog_id;
		if (function_exists ('get_blog_count')) {
			$imageParts = explode ('/files/', $theImageSrc);
			if (isset ($imageParts[1])) {
				$theImageSrc = '/blogs.dir/' . $blog_id . '/files/' . $imageParts[1];
			}
		}
	}
	
	return $theImageSrc;
	
}


/**
 *
 * @global <type> $akismet_api_host
 * @global <type> $akismet_api_port
 * @param <type> $content
 * @return <type>
 */
function bm_checkSpam ($content) {

	//$content['comment_author'] = name;
	//$content['comment_author_email'] = email address;
	//$content['comment_author_url'] = website;
	//$content['comment_content'] = content;

	// innocent until proven guilty
	$isSpam = FALSE;
	
	$content = (array) $content;
	
	if (function_exists ('akismet_init')) {
		
		$wpcom_api_key = get_option ('wordpress_api_key');
		
		if (!empty ($wpcom_api_key)) {
		
			global $akismet_api_host, $akismet_api_port;

			// set remaining required values for akismet api
			$content['user_ip'] = preg_replace ( '/[^0-9., ]/', '', $_SERVER['REMOTE_ADDR'] );
			$content['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
			$content['referrer'] = $_SERVER['HTTP_REFERER'];
			$content['blog'] = home_url();
			
			if (empty ($content['referrer'])) {
				$content['referrer'] = get_permalink();
			}
			
			$queryString = '';
			
			foreach ($content as $key => $data) {
				if (!empty ($data)) {
					$queryString .= $key . '=' . urlencode (stripslashes ($data)) . '&';
				}
			}
			
			$response = akismet_http_post ($queryString, $akismet_api_host, '/1.1/comment-check', $akismet_api_port);
			
			if ($response[1] == 'true') {
				update_option ('akismet_spam_count', get_option ('akismet_spam_count') + 1);
				$isSpam = TRUE;
			}
			
		}
		
	}
	
	return $isSpam;

}


/**
 *
 * @param <type> $url
 * @return <type> 
 */
function bm_getFavicon ($url = '') {

	if ($url != '') {
		$url = parse_url ($url);
	}
	
	$url = 'http://www.google.com/s2/favicons?domain=' . $url['host'];

	return $url;

}


/**
 *
 * based on code here: http://wordpress.org/support/topic/adding-a-class-to-first-and-last-menu-items
 * 
 * @param <type> $type
 * @param <type> $content
 * @return <type>
 */
function bm_adjustNavClass ($type, $content) {

	$content = preg_replace ('/class="' . $type . '/', 'class="first ' . $type, $content, 1);
	return $content;

}


/**
 *
 * @param <type> $content
 * @return <type>
 */
function bm_adjustMenuClass ($content) {
	return bm_adjustNavClass ('menu-item', $content);
}


/**
 *
 * @param <type> $content
 * @return <type>
 */
function bm_adjustListClass ($content) {
	return bm_adjustNavClass ('page-item', $content);
}


/**
 *
 * @param <type> $content
 * @return <type> 
 */
function bm_adjustCatClass ($content) {
	return bm_adjustNavClass ('cat-item', $content);
}


/**
 * 
 * @param <type> $fontName 
 */
function bm_previewFont ($fontName) {

	$font = bm_fontSettings ();

	if (isset ($font[$fontName])) {

		echo '<!doctype html>';
		echo '<html>';
		echo '<style> * { margin:0; padding: 0; } body { padding:10px; }</style>';

		bm_displayFont ($fontName, 'h1, h2, h3, h4, h5, h6');
		bm_displayFont ($fontName, 'html, body');

		$text = 'The Quick Brown Fox Jumped Over the Lazy Dog';
		echo '<h1 style="font-size:1.4em; text-transform:uppercase;">' . $text . '</h1>';
		echo '<p style="font-size:1.2em; text-transform:lowercase;">' . $text . '</p>';
		echo '</html>';
		die ();
		
	}

}


/**
 *
 * @global <type> $widgetCount
 * @global <type> $widgetColumns
 * @param <type> $columns
 */
function bm_resetWidgetClass ($columns = -1) {

	global $bm_widgetCount, $bm_widgetColumns;

	$bm_widgetCount = -1;
	$bm_widgetColumns = $columns;

}


/**
 *
 * @global <type> $widgetCount
 * @global  $widgetColumns
 * @param <type> $p
 * @return <type>
 */
function bm_modWidgetClass ($p) {

	global $bm_widgetCount, $bm_widgetColumns, $bm_widgetNum;

	$this_id = $p[0]['id'];

	if (!$bm_widgetNum) {
		$bm_widgetNum = array();
	}

	if (isset ($bm_widgetNum[$this_id])) {
		$bm_widgetNum[$this_id] ++;
	} else {
		$bm_widgetNum[$this_id] = 1;
	}

	$class = array ();
	$class[] = 'widget-' . $bm_widgetNum[$this_id];

	if ($bm_widgetColumns > 0) {
		
		$bm_widgetCount ++;
		$bm_widgetCount = $bm_widgetCount % $bm_widgetColumns;		

		$class[] = 'widget-column-' . ($bm_widgetCount + 1);

		if ($bm_widgetCount == 0) {
			$p[0]['before_widget'] = '<div style="clear:both;"></div>' . $p[0]['before_widget'];
		}

	} else {
		
		// only do first and last widget class if it's a single column sidebar
		if (!$registered_widgets = wp_cache_get ('bm_sidebar_widgets')) {
			$registered_widgets = wp_get_sidebars_widgets ();
			wp_cache_add ('bm_sidebar_widgets', $registered_widgets);
		}
		
		if (isset ($registered_widgets[$this_id])) {
			if ($bm_widgetNum[$this_id] == 1) {
				$class[] = 'widget-first';
			}
			if ($bm_widgetNum[$this_id] == count ($registered_widgets[$this_id])) {
				$class[] = 'widget-last';
			}
		}
	}

	$p[0]['before_widget'] = str_replace('class="', 'class="' . implode (' ', $class) . ' ', $p[0]['before_widget']);

	return $p;

}


/**
 * download the theme settins as a text file
 */
function bm_downloadSettings () {

	// make sure user is logged in
	if (is_user_logged_in ()) {

		// make sure user has access to edit the theme settings
		if (current_user_can (BM_EDITTHEME)) {

			header('Content-type: text/plain');
			header('Content-Disposition: attachment; filename="' . BM_THEMENAME . '-settings.txt"');

			$home_url = home_url ();

			$bm_options['options'] = bm_loadSettings (bm_adminSettings (), get_option ('bm_options'));
			$bm_options['actions'] = get_option ('bm_actions');

			$bm_options = serialize ($bm_options);
			$bm_options = str_replace ($home_url, '[HOME_URL]', $bm_options);
			$bm_options = base64_encode ($bm_options);

			echo $bm_options;
			
			die ();

		}

	}

}


/**
 * set a custom background image on the login screen
 * 
 * @global type $bm_options 
 */
function bm_customLogin () {

	global $bm_options;

	if (!empty ($bm_options['adminLoginImage'])) {
		$bm_options['adminLoginImage'] = str_replace ('&amp;', '&', $bm_options['adminLoginImage']);
?>
<style type="text/css">
/* <![CDATA[ */
	#login h1 a {
		background:url(<?php echo $bm_options['adminLoginImage']; ?>) no-repeat top center;
		margin:0 8px 16px 8px;
		-webkit-border-radius:10px;
		-moz-border-radius:10px;
		border-radius:10px;
		width:312px;
	}
/* ]]> */
</style>
<?php
	}
}


/**
 * display a custom message to admins only (unless BM_DEBUG is true)
 * generally used for errors
 * 
 * @param type $message
 * @return type 
 */
function bm_adminMessage ($message = '') {

	if (empty ($message)) {
		return false;
	}

	if (current_user_can (BM_EDITTHEME) || BM_DEBUG) {
		echo '<p class="error">' . $message . '</p>';
	}

}


/**
 *
 * @return type 
 */
function bm_is_bbPressPage () {
	
	return (class_exists ('bbPress') && is_bbpress ());
	
}
