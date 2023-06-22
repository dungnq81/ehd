<style>#login { width:360px; padding:5% 0 0; }</style>

<?php if ( ! empty( $args['error'] ) ) : ?>
	<div id="login_error"><strong><?php echo $args['error']; ?></strong><br /></div>
<?php endif ?>

<form name="ehd_2fa_form" id="loginform" action="<?php echo $args['action']; ?>" method="post">
	<h1><?php esc_html_e( '2-factor Authentication', EHD_PLUGIN_TEXT_DOMAIN ); ?></h1>
	<br />
	<p class="ehd-2fa-title"><?php esc_html_e( 'In order to log in, please enter one of the backup codes you have received on your first login:', EHD_PLUGIN_TEXT_DOMAIN ); ?></p>

	<input type="hidden" name="backup-code-used" value="1" />
	<p>
		<br />
		<label for="ehd_2fabackupcode"><?php esc_html_e( 'Backup Code:', EHD_PLUGIN_TEXT_DOMAIN ); ?></label>
		<input name="ehd_2fabackupcode" id="ehd_2fabackupcode" class="input" value="" size="20" pattern="[0-9]*" autofocus />
	</p>

	<?php if ( $args['interim_login'] ) : ?>
		<input type="hidden" name="interim-login" value="1" />
	<?php else : ?>
		<input type="hidden" name="redirect_to" value="<?php echo $args['redirect_to']; ?>" />
	<?php endif; ?>

	<input type="hidden" name="rememberme" id="rememberme" value="<?php echo $args['rememberme']; ?>" />
	<p>
		<br />
		<?php submit_button( __( 'Authenticate', EHD_PLUGIN_TEXT_DOMAIN ) ); ?>
	</p>
</form>

