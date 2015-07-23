<?php
require('libs/browscap-php/src/phpbrowscap/Browscap.php');

use phpbrowscap\Browscap;

date_default_timezone_set('America/Chicago');

$browscap = new Browscap('/var/php_cache/browser');
$browscap->updateCache();

?>
