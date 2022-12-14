<?php

namespace EHD\Sites\Themes;

\defined('\WPINC') || die;

/**
 * Global Functions Class
 *
 * @author EHD
 */
class Func
{
    /**
     * @param bool $echo
     *
     * @return string|null
     */
    public static function paginationLinks(bool $echo = true)
    {
        global $wp_query;
        if ($wp_query->max_num_pages > 1) {

            // This needs to be an unlikely integer
            $big = 999999999;

            // For more options and info view the docs for paginate_links()
            // http://codex.wordpress.org/Function_Reference/paginate_links
            $paginate_links = paginate_links(
                apply_filters(
                    'wp_pagination_args',
                    [
                        'base'      => str_replace($big, '%#%', html_entity_decode(get_pagenum_link($big))),
                        'current'   => max(1, get_query_var('paged')),
                        'total'     => $wp_query->max_num_pages,
                        'end_size'  => 3,
                        'mid_size'  => 3,
                        'prev_next' => true,
                        'prev_text' => '<i data-glyph=""></i>',
                        'next_text' => '<i data-glyph=""></i>',
                        'type'      => 'list',
                    ]
                )
            );

            $paginate_links = str_replace("<ul class='page-numbers'>", '<ul class="pagination">', $paginate_links);
            $paginate_links = str_replace('<li><span class="page-numbers dots">&hellip;</span></li>', '<li class="ellipsis"></li>', $paginate_links);
            $paginate_links = str_replace('<li><span aria-current="page" class="page-numbers current">', '<li class="current"><span aria-current="page" class="show-for-sr">You\'re on page </span>', $paginate_links);
            $paginate_links = str_replace('</span></li>', '</li>', $paginate_links);
            $paginate_links = preg_replace('/\s*page-numbers\s*/', '', $paginate_links);
            $paginate_links = preg_replace('/\s*class=""/', '', $paginate_links);

            // Display the pagination if more than one page is found.
            if ($paginate_links) {
                $paginate_links = '<nav aria-label="Pagination">' . $paginate_links . '</nav>';
                if ($echo) {
                    echo $paginate_links;
                } else {
                    return $paginate_links;
                }
            }
        }

        return null;
    }

    // -------------------------------------------------------------

    /**
     * @param mixed|null $id The ID, to load a single record;
     */
    public static function postComment(mixed $id = null)
    {
        if (!$id) {
            if (get_post_type() === 'product') {
                global $product;
                $id = $product->get_id();
            } else {
                $id = get_post()->ID;
            }
        }

        /*
         * If the current post is protected by a password and
         * the visitor has not yet entered the password we will
         * return early without loading the comments.
        */
        if (post_password_required($id)) {
            return;
        }

        $wrapper_open = '<section id="comments-section" class="section comments-section comments-wrapper">';
        $wrapper_close = '</section>';

        //...
        $facebook_comment = false;
        $zalo_comment = false;

        if (class_exists('\ACF')) {
            $facebook_comment = get_field('facebook_comment', $id);
            $zalo_comment = get_field('zalo_comment', $id);
        }

        if (comments_open() || true === $facebook_comment || true === $zalo_comment) {
            echo $wrapper_open;
            if (comments_open()) {
                //if ( ( class_exists( '\WooCommerce' ) && 'product' != $post_type ) || ! class_exists( '\WooCommerce' ) ) {
                comments_template();
                //}
            }
            if (true === $facebook_comment) {
                get_template_part('template-parts/comments/facebook');
            }
            if (true === $zalo_comment) {
                get_template_part('template-parts/comments/zalo');
            }

            echo $wrapper_close;
        }
    }
}
