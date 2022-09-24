<?php

namespace EHD\Themes;

\defined('\WPINC') || die;

if (!class_exists('ScriptLoader')) {

    /**
     * A class that provides a way to add `async` or `defer` attributes to scripts.
     */
    class ScriptLoader
    {
        /**
         * Adds async/defer attributes to enqueued / registered scripts.
         *
         * If #12009 lands in WordPress, this function can no-op since it would be handled in core.
         *
         * @link https://core.trac.wordpress.org/ticket/12009
         *
         * @param string $tag    The script tag.
         * @param string $handle The script handle.
         * @param string $src
         *
         * @return string Script HTML string.
         */
        public function filterScriptTag($tag, $handle, $src)
        {
            // async/defer
            foreach (['async', 'defer'] as $attr) {
                if (!wp_scripts()->get_data($handle, $attr)) {
                    continue;
                }

                // Prevent adding attribute when already added in #12009.
                if (!preg_match(":\s$attr(=|>|\s):", $tag)) {
                    $tag = preg_replace(':(?=></script>):', " $attr", $tag, 1);
                }

                // Only allow async or defer, not both.
                break;
            }

            // custom filter which adds proper attributes
            if (('fontawesome-kit' == $handle) && !preg_match(":\scrossorigin(=|>|\s):", $tag)) {
                $tag = preg_replace(':(?=></script>):', " crossorigin='anonymous'", $tag, 1);
            }

            //return $tag
            return $tag;
        }
    }
}