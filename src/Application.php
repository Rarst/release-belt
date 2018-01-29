<?php

namespace Rarst\ReleaseBelt;

use Mustache\Silex\Application\MustacheTrait;
use Mustache\Silex\Provider\MustacheServiceProvider;
use Rarst\ReleaseBelt\Provider\AuthenticationProvider;
use Rarst\ReleaseBelt\Provider\DownloadsLogProvider;
use Rarst\ReleaseBelt\Provider\FractalProvider;
use Rarst\ReleaseBelt\Provider\ModelProvider;
use Silex\Provider\MonologServiceProvider;
use Silex\Application\MonologTrait;
use Silex\Provider\SecurityServiceProvider;
use Symfony\Component\Finder\Finder;

class Application extends \Silex\Application
{
    use MustacheTrait, MonologTrait, DownloadsLogTrait;

    public function __construct(array $values = [])
    {
        parent::__construct();

        $app = $this;

        /** @deprecated 0.3:1.0 Deprecated in favor of `users`. */
        $app['http.users']  = [];
        $app['users']       = [];
        $app['release.dir'] = __DIR__.'/../releases';
        $app['finder']      = function () {
            $finder = new Finder();
            $finder->files()->in($this['release.dir']);

            return $finder;
        };
        $app['parser']      = function () use ($app) {
            return new ReleaseParser($app['finder']);
        };

        $app->register(new ModelProvider());
        $app->register(new FractalProvider());
        $app->register(new DownloadsLogProvider());
        $app->register(new AuthenticationProvider());

        $app->register(new MustacheServiceProvider, [
            'mustache.path' => __DIR__.'/mustache',
        ]);
        $app->register(new MonologServiceProvider());
        $app->register(new SecurityServiceProvider());

        $app->get('/', 'Rarst\\ReleaseBelt\\Controller::getHtml');

        $app->get('/packages.json', 'Rarst\\ReleaseBelt\\Controller::getJson')
            ->bind('json');

        $app->get('/{vendor}/{file}', 'Rarst\\ReleaseBelt\\Controller::getFile')
            ->bind('file');

        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }
    }
}
