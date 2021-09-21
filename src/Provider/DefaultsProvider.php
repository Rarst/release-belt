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
    public function register(Container $pimple): void
    {
        $pimple['debug'] = false;
        /** @deprecated 0.3:1.0 Deprecated in favor of `users`. */
        $pimple['http.users']    = [];
        $pimple['users']         = [];
        $pimple['release.dir']   = __DIR__.'/../../releases';
        $pimple['finder']        = function () use ($pimple) {
            $finder = new Finder();
            $finder->files()->in($pimple['release.dir']);

            return $finder;
        };
        $pimple['parser']        = function () use ($pimple) {
            return new ReleaseParser($pimple['finder']);
        };
        $pimple['url_generator'] = function () use ($pimple) {
            /** @var Request $request */
            $request = $pimple['request'];

            return new UrlGenerator($pimple['router'], $request->getUri());
        };
        $pimple['view']          = function () {
            $view = new Mustache([
                'loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/../mustache'),
            ]);

            return $view;
        };
    }
}
