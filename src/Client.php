<?php

/**
 * Created by PhpStorm.
 * User: yonh
 * Date: 2/5/17
 * Time: 3:21 PM
 */
class Client
{
	private $port;
	private $ip;
	private $token;

	private $separator = "|=+=|";

	/**
	 * Client constructor.
	 * @param string $string
	 * @param string $getPort
	 * @param int    $int
	 * @param string $getKey
	 */
	public function __construct($ip, $port, $token) {
		$this->ip = $ip;
		$this->port = $port;
		$this->token = $token;
	}

	public function send() {
		//define("SEPARATOR", "|=+=|");//used for explode filename and contents

		$start = time();
		$filename =  "/home/yonh/git/socket_transfer/bin/_init_config.php";
		$fp = fsockopen($this->ip, $this->port, $errno, $errstr, 30);
		if (!$fp) {
			echo "$errstr ($errno)<br />/n";
		} else {
			fwrite($fp, "file".$this->separator);
			fwrite($fp, $filename.$this->separator);
			$out = file_get_contents($filename);//read data in once
			$out = base64_encode($out);
			echo $out;
			echo "\n";
			fwrite($fp, $out);
		}


		echo md5_file($filename);

		$end = time();
		echo 'time:' . ($end - $start);
	}

	public function getFingerprint()
	{
		$start = time();
		//$filename =  "/home/yonh/git/socket_transfer/bin/_init_config.php";
		//$fp = fsockopen($this->ip, $this->port, $errno, $errstr, 30);

		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$result = socket_connect($socket, $this->ip, $this->port);


		//if (!$fp) {
		//	echo "$errstr ($errno)<br />/n";
		//} else {
		//fwrite($fp, "fingerprint".$this->separator);
		socket_write($socket, "fingerprint" . $this->separator);
		$first = true;

		$hash_file = __DIR__ . "/hash.json";


		while ($out = socket_read($socket, 8192)) {

			if (endsWith($out, trim($this->separator))) {
				$out = str_replace($this->separator, "", $out);
				file_put_contents($hash_file, $out, FILE_APPEND);
				socket_close($socket);
				break;
			} else {
				if ($first) {
					$first = false;
					file_put_contents($hash_file, $out);
				} else {
					file_put_contents($hash_file, $out,FILE_APPEND);
				}
			}
		}
		echo "close";
	}
}