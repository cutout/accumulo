<?php
	bm_banner ('footer');
?>
			</div><!--/main-->

			<div id="widgets-bottom">
				<div class="wrapper">
<?php
			bm_dynamicSidebar ('bottom-widgets');
?>
				</div>
			</div>

		</div>

		<div id="footer">
			<div class="wrapper">
				<div class="copyright">
				&#169;<?php echo date('Y'); ?> <span class="url fn org"><?php bloginfo('name'); ?></span> &bull; Powered by <a href="http://www.wordpress.com">WordPress</a> &amp; <a href="http://www.prothemedesign.com">Pro Theme Design</a>
				<?php wp_footer(); ?>
				</div>
				<?php get_template_part('content','searchform'); ?>
			</div>
		</div><!--/footer-->

		<script type="text/javascript">
			jQuery.noConflict();
			
			var currentPane = 0;

			jQuery(document).ready(function(){
				jQuery("ul#nav").superfish(); 

				jQuery("ul#tabber").tabs("div.panes > div",{
					onBeforeClick: function(event, i) {
						currentPane = i + 1;
						var pane = this.getPanes().eq(i);
						window.location.hash = '#tab' + currentPane;
<?php
		global $bm_options;
		if (!empty ($bm_options['googleAnalytics'])) {
?>
						_gaq.push(['_trackEvent', 'Accumulo', 'Tab_click', 'tab_' + currentPane]);
<?php
		}
?>
						if (pane.is(":empty")) {
							pane.html ('<div class="tabLoading"></div>');
							pane.load (
								this.getTabs().eq(i).attr("tab"),
								{},
								function() {
									bindTooltips();
								}
							);
						}
						
					}
					
				});
				
				bindTooltips();
			});
			
			function bindTooltips() {
			
				jQuery(".panes .pane:eq(" + (currentPane - 1) + ") ul.feed-item a.tt").tooltip({
					relative: true,
					predelay:5,
					position: "center right",
					effect: 'fade',					
					lazy: false
				}).dynamic ();
			}
		</script>

		<a href="#top" id="arrow-top" title="Back to Top">Back to Top</a>
		<?php bm_footer(); ?>
		
	</body>
</html>