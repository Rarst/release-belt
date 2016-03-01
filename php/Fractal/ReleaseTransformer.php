<?php
namespace Rarst\ReleaseBelt\Fractal;

use League\Fractal\TransformerAbstract;
use Rarst\ReleaseBelt\Release;

class ReleaseTransformer extends TransformerAbstract
{
    /** @var string $host */
    protected $host;

    /** @var string $protocol */
    protected $protocol;

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
                'url'  => $this->protocol  . $this->host . '/' . $release->vendor . '/' . $release->filename,
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

    public function setHost($host)
    {
        $this->host = $host;
    }

    public function setProtocol($scheme,$port)
    {
        if ('http' == $scheme && $port == 80) {
            $this->protocol = 'http://';
        } elseif('https' == $scheme && $port == 443) {
            $this->protocol = 'https://';
        }
    }

}
