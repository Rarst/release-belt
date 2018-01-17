<?php
namespace Rarst\ReleaseBelt;

use League\Fractal\Manager;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;
use Mustache\Silex\Application\MustacheTrait;
use Mustache\Silex\Provider\MustacheServiceProvider;
use Rarst\ReleaseBelt\Fractal\PackageSerializer;
use Rarst\ReleaseBelt\Fractal\ReleaseTransformer;
use Silex\Provider\MonologServiceProvider;
use Silex\Application\MonologTrait;
use Silex\Provider\SecurityServiceProvider;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Request;

class Application extends \Silex\Application
{
    use MustacheTrait, MonologTrait;

    public function __construct(array $values = [ ])
    {
        parent::__construct();

        $app = $this;

        $app->register(new MustacheServiceProvider, [
            'mustache.path'    => __DIR__ . '/mustache',
        ]);

        $app['http.users'] = [];

        $this['release.dir'] = __DIR__ . '/../releases';

        $this['finder'] = function () {

            $finder = new Finder();
            return $finder->files()->in($this['release.dir']);
        };

        $this['parser'] = function () use ($app) {

            return new ReleaseParser($app['finder']);
        };

        $this['fractal'] = function () {
            $fractal = new Manager();
            $fractal->setSerializer(new PackageSerializer());

            return $fractal;
        };

        $this['transformer'] = function () use ($app) {
            $transformer = new ReleaseTransformer();
            $transformer->setUrlGenerator($app['url_generator']);

            return $transformer;
        };

        $app->register(new MonologServiceProvider());

        $this['downloads.log.enabled'] = false;
        $this['downloads.log.path']    = __DIR__.'/../releases/downloads.log';
        $this['downloads.log.format']  =
            "%datetime%\t%context.user%\t%context.ip%\t%context.vendor%\t%context.package%\t%context.version%\n";

        $this['downloads.log'] = function () use ($app) {
            /** @var Logger $log */
            $log       = new $app['monolog.logger.class']('downloads');
            $handler   = new StreamHandler($app['downloads.log.path']);
            $formatter = new LineFormatter($app['downloads.log.format'], DATE_RFC3339);

            $handler->setFormatter($formatter);
            $log->pushHandler($handler);

            return $log;
        };

        $this->get('/', 'Rarst\\ReleaseBelt\\Controller::getHtml');

        $this->get('/packages.json', 'Rarst\\ReleaseBelt\\Controller::getJson');

        $this->get('/{vendor}/{file}', 'Rarst\\ReleaseBelt\\Controller::getFile')
            ->bind('file');

        foreach ($values as $key => $value) {
            $this[$key] = $value;
        }

        if (! empty($app['http.users'])) {
            $users = [];

            foreach ($app['http.users'] as $login => $hash) {
                $users[$login] = ['ROLE_COMPOSER', $hash];
            }

            $app->register(new SecurityServiceProvider(), [
                'security.firewalls' => [
                    'composer' => [
                        'pattern' => '^.*$',
                        'http'    => true,
                        'users'   => $users,
                    ]
                ]
            ]);
        }
    }

    public function logDownload(SplFileInfo $file)
    {
        if (! $this['downloads.log.enabled']) {
            return false;
        }

        /** @var Request $request */
        $request = $this['request_stack']->getCurrentRequest();
        $release = new Release($file);

        $package = "{$release->vendor}/{$release->package}";
        $context = [
            'user'    => $request->getUser() ?: 'anonymous',
            'ip'      => $request->getClientIp(),
            'vendor'  => $release->vendor,
            'package' => $release->package,
            'version' => $release->version,
        ];

        return $this['downloads.log']->info($package, $context);
    }
}
