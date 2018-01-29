<?php

namespace Rarst\ReleaseBelt\Tests;

use PHPUnit\Framework\TestCase;
use Rarst\ReleaseBelt\Release;
use Rarst\ReleaseBelt\ReleaseParser;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class ReleaseParserTest extends TestCase
{
    public function testGetReleases()
    {
        $finderMock = $this->getMockBuilder(Finder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $finderMock->expects($this->once())
            ->method('depth')
            ->with('== 2')
            ->willReturn($finderMock);

        $finderMock->expects($this->once())
            ->method('name')
            ->with('*.zip');

        $splFileInfoStub = $this->getMockBuilder(SplFileInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $splFileInfoStub->method('getRelativePath')
            ->willReturn('type/vendor');

        $splFileInfoStub->method('getFilename')
            ->willReturn('package-1.0.zip');

        $iterator = new \ArrayIterator([
            $splFileInfoStub,
            $splFileInfoStub,
        ]);

        $finderMock->expects($this->once())
            ->method('getIterator')
            ->willReturn($iterator);

        $releaseParser = new ReleaseParser($finderMock);

        $releases = $releaseParser->getReleases();

        $this->assertCount(2, $releases);
        $this->assertInstanceOf(Release::class, $releases[0]);
    }
}
