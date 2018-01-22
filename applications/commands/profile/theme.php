<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.0                 //
//===================================//

/*
 * Profile edit module for LayerBB.
 */
if (!defined('BASEPATH')) {
    die();
}
if (!$LAYER->sess->isLogged) {
    redirect(SITE_URL . '/404.php');
}//Check if user is logged in.

$page_title = 'Change Theme';

if ($PGET->g('set')) {

    //Breadcrumbs
    $LAYER->tpl->addBreadcrumb(
        $LANG['bb']['forum'],
        SITE_URL . '/forum.php'
    );
    $LAYER->tpl->addBreadcrumb(
        $LANG['bb']['members']['home'],
        SITE_URL . '/conversations.php'
    );
    $LAYER->tpl->addBreadcrumb(
        $LANG['bb']['profile']['change_theme'],
        '#',
        true
    );
    $content = $LAYER->tpl->breadcrumbs();


    $get     = ($PGET->g('set') == "default")? '0' : $PGET->g('set');

    $query   = $MYSQL->query("SELECT * FROM {prefix}themes");
    $t_names = array();
    foreach( $query as $t ) {
        $t_names[] = $t['id'];
    }
    $t_names[] = 0;

    //die(var_dump($get));

    if (in_array($get, $t_names)) {
        $MYSQL->bindMore(
            array(
                'chosen_theme' => $get,
                'id' => $LAYER->sess->data['id']
            )
        );

        if ($MYSQL->query("UPDATE {prefix}users SET chosen_theme = :chosen_theme WHERE id = :id") > 0) {
            $content .= $LAYER->tpl->entity(
                'success_notice',
                'content',
                $LANG['bb']['profile']['theme_set']
            );
            header('refresh:3;url=' . SITE_URL . '/forum.php');
        } else {
            $content .= $LAYER->tpl->entity(
                'danger_notice',
                'content',
                $LANG['bb']['profile']['theme_error']
            );
        }

    } else {
        $content .= $LAYER->tpl->entity(
            'danger_notice',
            'content',
            $LANG['bb']['profile']['theme_not_exist']
        );
    }

} else {
    redirect(SITE_URL . '/404.php');
}

?>
