<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//

/*
 * Conversations module for LayerBB
 * Everything that you want to display MUST be in the $content variable.
 */
if (!defined('BASEPATH')) {
    die();
}

$page_title = $LANG['bb']['conversations']['page_new'];
$content = '';
$notice = '';

if (isset($_POST['create'])) {
    try {
        foreach ($_POST as $parent => $child) {
            $_POST[$parent] = clean($child);
        }

        $user = $_POST['receiver'];
        $cont = htmlspecialchars_decode(emoji_to_text($_POST['content']));
        $title = htmlspecialchars_decode($_POST['title']);
        $time = time();
        $uid = explode(',', $user);

        if (!$user or !$cont or !$title) {
            throw new Exception ($LANG['global_form_process']['all_fields_required']);
        } else {

            foreach ($uid as $u) {

                if (!usernameExists($u)) {
                    throw new Exception (
                        str_replace(
                            '%username%',
                            $u,
                            $LANG['bb']['conversations']['user_not_exist']
                        )
                    );
                }
                $us = $LAYER->user($u);
                $MYSQL->bindMore(
                    array(
                        'message_title' => $title,
                        'message_content' => $cont,
                        'message_time' => $time,
                        'message_sender' => $LAYER->sess->data['id'],
                        'message_receiver' => $us['id']
                    )
                );

                if ($MYSQL->query("INSERT INTO {prefix}messages (message_title, message_content, message_time, message_sender, message_receiver, message_type) VALUES (:message_title, :message_content, :message_time, :message_sender, :message_receiver, 1)") > 0) {
                    $notice .= $LAYER->tpl->entity(
                        'success_notice',
                        'content',
                        str_replace(
                            '%username%',
                            $query['0']['username'],
                            $LANG['bb']['conversations']['message_sent']
                        )
                    );
                } else {
                    throw new Exception (
                        str_replace(
                            '%username%',
                            $query['0']['username'],
                            $LANG['bb']['conversations']['error_sending']
                        )
                    );
                }

            }

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

$pm_cont = (isset($_POST['content'])) ? $_POST['content'] : '';
$pm_user = (isset($_POST['receiver'])) ? $_POST['receiver'] : '';
$pm_title = (isset($_POST['title'])) ? $_POST['title'] : '';

//Breadcrumbs
$LAYER->tpl->addBreadcrumb(
    $LANG['bb']['forum'],
    SITE_URL . '/forum.php'
);
$LAYER->tpl->addBreadcrumb(
    $LANG['bb']['conversations']['page_conversations'],
    SITE_URL . '/conversations.php'
);
$LAYER->tpl->addBreadcrumb(
    $LANG['bb']['conversations']['page_new'],
    '#',
    true
);
$content .= $LAYER->tpl->breadcrumbs();

$content .= $notice . '<form action="" method="POST">
                 ' . $FORM->build('hidden', '', 'csrf_token', array('value' => CSRF_TOKEN)) . '
                 ' . $FORM->build('text', $LANG['bb']['conversations']['form_to'], 'receiver', array('value' => $pm_user, 'style' => 'width:100%')) . '
                 ' . $FORM->build('text', $LANG['bb']['conversations']['form_title'], 'title', array('value' => $pm_title)) . '
                 ' . $FORM->build('textarea', '', 'content', array('id' => 'editor', 'style' => 'width:100%;height:300px;max-width:100%;min-width:100%;', 'value' => $pm_cont)) . '
                 <br />
                 ' . $FORM->build('submit', '', 'create', array('value' => $LANG['bb']['conversations']['form_send'])) . '
               </form>';

?>