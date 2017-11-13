<?php

/*
 * Sign In module for LayerBB
 * Everything that you want to display MUST be in the $content variable.
 */
if (!defined('BASEPATH')) {
    die();
}

if ($LAYER->sess->isLogged) {
    redirect(SITE_URL);
} //If user is logged in.
$content = '';

//Breadcrumb
$LAYER->tpl->addBreadcrumb(
    $LANG['bb']['forum'],
    SITE_URL . '/forum.php'
);
$LAYER->tpl->addBreadcrumb(
    $LANG['bb']['members']['home'],
    SITE_URL . '/members.php'
);
$LAYER->tpl->addBreadcrumb(
    $LANG['bb']['members']['log_in'],
    '#',
    true
);
$content .= $LAYER->tpl->breadcrumbs();

if (isset($_POST['signin'])) {

    $notice = '';

    try {

        foreach ($_POST as $parent => $child) {
            $_POST[$parent] = clean($child);
        }

        $email = $_POST['email'];
        $password = $_POST['password'];

        if (!$email or !$password) {
            throw new Exception ($LANG['global_form_process']['all_fields_required']);
        } elseif (!userExists($email, $password)) {
            throw new Exception ($LANG['bb']['members']['invalid_login']);
        } elseif (($ban = userBanned($email)) !== false) {
            throw new Exception (
                str_replace(
                    array(
                        '%unban_date%',
                        '%ban_reason%'
                    ),
                    array(
                        date('F j, Y', $ban['unban_time']),
                        $ban['ban_reason']
                    ),
                    $LANG['bb']['members']['banned']
                )
            );
        } elseif (!userActivated($email)) {
            throw new Exception ($LANG['bb']['members']['email_not_activated']);
        } else {

            $remember = (isset($_POST['remember'])) ? true : false;
            $LAYER->sess->assign($email, $remember);
            if (isset($_POST['current_url']) && !empty($_POST['current_url'])) {
                header('refresh:3;url=http://' . $_POST['current_url']);
            } else {
                header('refresh:3;url=' . SITE_URL . '/forum.php');
            }

            $content .= $LAYER->tpl->entity(
                'success_notice',
                'content',
                $LANG['bb']['members']['login_success']
            );

        }

    } catch (Exception $e) {
        $content .= $LAYER->tpl->entity(
            'danger_notice',
            'content',
            $e->getMessage()
        );
    }

}
$form = $LAYER->tpl->entity(
    'login_form',
    array(
        'email_field_name',
        'password_field_name',
        'submit_field_name',
        'remember_field_name'
    ),
    array(
        'email',
        'password',
        'signin',
        'remember'
    )
);

$content = $content . $form;

?>
