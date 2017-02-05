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
	 * @return Config
	 */
	public function getConfig() {
		return new Config(__DIR__ . "/../config.json");
	}
}