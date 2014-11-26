<?php
namespace Rarst\ReleaseBelt;

use League\Fractal\Manager;
use League\Fractal\Resource\Collection;
use League\Fractal\Scope;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class Controller
{
    public function getHtml(Application $app)
    {
        $array = $this->getData($app);
        $json  = json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);

        return '<html><body><h1>// TODO human friendly index</h1><pre>' . $json . '</pre></body></html>';
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
