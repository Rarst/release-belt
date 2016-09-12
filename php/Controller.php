<?php
namespace Rarst\ReleaseBelt;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Scope;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\Request;

class Controller
{
    public function getHtml(Application $app)
    {
        $array = $this->getData($app);
        /** @var Request $request */
        $request              = $app['request_stack']->getCurrentRequest();
        $boilerplate          = new \stdClass();
        $boilerplate->require = [];
        $packages             = [];

        foreach ($array['packages'] as $name => $versions) {

            uksort($versions, 'version_compare');
            end($versions);
            $boilerplate->require[$name] = '^' . key($versions);

            $packages = array_merge($packages, array_values($versions));
        }

        $boilerplate->repositories = [
            (object)([
                'type' => 'composer',
                'url'  => $request->getSchemeAndHttpHost(),
            ])
        ];

        return $app->render('index', [
            'host'        => $request->getHost(),
            'boilerplate' => json_encode($boilerplate, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'packages'    => $packages,
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

        $finder->path($vendor)->name($file);

        /** @var SplFileInfo $file */
        foreach ($finder as $file) {
            return $app->sendFile($file->getRealPath());
        }

        return null;
    }

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
