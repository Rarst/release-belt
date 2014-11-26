<?php
namespace Rarst\ReleaseBelt;

use Symfony\Component\Finder\SplFileInfo;

class Release
{
    const VERSION_REGEX = '|((?:\d+\.*){1,3})|';

    /** @var SplFileInfo $file */
    protected $file;

    public $path;
    public $filename;
    public $type;
    public $vendor;
    public $package;
    public $version;

    public function __construct(SplFileInfo $file)
    {
        $this->file = $file;

        $this->path     = str_replace('\\', '/', $file->getRelativePath());
        $this->filename = $file->getFilename();

        list( $this->type, $this->vendor ) = explode('/', $this->path);
        list( $package, $version ) = preg_split(self::VERSION_REGEX, $this->filename, 2, PREG_SPLIT_DELIM_CAPTURE);

        $this->package = trim($package, '-.');
        $this->version = trim($version, '-.');
    }
}
