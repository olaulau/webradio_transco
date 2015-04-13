<?php

require_once 'Process.class.php';
require_once 'config.inc.php';
// require_once 'MyPDO.class.php';

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
	
	
	public function get_id() {
		return $this->id;
	}
	
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
			Stream::$db = new /*My*/PDO('sqlite:' . Stream::$sqlite_filename); //TODO MyPDO -> PDO
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
					dest_port INTEGER,
					pid INTEGER
				)";
//	 		echo $create_sql; die;
			Stream::$db->exec($create_sql);
			
			// insert test data //TODO remove later
			$row = array(
				'id' => 1,
				'actual_viewers' => 0,
				'peak_viewers' => 0,
				'total_viewers' => 0,
				'original_url' => 'http://hd.stream.frequence3.net/frequence3-256.mp3',
				'acodec' => 'vorb',
				'ab' => 192,
				'mux' => 'ogg',
				'dest_port' => NULL,
				'pid' => NULL
			);
			$s = new Stream();
			$s->fill_with_array($row);
			$s->save();
		}
	}
	
	
	public static function find_stream($id, $dest=null) {
		$sql = "
			SELECT	*
			FROM	".Stream::$table_name."
			WHERE	id = ".$id."
		";
// 		echo $sql; die;
		$stmt = Stream::$db->query($sql);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$row = $stmt->fetch();
// 		echo "<pre>", var_dump($row); echo "</pre>";
		if(isset($dest))
			$res = &$dest;
		else
			$res = new Stream();
		if($row === FALSE)
			$res = NULL;
		else
			$res->fill_with_array($row);
		return $res;
	}
	
	public function refresh() {
// 		echo "<pre>"; print_r($this); echo "</pre>"; die;
		Stream::find_stream($this->id, $this);
	}
	
	public static function get_all() {
		$select = "
			SELECT	*
			FROM	".Stream::$table_name."
		";
		$stmt = Stream::$db->query($select);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$res = array();
		foreach ($stmt as $row) {
			$s = new Stream();
			$s->fill_with_array($row);
			$res[] = $s;
		}
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
		Stream::affect_nullable_int($a['id'], $this->id); // null in case of creation
		Stream::affect_int($a['actual_viewers'], $this->actual_viewers);
		Stream::affect_int($a['peak_viewers'], $this->peak_viewers);
		Stream::affect_int($a['total_viewers'], $this->total_viewers);
		Stream::affect_str($a['original_url'], $this->original_url);
		Stream::affect_str($a['acodec'], $this->acodec);
		Stream::affect_int($a['ab'], $this->ab);
		Stream::affect_str($a['mux'], $this->mux);
		Stream::affect_nullable_int($a['dest_port'], $this->dest_port);
		Stream::affect_nullable_int($a['pid'], $this->pid);
	}
	
	
	public function __construct() {
		$this->id = null;
		$this->actual_viewers = 0;
	}
	
	private static function sql_nullable($val) {
		return isset($val) ? $val : 'NULL';
	}
	public function save() {
		$id = Stream::sql_nullable($this->id);
		$dest_port = Stream::sql_nullable($this->dest_port);
		$pid = Stream::sql_nullable($this->pid);
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
				$dest_port.", ".
				$pid
			." )";
// 		echo "<pre>"; echo $sql; echo "</pre>"; die
		
		Stream::$db->exec($sql);
		if(!isset($this->id))
	 		$this->id = Stream::$db->lastInsertId();
	}
	
	public static function begin_transaction() {
		$sql = "BEGIN TRANSACTION";
		Stream::$db->exec($sql);
	}
	public static function end_transaction() {
		$sql = "END TRANSACTION";
		Stream::$db->exec($sql);
	}
	
	public function start() {
		Stream::begin_transaction();
		$this->refresh();
// 		var_dump($this); die;
		if($this->get_actual_viewers() === 0) { // only if needed
			$this->start_process();
		}
		$this->add_viewer();
		$this->save();
		Stream::end_transaction();
	}
	
	
	public function stop() {
		Stream::begin_transaction();
		$this->refresh();
// 		var_dump($this); die;
		if($this->get_actual_viewers() === 1) { // only if needed
			$this->stop_process();
		}
		$this->remove_viewer();
		$this->save();
		Stream::end_transaction();
	}
	
	
	private static function dest_port() {
		global $conf;
		$select = "
			SELECT COUNT(*) AS nb
			FROM ".Stream::$table_name."
			WHERE dest_port IS NOT NULL
		";
		$stmt = Stream::$db->query($select);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$row = $stmt->fetch();
// 		echo "NB=".$row['nb']; die;
		if($row['nb'] > 0) {
			$select = "
				SELECT max(dest_port) AS dest_port
				FROM ".Stream::$table_name."
			";
			$stmt = Stream::$db->query($select);
			$stmt->setFetchMode(PDO::FETCH_ASSOC);
			$row = $stmt->fetch();
			$dest_port = (int)$row['dest_port'] + 1;
			if($dest_port <= $conf['max_dest_port'])
				return $dest_port;
			else
				die("all port have been allocated.");
		}
		else {
			return $conf['min_dest_port'];
		}
		
		
		
	} 
	private function start_process() {
		global $conf;
		if(!isset($this->pid)) {
			$dest_port = Stream::dest_port();
// 			echo $dest_port; die;
			$command = $conf['vlc_executable'] . " -vvv " . $this->original_url . " --sout '#transcode{vcodec=none,acodec=" . $this->acodec . ",ab=" . $this->ab . "} :standard{access=http,mux=" . $this->mux . ",dst=:" . $dest_port . "/}'";
// 			echo "launching : $command"; die;
			$p = new Process($command);
			$this->pid = $p->getPid();
			$this->dest_port = $dest_port;
			$this->save();
		}
		else {
			die("already running");
		}
	}
	
	
	private function stop_process() {
		if(isset($this->pid)) {
			$p = new Process();
			$p->setPid($this->get_pid());
			$ret = $p->stop();
			$this->dest_port = null;
			$this->pid = null;
			$this->save();
		}
		else {
			die("not running");
		}
	}
	
	
	public function test_http() {
		$connection = @fsockopen("localhost", $this->dest_port);
		return is_resource($connection);
	}
	
}




// Stream::prepare_db();
// $s = new Stream();
// $s->save();
// $s->add_viewer();
// $s->save();

