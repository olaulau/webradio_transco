<?php
// fill in to your needs and rename this file 'config.inc.php


// location of the VLC executable
$conf['vlc_executable'] = '/usr/bin/cvlc';												// for a standart installation of the vlc-nox package
// $conf['vlc_executable'] = '/home/user'.'/vlc_build/vlc-2.1.6/vlc -I "dummy" "$@"';	// for a compiled version of VLC

// range of ports to use for VLC HTTP servers
$conf['min_dest_port'] = 8000;
$conf['max_dest_port'] = 8999;

// admin users, open admin/auth/passwd.php to create a password hash
$conf['admins'] = array(
		'admin' => '$2y$10$FXt/US/dtgFQQYrqXCEEn.qOrB9F8GoVDW/Ymy7PDmr91/TyP1IS6'
);
