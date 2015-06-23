<?php
define("APP_PATH",  dirname(__FILE__) . '/');
define("CONF_PATH",  APP_PATH . 'conf/');

$app  = new Yaf_Application( CONF_PATH . "application.ini" );
$app->bootstrap();
$app->run();