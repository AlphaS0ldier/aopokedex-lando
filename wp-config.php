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
 * @link https://wordpress.org/documentation/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'wordpress' );

/** Database password */
define( 'DB_PASSWORD', 'wordpress' );

/** Database hostname */
define( 'DB_HOST', 'database' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         'zF;H.&vZyh&d[CDpWYc-6wl+*bX(l_sWyq^bqvZ$K*dRj=WX-%HYS$()mj<mvhVM' );
define( 'SECURE_AUTH_KEY',  'Q-314[zG:s`D2>y*=7wiO~X=KNnFs`D)(U8$+N]c9O}pB#Nsau:n+d*#.)W_Z25r' );
define( 'LOGGED_IN_KEY',    '/gg]xOzoU(+h,T7~DR6csi4<f]LxU:qz{~HgIsPr)1ahqV6<o#/W( P`Ow=0{~_~' );
define( 'NONCE_KEY',        '_-MD0Zi^lGq)6xG)3_,C.bgtU;_{2UwcYjc>G,J*,oY%ZQGoj}ITDm[/h&d:c)<6' );
define( 'AUTH_SALT',        '87eI#SPp>PB<:O0y3Vjh*Gp[m.},17Byr%u>Kap1$6dD4Ss4E}1:X3Sv- S@]Ka%' );
define( 'SECURE_AUTH_SALT', 'mGTl&}qIt36H6a20&~N&:K@,*e<[IZ<`|EcqME?G71GM^Gv}nSohN{4M_^o]~lOx' );
define( 'LOGGED_IN_SALT',   'mM{ X-%n sm0e}n_BGq 5Zgsdq|0odS2i5w.C] SXscMWl]<S(MEc&tzL WEVcES' );
define( 'NONCE_SALT',       'n+Q9U%A2d[Q1A[a!7 C{&5hkg.V;LOTXLGz(u9;A1X;=<Ly31y@W@XEZd92vlsKB' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';

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
 * @link https://wordpress.org/documentation/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */

set_time_limit(30000);

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
