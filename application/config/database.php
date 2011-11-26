<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the 'Database Connection'
| page of the User Guide.
|
| -------------------------------------------------------------------
| EXPLANATION OF VARIABLES
| -------------------------------------------------------------------
|
|	['hostname'] The hostname of your database server.
|	['username'] The username used to connect to the database
|	['password'] The password used to connect to the database
|	['database'] The name of the database you want to connect to
|	['dbdriver'] The database type. ie: mysql.  Currently supported:
				 mysql, mysqli, postgre, odbc, mssql, sqlite, oci8
|	['dbprefix'] You can add an optional prefix, which will be added
|				 to the table name when using the  Active Record class
|	['pconnect'] TRUE/FALSE - Whether to use a persistent connection
|	['db_debug'] TRUE/FALSE - Whether database errors should be displayed.
|	['cache_on'] TRUE/FALSE - Enables/disables query caching
|	['cachedir'] The path to the folder where cache files should be stored
|	['char_set'] The character set used in communicating with the database
|	['dbcollat'] The character collation used in communicating with the database
|				 NOTE: For MySQL and MySQLi databases, this setting is only used
| 				 as a backup if your server is running PHP < 5.2.3 or MySQL < 5.0.7.
| 				 There is an incompatibility in PHP with mysql_real_escape_string() which
| 				 can make your site vulnerable to SQL injection if you are using a
| 				 multi-byte character set and are running versions lower than these.
| 				 Sites using Latin-1 or UTF-8 database character set and collation are unaffected.
|	['swap_pre'] A default table prefix that should be swapped with the dbprefix
|	['autoinit'] Whether or not to automatically initialize the database.
|	['stricton'] TRUE/FALSE - forces 'Strict Mode' connections
|							- good for ensuring strict SQL while developing
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the 'default' group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = AlebrijeConfig::get('IKI_ENVIRONMENT');
$active_record = TRUE;

$db['dev']['hostname'] = '127.0.0.1';
$db['dev']['username'] = 'root';
$db['dev']['password'] = 'root';
$db['dev']['database'] = 'socializador';
$db['dev']['dbdriver'] = 'mysql';
$db['dev']['dbprefix'] = '';
$db['dev']['pconnect'] = TRUE;
$db['dev']['db_debug'] = TRUE;
$db['dev']['cache_on'] = FALSE;
$db['dev']['cachedir'] = '';
$db['dev']['char_set'] = 'utf8';
$db['dev']['dbcollat'] = 'utf8_general_ci';
$db['dev']['swap_pre'] = '';
$db['dev']['autoinit'] = TRUE;
$db['dev']['stricton'] = FALSE;

$db['stage']['hostname'] = '127.0.0.1';
$db['stage']['username'] = 'socializador';
$db['stage']['password'] = 'Ktj9GbQRGwxHZSHL';
$db['stage']['database'] = 'socializador';
$db['stage']['dbdriver'] = 'mysql';
$db['stage']['dbprefix'] = '';
$db['stage']['pconnect'] = TRUE;
$db['stage']['db_debug'] = TRUE;
$db['stage']['cache_on'] = FALSE;
$db['stage']['cachedir'] = '';
$db['stage']['char_set'] = 'utf8';
$db['stage']['dbcollat'] = 'utf8_general_ci';
$db['stage']['swap_pre'] = '';
$db['stage']['autoinit'] = TRUE;
$db['stage']['stricton'] = FALSE;

$db['prod']['hostname'] = '127.0.0.1';
$db['prod']['username'] = 'socializador';
$db['prod']['password'] = 'awSS3AEsJL24qWK2';
$db['prod']['database'] = 'socializador';
$db['prod']['dbdriver'] = 'mysql';
$db['prod']['dbprefix'] = '';
$db['prod']['pconnect'] = TRUE;
$db['prod']['db_debug'] = TRUE;
$db['prod']['cache_on'] = FALSE;
$db['prod']['cachedir'] = '';
$db['prod']['char_set'] = 'utf8';
$db['prod']['dbcollat'] = 'utf8_general_ci';
$db['prod']['swap_pre'] = '';
$db['prod']['autoinit'] = TRUE;
$db['prod']['stricton'] = FALSE;

/* End of file database.php */
/* Location: ./application/config/database.php */
