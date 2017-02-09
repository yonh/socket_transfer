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
	public function send2($send_file, $filename) {
		//define("SEPARATOR", "|=+=|");//used for explode filename and contents

		$start = time();
		//$filename =  "/home/yonh/git/socket_transfer/bin/_init_config.php";
		$fp = fsockopen($this->ip, $this->port, $errno, $errstr, 30);
		if (!$fp) {
			echo "$errstr ($errno)<br />/n";
		} else {
			fwrite($fp, "file".$this->separator);
			fwrite($fp, $filename.$this->separator);
			fwrite($fp, md5_file($send_file).$this->separator);
			$out = file_get_contents($send_file);//read data in once
			$out = base64_encode($out);
			//echo $out . "\n";
			fwrite($fp, $out);

		}

		//echo md5_file($send_file);

		$end = time();
		echo 'time:' . ($end - $start);
	}

	public function send($send_file, $filename) {

		//define("SEPARATOR", "|=+=|");//used for explode filename and contents

		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		$result = socket_connect($socket, $this->ip, $this->port);


		//$start = time();
		//$filename =  "/home/yonh/git/socket_transfer/bin/_init_config.php";
		//$fp = fsockopen($this->ip, $this->port, $errno, $errstr, 30);
		if (!$result) {
			echo "socket error \n";
		} else {
			echo "send file: " . escapeshellcmd($send_file) . "\n";
			socket_write($socket, "file".$this->separator);
			socket_write($socket, $filename.$this->separator);
			socket_write($socket, md5_file($send_file).$this->separator);

			$out = file_get_contents($send_file);//read data in once
			$out = base64_encode($out);
			//echo $out . "\n";
			socket_write($socket, $out);

		}

		@socket_close($socket);

		//echo md5_file($send_file);

		//$end = time();
		//echo 'time:' . ($end - $start);
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

		@socket_close($socket);

		$hash_data = @json_decode(file_get_contents($hash_file), true);
		@unlink($hash_file);
		return $hash_data;
	}
}