<?php
require_once __DIR__.'/../includes/ALL.inc.php';

Admin::restrict();

if(!empty($_GET['id'])) {
	$id = $_GET['id'];
	if(ctype_digit($id) && $id > 0) {
		$id = (int)$id;
		// DB
		Stream::prepare_db();
		$s = Stream::find_stream($id);
		$s->force_stop();
		$_SESSION['messages'][] = 'successfully stopped the stream #'.$id;
		header("Location: ".$_SERVER['HTTP_REFERER']);
	}
	else {
		die("id is not a positive integer");
	}
}
else {
	die("no stream id specified");
}
