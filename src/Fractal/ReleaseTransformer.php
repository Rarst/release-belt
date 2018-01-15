<?php
namespace Rarst\ReleaseBelt\Fractal;

use League\Fractal\TransformerAbstract;
use Rarst\ReleaseBelt\Release;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ReleaseTransformer extends TransformerAbstract
{

    /** @var UrlGeneratorInterface $urlGenerator */
    protected $urlGenerator;

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

        if ('library' != $release->type) {
            $package['type'] = $release->type;
        }

        if (in_array($release->type, [ 'wordpress-plugin', 'wordpress-theme', 'wordpress-muplugin' ])) {
            $package['require'] = [
                'composer/installers' => '~1.0'
            ];
        }

        return $package;
    }

    /**
     * Set UrlGenerator to use for file links in output.
     *
     * @param UrlGeneratorInterface $urlGenerator
     */
    public function setUrlGenerator(UrlGeneratorInterface $urlGenerator)
    {
        $this->urlGenerator = $urlGenerator;
    }
}
