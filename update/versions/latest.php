<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//
include '../../applications/config.php';
$dsn = 'mysql:dbname=' . MYSQL_DATABASE . ';host=' . MYSQL_HOST;
try {
    $MYSQL = new PDO($dsn, MYSQL_USERNAME, MYSQL_PASSWORD);
} catch (PDOException $e) {
    throw new Exception('Connection failed: ' . $e->getMessage());
}

// Latest SQL
/*$MYSQL->query("ALTER TABLE `".MYSQL_PREFIX."generic` ADD `enable_signatures` INT(1) NOT NULL DEFAULT '1' AFTER `enable_rtl`, ADD `enable_pcomments` INT(1) NOT NULL DEFAULT '1' AFTER `enable_signatures`;");
$MYSQL->query("ALTER TABLE `".MYSQL_PREFIX."generic` DROP `smtp_address`, DROP `smtp_port`, DROP `smtp_username`, DROP `smtp_password`;");
$MYSQL->query("ALTER TABLE `".MYSQL_PREFIX."usergroups` ADD `banner_style_s` VARCHAR(255) NOT NULL DEFAULT '<span class=\"label label -default\">' AFTER `group_style`;");
$MYSQL->query("ALTER TABLE `".MYSQL_PREFIX."usergroups` ADD `banner_style_e` VARCHAR(255) NOT NULL DEFAULT '</span>' AFTER `banner_style_s`;");
$MYSQL->query("CREATE TABLE IF NOT EXISTS `".MYSQL_PREFIX."user_followers` ( `id` INT(12) NOT NULL AUTO_INCREMENT , `profile_owner` INT(12) NULL , `follower` INT(12) NULL , PRIMARY KEY (`id`))");
$MYSQL->query("CREATE TABLE IF NOT EXISTS `".MYSQL_PREFIX."likes` ( `id` INT(12) NOT NULL AUTO_INCREMENT , `u_id` INT(12) NULL , `p_id` INT(12) NULL , PRIMARY KEY (`id`))");

// Update theme
$sand = file_get_contents('../public/themes/Sand/entities.json');
$stmt = $MYSQL->prepare("UPDATE `" . MYSQL_PREFIX . "themes` SET `theme_json_data` = :sand WHERE `id` = 1;");
$stmt->bindParam(':sand', $sand);
$stmt->execute();*/
?>