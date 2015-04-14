<?php

class Admin {
	
	public static function is_admin() {
		return (isset($_SESSION['admin']) && $_SESSION['admin'] === TRUE);
	}
	
	public static function restrict() {
		if(!Admin::is_admin()) {
			header('Location: '.$_SERVER['HTTP_REFERER']);
		}
	}
}