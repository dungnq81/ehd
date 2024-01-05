<?php

namespace EHD_Cores\Traits;

\defined( 'ABSPATH' ) || die;

trait Plugin {
	use Wp;

	// --------------------------------------------------

	/**
	 * @var array
	 */
	public static array $checkedPlugins = [];

	/**
	 * @param $plugin
	 *
	 * @return bool|mixed
	 */
	public static function isPluginActive( $plugin ): mixed {
		if ( isset( self::$checkedPlugins[ $plugin ] ) ) {
			return self::$checkedPlugins[ $plugin ];
		}

		$is_active                       = self::isPluginMustUse( $plugin ) || self::isPluginActiveForLocal( $plugin ) || self::isPluginActiveForNetwork( $plugin );
		self::$checkedPlugins[ $plugin ] = $is_active;

		return $is_active;
	}

	/**
	 * @param $plugin
	 *
	 * @return bool
	 */
	public static function isPluginMustUse( $plugin ): bool {
		$mu_plugins = wp_get_mu_plugins();

		// Must Use
		if ( is_dir( WPMU_PLUGIN_DIR ) ) {
			$mu_dir_plugins = \glob( WPMU_PLUGIN_DIR . '/*/*.php' );

			// Must Use
			if ( ! empty( $mu_dir_plugins ) ) {
				foreach ( $mu_dir_plugins as $aplugin ) {
					$mu_plugins[] = $aplugin;
				}
			}
		}

		require_once ABSPATH . 'wp-admin/includes/plugin.php';
		if ( ! empty( $mu_plugins ) ) {
			foreach ( $mu_plugins as $aplugin ) {
				$plugin_data = get_plugin_data( $aplugin );
				//if (!empty($plugin_data['Name']) && $plugin_data['Name'] == 'Advanced Custom Fields PRO') {
				//$mu_plugins[] = str_replace('acf.php', 'advanced-custom-fields-pro.php', $aplugin);
				//break;
				//}
			}
		}

		return self::checkPlugin( $plugin, $mu_plugins );
	}

	/**
	 * @param $plugin
	 *
	 * @return bool
	 */
	public static function isPluginActiveForLocal( $plugin ): bool {
		$active_plugins = self::getOption( 'active_plugins', [] );

		return self::checkPlugin( $plugin, $active_plugins );
	}

	/**
	 * @param $plugin
	 *
	 * @return false
	 */
	public static function isPluginActiveForNetwork( $plugin ): bool {
		$active_plugins = self::getOption( 'active_sitewide_plugins' );
		if ( ! empty( $active_plugins ) ) {
			$active_plugins = array_keys( $active_plugins );

			return self::checkPlugin( $plugin, $active_plugins );
		}

		return \false;
	}

	/**
	 * @param $plugin
	 * @param array $active_plugins
	 *
	 * @return bool
	 */
	public static function checkPlugin( $plugin, array $active_plugins = [] ): bool {
		if ( in_array( $plugin, (array) $active_plugins ) ) {
			return true;
		}

		if ( ! empty( $active_plugins ) ) {
			foreach ( $active_plugins as $aplugin ) {
				$tmp = basename( $aplugin );
				$tmp = pathinfo( $tmp, \PATHINFO_FILENAME );
				if ( $plugin == $tmp ) {
					return true;
				}
			}
		}
		if ( ! empty( $active_plugins ) ) {
			foreach ( $active_plugins as $aplugin ) {
				$pezzi = explode( '/', $aplugin );
				$tmp   = reset( $pezzi );
				if ( $plugin == $tmp ) {
					return true;
				}
			}
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public static function isWoocommerceActive(): bool {
		if ( class_exists( '\WooCommerce' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public static function isAcfActive(): bool {
		if ( class_exists( '\ACF' ) && \defined( 'ACF' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public static function isAcfProActive(): bool {
		if ( class_exists( '\ACF' ) && \defined( '\ACF_PRO' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public static function isElementorActive(): bool {
		if ( class_exists( '\Elementor\\Plugin' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @return bool
	 */
	public static function isElementorProActive(): bool {
		if ( class_exists( '\ElementorPro\\Plugin' ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param bool $ret
	 * @param array $deps
	 *
	 * @return array|bool
	 */
	public static function checkPluginDependencies( bool $ret = false, array $deps = [] ): bool|array {
		$depsDisabled = [];
		if ( ! empty( $deps ) ) {
			$isActive = true;
			foreach ( $deps as $pkey => $plugin ) {
				if ( ! is_numeric( $pkey ) ) {
					if ( ! self::isPluginActive( $pkey ) ) {
						$isActive = false;
					}
				} else {
					if ( ! self::isPluginActive( $plugin ) ) {
						$isActive = false;
					}
				}
				if ( ! $isActive ) {
					if ( ! $ret ) {
						return false;
					}
					if ( is_numeric( $pkey ) ) {
						$depsDisabled[] = $plugin;
					} else {
						$depsDisabled[] = $pkey;
					}
				}
			}
		}

		if ( $ret ) {
			return $depsDisabled;
		}

		return true;
	}
}
