<?php
if(!file_exists('applications/config.php')){
    header('Location: install/');
}
define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');

if($LAYER->data['site_enable'] == 1) {
	redirect(SITE_URL . '/forum.php');
} else {
	redirect(SITE_URL . '/offline.php');
}

?>
