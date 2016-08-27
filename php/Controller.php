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
        $list                 = '';

        foreach ($array['packages'] as $name => $versions) {

            uksort($versions, 'version_compare');
            end($versions);
            $version                     = key($versions);
            $boilerplate->require[$name] = "^{$version}";

            foreach ($versions as $version => $data) {
                if (empty($data['type'])) {
                    $data['type'] = '';
                }
                $list .= "<tr>
    <td>{$name}</td>
    <td>{$version}</td>
    <td>{$data['type']}</td>
    <td><a href='{$data['dist']['url']}'>{$data['dist']['url']}</a></td>
</tr>\n";
            }
        }

        $boilerplate->repositories = [
            (object)([
                'type' => 'composer',
                'url'  => $request->getSchemeAndHttpHost(),
            ])
        ];

        return '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" rel="stylesheet">
<body>
<div class="container">
    <div class="page-header">
        <h1>Release Belt <small>' . $request->getHost() . '</small></h1>
    </div>
    
    <h2>Boilerplate</h2>
    <div class="row">
        <div class="col-md-6">
            <pre>' . json_encode($boilerplate, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) . '</pre>
        </div>
    </div>
    
    <h2>Packages</h2>
    
    <table class="table">
    <thead>
        <tr>
            <th>Package</th>
            <th>Version</th>
            <th>Type</th>
            <th>URL</th>
        </tr>
    </thead>
       <tbody>' . $list . '</tbody>
    </table>
    
    <hr />
    <p class="text-center">
        Created with <a href="https://github.com/Rarst/release-belt">Release Belt</a> — Composer repo for ZIPs.
    </p>
</div>
</body>
</html>';
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
