<?php

// register shutdown callback, to stop the stream server
function shutdown()
{
	exec('./stop.sh');
}
register_shutdown_function('shutdown');


// start the stream server
exec('./start.sh');

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
