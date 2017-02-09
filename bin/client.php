<?php
/**
 * Created by PhpStorm.
 * User: yonh
 * Date: 2/5/17
 * Time: 10:17 AM
 */
require_once __DIR__ . '/../vendor/autoload.php';

$serviceManage = new ServiceManager();
$config = $serviceManage->getClientConfig();

require_once __DIR__ . "/_init_config.php";


//echo "key:" . $config->getKey() . PHP_EOL;
//echo "dir:" . $config->getDir();



$client = $serviceManage->getClient();

//$client->send();
$client->getFingerprint();