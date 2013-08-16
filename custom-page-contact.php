<?php
/*
Template Name: Contact
*/
	bm_define('DONOTCACHEPAGE', true);

	get_header();
?>

<div id="content">
<?php
	if (have_posts()) {
		the_post();	
?>
	<h1 class="posttitle"><?php the_title(); ?></h1>
<?php
		bm_doAction ('bm_afterTitle');
		the_content ();
		
		$displayForm = true;

		if (isset ($_COOKIE['messageSent'])) {
			switch ($_COOKIE['messageSent']) {

				// message sent successfully
				case 1:

					echo '<p class="success">' . __('Thanks for the message, we\'ll get back to you soon', BM_THEMENAME) . '</p>';
					$displayForm = false;
					break;

				// error sending message
				case -1:
				case -2:

					echo '<p class="error">' . __('Error sending message, please try again later', BM_THEMENAME) . '</p>';
					echo '<!-- error ' . $_COOKIE['messageSent'] . ' -->';
					break;

				default:

			}
		}
		
		if ($displayForm && $bm_options['email'] != '') {
?>
			<form method="post" action="<?php echo BM_SITEURL; ?>/index.php" id="commentform">
			
				<p>
					<input type="" name="bmName" id="bmName" class="text" />
					<label for="bmName"><?php _e('Name', BM_THEMENAME); ?> *</label>
				</p>

				<p>
					<input type="" name="bmEmail" id="bmEmail" class="text" />
					<label for="bmEmail"><?php _e('Email Address', BM_THEMENAME); ?> *</label>
				</p>

				<p>
					<input type="" name="bmWebsite" id="bmWebsite" class="text" />
					<label for="bmWebsite"><?php _e('Website', BM_THEMENAME); ?></label>
				</p>

				<p>
					<input type="" name="bmSubject" id="bmSubject" class="text" />
					<label for="bmSubject"><?php _e('Subject', BM_THEMENAME); ?></label>
				</p>

				<p>
					<textarea name="bmMessage" id="bmMessage"></textarea>
				</p>

				<p>
					<img src="<?php bm_captchaImagePath (); ?>" width="70" height="22" class="captcha" />
					<input type="input" id="bmCaptcha" name="bmCaptcha" class="text small" maxlength="5" autocomplete="off"  />
					<label for="bmCaptcha"><?php _e('Please enter the characters in the image to the left', BM_THEMENAME); ?></label>
				</p>

				<p>
					<input type="submit" value="<?php _e('Send Message', BM_THEMENAME); ?>" class="button" />
				</p>
				
				<input type="hidden" name="bmAction" value="sendMessage" />
			</form>
<?php
		}
		
		if ($bm_options['email'] == '') {
			bm_adminMessage (__('There is no email address specified in the theme control panel. Add an email address for the contact form to display', BM_THEMENAME));
		}
	}
?>
</div>
<?php
	get_sidebar();
	get_footer();
?>