<?php

// location of the VLC executable
$conf['vlc_executable'] = '/usr/bin/cvlc';									// for a standart installation of the vlc-nox package
// $conf['vlc_executable'] = '~/vlc_build/vlc-2.1.6/vlc -I "dummy" "$@"';	// for a compiled version of VLC

// range of ports to use for VLC HTTP servers
$conf['min_dest_port'] = 8000;
$conf['max_dest_port'] = 8999;
