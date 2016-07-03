<?php
// For the ZFiA source code distribution, we have one copy of Zend
// Framework, so we add it to the include path here.
$lib = realpath(dirname(basename(__FILE__)) . '/../../lib');
set_include_path(get_include_path() . PATH_SEPARATOR . $lib );

include '../application/bootstrap.php';

// Specify your config section here or use an environment variable
$configSection = getenv('PLACES_CONFIG') ? getenv('PLACES_CONFIG') : 'dev';
$bootstrap = new Bootstrap('general');
$bootstrap->runApp();
