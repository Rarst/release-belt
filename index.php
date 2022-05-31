<?php
declare(strict_types=1);

/**
 * @deprecated 0.2:1.0 Use of package root as web root is deprecated in favor of `public/` directory.
 */

trigger_error('Use of package root as web root is deprecated in favor of `public/` directory.', E_USER_DEPRECATED);

require __DIR__.'/public/index.php';
