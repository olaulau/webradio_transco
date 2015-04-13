<?php

require_once 'external/log4php/Logger.php';

class MyPDO extends PDO {
	
	private $log;
	
	public function __construct($dsn, $username=NULL , $password=NULL , $options=NULL ) {
		Logger::configure('log4php.xml');
		$this->log = Logger::getLogger(__CLASS__);
		
		$this->log->trace('creating PDO with DSN : ' . $dsn);
		parent::__construct( $dsn , $username , $password , $options );
	}
	
	public function setAttribute($attribute, $value ) {
		$this->log->trace('PDO setAttribute : ' . $attribute . ' = ' . $value);
		return parent::setAttribute($attribute, $value);
	}
	
	public function query($statement) {
		$this->log->trace('PDO query : statement = ' . $statement);
		return parent::query($statement);
	}
	
	public function exec($statement) {
		$this->log->trace('PDO exec : statement = ' . $statement);
		return parent::exec($statement);
	}
	
}


// $m = new MyPDO('sqlite:data.sqlite');
