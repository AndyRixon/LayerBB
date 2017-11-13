<?php

/*
 * Register Module for LayerBB.
 * Everything that you want to display MUST be in the $content variable.
 */
if (!defined('BASEPATH')) {
    die();
}

if ($LAYER->sess->isLogged) {
    redirect(SITE_URL);
} //If user is logged in.

$notice = '';
$content = '';

if (isset($_POST['register']) && $LAYER->data['register_enable'] == 1) {
    try {

        foreach ($_POST as $parent => $child) {
            $_POST[$parent] = clean($child);
        }

        NoCSRF::check('csrf_token', $_POST);//CSRF Checking.
        $username = $_POST['username'];
        $password = $_POST['password'];
        $a_password = $_POST['a_password'];
        $email = $_POST['email'];

        //Remembering Username and Email.
        $_SESSION['register_form_username'] = $username;
        $_SESSION['register_form_email'] = $email;
        $time = time();

        if (!$username or !$password or !$a_password or !$email) {
            throw new Exception ($LANG['global_form_process']['all_fields_required']);
        } elseif ($password !== $a_password) {
            throw new Exception ($LANG['bb']['members']['password_different']);
        } elseif (usernameExists($username)) {
            throw new Exception ($LANG['bb']['members']['username_taken']);
        } elseif (!validEmail($email)) {
            throw new Exception ($LANG['global_form_process']['invalid_email']);
        } elseif (emailTaken($email)) {
            throw new Exception ($LANG['global_form_process']['email_used']);
        } elseif (substr_count($username, " ") > 0) {
            throw new Exception ($LANG['bb']['members']['space_user']);
        } else {

            //Verifying the captcha.
            $LAYER->captcha->verify();

            if ($LAYER->data['register_email_activate'] == "1") {
                $MYSQL->bindMore(array(
                    'username' => $username,
                    'user_password' => encrypt($password),
                    'user_email' => $email,
                    'date_joined' => $time,
                    'user_disabled' => 1
                ));
            } else {
                $MYSQL->bindMore(array(
                    'username' => $username,
                    'user_password' => encrypt($password),
                    'user_email' => $email,
                    'date_joined' => $time,
                    'user_disabled' => 0
                ));
            }

            try {
                $MYSQL->query('INSERT INTO {prefix}users (username, user_password, user_email, date_joined, user_disabled) VALUES (:username, :user_password, :user_email, :date_joined, :user_disabled)');

                if ($LAYER->data['register_email_activate'] == "1") {

                    $send = $MAIL->setTo($email, $username)
                        ->setSubject($LANG['email']['forgot_password']['subject'])
                        ->addGenericHeader('X-Mailer', 'PHP/' . phpversion())
                        ->addGenericHeader('Content-Type', 'text/html; charset="utf-8"')
                        ->setMessage(
                            str_replace(
                                array(
                                    '%site_name%',
                                    '%activate_url%'
                                ),
                                array(
                                    $LAYER->data['site_name'],
                                    SITE_URL . '/members.php/activate/code/' . $time
                                ),
                                $LANG['email']['register']['content']
                            )
                        )
                        ->setWrap(100)
                        ->send();

                    if ($send) {

                        $notice .= $LAYER->tpl->entity(
                            'success_notice',
                            'content',
                            $LANG['bb']['members']['register_successful_email']
                        );
                    } else {
                        throw new Exception ($LANG['bb']['members']['error_register']);
                    }
                } else {
                    $MYSQL->bind('username', $username);
                    $l_q = $MYSQL->query('SELECT * FROM {prefix}users WHERE username = :username');
                    $LAYER->sess->assign($l_q['0']['user_email'], true);
                    header('refresh:3;url=' . SITE_URL . '/forum.php');
                    $notice .= $LAYER->tpl->entity(
                        'success_notice',
                        'content',
                        $LANG['bb']['members']['register_successful']
                    );
                }

            } catch (mysqli_sql_exception $e) {
                throw new Exception ($LANG['bb']['members']['error_register']);
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
    $LANG['bb']['members']['register'],
    '#',
    true
);
$content .= $LAYER->tpl->breadcrumbs();

//Form Values
$username_value = (isset($_SESSION['register_form_username'])) ? $_SESSION['register_form_username'] : '';
$email_value = (isset($_SESSION['register_form_email'])) ? $_SESSION['register_form_email'] : '';

if ($LAYER->data['register_enable'] == 1) {
    $content .= $LAYER->tpl->entity(
        'register_form',
        array(
            'notice',
            'csrf_field',
            'username_field_name',
            'password_field_name',
            'password_a_field_name',
            'email_field_name',
            'captcha',
            'submit_name',
            'register_notice',
            'form_username_value',
            'form_email_value'
        ),
        array(
            $notice,
            $FORM->build('hidden', '', 'csrf_token', array('value' => CSRF_TOKEN)),
            'username',
            'password',
            'a_password',
            'email',
            $LAYER->captcha->display(),
            'register',
            $LANG['bb']['members']['register_message'],
            $username_value,
            $email_value
        )
    );

    //Resetting the form values.
    unset($_SESSION['register_form_username']);
    unset($_SESSION['register_form_email']);
} else {
    $content .= $LAYER->tpl->entity(
        'danger_notice',
        'content',
        $LANG['bb']['members']['register_disabled']
    );
}

?>