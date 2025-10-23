<?php

function app_base_path() {
		// echo $_SERVER['PHP_SELF'] . "<br/>";
		// echo $_SERVER['SCRIPT_NAME'] . "<br/>";
		// echo $script_filename . "<br/>";
		// echo "<br/>";
		// echo __FILE__ . "<br/>";
		// echo __DIR__ . "<br/>";
		// echo "<br/>";
	$subdir_relative_from_project_root = "../";

	$project_root = realpath(__DIR__ . "/" . $subdir_relative_from_project_root);
	$script_filename = realpath($_SERVER['SCRIPT_FILENAME']);

	$project_relative_script = str_replace($project_root, '', $script_filename);
		// echo $script_filename . "<br/>";
		// echo " - <br/>";
		// echo $project_root . "<br/>";
		// echo " = <br/>";
		// echo $project_relative_script . "<br/><br/>";

	$app_base_path = str_replace($project_relative_script, '', $_SERVER['SCRIPT_NAME']);
		// echo $_SERVER['SCRIPT_NAME'] . "<br/>";
		// echo " - <br/>";
		// echo $project_relative_script . "<br/>";
		// echo " = <br/>";
		// echo $app_base_path . "<br/><br/>";
	return $app_base_path;
}


$app_base_path = app_base_path();
session_set_cookie_params(0, $app_base_path);


require_once __DIR__ . '/../admin/Admin.class.php';

require_once __DIR__ . '/../includes/config.inc.php';
require_once __DIR__ . '/../includes/StreamMdl.php';
require_once __DIR__ . '/../includes/StreamSvc.php';
require_once __DIR__ . '/../includes/VLC_capabilities.class.php';

require_once __DIR__ . '/../external/Process.class.php';
