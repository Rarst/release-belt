<?php
namespace Rarst\ReleaseBelt;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Scope;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Controller
{
    public function getHtml(Application $app)
    {
        $array = $this->getData($app);
        /** @var Request $request */
        $request              = $app['request_stack']->getCurrentRequest();
        $composer             = new \stdClass();
        $composer->require    = [];
        $packages             = [];

        foreach ($array['packages'] as $name => $versions) {
            uksort($versions, 'version_compare');
            end($versions);
            $latestVersion            = key($versions);
            $composer->require[$name] = '^'.$latestVersion;

            $packages[$name]['name'] = $name;

            if (! empty($versions[$latestVersion]['type'])) {
                $packages[$name]['type'] = $versions[$latestVersion]['type'];
            }

            $packages[$name]['versions']  = array_values($versions);
        }

        $packages = array_values($packages);

        $host              = $request->getHost();
        $schemeAndHttpHost = $request->getSchemeAndHttpHost();

        $composer->repositories = (object)[
            $host => [
                'type' => 'composer',
                'url'  => $schemeAndHttpHost,
            ],
        ];

        $auth = false;

        if (! empty($app['http.users'])) {
            $auth = [
                'http-basic' => [
                    $request->getHttpHost() => [
                        'username' => $request->getUser(),
                        'password' => 'FILL IN PASSWORD',
                    ],
                ],
            ];
        }

        return $app->render('index', [
            'host'     => $host,
            'composer' => json_encode($composer, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'cli'      => "composer config repo.{$host} composer {$schemeAndHttpHost}",
            'auth'     => $auth ? json_encode($auth, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : false,
            'packages' => $packages,
            'jsonUrl'  => $app['url_generator']->generate('json'),
        ]);
    }

    public function getJson(Application $app)
    {
        $array = $this->getData($app);

        return $app->json($array);
    }

    public function getFile(Application $app, $vendor, $file)
    {
        /** @var Finder $finder */
        $finder = $app['finder'];

        $iterator = $finder->path($vendor)->name($file)->getIterator();
        $iterator->rewind();

        if (! $iterator->valid()) {
            return new Response('Package file not found.', Response::HTTP_NOT_FOUND);
        }

        $sendFile = $iterator->current();
        $app->logDownload($sendFile);

        return $app->sendFile($sendFile->getRealPath());
    }

    /**
     * @param Application $app
     *
     * @return array[]
     */
    protected function getData(Application $app)
    {
        /** @var ReleaseParser $parser */
        $parser   = $app['parser'];
        $releases = $parser->getReleases();
        $resource = new Collection($releases, $app['transformer']);

        /** @var Manager $fractal */
        $fractal = $app['fractal'];

        /** @var Scope $data */
        $data = $fractal->createData($resource);

        return $data->toArray();
    }
}
