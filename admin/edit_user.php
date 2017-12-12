<?php

define('BASEPATH', 'Staff');
require_once('../applications/wrapper.php');

if (!$LAYER->perm->check('access_administration')) {
    redirect(SITE_URL);
}//Checks if user has permission to create a thread.
//require_once('template/top.php');
echo $ADMIN->template('top');
$notice = '';

function list_groups()
{
    global $MYSQL;
    $query = $MYSQL->query('SELECT * FROM {prefix}usergroups');
    $return = '';
    foreach ($query as $s) {
        $checked = ($s['id'] == $check) ? ' selected' : '';
        $return .= '<option value="' . $s['id'] . '"' . $checked . '>' . $s['group_name'] . '</option>';
    }
    return $return;
}


if ($PGET->g('id')) {

    $id = clean($PGET->g('id'));
    /*$MYSQL->where('id', $id);
    $query = $MYSQL->get('{prefix}forum_node');*/
    $MYSQL->bind('id', $id);
    $query = $MYSQL->query('SELECT * FROM {prefix}users WHERE id = :id');

    if (!empty($query)) {

        if (isset($_POST['update'])) {
            try {
                NoCSRF::check('csrf_token', $_POST);

                $username = clean($_POST['username']);
                $email = clean($_POST['email']);
                $usermsg = clean($_POST['usermsg']);
                $usergroup = clean($_POST['usergroup']);

                if (!$username) {
                    throw new Exception ('All fields are required!');
                } else {
                    $MYSQL->bind('username', $username);
                    $MYSQL->bind('email', $email);
                    $MYSQL->bind('usermsg', $usermsg);
                    $MYSQL->bind('usergroup', $usergroup);
                    $MYSQL->bind('id', $id);
                    try {
                        $u_query = $MYSQL->query('UPDATE {prefix}users SET username = :username,
                                                                           user_email = :email,
                                                                           user_message = :usermsg,
                                                                           user_group = :usergroup
                                                                           WHERE id = :id');
                        redirect(SITE_URL . '/admin/members.php/notice/edit_success');
                    } catch (mysqli_sql_exception $e) {
                        throw new Exception ('Error updating node.');
                    }

                }

            } catch (Exception $e) {
                $notice .= $ADMIN->alert(
                    $e->getMessage(),
                    'danger'
                );
            }
        }

        $token = NoCSRF::generate('csrf_token');
        echo $ADMIN->box(
            'Edit User (' . $query['0']['username'] . ') <p class="pull-right"><a href="' . SITE_URL . '/admin/members.php" class="btn btn-default btn-xs">Back</a></p>',
            $notice .
            '<form action="" method="POST">
                <input type="hidden" name="csrf_token" value="' . $token . '">
                <label for="username">Username</label>
                <input type="text" name="username" id="username" value="' . $query['0']['username'] . '" class="form-control" />
                <label for="email">Email Address</label>
                <input type="text" name="email" id="email" value="' . $query['0']['user_email'] . '" class="form-control" />
                <label for="usermsg">User Message</label>
                <input type="text" name="usermsg" id="usermsg" value="' . $query['0']['user_message'] . '" class="form-control" />
                <label for="usergroup">Usergroup</label><br />
                <select name="usergroup" id="usergroup" style="width:100%;">
                <option value="' . $query['0']['user_group'] . '" selected>Dont Change</option>
                ' . list_groups() . '
                </select><br /><br />
                <input type="submit" name="update" value="Save Changes" class="btn btn-default" />
            </form>',
            '',
            '12'
        );

    } else {
        redirect(SITE_URL . '/admin');
    }

} else {
    redirect(SITE_URL . '/admin');
}

//require_once('template/bot.php');
echo $ADMIN->template('bot');
?>
