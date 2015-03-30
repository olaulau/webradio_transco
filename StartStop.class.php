<?php

require_once 'Process.class.php';


class StartStop {
	
	public static $pid_filename = 'webtransco.pid';
	
	
	public static function start() {
		if(! file_exists(StartStop::$pid_filename)) {
			$command = "cvlc -vvv http://hd.stream.frequence3.net/frequence3.flac --sout '#transcode{vcodec=none,acodec=vorb,ab=256} :standard{access=http,mux=ogg,dst=:8000/}'";
			$p = new Process($command);
	
			$fo = fopen(StartStop::$pid_filename, 'w');
			fwrite($fo, $p->getPid());
			fclose($fo);
		}
		else {
			die("already running");
		}
	}
	
	
	public static function stop() {
		if(file_exists(StartStop::$pid_filename)) {
			$fo = fopen(StartStop::$pid_filename, 'r');
			$pid = fgets($fo);
			fclose($fo);
	
			$p = new Process();
			$p->setPid($pid);
			$ret = $p->stop();
	
			unlink(StartStop::$pid_filename);
		}
		else {
			die("not running");
		}
	}
	
}
