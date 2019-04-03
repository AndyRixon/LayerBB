<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
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

$page_title = $LANG['bb']['profile']['signature'];
$content = '';
$notice = '';

if (isset($_POST['edit'])) {

    try {

        foreach ($_POST as $parent => $child) {
            $_POST[$parent] = clean($child);
        }

        NoCSRF::check('csrf_token', $_POST, true, 60*10, true);
        $sig = emoji_to_text($_POST['sig']);
        $MYSQL->bindMore(
            array(
                'user_signature' => $sig,
                'id' => $LAYER->sess->data['id']
            )
        );
        if ($MYSQL->query("UPDATE {prefix}users SET user_signature = :user_signature WHERE id = :id") > 0) {
            redirect(SITE_URL . '/profile.php/cmd/signature');
        } else {
            throw new Exception ($LANG['bb']['profile']['error_updating_signature']);
        }
    } catch (Exception $e) {
        $notice .= $LAYER->tpl->entity(
            'danger_notice',
            'content',
            $e->getMessage()
        );
    }

}

define('CSRF_TOKEN', NoCSRF::generate('csrf_token'));
if($LAYER->data['enable_signatures'] == '1') {
    $content .= '<form id="LAYER_form" action="" method="POST">
                 ' . $FORM->build('hidden', '', 'csrf_token', array('value' => CSRF_TOKEN)) . '
                 ' . $FORM->build('textarea', '', 'sig', array('value' => $LAYER->sess->data['user_signature'], 'id' => 'editor', 'style' => 'width:100%;height:300px;max-width:100%;min-width:100%;')) . '
                 <br /><br />
                 ' . $FORM->build('submit', '', 'edit', array('value' => $LANG['bb']['profile']['form_save'])) . '
               </form>';
} else {
    $content .= $LAYER->tpl->entity(
        'danger_notice',
        'content',
        $LANG['errors']['sig_disabled']
    );
}

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
    $LANG['bb']['profile']['signature'],
    '#',
    true
);
$bc = $LAYER->tpl->breadcrumbs();

$content = $bc . $notice . $content;

?>
