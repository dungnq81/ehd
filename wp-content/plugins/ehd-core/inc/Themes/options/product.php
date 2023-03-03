<?php

$product_options = get_option('product_options');

if (!is_array($product_options)) {
    $product_options = [];
}

?>
<h2><?php _e('Products Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></h2>
