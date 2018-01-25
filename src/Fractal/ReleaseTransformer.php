<?php
namespace Rarst\ReleaseBelt\Fractal;

use League\Fractal\TransformerAbstract;
use Rarst\ReleaseBelt\Release;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

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
                'url'  => $this->urlGenerator->generate(
                    'file',
                    [ 'vendor' => $release->vendor, 'file' => $release->filename ],
                    UrlGeneratorInterface::ABSOLUTE_URL
                ),
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

    /**
     * Set UrlGenerator to use for file links in output.
     *
     * @deprecated 0.3:1.0 Deprecated in favor of constructor.
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
}
