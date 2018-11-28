<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt;

use Mustache_Loader_FilesystemLoader;
use Psr\Http\Message\ServerRequestInterface;
use Rarst\ReleaseBelt\Provider\AuthenticationProvider;
use Rarst\ReleaseBelt\Provider\ControllerProvider;
use Rarst\ReleaseBelt\Provider\DownloadsLogProvider;
use Rarst\ReleaseBelt\Provider\FractalProvider;
use Rarst\ReleaseBelt\Provider\ModelProvider;
use RKA\Middleware\IpAddress;
use Slim\App;
use Slim\Container;
use Slim\Http\Environment;
use Slim\Http\Request;
use Slim\Views\Mustache;
use Symfony\Component\Finder\Finder;

/**
 * Main application class and entry point.
 *
 * @phan-suppress-next-line PhanParamSignatureMismatch
 * @method Container getContainer()
 */
class Application extends App
{
    /**
     * Main application constructor.
     *
     * Belt is hardwired to use Pimple since Silex times, so overriding Slim’s container object is not supported.
     *
     * @suppress PhanUndeclaredFunctionInCallable
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

        $container['debug'] = false;
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
        $container['username'] = function () use ($container) {
            /** @var Environment $environment */
            $environment = $container['environment'];
            return $environment->get('PHP_AUTH_USER', '');
        };
        $container['url_generator'] = function () use ($container) {
            /** @var Request $request */
            $request = $container['request'];
            return new UrlGenerator($container['router'], $request->getUri());
        };
        $container['view'] = function () {
            $view = new Mustache([
                'loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/mustache'),
            ]);

            return $view;
        };

        $container->extend('request', function (ServerRequestInterface $request) use ($container) {
            return $request->withAttribute('username', $container['username']);
        });

        $container->register(new ModelProvider());
        $container->register(new ControllerProvider());
        $container->register(new FractalProvider());
        $container->register(new DownloadsLogProvider());

        $this->get('/', 'controller.index')->setName('index');
        $this->get('/packages.json', 'controller.json')->setName('json');
        $this->get('/{vendor}/{file}', 'controller.file')->setName('file');

        $this->add(new IpAddress());

        foreach ($values as $key => $value) {
            $container[$key] = $value;
        }

        // This is leftover from Silex for now, since Slim doesn’t have bootable service providers.
        $authentication = new AuthenticationProvider();
        $authentication->boot($this);
    }
}
