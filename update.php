<?php
define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');
@ini_set('zlib.output_compression', 1);

error_reporting(1);
ini_set('magic_quotes_runtime', 0);

ob_start();
session_start();
define('VERSION', '1.0.0 BETA 5');
echo '<title>LayerBB '. VERSION .' Updater!</title>';
require_once 'applications/config.php';
$new_mysql_host = MYSQL_HOST;
$new_mysql_login = MYSQL_USERNAME;
$new_mysql_pass = MYSQL_PASSWORD;
$new_mysql_database = MYSQL_DATABASE;
$new_db_prefix = MYSQL_PREFIX;
$new_site_url = SITE_URL;
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
 * LayerBB Local Details
 */
define(\'SITE_URL\', "'.$new_site_url.'");//Without the ending "/"
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
		echo 'The updater has successfully updated the config.php file.<br /><a href="update.php?step=2" class="btn btn-default" role="button">Click here if the updater fails to go to the next step.</a>';
		header( "refresh:3;url=update.php?step=2" );
	break;
	case '2':
		$dsn = 'mysql:dbname=' . $new_mysql_database . ';host=' . $new_mysql_host;

            try {
                $MYSQL = new PDO($dsn, $new_mysql_login, $new_mysql_pass);
            } catch (PDOException $e) {
                throw new Exception('Connection failed: ' . $e->getMessage());
            }
        $MYSQL->query("ALTER TABLE `" . $new_db_prefix . "users` CHANGE `about_user` `about_user` LONGTEXT NULL;");
		$MYSQL->query("DROP TABLE IF EXISTS `" . $new_db_prefix . "themes`;");
		$MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $new_db_prefix . "themes` (`id` int(11) NOT NULL AUTO_INCREMENT, `theme_name` varchar(255) NOT NULL, `theme_version` varchar(255) NOT NULL DEFAULT '1', `theme_json_data` LONGTEXT NOT NULL, PRIMARY KEY(`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
		$sand = file_get_contents('install/assets/theme-json/sand.json');
        $stmt = $MYSQL->prepare("INSERT INTO " . $new_db_prefix . "themes (`theme_name`, `theme_version`, `theme_json_data`) VALUES ('Sand', '1.0', :sand);");
        $stmt->bindParam(':sand', $sand);
        $stmt->execute();

		echo 'The updater is currently making changes to the database.<br /><a href="update.php?step=success" class="btn btn-default" role="button">Click here if the updater fails to go to the next step.</a>';
		header( "refresh:3;url=update.php?step=success" );
	break;
	case 'success':
		echo '<div class="alert alert-success" role="alert">You have successfully updated LayerBB<br /><strong>Please remember to remove the update.php file and install folder!</strong><br /><br /><a href="index.php" class="btn btn-default" role="button">Click here to go to your forums!</a></div>';
	break;
	default:
		echo "Welcome to the Updater Script for <b>LayerBB ". VERSION ."</b>";
		echo '<div class="alert alert-danger" role="alert">Please ensure that you are currently running on the previous version of LayerBB, otherwise you may break your forum. Always take a backup.<br /><br />Please make sure that the config.php file in your applications directory has writable (777) permissions before continuing.<br /><a href="update.php?step=1" class="btn btn-default" role="button">Click here to start the updater!</a></div>';
	break;
}
echo '</div>
  <div class="panel-footer">&copy; <a href="https://www.layerbb.com" target="_blank">LayerBB 2017</a></div>
</div>
</div>
  <div class="col-md-4"></div>
</div>';
?>