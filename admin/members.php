<?php

define('BASEPATH', 'Staff');
require_once('../applications/wrapper.php');

if (!$LAYER->perm->check('access_administration')) {
    redirect(SITE_URL);
}//Checks if user has permission to create a thread.
//require_once('template/top.php');
echo $ADMIN->template('top');
$notice = '';

/*
 * Additional notice.
 */
if ($PGET->g('notice')) {
     switch ($PGET->g('notice')) {
         case "create_success":
             $notice .= $ADMIN->alert(
                 'Usergroup has been created!',
                 'success'
             );
             break;
         case "edit_success":
             $notice .= $ADMIN->alert(
                 'Usergroup has been successfully edited!',
                 'success'
             );
             break;
     }
 }

/*
 * Delete User
 */
 if ($PGET->g('delete_user')) {
     $d_u = clean($PGET->g('delete_user'));
     /*$MYSQL->where('id', $d_u);
     $query = $MYSQL->get('{prefix}usergroups');*/
     $MYSQL->bind('id', $d_u);
     $query = $MYSQL->query('SELECT * FROM {prefix}users WHERE id = :id');

     if (!empty($query)) {

         //$MYSQL->where('id', $d_u);
         $MYSQL->bind('id', $d_u);
         try {
             //$MYSQL->delete('{prefix}usergroups');
             $MYSQL->query('DELETE FROM {prefix}users WHERE id = :id');
             $notice .= $ADMIN->alert(
                 'User <strong>' . $query['0']['username'] . '</strong> has been deleted!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error deleting user.',
                 'danger'
             );
         }

     } else {
         $notice .= $ADMIN->alert(
             'User does not exist!',
             'danger'
         );
     }
 }

$query = $MYSQL->query("SELECT * FROM {prefix}users");

$token = NoCSRF::generate('csrf_token');
$users = '';
foreach ($query as $u) {
    $users .= '<tr>
                        <td>
                          <strong>' . $u['username'] . '</strong>
                        </td>
                        <td>
                          <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                              Options <span class="caret"></span>
                            </button>
                            <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                            <ul class="dropdown-menu dropdown-inverse" role="menu">
                              <li><a href="' . SITE_URL . '/admin/edit_user.php/id/' . $u['id'] . '">Edit User</a></li>
                              <li><a href="' . SITE_URL . '/admin/members.php/delete_user/' . $u['id'] . '">Delete User</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>';
}

echo $ADMIN->box(
    'Users  <p class="pull-right"><a href="' . SITE_URL . '/admin/new_user.php" class="btn btn-default btn-xs">New User</a></p>',
    $notice .
    'You can manage the users here.',
    '<table class="table table-hover">
         <thead>
           <tr>
              <th style="width:80%">User</th>
              <th style="width:20%">Controls</th>
            </tr>
         </thead>
         <tbody>
           ' . $users . '
        </tbody>
       </table>',
    '12'
);

//require_once('template/bot.php');
echo $ADMIN->template('bot');
?>
