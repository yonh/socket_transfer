<?php
/**
 * Created by PhpStorm.
 * User: yonh
 * Date: 2/9/17
 * Time: 10:26 AM
 */

class FileHashHelper
{
	private $folder;

	public function __construct($folder)
	{
		$this->folder = $folder;
	}

	public function generate_hash_array() {
		//$ignore = $this->ignores();

		$contents = array();
		$dir = new \RecursiveDirectoryIterator($this->folder);

		//$upgradeObject = $this->upgradeService = new UpgradeService(REAL_PATH, "192.168.50.45:9999", REAL_PATH . "/datas/upgrade");

		foreach (new \RecursiveIteratorIterator($dir) as $file) {
			$filename = $file->getFilename();
			$fullname = $file->getPathname();
			$rel = $this->relativelyDirname($fullname, $this->folder);
			$rel = str_replace("\\", "/", $rel);

			if ($filename == '..' || $filename == '.') {
				continue;
			}

			$ignored = false;
//			foreach ($ignore as $ig) {
//				$isdir = false;
//				if ($this->endWith("/", $ig)) {
//					$isdir =true;
//				}
//				//忽略临时文件
//				if ($this->endWith("~", $rel)) {
//					$ignored = true;
//					break;
//				}
//
//				if ($isdir && $this->startwith($ig, $rel)) {
//					$ignored  = true;
//					break;
//				} elseif ($rel == $ig) {
//					$ignored  = true;
//					break;
//				}
//			}

			if ($ignored) continue;

			$hash = md5_file($fullname);
			//echo $fullname;

			$data = array(
				'file'=>$rel,
				'hash'=>$hash,
			);

//			$fileInfo = new FileInfo();
//			$fileInfo->hash = $hash;
//			$fileInfo->filename = $rel;
//			$fileInfo->url = $this->downloadUrl($rel, $download_host);
//			$fileInfo->update_time = date('Y-m-d H:i:s');
//			$fileInfo->version = $this->version;

			$contents[$rel] = $data;
		}

		//$file = REAL_PATH . "/temp/files.hash";
//		$file = $writeto;
//		$fm = new FileManager($file, $contents);
//		$fm->writeList($contents);

		return $contents;
	}

	function relativelyDirname($file, $basename) {
		//$basename = REAL_PATH;
		$len = strlen($basename);
		return substr($file, $len);
	}
}