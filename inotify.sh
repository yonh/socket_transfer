#!/bin/bash

dir="/home/yonh/git/socket_transfer/tests/client/"
inotifywait -mrq --timefmt '%d%m%y %H:%M' --format '%w%f %e' -e close_write,delete,create,attrib $dir | while read file
do
	php bin/notify.php "${file}"
	echo "${file}" >> log.txt
done