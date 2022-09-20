<?php
    defined( 'ABSPATH' ) || exit;
?>
<script type="text/template" id="tmpl-wvs-variation-template">
    {{{data.variation.availability_html }}}
</script>
<script type="text/template" id="tmpl-wvs-unavailable-variation-template">
    <p><?php esc_html_e( 'Sorry, this product is unavailable. Please choose a different combination.', 'woo-variation-swatches-pro' ); ?></p>
</script>
