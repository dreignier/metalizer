<?php
/*
 Metalizer, a MVC php Framework.
 Copyright (C) 2012 David Reignier

 This program is free software: you can redistribute it and/or modify
 it under the terms of the GNU General Public License as published by
 the Free Software Foundation, either version 3 of the License, or
 (at your option) any later version.

 This program is distributed in the hope that it will be useful,
 but WITHOUT ANY WARRANTY; without even the implied warranty of
 MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 GNU General Public License for more details.

 You should have received a copy of the GNU General Public License
 along with this program.  If not, see <http://www.gnu.org/licenses/>
 */

/**
 * Handle the database and queries.
 * A database retrieve its configuration from its name. All keys are "database.$name.$key". So a database need this keys in the configuration :
 *  database.$name.host
 *  database.$name.port
 *  database.$name.login
 *  database.$name.password
 *  database.$name.name
 * @author David Reignier
 *
 */
class Database extends MetalizerObject {

	/**
	 * The host
	 * @var string
	 */
	private $host;

	/**
	 * The port
	 * @var string
	 */
	private $port;

	/**
	 * The username
	 * @var string
	 */
	private $login;

	/**
	 * The password
	 * @var string
	 */
	private $password;

	/**
	 * The name of the database
	 * @var string
	 */
	private $name;

	/**
	 * The link to the database. null if the Database is not connected
	 * @var mysqli_link
	 */
	private $link;

	/**
	 * Create a new Database
	 * @param $name string
	 * 	The name of the Database
	 * @return Database
	 */
	public function __construct($name) {
		$key = "database.$name";

		$this->host = config("$key.host");
		$this->port = config("$key.port");
		$this->login = config("$key.login");
		$this->password = config("$key.password");
		$this->name = config("$key.name");
		$this->connect();
	}

	/**
	 * Override the default getLogName.
	 * @return string
	 * 	[host:name]
	 * @see MetalizerObject#getLogName()
	 */
	public function getLogName() {
		return "[$this->host:$this->name]";
	}

	/**
	 * Connect the database.
	 */
	public function connect() {
		$this->link = mysqli_connect($this->host, $this->login, $this->password, $this->name, $this->port);
		if (mysqli_connect_errno()) {
			$this->link = null;
			$errno = mysqli_connect_errno();
			$error = mysqli_connect_error();
			throw new MysqlException("Error during connection : ($errno) $error");
		}
	}

	/**
	 * Disconnect the database
	 */
	public function disconnect() {
		mysqli_close($this->link);
		$this->link = null;
	}

	/**
	 * On sleep, the database disconnect itself.
	 * @see MetalizerObject#onSleep()
	 */
	public function onSleep() {
		$this->disconnect();
	}

	/**
	 * On wake up, the database connect itself.
	 * @see MetalizerObject#onWakeUp()
	 */
	public function onWakeUp() {
		$this->connect();
	}

	/**
	 * Do a query on the database.
	 * @param $query string
	 * 	The mysql query.
	 * @return mixed
	 * 	A QueryResult or a boolean. As specify by the mysqli_query function.
	 * @throws MysqlException
	 *  If the query result if false (an error).
	 */
	public function query($query) {
		$this->logInfo($query);

		$result = mysqli_query($this->link, $query);

		if ($result === false && mysqli_errno($this->link)) {
			throw new MysqlException(mysqli_errno($this->link), mysqli_error($this->link));
		}

		if ($result === true) {
			return true;
		}

		return new QueryResult($result);
	}
	
	/**
	 * Escape a value
	 * @param $value string
	 * 	A value.
	 * @return string
	 *  The value escaped for the Database.
	 */
	public function escape($value) {
		return mysqli_real_escape_string($this->link, $value);
	}
}