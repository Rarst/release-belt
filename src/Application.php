<?php

namespace Rarst\ReleaseBelt;

use Mustache_Loader_FilesystemLoader;
use Rarst\ReleaseBelt\Provider\AuthenticationProvider;
use Rarst\ReleaseBelt\Provider\DownloadsLogProvider;
use Rarst\ReleaseBelt\Provider\FractalProvider;
use Rarst\ReleaseBelt\Provider\ModelProvider;
use Slim\App;
use Slim\Container;
use Slim\Views\Mustache;
use Symfony\Component\Finder\Finder;

class Application extends App
{
    use DownloadsLogTrait;

    /**
     * Main application constructor.
     *
     * Belt is hardwired to use Pimple since Silex times, so overriding Slim’s container object is not supported.
     *
     * @param array $values
     */
    public function __construct(array $values = [])
    {
        $settings = [];

        if (isset($values['settings'])) { // These are handled/used early by Slim’s internal registrations.
            $settings = $values['settings'];
            unset($values['settings']);
        }

        parent::__construct(['settings' => $settings]);
        /** @var Container $container */
        $container = $this->getContainer();

        /** @deprecated 0.3:1.0 Deprecated in favor of `users`. */
        $container['http.users']  = [];
        $container['users']       = [];
        $container['release.dir'] = __DIR__.'/../releases';
        $container['finder']      = function () use ($container) {
            $finder = new Finder();
            $finder->files()->in($container['release.dir']);

            return $finder;
        };
        $container['parser']      = function () use ($container) {
            return new ReleaseParser($container['finder']);
        };
        $container['url_generator'] = function () use ($container) {
            return new UrlGenerator($container->router);
        };
        $container['view'] = function () {
            $view = new Mustache([
                'loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/mustache'),
            ]);

            return $view;
        };

        $container->register(new ModelProvider());
        $container->register(new FractalProvider());
        $container->register(new DownloadsLogProvider());
//        $app->register(new AuthenticationProvider());
//        $app->register(new MonologServiceProvider());
//        $app->register(new SecurityServiceProvider());

        $this->get('/', 'Rarst\\ReleaseBelt\\Controller:getHtml');

        $this->get('/packages.json', 'Rarst\\ReleaseBelt\\Controller:getJson')
            ->setName('json');

        $this->get('/{vendor}/{file}', 'Rarst\\ReleaseBelt\\Controller:getFile')
            ->setName('file');

        foreach ($values as $key => $value) {
            $container[$key] = $value;
        }
    }
}
