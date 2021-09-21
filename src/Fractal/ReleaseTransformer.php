<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Fractal;

use League\Fractal\TransformerAbstract;
use Rarst\ReleaseBelt\UrlGeneratorInterface;
use Rarst\ReleaseBelt\Release;

/**
 * Prepares release data for the repository format.
 */
class ReleaseTransformer extends TransformerAbstract
{
    /** @var UrlGeneratorInterface $urlGenerator */
    protected $urlGenerator;

    /** @var array<string, string[]> $installerTypes */
    protected $installerTypes;

    /**
     * ReleaseTransformer constructor.
     *
     * @param array<string, string[]> $installerTypes
     */
    public function __construct(UrlGeneratorInterface $urlGenerator, array $installerTypes = [])
    {
        $this->urlGenerator   = $urlGenerator;
        $this->installerTypes = $installerTypes;
    }

    /**
     * Formats release data into the repository representation.
     *
     * Adds Composer installers dependency for the recognized package types.
     */
    public function transform(Release $release): array
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

        $installers = [];

        foreach ($this->installerTypes as $version => $types) {
            if (\in_array($release->type, $types, true)) {
                $installers[] = '^'.$version;
            }
        }

        if ( ! empty($installers)) {
            $package['require'] = [
                'composer/installers' => implode(' || ', $installers),
            ];
        }

        return $package;
    }
}
