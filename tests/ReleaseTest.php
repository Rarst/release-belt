<?php

namespace Rarst\ReleaseBelt;

use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class ReleaseTest extends TestCase
{
    /**
     * @dataProvider filenameProvider
     *
     * @param string $filename
     * @param string $package
     * @param string $version
     */
    public function testParseFilename($filename, $package, $version)
    {
        $splFileInfoMock = $this->getMockBuilder(SplFileInfo::class)
            ->disableOriginalConstructor()
            ->getMock();

        $splFileInfoMock->expects($this->once())
            ->method('getRelativePath')
            ->willReturn('type/vendor');

        $splFileInfoMock->expects($this->once())
            ->method('getFilename')
            ->willReturn($filename);

        $release = new Release($splFileInfoMock);

        $this->assertEquals($package, $release->package);
        $this->assertEquals($version, $release->version);
    }

    public function filenameProvider()
    {
        return [
            ['invalid', null, 'dev-unknown'],
            [ 'backupbuddy-4.1.2.2.zip', 'backupbuddy', '4.1.2.2' ],
//            [ 'contact-form-7.4.1.zip', 'contact-form', '4.1' ], 7 indistinguishable from part of version
            [ 'facetwp-1.8.6.zip', 'facetwp', '1.8.6' ],
            [ 'ga-ecommerce-3.0.2.zip', 'ga-ecommerce', '3.0.2' ],
            [ 'google-analytics-premium-1.1.8.zip', 'google-analytics-premium', '1.1.8' ],
            [ 'gravityforms_1.8.22.zip', 'gravityforms', '1.8.22' ],
            [ 'gravityformsmailchimp_3.2.zip', 'gravityformsmailchimp', '3.2' ],
            [ 'mailchimp-for-wp-pro-2.5.5.zip', 'mailchimp-for-wp-pro', '2.5.5' ],
            [ 'nextgen-facebook.7.7.5.1.zip', 'nextgen-facebook', '7.7.5.1' ],
            [ 'ninja-forms-mailchimp-v1.3.2.zip', 'ninja-forms-mailchimp', '1.3.2' ],
            [ 'p3-profiler.1.5.3.6.zip', 'p3-profiler', '1.5.3.6' ],
            [ 'searchwp-2.4.9.zip', 'searchwp', '2.4.9' ],
            [ 'searchwp-term-highlight-1.8.7.zip', 'searchwp-term-highlight', '1.8.7' ],
            [ 'sitepress-multilingual-cms.3.1.8.4.zip', 'sitepress-multilingual-cms', '3.1.8.4' ],
            [ 'wp-retina-2x.2.4.0.zip', 'wp-retina-2x', '2.4.0' ],
            [ 'wp-lightbox-2.zip', 'wp-lightbox', '2' ],
            [ 'wpml-translation-management.1.9.9.zip', 'wpml-translation-management', '1.9.9' ],
            [ 'polylang-2.0.zip', 'polylang', '2.0' ],
            [ 'polylang-2.0-beta2.zip', 'polylang', '2.0-beta2' ],
            [ 'polylang-2.0-beta.1.zip', 'polylang', '2.0-beta.1' ],
//            [ 'polylang-2.0-rc.1.zip', 'polylang', '2.0-rc.1' ], no support for numeric RCs in regex
        ];
    }
}
