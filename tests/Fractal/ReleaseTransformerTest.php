<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Tests\Fractal;

use PHPUnit\Framework\TestCase;
use Rarst\ReleaseBelt\Fractal\ReleaseTransformer;
use Rarst\ReleaseBelt\Release;
use Rarst\ReleaseBelt\UrlGenerator;

class ReleaseTransformerTest extends TestCase
{
    public function testTransform(): void
    {
        $vendor   = 'vendor';
        $package  = 'package';
        $type     = 'wordpress-plugin';
        $version  = '1.0';
        $filename = "{$package}-{$version}.zip";
        $url      = "https://example.com/{$vendor}/{$filename}";

        $urlGeneratorMock = $this->getMockBuilder(UrlGenerator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $urlGeneratorMock->expects($this->once())
            ->method('getFileUrl')
            ->with($vendor, $filename)
            ->willReturn($url);

        $releaseMock = $this->getMockBuilder(Release::class)
            ->disableOriginalConstructor()
            ->getMock();

        $releaseMock->vendor   = $vendor;
        $releaseMock->package  = $package;
        $releaseMock->type     = $type;
        $releaseMock->version  = $version;
        $releaseMock->filename = $filename;

        $releaseTransformer = new ReleaseTransformer($urlGeneratorMock, [$type]);

        $transformResult = $releaseTransformer->transform($releaseMock);

        $this->assertArraySubset([
            'name'    => "{$vendor}/{$package}",
            'version' => $version,
            'dist'    => [
                'url'  => $url,
                'type' => 'zip',
            ],
            'type'    => $type,
            'require' => [
                'composer/installers' => '^1.5 || ^2.0',
            ],
        ], $transformResult);
    }
}
