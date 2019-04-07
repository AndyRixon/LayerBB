<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//

/*
 * Password Reset Module for LayerBB.
 * Everything that you want to display MUST be in the $content variable.
 */
if (!defined('BASEPATH')) {
    die();
}

if ($LAYER->sess->isLogged) {
    redirect(SITE_URL);
} //If user is logged in.

$page_title = $LANG['bb']['members']['forgot_password'];
$content = '';
$notice = '';

if (isset($_POST['forget'])) {
    try {

        foreach ($_POST as $parent => $child) {
            $_POST[$parent] = clean($child);
        }

        $email = $_POST['email'];

        NoCSRF::check('csrf_token', $_POST, true, 60*10, true);

        if (!$email) {
            throw new Exception ($LANG['global_form_process']['all_fields_required']);
        } elseif (!emailTaken($email)) {
            throw new Exception ($LANG['global_form_process']['email_not_exist']);
        } else {
            $MYSQL->bind('user_email', $email);
            $query = $MYSQL->query("SELECT * FROM {prefix}users WHERE user_email = :user_email");

            // deactivate all previous reset requests
            $MYSQL->bind('user', $query['0']['id']);
            $MYSQL->query("UPDATE {prefix}password_reset_requests SET active = 0 WHERE user = :user");

            $reset_token = randomHexBytes(16);
            $token_hash = hash('sha256', $reset_token);
            $MYSQL->bindMore(
                array(
                    'user' => $query[0]['id'],
                    'reset_token' => $token_hash,
                    'request_time' => time(),
                )
            );

            $successful = false;
            if ($MYSQL->query("INSERT INTO {prefix}password_reset_requests (user, reset_token, request_time) VALUES (:user, :reset_token, :request_time)") > 0) {
            	$sitename = $LAYER->data['site_name'];
                $siteemail = $LAYER->data['site_email'];
                $subject = 'Reset your password request';
                $emailcontent = 'Hey ' . $query['0']['username'] . ', You have requested to reset your password on ' . $sitename . ', please follow the link below and follow the on screen instructions.' . "\r\n\r\n" . SITE_URL . '/members.php/cmd/resetpassword/token/' . urlencode($token_hash) . "\r\n\r\n" . 'If this wasnt you, please ignore this email.';
                $headers = 'From: '.$sitename.' <'.$siteemail.'>' . "\r\n" .
                            'Reply-To: '.$siteemail . "\r\n" .
                            'X-Sender: '.$siteemail . "\r\n" .
                             'X-Mailer: PHP/' . phpversion();
                $successful = mail($email, $subject, $emailcontent, $headers);
            } else {
                $successful = false;
            }

            if ($successful) {
                $notice .= $LAYER->tpl->entity(
                    'success_notice',
                    'content',
                    $LANG['bb']['members']['password_reset_link_sent']
                );
            } else {
                throw new Exception ($LANG['bb']['members']['error_request_password_reset']);
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
    $LANG['bb']['members']['forgot_password'],
    '#',
    true
);
$bc = $LAYER->tpl->breadcrumbs();

$content .= $LAYER->tpl->entity(
    'forget_password_form',
    array(
        'csrf_field',
        'email_field_name',
        'submit_field_name'
    ),
    array(
        $FORM->build('hidden', '', 'csrf_token', array('value' => CSRF_TOKEN)),
        'email',
        'forget'
    )
);

$content = $bc . $notice . $content;
