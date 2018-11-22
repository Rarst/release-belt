<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Tests\Model;

use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UriInterface;
use Rarst\ReleaseBelt\Model\IndexModel;
use Rarst\ReleaseBelt\UrlGenerator;

class IndexModelTest extends TestCase
{
    public function testGetContext(): void
    {
        $packages = [
            'package1' => [
                '1.0' => [],
                '2.0' => [
                    'type' => 'wordpress-plugin',
                ],
            ],
            'package2' => [
                '1.1' => [
                    'foo' => 'bar',
                ],
            ],
        ];

        $uriDummy = $this->getMockBuilder(UriInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $urlGeneratorDummy = $this->getMockBuilder(UrlGenerator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $indexModel = new IndexModel($packages, $urlGeneratorDummy);
        $context    = $indexModel->getContext();

        $this->assertArraySubset([
            'packages' => [
                [
                    'name'     => 'package1',
                    'latest'   => '2.0',
                    'type'     => 'wordpress-plugin',
                    'versions' => [[], []],
                ],
                [
                    'name'     => 'package2',
                    'latest'   => '1.1',
                    'versions' => [
                        [
                            'foo' => 'bar',
                        ],
                    ],
                ],
            ],
        ], $context);
    }
}
