<?php
require_once __DIR__.'/../../includes/ALL.inc.php';

unset($_SESSION['admin']);
$_SESSION['messages'][] = 'successfully signed out';
header("Location: ".$_SERVER['HTTP_REFERER']);
