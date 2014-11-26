<?php
namespace Rarst\ReleaseBelt;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ReleaseParser
{
    /** @var Finder $finder */
    protected $finder;

    /**
     * @param Finder $finder
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @return array
     */
    public function getReleases()
    {
        $releases = [ ];

        $this->finder->depth('== 2')->name('*.zip');

        /** @var SplFileInfo $file */
        foreach ($this->finder as $file) {

            $releases[] = new Release($file);
        }

        return $releases;
    }
}
