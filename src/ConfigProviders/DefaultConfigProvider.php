<?php

declare(strict_types=1);

namespace OpenEuropa\TaskRunner\ConfigProviders;

use OpenEuropa\TaskRunner\Contract\ConfigProviderInterface;
use OpenEuropa\TaskRunner\Traits\ConfigFromFilesTrait;
use Robo\Config\Config;

/**
 * Provides the basic default configuration for the task runner.
 *
 * This will import the following files:
 * - The example configuration provided in the task runner library itself.
 * - The default configuration "runner.yml.dist" shipped in the root folder of the project which uses the task runner.
 *
 * This serves as a safe default implementation which can be overridden by environment specific configuration and user
 * preferences.
 */
class DefaultConfigProvider implements ConfigProviderInterface
{
    use ConfigFromFilesTrait;

    /**
     * {@inheritdoc}
     */
    public static function provide(Config $config): void
    {
        static::importFromFiles($config, [
            __DIR__.'/../../config/runner.yml',
            'runner.yml.dist',
        ]);
    }
}
