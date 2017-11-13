<?php

define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');
$LAYER->tpl->getTpl('forum');
$LAYER->tpl->addParam('forum_listings', $LAYER->bb->listings());

echo $LAYER->tpl->output();

?>