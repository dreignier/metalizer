<?php
class MysqlDriver extends AbstractDatabaseDriver {
	private $host;
	private $port;
	private $login;
	private $password;
	private $name;
	private $link;
	
	public function __construct($name) {
		$key = "database.$name";
		
		$this->host = config("$key.host");
		$this->port = config("$key.port");
		$this->login = config("$key.login");
		$this->password = config("$key.password");
		$this->name = config("$key.name");
		$this->connect();
	}
	
	public function getLogName() {
		return "[$this->host:$this->name]";
	}
	
	public function connect() {
		$this->link = mysqli_connect($this->host, $this->login, $this->password, $this->name, $this->port);
		if (mysqli_connect_errno()) {
			$this->link = null;
			$errno = mysqli_connect_errno();
			$error = mysqli_connect_error();
			throw new MysqlException("Error during connection : ($errno) $error");		
		}
	}
	
	public function disconnect() {
		mysqli_close($this->link);
		$this->link = null;
	}
	
	public function onSleep() {
		$this->disconnect();
	}
	
	public function onWakeUp() {
		$this->connect();
	}
	
	public function query($query) {
		$this->logInfo($query);
		
		$result = mysqli_query($this->link, $query);
		
		if ($result === false && mysqli_stmt_errno()) {
			$errno = mysqli_stmt_errno();
			$error = mysqli_stmt_error();
			throw new MysqlException("Error during a query : ($errno) $error");
			return false;
		}
		
		if ($result === true) {
			return true;
		}
		
		return new Query($result);
	}
}