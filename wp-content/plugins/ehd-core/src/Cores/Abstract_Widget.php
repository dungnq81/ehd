<?php

namespace EHD_Cores;

use ReflectionClass;
use WP_Widget;

\defined( 'ABSPATH' ) || die;

abstract class Abstract_Widget extends WP_Widget {

	protected string $prefix = 'w-';
	protected string $widget_id;
	protected string $widget_classname;
	protected string $widget_name = 'Unknown Widget';
	protected string $widget_description = '';
	protected string $widget_mime_type = '';

	protected array $settings;

	/**
	 * Whether the widget has been registered yet.
	 *
	 * @var bool
	 */
	protected bool $registered = false;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$className              = ( new ReflectionClass( $this ) )->getShortName();
		$this->widget_classname = str_replace( [
			'_widget',
			'-widget'
		], '', Helper::dashCase( strtolower( $className ) ) );
		$this->widget_id        = $this->prefix . $this->widget_classname;

		parent::__construct( $this->widget_id, $this->widget_name, $this->widget_options(), $this->control_options() );

		add_action( 'save_post', [ &$this, 'flush_widget_cache' ] );
		add_action( 'deleted_post', [ &$this, 'flush_widget_cache' ] );
		add_action( 'switch_theme', [ &$this, 'flush_widget_cache' ] );
	}

	/**
	 * @return array
	 */
	protected function widget_options(): array {
		return [
			'classname'                   => $this->widget_classname,
			'description'                 => $this->widget_description,
			'customize_selective_refresh' => true,
			'show_instance_in_rest'       => true,
			'mime_type'                   => $this->widget_mime_type,
		];
	}

	/**
	 * @return array
	 */
	protected function control_options(): array {
		return [];
	}

	/**
	 * Flush the cache
	 *
	 * @return void
	 */
	public function flush_widget_cache(): void {
		foreach ( [ 'https', 'http' ] as $scheme ) {
			wp_cache_delete( $this->get_widget_id_for_cache( $this->widget_id, $scheme ), 'widget' );
		}
	}

	/**
	 * @param        $widget_id
	 * @param string $scheme
	 *
	 * @return mixed|void
	 */
	protected function get_widget_id_for_cache( $widget_id, string $scheme = '' ) {
		if ( $scheme ) {
			$widget_id_for_cache = $widget_id . '-' . $scheme;
		} else {
			$widget_id_for_cache = $widget_id . '-' . ( is_ssl() ? 'https' : 'http' );
		}

		return apply_filters( 'w_cached_widget_id', $widget_id_for_cache );
	}

	/**
	 * Cache the widget
	 *
	 * @param array $args Arguments
	 * @param string $content Content
	 *
	 * @return string the content that was cached
	 */
	public function cache_widget( array $args, string $content ): string {
		// Don't set any cache if widget_id doesn't exist
		if ( empty( $args['widget_id'] ) ) {
			return $content;
		}

		$cache = wp_cache_get( $this->get_widget_id_for_cache( $this->widget_id ), 'widget' );
		if ( ! is_array( $cache ) ) {
			$cache = [];
		}

		$cache[ $this->get_widget_id_for_cache( $args['widget_id'] ) ] = $content;
		wp_cache_set( $this->get_widget_id_for_cache( $this->widget_id ), $cache, 'widget' );

		return $content;
	}

	/**
	 * Get cached widget
	 *
	 * @param array $args Arguments
	 *
	 * @return bool true if the widget is cached otherwise false
	 */
	public function get_cached_widget( array $args ): bool {
		// Don't get cache if widget_id doesn't exists
		if ( empty( $args['widget_id'] ) ) {
			return false;
		}

		$cache = wp_cache_get( $this->get_widget_id_for_cache( $this->widget_id ), 'widget' );
		if ( ! is_array( $cache ) ) {
			$cache = [];
		}

		if ( isset( $cache[ $this->get_widget_id_for_cache( $args['widget_id'] ) ] ) ) {
			echo $cache[ $this->get_widget_id_for_cache( $args['widget_id'] ) ]; // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped

			return true;
		}

		return false;
	}

	/**
	 * @param array $instance Array of instance options.
	 *
	 * @return string
	 */
	protected function get_instance_title( array $instance ): string {
		if ( isset( $instance['title'] ) ) {
			return $instance['title'];
		}

		if ( isset( $this->settings, $this->settings['title'], $this->settings['title']['std'] ) ) {
			return $this->settings['title']['std'];
		}

		return '';
	}

	/**
	 * @param $new_instance
	 * @param $old_instance
	 *
	 * @return array
	 */
	public function update( $new_instance, $old_instance ): array {
		$instance = $old_instance;
		if ( empty( $this->settings ) ) {
			return $instance;
		}

		// Loop settings and get values to save
		foreach ( $this->settings as $key => $setting ) {

			$setting_type = $setting['type'] ?? '';
			if ( ! $setting_type ) {
				continue;
			}

			// Format the value based on settings type.
			switch ( $setting_type ) {
				case 'number':
					$instance[ $key ] = absint( $new_instance[ $key ] );

					if ( isset( $setting['min'] ) && '' !== $setting['min'] ) {
						$instance[ $key ] = max( $instance[ $key ], $setting['min'] );
					}

					if ( isset( $setting['max'] ) && '' !== $setting['max'] ) {
						$instance[ $key ] = min( $instance[ $key ], $setting['max'] );
					}
					break;
				case 'textarea':
					$instance[ $key ] = wp_kses( trim( wp_unslash( $new_instance[ $key ] ) ), wp_kses_allowed_html( 'post' ) );
					break;
				case 'checkbox':
					$instance[ $key ] = empty( $new_instance[ $key ] ) ? 0 : 1;
					break;
				default:
					$instance[ $key ] = isset( $new_instance[ $key ] ) ? sanitize_text_field( $new_instance[ $key ] ) : $setting['std'];
					break;
			}

			// Sanitize the value of a setting.
			$instance[ $key ] = apply_filters( 'w_widget_settings_sanitize_option', $instance[ $key ], $new_instance, $key, $setting );
		}

		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * @param $instance
	 *
	 * @return void
	 */
	public function form( $instance ): void {
		if ( empty( $this->settings ) ) {
			return;
		}

		foreach ( $this->settings as $key => $setting ) {

			$class = $setting['class'] ?? '';
			$value = $instance[ $key ] ?? $setting['std'];

			switch ( $setting['type'] ) {
				case 'text':
					?>
                    <p>
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo wp_kses_post( $setting['label'] ); ?></label><?php // phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped
						?>
                        <input class="widefat <?php echo esc_attr( $class ); ?>"
                               id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
                               name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="text"
                               value="<?php echo esc_attr( $value ); ?>"/>
						<?php if ( isset( $setting['desc'] ) ) : ?>
                            <small class="help-text"><?php echo $setting['desc']; ?></small>
						<?php endif; ?>
                    </p>
					<?php
					break;

				case 'number':
					?>
                    <p>
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
                        <input class="widefat <?php echo esc_attr( $class ); ?>"
                               id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
                               name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="number"
                               min="<?php echo esc_attr( $setting['min'] ); ?>"
                               max="<?php echo esc_attr( $setting['max'] ); ?>"
                               value="<?php echo esc_attr( $value ); ?>"/>
						<?php if ( isset( $setting['desc'] ) ) : ?>
                            <small class="help-text"><?php echo $setting['desc']; ?></small>
						<?php endif; ?>
                    </p>
					<?php
					break;

				case 'select':
					?>
                    <p>
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
                        <select class="widefat <?php echo esc_attr( $class ); ?>"
                                id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
                                name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>">
							<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
                                <option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
                        </select>
						<?php if ( isset( $setting['desc'] ) ) : ?>
                            <small class="help-text"><?php echo $setting['desc']; ?></small>
						<?php endif; ?>
                    </p>
					<?php
					break;

				case 'textarea':
					?>
                    <p>
                        <label for="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></label>
                        <textarea class="widefat <?php echo esc_attr( $class ); ?>"
                                  id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
                                  name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" cols="20"
                                  rows="2"><?php echo esc_textarea( $value ); ?></textarea>
						<?php if ( isset( $setting['desc'] ) ) : ?>
                            <small class="help-text"><?php echo $setting['desc']; ?></small>
						<?php endif; ?>
                    </p>
					<?php
					break;

				case 'checkbox':
					?>
                    <p>
                        <label>
                            <input class="ehd-checkbox checkbox <?php echo esc_attr( $class ); ?>"
                                   id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>"
                                   name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox"
                                   value="1" <?php checked( $value, 1 ); ?> />
                            <span class="message"><?php echo $setting['label']; /* phpcs:ignore WordPress.XSS.EscapeOutput.OutputNotEscaped */ ?></span>
                        </label>
                    </p>
					<?php
					break;

				// Default: run an action.
				default:
					do_action( 'widget_field_' . $setting['type'], $key, $value, $setting, $instance );
					break;
			}
		}
	}

	/**
	 * @param int $number
	 */
	public function _register_one( $number = - 1 ): void {
		parent::_register_one( $number );
		if ( $this->registered ) {
			return;
		}

		$this->registered = true;

		if ( is_active_widget( false, false, $this->id_base, true ) ) {
			add_action( 'wp_enqueue_scripts', [ &$this, 'styles_and_scripts' ], 12 );
		}
	}

	/**
	 * styles_and_scripts
	 */
	public function styles_and_scripts() {}

	/**
	 * @param $instance
	 * @param $default_settings
	 *
	 * @return array
	 */
	protected function swiperOptions( $instance, $default_settings ): array {
		$rows = Helper::notEmpty( $instance['rows'] ) ? absint( $instance['rows'] ) : $default_settings['rows']['std'];

		$_columns_number = $instance['columns_number'] ?? $default_settings['columns_number']['std'];
		$_gap            = $instance['gap'] ?? $default_settings['gap']['std'];
		$_columns_number = Helper::separatedToArray( $_columns_number, ',' );
		$_gap            = Helper::separatedToArray( $_gap, ',' );

		$pagination = isset( $instance['pagination'] ) ? sanitize_title( $instance['pagination'] ) : $default_settings['pagination']['std'];
		$direction  = isset( $instance['direction'] ) ? sanitize_title( $instance['direction'] ) : $default_settings['direction']['std'];
		$effect     = isset( $instance['effect'] ) ? sanitize_title( $instance['effect'] ) : $default_settings['effect']['std'];

		$navigation = Helper::notEmpty( $instance['navigation'] );
		$autoplay   = Helper::notEmpty( $instance['autoplay'] );
		$loop       = Helper::notEmpty( $instance['loop'] );
		$marquee    = Helper::notEmpty( $instance['marquee'] );
		$scrollbar  = Helper::notEmpty( $instance['scrollbar'] );

		$delay = Helper::notEmpty( $instance['delay'] ) ? absint( $instance['delay'] ) : $default_settings['delay']['std'];
		$speed = Helper::notEmpty( $instance['speed'] ) ? absint( $instance['speed'] ) : $default_settings['speed']['std'];

		//...
		$desktop_gap     = $_gap[0] ?? 0;
		$mobile_gap      = $_gap[1] ?? 0;
		$columns_desktop = $_columns_number[0] ?? 0;
		$columns_tablet  = $_columns_number[1] ?? 0;
		$columns_mobile  = $_columns_number[2] ?? 0;

		//...
		$swiper_class = '';
		$_data        = [
			'observer' => true,
		];

		if ( $desktop_gap ) {
			$_data['desktop_gap'] = absint( $desktop_gap );
		}
		if ( $mobile_gap ) {
			$_data['mobile_gap'] = absint( $mobile_gap );
		}

		if ( $delay > 0 ) {
			$_data['delay'] = $delay;
		}
		if ( $speed > 0 ) {
			$_data['speed'] = $speed;
		}

		if ( $pagination ) {
			$_data['pagination'] = $pagination;
		}
		if ( $direction ) {
			$_data['direction'] = $direction;
		}
		if ( $effect ) {
			$_data['effect'] = $effect;
		}

		if ( $navigation ) {
			$_data['navigation'] = true;
		}
		if ( $autoplay ) {
			$_data['autoplay'] = true;
		}
		if ( $loop ) {
			$_data['loop'] = true;
		}

		if ( $marquee ) {
			$_data['marquee'] = true;
			$swiper_class     .= ' marquee';
		}
		if ( $scrollbar ) {
			$_data['scrollbar'] = true;
			$swiper_class       .= ' scrollbar';
		}

		if ( ! $columns_desktop || ! $columns_tablet || ! $columns_mobile ) {
			$_data['autoview'] = true;
			$swiper_class      .= ' autoview';
		} else {
			$_data['desktop'] = absint( $columns_desktop );
			$_data['tablet']  = absint( $columns_tablet );
			$_data['mobile']  = absint( $columns_mobile );
		}

		if ( $rows > 1 ) {
			$_data['row']  = $rows;
			$_data['loop'] = false;
			$swiper_class  .= ' multirow';
		}

		return [
			'class' => $swiper_class,
			'data'  => json_encode( $_data, JSON_FORCE_OBJECT | JSON_UNESCAPED_UNICODE ),
		];
	}

	/**
	 * @param $id
	 *
	 * @return object
	 */
	protected function acfFields( $id ): ?object {
		if ( ! class_exists( '\ACF' ) ) {
			return (object) [];
		}

		$_fields = \get_fields( $id ) ?? [];

		return Helper::toObject( $_fields );
	}
}
