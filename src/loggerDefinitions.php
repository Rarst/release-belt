<?php

/** @deprecated 0.7:0.8 */

declare(strict_types=1);

use Monolog\Formatter\LineFormatter;
use Monolog\Handler\NullHandler;
use Monolog\Handler\StreamHandler;
use Monolog\Logger;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;

use function DI\get;

return [
    'monolog.logger.class' => Logger::class,
    'downloads.logfile'    => null,
    'downloads.log.format' =>
        "%datetime%\t%context.user%\t%context.ip%\t%context.vendor%\t%context.package%\t%context.version%\n",
    'downloads.log'        => function (ContainerInterface $container) {
        if (! is_null($container->get('downloads.logfile'))) {
            trigger_error(
                'Built-in logging is deprecated and will be removed in version 0.8',
                E_USER_DEPRECATED
            );
        }

        /**
         * @var class-string<Logger> $loggerClass
         * @var Logger::class $container['monolog.logger.class']
         * @var Logger               $log
         */
        $loggerClass = $container->get('monolog.logger.class');
        $log         = new $loggerClass('downloads');
        $handler     = $container->get('downloads.logfile') ?
            new StreamHandler($container->get('downloads.logfile')) :
            new NullHandler();
        $formatter   = new LineFormatter($container->get('downloads.log.format'), DATE_RFC3339);

        $handler->setFormatter($formatter);
        $log->pushHandler($handler);

        return $log;
    },
    LoggerInterface::class => get('downloads.log'),
];
