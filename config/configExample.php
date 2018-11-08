<?php
declare(strict_types=1);

/**
 * This is an annotated example of a configuration file with default settings (or notes what they are).
 *
 * Copy to `config.php` and customize as needed.
 */

return [
    // Enable to put the application into the debug mode with extended error messages.
    // 'debug'                 => false,

    // Customize path to the directory containing release ZIP files.
    // 'release.dir'           => __DIR__.'/../releases',

    // General error log path.
    // 'monolog.logfile'       => null,

    // Error logging level.
    // 'monolog.level'         => 'DEBUG',

    // Path to downloads log, defaults to null for disabled.
    // 'downloads.logfile'    => __DIR__.'/../releases/downloads.log',

    // Format of download log entries.
    // 'downloads.log.format'  =>
    //    "%datetime%\t%context.user%\t%context.ip%\t%context.vendor%\t%context.package%\t%context.version%\n",

    //'users'                    => [
          // User login.
    //    'composer' => [
              // Provide password hash for HTTP authentication. See bin/encodePassword.php helper.
    //        'hash'     => '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a', // foo

              // Array of allowed package path matches.
    //        'allow'    => ['foo'],

              // Array of disallowed package path matches.
    //        'disallow' => ['bar'],
    //    ],
    // ],
];
