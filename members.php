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

$LAYER->tpl->getTpl('members');
switch ($PGET->g('cmd')) {

    case "register":
        if (!$LAYER->perm->check('access_administration')) {
            if($LAYER->data['site_enable'] == 0) {
                redirect(SITE_URL . '/offline.php');
            }
        }
        require_once('applications/commands/members/register.php');
        $LAYER->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $LANG['bb']['members']['register'],
                $content
            )
        );
        break;

    case "signin":
        require_once('applications/commands/members/signin.php');
        $LAYER->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $LANG['bb']['members']['log_in'],
                $content
            )
        );
        break;

    case "logout":
        require_once('applications/commands/members/logout.php');
        break;

    case "user":
        if (!$LAYER->perm->check('access_administration')) {
            if($LAYER->data['site_enable'] == 0) {
                redirect(SITE_URL . '/offline.php');
            }
        }
        require_once('applications/commands/members/user.php');
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

    case "activate":
        if (!$LAYER->perm->check('access_administration')) {
            if($LAYER->data['site_enable'] == 0) {
                redirect(SITE_URL . '/offline.php');
            }
        }
        require_once('applications/commands/members/activate.php');
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

    case "forgotpassword":
        if (!$LAYER->perm->check('access_administration')) {
            if($LAYER->data['site_enable'] == 0) {
                redirect(SITE_URL . '/offline.php');
            }
        }
        require_once('applications/commands/members/forgotpassword.php');
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

    case "resetpassword":
        if (!$LAYER->perm->check('access_administration')) {
            if($LAYER->data['site_enable'] == 0) {
                redirect(SITE_URL . '/offline.php');
            }
        }
        require_once('applications/commands/members/resetpassword.php');
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

    case "rules":
        if (!$LAYER->perm->check('access_administration')) {
            if($LAYER->data['site_enable'] == 0) {
                redirect(SITE_URL . '/offline.php');
            }
        }
        require_once('applications/commands/members/rules.php');
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
        if (!$LAYER->perm->check('access_administration')) {
            if($LAYER->data['site_enable'] == 0) {
                redirect(SITE_URL . '/offline.php');
            }
        }
        require_once('applications/commands/members/home.php');
        $LAYER->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $LANG['bb']['members']['home'],
                $content
            )
        );
        break;

}

echo $LAYER->tpl->output();

?>