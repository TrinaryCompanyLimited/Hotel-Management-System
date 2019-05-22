<?php
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
 set_time_limit(300);

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'guesthouse');

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
define('AUTH_KEY',         ')>WgpXyMlMe3DUF)x!Fq}/JW{^W)_oKv!cARyg{se*?9--9<Nc,!G}`U5qf+!J6.');
define('SECURE_AUTH_KEY',  '~YV^~KKN%U=sHqtCKyR}TGzzp.w--FiYO]LoNLIl-bm81+H?ndb^HFLN;;K~BD)$');
define('LOGGED_IN_KEY',    ' D}~l_B@9D0;*8ZGZ7N}euc7k)T#Tag}`7R0fTzQ1J9/ix+3QVr^ <lr=3R`O&pv');
define('NONCE_KEY',        'xqE. OJYbwK4u}uSOE-J1j[@%OzKh y0ZZipsDl8BW?vE8oP~t,~?o~qC@hXE$pm');
define('AUTH_SALT',        'mfKz~96%yf0#Hg(2U:^3U`|]mq]Oa8k)b!!VP|;F^FL+q;`|DJ%6LSTmEb9%)!r^');
define('SECURE_AUTH_SALT', '{3+~!0-mOx|ndZ@oQBD%Qgcp#EVvh5Y&$*%O7EzdgxVS?G-(yW}.@*q$gA=oSBRX');
define('LOGGED_IN_SALT',   '|iH[yD*VF.+HR%)@nnU/Hl@O#,@HvN,J4TBNl;o[x:da2q+&`q^*&;^3LjJ>?A#.');
define('NONCE_SALT',       'IqIY+ Q4HQzx:$b8YiAc~9LNFloH%i1i0m(*tm8ZI0^!+Lm6MTovv/&B-EdIx?fS');

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
