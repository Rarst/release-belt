<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Provider;

use Mustache_Loader_FilesystemLoader;
use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Rarst\ReleaseBelt\ReleaseParser;
use Rarst\ReleaseBelt\UrlGenerator;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Views\Mustache;
use Symfony\Component\Finder\Finder;

/**
 * Provides default services.
 */
class DefaultsProvider implements ServiceProviderInterface
{
    /**
     * Performs service registrations.
     */
    public function register(Container $container): void
    {
        $container['debug'] = false;
        /** @deprecated 0.3:1.0 Deprecated in favor of `users`. */
        $container['http.users']    = [];
        $container['users']         = [];
        $container['release.dir']   = __DIR__.'/../../releases';
        $container['finder']        = function () use ($container) {
            $finder = new Finder();
            $finder->files()->in($container['release.dir']);

            return $finder;
        };
        $container['parser']        = function () use ($container) {
            return new ReleaseParser($container['finder']);
        };
        $container['url_generator'] = function () use ($container) {
            /** @var Request $request */
            $request = $container['request'];

            return new UrlGenerator($container['router'], $request->getUri());
        };
        $container['view']          = function () {
            $view = new Mustache([
                'loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/../mustache'),
            ]);

            return $view;
        };
    }
}
