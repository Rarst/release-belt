<?php
/**
 * This is an annotated example of a configuration file with default settings.
 *
 * Copy to `config.php` and customize as needed.
 */

return [
    // Enable to put the application into the debug mode with extended error messages.
    'debug'       => false,

    // Customize path to the directory containing release ZIP files.
    'release.dir' => __DIR__.'/../releases',

    // Provide login => password hash pairs to enable HTTP authentication. See bin/encodePassword.php helper.
    // 'http.users' => [
    //     'composer' => '$2y$10$3i9/lVd8UOFIJ6PAMFt8gu3/r5g0qeCJvoSlLCsvMTythye19F77a', // foo
    // ],
];
