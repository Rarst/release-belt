<?php
/**
 * This is an annotated example of a configuration file with default settings.
 *
 * Copy to `config.php` and customize as needed.
 */

return [
    // Enable to put the application into the debug mode with extended error messages.
    'debug'                 => false,

    // Customize path to the directory containing release ZIP files.
    'release.dir'           => __DIR__.'/../releases',

    // General error log, defaults to `error_log` PHP setting.
    // 'monolog.log'           => null,

    // Error logging level, defaults to ERROR or DEBUG if enabled as above.
    // 'monolog.level'         => 'ERROR',

    // Enable log of downloads.
    'downloads.log.enabled' => false,

    // Path to downloads log.
    'downloads.log.path'    => __DIR__.'/../releases/downloads.log',

    // Format of download log entries.
    'downloads.log.format'  =>
        "%datetime%\t%context.user%\t%context.ip%\t%context.vendor%\t%context.package%\t%context.version%\n",

    // Provide login => password hash pairs to enable HTTP authentication. See bin/encodePassword.php helper.
    // 'http.users' => [
    //     'composer' => '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a', // foo
    // ],
];
