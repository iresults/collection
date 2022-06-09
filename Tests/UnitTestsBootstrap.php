<?php
declare(strict_types=1);

namespace Iresults\Collection\Tests;

use function file_exists;

/**
 * Bootstrapping for unit tests
 *
 * @package Iresults\ResourceBooking\Tests
 */
class UnitTestsBootstrap
{
    /**
     * Bootstrap the testing environment
     */
    public function bootstrapSystem()
    {
        $this->registerAutoloader();
    }

    /**
     * Require composer's autoloader
     */
    protected function registerAutoloader()
    {
        if (file_exists(__DIR__ . '/../vendor/autoload.php'))
        require_once __DIR__ . '/../vendor/autoload.php';
    }
}

$bootstrap = new UnitTestsBootstrap();
$bootstrap->bootstrapSystem();
unset($bootstrap);
