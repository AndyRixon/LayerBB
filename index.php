<?php
if(!file_exists('applications/config.php')){
    header('Location: install/');
}
define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');

redirect('forum.php');

?>
