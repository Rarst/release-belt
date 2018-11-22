<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt;

/**
 * Declares a way to generate absolute URLs to the app routes.
 */
interface UrlGeneratorInterface
{
    /**
     * Generates the absolute public URL for the given route and data.
     *
     * @param string $name Route name.
     * @param array  $data Replacement data.
     *
     * @return string
     */
    public function getUrl(string $name, array $data = []): string;

    /**
     * Generates absolute public URL to the given file in given vendor space.
     *
     * @param string $vendor Vendor name.
     * @param string $file   File name.
     *
     * @return string Absolute URL to file.
     */
    public function getFileUrl(string $vendor, string $file): string;
}
