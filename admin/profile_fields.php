<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//

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
         case "add_success":
             $notice .= $ADMIN->alert(
                 'New custom profile field has been added!',
                 'success'
             );
             break;
         case "edit_success":
             $notice .= $ADMIN->alert(
                 'Custom profile field has been successfully edited!',
                 'success'
             );
             break;
     }
 }

/*
 * Delete User
 */
 if ($PGET->g('delete_field')) {
     $d_n = clean($PGET->g('delete_field'));
     /*$MYSQL->where('id', $d_u);
     $query = $MYSQL->get('{prefix}usergroups');*/
     $MYSQL->bind('id', $d_n);
     $query = $MYSQL->query('SELECT * FROM {prefix}profile_fields WHERE id = :id');

     if (!empty($query)) {

         //$MYSQL->where('id', $d_u);
         $MYSQL->bind('id', $d_n);
         try {
             //$MYSQL->delete('{prefix}usergroups');
             $MYSQL->query('DELETE FROM {prefix}profile_fields WHERE id = :id');
             $MYSQL->bind('id', $d_n);
             $MYSQL->query('DELETE FROM {prefix}profile_field_content WHERE fieldid = :id');
             $notice .= $ADMIN->alert(
                 'Custom profile field <strong>' . $query['0']['title'] . '</strong> has been deleted!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error deleting custom profile field.',
                 'danger'
             );
         }

     } else {
         $notice .= $ADMIN->alert(
             'Custom profile field does not exist!',
             'danger'
         );
     }
 }

 if (isset($_POST['savechange'])) {

    NoCSRF::check('csrf_token', $_POST);
     $id = clean($_POST['id']);
     $title = clean($_POST['title']);
     
     $MYSQL->bind('id', $id);
     $MYSQL->bind('title', $title);               
    
     try {
             $MYSQL->query('UPDATE {prefix}profile_fields SET title = :title
                                                    WHERE id = :id');
             $notice .= $ADMIN->alert(
                 'Custom profile field edited!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error edited custom profile field.',
                 'danger'
             );
         }
 }

 if (isset($_POST['newfield'])) {

     $title = clean($_POST['title']);
     
     $MYSQL->bind('title', $title);              
    
     try {
             $MYSQL->query('INSERT INTO `{prefix}profile_fields` (title) VALUES (:title);');
             $notice .= $ADMIN->alert(
                 'Custom profile field added!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error adding custom profile field.',
                 'danger'
             );
         }
 }

$query = $MYSQL->query("SELECT * FROM {prefix}profile_fields");

$token = NoCSRF::generate('csrf_token');
$field = '';
foreach ($query as $n) {
    $field .= '<tr>
                        <td>
                          <strong>' . $n['title'] . '</strong>
                        </td>
                        <td>
                          <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                              Options <span class="caret"></span>
                            </button>
                            <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                            <ul class="dropdown-menu dropdown-inverse" role="menu">
                              <li><a href="#" data-toggle="modal" data-target="#editfield-' . $n['id'] . '">Edit Field</a></li>
                              <li><a href="' . SITE_URL . '/admin/profile_fields.php/delete_field/' . $n['id'] . '">Delete Field</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>';
    $field .= '<div class="modal fade bs-example-modal-lg" id="editfield-' . $n['id'] . '" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editing <b>'.$n['title'].'</b> Custom Profile Field</h4>
      </div>
      <div class="modal-body">
        <form method="POST" ACTION="">
        <input type="hidden" name="csrf_token" value="' . $token . '">
        <input type="hidden" name="id" value="' . $n['id'] . '">
        <div class="form-group">
    <label for="title">Title</label>
    <input type="text" class="form-control" id="title" name="title" value="'.$n['title'].'">
  </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" name="savechange" id="savechange" class="btn btn-primary">Save Changes</button>
        </form>
      </div>
    </div>
  </div>
</div>';
}

echo $ADMIN->box(
    'Custom Profile Fields  <p class="pull-right"><a href="#" data-toggle="modal" data-target="#newfield" class="btn btn-default btn-xs">New Field</a></p>',
    $notice .
    'You can manage the custom profile fields here.',
    '<table class="table table-hover">
         <thead>
           <tr>
              <th style="width:80%">Title</th>
              <th style="width:20%">Controls</th>
            </tr>
         </thead>
         <tbody>
           ' . $field . '
        </tbody>
       </table>

<div class="modal fade bs-example-modal-lg" id="newfield" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">New Profile Field</h4>
      </div>
      <div class="modal-body">
        <form method="POST" ACTION="">
        <input type="hidden" name="csrf_token" value="' . $token . '">
        <div class="form-group">
    <label for="title">Title</label>
    <input type="text" class="form-control" id="title" name="title" placeholder="Custom Profile Field Title">
  </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" name="newfield" id="newfield" class="btn btn-primary">Save Profile Field</button>
        </form>
      </div>
    </div>
  </div>
</div>
       ',
    '12'
);

//require_once('template/bot.php');
echo $ADMIN->template('bot');
?>
