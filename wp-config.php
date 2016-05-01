<?php
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
define('DB_NAME', 'klubin_1');

/** MySQL database username */
define('DB_USER', 'klubin_1');

/** MySQL database password */
define('DB_PASSWORD', 'fEq!7BEHlPoB#7rQQhuS5u');

/** MySQL hostname */
define('DB_HOST', 'sql.klubin.nazwa.pl:3306');

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
define('AUTH_KEY',         '{S?(=D&JjD&Q5v(QUpiwm{G|S=l*2rf6xc<T-xwuF$S^X>zLwj~T`rt Q|3 [x8k');
define('SECURE_AUTH_KEY',  '2Y<TIljLO;I7$@cKs+U#KBAx?WY~KmJ7E:ye6eLgG#3A@?X0aY&a.CPNLU bT7<^');
define('LOGGED_IN_KEY',    '<O,,#[4uhTeE+ZPM38h9*6jR%v2a63Dp4,>E[ENUc<oaN#f~)l M8{;(#WKyQ3S6');
define('NONCE_KEY',        '89@T);F{K_8Q9XH%NBth&G>_.),Zp6K<klc>AJUV0o[Lmo=E<_mzaG!vX{{aTI+0');
define('AUTH_SALT',        'y-9#Q+.ywG]E:jO&!eL%9j]%{ZSATM907cW<X(^<86h,~~aG|Ln&Cji7*I^o2&pv');
define('SECURE_AUTH_SALT', 'eam{*irGpTYFNf^K*l^$wzkSTKnV6>nJeY&O?x6)1iq~V:K}ao{wt|%rU=%$T=ow');
define('LOGGED_IN_SALT',   'sWlyaY4zuBY<*{Y>lmwNQijyr}Xz=mU!FxJD?de)I8cD;n+?>Iqwr(Pl)$~!}K9h');
define('NONCE_SALT',       'N}-ECxNaB/,d;i?X)5zQq,+2$:^V,H5d`fZCMU*d&#FK/+1zr#U.crpf/h3<{L]q');

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'dp_';

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
