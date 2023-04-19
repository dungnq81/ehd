<?php

use EHD\Cores\Helper;

$smtp_options = Helper::getOption( 'smtp__options', false, false );

$smtp_host                     = $smtp_options['smtp_host'] ?? '';
$smtp_auth                     = $smtp_options['smtp_auth'] ?? '';
$smtp_username                 = $smtp_options['smtp_username'] ?? '';
$smtp_password                 = $smtp_options['smtp_password'] ?? '';
$smtp_encryption               = $smtp_options['smtp_encryption'] ?? '';
$smtp_port                     = $smtp_options['smtp_port'] ?? '';
$smtp_from_email               = $smtp_options['smtp_from_email'] ?? '';
$smtp_from_name                = $smtp_options['smtp_from_name'] ?? '';
$smtp_disable_ssl_verification = $smtp_options['smtp_disable_ssl_verification'] ?? '';

?>
<h2><?php _e('SMTP Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></h2>
<div class="section section-text" id="section_smtp_host">
    <label class="heading" for="smtp_host"><?php _e('SMTP Host', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr($smtp_host); ?>" class="ehd-input ehd-control" type="text" id="smtp_host" name="smtp_host">
        </div>
        <div class="explain">The SMTP server which will be used to send email. For example: smtp.gmail.com</div>
    </div>
</div>
<div class="section section-select" id="section_smtp_auth">
    <label class="heading" for="smtp_auth"><?php _e('SMTP Authentication', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <div class="select_wrapper">
                <select class="ehd-control ehd-select" name="smtp_auth" id="smtp_auth">
                    <option value="true"<?php echo selected( $smtp_auth, 'true', false ); ?>>True</option>
                    <option value="false"<?php echo selected( $smtp_auth, 'false', false ); ?>>False</option>
                </select>
            </div>
        </div>
        <div class="explain">Whether to use SMTP Authentication when sending an email (recommended: True).</div>
    </div>
</div>
<div class="section section-text" id="section_smtp_username">
    <label class="heading" for="smtp_username"><?php _e('SMTP Username', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr($smtp_username); ?>" class="ehd-input ehd-control" type="text" id="smtp_username" name="smtp_username">
        </div>
        <div class="explain">Your SMTP Username. For example: abc@gmail.com</div>
    </div>
</div>
<div class="section section-password" id="section_smtp_password">
    <label class="heading" for="smtp_password"><?php _e('SMTP Password', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input value="" class="ehd-input ehd-control" type="password" id="smtp_password" name="smtp_password">
        </div>
        <div class="explain">Your SMTP Password (The saved password is not shown for security reasons. If you do not want to update the saved password, you can leave this field empty when updating other options).</div>
    </div>
</div>
<div class="section section-select" id="section_smtp_encryption">
    <label class="heading" for="smtp_encryption"><?php _e('Type of Encryption', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <div class="select_wrapper">
                <select class="ehd-control ehd-select" name="smtp_encryption" id="smtp_encryption">
                    <option value="tls"<?php echo selected( $smtp_encryption, 'tls', false );?>>TLS</option>
                    <option value="ssl"<?php echo selected( $smtp_encryption, 'ssl', false );?>>SSL</option>
                    <option value="none"<?php echo selected( $smtp_encryption, 'none', false );?>>No Encryption</option>
                </select>
            </div>
        </div>
        <div class="explain">The encryption which will be used when sending an email (recommended: TLS).</div>
    </div>
</div>
<div class="section section-text" id="section_smtp_port">
    <label class="heading" for="smtp_port"><?php _e('SMTP Port', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr($smtp_port); ?>" class="ehd-input ehd-control" type="text" id="smtp_port" name="smtp_port">
        </div>
        <div class="explain">The port which will be used when sending an email (587/465/25). If you choose TLS it should be set to 587. For SSL use port 465 instead.</div>
    </div>
</div>
<div class="section section-text" id="section_smtp_from_email">
    <label class="heading" for="smtp_from_email"><?php _e('From Email Address', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr($smtp_from_email); ?>" class="ehd-input ehd-control" type="text" id="smtp_from_email" name="smtp_from_email">
        </div>
        <div class="explain">The email address which will be used as the From Address if it is not supplied to the mail function.</div>
    </div>
</div>
<div class="section section-text" id="section_smtp_from_name">
    <label class="heading" for="smtp_from_name"><?php _e('From Name', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input value="<?php echo esc_attr($smtp_from_name); ?>" class="ehd-input ehd-control" type="text" id="smtp_from_name" name="smtp_from_name">
        </div>
        <div class="explain">The name which will be used as the From Name if it is not supplied to the mail function.</div>
    </div>
</div>
<div class="section section-checkbox" id="section_smtp_disable_ssl_verification">
    <label class="heading" for="smtp_disable_ssl_verification"><?php _e('Disable SSL Certificate Verification', EHD_PLUGIN_TEXT_DOMAIN); ?></label>
    <div class="option">
        <div class="controls">
            <input type="checkbox" class="ehd-checkbox ehd-control" name="smtp_disable_ssl_verification" id="smtp_disable_ssl_verification" <?php checked($smtp_disable_ssl_verification, 1); ?> value="1">
        </div>
        <div class="explain">You should get your host to fix the SSL configurations instead of bypassing it.</div>
    </div>
</div>
