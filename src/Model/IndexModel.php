<?php

namespace Rarst\ReleaseBelt\Model;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class IndexModel
{
    protected $packages;

    protected $request;

    protected $urlGenerator;

    public function __construct(array $packages, Request $request, UrlGeneratorInterface $urlGenerator)
    {
        $this->packages     = $packages;
        $this->request      = $request;
        $this->urlGenerator = $urlGenerator;
    }

    public function getContext()
    {
        return [
            'host'              => $this->request->getHost(),
            'schemeAndHttpHost' => $this->request->getSchemeAndHttpHost(),
            'user'              => $this->request->getUser(),
            'packages'          => $this->getPackages(),
            'jsonUrl'           => $this->urlGenerator->generate('json'),
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
