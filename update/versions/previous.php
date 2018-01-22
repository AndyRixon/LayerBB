<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.0                 //
//===================================//
include '../../applications/config.php';
$dsn = 'mysql:dbname=' . MYSQL_DATABASE . ';host=' . MYSQL_HOST;
try {
    $MYSQL = new PDO($dsn, MYSQL_USERNAME, MYSQL_PASSWORD);
} catch (PDOException $e) {
    throw new Exception('Connection failed: ' . $e->getMessage());
}

// Version 1.0.0b4
$MYSQL->query("ALTER TABLE `" . MYSQL_PREFIX . "generic` ADD `site_enable` INT(1) NOT NULL DEFAULT '1' AFTER `number_subs`;");
// Version 1.0.0b5
$MYSQL->query("ALTER TABLE `" . MYSQL_PREFIX . "users` CHANGE `about_user` `about_user` LONGTEXT NULL;");
// Version 1.0.0rc1
$MYSQL->query("ALTER TABLE `" . MYSQL_PREFIX . "users` CHANGE `about_user` `about_user` LONGTEXT NULL;");
$MYSQL->query("ALTER TABLE `" . MYSQL_PREFIX . "generic` ADD `offline_msg` LONGTEXT NOT NULL AFTER `site_enable`;");
$MYSQL->query("ALTER TABLE `" . MYSQL_PREFIX . "forum_posts` ADD `views` INT(11) NOT NULL DEFAULT '0' AFTER `label`;");
// Version 1.0.0
$MYSQL->query("DROP TABLE IF EXISTS `" . MYSQL_PREFIX . "extensions`;");
$MYSQL->query("ALTER TABLE `" . MYSQL_PREFIX . "generic` ADD `whiteboard` LONGTEXT NOT NULL AFTER `offline_msg`;");
$MYSQL->query("CREATE TABLE IF NOT EXISTS `" . MYSQL_PREFIX . "nav` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `url` VARCHAR(255) NOT NULL , `newpage` INT(1) NOT NULL , `title`VARCHAR(255) NOT NULL , `ordernav` INT(11) NOT NULL , PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
$MYSQL->query("CREATE TABLE IF NOT EXISTS `" . MYSQL_PREFIX . "sidebar` ( `id` INT(11) NOT NULL AUTO_INCREMENT , `title` VARCHAR(50) NOT NULL , `content` LONGTEXT NOT NULL , `sideorder` INT(2) NOT NULL , `style` VARCHAR(50) NOT NULL , `glyphicon` VARCHAR(50) NOT NULL , PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
$MYSQL->query("CREATE TABLE IF NOT EXISTS `" . MYSQL_PREFIX . "apps` (`id` int(2) NOT NULL AUTO_INCREMENT,`appid` varchar(50) NOT NULL,`title` varchar(250) NOT NULL,`short` longtext NOT NULL,`active` int(1) NOT NULL,`w_url` int(1) NOT NULL,`url` varchar(50) NOT NULL,`author` varchar(50) NOT NULL,`version` varchar(50) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
// Version 1.0.1
$MYSQL->query("ALTER TABLE `" . MYSQL_PREFIX . "generic` ADD `logo` varchar(255) NOT NULL AFTER `offline_msg`;");
$MYSQL->query("CREATE TABLE IF NOT EXISTS `" . MYSQL_PREFIX . "profile_fields` (`id` int(2) NOT NULL AUTO_INCREMENT,`title` varchar(50) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
$MYSQL->query("CREATE TABLE IF NOT EXISTS `" . MYSQL_PREFIX . "profile_field_content` (`id` int(2) NOT NULL AUTO_INCREMENT,`userid` INT(2) NOT NULL,`fieldid` INT(2) NOT NULL,`content` LONGTEXT NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
// Version 1.0.2

?>