<?php
require_once __DIR__.'/../includes/ALL.inc.php';

Admin::restrict();

if(!empty($_GET['id'])) {
	$id = $_GET['id'];
	if(ctype_digit($id) && $id > 0) {
		$id = (int)$id;
		// DB
		StreamMdl::prepare_db();
		$s = StreamSvc::find_stream($id);
		$s->remove();
		session_start();
		$_SESSION['messages'][] = 'successfully deleted stream #'.$id;
		session_write_close();
		header("Location: ".$_SERVER['HTTP_REFERER']);
	}
	else {
		die("id is not a positive integer");
	}
}
else {
	die("no stream id specified");
}
