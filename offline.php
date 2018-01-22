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

  if($LAYER->data['site_enable'] == 1) {
    redirect('forum.php');
  }

  $LAYER->tpl->getTpl('page');
  $msg = html_entity_decode($LAYER->data['offline_msg']);
  $LAYER->tpl->addParam(
      array(
          'page_title',
          'content'
      ),
      array(
          'Forum Offline',
          $msg
      )
  );

  echo $LAYER->tpl->output();

?>