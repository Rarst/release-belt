<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Rarst\ReleaseBelt\Controller\FileController;
use Rarst\ReleaseBelt\Controller\IndexController;
use Rarst\ReleaseBelt\Controller\JsonController;

/**
 * Provides controller services to handle routes.
 */
class ControllerProvider implements ServiceProviderInterface
{
    /**
     * Performs service registrations.
     */
    public function register(Container $container): void
    {
        $container['controller.index'] = function () use ($container) {
            return new IndexController($container['view'], $container['model.index']);
        };

        $container['controller.json'] = function () use ($container) {
            return new JsonController($container['data']);
        };

        $container['controller.file'] = function () use ($container) {
            return new FileController($container['model.file'], $container['downloads.log']);
        };
    }
}
