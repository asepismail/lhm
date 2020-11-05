<?php  if ( ! defined('BASEPATH')) exit('No direct script access allowed');
/*
| -------------------------------------------------------------------
| DATABASE CONNECTIVITY SETTINGS
| -------------------------------------------------------------------
| This file will contain the settings needed to access your database.
|
| For complete instructions please consult the "Database Connection"
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
|
| The $active_group variable lets you choose which connection group to
| make active.  By default there is only one group (the "default" group).
|
| The $active_record variables lets you determine whether or not to load
| the active record class
*/

$active_group = "default";
$active_record = TRUE;

//$db['default']['hostname'] = "10.88.1.104";
$db['default']['hostname'] = "localhost";
//$db['default']['hostname'] = "10.88.1.71";
$db['default']['username'] = "root";
//$db['default']['password'] = "app5224878";
$db['default']['password'] = "";
//$db['default']['database'] = "testing_lhm";
//$db['default']['database'] = "uat_lhm";
$db['default']['database'] = "lhm_online";
//$db['default']['database'] = "timbangan_gkm";

$db['default']['dbdriver'] = "mysqli";
$db['default']['dbprefix'] = "";
$db['default']['pconnect'] = TRUE;
$db['default']['db_debug'] = FALSE;
$db['default']['cache_on'] = FALSE;
$db['default']['cachedir'] = "";
$db['default']['char_set'] = "utf8";
$db['default']['dbcollat'] = "utf8_general_ci";

//Added by Ridhu, 20140325
$db['adem']['hostname'] = "10.88.1.109";
$db['adem']['username'] = "adempiere";
$db['adem']['password'] = "adempiere";
$db['adem']['database'] = "adempiere";
$db['adem']['dbdriver'] = "postgre";
$db['adem']['dbprefix'] = "";
$db['adem']['pconnect'] = TRUE;
$db['adem']['db_debug'] = FALSE;
$db['adem']['cache_on'] = FALSE;
$db['adem']['cachedir'] = "";
$db['adem']['port'] = "5432";

//Added by Asep, 20130521
$db['lhm_gkm']['hostname'] = "localhost";
//$db['lhm_gkm']['hostname'] = "10.88.1.104";
$db['lhm_gkm']['username'] = "root";
$db['lhm_gkm']['password'] = "app5224878";
//$db['lhm_gkm']['password'] = "";
$db['lhm_gkm']['database'] = "lhm_online_gkm";
$db['lhm_gkm']['dbdriver'] = "mysqli";
$db['lhm_gkm']['dbprefix'] = "";
$db['lhm_gkm']['pconnect'] = TRUE;
$db['lhm_gkm']['db_debug'] = FALSE;
$db['lhm_gkm']['cache_on'] = FALSE;
$db['lhm_gkm']['cachedir'] = "";
$db['lhm_gkm']['char_set'] = "utf8";
$db['lhm_gkm']['dbcollat'] = "utf8_general_ci";

//Added by Asep, 20130617
//$db['MAG']['hostname'] = "10.88.1.71";
$db['MAG']['username'] = "root";
$db['MAG']['password'] = "pr0v1d3ntmysql";
$db['MAG']['database'] = "timbangan_mag_uat";
//$db['MAG']['database'] = "timbangan_mag";
$db['MAG']['dbdriver'] = "mysqli";
$db['MAG']['dbprefix'] = "";
$db['MAG']['pconnect'] = FALSE;
$db['MAG']['db_debug'] = FALSE;
$db['MAG']['cache_on'] = FALSE;
$db['MAG']['cachedir'] = "";
$db['MAG']['char_set'] = "utf8";
$db['MAG']['dbcollat'] = "utf8_general_ci";

//Added by Asep, 20130617
//$db['LIH']['hostname'] = "localhost";
//$db['LIH']['hostname'] = "10.88.1.71";
$db['LIH']['username'] = "root";
//$db['LIH']['password'] = "";//pr0v1d3ntmysql
$db['LIH']['password'] = "pr0v1d3ntmysql";
//$db['LIH']['database'] = "timbangan_lih_uat";
$db['LIH']['database'] = "timbangan_lih";
$db['LIH']['dbdriver'] = "mysqli";
$db['LIH']['dbprefix'] = "";
$db['LIH']['pconnect'] = FALSE;
$db['LIH']['db_debug'] = FALSE;
$db['LIH']['cache_on'] = FALSE;
$db['LIH']['cachedir'] = "";
$db['LIH']['char_set'] = "utf8";
$db['LIH']['dbcollat'] = "utf8_general_ci";

//Added by Asep, 20130618
$db['GKM']['hostname'] = "10.88.1.63";
$db['GKM']['username'] = "root";
$db['GKM']['password'] = "pr0v1d3ntmysql";
$db['GKM']['database'] = "timbangan_gkm";
$db['GKM']['dbdriver'] = "mysqli";
$db['GKM']['dbprefix'] = "";
$db['GKM']['pconnect'] = FALSE;
$db['GKM']['db_debug'] = FALSE;
$db['GKM']['cache_on'] = FALSE;
$db['GKM']['cachedir'] = "";
$db['GKM']['char_set'] = "utf8";
$db['GKM']['dbcollat'] = "utf8_general_ci";

$db['SMI']['hostname'] = "10.88.1.63";
$db['SMI']['username'] = "root";
$db['SMI']['password'] = "pr0v1d3ntmysql";
$db['SMI']['database'] = "timbangan_sss_mill";
$db['SMI']['dbdriver'] = "mysqli";
$db['SMI']['dbprefix'] = "";
$db['SMI']['pconnect'] = FALSE;
$db['SMI']['db_debug'] = FALSE;
$db['SMI']['cache_on'] = FALSE;
$db['SMI']['cachedir'] = "";
$db['SMI']['char_set'] = "utf8";
$db['SMI']['dbcollat'] = "utf8_general_ci";

