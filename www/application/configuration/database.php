<?php
/**
 * The database connection string. Just uncomment the one you want.
 */
// MySQL
$config['database.connection_string'] = 'mysql:host=127.0.0.1;dbname=metalizer';

// PostgreSQL
//$config['database.connection_string'] = 'pgsql:host=localhost;dbname=metalizer';

// SQLite
//$config['database.connection_string'] = 'sqlite:/sqlite/dbfile.txt';

// CUBRID
//$config['database.connection_string'] = 'cubrid:host=localhost;port=30000';

/**
 * The login for the metalizer database
 * @var string
 */
$config['database.user'] = 'root';

/**
 * The password for the metalizer database. Specify an empty string for no password
 * @var string
 */
$config['database.password'] = '';