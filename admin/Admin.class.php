<?php

class Admin {
	
	public static function is_admin() {
		//var_dump($_SESSION); die;
		return (isset($_SESSION['admin']) && $_SESSION['admin'] === TRUE);
	}
	
	public static function restrict() {
		if(!Admin::is_admin()) {
			header('Location: '.'auth/signin.php');
		}
	}
}
