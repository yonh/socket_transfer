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

	public function __construct($ip, $port, $run_time, $token) {
		$this->run_time = $run_time;
		$this->ip = $ip;
		$this->port = $port;
		$this->token = $token;
	}

	public function listen() {

		define("SEPARATOR", "|=+=|");//used for explode filename and contents
		define("APP_RECV_PACK_SIZE", 1024*10);
		//server
		$socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
		socket_bind($socket, $this->ip, (int)$this->port);
		socket_listen($socket);
		$path = "/tmp/socket_transfer/";
		echo "server listen ".$this->ip.":".$this->port."...";

		while (true)
		{

			$connection = socket_accept($socket);
			if($connection)
			{
				//$timer = new timer();
				//$timer->start();

				$buffer = '';
				$begin = TRUE;
				while(socket_recv($connection, $buffer, APP_RECV_PACK_SIZE, 0))
				{
					if ($begin)
					{
						$msg = explode(SEPARATOR, $buffer);
						$file_name = $msg[0];


						if ($file_name != "fingerprint") {
							$file_head = $msg[1];
							$file_addr = $path . basename($file_name ). ".bak";
							$fp = fopen($file_addr, "wb"); //create new file
							if (!$fp)
							{
								echo "Create file error!/n";
								break;
							} else {
								fwrite($fp, $file_head); //begin to write
							}
							$begin = FALSE;
							continue;
						} else {
							// 发送指纹文件
							//$fingerpointData = file_get_contents("/home/yonh/Pictures/Screenshot from 2017-01-25 15-26-40.png");
							echo "send fingerpoint data\n";

							//生成指纹文件
							$hash_array = $this->fileHashHelper->generate_hash_array();
							//file_put_contents(__DIR__ . "/hash.json", json_encode($hash_array));
							$fingerpointData = json_encode($hash_array);

							socket_write($connection, $fingerpointData);
							socket_write($connection, SEPARATOR);

							continue;
						}
					}
					//write the remaining data with loop
					if (isset($fp)) {
						fwrite($fp, $buffer);
					}
				}
//$timer->stop();
//echo $timer->spent()."/n";
				if (isset($fp)) {
					fclose($fp);
				}
				socket_close($connection);
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


}