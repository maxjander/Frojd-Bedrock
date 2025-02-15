<?php
const APP_VERSION = "2.2.1";

define('ROOT_DIR', dirname(__DIR__));
define('WEBROOT_DIR', ROOT_DIR . '/src');
define('VENDOR_DIR', ROOT_DIR . '/vendor');

/**
 * Use Dotenv to set required environment variables and load .env file in root
 * .env.local will override .env if it exists
 */
if (file_exists($root_dir . '/.env')) {
    $env_files = file_exists(ROOT_DIR . '/.env.local')
        ? ['.env', '.env.local']
        : ['.env'];

    $repository = Dotenv\Repository\RepositoryBuilder::createWithNoAdapters()
        ->addAdapter(Dotenv\Repository\Adapter\EnvConstAdapter::class)
        ->addAdapter(Dotenv\Repository\Adapter\PutenvAdapter::class)
        ->immutable()
        ->make();

    $dotenv = Dotenv\Dotenv::create($repository, $root_dir, $env_files, false);
    $dotenv->load();

    $dotenv->required(['DB_NAME', 'DB_USER', 'DB_PASSWORD', 'WP_HOME', 'WP_SITEURL']);
}

function getenv_bool($variable, $default = false) {
  $val = getenv($variable) ?: $default; // Fallback to default if not exists
  // Check if false or true, otherwise fallback to value
  $val = $val === 'false' ? false : ($val === 'true' ? true : $val);
  // If boolean value return it, otherwise fallback to default
  return is_bool($val) ? $val : $default;
}

/**
 * Set up our global environment constant and load its config first
 * Default: development
 */
define('WP_ENV', getenv('WP_ENV') ?: 'development');
define("IS_DEVELOPMENT", WP_ENV == 'development');

$env_config = __DIR__ . '/environments/' . WP_ENV . '.php';

if (file_exists($env_config)) {
  require_once $env_config;
}

/**
 * URLs
 */
define('WP_HOME', getenv('WP_HOME'));
define('WP_SITEURL', getenv('WP_SITEURL'));

/**
 * Custom Content Directory
 */
define('CONTENT_DIR', '/app');
define('WP_CONTENT_DIR', WEBROOT_DIR . CONTENT_DIR);
define('WP_CONTENT_URL', WP_HOME . CONTENT_DIR);

/**
 * DB settings
 */
define('DB_NAME', getenv('DB_NAME'));
define('DB_USER', getenv('DB_USER'));
define('DB_PASSWORD', getenv('DB_PASSWORD'));
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', '');
$table_prefix = getenv('DB_PREFIX') ?: 'wp_';

/**
 * Authentication Unique Keys and Salts
 */
define('AUTH_KEY', getenv('AUTH_KEY'));
define('SECURE_AUTH_KEY', getenv('SECURE_AUTH_KEY'));
define('LOGGED_IN_KEY', getenv('LOGGED_IN_KEY'));
define('NONCE_KEY', getenv('NONCE_KEY'));
define('AUTH_SALT', getenv('AUTH_SALT'));
define('SECURE_AUTH_SALT', getenv('SECURE_AUTH_SALT'));
define('LOGGED_IN_SALT', getenv('LOGGED_IN_SALT'));
define('NONCE_SALT', getenv('NONCE_SALT'));

/**
 * Custom Settings
 */
define('AUTOMATIC_UPDATER_DISABLED', true);
define('DISABLE_WP_CRON', true);
define('WP_POST_REVISIONS', 10);
define('WP_MEMORY_LIMIT', '124M');

/* Cookie script */
define('COOKIE_SCRIPT', getenv('COOKIE_SCRIPT', ''));

/**
 * Gravity forms
 */
define('GF_LICENSE_KEY', getenv('GF_LICENSE_KEY', ''));
define('GF_RECAPTCHA_PRIVATE_KEY', getenv('GF_RECAPTCHA_PRIVATE_KEY', ''));
define('GF_RECAPTCHA_PUBLIC_KEY', getenv('GF_RECAPTCHA_PUBLIC_KEY', ''));
define('GF_DEBUG', getenv_bool('GF_DEBUG', false));
define('GFORM_DISABLE_AUTO_UPDATE', getenv_bool('GFORM_DISABLE_AUTO_UPDATE', true));

/**
 * Elasticpress
 */
define('EP_HOST', getenv('EP_HOST'));

/**
 * Bootstrap WordPress
 */
if (!defined('ABSPATH')) {
  define('ABSPATH', WEBROOT_DIR . '/wp/');
}

/**
 * Sentry logging
 */
define('SENTRY_DSN', getenv('SENTRY_DSN') ?: null);
if (SENTRY_DSN) {
  define("WP_SENTRY_PHP_DSN", SENTRY_DSN);

  if (defined('CURRENT_SITE') && !empty(CURRENT_SITE)) {
    $env = CURRENT_SITE . "-" . WP_ENV . "-php";
  }
  define("WP_SENTRY_ENV", $env);

  define("WP_SENTRY_TRACES_SAMPLE_RATE", WP_ENV == "production" ? .2 : 1.0);
  define("WP_SENTRY_PROFILES_SAMPLE_RATE", WP_ENV == "production" ? .2 : 1.0);

  define("WP_SENTRY_BROWSER_DSN", true);
  define("WP_SENTRY_BROWSER_TRACES_SAMPLE_RATE", WP_ENV == "production" ? .05 : 1.0);
}
