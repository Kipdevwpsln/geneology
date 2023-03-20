<?php

//Begin Really Simple SSL session cookie settings
@ini_set('session.cookie_httponly', true);
@ini_set('session.cookie_secure', true);
@ini_set('session.use_only_cookies', true);
//END Really Simple SSL cookie settings
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
define('DB_NAME', 'aejzmclacck');
/** MySQL database username */
define('DB_USER', 'aejzmclacck');
/** MySQL database password */
define('DB_PASSWORD', 'Welcome123');
/** MySQL hostname */
define('DB_HOST', 'aejzmclacck.mysql.db:3306');
/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');
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
define('AUTH_KEY',         'COsmIoAXIAQ/zUVFnA7zJNs970nMHFtyJG2F3+UXiPopf2JrGCKay+W4/Cxc');
define('SECURE_AUTH_KEY',  'nbifAsVqklSE3JELlKkPBulO6oltho/sI6J6tgiw5Kq7et7Sgg+y/nERNHbw');
define('LOGGED_IN_KEY',    'roVvEWQcA6MEpVSLKdk9z+cGTRTzLgP4jcw/euNR6eFT/HV54VWez+MtCxb2');
define('NONCE_KEY',        '0O7nbDzs7IU+VxoYEoEM8IcjnLSBvq7SVVHtvrN3l6bEmXOloaGLCmSVXmbH');
define('AUTH_SALT',        'T/sMMAMb4EDAnIr3y/nQlA+Jer0ozp34vHYYYrF+8ZTNIxUzvt8KYggZYndh');
define('SECURE_AUTH_SALT', 'j347doTr+/Rdi8bA8z6InCYLjk3ET6pI2cRAXKTgnY/cL8RW4mslxW+pW7dp');
define('LOGGED_IN_SALT',   'Lqiky3f7t9WgtDG25EXFp6tC3EfnkaOe5yip/eJw/QeNo/66Y7/zOoroT7TU');
define('NONCE_SALT',       'RlV5NQJBFATXQ6LeEqNNJUbHN1n7qPRrTQ/+AsFupoe5yD9wHJ0M+oUR8HI3');
/**#@-*/
/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wor4233_';
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
define('WP_DEBUG', true);
/* That's all, stop editing! Happy blogging. */
/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');
/* Fixes "Add media button not working", see http://www.carnfieldwebdesign.co.uk/blog/wordpress-fix-add-media-button-not-working/ */
define('CONCATENATE_SCRIPTS', false );
/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
