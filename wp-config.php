<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

/** PHP Memory */
const WP_MEMORY_LIMIT     = '512M';
const WP_MAX_MEMORY_LIMIT = '512M';

const DISALLOW_FILE_EDIT = true;
const DISALLOW_FILE_MODS = false;

/* SSL */
const FORCE_SSL_LOGIN = true;
const FORCE_SSL_ADMIN = true;

const WP_POST_REVISIONS = 1;
const EMPTY_TRASH_DAYS  = 5;
const AUTOSAVE_INTERVAL = 120;

const ALLOW_UNFILTERED_UPLOADS = true;

/** Disable WordPress core auto-update, */
const WP_AUTO_UPDATE_CORE = false;

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */

const DB_NAME = 'ehd';
const DB_USER = 'root';
const DB_PASSWORD = 'root';

const DB_HOST = 'localhost';
const DB_CHARSET = 'utf8mb4';
const DB_COLLATE = '';

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'tIxNNs88XY@#R bkN/$00`/Z:n #,03$2ngb)S[k%7t?)2XTIxcks>=!z@7t`+0A' );
define( 'SECURE_AUTH_KEY',  '!v$H J+-ooM?~*_nt4FrB^ nop2K<PqCOzES!Jidpk/p9_vm^s!,H(gL1~dqI-_+' );
define( 'LOGGED_IN_KEY',    'mU&5|m%c:t Wpcx[0A$QH(5e^UeDqJ$Gj6m <i;-d^!9,>?F^p}4vjc=`Tp`I` q' );
define( 'NONCE_KEY',        'zm:p+1hfw&#t*M!$d%fSY!q--$7Lwk*Sms <,<pOw#aA~Dqd:-h,]]rZRZ)~;n|%' );
define( 'AUTH_SALT',        '&L>,GIlHl5f?jZ09H3C4MPTWp|6j* 1U}~*T8*HRcTH#g_=e;mOm44V{`*QCI^a`' );
define( 'SECURE_AUTH_SALT', '3S(WRjvJe,?+=R~jj}FS`;bmpWc-tqmZ5rXfjpz]BhB*/ps&x/93!{b%a;z6&}dX' );
define( 'LOGGED_IN_SALT',   'CPl{m:b}oEluv8=.>PSVknH]C-Q%uwc*3DXn(sz):)5rAIpAdb33dx2,~a3vc8[K' );
define( 'NONCE_SALT',       '>`>6qq@Ls Cv/Ig/KWfM#xiQtdGuTUP!5>|r1?_.zm+`ti}K! Ea06QMmRwri&ji' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'w_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
const WP_DEBUG = false;

/* Add any custom values between this line and the "stop editing" line. */

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
