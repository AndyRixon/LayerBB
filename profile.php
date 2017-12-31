<?php

define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');

if (!$LAYER->perm->check('access_administration')) {
    if($LAYER->data['site_enable'] == 0) {
        redirect(SITE_URL . '/offline.php');
    }
}

if (!$LAYER->sess->isLogged) {
    redirect(SITE_URL . '/404.php');
}//Check if user is logged in.

$LAYER->tpl->getTpl('members');

switch ($PGET->g('cmd')) {

    case "edit":
        require_once('applications/commands/profile/edit.php');
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

    case "avatar":
        require_once('applications/commands/profile/avatar.php');
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

    case "signature":
        require_once('applications/commands/profile/signature.php');
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

    case "password":
        require_once('applications/commands/profile/password.php');
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

    case "theme":
        require_once('applications/commands/profile/theme.php');
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
        redirect(SITE_URL . '/404.php');
        break;

}

echo $LAYER->tpl->output();

?>