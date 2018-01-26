<?php

namespace Rarst\ReleaseBelt\Provider;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

class DownloadsLogProvider implements ServiceProviderInterface
{
    public function register(Container $app)
    {
        $app['downloads.log.enabled'] = false;
        $app['downloads.log.path']    = __DIR__.'/../../releases/downloads.log';
        $app['downloads.log.format']  =
            "%datetime%\t%context.user%\t%context.ip%\t%context.vendor%\t%context.package%\t%context.version%\n";

        $app['downloads.log'] = function () use ($app) {
            /** @var Logger $log */
            $log       = new $app['monolog.logger.class']('downloads');
            $handler   = new StreamHandler($app['downloads.log.path']);
            $formatter = new LineFormatter($app['downloads.log.format'], DATE_RFC3339);

            $handler->setFormatter($formatter);
            $log->pushHandler($handler);

            return $log;
        };
    }
}