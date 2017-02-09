<?php

/**
 * Created by PhpStorm.
 * User: yonh
 * Date: 2/9/17
 * Time: 10:29 AM
 */
class FileHashHelperTest extends \PHPUnit_Framework_TestCase
{
	public function test() {
		$files_dir = __DIR__ . "/test_files";
		$hashHelper = new FileHashHelper($files_dir);
		$actual = $hashHelper->generate_hash_array();

		$expect = array(
			'/ff/a'=>array(
				'file' => '/ff/a',
				'hash'=>md5_file($files_dir . "/ff/a")
			),
			'/a'=>array(
				'file' => '/a',
				'hash'=>md5_file($files_dir . "/a")
			),
			'/b'=>array(
				'file' => '/b',
				'hash'=>md5_file($files_dir . "/b")
			)
		);

		$this->assertSame($expect, $actual);

	}
}
