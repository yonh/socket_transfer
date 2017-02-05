<?php
/**
 * Created by PhpStorm.
 * User: yonh
 * Date: 2/5/17
 * Time: 10:17 AM
 */
require_once __DIR__ . '/../vendor/autoload.php';

$serviceManage = new ServiceManager();
$config = $serviceManage->getConfig();

if (!$config->check()) {
	echo "配置文件未创建是否创建(yes)";
	$create = fgets(STDIN);

	if (empty(trim($create)) || "yes" == $create) {
		$config->create_config();

	} else {
		echo "exit.";
		die;
	}
}






echo "key:" . $config->getKey() . PHP_EOL;
echo "dir:" . $config->getDir();



