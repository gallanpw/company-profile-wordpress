<?php
/** Enable W3 Total Cache Edge Mode */
define( 'WPCACHEHOME', 'D:\xampp\htdocs\wpconn\wp-content\plugins\wp-super-cache/' ); //Added by WP-Cache Manager
define('W3TC_EDGE_MODE', true); // Added by W3 Total Cache

/** Enable W3 Total Cache */
define('WP_CACHE', true); // Added by W3 Total Cache

/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wpconn');

/** MySQL database username */
define('DB_USER', 'root');

/** MySQL database password */
define('DB_PASSWORD', '');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8mb4');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'VD2Z;2CIE+/f,UTZ+jr0kefWRM@Hr38k6#XA%CD6|D6N]>/-aa$9b%rTnZ k[g?R');
define('SECURE_AUTH_KEY',  '6h ][t!7t]*&ju!KP$Hlc|AB;5}._!uY:O7n}^cfMLM+RT2UW=fXX&=ga2(tGWW ');
define('LOGGED_IN_KEY',    '[,/38.g30JoFmAp)HDuI$rK1aKb/lJpt!S0lihrMSh4KvVtc|3w5+%s>bGjR_ZVa');
define('NONCE_KEY',        '**==qR[84~@Znmb[+.gY08<V,qgM7rM1PJO@KeY>QQ1>bCW!v1@DPjp9<;t_tY[(');
define('AUTH_SALT',        '-nw!ex^.<3kUa?H:`<{~R6R_tAcIQn1!Ti_(%g!M4mjw{l+SN*9}!p.l;m0d1h=C');
define('SECURE_AUTH_SALT', '6f{SE(p;Bl0fu5k%wk7zw+6QF:t`Gc}9gxC&v4&[J`xpl-V-pIg50*E`Vzfci,yB');
define('LOGGED_IN_SALT',   'g=Vjya3LI,v2,oQfYg[:)M]EYSRAj%C<:,~Qh?zcv4GEp|}iQAfIz%yH0?v8!hFO');
define('NONCE_SALT',       '?2B7VK#w>n@FH:T_:ey-zD3U`.D@/w%qE0g|dWd1txn)|e&@shR_f(N0f9YHbbp2');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the Codex.
 *
 * @link https://codex.wordpress.org/Debugging_in_WordPress
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
