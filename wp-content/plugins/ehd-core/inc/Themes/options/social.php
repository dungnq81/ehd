<?php

$social_options = get_option('social_options');

if (!is_array($social_options)) {
    $social_options = [];
}

?>
<h2><?php _e('Socials Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></h2>
