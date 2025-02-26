<?php

/**
 * @wp-staging
 * Site         : http://localhost:8888/wordpress/excelsior
 * Parent       : http://localhost
 * Created at   : 26.02.2025 06:21:12
 * Updated at   : 26.02.2025 06:21:12
 * Read more    : https://wp-staging.com/docs/create-a-staging-site-clone-wordpress/
 */

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
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress_db' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

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
define( 'AUTH_KEY',         ';USuUXyTggO;bqq9Bk<mmjqmU{7*`cBdMFQ?E{W+e:Ht<5z3/#vHf,eBIxX[zG(E' );
define( 'SECURE_AUTH_KEY',  'YJK>ge{YC~VGTR4>ypjViPW,va<=9/f/R>gf]pyh r@MWQq!nPKJ(1+|o1B+cEpg' );
define( 'LOGGED_IN_KEY',    '0y+=L/-v{#_r/TqMq#k;FA[&U.?8ht9}|( __w%V]BMAFizp41=.^<E5_X7%qQ7n' );
define( 'NONCE_KEY',        'D]82;{NtdtN u&_P[hoild0 *uk)nF^~S-+a!Bl1[ytxhV|Z=;|evcrsL([yYwH_' );
define( 'AUTH_SALT',        'FOng7<r1!qn@! Y?,,E`9amcPoP~NYiMP^x8&XGn}Zx)p;d`t-B8D>4x2|RB-qf3' );
define( 'SECURE_AUTH_SALT', 'Mr})T.T<&pI8sa=IB5{S:!~9<n(_jkJ1&mK;JFcglNb:->Z6|&:@&2T`340!3RO)' );
define( 'LOGGED_IN_SALT',   'B+p/P@Qt6vv~-$~=`.HM0S0y+fy&0%1<.ILpL(B_,)c+Ef{, 0gP?b26)8y{hLom' );
define( 'NONCE_SALT',       'f+-,)66T%Btx4oezvgpJNQ|vywD`T{M%J:Yb3s*(zbU||ZCY+*3FGPYHgr8U<~LZ' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wpstg0_'; // Changed by WP STAGING

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define( 'WP_DEBUG', true );
define( 'WP_DEBUG_LOG', true );

/* Add any custom values between this line and the "stop editing" line. */

if ( ob_get_length() ) ob_end_clean();

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
define('WP_LANG_DIR', __DIR__ . '/wp-content/languages');
if ( ! defined( 'WP_HOME' ) ) {
    define('WP_HOME', 'http://localhost:8888/wordpress/excelsior');
}
if ( ! defined( 'WP_SITEURL' ) ) {
    define('WP_SITEURL', 'http://localhost:8888/wordpress/excelsior');
}
define('WP_CACHE', false);
define('DISABLE_WP_CRON', false);
if ( ! defined( 'WP_ENVIRONMENT_TYPE' ) ) {
    define('WP_ENVIRONMENT_TYPE', 'staging');
}
define('WP_DEVELOPMENT_MODE', 'all');
if ( ! defined( 'WPSTAGING_DEV_SITE' ) ) {
    define('WPSTAGING_DEV_SITE', true);
}
define('UPLOADS', 'wp-content/uploads');
define('WP_PLUGIN_DIR', __DIR__ . "/wp-content/plugins");
define('WP_PLUGIN_URL', 'http://localhost:8888/wordpress/excelsior/wp-content/plugins');
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
