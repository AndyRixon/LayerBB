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
                 'New sidebar item has been added!',
                 'success'
             );
             break;
         case "edit_success":
             $notice .= $ADMIN->alert(
                 'Sidebar menu item has been successfully edited!',
                 'success'
             );
             break;
     }
 }

/*
 * Delete User
 */
 if ($PGET->g('delete_side')) {
     $d_n = clean($PGET->g('delete_side'));
     /*$MYSQL->where('id', $d_u);
     $query = $MYSQL->get('{prefix}usergroups');*/
     $MYSQL->bind('id', $d_n);
     $query = $MYSQL->query('SELECT * FROM {prefix}sidebar WHERE id = :id');

     if (!empty($query)) {

         //$MYSQL->where('id', $d_u);
         $MYSQL->bind('id', $d_n);
         try {
             //$MYSQL->delete('{prefix}usergroups');
             $MYSQL->query('DELETE FROM {prefix}sidebar WHERE id = :id');
             $notice .= $ADMIN->alert(
                 'Sidebar item <strong>' . $query['0']['title'] . '</strong> has been deleted!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error deleting sidebar item.',
                 'danger'
             );
         }

     } else {
         $notice .= $ADMIN->alert(
             'Sidebar item does not exist!',
             'danger'
         );
     }
 }

 if (isset($_POST['savechange'])) {

    NoCSRF::check('csrf_token', $_POST, true, 60*10, true);
     $id = clean($_POST['id']);
     $title = clean($_POST['title']);
     $content = $_POST['content'];
     $style = clean($_POST['style']);
     $glyphicon = clean($_POST['glyphicon']);
     $order = clean($_POST['order']);

     $MYSQL->bind('id', $id);
     $MYSQL->bind('title', $title);
     $MYSQL->bind('content', $content);
     $MYSQL->bind('style', $style);
     $MYSQL->bind('glyphicon', $glyphicon);
     $MYSQL->bind('order', $order);                

     try {
             $MYSQL->query('UPDATE {prefix}sidebar SET title = :title,
                                                    content = :content,
                                                    style = :style,
                                                    glyphicon = :glyphicon,
                                                    sideorder = :order
                                                    WHERE id = :id');
             $notice .= $ADMIN->alert(
                 'Sidebar item edited!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error edited sidebar item.',
                 'danger'
             );
         }
 }

 if (isset($_POST['newitem'])) {

     $title = clean($_POST['title']);
     $content = $_POST['content'];
     $style = clean($_POST['style']);
     $glyphicon = clean($_POST['glyphicon']);
     $order = clean($_POST['order']);

     $MYSQL->bind('title', $title);
     $MYSQL->bind('content', $content);
     $MYSQL->bind('style', $style);
     $MYSQL->bind('glyphicon', $glyphicon);
     $MYSQL->bind('order', $order);

     try {
             $MYSQL->query('INSERT INTO `{prefix}sidebar` (title, content, style, glyphicon, sideorder) VALUES (:title, :content, :style, :glyphicon, :order);');
             $notice .= $ADMIN->alert(
                 'Sidebar item added!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error adding sidebar item.',
                 'danger'
             );
         }
 }

$query = $MYSQL->query("SELECT * FROM {prefix}sidebar");

$token = NoCSRF::generate('csrf_token');
$side = '';
foreach ($query as $n) {
    $side .= '<tr>
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
                              <li><a href="#" data-toggle="modal" data-target="#editside-' . $n['id'] . '">Edit Item</a></li>
                              <li><a href="' . SITE_URL . '/admin/sidebar.php/delete_side/' . $n['id'] . '">Delete Item</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>';
    $side .= '<div class="modal fade bs-example-modal-lg" id="editside-' . $n['id'] . '" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editing <b>'.$n['title'].'</b> sidebar Item</h4>
      </div>
      <div class="modal-body">
        <form method="POST" ACTION="">
        <input type="hidden" name="csrf_token" value="' . $token . '">
        <input type="hidden" name="id" value="' . $n['id'] . '">
        <div class="form-group">
    <label for="title">Title</label>
    <input type="text" class="form-control" id="title" name="title" value="'.$n['title'].'">
  </div>
  <div class="form-group">
    <label for="content">Content</label>
    <textarea class="form-control" name="content" id="content" style="min-height:250px;" />'.$n['content'].'</textarea>
  </div>
  <div class="form-group">
  <label for="style">Style</label>
  <select class="form-control" id="style" name="style">
  <option value="'.$n['style'].'">Current - Do Not Change</option>
  <option value="primary">Primary</option>
  <option value="success">Success</option>
  <option value="info">Info</option>
  <option value="warning">Warning</option>
  <option value="danger">Danger</option>
</select>
</div>
<div class="form-group">
    <label for="glyphicon">Glyphicon (Optional)</label>
    <input type="text" class="form-control" id="glyphicon" name="glyphicon" value="'.$n['glyphicon'].'">
  </div>
  <div class="form-group">
    <label for="order">Order</label>
    <input type="text" class="form-control" id="order" name="order" value="'.$n['sideorder'].'">
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
    'Sidebar Items  <p class="pull-right"><a href="#" data-toggle="modal" data-target="#newside" class="btn btn-default btn-xs">New Item</a></p>',
    $notice .
    'You can manage the custom sidebar items here..',
    '<table class="table table-hover">
         <thead>
           <tr>
              <th style="width:80%">Title</th>
              <th style="width:20%">Controls</th>
            </tr>
         </thead>
         <tbody>
           ' . $side . '
        </tbody>
       </table>

<div class="modal fade bs-example-modal-lg" id="newside" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">New sidebar Item</h4>
      </div>
      <div class="modal-body">
        <form method="POST" ACTION="">
        <input type="hidden" name="csrf_token" value="' . $token . '">
        <div class="form-group">
    <label for="title">Title</label>
    <input type="text" class="form-control" id="title" name="title" placeholder="Sidebar Title">
  </div>
  <div class="form-group">
    <label for="content">Content</label>
    <textarea class="form-control" name="content" id="content" style="min-height:250px;" /></textarea>
  </div>
  <div class="form-group">
  <label for="style">Style</label>
  <select class="form-control" id="style" name="style">
  <option value="default">Default</option>
  <option value="primary">Primary</option>
  <option value="success">Success</option>
  <option value="info">Info</option>
  <option value="warning">Warning</option>
  <option value="danger">Danger</option>
</select>
</div>
<div class="form-group">
    <label for="glyphicon">Glyphicon (Optional)</label>
    <input type="text" class="form-control" id="glyphicon" name="glyphicon" placeholder="(Optional: Glypicon, list found on LayerBB)">
  </div>
  <div class="form-group">
    <label for="order">Order</label>
    <input type="text" class="form-control" id="order" name="order" placeholder="Order for Sidebar Item">
  </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" name="newitem" id="newitem" class="btn btn-primary">Save Side Item</button>
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
