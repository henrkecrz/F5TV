<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'u929288558_YRLdk' );

/** Database username */
define( 'DB_USER', 'u929288558_IShWr' );

/** Database password */
define( 'DB_PASSWORD', 'O_AG![9#6U' );

/** Database hostname */
define( 'DB_HOST', '127.0.0.1' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

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
define( 'AUTH_KEY',          'v9MBbFT<88kK,)m]Fi?5>W= l4k[=^[(b@;Xtl9+Btl<P;qTa^b41*7Sao&%LnQe' );
define( 'SECURE_AUTH_KEY',   'P0=j2lS9K}7s/q.[)jw+DW_k(-]$k<@heKbC~ZiB5W,<jv$WS^FDLyA(O$klR1mv' );
define( 'LOGGED_IN_KEY',     '^~f^eNl,:qt9?)_Ae%9C;GgTJ+x%hbz_m|~luI4|CsNXe%f^+V#d$F9g]lbbW4i3' );
define( 'NONCE_KEY',         '+;nGjCP.c;u!7bR0GT(qsiiGoRB-,Cr2Z!P3xI*~S=?C?Bg*P&.P|JN|dJ/qC-c-' );
define( 'AUTH_SALT',         '9IPquEYirr^6KX[:JHMH| *J/Ax:k[1a46nR3=#Ee`:4@|G I!g&q0PW!;cI<21Q' );
define( 'SECURE_AUTH_SALT',  'Kf}3_Qt975YUqMjY!*y&uN;4Cw~=8cg/v/22{(&t$/A7$(a~TKzmIfM p]];,ZSq' );
define( 'LOGGED_IN_SALT',    'M89|;>#seOk%aIkjg#e,1v>2gy)He[_gy%u69%(hKnG&4_s];k!GGF;k2:+tLJQ7' );
define( 'NONCE_SALT',        '*pv6Y)n/]BUNwRJx8=>Gi=8agY[rI,iKhYrm]eK|@ciu/bv({I5J#w$2hIo#}z,E' );
define( 'WP_CACHE_KEY_SALT', 'S,M$c[8A%44n`#kLSMohlA^Reak}Tk[g7:#E1)qp979`VkojGR5^7al0JliTi>})' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



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
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'FS_METHOD', 'direct' );
define( 'COOKIEHASH', '24e3b9d7978616ee787dea98b49fed67' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
