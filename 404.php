<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.0                 //
//===================================//

define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');

$LAYER->tpl->getTpl('page');

$LAYER->tpl->addParam('page_title', $LANG['error_pages']['404']['header']);
$LAYER->tpl->addParam('content', $LANG['error_pages']['404']['message']);

echo $LAYER->tpl->output();

?>