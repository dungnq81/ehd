<style>#login { width:360px; padding:5% 0 0; }</style>

<form name="ehd_2fa_form" id="loginform" action="<?php echo $args['redirect_to']; ?>" method="post">
	<h1><?php esc_html_e( 'You are logging in with a one-time Backup Code', EHD_PLUGIN_TEXT_DOMAIN ); ?></h1>
	<br />
	<p class="ehd-2fa-title"><?php esc_html_e( 'We have noticed that you’re using a one-time backup code to log in. This code cannot be used anymore. In case you lost access to your authenticator app, please ask the website administrator to reset your 2FA setup.', EHD_PLUGIN_TEXT_DOMAIN ); ?></p>
	
	<button id="saved_codes" href="<?php echo $args['redirect_to']; ?>" class="button button-primary"><?php esc_html_e( 'Continue', EHD_PLUGIN_TEXT_DOMAIN ); ?></button>
</form>
