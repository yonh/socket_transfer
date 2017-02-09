<?php
namespace Config;

/**
 * Created by PhpStorm.
 * User: yonh
 * Date: 2/5/17
 * Time: 10:25 AM
 */
class Config
{
	private $config_file;
	public $config_data = null;
	private $fileHelper = null;

	public function __construct($config_file)
	{
		$this->config_file = $config_file;
		$this->fileHelper = new \FileHelper();

		$this->reload_config_data();
	}

	public function check()
	{
		if (!$this->fileHelper->file_exists($this->config_file)) {
			return false;
		}
		return true;
	}

	public function create_config() {
		$key = $this->prompt_get_when_empty_exit("密钥");
		$dir = $this->prompt_get_when_empty_exit("目录");
		$ip = $this->prompt_get_when_empty_exit("监听IP");
		$port = $this->prompt_get_when_empty_exit("监听端口");

		$json = array(
			'key'=>$key,
			'dir'=>$dir,
			'ip'=>$ip,
			'port'=>$port,
		);

		if (!file_exists($dir)) {
			echo "目录不存在,程序退出";
			die;
		}
		if (!intval($port)) {
			echo "端口异常,程序退出";
			die;
		}

		if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
			echo "IP异常,程序退出";
			die;
		}

		file_put_contents($this->config_file, json_encode($json));
		$this->config_data = $json;
	}

	private function prompt_get_when_empty_exit($key) {
		$value = $this->prompt_get($key);

		if (empty($value)) {
			echo "[$key]输入错误,程序退出";
			die;
		}

		return $value;
	}

	private function prompt_get($keyword, $repeat_times = 3) {
		$times = 0;
		$key = "";

		echo "请输入[$keyword]:";
		while ($times<$repeat_times) {
			$times++;
			$key = trim(fgets(STDIN));
			if (empty($key)) {
				echo "[$keyword]不能为空,请重新输入:";
			} else {
				return $key;
			}
		}

		return $key;
	}

	public function errorMessage() {

		return "error";
	}
	private function getConfigValue($key) {
		if (isset($this->config_data[$key])) {
			return $this->config_data[$key];
		}

		return "";
	}
	public function getKey() {
		if (empty($this->config_data)) {
			$this->reload_config_data();
		}

		return $this->getConfigValue('key');
	}

	public function getDir() {
		return $this->getConfigValue('dir');
	}

	public function getPort() {
		return $this->getConfigValue('port');
	}

	public function getIp() {
		return $this->getConfigValue('ip');
	}

	public function reload_config_data() {
		if ($this->fileHelper->file_exists($this->config_file)) {
			$this->config_data = json_decode(file_get_contents($this->config_file), true);
		}
	}




}