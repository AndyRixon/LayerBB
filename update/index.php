<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//
if(file_exists("../applications/install.lock")){
    echo "<div style='padding:10px; background: #ECD5D8; color: #BC2A4D; border:1px solid #BC2A4D;'>The updater is currently locked, you will need to remove /applications/install.lock to continue!</div>";
    exit();
}
define('BASEPATH', 'Forum');

@ini_set('zlib.output_compression', 1);

error_reporting(1);
ini_set('magic_quotes_runtime', 0);

ob_start();
session_start();
define('VERSION', '1.1.4');
echo '<title>LayerBB '. VERSION .' Updater!</title>';
require_once '../applications/config.php';
$new_mysql_host = MYSQL_HOST;
$new_mysql_login = MYSQL_USERNAME;
$new_mysql_pass = MYSQL_PASSWORD;
$new_mysql_database = MYSQL_DATABASE;
$new_db_prefix = MYSQL_PREFIX;
$new_site_url = SITE_URL;
$new_site_path = SITE_PATH;

echo '<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
<br /><br /><div class="row">
  <div class="col-md-4"></div>
  <div class="col-md-4">
<div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">LayerBB '. VERSION .' Updater!</h3>
  </div>
  <div class="panel-body">';
switch($_GET['step']) {
	case '1':
		$current = fopen("current.txt", "w");
		fwrite($current, LayerBB_VERSION);
		fclose($current);
		$ourFileName	= '../applications/config.php';
		$ourFileHandle	= fopen($ourFileName, 'w') or die("can't open file");

		$stringData = '<?php

if (!defined(\'BASEPATH\')) {
    die();
}

/*
 * LayerBB Configuration File.
 */
define(\'MYSQL_HOST\', "'.$new_mysql_host.'");
define(\'MYSQL_USERNAME\', "'.$new_mysql_login.'");
define(\'MYSQL_PASSWORD\', "'.$new_mysql_pass.'");
define(\'MYSQL_DATABASE\', "'.$new_mysql_database.'");
define(\'MYSQL_PREFIX\', "'.$new_db_prefix.'");
define(\'MYSQL_PORT\', 3306);

/*
 * LayerBB Local Details
 */
define(\'SITE_URL\', "'.$new_site_url.'");//Without the ending "/"
define(\'SITE_PATH\', "'.$new_site_path.'");//Without the ending "/"
define(\'LayerBB_VERSION\', "'. VERSION .'");
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
		echo 'The updater has successfully updated the config.php file.<br /><center><a href="index.php?step=2" class="btn btn-primary" role="button">Click here if the updater fails to go to the next step.</a></center>';
		header( "refresh:3;url=index.php?step=2" );
	break;
	case '2':		
		$current = fopen("current.txt", "r");
		$old_version = fgets($current);
		fclose($current);
		$dsn = 'mysql:dbname=' . $new_mysql_database . ';host=' . $new_mysql_host;

            try {
                $MYSQL = new PDO($dsn, $new_mysql_login, $new_mysql_pass);
            } catch (PDOException $e) {
                throw new Exception('Connection failed: ' . $e->getMessage());
            }
        echo 'The updater is currently making changes to the database.<br /><center><a href="index.php?step=success" class="btn btn-primary" role="button">Click here if the updater fails to go to the next step.</a></center>';
        if ('1.0.0 BETA 3' == $old_version) {
			include 'versions/previous.php';
			include 'versions/latest.php';
		} elseif ('1.0.0 BETA 4' == $old_version) {
			include 'versions/previous.php';
			include 'versions/latest.php';
		} elseif ('1.0.0 BETA 5' == $old_version) {
			include 'versions/previous.php';
			include 'versions/latest.php';
		} elseif ('1.0.0-RC1' == $old_version) {
			include 'versions/previous.php';
			include 'versions/latest.php';
		} elseif ('1.0.0' == $old_version) {
			include 'versions/previous.php';
			include 'versions/latest.php';
		} elseif ('1.0.1' == $old_version) {
			include 'versions/previous.php';
			include 'versions/latest.php';
		} elseif ('1.0.2' == $old_version) {
			include 'versions/previous.php';
			include 'versions/latest.php';
		} elseif ('1.0.3' == $old_version) {
			include 'versions/previous.php';
			include 'versions/latest.php';
		} elseif ('1.0.4' == $old_version) {
			include 'versions/previous.php';
			include 'versions/latest.php';
		} elseif ('1.0.5' == $old_version) {
			include 'versions/previous.php';
			include 'versions/latest.php';
		} elseif ('1.1.0' == $old_version) {
			include 'versions/previous.php';
			include 'versions/latest.php';
		} elseif ('1.1.1' == $old_version) {
			include 'versions/previous.php';
			include 'versions/latest.php';
		} else {
			include 'versions/latest.php';
		}

		// Temp install log check
		$website = SITE_URL;
        $name = 'From Updater';
        $url = 'https://api.layerbb.com/api.php?cmd=install';
        $myvars = 'url=' . $website . '&name=' . $name;
        $ch = curl_init( $url );
        curl_setopt( $ch, CURLOPT_POST, 1);
        curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
        curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
        curl_setopt( $ch, CURLOPT_HEADER, 0);
        curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
        curl_exec( $ch ); 

		header( "refresh:3;url=index.php?step=success" );
	break;
	case 'success':
        $createlock = fopen("../applications/install.lock", "w");
        fwrite($createlock, '');
        fclose($createlock);
		echo '<div class="alert alert-success" role="alert">You have successfully updated LayerBB<br /><strong>Please remember to remove the update folder!</strong><br /><br /><center><a href="../index.php" class="btn btn-primary" role="button">Click here to go to your forums!</a></center></div>';
	break;
	default:
		$config = substr(sprintf('%o', fileperms('../applications/config.php')), -3);
        $app = substr(sprintf('%o', fileperms('../applications')), -3);
		$current = substr(sprintf('%o', fileperms('current.txt')), -3);
		if ($current == '777' && $config == '777' && $app == '777'){
			$start = '<center><a href="index.php?step=1" class="btn btn-primary" role="button">Start Update</a></center>';
		} else {
			$start = '<div class="alert alert-danger" role="alert"><b>Incorrect File Permission:</b> Please check the file permissions above are correct.</div>';
		}
		echo "Welcome to the Updater Script for <b>LayerBB ". VERSION ."</b>";
		echo '<div class="alert alert-danger" role="alert">Any changes that you have made to the theme will be overwritten during this process, please always take a full backup.</div>
		<b>File Permissions</b>
		<ul>
  <li>applications/ set to 777</li>
  <li>applications/config.php set to 777</li>
  <li>update/current.txt set to 777 </li>
</ul>'. $start;
	break;
}
echo '</div>
  <div class="panel-footer">&copy; <a href="https://www.layerbb.com" target="_blank">LayerBB 2019</a></div>
</div>
</div>
  <div class="col-md-4"></div>
</div>';
?>