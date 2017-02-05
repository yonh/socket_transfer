<?php

/**
 * Created by PhpStorm.
 * User: yonh
 * Date: 16-11-9
 * Time: 下午8:31
 *
 * version 1.0.2
 */
class FileHelper
{
	private function handleFilename($file) {
		//为了兼容windows和linux上文件名编码不一致
		if (strpos(php_uname(), 'Windows')!== false) {
			$file = iconv("utf-8", "gbk//IGNORE", $file);
		}

		return $file;
	}

	public function mkdirIfNotExists($dirname) {
		if (!self::file_exists($dirname)) {
			$mk = mkdir($dirname, 0700, true);
			if (!$mk) {
				throw new \Exception('创建目录失败:' . $dirname);
			}
		}
	}

	public function deleteFile($file) {
		if (self::file_exists($file) && is_file($file)) {
			if (!unlink($file)) {
				return false;
			} else {
				return true;
			}
		}
	}

	public function deleteFolder($dir)
	{
		$files = array_diff(scandir($dir), array('.','..'));
		foreach ($files as $file) {
			(is_dir("$dir/$file")) ? $this->deleteFolder("$dir/$file") : unlink("$dir/$file");
		}
		return rmdir($dir);
	}

	public function writeable($file) {
		$file = self::handleFilename($file);

		return is_writeable($file);
	}

	public function file_exists($file) {
		$file = self::handleFilename($file);

		return file_exists($file);
	}


}