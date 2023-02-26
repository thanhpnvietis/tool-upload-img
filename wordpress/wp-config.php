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
define('DB_NAME', 'toolwp');

/** Database username */
define('DB_USER', 'root');

/** Database password */
define('DB_PASSWORD', 'Password@123');

/** Database hostname */
define('DB_HOST', 'localhost');

/** Database charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The database collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

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
define('AUTH_KEY', 'N]|/1+Ho2v$2S+eIOMHQwm3~@$K}Ga71<mFpRhvTM 6lAfh[wZZIu_7Ns;(X27u4');
define('SECURE_AUTH_KEY', '>WG[f3S=Dyr%-ZJ%W?X]?kTG6Os4G:h]BK<w`D`.RuA`o!5L0i42./k{R`KD%)_M');
define('LOGGED_IN_KEY', '%a1<2+$W9Z.=4%auF6Da70N+FD6-sZEKk /}kD(L{&[=*+6VU8_~l1BOnp`)LtY9');
define('NONCE_KEY', 'w}ZeFLh]sVvKgiLJ3+N-W=W+[pn|@(hJ5,K<EGEF~14.}47`|k*d2}wQz;fy!mym');
define('AUTH_SALT', ';AwDP:m%l]MnOphF4+Yp~/o?$cB:DEA$J%Xil+m)9Mw~+.Ew0}G{B?t$3|hQs*Bx');
define('SECURE_AUTH_SALT', 'hIy}+LIo,kVD9?-~K a!&$8+|`zk<:%k9cNx!)*{ZY-u]oF!3:(KbV3bi)cBTM.[');
define('LOGGED_IN_SALT', '{ip0kOLOT(GSk?4KQRow4]E-f~Q/FYK$8u1{G`Z)ai7d/E8Hpt#E*guyrA(*4Zoj');
define('NONCE_SALT', 'dhQ*Q~$yJY{y<AkJr;xT+[OVF@X4>Sp8!11<7VH/K_~1(ECT!Q.c_d=CcF9@q?E%');

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
define('WP_DEBUG', false);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if (!defined('ABSPATH')) {
  define('ABSPATH', __DIR__ . '/');
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
