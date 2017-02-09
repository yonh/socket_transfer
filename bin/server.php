<?php
/**
 * Created by PhpStorm.
 * User: yonh
 * Date: 2/5/17
 * Time: 10:17 AM
 */
require_once __DIR__ . '/../vendor/autoload.php';

$serviceManage = new ServiceManager();
$config = $serviceManage->getServerConfig();

require_once __DIR__ . "/_init_config.php";


$server = $serviceManage->getServer();
$server->listen();
