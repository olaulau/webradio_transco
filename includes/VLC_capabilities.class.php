<?php
// https://wiki.videolan.org/Codec/

class VLC_capabilities
{
	
	public static $acodecs = [
		'mpga' 	=> 'MPEG audio',
		'mp3' 	=> 'MPEG Layer 3 audio',
		'mp4a'	=> 'AAC (Advanced Audio Coding)',
		'a52' 	=> 'Dolby Digital (A52 or AC3)',
		'vorb' 	=> 'Vorbis',
		'spx' 	=> 'Speex',
		'flac'  => 'FLAC',
		
	    'dts'	=> 'DTS',
// 	    Windows Media Audio
// 	    DV Audio
// 	    LPCM
// 	    ADPCM
	    'samr'	=> 'AMR',
// 	    QuickTime Audio
// 	    RealAudio
// 	    MACE
	    'mpc'	=> 'MusePack', 
];
	
	
	public static $muxers = [
		'mpeg1' => 'MPEG-1 multiplexing',
		'ts' 	=> 'MPEG Transport Stream',
		'ps' 	=> 'MPEG Program Stream',
		'mp4' 	=> 'MPEG-4 mux format',
		'avi' 	=> 'AVI',
		'asf' 	=> 'ASF',
		'dummy' => 'dummy output',
		'ogg' 	=> 'Xiph.org\'s ogg container format',
];
	
}


// print_r(VLC_capabilities::$acodecs);
// print_r(VLC_capabilities::$muxers);
