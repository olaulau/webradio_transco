<?php

require_once 'Stream.class.php';


// DB
Stream::prepare_db();


$streams = Stream::get_all();
foreach ($streams as $s) {
	echo '<a href="' . 'stream.php?id=' . $s->get_id() . '"> ' . $s->get_original_url() . ' [' . $s->get_mux() . ' ' . $s->get_acodec() . ' ' . $s->get_ab() . 'kbps] </a> <br/>';
}