//Added by Asep, 20130618
//$db['GKM_SITE']['hostname'] = "10.88.22.20";
$db['GKM_SITE']['hostname'] = "10.88.1.71";
$db['GKM_SITE']['username'] = "root";
$db['GKM_SITE']['password'] = "pr0v1d3ntmysql";
//$db['GKM_SITE']['database'] = "timbangan";
$db['GKM_SITE']['database'] = "uat_timbangan_gkm";
$db['GKM_SITE']['dbdriver'] = "mysqli";
$db['GKM_SITE']['dbprefix'] = "";
$db['GKM_SITE']['pconnect'] = FALSE;
$db['GKM_SITE']['db_debug'] = FALSE;
$db['GKM_SITE']['cache_on'] = FALSE;
$db['GKM_SITE']['cachedir'] = "";
$db['GKM_SITE']['char_set'] = "utf8";
$db['GKM_SITE']['dbcollat'] = "utf8_general_ci";

//Added by Asep, 20130618
//$db['MAG_SITE']['hostname'] = "10.88.22.20";
$db['MAG_SITE']['hostname'] = "10.88.1.71";
$db['MAG_SITE']['username'] = "root";
$db['MAG_SITE']['password'] = "pr0v1d3ntmysql";
$db['MAG_SITE']['database'] = "uat_timbangan_mag";
$db['MAG_SITE']['dbdriver'] = "mysqli";
$db['MAG_SITE']['dbprefix'] = "";
$db['MAG_SITE']['pconnect'] = FALSE;
$db['MAG_SITE']['db_debug'] = FALSE;
$db['MAG_SITE']['cache_on'] = FALSE;
$db['MAG_SITE']['cachedir'] = "";
$db['MAG_SITE']['char_set'] = "utf8";
$db['MAG_SITE']['dbcollat'] = "utf8_general_ci";

//Added by Asep, 20130618
//$db['LIH_SITE']['hostname'] = "10.88.22.20";
$db['LIH_SITE']['hostname'] = "10.88.1.71";
$db['LIH_SITE']['username'] = "root";
$db['LIH_SITE']['password'] = "pr0v1d3ntmysql";
//$db['LIH_SITE']['database'] = "timbangan";
$db['LIH_SITE']['database'] = "uat_timbangan_lih";
$db['LIH_SITE']['dbdriver'] = "mysqli";
$db['LIH_SITE']['dbprefix'] = "";
$db['LIH_SITE']['pconnect'] = FALSE;
$db['LIH_SITE']['db_debug'] = FALSE;
$db['LIH_SITE']['cache_on'] = FALSE;
$db['LIH_SITE']['cachedir'] = "";
$db['LIH_SITE']['char_set'] = "utf8";
$db['LIH_SITE']['dbcollat'] = "utf8_general_ci";

/*
//Added by Asep, 20130617
$db['LIH']['hostname'] = "10.88.1.71";
$db['LIH']['username'] = "root";
$db['LIH']['password'] = "pr0v1d3ntmysql";//
$db['LIH']['database'] = "timbangan_lih";
$db['LIH']['dbdriver'] = "mysqli";
$db['LIH']['dbprefix'] = "";
$db['LIH']['pconnect'] = FALSE;
$db['LIH']['db_debug'] = FALSE;
$db['LIH']['cache_on'] = FALSE;
$db['LIH']['cachedir'] = "";
$db['LIH']['char_set'] = "utf8";
$db['LIH']['dbcollat'] = "utf8_general_ci";

//Added by Asep, 20130618
$db['GKM']['hostname'] = "10.88.1.71";
$db['GKM']['username'] = "root";
$db['GKM']['password'] = "pr0v1d3ntmysql";
$db['GKM']['database'] = "timbangan_gkm";
$db['GKM']['dbdriver'] = "mysqli";
$db['GKM']['dbprefix'] = "";
$db['GKM']['pconnect'] = FALSE;
$db['GKM']['db_debug'] = FALSE;
$db['GKM']['cache_on'] = FALSE;
$db['GKM']['cachedir'] = "";
$db['GKM']['char_set'] = "utf8";
$db['GKM']['dbcollat'] = "utf8_general_ci";

//Added by Asep, 20130618
$db['NAK']['hostname'] = "10.88.1.71";
$db['NAK']['username'] = "root";
$db['NAK']['password'] = "pr0v1d3ntmysql";
$db['NAK']['database'] = "timbangan_nakau";
$db['NAK']['dbdriver'] = "mysqli";
$db['NAK']['dbprefix'] = "";
$db['NAK']['pconnect'] = FALSE;
$db['NAK']['db_debug'] = FALSE;
$db['NAK']['cache_on'] = FALSE;
$db['NAK']['cachedir'] = "";
$db['NAK']['char_set'] = "utf8";
$db['NAK']['dbcollat'] = "utf8_general_ci";

//Added by Asep, 20130618
$db['TPAI']['hostname'] = "10.88.1.71";
$db['TPAI']['username'] = "root";
$db['TPAI']['password'] = "pr0v1d3ntmysql";
$db['TPAI']['database'] = "timbangan_tpai";
$db['TPAI']['dbdriver'] = "mysqli";
$db['TPAI']['dbprefix'] = "";
$db['TPAI']['pconnect'] = FALSE;
$db['TPAI']['db_debug'] = FALSE;
$db['TPAI']['cache_on'] = FALSE;
$db['TPAI']['cachedir'] = "";
$db['TPAI']['char_set'] = "utf8";
$db['TPAI']['dbcollat'] = "utf8_general_ci";
*/
/* End of file database.php */
/* Location: ./system/application/config/database.php */
