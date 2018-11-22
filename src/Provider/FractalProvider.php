<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Provider;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Rarst\ReleaseBelt\Fractal\PackageSerializer;
use Rarst\ReleaseBelt\Fractal\ReleaseTransformer;
use Rarst\ReleaseBelt\ReleaseParser;

/**
 * Implements processing and serializing of release data with Fractal.
 */
class FractalProvider implements ServiceProviderInterface
{
    /**
     * Registers services on the container.
     */
    public function register(Container $app): void
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

        $app['collection'] = function () use ($app) {
            /** @var ReleaseParser $parser */
            $parser   = $app['parser'];
            $releases = $parser->getReleases();

            return new Collection($releases, $app['transformer']);
        };

        $app['data'] = function () use ($app) {

            /** @var Manager $fractal */
            $fractal = $app['fractal'];

            return $fractal->createData($app['collection'])->toArray();
        };
    }
}
