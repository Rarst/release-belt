<?php

namespace Rarst\ReleaseBelt\Tests\Model;

use PHPUnit\Framework\TestCase;
use Rarst\ReleaseBelt\Model\IndexModel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Generator\UrlGenerator;

class IndexModelTest extends TestCase
{
    public function testGetContext()
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

        $requestDummy = $this->getMockBuilder(Request::class)
            ->disableOriginalConstructor()
            ->getMock();

        $urlGeneratorDummy = $this->getMockBuilder(UrlGenerator::class)
            ->disableOriginalConstructor()
            ->getMock();

        $indexModel = new IndexModel($packages, $requestDummy, $urlGeneratorDummy);
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
