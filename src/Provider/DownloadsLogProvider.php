<?php
declare(strict_types=1);

namespace Rarst\ReleaseBelt\Provider;

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Pimple\Container;
use Pimple\ServiceProviderInterface;

/**
 * Implements log of downloads.
 */
class DownloadsLogProvider implements ServiceProviderInterface
{
    /**
     * Registers and configures log service.
     */
    public function register(Container $app): void
    {
        $app['monolog.logger.class'] = Logger::class;
        $app['downloads.logfile']    = null;
        $app['downloads.log.format'] =
            "%datetime%\t%context.user%\t%context.ip%\t%context.vendor%\t%context.package%\t%context.version%\n";

        $app['downloads.log'] = function () use ($app) {
            /** @var Logger $log */
            $log       = new $app['monolog.logger.class']('downloads');
            $handler   = $app['downloads.logfile'] ?
                new StreamHandler($app['downloads.logfile']) :
                new NullHandler();
            $formatter = new LineFormatter($app['downloads.log.format'], DATE_RFC3339);

            $handler->setFormatter($formatter);
            $log->pushHandler($handler);

            return $log;
        };
    }
}
