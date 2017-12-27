<?php

  define('BASEPATH', 'Forum');
  require_once('applications/wrapper.php');

  if($LAYER->data['site_enable'] == 1) {
    redirect('forum.php');
  }

  $LAYER->tpl->getTpl('page');

  $LAYER->tpl->addParam(
      array(
          'page_title',
          'content'
      ),
      array(
          'Forum Offline',
          $LAYER->data['offline_msg']
      )
  );

  echo $LAYER->tpl->output();

?>