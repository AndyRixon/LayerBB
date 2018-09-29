<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//

define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');

if (!$LAYER->perm->check('access_administration')) {
    if($LAYER->data['site_enable'] == 0) {
	    redirect(SITE_URL . '/offline.php');
	}
}

$LAYER->tpl->getTpl('forum');
$LAYER->tpl->addParam('forum_listings', $LAYER->bb->listings());

echo $LAYER->tpl->output();

?>