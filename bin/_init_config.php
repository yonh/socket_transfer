<?php

if (!$config->check()) {
	echo "配置文件未创建是否创建(yes)";
	$create = fgets(STDIN);

	if (empty(trim($create)) || "yes" == $create) {
		$config->create_config();

	} else {
		echo "exit.";
		die;
	}
}
