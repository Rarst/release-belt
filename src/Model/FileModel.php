<?php

namespace Rarst\ReleaseBelt\Model;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\HttpFoundation\File\Exception\FileNotFoundException;

class FileModel
{
    protected $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * @param string $vendor
     * @param string $file
     *
     * @return SplFileInfo
     */
    public function getFile($vendor, $file)
    {
        $iterator = $this->finder->path($vendor)->name($file)->getIterator();
        $iterator->rewind();

        if (! $iterator->valid()) {
            throw new FileNotFoundException("{$vendor}/{$file}");
        }

        return $iterator->current();
    }
}
