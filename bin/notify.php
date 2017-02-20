<?php

require_once __DIR__ . '/../vendor/autoload.php';

$serviceManage = new ServiceManager();
$config = $serviceManage->getClientConfig();

require_once __DIR__ . "/_init_config.php";

$param =  $argv[1];

$client = $serviceManage->getClient();

if (endsWith($param, " CLOSE_WRITE,CLOSE")) {
	$repate_times = 20;
	while (true) {
		$repate_times--;
		if ($repate_times<0) break;

		$arr = explode(" CLOSE_WRITE,CLOSE", $param);
		$base_dir = $serviceManage->getClientConfig()->getDir();
		$filename = substr($arr[0], strlen($base_dir));

		// 文件是发布包则忽略
		if (endsWith($filename, "/releases.zip")) break;
		if (endsWith($filename, "/test-local.zip")) break;

		$result = $client->send($serviceManage->getClientConfig()->getDir() . $filename, $filename);

		if ($result) break;
	}
}




