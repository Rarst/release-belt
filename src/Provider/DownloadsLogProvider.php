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
    public function register(Container $container): void
    {
        $container['monolog.logger.class'] = Logger::class;
        $container['downloads.logfile']    = null;
        $container['downloads.log.format'] =
            "%datetime%\t%context.user%\t%context.ip%\t%context.vendor%\t%context.package%\t%context.version%\n";

        $container['downloads.log'] = function () use ($container) {
            /** @var Logger $log */
            $log       = new $container['monolog.logger.class']('downloads');
            $handler   = $container['downloads.logfile'] ?
                new StreamHandler($container['downloads.logfile']) :
                new NullHandler();
            $formatter = new LineFormatter($container['downloads.log.format'], DATE_RFC3339);

            $handler->setFormatter($formatter);
            $log->pushHandler($handler);

            return $log;
        };
    }
}
