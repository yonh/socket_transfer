<?php

/**
 * Created by PhpStorm.
 * User: yonh
 * Date: 2/5/17
 * Time: 3:03 PM
 */
class Server
{
	private $run_time = 0;
	private $ip;
	private $port;
	private $token;

	/** @var  FileHashHelper */
	private $fileHashHelper;
	/** @var  FileHelper */
	private $fileHelper;

	private $config;

	public function __construct($ip, $port, $run_time, $token)
	{
		$this->run_time = $run_time;
		$this->ip       = $ip;
		$this->port     = $port;
		$this->token    = $token;
	}

	private function sendHash($connection)
	{
		// 发送指纹文件
		//$fingerpointData = file_get_contents("/home/yonh/Pictures/Screenshot from 2017-01-25 15-26-40.png");
		echo "send fingerpoint data\n";

		//生成指纹文件
		$hash_array = $this->fileHashHelper->generate_hash_array();
		//file_put_contents(__DIR__ . "/hash.json", json_encode($hash_array));
		$fingerpointData = json_encode($hash_array);

		socket_write($connection, $fingerpointData);
		socket_write($connection, SEPARATOR);
	}

	private function file($data)
	{
		// todo 保存文件 done
		// todo 校验文件 done
		// todo 替换文件 done
		// todo 创建不存在的目录 done

		echo "echo file\n";

		print_r($data);

		$file_name = $data[1];
		$hash = $data[2];
		$file_contents = $data[3];
		$path = "/tmp/socket_transfer/";
		$file_addr = $path . basename($file_name) . ".bak";
		$fp        = fopen($file_addr, "wb"); //create new file
		if (!$fp) {
			echo "Create file error!/n";
		} else {
			fwrite($fp, base64_decode($file_contents));
		}

		if (md5_file($file_addr) == $hash) {
			$basedir = $this->config->getDir() . dirname($file_name);
			echo "move file to " . $basedir . basename($file_name) . "\n";
			$this->fileHelper->mkdirIfNotExists($this->config->getDir() . dirname($file_name));

			return rename($file_addr, $basedir . "/" . basename($file_name));
		} else {
			echo "file verify fail.";
		}
		echo "\n";
	}

	public function listen()
	{

		define("SEPARATOR", "|=+=|");//used for explode filename and contents
		define("APP_RECV_PACK_SIZE", 1024 * 10);
		//server
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_bind($socket, $this->ip, (int)$this->port);
		socket_listen($socket);

		echo "server listen " . $this->ip . ":" . $this->port . "...\n";

		while (true) {

			$connection = socket_accept($socket);
			if ($connection) {


				$buffer = '';

				$tmp_file = "/tmp/rev";
				file_put_contents($tmp_file, '');

				$action = "";

				while (socket_recv($connection, $buffer, APP_RECV_PACK_SIZE, 0)) {
					//将请求数据写入本地
					file_put_contents($tmp_file, $buffer, FILE_APPEND);
					if ($action == "") {
						$data   = explode(SEPARATOR, file_get_contents($tmp_file));
						$action = $data[0];
					}

					switch ($action) {
						case 'fingerprint':
							$this->sendHash($connection);
							break;
					}

				}

				$data = explode(SEPARATOR, file_get_contents($tmp_file));
//
				switch ($data[0]) {
					case 'file':
						$this->file($data);
						break;
				}

				socket_close($connection);


//				while(socket_recv($connection, $buffer, APP_RECV_PACK_SIZE, 0))
//				{
//					file_put_contents($tmp_file, $buffer, FILE_APPEND);
//					if ($begin)
//					{
//						$msg = explode(SEPARATOR, $buffer);
//						$request_tag = $msg[0];
//
//						//print_r($msg);
//
//
////						if ($request_tag != "fingerprint") {
////							$file_head = $msg[1];
////							$file_addr = $path . basename($request_tag) . ".bak";
////							$fp        = fopen($file_addr, "wb"); //create new file
////							if (!$fp) {
////								echo "Create file error!/n";
////								break;
////							} else {
////								fwrite($fp, $file_head); //begin to write
////							}
////							$begin = FALSE;
////							continue;
////						} else
//						if ($request_tag == "file") {
//							echo "echo file\n";
//
//							$file_name = $msg[1];
//							$file_head = $msg[2];
//							//print_r($buffer);
//							$file_addr = $path . basename($file_name) . ".bak";
//							$fp        = fopen($file_addr, "wb"); //create new file
//							if (!$fp) {
//								echo "Create file error!/n";
//								break;
//							} else {
//								fwrite($fp, $file_head); //begin to write
//							}
//							$begin = FALSE;
//
//							continue;
//						} else if ($request_tag == "fingerprint") {
//							// 发送指纹文件
//							//$fingerpointData = file_get_contents("/home/yonh/Pictures/Screenshot from 2017-01-25 15-26-40.png");
//							echo "send fingerpoint data\n";
//
//							//生成指纹文件
//							$hash_array = $this->fileHashHelper->generate_hash_array();
//							//file_put_contents(__DIR__ . "/hash.json", json_encode($hash_array));
//							$fingerpointData = json_encode($hash_array);
//
//							socket_write($connection, $fingerpointData);
//							socket_write($connection, SEPARATOR);
//
//							continue;
//						} else {
//							echo 'fff';
//						}
//					}
//					//write the remaining data with loop
//					if (isset($fp)) {
//						fwrite($fp, $buffer);
//						echo $buffer;
//						echo "write buff " . rand() . "\n";
//					}
//				}
////$timer->stop();
////echo $timer->spent()."/n";
//				if (isset($fp)) {
//					fclose($fp);
//				}
//				socket_close($connection);
			}
		}
		socket_close($socket);
	}

	/**
	 * @return FileHashHelper
	 */
	public function getFileHashHelper()
	{
		return $this->fileHashHelper;
	}

	/**
	 * @param FileHashHelper $fileHashHelper
	 */
	public function setFileHashHelper($fileHashHelper)
	{
		$this->fileHashHelper = $fileHashHelper;
	}

	/**
	 * @return mixed
	 */
	public function getConfig()
	{
		return $this->config;
	}

	/**
	 * @param mixed $config
	 */
	public function setConfig($config)
	{
		$this->config = $config;
	}

	/**
	 * @return FileHelper
	 */
	public function getFileHelper()
	{
		return $this->fileHelper;
	}

	/**
	 * @param FileHelper $fileHelper
	 */
	public function setFileHelper($fileHelper)
	{
		$this->fileHelper = $fileHelper;
	}


}