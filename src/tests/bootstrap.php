<?php
/**
 * Helper for setting up tests.
 *
 * @author    Ch'Ih-Yu <chi-yu@web.de>
 * @copyright 2020 random-host.tv
 * @license   http://www.debian.org/misc/bsd.license  BSD License (3 Clause)
 *
 * @see       https://github.random-host.tv/image/
 */
if (!defined('APP_TOPDIR')) {
    define('APP_TOPDIR', realpath(__DIR__.'/../php'));
    define('VENDOR', realpath(__DIR__.'/../../vendor'));
    define('APP_TESTDIR', realpath(__DIR__.'/unit-tests/php'));
    define('APP_LIBDIR', realpath(VENDOR.'php'));

    define('PHPSPEC_BASE', VENDOR.'/phpspec');
    define('BEHAT_BASE', VENDOR.'/behat');
    define('SYMFONY', VENDOR.'/symfony');
}

require_once VENDOR.'/autoload.php';
