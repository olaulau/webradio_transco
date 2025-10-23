<?php
require_once __DIR__ . '/../includes/ALL.inc.php';

// print_r($_POST); die;
if(!empty($_POST['name']) && !empty($_POST['original_url']) && !empty($_POST['acodec']) && !empty($_POST['ab']) && !empty($_POST['mux'])) {
	if(!ctype_digit($_POST['ab'])  ||  $_POST['ab'] < 32  ||  $_POST['ab'] > 320)
		die("incorrect audio bitrate");
	
	$tab = array();
	StreamMdl::prepare_db();
	if(!empty($_POST['id'])) {
		$id = $_POST['id'];
		if(ctype_digit($id) && $id > 0) {
			$id = (int)$id;
			$stream = StreamSvc::find_stream($id);
			if(!isset($stream)) {
				die("stream not found in DB");
			}
// 			echo "found stream : "; print_r($stream); die;
		}
		else {
			die("id is not a positive integer");
		}
	}
	else {
		$stream = new StreamSvc();
	}
	
	$tab['name'] = $_POST['name'];
	$tab['original_url'] = $_POST['original_url'];
	$tab['original_track_id'] = $_POST['original_track_id'];
	$tab['acodec'] = $_POST['acodec'];
	$tab['ab'] = $_POST['ab'];
	$tab['mux'] = $_POST['mux'];
		
	$stream->fill_with_array($tab);
	$stream->save();
	header('Location: ' . '../');
}
else {
	die("missing parameter");
}
