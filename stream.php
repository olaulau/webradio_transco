<?php


require_once 'Stream.class.php';


// DB
Stream::prepare_db();


// register shutdown callback, to stop the stream server
function shutdown()
{
	$stream = Stream::find_stream(1);
	$stream->stop();
}
register_shutdown_function('shutdown');


// start the stream server
$stream = Stream::find_stream(1);
$stream->start();


// wait for the HTTP server to respond
function test_http() {
	$connection = @fsockopen("localhost", 8000);
	return is_resource($connection);
}
while(!test_http()) {
	usleep(100*1000); // 0.1s 
}


// send headers, so that it can be viewed directly in the browser with a plugin
header('Content-type: application/octet-stream');
header('Cache-Control: no-cache');


// start streaming
$handle = fopen("http://localhost:8000/", "rb");
// echo "<pre>"; print_r($http_response_header); echo "</pre>"; die;
if (FALSE === $handle) {
	exit("Echec lors de l'ouverture du flux vers l'URL");
}

while (!feof($handle)) {
	$buffer = fread($handle, 8192);
	echo $buffer;
}
fclose($handle);
