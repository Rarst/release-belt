<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Rarst\ReleaseBelt\Model\FileModel;
use Rarst\ReleaseBelt\Model\IndexModel;

/**
 * Implements data–handling models.
 */
class ModelProvider implements ServiceProviderInterface
{
    /**
     * Registers model services on the container.
     */
    public function register(Container $app): void
    {
        $app['model.index'] = function () use ($app) {
            return new IndexModel($app['data']['packages'], $app['url_generator'], $app['username']);
        };

        $app['model.file'] = function () use ($app) {
            return new FileModel($app['finder']);
        };
    }
}
