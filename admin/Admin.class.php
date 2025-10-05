<?php

class Admin {
	
	public static function is_admin() {
		session_start();
		$admin = $_SESSION['admin'] ?? null;
		session_write_close();
		return (isset($admin) && $admin === TRUE);
	}
	
	public static function restrict() {
		if(!Admin::is_admin()) {
			header('Location: '.'auth/signin.php');
		}
	}
}
