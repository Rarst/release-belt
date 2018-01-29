<?php

namespace Rarst\ReleaseBelt\Tests\Model;

use PHPUnit\Framework\TestCase;
use Rarst\ReleaseBelt\Model\FileModel;
use Symfony\Component\Finder\Finder;

class FileModelTest extends TestCase
{
    public function testGetFile()
    {
        $vendor  = 'vendor';
        $package = 'package-1.0.zip';

        $finderMock = $this->getMockBuilder(Finder::class)
            ->disableOriginalConstructor()
            ->getMock();

        $finderMock->expects($this->once())
            ->method('path')
            ->with($vendor)
            ->willReturn($finderMock);

        $finderMock->expects($this->once())
            ->method('name')
            ->with($package)
            ->willReturn($finderMock);

        $iteratorMock = $this->getMockBuilder(\Iterator::class)
            ->getMock();

        $iteratorMock->expects($this->once())
            ->method('valid')
            ->willReturn('true');

        $iteratorMock->expects($this->once())
            ->method('current')
            ->willReturn("{$vendor}/{$package}");

        $finderMock->expects($this->once())
            ->method('getIterator')
            ->willReturn($iteratorMock);

        $fileModel = new FileModel($finderMock);

        $file = $fileModel->getFile($vendor, $package);

        $this->assertEquals("{$vendor}/{$package}", $file);
    }
}
