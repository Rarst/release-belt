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
    public function register(Container $container): void
    {
        $container['fractal'] = function () {
            $fractal = new Manager();
            $fractal->setSerializer(new PackageSerializer());

            return $fractal;
        };

        $container['transformer'] = function () use ($container) {
            $transformer = new ReleaseTransformer(
                $container['url_generator'],
                require __DIR__.'/../../config/installerTypes.php'
            );

            return $transformer;
        };

        $container['collection'] = function () use ($container) {
            /** @var ReleaseParser $parser */
            $parser   = $container['parser'];
            $releases = $parser->getReleases();

            return new Collection($releases, $container['transformer']);
        };

        $container['data'] = function () use ($container) {

            /** @var Manager $fractal */
            $fractal = $container['fractal'];

            return $fractal->createData($container['collection'])->toArray();
        };
    }
}
