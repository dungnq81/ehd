<?php

namespace EHD\Widgets;

if (!class_exists('offCanvas_Widget')) {
	class offCanvas_Widget extends Widget
	{
        /**
         * Constructor.
         */
        public function __construct()
        {
            $this->widget_description = __('Display offCanvas Button', 'ehd' );
            $this->widget_name        = __('W - offCanvas Button', 'ehd' );
            $this->settings = [
                'hide_if_desktop' => [
                    'type' => 'checkbox',
                    'std' => 1,
                    'label' => __('Hide if desktop devices', 'ehd'),
                ],
            ];

            parent::__construct();
        }

		/**
		 * Creating widget front-end
		 *
		 * @param array $args
		 * @param array $instance
		 */
		public function widget($args, $instance)
		{
            if ( $this->get_cached_widget( $args ) ) {
                return;
            }

            $hide_if_desktop = empty( $instance['hide_if_desktop'] ) ? 0 : 1;
            $class = '';
            if ($hide_if_desktop) {
                $class = ' hide-for-large';
            }

            ob_start();

            ?>
			<div class="off-canvas-content<?=$class?>" data-off-canvas-content>
				<button class="menu-lines" type="button" data-open="offCanvasMenu" aria-label="button">
					<span class="menu-txt"><?php echo __('Menu', 'ehd' ); ?></span>
				</button>
			</div>
		<?php
            $content = ob_get_clean();
            echo $content; // WPCS: XSS ok.

            $this->cache_widget( $args, $content );
		}
	}
}