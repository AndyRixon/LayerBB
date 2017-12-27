<?php

define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');
if($LAYER->data['site_enable'] == 0) {
    redirect(SITE_URL . '/offline.php');
}
$LAYER->tpl->getTpl('forum');
$LAYER->tpl->addParam('forum_listings', $LAYER->bb->listings());

echo $LAYER->tpl->output();

?>