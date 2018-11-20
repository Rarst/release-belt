<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Model;

use Psr\Http\Message\UriInterface;
use Rarst\ReleaseBelt\UrlGeneratorInterface;

class IndexModel
{
    protected $packages;

    protected $uri;

    protected $urlGenerator;

    public function __construct(array $packages, UriInterface $uri, UrlGeneratorInterface $urlGenerator)
    {
        $this->packages     = $packages;
        $this->uri          = $uri;
        $this->urlGenerator = $urlGenerator;
    }

    public function getContext() : array
    {
        [$user] = explode(':', $this->uri->getUserInfo());

        return [
            'host'              => $this->uri->getHost(),
            'schemeAndHttpHost' => $this->urlGenerator->getUrl('index'),
            'user'              => $user,
            'packages'          => $this->getPackages(),
            'jsonUrl'           => $this->urlGenerator->getUrl('json'),
        ];
    }

    protected function getPackages(): array
    {
        return array_map([$this, 'transformPackage'], array_keys($this->packages), $this->packages);
    }

    protected function transformPackage($name, $versions): array
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
