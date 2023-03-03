<?php

$global_options = get_option('global_options');

if (!is_array($global_options)) {
    $global_options = [];
}

?>
<h2><?php _e('Global Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></h2>
