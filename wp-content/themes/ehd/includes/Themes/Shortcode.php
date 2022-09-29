<?php

namespace EHD\Themes;

use EHD\Helpers\Url;

\defined('\WPINC') || die;

if (!class_exists('Shortcode')) {
    class Shortcode
    {
        // ------------------------------------------------------

        public function __construct()
        {
            add_shortcode('safe_mail', [&$this, 'safe_mailto_shortcode'], 11);
            add_shortcode('site_logo', [&$this, 'site_logo_shortcode'], 11);

            add_shortcode('horizontal_menu', [&$this, 'horizontal_menu_shortcode'], 11);
            add_shortcode('vertical_menu', [&$this, 'vertical_menu_shortcode'], 11);

            add_shortcode('mobile_menu', [&$this, 'mobile_menu_shortcode'], 11);
            add_shortcode('main_menu', [&$this, 'main_menu_shortcode'], 11);
            add_shortcode('term_menu', [&$this, 'term_menu_shortcode'], 11);

            add_shortcode('inline-search', [&$this, 'inline_search_shortcode'], 11);
        }

        // ------------------------------------------------------

        /**
         * @param array $atts
         * @return void
         */
        public function inline_search_shortcode(array $atts = [])
        {
            // override default attributes
            $a = shortcode_atts(
                [
                    'class' => 'inline-search',
                ],
                array_change_key_case((array) $atts, CASE_LOWER)
            );

            $_unique_id = esc_attr(uniqid('search-form-'));
            $title = __('Search', 'ehd');
            $title_for = __('Search for', 'ehd');
            $placeholder_title = esc_attr(__('Search ...', 'ehd'));

            if (class_exists('\WooCommerce')) :
                $title = __('Search products', 'ehd');
                $title_for = __('Search products', 'ehd');
                $placeholder_title = esc_attr(__('Search products ...', 'ehd'));
            endif;

            ?>
            <div class="inside-search <?php echo $a['class']; ?>">
                <form role="search" action="<?php echo Url::home(); ?>" class="frm-search" method="get"
                      accept-charset="UTF-8" data-abide novalidate>
                    <label for="<?php echo $_unique_id; ?>" class="screen-reader-text"><?php echo $title_for; ?></label>
                    <input id="<?php echo $_unique_id; ?>" required pattern="^(.*\S+.*)$" type="search"
                           autocomplete="off" name="s" value="<?php echo get_search_query(); ?>"
                           placeholder="<?php echo $placeholder_title; ?>">
                    <button type="submit" data-glyph="">
                        <span><?php echo $title; ?></span>
                    </button>
                    <?php if (class_exists('\WooCommerce')) : ?>
                        <input type="hidden" name="post_type" value="product">
                    <?php endif; ?>
                </form>
            </div>
            <?php
        }

        // ------------------------------------------------------

        /**
         * @param array $atts
         *
         * @return bool|string
         */
        public function term_menu_shortcode(array $atts = [])
        {
            // override default attributes
            $a = shortcode_atts(
                [
                    'location' => 'policy-nav',
                    'menu_class' => 'terms-menu',
                ],
                array_change_key_case((array) $atts, CASE_LOWER)
            );

            return Func::termNav($a['location'], $a['menu_class']);
        }

        // ------------------------------------------------------

        /**
         * @param array $atts
         *
         * @return bool|string
         */
        public function main_menu_shortcode(array $atts = [])
        {
            // override default attributes
            $a = shortcode_atts(
                [
                    'location' => 'main-nav',
                    'menu_class' => 'desktop-menu',
                    'menu_id' => 'main-menu',
                ],
                array_change_key_case((array) $atts, CASE_LOWER)
            );

            return Func::mainNav($a['location'], $a['menu_class'], $a['menu_id']);
        }

        // ------------------------------------------------------

        /**
         * @param array $atts
         *
         * @return bool|string
         */
        public function mobile_menu_shortcode(array $atts = [])
        {
            // override default attributes
            $a = shortcode_atts(
                [
                    'location' => 'mobile-nav',
                    'menu_class' => 'mobile-menu',
                    'menu_id' => 'mobile-menu',
                ],
                array_change_key_case((array) $atts, CASE_LOWER)
            );

            return Func::mobileNav($a['location'], $a['menu_class'], $a['menu_id']);
        }

        // ------------------------------------------------------

        /**
         * @param array $atts
         *
         * @return bool|string
         */
        public function vertical_menu_shortcode(array $atts = [])
        {
            // override default attributes
            $a = shortcode_atts(
                [
                    'id' => '',
                    'class' => 'mobile-menu',
                    'location' => 'mobile-nav',
                    'depth' => 4,
                ],
                array_change_key_case((array) $atts, CASE_LOWER)
            );

            return Func::verticalNav([
                'menu_id' => $a['id'],
                'menu_class' => 'vertical menu vertical-menu ' . $a['class'],
                'theme_location' => $a['location'],
                'depth' => $a['depth'],
                'echo' => false,
            ]);
        }

        // ------------------------------------------------------

        /**
         * @param array $atts
         *
         * @return bool|string
         */
        public function horizontal_menu_shortcode(array $atts = [])
        {
            // override default attributes
            $a = shortcode_atts(
                [
                    'id' => '',
                    'class' => 'desktop-menu',
                    'location' => 'main-nav',
                    'depth' => 4,
                ],
                array_change_key_case((array) $atts, CASE_LOWER)
            );

            return Func::horizontalNav([
                'menu_id' => $a['id'],
                'menu_class' => 'dropdown menu horizontal horizontal-menu ' . $a['class'],
                'theme_location' => $a['location'],
                'depth' => $a['depth'],
                'echo' => false,
            ]);
        }

        // ------------------------------------------------------

        /**
         * @param array $atts
         *
         * @return string
         */
        public function site_logo_shortcode(array $atts = [])
        {
            // override default attributes
            $a = shortcode_atts(
                [
                    'theme' => 'default',
                    'class' => 'site-logo',
                ],
                array_change_key_case((array) $atts, CASE_LOWER)
            );

            return Func::siteLogo($a['theme'], $a['class']);
        }

        // ------------------------------------------------------

        /**
         * @param array $atts
         * @return string
         */
        public function safe_mailto_shortcode(array $atts = [])
        {
            // override default attributes
            $a = shortcode_atts(
                [
                    'email' => 'info@webhd.vn',
                    'title' => '',
                    'attributes' => '',
                    'class' => '',
                    'id' => esc_attr(uniqid('mail-')),
                ],
                array_change_key_case((array) $atts, CASE_LOWER)
            );

            $_attr = [];
            if ($a['id']) {
                $_attr['id'] = $a['id'];
            }

            if ($a['class']) {
                $_attr['class'] = $a['class'];
            }

            if (empty($a['title'])) {
                $a['title'] = esc_attr($a['email']);
            }

            $_attr['title'] = $a['title'];

            if ($a['attributes']) {
                $a['attributes'] = array_merge($_attr, (array) $a['attributes']);
            } else {
                $a['attributes'] = $_attr;
            }

            return safe_mailto($a['email'], $a['title'], $a['attributes']);
        }
    }
}