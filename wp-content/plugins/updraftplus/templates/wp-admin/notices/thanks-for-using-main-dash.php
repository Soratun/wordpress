<div id="updraft-dashnotice" class="updated">
	<div style="float:right;"><a href="#" onclick="jQuery('#updraft-dashnotice').slideUp(); jQuery.post(ajaxurl, {action: 'updraft_ajax', subaction: 'dismissdashnotice', nonce: '<?php echo esc_js(wp_create_nonce('updraftplus-credentialtest-nonce'));?>' });"><?php printf(esc_html__('Dismiss (for %s months)', 'updraftplus'), 12); ?></a></div>

	<h3><?php esc_html_e('Thank you for installing UpdraftPlus!', 'updraftplus');?></h3>
	
	<a href="<?php echo apply_filters('updraftplus_com_link', 'https://updraftplus.com/');?>"><img style="border: 0px; float: right; height: 150px; width: 150px; margin: 20px 15px 15px 35px;" alt="UpdraftPlus" src="<?php echo UPDRAFTPLUS_URL.'/images/ud-logo-150.png'; ?>"></a>

	<?php
	if (!file_exists(UPDRAFTPLUS_DIR.'/udaddons')) {
		echo '<p>'.esc_html__("If you like UpdraftPlus, you'll love UpdraftPlus Premium!").' '.__('Protect your WordPress investment with premium features, or check out our other 5* rated  plugins below:', 'updraftplus').'</p>';
	} else {
		echo '<p>'.esc_html__("If you like UpdraftPlus, you'll love our other plugins.", 'updraftplus').' '.__('All 5* rated and actively installed on millions of WordPress websites:', 'updraftplus').'</p>';
	}

	if (!file_exists(UPDRAFTPLUS_DIR.'/udaddons')) {
	?>
	<p>
		<?php echo '<strong><a href="'.esc_url($updraftplus->get_url('premium')).'" target="_blank">'.esc_html__('UpdraftPlus Premium', 'updraftplus').'</a>: </strong>'.__(" Upgrade for automatic backups before updates, incremental backups, more remote storage locations, premium support and", 'updraftplus');
		echo ' <a href="'.esc_url($updraftplus->get_url('premium')).'" target="_blank">'.esc_html__('more', 'updraftplus').'</a>';
	?>
	</p>
	<?php } ?>
	<p>
		<?php echo '<strong><a href="https://getwpo.com/buy/" target="_blank">WP-Optimize</a>: </strong>'.esc_html__('Speed up and optimize your WordPress website.', 'updraftplus').' '.__('Cache your site, clean the database and compress images.', 'updraftplus'); ?>
	</p>
	<p>
		<?php echo '<strong><a href="https://aiosplugin.com/" target="_blank">'.esc_html__('All-In-One Security (AIOS)', 'updraftplus').'</a>: </strong>'. esc_html__("Still on the fence?", 'updraftplus').' '.esc_html__("Secure your WordPress website with AIOS.", 'updraftplus').' '.esc_html__(" Comprehensive, cost-effective, 5* rated and easy to use.", 'updraftplus');
	?>
	</p>
	<p>
		<?php echo '<strong><a href="https://www.internallinkjuicer.com/" target="_blank">'.esc_html__('Internal Link Juicer', 'updraftplus').'</a>: </strong>'.__('Automate the building of internal links on your WordPress website.', 'updraftplus').' '.esc_html__('Save time and boost SEO!', 'updraftplus').' '. __('You don’t need to be an SEO expert to use this plugin.', 'updraftplus');
	?>
	</p>
	<p>
		<?php echo '<strong><a href="https://wpovernight.com/" target="_blank">'.__('WP Overnight', 'updraftplus').'</a>: </strong>'.esc_html__("Quality add-ons for WooCommerce.", 'updraftplus').' '.esc_html__("Designed to optimize your store, enhance user experience  and increase revenue!", 'updraftplus');
	?>
	</p>
	<p>
		<?php echo '<strong>'.esc_html__('More quality plugins', 'updraftplus').' :</strong>';?>
		<a href="https://www.simbahosting.co.uk/s3/shop/" target="_blank"><?php echo __('Premium WooCommerce plugins', 'updraftplus').'</a> | <a href="https://wordpress.org/plugins/two-factor-authentication/" target="_blank">'.__('Free two-factor security plugin', 'updraftplus');?></a>
	</p>
	<p></p>
	<div style="float:right;"><a href="#" onclick="jQuery('#updraft-dashnotice').slideUp(); jQuery.post(ajaxurl, {action: 'updraft_ajax', subaction: 'dismissdashnotice', nonce: '<?php echo esc_js(wp_create_nonce('updraftplus-credentialtest-nonce'));?>' });"><?php printf(esc_html__('Dismiss (for %s months)', 'updraftplus'), 12); ?></a></div>
	<p>&nbsp;</p>
</div>
