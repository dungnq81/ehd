<?php

namespace EHD\Plugins;

\defined('ABSPATH') || die;

/**
 * RankMath Plugins
 * @author   WEBHD
 */

final class RankMath
{
    public function __construct()
    {
        add_action('after_setup_theme', [&$this, 'setup_theme']);
        add_filter('rank_math/frontend/breadcrumb/args', [&$this, 'breadcrumb_args']);

        // show_keywords
        add_filter('rank_math/frontend/show_keywords', '__return_true');

        // disable_adjacent_rel_links
        add_filter('rank_math/frontend/disable_adjacent_rel_links', '__return_true');

        // remove author schema
        add_filter('rank_math/json_ld', function ($entities, $jsonld) {
            if (isset($entities['ProfilePage'])) {
                $id = $entities['ProfilePage']['@id'];
                foreach ($entities as $key => $entity) {
                    if (isset($entity['author']['@id']) && $id === $entity['author']['@id']) {
                        unset($entities[$key]['author']);
                    }
                }

                unset($entities['ProfilePage']);
            }

            return $entities;

        }, 999, 2);
    }

    /** ---------------------------------------- */

    /**
     * @return void
     */
    public function setup_theme()
    {
        // Rank Math Breadcrumb.
        add_theme_support('rank-math-breadcrumbs');
    }

    /** ---------------------------------------- */

    /**
     * @param $args
     *
     * @return string[]
     */
    public function breadcrumb_args($args)
    {
        return [
            'delimiter'   => '',
            'wrap_before' => '<ul id="breadcrumbs" class="breadcrumbs" aria-label="Breadcrumbs">',
            'wrap_after'  => '</ul>',
            'before'      => '<li><span property="itemListElement" typeof="ListItem">',
            'after'       => '</span></li>',
        ];
    }
}