<?php
namespace Rarst\ReleaseBelt\Fractal;

use League\Fractal\TransformerAbstract;
use Rarst\ReleaseBelt\UrlGeneratorInterface;
use Rarst\ReleaseBelt\Release;

class ReleaseTransformer extends TransformerAbstract
{
    /** @var UrlGeneratorInterface $urlGenerator */
    protected $urlGenerator;

    /** @var array $installerTypes */
    protected $installerTypes;

    public function __construct(UrlGeneratorInterface $urlGenerator, array $installerTypes = [])
    {
        $this->urlGenerator   = $urlGenerator;
        $this->installerTypes = $installerTypes;
    }

    /**
     * @param Release $release
     *
     * @return array
     */
    public function transform(Release $release)
    {
        $package = [
            'name'    => $release->vendor . '/' . $release->package,
            'version' => $release->version,
            'dist'    => [
                'url'  => $this->urlGenerator->getFileUrl($release->vendor, $release->filename),
                'type' => 'zip',
            ],
        ];

        if ('library' !== $release->type) {
            $package['type'] = $release->type;
        }

        if (in_array($release->type, $this->installerTypes, true)) {
            $package['require'] = [
                'composer/installers' => '^1.5',
            ];
        }

        return $package;
    }
}
