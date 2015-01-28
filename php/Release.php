<?php
namespace Rarst\ReleaseBelt;

use Symfony\Component\Finder\SplFileInfo;

class Release
{
    const SEPARATORS = '.-_';

    const VERSION_REGEX = '|(?P<package>.*?)(?P<version>(?:\d+\.*){1,4})\.zip|';

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
        preg_match(self::VERSION_REGEX, $this->filename, $matches);
        $package       = rtrim($matches['package'], self::SEPARATORS);
        $version       = $matches['version'];
        $this->package = trim($package);
        $this->version = trim($version);
    }
}
