<?php
require_once __DIR__.'/../includes/ALL.inc.php';

Admin::restrict();

if(empty($_GET['id'])) {
	die("no stream id specified");
}

$id = $_GET['id'];
if(!ctype_digit($id) || $id <= 0) {
	die("id is not a positive integer");
}

$id = (int)$id;
// DB
Stream::prepare_db();
$s = Stream::find_stream($id);
$s->force_stop();
session_start();
$_SESSION['messages'][] = 'successfully stopped the stream #'.$id;
session_write_close();
header("Location: ".$_SERVER['HTTP_REFERER']);
