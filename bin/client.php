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

$success = 0;
$fail = 0;
$time = time();

	$client = $serviceManage->getClient();
	$server_hash = $client->getFingerprint();

	//print_r($server_hash);die;


	$hash_keys = array_keys($server_hash);

	$fhh = new FileHashHelper($serviceManage->getClientConfig()->getDir());
	$local_hash = $fhh->generate_hash_array();



	//500->88 ->0f
	//250->90 ->16f
	//300->90 ->28f
	//400->89 ->2f

	$sleepMicro = 1500; // last best 5000
	foreach ($local_hash as $k=>$hash) {

		if (in_array($k, $hash_keys)) {

			if ($hash['hash'] != $server_hash[$k]['hash']) {
				usleep($sleepMicro);
				//echo $hash['hash'] . "    " . $server_hash[$k]['hash'];
				$result = $client->send($serviceManage->getClientConfig()->getDir() . $hash['file'], $hash['file']);
				if ($result) {
					$success++;
				} else {
					$fail++;
				}

			} else {
				//echo "equals\n";
			}
		} else {
			usleep($sleepMicro);
			//usleep(15000);
			//echo $hash['file'] . $hash['hash'] . "    " . $server_hash[$k]['hash'];
			if ($client->send($serviceManage->getClientConfig()->getDir() . $hash['file'], $hash['file'])) {
				$success++;
			} else {
				$fail++;
			}
		}
		//sleep(1);
	}



echo "send files:" . ($success + $fail) . "\n";
echo "success files:" . $success . "\n";
echo "fail files:" . $fail . "\n";
echo "time:" . (time() - $time) . "\n";

//$client->send( "/home/yonh/git/socket_transfer/bin/_init_config.php", "/a.php");
