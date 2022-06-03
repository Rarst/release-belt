<?php

declare(strict_types=1);

namespace Rarst\ReleaseBelt\Fractal;

use League\Fractal\Serializer\ArraySerializer;

/**
 * Serializes package data into repository representation.
 */
class PackageSerializer extends ArraySerializer
{
    /**
     * Serializes the data into the needed array structure.
     */
    public function collection(?string $resourceKey, array $data): array
    {
        $packages = [];

        foreach ($data as $package) {
            $packages[$package['name']][$package['version']] = $package;
        }

        return ['packages' => $packages];
    }
}
