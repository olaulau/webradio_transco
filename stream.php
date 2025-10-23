<?php
require_once __DIR__ . '/includes/ALL.inc.php';

if(empty($_GET['id'])) {
	die("no stream id specified");
}

$id = $_GET['id'];
if(!ctype_digit($id) || $id <= 0) {
	die("id is not a positive integer");
}

$id = (int)$id;

// DB
StreamMdl::prepare_db();


// register shutdown callback :
// handle client disconnection (or script end : end of stream)
// to update counter and stop the server if useless
function shutdown()
{
	global $id;
	try { StreamMdl::end_transaction(); } catch(Exception $ex) {}
	$stream = StreamSvc::find_stream($id);
	if(isset($stream))
		$stream->stop();
}
register_shutdown_function('shutdown');


// start the stream server
$stream = StreamSvc::find_stream($id);
if(!isset($stream)) {
	die("stream not found in DB");
}
$stream->start();
set_time_limit(0);

// wait for the HTTP server to respond
while(!$stream->test_http()) {
	usleep(100*1000); // 0.1s
}

// start downstreaming (VLC transcode and HTTP serv)
$handle = fopen("http://localhost:" . $stream->get_dest_port() . "/", "rb");
// echo "<pre>"; print_r($http_response_header); echo "</pre>"; die;
if (FALSE === $handle) {
	exit("Echec lors de l'ouverture du flux");
}

// send headers, so that it can be viewed directly in the browser with a plugin
header('Content-type: audio/ogg'); //TODO choose right mime type based on stream specs
header('Cache-Control: no-cache');

// start upstreaming (PHP)
while (!feof($handle)) {
	$buffer = fread($handle, 8192);
	echo $buffer;
}
fclose($handle);
