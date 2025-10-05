<?php
require_once __DIR__.'/../../includes/ALL.inc.php';

session_start();
unset($_SESSION['admin']);
$_SESSION['messages'][] = 'successfully signed out';
session_write_close();
header("Location: ".$_SERVER['HTTP_REFERER']);
