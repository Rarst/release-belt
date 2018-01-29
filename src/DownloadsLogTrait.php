<?php

namespace Rarst\ReleaseBelt;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Finder\SplFileInfo;

trait DownloadsLogTrait
{
    public function logDownload(SplFileInfo $file)
    {
        if ( ! $this['downloads.logfile']) {
            return false;
        }

        /** @var Request $request */
        $request = $this['request_stack']->getCurrentRequest();
        $release = new Release($file);

        $package = "{$release->vendor}/{$release->package}";
        $context = [
            'user'    => $request->getUser() ?: 'anonymous',
            'ip'      => $request->getClientIp(),
            'vendor'  => $release->vendor,
            'package' => $release->package,
            'version' => $release->version,
        ];

        return $this['downloads.log']->info($package, $context);
    }
}
