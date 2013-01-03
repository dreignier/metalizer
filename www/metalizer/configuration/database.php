<?php
/**
 * The database connection string.
 * @ar string
 */
// MySQL
$config['database.connection_string'] = 'mysql:host=localhost;dbname=metalizer';

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
$config['database.user'] = 'metalizer';

/**
 * The password for the metalizer database. Specify an empty string for no password
 * @var string
 */
$config['database.password'] = 'metalizer';

/**
 * The prefix for all tables in the database
 * @var string
 */
$config['database.table_prefix'] = 'metalizer_';

/**
 * The freeze value for redbean.
 * @var mixed
 */
$config['database.freeze'] = false;