<?php
define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');
@ini_set('zlib.output_compression', 1);

error_reporting(1);
ini_set('magic_quotes_runtime', 0);

ob_start();
session_start();
echo '<title>LayerBB Updater!</title>';
require_once 'applications/config.php';
$new_mysql_host = MYSQL_HOST;
$new_mysql_login = MYSQL_USERNAME;
$new_mysql_pass = MYSQL_PASSWORD;
$new_mysql_database = MYSQL_DATABASE;
$new_db_prefix = MYSQL_PREFIX;
$new_site_url = SITE_URL;
switch($_GET['step']) {
	case '1':
		echo 'The updater is currently updating the config.php file. Please wait...';
		$ourFileName	= 'applications/config.php';
		$ourFileHandle	= fopen($ourFileName, 'w') or die("can't open file");

		$stringData = '<?php

if (!defined(\'BASEPATH\')) {
    die();
}

/*
 * LayerBB Configuration File.
 * LayerBB (http://LayerBB.net)
 */
define(\'MYSQL_HOST\', "'.$new_mysql_host.'");
define(\'MYSQL_USERNAME\', "'.$new_mysql_login.'");
define(\'MYSQL_PASSWORD\', "'.$new_mysql_pass.'");
define(\'MYSQL_DATABASE\', "'.$new_mysql_database.'");
define(\'MYSQL_PREFIX\', "'.$new_db_prefix.'");
define(\'MYSQL_PORT\', 3306);

/*
 * Iko Local Details
 */
define(\'SITE_URL\', "'.$new_site_url.'");//Without the ending "/"
define(\'LayerBB_VERSION\', \'1.0.0 BETA 3\');
define(\'LAYER_SESSION_TIMEOUT\', 31536000);//In seconds.
define(\'USER_PASSWORD_HASH_COST\', 10);

/*
 * Usergroup Details.
 * DO NOT CHANGE IF YOU DONT KNOW WHAT THIS WILL DO.
 */
define(\'ADMIN_ID\', \'4\');
define(\'MOD_ID\', \'3\');
define(\'BAN_ID\', \'2\');

/*
 * Forum Configuration.
 */
define(\'THREAD_RESULTS_PER_PAGE\', 12);
define(\'POST_RESULTS_PER_PAGE\', 9);

?>';

		fwrite($ourFileHandle, $stringData);
		fclose($ourFileHandle);
		echo '<br />The updater has successfully updated the config.php file.<br /><a href="update.php?step=2">Click here to continue with the update!</a>';
	break;
	case '2':
		echo 'You have successfully updated LayerBB<br /><strong>Please remember to remove the update.php file!</strong><br /><br /><a href="index.php">Click here to go to your forums!</a>';
	break;
	default:
		echo "Welcome to the Updater Script for LayerBB";
		echo "<div style='padding:10px; background: #ECD5D8; color: #BC2A4D; border:1px solid #BC2A4D;'>Please make sure that the config.php file in your applications directory has writable (777) permissions before continuing.<br /><a href='update.php?step=1'>Click here to start the updater!</a></div>";
	break;
}
?>