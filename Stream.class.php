<?php

require_once 'Process.class.php';
require_once 'config.inc.php';

class Stream {
	
	private $id;
	private $actual_viewers;
	private $peak_viewers;
	private $total_viewers;
	private $original_url;
	private $acodec;
	private $ab;
	private $mux;
	private $dest_port;
	private $pid;
	
	private static $db;
	
	public static $sqlite_filename = 'data.sqlite';
	public static $table_name = 'streams';
	public static $pid_filename = 'webtransco.pid';
	
	
	public function get_actual_viewers() {
		return $this->actual_viewers;
	}
	
	public function add_viewer() {
		$this->actual_viewers ++;
		if($this->actual_viewers > $this->peak_viewers)
			$this->peak_viewers = $this->actual_viewers;
		$this->total_viewers ++;
		
	}
	
	public function remove_viewer() {
		$this->actual_viewers --;
	}
	
	public function get_peak_viewers() {
		return $this->peak_viewers;
	}
	
	public function get_total_viewers() {
		return $this->total_viewers;
	}
	
	public function get_original_url() {
		return $this->original_url;
	}
	
	public function get_acodec() {
		return $this->acodec;
	}
	public function get_ab() {
		return $this->ab;
	}
	public function get_mux() {
		return $this->mux;
	}
	public function get_dest_port() {
		return $this->dest_port;
	}
	public function get_pid() {
		return $this->pid;
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
			SELECT	count(*)
			FROM	sqlite_master
			WHERE	type='table'
			AND		name='" . Stream::$table_name . "'
			COLLATE	NOCASE
		";
		$stmt = Stream::$db->query($table_select);
// 		var_dump($stmt->fetchColumn()); die;
		if($stmt->fetchColumn() != 1) {
// 			echo nl2br("creating table" . PHP_EOL);
			$create_sql = "
				CREATE TABLE ".Stream::$table_name."(
					id INTEGER PRIMARY KEY,
					actual_viewers INTEGER NOT NULL DEFAULT 0,
					peak_viewers INTEGER NOT NULL DEFAULT 0,
					total_viewers INTEGER NOT NULL DEFAULT 0,
					original_url TEXT NOT NULL,
					acodec TEXT NOT NULL,
					ab INTEGER NOT NULL,
					mux TEXT NOT NULL,
					dest_port INTEGER NOT NULL,
					pid INTEGER NOT NULL
				)";
//	 		echo $create_sql; die;
			Stream::$db->exec($create_sql);
			
			// insert test data //TODO remove later
			$row = array(
				'id' => 1,
				'actual_viewers' => 0,
				'peak_viewers' => 0,
				'total_viewers' => 0,
				'original_url' => 'http://hd.stream.frequence3.net/frequence3.flac',
				'acodec' => 'vorb',
				'ab' => 256,
				'mux' => 'ogg',
				'dest_port' => 8000,
				'pid' => null
			);
			$s = new Stream();
			$s->fill_with_array($row);
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
	
	private static function affect_nullable_int($value, &$var) {
		if(isset($value))
			$var = (int)$value;
		else
			$var = null;
	}
	private static function affect_int($value, &$var) {
		if(isset($value))
			$var = (int)$value;
		else
			$var = 0;
	}
	private static function affect_str($value, &$var) {
		if(isset($value))
			$var = $value;
		else
			$var = '';
	}
	private function fill_with_array($a) {
		Stream::affect_nullable_int($a['id'], $this->id);
		Stream::affect_int($a['actual_viewers'], $this->actual_viewers);
		Stream::affect_int($a['peak_viewers'], $this->peak_viewers);
		Stream::affect_int($a['total_viewers'], $this->total_viewers);
		Stream::affect_str($a['original_url'], $this->original_url);
		Stream::affect_str($a['acodec'], $this->acodec);
		Stream::affect_int($a['ab'], $this->ab);
		Stream::affect_str($a['mux'], $this->mux);
		Stream::affect_int($a['dest_port'], $this->dest_port);
		Stream::affect_int($a['pid'], $this->pid);
	}
	
	
	public function __construct() {
		$this->id = null;
		$this->actual_viewers = 0;
	}
	
	
	public function save() {
		$id = isset($this->id) ? $this->id : 'NULL';
		$sql = "
			INSERT OR REPLACE INTO ".Stream::$table_name."
				( id, actual_viewers, peak_viewers, total_viewers, original_url, acodec, ab, mux, dest_port, pid )
			VALUES ( ".
				$id.", ".
				$this->actual_viewers.", ".
				$this->peak_viewers.", ".
				$this->total_viewers.", ".
				"'".$this->original_url."', ".
				"'".$this->acodec."', ".
				$this->ab.", ".
				"'".$this->mux."', ".
				$this->dest_port.", ".
				$this->pid
			." )";
// 		echo $sql; die;
		Stream::$db->exec($sql);
		if(!isset($this->id))
	 		$this->id = Stream::$db->lastInsertId();
	}
	
	
	public function start() {
		$sql = "BEGIN TRANSACTION";		
		Stream::$db->exec($sql);
		
		$stream = Stream::find_stream($this->id);
// 		var_dump($stream); die;
		if($stream->get_actual_viewers() === 0) { // only if needed
			$this->start_process();
		}
		
		$stream->add_viewer();
		$stream->save();
		
		$sql = "END TRANSACTION";
		Stream::$db->exec($sql);
	}
	
	
	public function stop() {
		$sql = "BEGIN TRANSACTION";
		Stream::$db->exec($sql);
	
		$stream = Stream::find_stream($this->id);
// 		var_dump($stream); die;
		if($stream->get_actual_viewers() === 1) { // only if needed
			$this->stop_process();
		}
	
		$stream->remove_viewer();
		$stream->save();
	
		$sql = "END TRANSACTION";
		Stream::$db->exec($sql);
	}
	
	
	private function start_process() {
		global $conf;
		if(! file_exists(Stream::$pid_filename)) {
			$command = $conf['vlc_executable'] . " -vvv " . $this->original_url . " --sout '#transcode{vcodec=none,acodec=" . $this->acodec . ",ab=" . $this->ab . "} :standard{access=http,mux=" . $this->mux . ",dst=:" . $this->dest_port . "/}'";
// 			echo "launching : $command"; die;
			$p = new Process($command);
	
			$fo = fopen(Stream::$pid_filename, 'w');
			fwrite($fo, $p->getPid());
			fclose($fo);
		}
		else {
			die("already running");
		}
	}
	
	
	private function stop_process() {
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

