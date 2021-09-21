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
     *
     * @psalm-suppress UnsafeInstantiation
     */
    public function register(Container $pimple): void
    {
        $pimple['monolog.logger.class'] = Logger::class;
        $pimple['downloads.logfile']    = null;
        $pimple['downloads.log.format'] =
            "%datetime%\t%context.user%\t%context.ip%\t%context.vendor%\t%context.package%\t%context.version%\n";

        $pimple['downloads.log'] = function () use ($pimple) {
            /**
             * @var class-string<Logger> $loggerClass
             * @var Logger::class $container['monolog.logger.class']
             * @var Logger               $log
             */
            $loggerClass = $pimple['monolog.logger.class'];
            $log         = new $loggerClass('downloads');
            $handler     = $pimple['downloads.logfile'] ?
                new StreamHandler($pimple['downloads.logfile']) :
                new NullHandler();
            $formatter   = new LineFormatter($pimple['downloads.log.format'], DATE_RFC3339);

            $handler->setFormatter($formatter);
            $log->pushHandler($handler);

            return $log;
        };
    }
}
