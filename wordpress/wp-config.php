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
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** Database username */
define( 'DB_USER', 'thanhpn' );

/** Database password */
define( 'DB_PASSWORD', 'Password@123' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

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
define( 'AUTH_KEY',         '46>Y# 3dSHHO=)Q@9:D0iMh<Hc0naEikyaTP9lxo[;Qsh<z8E2_qaC]xnmE=U:=m' );
define( 'SECURE_AUTH_KEY',  '{K!hO3Tz%4tZQxc)C.5?-/5: Z9h<exlxsFMW9_Z*G?|7]7:p#m;[vEA}!5oA$&r' );
define( 'LOGGED_IN_KEY',    'xNnt1~CK0}nHZA cb$Q[>f%PHR{/2dDu]%hfth&Em:RAZ|0|Ef/wV,XYFNt4I8/m' );
define( 'NONCE_KEY',        'T-dYH}Lc$U[Suhre6kH$6C5ERo2SSj}@}BlmcUMGLCz/3k19:JrmGGYX?0Dp>^`[' );
define( 'AUTH_SALT',        'B}F2]cca2~Dom7Ii)faFZ4rGdFO7Oh/_~3+U2 _Nq)4b8xG1I!xMY%AA(w_yTu<}' );
define( 'SECURE_AUTH_SALT', 'G;4WMzZ${QVzF-^XXpx5NrY)6vLu1&u%A%b`gYpH*>ALM($XhJuemTy8[[71klKc' );
define( 'LOGGED_IN_SALT',   'dY0(B{O[.ZEcX?lu#pgA9_F3=&QgpL@Y)Jik-mr:{1UsJwrWu*5l6DPc*$8]MF)K' );
define( 'NONCE_SALT',       'Q._(`X?IGd{;),Ra>g;=!MYK#J<AXPyO 2A.}M`#z*9B|D+sy]5i{q*kjT}QY%Nd' );

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
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
