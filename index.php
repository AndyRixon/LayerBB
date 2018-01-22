<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.0                 //
//===================================//

if(!file_exists('applications/config.php')){
    header('Location: install/');
}
define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');

redirect('forum.php');

?>
