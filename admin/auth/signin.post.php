<?php
session_start();

require_once __DIR__.'/../../includes/config.inc.php';
// print_r($_POST); die;
if(!empty($_POST['login']) && !empty($_POST['password'])) {
// 	var_dump(password_verify($_POST['password'], $conf['admins'][$_POST['login']])); die;
	if( isset($conf['admins'])  &&  password_verify($_POST['password'], $conf['admins'][$_POST['login']]) ) {
		$_SESSION['admin'] = TRUE;
		header("Location: ".$_POST['redirect']);
	}
	else {
		sleep(3);
		die("bad login/password provided");
	}
}
else {
	die("missing login / password parameters");
}