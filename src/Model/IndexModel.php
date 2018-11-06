<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Model;

use Psr\Http\Message\ServerRequestInterface;
use Rarst\ReleaseBelt\UrlGeneratorInterface;

class IndexModel
{
    protected $packages;

    protected $request;

    protected $urlGenerator;

    public function __construct(array $packages, ServerRequestInterface $request, UrlGeneratorInterface $urlGenerator)
    {
        $this->packages     = $packages;
        $this->request      = $request;
        $this->urlGenerator = $urlGenerator;
    }

    public function getContext() : array
    {
        $uri = $this->request->getUri();
        [$user] = explode(':', $uri->getUserInfo());

        return [
            'host'              => $uri->getHost(),
            'schemeAndHttpHost' => $uri->withUserInfo(''),
            'user'              => $user,
            'packages'          => $this->getPackages(),
            'jsonUrl'           => $this->urlGenerator->getUrl('json'),
        ];
    }

    protected function getPackages()
    {
        return array_map([$this, 'transformPackage'], array_keys($this->packages), $this->packages);
    }

    protected function transformPackage($name, $versions)
    {
        uksort($versions, 'version_compare');
        $versions = array_reverse($versions);
        $latest   = current(array_keys($versions));
        $package  = [
            'name'     => $name,
            'latest'   => $latest,
            'versions' => array_values($versions),
        ];

        if (! empty($versions[$latest]['type'])) {
            $package['type'] = $versions[$latest]['type'];
        }

        return $package;
    }
}
