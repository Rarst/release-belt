<?php

namespace Rarst\ReleaseBelt\Provider;

use League\Fractal\Manager;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Rarst\ReleaseBelt\Fractal\PackageSerializer;
use Rarst\ReleaseBelt\Fractal\ReleaseTransformer;

class FractalProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['fractal'] = function () {
            $fractal = new Manager();
            $fractal->setSerializer(new PackageSerializer());

            return $fractal;
        };

        $app['transformer'] = function () use ($app) {
            $transformer = new ReleaseTransformer(
                $app['url_generator'],
                require __DIR__.'/../../config/installerTypes.php'
            );

            return $transformer;
        };
    }
}
