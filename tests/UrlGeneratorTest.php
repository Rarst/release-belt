<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Tests;

use Psr\Http\Message\UriInterface;
use Rarst\ReleaseBelt\UrlGenerator;
use PHPUnit\Framework\TestCase;
use Slim\Interfaces\RouterInterface;

class UrlGeneratorTest extends TestCase
{
    public function testGetFileUrl(): void
    {
        $routerMock = $this->getMockBuilder(RouterInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $routerMock->expects($this->once())
            ->method('pathFor')
            ->with('file', ['vendor' => 'vendor', 'file' => 'file.zip'])
            ->willReturn('/vendor/file.zip');

        $urlMock = $this->getMockBuilder(UriInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $urlMock->expects($this->once())
            ->method('withPath')
            ->with('/vendor/file.zip')
            ->willReturn('https://example.com/vendor/file.zip');

        $urlGenerator = new UrlGenerator($routerMock, $urlMock);

        $this->assertSame('https://example.com/vendor/file.zip', $urlGenerator->getFileUrl('vendor', 'file.zip'));
    }
}
