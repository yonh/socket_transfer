<?php

/**
 * Created by PhpStorm.
 * User: yonh
 * Date: 2/5/17
 * Time: 10:21 AM
 */
class ServiceManager
{
	/**
	 * @return \Config\Config
	 */
	public function getServerConfig() {
		return new \Config\Config(__DIR__ . "/../s_config.json");
	}

	public function getClientConfig() {
		return new \Config\Config(__DIR__ . "/../c_config.json");
	}

	/** @return Server */
	public function getServer() {
		$config = $this->getServerConfig();

		$server = new Server($config->getIp(), $config->getPort(), 3600, $config->getKey());
		$server->setFileHashHelper(new FileHashHelper($config->getDir()));
		$server->setConfig($config);
		$server->setFileHelper(new FileHelper());

		return $server;
	}

	public function getClient() {
		$config = $this->getClientConfig();

		return new Client($config->getIp(), $config->getPort(), $config->getKey());
	}



}