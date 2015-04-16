<?php

require_once __DIR__.'/../external/Process.class.php';
require_once __DIR__.'/config.inc.php';
// require_once __DIR__.'/MyPDO.class.php';

class Stream {
	
	/// attributes ///
	private $id = null;
	private $name;
	private $actual_viewers = 0;
	private $peak_viewers = 0;
	private $total_viewers = 0;
	private $original_url;
	private $acodec;
	private $ab;
	private $mux;
	private $dest_port;
	private $pid;
	
	private static $db;
	
	/// constants ///
	public static function sqlite_filename() { return __DIR__.'/../data.sqlite'; }
	public static $table_name = 'streams';
	
	
	/// constructor ///
	public function __construct() {
		
	}
	
	/// getters and setters ///
	public function get_id() {
		return $this->id;
	}
	
	public function get_name() {
		return $this->name;
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
	
	
	/// static functions ///
	public static function prepare_db() {
		// check db object created
		if(!isset(Stream::$db)) {
// 			echo nl2br("connecting to sqlite db" . PHP_EOL);
			Stream::$db = new /*My*/PDO('sqlite:' . Stream::sqlite_filename()); //TODO MyPDO -> PDO ?
			Stream::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}
	
	public static function create_structure() {
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
					name TEXT NOT NULL,
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
// 	 		echo $create_sql; die;
			Stream::$db->exec($create_sql);
			$_SESSION['messages'][] = 'created data structure';
				
			Stream::insert_test_data(); //TODO remove later
			$_SESSION['messages'][] = 'inserted test data';
		}
	}
	
	public static function insert_test_data() {
		// insert test data
		$row = array(
			'id' => NULL,
			'name' => 'fréquence 3',
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
	
	
	private static function sql_nullable($val) {
		return isset($val) ? $val : 'NULL';
	}
	
	
	public static function begin_transaction() {
		$sql = "BEGIN TRANSACTION";
		Stream::$db->exec($sql);
	}
	public static function end_transaction() {
		$sql = "END TRANSACTION";
		Stream::$db->exec($sql);
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
	
	
	/// methods ///
	public function refresh() {
// 		echo "<pre>"; print_r($this); echo "</pre>"; die;
		Stream::find_stream($this->id, $this);
	}
	
	
	public  function fill_with_array($a) {
		if(isset($a['id']))
			Stream::affect_nullable_int((isset($a['id'])?$a['id']:NULL), $this->id); // null in case of creation
		if(isset($a['name']))
			Stream::affect_str((isset($a['name'])?$a['name']:NULL), $this->name);
		if(isset($a['actual_viewers']))
			Stream::affect_int((isset($a['actual_viewers'])?$a['actual_viewers']:NULL), $this->actual_viewers);
		if(isset($a['peak_viewers']))
			Stream::affect_int((isset($a['peak_viewers'])?$a['peak_viewers']:NULL), $this->peak_viewers);
		if(isset($a['total_viewers']))
			Stream::affect_int((isset($a['total_viewers'])?$a['total_viewers']:NULL), $this->total_viewers);
		if(isset($a['original_url']))
			Stream::affect_str((isset($a['original_url'])?$a['original_url']:NULL), $this->original_url);
		if(isset($a['acodec']))
			Stream::affect_str((isset($a['acodec'])?$a['acodec']:NULL), $this->acodec);
		if(isset($a['ab']))
			Stream::affect_int((isset($a['ab'])?$a['ab']:NULL), $this->ab);
		if(isset($a['mux']))
			Stream::affect_str((isset($a['mux'])?$a['mux']:NULL), $this->mux);
		if(isset($a['dest_port']))
			Stream::affect_nullable_int((isset($a['dest_port'])?$a['dest_port']:NULL), $this->dest_port);
		if(isset($a['pid']))
			Stream::affect_nullable_int((isset($a['pid'])?$a['pid']:NULL), $this->pid);
	}
	
	
	public function save() {
		$id = Stream::sql_nullable($this->id);
		$dest_port = Stream::sql_nullable($this->dest_port);
		$pid = Stream::sql_nullable($this->pid);
		$sql = "
			INSERT OR REPLACE INTO ".Stream::$table_name."
				( id, name, actual_viewers, peak_viewers, total_viewers, original_url, acodec, ab, mux, dest_port, pid )
			VALUES ( ".
				$id.", ".
				"'".$this->name."', ".
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
// 		echo "<pre>"; echo $sql; echo "</pre>"; die;
		
		Stream::$db->exec($sql);
		if(!isset($this->id))
	 		$this->id = Stream::$db->lastInsertId();
	}
	
	
	public function remove() {
		$sql = "
			DELETE FROM ".Stream::$table_name."
			WHERE	id = " . $this->id;
// 		echo "<pre>"; echo $sql; echo "</pre>"; die
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



/// test ///
// Stream::prepare_db();
// $s = new Stream();
// $s->save();
// $s->add_viewer();
// $s->save();

