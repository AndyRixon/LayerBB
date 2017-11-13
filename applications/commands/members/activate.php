<?php

/*
 * Account Activation Module for LayerBB.
 * Everything that you want to display MUST be in the $content variable.
 */
if (!defined('BASEPATH')) {
    die();
}

if ($LAYER->sess->isLogged) {
    redirect(SITE_URL);
} //If user is logged in.

$page_title = $LANG['bb']['members']['activate_account'];

if ($PGET->g('code')) {

    $code = clean($PGET->g('code'));
    $MYSQL->bind('date_joined', $code);
    $query = $MYSQL->query("SELECT * FROM {prefix}users WHERE date_joined = :date_joined");

    if (!empty($query)) {

        //Breadcrumbs
        $LAYER->tpl->addBreadcrumb(
            $LANG['bb']['forum'],
            SITE_URL . '/forum.php'
        );
        $LAYER->tpl->addBreadcrumb(
            $LANG['bb']['members']['home'],
            SITE_URL . '/members.php'
        );
        $LAYER->tpl->addBreadcrumb(
            $LANG['bb']['members']['activate_account'],
            '#',
            true
        );
        $content = $LAYER->tpl->breadcrumbs();

        if ($query['0']['user_disabled'] == 1) {
            $MYSQL->bind('id', $query['0']['id']);
            if ($MYSQL->query("UPDATE {prefix}users SET user_disabled = 0 WHERE id = :id") > 0) {
                $content .= $LAYER->tpl->entity(
                    'success_notice',
                    'content',
                    $LANG['bb']['members']['account_activated']
                );
            } else {
                $content .= $LAYER->tpl->entity(
                    'danger_notice',
                    'content',
                    $LANG['bb']['members']['error_activating']
                );
            }

        } else {
            redirect(SITE_URL);
        }

    } else {
        redirect(SITE_URL);
    }

} else {
    redirect(SITE_URL);
}

?>