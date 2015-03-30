<?php

class Stream {
	
	private $id;
	private $viewers;
	
	private static $db;
	
	public static $sqlite_filename = 'data.sqlite';
	public static $table_name = 'streams';
	
	
	public function get_viewers() {
		return $this->viewers;
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
			
			// insert test data
			$insert_sql = "
				INSERT INTO ".Stream::$table_name." ( viewers )
				VALUES ( 0 )";
//	 		echo $insert_sql; die;
			Stream::$db->exec($insert_sql);
//	 		$id = Stream::$db->lastInsertId();
		}
	}
	
	
	public static function get_the_stream($id) {
		$select = "
			SELECT *
			FROM ".Stream::$table_name."
			WHERE id = ".$id."
		";
		$stmt = Stream::$db->query($select);
		$stmt->setFetchMode(PDO::FETCH_ASSOC);
		$row = $stmt->fetch();
// 		echo "<pre>", var_dump($row); echo "</pre>";
		$res = new Stream($row['id'], $row['viewers']);
		return $res;
	}
	
	public function __construct($id, $viewers) {
		$this->id = (int)$id;
		$this->viewers = (int)$viewers;
	}
	
	
	public static function start($id) {
		$sql = "BEGIN TRANSACTION";		
		Stream::$db->exec($sql);
		
		$stream = Stream::get_the_stream($id);
// 		var_dump($stream); die;
		if($stream->get_viewers() === 0) { // only if needed
			StartStop::start();
		}
		
		$update_sql = "
			UPDATE ".Stream::$table_name."
			SET viewers = " . ($stream->get_viewers() + 1) . "
			WHERE id = ".$id."
		";
// 	 	echo $update_sql; die;
		Stream::$db->exec($update_sql);
		
		$sql = "END TRANSACTION";
		Stream::$db->exec($sql);
	}
	
	
	public static function stop($id) {
		$sql = "BEGIN TRANSACTION";
		Stream::$db->exec($sql);
	
		$stream = Stream::get_the_stream($id);
// 		var_dump($stream); die;
		if($stream->get_viewers() === 1) { // only if needed
			StartStop::stop();
		}
	
		$update_sql = "
			UPDATE ".Stream::$table_name."
			SET viewers = " . ($stream->get_viewers() - 1) . "
			WHERE id = ".$id."
		";
//	 	echo $update_sql; die;
		Stream::$db->exec($update_sql);
	
		$sql = "END TRANSACTION";
		Stream::$db->exec($sql);
	}
	
}
