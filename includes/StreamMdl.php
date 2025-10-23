<?php
require_once __DIR__ . '/../includes/ALL.inc.php';

class StreamMdl
{
	
	/// attributes ///
	protected $id = null;
	protected $name;
	protected $actual_viewers = 0;
	protected $peak_viewers = 0;
	protected $total_viewers = 0;
	protected $original_url;
	protected $original_track_id;
	protected $acodec;
	protected $ab;
	protected $mux;
	protected $dest_port;
	protected $pid;
	
	private static $db;
	
	/// constants ///
	public static function sqlite_filename()
	{
		return __DIR__.'/../data.sqlite';
	}
	public static $table_name = 'streams';
	
	
	/// constructor ///
	public function __construct()
	{
		
	}
	
	/// getters and setters ///
	public function get_id()
	{
		return $this->id;
	}
	
	public function get_name()
	{
		return $this->name;
	}
	
	public function get_actual_viewers()
	{
		return $this->actual_viewers;
	}
	public function add_viewer() {
		$this->actual_viewers ++;
		if($this->actual_viewers > $this->peak_viewers)
			$this->peak_viewers = $this->actual_viewers;
		$this->total_viewers ++;
		
	}
	public function remove_viewer()
	{
		$this->actual_viewers --;
		if($this->actual_viewers < 0)
			$this->actual_viewers = 0;
	}
	
	public function get_peak_viewers()
	{
		return $this->peak_viewers;
	}
	public function get_total_viewers()
	{
		return $this->total_viewers;
	}
	
	public function get_original_url()
	{
		return $this->original_url;
	}

	public function get_original_track_id()
	{
		return $this->original_track_id;
	}

	public function get_acodec()
	{
		return $this->acodec;
	}
	public function get_ab()
	{
		return $this->ab;
	}
	public function get_mux()
	{
		return $this->mux;
	}
	public function get_dest_port()
	{
		return $this->dest_port;
	}
	public function get_pid()
	{
		return $this->pid;
	}
	
	
	/// static functions ///
	public static function prepare_db()
	{
		// check db object created
		if(!isset(self::$db)) {
// 			echo nl2br("connecting to sqlite db" . PHP_EOL);
			self::$db = new PDO('sqlite:' . self::sqlite_filename());
			self::$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
		}
	}
	

	public static function create_structure()
	{
		// check db table created
		$table_select = "
			SELECT	count(*)
			FROM	sqlite_master
			WHERE	type='table'
			AND		name='" . self::$table_name . "'
			COLLATE	NOCASE
		";
		$stmt = self::$db->query($table_select);
// 		var_dump($stmt->fetchColumn()); die;

		if($stmt->fetchColumn() != 1) {
// 			echo nl2br("creating table" . PHP_EOL);
			$create_sql = "
				CREATE TABLE ".self::$table_name."(
					id INTEGER PRIMARY KEY,
					name TEXT NOT NULL,
					actual_viewers INTEGER NOT NULL DEFAULT 0,
					peak_viewers INTEGER NOT NULL DEFAULT 0,
					total_viewers INTEGER NOT NULL DEFAULT 0,
					original_url TEXT NOT NULL,
					original_track_id INTEGER,
					acodec TEXT,
					ab INTEGER,
					mux TEXT NOT NULL,
					dest_port INTEGER,
					pid INTEGER
				)";
// 	 		echo $create_sql; die;
			self::$db->exec($create_sql);
			session_start();
			$_SESSION['messages'][] = 'created data structure';
			
			self::insert_test_data(); //TODO remove later
			$_SESSION['messages'][] = 'inserted test data';
			session_write_close();
		}
	}
	

	public static function insert_test_data()
	{
		// insert test data
		$row = [
			'id' => NULL,
			'name' => 'fréquence 3',
			'actual_viewers' => 0,
			'peak_viewers' => 0,
			'total_viewers' => 0,
			'original_url' => 'https://frequence3.net-radio.fr/frequence3.flac',
			'original_track_id'	=>	null,
			'acodec' => 'vorb',
			'ab' => 256,
			'mux' => 'ogg',
			'dest_port' => NULL,
			'pid' => NULL
		];
		$s = new Stream();
		$s->fill_with_array($row);
		$s->save();

		$row = [
			'id' => NULL,
			'name' => 'skyrock',
			'actual_viewers' => 0,
			'peak_viewers' => 0,
			'total_viewers' => 0,
			'original_url' => 'rtsp://mafreebox.freebox.fr/fbxtv_pub/stream?namespace=1&service=100011',
			'original_track_id'	=>	1004,
			'acodec' => 'vorb',
			'ab' => 128,
			'mux' => 'ogg',
			'dest_port' => NULL,
			'pid' => NULL
		];
		$s = new Stream();
		$s->fill_with_array($row);
		$s->save();
	}
	
	
	public static function find_stream($id, $dest=null) : ?static
	{
		$sql = "
			SELECT	*
			FROM	".self::$table_name."
			WHERE	id = ".$id."
		";
// 		echo $sql; die;
		$stmt = self::$db->query($sql);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$row = $stmt->fetch();
// 		echo "<pre>", var_dump($row); echo "</pre>";

		if(isset($dest))
			$res = &$dest;
		else
			$res = new static();

		if($row === FALSE)
			$res = NULL;
		else
			$res->fill_with_array($row);

		return $res;
	}
	
	
	public static function get_all()
	{
		$select = "
			SELECT	*
			FROM	".self::$table_name."
		";
		$stmt = self::$db->query($select);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$res = array();
		foreach ($stmt as $row) {
			$s = new self();
			$s->fill_with_array($row);
			$res[] = $s;
		}
		return $res;
	}
	
