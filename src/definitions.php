<?php

declare(strict_types=1);

use League\Fractal\Manager as Fractal;
use League\Fractal\Resource\Collection;
use Psr\Container\ContainerInterface;
use Rarst\ReleaseBelt\Controller\JsonController;
use Rarst\ReleaseBelt\Fractal\PackageSerializer;
use Rarst\ReleaseBelt\Fractal\ReleaseTransformer;
use Rarst\ReleaseBelt\Model\IndexModel;
use Rarst\ReleaseBelt\ReleaseParser;
use Rarst\ReleaseBelt\UrlGenerator;
use Rarst\ReleaseBelt\UrlGeneratorInterface;
use Slim\App;
use Slim\Interfaces\RouteParserInterface;
use Slim\Psr7\Factory\UriFactory;
use Slim\Views\Mustache;
use Symfony\Component\Finder\Finder;

use function DI\autowire;
use function DI\create;
use function DI\get;

return [
    Collection::class            => create()->constructor(get('releases'), get(ReleaseTransformer::class)),
    'data'                       => function (Fractal $fractal, Collection $collection) {
        return $fractal->createData($collection)->toArray();
    },
    'debug'                      => false,
    Finder::class                => function (ContainerInterface $container) {
        return (new Finder())->files()->in($container->get('release.dir'));
    },
    Fractal::class               => create()->method('setSerializer', get(PackageSerializer::class)),
    /** @deprecated 0.3:1.0 Deprecated in favor of `users`. */
    'http.users'                 => [],
    IndexModel::class            => autowire()
        ->constructorParameter('packages', get('packages'))
        ->constructorParameter('username', get('username')),
    'installerTypes'             => fn() => array_merge(
        require __DIR__ . '/../config/installerTypes.php',
        require __DIR__ . '/../config/installerTypesV2.php'
    ),
    JsonController::class        => create()->constructor(get('data'), get('debug')),
    Mustache::class              => fn() => new Mustache([
        'loader' => new Mustache_Loader_FilesystemLoader(__DIR__ . '/mustache'),
    ]),
    'packages'                   => fn(ContainerInterface $container): array => $container->get('data')['packages'],
    'release.dir'                => __DIR__ . '/../releases',
    'releases'                   => fn(ReleaseParser $parser) => $parser->getReleases(),
    ReleaseTransformer::class    => autowire()->constructorParameter('installerTypes', get('installerTypes')),
    RouteParserInterface::class  => fn(App $app) => $app->getRouteCollector()->getRouteParser(),
    UrlGeneratorInterface::class => function (RouteParserInterface $routeParser, UriFactory $uriFactory) {
        /** @psalm-suppress InternalMethod */
        $requestUri = $uriFactory->createFromGlobals($_SERVER);

        return new UrlGenerator($routeParser, $requestUri);
    },
    'username'                   => fn(): string => (string)($_SERVER['PHP_AUTH_USER'] ?? ''),
    'users'                      => [],
];
