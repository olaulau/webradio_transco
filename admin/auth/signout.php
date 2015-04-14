<?php
session_start();

unset($_SESSION['admin']);
header("Location: ".$_SERVER['HTTP_REFERER']);
