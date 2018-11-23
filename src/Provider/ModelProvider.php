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
    public function register(Container $container): void
    {
        $container['model.index'] = function () use ($container) {
            return new IndexModel($container['data']['packages'], $container['url_generator'], $container['username']);
        };

        $container['model.file'] = function () use ($container) {
            return new FileModel($container['finder']);
        };
    }
}
