<?php
session_start();

require_once __DIR__.'/../includes/config.inc.php';
require_once __DIR__.'/../includes/Stream.class.php';

// print_r($_POST); die;
if(!empty($_POST['original_url']) && !empty($_POST['acodec']) && !empty($_POST['ab']) && !empty($_POST['mux'])) {
	if(!ctype_digit($_POST['ab'])  ||  $_POST['ab'] < 32  ||  $_POST['ab'] > 320)
		die("incorrect audio bitrate");
	
	$tab = array();
	$tab['original_url'] = $_POST['original_url'];
	$tab['acodec'] = $_POST['acodec'];
	$tab['ab'] = $_POST['ab'];
	$tab['mux'] = $_POST['mux'];
	
	Stream::prepare_db();
	$s = new Stream();
	$s->fill_with_array($tab);
	$s->save();
	header('Location: ' . '../');
}
else {
	die("missing parameter");
}
