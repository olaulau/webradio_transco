<?php

require_once 'Stream.class.php';

if(!empty($_GET['id'])) {
	$id = $_GET['id'];
	if(ctype_digit($id) && $id > 0) {
		$id = (int)$id;
		// DB
		Stream::prepare_db();
		
		
		// register shutdown callback :
		// handle client disconnection (or script end : end of stream)
		// to update counter and stop the server if useless
		function shutdown()
		{
			global $id;
			try { Stream::end_transaction(); } catch(Exception $ex) {}
			$stream = Stream::find_stream($id);
			if(isset($stream))
				$stream->stop();
		}
		register_shutdown_function('shutdown');
		
		
		// start the stream server
		$stream = Stream::find_stream($id);
		if(isset($stream)) {
			$stream->start();

			
			// wait for the HTTP server to respond
			while(!$stream->test_http()) {
				usleep(100*1000); // 0.1s
			}
			
			
			// send headers, so that it can be viewed directly in the browser with a plugin
			header('Content-type: application/octet-stream');
			header('Cache-Control: no-cache');
			
			
			// start streaming
			$handle = fopen("http://localhost:" . $stream->get_dest_port() . "/", "rb");
			// echo "<pre>"; print_r($http_response_header); echo "</pre>"; die;
			if (FALSE === $handle) {
				exit("Echec lors de l'ouverture du flux");
			}
			
			while (!feof($handle)) {
				$buffer = fread($handle, 8192);
				echo $buffer;
			}
			fclose($handle);
		}
		else {
			die("stream not found in DB");
		}
		
	}
	else {
		// id is not a positive integer
		die("id is not a positive integer");
	}
}
else {
	// no stream id specified
	die("no stream id specified");
}
