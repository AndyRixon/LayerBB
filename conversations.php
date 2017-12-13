<?php

define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');

if($LAYER->data['site_enable'] == 0) {
    redirect(SITE_URL . '/offline.php');
}

if (!$LAYER->sess->isLogged) {
    redirect(SITE_URL);
}

$LAYER->tpl->getTpl('page');

switch ($PGET->g('cmd')) {

    case "view":
        require_once('applications/commands/conversations/view.php');
        $LAYER->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $page_title,
                $content
            )
        );
        break;

    case "new":
        require_once('applications/commands/conversations/new.php');
        $LAYER->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $page_title,
                $content
            )
        );
        break;

    case "reply":
        require_once('applications/commands/conversations/reply.php');
        $LAYER->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $page_title,
                $content
            )
        );
        break;

    case "delete":
        require_once('applications/commands/conversations/delete.php');
        $LAYER->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $page_title,
                $content
            )
        );
        break;

    default:
        require_once('applications/commands/conversations/home.php');
        $LAYER->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $page_title,
                $content
            )
        );
        break;

}

echo $LAYER->tpl->output();

?>