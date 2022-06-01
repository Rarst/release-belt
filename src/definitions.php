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
use Slim\Views\Mustache;
use Symfony\Component\Finder\Finder;
use function DI\autowire;
use function DI\create;
use function DI\get;

return [
    Collection::class            => function (ReleaseParser $parser, ReleaseTransformer $transformer) {
        return new Collection($parser->getReleases(), $transformer);
    },
    'data'                       => function (Fractal $fractal, Collection $collection) {
        return $fractal->createData($collection)->toArray();
    },
    'debug'                      => false,
    Finder::class                => function (ContainerInterface $container) {
        $finder = new Finder();
        $finder->files()->in($container->get('release.dir'));

        return $finder;
    },
    Fractal::class               => create()->method('setSerializer', get(PackageSerializer::class)),
    /** @deprecated 0.3:1.0 Deprecated in favor of `users`. */
    'http.users'                 => [],
    JsonController::class        => create()->constructor(get('data'), get('debug')),
    IndexModel::class            => autowire()
        ->constructorParameter('packages', get('packages'))
        ->constructorParameter('username', get('username')),
    Mustache::class              => function () {
        return new Mustache([
            'loader' => new Mustache_Loader_FilesystemLoader(__DIR__.'/mustache'),
        ]);
    },
    'packages'                   => function (ContainerInterface $container): array {
        return $container->get('data')['packages'];
    },
    'release.dir'                => __DIR__.'/../releases',
    ReleaseTransformer::class    => function (UrlGeneratorInterface $urlGenerator) {
        return new ReleaseTransformer($urlGenerator, array_merge(
            require __DIR__.'/../config/installerTypes.php',
            require __DIR__.'/../config/installerTypesV2.php'
        ));
    },
    RouteParserInterface::class  => function (App $app) {
        return $app->getRouteCollector()->getRouteParser();
    },
    UrlGeneratorInterface::class => autowire(UrlGenerator::class)
        ->constructorParameter('uri', get('request.uri')),
    'username'                   => function (): string {
        return (string)($_SERVER['PHP_AUTH_USER'] ?? '');
    },
    'users'                      => [],
];
