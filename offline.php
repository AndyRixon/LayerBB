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
          'Our forum is currently offline for maintanance, we will be back shortly.'
      )
  );

  echo $LAYER->tpl->output();

?>