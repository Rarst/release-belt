<?php

declare(strict_types=1);

namespace Rarst\ReleaseBelt;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Finds and processes releases in a file system.
 */
class ReleaseParser
{
    /** @var Finder $finder */
    protected $finder;

    /**
     * ReleaseParser constructor.
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Finds release files at the target folder level and loads into release instances.
     */
    public function getReleases(): array
    {
        $releases = [];

        $this->finder->depth('== 2')->name('*.zip');

        foreach ($this->finder as $file) {
            $releases[] = new Release($file);
        }

        return $releases;
    }
}
