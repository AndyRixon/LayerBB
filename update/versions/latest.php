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

// Latest SQL
//$MYSQL->query("");

// Update theme
$sand = file_get_contents('../public/themes/Sand/entities.json');
$stmt = $MYSQL->prepare("UPDATE `" . MYSQL_PREFIX . "themes` SET `theme_json_data` = :sand WHERE `id` = 1;");
$stmt->bindParam(':sand', $sand);
$stmt->execute();
?>