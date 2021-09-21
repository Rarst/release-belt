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
    public function register(Container $pimple): void
    {
        $pimple['controller.index'] = function () use ($pimple) {
            return new IndexController($pimple['view'], $pimple['model.index']);
        };

        $pimple['controller.json'] = function () use ($pimple) {
            return new JsonController($pimple['data'], $pimple['debug']);
        };

        $pimple['controller.file'] = function () use ($pimple) {
            return new FileController($pimple['model.file'], $pimple['downloads.log']);
        };
    }
}
