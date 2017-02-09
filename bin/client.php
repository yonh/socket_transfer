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
$server_hash = $client->getFingerprint();


$hash_keys = array_keys($server_hash);

$fhh = new FileHashHelper($serviceManage->getClientConfig()->getDir());
$local_hash = $fhh->generate_hash_array();

foreach ($local_hash as $k=>$hash) {
	if (in_array($k, $hash_keys)) {

		if ($hash['hash'] != $server_hash[$k]['hash']) {
			//echo $hash['hash'];
			$client->send($serviceManage->getClientConfig()->getDir() . $hash['file'], $hash['file']);
		} else {
			//echo "equals\n";
		}
	} else {
		$client->send($serviceManage->getClientConfig()->getDir() . $hash['file'], $hash['file']);
	}

	//usleep(10);
}



//$client->send( "/home/yonh/git/socket_transfer/bin/_init_config.php", "/a.php");
