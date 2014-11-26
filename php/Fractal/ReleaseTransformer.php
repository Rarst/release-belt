<?php
namespace Rarst\ReleaseBelt\Fractal;

use League\Fractal\TransformerAbstract;
use Rarst\ReleaseBelt\Release;

class ReleaseTransformer extends TransformerAbstract
{
    /** @var string $host */
    protected $host;

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
                // TODO https detection
                'url'  => 'http://' . $this->host . '/' . $release->vendor . '/' . $release->filename,
                'type' => 'zip',
            ],
        ];

        if ('library' != $release->type) {
            $package['type'] = $release->type;
        }

        if (in_array($release->type, [ 'wordpress-plugin', 'wordpress-theme', 'wordpress-muplugin' ])) {
            $package['requires'] = [
                'composer/installers' => '~1.0'
            ];
        }

        return $package;
    }

    public function setHost($host)
    {
        $this->host = $host;
    }
}
