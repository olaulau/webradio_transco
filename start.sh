#! /bin/bash

if [ ! -f webtransco.pid ]
then
	cvlc -vvv http://hd.stream.frequence3.net/frequence3.flac --sout '#transcode{vcodec=none,acodec=vorb,ab=256} :standard{access=http,mux=ogg,dst=:8000/}' >/dev/null 2>&1 &
	PID=$!
	echo $PID > webtransco.pid
else
	echo "already running"
fi

exit

