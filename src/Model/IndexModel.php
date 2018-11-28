<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Model;

use Rarst\ReleaseBelt\UrlGeneratorInterface;

/**
 * Provides data context for the index page.
 */
class IndexModel
{
    /** @var array[] */
    protected $packages;

    /** @var UrlGeneratorInterface */
    protected $urlGenerator;

    /** @var string */
    protected $username;

    /**
     * IndexModel constructor.
     *
     * @param array[] $packages
     */
    public function __construct(array $packages, UrlGeneratorInterface $urlGenerator, string $username = '')
    {
        $this->packages     = $packages;
        $this->urlGenerator = $urlGenerator;
        $this->username     = $username;
    }

    /**
     * Builds context to be passed into template for render.
     */
    public function getContext(): array
    {
        $url = $this->urlGenerator->getUrl('index');

        return [
            'host'              => parse_url($url, PHP_URL_HOST),
            'schemeAndHttpHost' => $url,
            'user'              => $this->username,
            'packages'          => $this->getPackages(),
            'jsonUrl'           => $this->urlGenerator->getUrl('json'),
        ];
    }

    /**
     * Processes a set of packages data for display.
     *
     * @suppress PhanPartialTypeMismatchArgument
     */
    protected function getPackages(): array
    {
        return array_map([$this, 'transformPackage'], array_keys($this->packages), $this->packages);
    }

    /**
     * Prepares the data of an individual package for display.
     */
    protected function transformPackage(string $name, array $versions): array
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