	private static function affect_nullable_int($value, &$var)
	{
		if(isset($value))
			$var = (int)$value;
		else
			$var = null;
	}
	private static function affect_int($value, &$var)
	{
		if(isset($value))
			$var = (int)$value;
		else
			$var = 0;
	}
	private static function affect_str($value, &$var)
	{
		if(isset($value))
			$var = $value;
		else
			$var = '';
	}
	
	
	private static function sql_nullable($val)
	{
		return isset($val) ? $val : 'NULL';
	}
	
	
	public static function begin_transaction()
	{
		$sql = "BEGIN TRANSACTION";
		self::$db->exec($sql);
	}
	public static function end_transaction()
	{
		$sql = "END TRANSACTION";
		self::$db->exec($sql);
	}
	
	
	protected static function next_dest_port()
	{
		global $conf;
		$select = "
			SELECT COUNT(*) AS nb
			FROM ".self::$table_name."
			WHERE dest_port IS NOT NULL
		";
		$stmt = self::$db->query($select);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$row = $stmt->fetch();
		// 		echo "NB=".$row['nb']; die;
		if($row['nb'] > 0) {
			$select = "
				SELECT max(dest_port) AS dest_port
				FROM ".self::$table_name."
			";
			$stmt = self::$db->query($select);
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
	public function refresh()
	{
// 		echo "<pre>"; print_r($this); echo "</pre>"; die;
		self::find_stream($this->id, $this);
	}
	
	
	public function fill_with_array($a)
	{
		if(isset($a['id']))
			self::affect_nullable_int((isset($a['id'])?$a['id']:NULL), $this->id); // null in case of creation
		if(isset($a['name']))
			self::affect_str((isset($a['name'])?$a['name']:NULL), $this->name);
		if(isset($a['actual_viewers']))
			self::affect_int((isset($a['actual_viewers'])?$a['actual_viewers']:NULL), $this->actual_viewers);
		if(isset($a['peak_viewers']))
			self::affect_int((isset($a['peak_viewers'])?$a['peak_viewers']:NULL), $this->peak_viewers);
		if(isset($a['total_viewers']))
			self::affect_int((isset($a['total_viewers'])?$a['total_viewers']:NULL), $this->total_viewers);
		if(isset($a['original_url']))
			self::affect_str((isset($a['original_url'])?$a['original_url']:NULL), $this->original_url);
		if(isset($a['original_track_id']))
			self::affect_int((isset($a['original_track_id'])?$a['original_track_id']:NULL), $this->original_track_id);
		if(isset($a['acodec']))
			self::affect_str((isset($a['acodec'])?$a['acodec']:NULL), $this->acodec);
		if(isset($a['ab']))
			self::affect_int((isset($a['ab'])?$a['ab']:NULL), $this->ab);
		if(isset($a['mux']))
			self::affect_str((isset($a['mux'])?$a['mux']:NULL), $this->mux);
		if(isset($a['dest_port']))
			self::affect_nullable_int((isset($a['dest_port'])?$a['dest_port']:NULL), $this->dest_port);
		if(isset($a['pid']))
			self::affect_nullable_int((isset($a['pid'])?$a['pid']:NULL), $this->pid);
	}
	
	
	public function save()
	{
		$id = self::sql_nullable($this->id);
		$dest_port = self::sql_nullable($this->dest_port);
		$pid = self::sql_nullable($this->pid);
		$sql = "
			INSERT OR REPLACE INTO ".self::$table_name."
				( id, name, actual_viewers, peak_viewers, total_viewers, original_url, original_track_id, acodec, ab, mux, dest_port, pid )
			VALUES ( ".
				$id.", ".
				"'".$this->name."', ".
				$this->actual_viewers.", ".
				$this->peak_viewers.", ".
				$this->total_viewers.", ".
				"'".$this->original_url."', ".
				self::sql_nullable($this->original_track_id).", ".
				"'".$this->acodec."', ".
				$this->ab.", ".
				"'".$this->mux."', ".
				$dest_port.", ".
				$pid
			." )";
// 		echo "<pre>"; echo $sql; echo "</pre>"; die;
		
		self::$db->exec($sql);
		if(!isset($this->id))
	 		$this->id = self::$db->lastInsertId();
	}
	
	
	public function remove()
	{
		$sql = "
			DELETE FROM ".self::$table_name."
			WHERE	id = " . $this->id;
// 		echo "<pre>"; echo $sql; echo "</pre>"; die
		self::$db->exec($sql);
	}
	
	
	
	
}
