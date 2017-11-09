<?php
/**
 * Default config settings
 *
 * Enter any WordPress config settings that are default to all environments
 * in this file.
 *
 * Please note if you add constants in this file (i.e. define statements)
 * these cannot be overridden in environment config files so make sure these are only set once.
 *
 * @package    Studio 24 WordPress Multi-Environment Config
 * @version    2.0.0
 * @author     Studio 24 Ltd  <hello@studio24.net>
 */


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
define('AUTH_KEY',         'udA+?6ay_>lJ`BZVgcnsI}kJ1Iw52M:UXJWPQvG<-M1L6DW/ _UA5P}##u6$p*OP');
define('SECURE_AUTH_KEY',  '`R@G+<>b`2>-m};FYc%>[ 6s1Yt5RDX`!8HUBFd7HME7>O.Q|iwB.gSs5By<|(pV');
define('LOGGED_IN_KEY',    '$nW@=$/XL3Os5D{Li4<-E<ed7@&qgS_ew/`|[l;IiD!Yt%,8U)MVxh|5K62DL{`r');
define('NONCE_KEY',        'yrLi$yha>7Y>$0&7QZ9N%SfO>i|}#$.HRvl-iFA_>y?.f6x9rp?Ln)[9f8BAFJoh');
define('AUTH_SALT',        '[yp!1TS3$.$p)rTmz~E]eA?!;M-;+%BIa.`,zr,.{o!>,uPYa~VUrG%./EAjr.E~');
define('SECURE_AUTH_SALT', 'lm<;r=y&gsQd.5hTW%9dQ$}Nv8d+XkS4&rn`Y`rTR)f$4+DSeY,In@WVhhbBFF^>');
define('LOGGED_IN_SALT',   '%)8cq?BGPw;B:g{4^>-;:L- !oN;z&2pzF|f7n|/A(t/9a=+tbD5)H#dbIMcp+u#');
define('NONCE_SALT',       'BuU{*TWyk]9$l)<1x@^9j.IH583>)n b;!_HYR.X-&BZ5`_9?o#e,qRFzv-]Pho$');

/**#@-*/


/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * Increase memory limit.
 */
define('WP_MEMORY_LIMIT', '128M');
