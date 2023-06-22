<form name="ehd_2fa_form" id="loginform" action="<?php echo $args['redirect_to'] ?>" method="post">
	<h1><?php esc_html_e( 'Save the Backup Codes', EHD_PLUGIN_TEXT_DOMAIN ); ?></h1>
	<br />
	<p class="ehd-2fa-title"><?php esc_html_e( 'Important! In case you lose or change your phone and you no longer have access to the Authenticator app, you can use one of the codes below to log in.', EHD_PLUGIN_TEXT_DOMAIN ); ?></p>
	<br />
	<p><b><?php esc_html_e( 'Save the codes to make sure that you don’t end up locked out of this website.', EHD_PLUGIN_TEXT_DOMAIN ); ?></b> <?php esc_html_e( 'Each code can only be used once.', EHD_PLUGIN_TEXT_DOMAIN ); ?></p>

	<?php if ( ! empty( $args['backup_codes'] ) ) : ?>
		<div class="qr-section" style="text-align: center">
			<pre style="margin: 20px;"><code style="background: #2b2b2b; color: #f8f8f2;"><?php echo implode( "\n", $args['backup_codes'] ); ?></code></pre>
		</div>
	<?php endif ?>
	<div class="backup_codes_written" style="text-align: left">
		<input name="backup_codes_checkbox" type="checkbox" id="backup_codes_checkbox" onchange="document.getElementById('saved_codes').disabled = !this.checked;" />
		<label for="backup_codes_checkbox"><?php esc_html_e( 'I have saved my backup codes.', EHD_PLUGIN_TEXT_DOMAIN ); ?></label>
	</div>
	<br />
	<button id="saved_codes" disabled href="<?php echo $args['redirect_to'] ?>" class="button button-primary"><?php esc_html_e( 'Continue', EHD_PLUGIN_TEXT_DOMAIN ); ?></button>
</form>
