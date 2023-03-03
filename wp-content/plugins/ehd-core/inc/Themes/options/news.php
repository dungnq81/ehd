<?php

$news_options = get_option('news_options');

if (!is_array($news_options)) {
    $news_options = [];
}

?>
<h2><?php _e('News Settings', EHD_PLUGIN_TEXT_DOMAIN); ?></h2>
