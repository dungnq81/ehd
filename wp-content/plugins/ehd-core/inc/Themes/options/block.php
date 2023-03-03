<?php

$block_options = get_option('block_options');

if (!is_array($block_options)) {
    $block_options = [];
}

?>
<h2><?php _e('Blocks Editor Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></h2>
