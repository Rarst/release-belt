<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt;

use Rarst\ReleaseBelt\Provider\AuthenticationProvider;
use Rarst\ReleaseBelt\Provider\ControllerProvider;
use Rarst\ReleaseBelt\Provider\DefaultsProvider;
use Rarst\ReleaseBelt\Provider\DownloadsLogProvider;
use Rarst\ReleaseBelt\Provider\FractalProvider;
use Rarst\ReleaseBelt\Provider\ModelProvider;
use RKA\Middleware\IpAddress;
use Slim\App;
use Slim\Container;

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
        parent::__construct(['settings' => $values['settings'] ?? []]);
        unset($values['settings']);

        /** @var Container $container */
        $container = $this->getContainer();

        $container->register(new DefaultsProvider());
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
