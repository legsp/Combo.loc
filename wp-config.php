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
define( 'DB_NAME', 'u982564790_hT9RY' );

/** Database username */
define( 'DB_USER', 'u982564790_9iLmT' );

/** Database password */
define( 'DB_PASSWORD', 'XZQQZlmD92' );

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
define( 'AUTH_KEY',          'MsjeTl1uza;!>(Z 1Nm/o;c1zAHn^w_3}QlNl&ykT_Y<G[!UU@1ri%)FlFB8e0KP' );
define( 'SECURE_AUTH_KEY',   'E?[sU`3q][.*QN2r7:c,Ql8N)|4@(bqz~#iaE,29|:_W&Cy3;.0cyhf5x;pgRhIv' );
define( 'LOGGED_IN_KEY',     'Id3%fil|D+d$G=mc-Q{f. j4`$@}r%<by<}j<tD/6!fCGE5-Q_3w53@jE(7}|B)D' );
define( 'NONCE_KEY',         'w.ROh:7Ar5#Upbji`h|nf%Lq]<Xey+d@j! 4%y0H.]k+tkA?$#?/Oh&S,hcm,4m0' );
define( 'AUTH_SALT',         'g>BF2[FSE,!CCKD1z.E@>W_5x0BozEalnl.oFJH],1Y{:^r6a.WP^QXVQ@l<v dy' );
define( 'SECURE_AUTH_SALT',  'SLs^{f~]bEMgb%gK]TIMv3E0x&fZa%YCx5=pI@6#Q*Sornlyxx;_VO90%O!IPw1B' );
define( 'LOGGED_IN_SALT',    '};/3PAD1$!%_)0~,dhb0|b2bDl|6V``j!0r~Gm}d{5w{mQOZ!!s^2f%d`4gX2*E+' );
define( 'NONCE_SALT',        '>&gpN+@9}5qY~RHl&_fvx2H*O1RnXCXSLYP,]B)$n5M^foc;ZwDS~oQ]uzEj{9A!' );
define( 'WP_CACHE_KEY_SALT', 'GH:5w)cqc36KV=Vnbg67b&&lV|sQDsH!Z6#<qVq]XsyX_5xNYXFlbvD^)AR@/F.1' );


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
define( 'COOKIEHASH', '1178125853be8e7e27c4684b26e1f687' );
define( 'WP_AUTO_UPDATE_CORE', 'minor' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
