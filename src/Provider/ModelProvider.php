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
    public function register(Container $pimple): void
    {
        $pimple['model.index'] = function () use ($pimple) {
            return new IndexModel($pimple['data']['packages'], $pimple['url_generator'], $pimple['username']);
        };

        $pimple['model.file'] = function () use ($pimple) {
            return new FileModel($pimple['finder']);
        };
    }
}
