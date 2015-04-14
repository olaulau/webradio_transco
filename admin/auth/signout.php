<?php
session_start();

unset($_SESSION['admin']);
$_SESSION['messages'][] = 'successfully signed out';
header("Location: ".$_SERVER['HTTP_REFERER']);
