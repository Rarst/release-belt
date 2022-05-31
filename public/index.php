<?php
declare(strict_types=1);

use Pimple\Container;
use Pimple\Psr11\Container as PsrContainer;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Server\RequestHandlerInterface;
use Rarst\ReleaseBelt\Provider\AuthenticationProvider;
use Rarst\ReleaseBelt\Provider\ControllerProvider;
use Rarst\ReleaseBelt\Provider\DefaultsProvider;
use Rarst\ReleaseBelt\Provider\DownloadsLogProvider;
use Rarst\ReleaseBelt\Provider\FractalProvider;
use Rarst\ReleaseBelt\Provider\ModelProvider;
use Rarst\ReleaseBelt\UrlGenerator;
use RKA\Middleware\IpAddress;
use Slim\Factory\AppFactory;

require __DIR__.'/../vendor/autoload.php';

$configPath = __DIR__.'/../config/config.php';
$config     = file_exists($configPath) ? require $configPath : [];
$pimple     = new Container();

$pimple->register(new DefaultsProvider());
$pimple->register(new ModelProvider());
$pimple->register(new ControllerProvider());
$pimple->register(new FractalProvider());
$pimple->register(new DownloadsLogProvider());

foreach ($config as $key => $value) {
    $pimple[$key] = $value;
}

AppFactory::setContainer(new PsrContainer($pimple));

$app = AppFactory::create();

$pimple['username'] = function (): string {
    return (string)($_SERVER['PHP_AUTH_USER'] ?? '');
};

$pimple['url_generator'] = function () use ($pimple, $app) {
    return new UrlGenerator($app->getRouteCollector()->getRouteParser(), $pimple['request.uri']);
};

$app->get('/', 'controller.index')->setName('index');
$app->get('/packages.json', 'controller.json')->setName('json');
$app->get('/{vendor}/{file}', 'controller.file')->setName('file');

$app->add(function (ServerRequestInterface $request, RequestHandlerInterface $handler) use ($pimple) {
    $pimple['request.uri'] = $request->getUri();

    return $handler->handle($request);
});

$app->add(new IpAddress());

$authentication = new AuthenticationProvider();
$authentication->boot($app);

$app->addErrorMiddleware($pimple['debug'] ?? false, true, true);

$app->run();
