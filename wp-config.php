<?php
/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache


/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'LALALA' );

/** MySQL database username */
define( 'DB_USER', 'LALALA' );

/** MySQL database password */
define( 'DB_PASSWORD', 'LALALA' );

/** MySQL hostname */
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
define( 'AUTH_KEY',         'fvn1etlb27zpwbvihynnu35gomrsn4bhoi7ovu5cs7vea8mujtxanbgvcea7gnrg' );
define( 'SECURE_AUTH_KEY',  'a1xvwsn6a5japqrnjcrdyqijawik9dlgremb52hk1yoglom6kgky7vn1pt69sxya' );
define( 'LOGGED_IN_KEY',    'k5igqhgymq99mddownjer34t7fw3fxxg2adhakws7hav13za5hligkdtkh1vywhm' );
define( 'NONCE_KEY',        'jtbp5gvghxics9ooybkixcwtseetci7gnqnjlrl5igshbqwr5sbywgbaxruutxgy' );
define( 'AUTH_SALT',        'ggjksdh06apzq1wjosa6me3tix2nmtpdn6f4g0rdwv4vc7hykmooyunmxokklzfv' );
define( 'SECURE_AUTH_SALT', 'kgfewwylgtz144rpglacc6ku7fynmkflri3knrq5ny90r0uyjvbttra1ybzmeiv1' );
define( 'LOGGED_IN_SALT',   'hmjebeh40yw0i8o4voxx5txcwyzvpmoop5lj6wxdltkcqsexdvjqckepjoivls03' );
define( 'NONCE_SALT',       'hr8mswtu1zoidtregeoqqq7fr9iyqgunejszhzmzwpq0yxoq7yuagz7cqytqr57m' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wpig_';

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
