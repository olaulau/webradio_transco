<?php

require_once 'StartStop.class.php';
require_once 'Stream.class.php';


// DB
Stream::prepare_db();


// register shutdown callback, to stop the stream server
function shutdown()
{
// 	StartStop::stop();
	$stream = Stream::get_the_stream(1);
	$stream->stop(1);
}
register_shutdown_function('shutdown');


// start the stream server
// StartStop::start();
$stream = Stream::get_the_stream(1);
$stream->start(1);


// wait for the HTTP server to respond
function test_http() {
	$connection = @fsockopen("localhost", 8000);
	return is_resource($connection);
}
while(!test_http()) {
	usleep(100*1000); // 0.1s 
}


// start streaming
$handle = fopen("http://localhost:8000/", "rb");
if (FALSE === $handle) {
	exit("Echec lors de l'ouverture du flux vers l'URL");
}

while (!feof($handle)) {
	$buffer = fread($handle, 8192);
	echo $buffer;
}
fclose($handle);
