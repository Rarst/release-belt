<?php
namespace Rarst\ReleaseBelt\Fractal;

use League\Fractal\Serializer\ArraySerializer;

class PackageSerializer extends ArraySerializer
{
    /**
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function collection($resourceKey, array $data)
    {
        $packages = [ ];

        foreach ($data as $package) {
            $packages[$package['name']][$package['version']] = $package;
        }

        return [ 'packages' => $packages ];
    }
}
