#! /bin/bash

if [ -f webtransco.pid ]
then
	let PID=`cat webtransco.pid`
	kill -TERM $PID
	rm webtransco.pid
else
	echo "not running"
fi

exit
	
