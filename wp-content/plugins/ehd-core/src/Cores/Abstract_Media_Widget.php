<?php

namespace EHD_Cores;

use ReflectionClass;
use WP_Widget_Media;

\defined( 'ABSPATH' ) || die;

abstract class Abstract_Media_Widget extends WP_Widget_Media {

	protected string $prefix = 'w-';
	protected string $widget_id;
	protected string $widget_classname;
	protected string $widget_name = 'Unknown Widget';
	protected string $widget_description = '';
	protected string $widget_mime_type = '';

	/**
	 * Constructor.
	 */
	public function __construct() {
		$className = ( new ReflectionClass( $this ) )->getShortName();
		$this->widget_classname = str_replace( [ '_widget', '-widget' ], '', Helper::dashCase( strtolower( $className ) ) );
		$this->widget_id = $this->prefix . $this->widget_classname;

		parent::__construct(
			$this->widget_id,
			$this->widget_name,
			$this->widget_options(),
			$this->control_options()
		);

		$this->l10n = array_merge( self::get_l10n_defaults(), array_filter( $this->l10n ) );

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

		return apply_filters( 'media_w_cached_widget_id', $widget_id_for_cache );
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
	 * @param int $number
	 */
	public function _register_one( $number = - 1 ): void {
		parent::_register_one( $number );

		if ( is_active_widget( false, false, $this->id_base, true ) ) {
			add_action( 'wp_enqueue_scripts', [ &$this, 'styles_and_scripts' ], 12 );
		}
	}

	/**
	 * styles_and_scripts
	 */
	public function styles_and_scripts() {}

	/**
	 * @param $id
	 * @return array|object
	 */
	protected function acfFields( $id ): ?object {
		if ( ! class_exists( '\ACF' ) ) {
			return [];
		}

		$_fields = \get_fields( $id ) ?? [];
		if ( $_fields ) {
			return Helper::toObject( $_fields );
		}

		return [];
	}
}