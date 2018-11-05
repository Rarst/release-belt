<?php

namespace Rarst\ReleaseBelt\Provider;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Rarst\ReleaseBelt\Model\FileModel;
use Rarst\ReleaseBelt\Model\IndexModel;

class ModelProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['model.index'] = function () use ($app) {
            return new IndexModel(
                $app['data']['packages'],
                $app['request'],
                $app['url_generator']
            );
        };

        $app['model.file'] = function () use ($app) {
            return new FileModel($app['finder']);
        };
    }
}
