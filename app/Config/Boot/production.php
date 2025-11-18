<?php

/**
 * Workaround para quando a extensão intl não está disponível
 * Cria uma classe Locale polyfill básica
 */
if (!extension_loaded('intl')) {
    class Locale
    {
        private static $defaultLocale = 'pt-BR';

        public static function getDefault(): string
        {
            return self::$defaultLocale;
        }

        public static function setDefault(string $locale): bool
        {
            self::$defaultLocale = $locale;
            return true;
        }

        public static function canonicalize(string $locale): string
        {
            return $locale;
        }

        public static function acceptFromHttp(string $header): string
        {
            return self::$defaultLocale;
        }
    }
}

/*
 |--------------------------------------------------------------------------
 | ERROR DISPLAY
 |--------------------------------------------------------------------------
 | Don't show ANY in production environments. Instead, let the system catch
 | it and display a generic error message.
 |
 | If you set 'display_errors' to '1', CI4's detailed error report will show.
 */
error_reporting(E_ALL & ~E_DEPRECATED);
// If you want to suppress more types of errors.
// error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
ini_set('display_errors', '0');

/*
 |--------------------------------------------------------------------------
 | DEBUG MODE
 |--------------------------------------------------------------------------
 | Debug mode is an experimental flag that can allow changes throughout
 | the system. It's not widely used currently, and may not survive
 | release of the framework.
 */
defined('CI_DEBUG') || define('CI_DEBUG', false);
