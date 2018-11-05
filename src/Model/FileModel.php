<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Model;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class FileModel
{
    protected $finder;

    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Returns the file info object for the vendor and file name provided.
     *
     * If the file isn't located the returned object would not have isReadable() status.
     *
     * @param string $vendor
     * @param string $file
     *
     * @return SplFileInfo
     */
    public function getFile(string $vendor, string $file): SplFileInfo
    {
        $iterator = $this->finder->path($vendor)->name($file)->getIterator();
        $iterator->rewind();

        if (! $iterator->valid()) {
            return new SplFileInfo('', '', '');
        }

        return $iterator->current();
    }
}
