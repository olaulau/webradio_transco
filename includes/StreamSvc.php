<?php
require_once __DIR__ . '/../includes/ALL.inc.php';

class StreamSvc extends StreamMdl
{
	
	private static function port_available (int $port) : bool
	{
		$cmd = "netstat -tulpn 2>/dev/null | tail -n +3 | grep :$port";
		$output = `$cmd`;
		return empty($output);
	}
	

	public function start()
	{
		self::begin_transaction();
		$this->refresh();
		if($this->get_actual_viewers() <= 0) { // only if needed
			$this->start_process();
		}
		$this->add_viewer();
		$this->save();
		self::end_transaction();
	}
	
	
	public function stop()
	{
		self::begin_transaction();
		$this->refresh();
		if($this->get_actual_viewers() === 1) { // only if needed
			$this->stop_process();
		}
		$this->remove_viewer();
		$this->save();
		self::end_transaction();
	}
	
 
	private function start_process()
	{
		global $conf;
		if(!isset($this->pid)) {
			$dest_port = StreamMdl::next_dest_port();
			while(!self::port_available($dest_port)) {
				$dest_port ++;
			}
			$this->dest_port = $dest_port;

			$command = "
				{$conf['vlc_executable']} -vvv '{$this->original_url}' \
				 --no-video --sout-all --sout-keep \
				 --sout '#transcode{vcodec=none,acodec={$this->acodec},ab={$this->ab}}:duplicate{dst=std{access=http,mux={$this->mux},dst=:{$this->dest_port}/},select='es={$this->original_track_id}'}' \
			";
			// var_dump($command); die;
			$p = new Process($command);
			$status = $p->status();
			$this->pid = $p->getPid();
			$this->save();
		}
		else {
			die("already running");
		}
	}


	private function stop_process()
	{
		if (empty($this->pid)) {
			die("not running");
		}

		$p = new Process();
		$p->setPid($this->get_pid());
		$ret = $p->stop();
		// var_dump($ret); die;
		$this->dest_port = null;
		$this->pid = null;
		$this->save();
	}
	
	
	public function force_stop()
	{
		self::begin_transaction();
		$this->refresh();
		// var_dump($this); die;
		$this->actual_viewers = 0;
		$this->save();
		self::end_transaction();
		$this->stop_process();
	}
	
	
	public function test_http()
	{
		$connection = @fsockopen("localhost", $this->dest_port);
		return is_resource($connection);
	}
	
}
