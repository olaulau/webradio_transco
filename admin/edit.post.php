<?php
session_start();

require_once __DIR__.'/../includes/config.inc.php';
require_once __DIR__.'/../includes/Stream.class.php';

// print_r($_POST); die;
if(!empty($_POST['id']) && !empty($_POST['name']) && !empty($_POST['original_url']) && !empty($_POST['acodec']) && !empty($_POST['ab']) && !empty($_POST['mux'])) {
	if(!ctype_digit($_POST['ab'])  ||  $_POST['ab'] < 32  ||  $_POST['ab'] > 320)
		die("incorrect audio bitrate");
	
	$id = $_POST['id'];
	if(ctype_digit($id) && $id > 0) {
		$id = (int)$id;
		Stream::prepare_db();
		$stream = Stream::find_stream($id);
		if(isset($stream)) {
			$tab = array();
			$tab['id'] = $_POST['id'];
			$tab['name'] = $_POST['name'];
			$tab['original_url'] = $_POST['original_url'];
			$tab['acodec'] = $_POST['acodec'];
			$tab['ab'] = $_POST['ab'];
			$tab['mux'] = $_POST['mux'];
			
			$stream->fill_with_array($tab);
			$stream->save();
			header('Location: ' . '../');
		}
		else {
			die("stream not found in DB");
		}
	}
	else {
		die("id is not a positive integer");
	}
}
else {
	die("missing parameter");
}
