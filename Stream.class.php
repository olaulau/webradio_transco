<?php

require_once 'Process.class.php';

class Stream {
	
	private $id;
	private $viewers;
	
	private static $db;
	
	public static $sqlite_filename = 'data.sqlite';
	public static $table_name = 'streams';
	public static $pid_filename = 'webtransco.pid';
	
	
	public function get_viewers() {
		return $this->viewers;
	}
	
	public function add_viewer() {
		$this->viewers ++;
	}
	
	public function remove_viewer() {
		$this->viewers --;
	}
	
	
	public static function prepare_db() {
		// check db object created
		if(!isset(Stream::$db)) {
// 			echo nl2br("connecting to sqlite db" . PHP_EOL);
			Stream::$db = new PDO('sqlite:' . Stream::$sqlite_filename);
			Stream::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
		
		// check db table created
		$table_select = "
			SELECT count(*)
			FROM sqlite_master
			WHERE type='table'
			AND name='" . Stream::$table_name . "'
			COLLATE NOCASE
		";
		$stmt = Stream::$db->query($table_select);
// 		var_dump($stmt->fetchColumn()); die;
		if($stmt->fetchColumn() != 1) {
// 			echo nl2br("creating table" . PHP_EOL);
			$create_sql = "
				CREATE TABLE ".Stream::$table_name."
				( id INTEGER PRIMARY KEY, viewers INTEGER NOT NULL DEFAULT 0 )";
//	 		echo $create_sql; die;
			Stream::$db->exec($create_sql);
			
			// insert test data //TODO remove later
			$s = new Stream();
			$s->save();
		}
	}
	
	
	public static function find_stream($id) {
		$select = "
			SELECT *
			FROM ".Stream::$table_name."
			WHERE id = ".$id."
		";
		$stmt = Stream::$db->query($select);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$row = $stmt->fetch();
// 		echo "<pre>", var_dump($row); echo "</pre>";
		$res = new Stream();
		$res->fill_with_array($row);
		return $res;
	}
	
	
	private function fill_with_array($a) {
		if(isset($a['id']))
			$this->id = (int)$a['id'];
		else
			$this->id = null;
		
		if(isset($a['viewers']))
			$this->viewers = (int)$a['viewers'];
		else
			$this->viewers = 0;
	}
	
	
	public function __construct() {
		$this->id = null;
		$this->viewers = 0;
	}
	
	
	public function save() {
		$id = isset($this->id) ? $this->id : 'NULL';
		$sql = "
			INSERT OR REPLACE INTO ".Stream::$table_name." ( id, viewers )
			VALUES ( ".$id.", ".$this->viewers." )
		";
// 		echo $sql; die;
		Stream::$db->exec($sql);
	 	$this->id = Stream::$db->lastInsertId();
	}
	
	
	public function start() {
		$sql = "BEGIN TRANSACTION";		
		Stream::$db->exec($sql);
		
		$stream = Stream::find_stream($this->id);
// 		var_dump($stream); die;
		if($stream->get_viewers() === 0) { // only if needed
			Stream::start_process();
		}
		
		$stream->viewers ++;
		$stream->save();
		
		$sql = "END TRANSACTION";
		Stream::$db->exec($sql);
	}
	
	
	public function stop() {
		$sql = "BEGIN TRANSACTION";
		Stream::$db->exec($sql);
	
		$stream = Stream::find_stream($this->id);
// 		var_dump($stream); die;
		if($stream->get_viewers() === 1) { // only if needed
			Stream::stop_process();
		}
	
		$stream->viewers --;
		$stream->save();
	
		$sql = "END TRANSACTION";
		Stream::$db->exec($sql);
	}
	
	
	private static function start_process() {
		if(! file_exists(Stream::$pid_filename)) {
			$command = "cvlc -vvv http://hd.stream.frequence3.net/frequence3.flac --sout '#transcode{vcodec=none,acodec=vorb,ab=256} :standard{access=http,mux=ogg,dst=:8000/}'";
			$p = new Process($command);
	
			$fo = fopen(Stream::$pid_filename, 'w');
			fwrite($fo, $p->getPid());
			fclose($fo);
		}
		else {
			die("already running");
		}
	}
	
	
	private static function stop_process() {
		if(file_exists(Stream::$pid_filename)) {
			$fo = fopen(Stream::$pid_filename, 'r');
			$pid = fgets($fo);
			fclose($fo);
	
			$p = new Process();
			$p->setPid($pid);
			$ret = $p->stop();
	
			unlink(Stream::$pid_filename);
		}
		else {
			die("not running");
		}
	}
	
}




// Stream::prepare_db();
// $s = new Stream();
// $s->save();
// $s->add_viewer();
// $s->save();

