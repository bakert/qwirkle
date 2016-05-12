<?php

date_default_timezone_set('UTC');
require_once "autoloader.php";
spl_autoload_register([new Autoloader(), 'load']);

(new Exec())->go();
