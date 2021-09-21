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
    public function register(Container $pimple): void
    {
        $pimple['fractal'] = function () {
            $fractal = new Manager();
            $fractal->setSerializer(new PackageSerializer());

            return $fractal;
        };

        $pimple['transformer'] = function () use ($pimple) {
            $transformer = new ReleaseTransformer(
                $pimple['url_generator'],
                array_merge(
                    require __DIR__.'/../../config/installerTypes.php',
                    require __DIR__.'/../../config/installerTypesV2.php'
                )
            );

            return $transformer;
        };

        $pimple['collection'] = function () use ($pimple) {
            /** @var ReleaseParser $parser */
            $parser   = $pimple['parser'];
            $releases = $parser->getReleases();

            return new Collection($releases, $pimple['transformer']);
        };

        $pimple['data'] = function () use ($pimple) {

            /** @var Manager $fractal */
            $fractal = $pimple['fractal'];

            return $fractal->createData($pimple['collection'])->toArray();
        };
    }
}
