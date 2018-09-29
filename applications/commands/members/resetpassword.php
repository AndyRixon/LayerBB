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

$page_title = 'Reset Password';
$content = '';
$notice = '';

if (isset($_POST['reset'])) {
    try {
        foreach ($_POST as $parent => $child) {
            $_POST[$parent] = clean($child);
        }
        $token = clean($PGET->g('token'));
        


        NoCSRF::check('csrf_token', $_POST);

        if($_POST['password']==$_POST['a_password']){
            if($_POST['a_password']==$_POST['password']){
                $password = encrypt($_POST['password']);
                $MYSQL->bind('token', $token);
                $query = $MYSQL->query("SELECT * FROM {prefix}password_reset_requests WHERE reset_token = :token");
                $MYSQL->bind('user', $query['0']['user']);
                $MYSQL->bind('hashpass', $password);               
                $resetpass = $MYSQL->query("UPDATE {prefix}users SET user_password = :hashpass WHERE id = :user");
                if ($resetpass) {
                    $MYSQL->query("DELETE FROM {prefix}password_reset_requests WHERE user = ".$query['0']['user']."");
                    $notice .= $LAYER->tpl->entity(
                        'success_notice',
                        'content',
                        'Your password has been reset successfully, you can now login with your new password!'
                    );
                } else {
                    throw new Exception ('There was an issue resetting your password!');
                }
            } else {
                throw new Exception ('The passwords did not match!');
            }
        } else {
            throw new Exception ('The passwords did not match!');
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
    'Reset Password',
    '#',
    true
);
$bc = $LAYER->tpl->breadcrumbs();

$content .= '<form action="" method="POST" id="LAYER_form">
    <label for="csrf_token"></label>
                      <input type="hidden" name="csrf_token" id="csrf_token" value="'.CSRF_TOKEN.'" />
    <label for="password">Password</label>
    <input type="password" name="password" id="password" class="form-control"/>
    <label for="a_password">Confirm Password</label>
    <input type="password" name="a_password" id="a_password" class="form-control"/>
    <br/><br/>
    <input type="submit" name="reset" value="Reset Password" class="btn btn-default"/>
</form>
';

$content = $bc . $notice . $content;
