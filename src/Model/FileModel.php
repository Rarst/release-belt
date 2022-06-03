<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Model;

use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

/**
 * Implements file lookup.
 */
class FileModel
{
    /** @var Finder */
    protected $finder;

    /**
     * FileModel constructor.
     */
    public function __construct(Finder $finder)
    {
        $this->finder = $finder;
    }

    /**
     * Returns the file info object for the vendor and file name provided.
     *
     * If the file isn't located the returned object would not have isReadable() status.
     */
    public function getFile(string $vendor, string $file): SplFileInfo
    {
        $iterator = $this->finder->path($vendor)->name($file)->getIterator();
        $iterator->rewind();

        if (! $iterator->valid()) {
            return new SplFileInfo('', '', '');
        }

        /** @var SplFileInfo */
        return $iterator->current();
    }
}
