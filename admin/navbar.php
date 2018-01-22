<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.0                 //
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
                 'New navbar item has been added!',
                 'success'
             );
             break;
         case "edit_success":
             $notice .= $ADMIN->alert(
                 'Navbar menu item has been successfully edited!',
                 'success'
             );
             break;
     }
 }

/*
 * Delete User
 */
 if ($PGET->g('delete_nav')) {
     $d_n = clean($PGET->g('delete_nav'));
     /*$MYSQL->where('id', $d_u);
     $query = $MYSQL->get('{prefix}usergroups');*/
     $MYSQL->bind('id', $d_n);
     $query = $MYSQL->query('SELECT * FROM {prefix}nav WHERE id = :id');

     if (!empty($query)) {

         //$MYSQL->where('id', $d_u);
         $MYSQL->bind('id', $d_n);
         try {
             //$MYSQL->delete('{prefix}usergroups');
             $MYSQL->query('DELETE FROM {prefix}nav WHERE id = :id');
             $notice .= $ADMIN->alert(
                 'Navbar item <strong>' . $query['0']['title'] . '</strong> has been deleted!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error deleting navbar item.',
                 'danger'
             );
         }

     } else {
         $notice .= $ADMIN->alert(
             'Navbar item does not exist!',
             'danger'
         );
     }
 }

 if (isset($_POST['savechange'])) {

    NoCSRF::check('csrf_token', $_POST);
     $id = clean($_POST['id']);
     $title = clean($_POST['title']);
     $url = clean($_POST['url']);
     $newpage = clean($_POST['newpage']);
     $order = clean($_POST['order']);
     
     $MYSQL->bind('id', $id);
     $MYSQL->bind('title', $title);
     $MYSQL->bind('url', $url);
     $MYSQL->bind('newpage', $newpage);
     $MYSQL->bind('order', $order);                
    
     try {
             $MYSQL->query('UPDATE {prefix}nav SET title = :title,
                                                    url = :url,
                                                    newpage = :newpage,
                                                    ordernav = :order
                                                    WHERE id = :id');
             $notice .= $ADMIN->alert(
                 'Navbar item edited!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error edited navbar item.',
                 'danger'
             );
         }
 }

 if (isset($_POST['newitem'])) {

    NoCSRF::check('csrf_token', $_POST);
     $title = clean($_POST['title']);
     $url = clean($_POST['url']);
     $newpage = clean($_POST['newpage']);
     $order = clean($_POST['order']);
     
     $MYSQL->bind('title', $title);
     $MYSQL->bind('url', $url);
     $MYSQL->bind('newpage', $newpage);
     $MYSQL->bind('order', $order);                
    
     try {
             $MYSQL->query('INSERT INTO `{prefix}nav` (url, newpage, title, ordernav) VALUES (:url, :newpage, :title, :order);');
             $notice .= $ADMIN->alert(
                 'Navbar item added!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error adding navbar item.',
                 'danger'
             );
         }
 }

$query = $MYSQL->query("SELECT * FROM {prefix}nav");

$token = NoCSRF::generate('csrf_token');
$nav = '';
foreach ($query as $n) {
    if($n['newpage']==1){
        $newpage = 'YES';
    } else {
        $newpage = 'NO';
    }
    $nav .= '<tr>
                        <td>
                          <strong>' . $n['title'] . '</strong>
                        </td>
                        <td>
                          ' . $n['url'] . '
                        </td>
                        <td>
                          ' . $newpage . '
                        </td>
                        <td>
                          <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                              Options <span class="caret"></span>
                            </button>
                            <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                            <ul class="dropdown-menu dropdown-inverse" role="menu">
                              <li><a href="#" data-toggle="modal" data-target="#editNav-' . $n['id'] . '">Edit Item</a></li>
                              <li><a href="' . SITE_URL . '/admin/navbar.php/delete_nav/' . $n['id'] . '">Delete Item</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>';
    $nav .= '<div class="modal fade bs-example-modal-sm" id="editNav-' . $n['id'] . '" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editing <b>'.$n['title'].'</b> Navbar Item</h4>
      </div>
      <div class="modal-body">
        <form method="POST" ACTION="">
        <input type="hidden" name="csrf_token" value="' . $token . '">
        <input type="hidden" name="id" value="' . $n['id'] . '">
        <div class="form-group">
    <label for="title">URL Title</label>
    <input type="text" class="form-control" id="title" name="title" value="'.$n['title'].'">
  </div>
  <div class="form-group">
    <label for="url">URL</label>
    <input type="text" class="form-control" id="url" name="url"  value="'.$n['url'].'">
  </div>
  <div class="form-group">
  <label for="newpage">Open URL in new page</label>
  <select class="form-control" id="newpage" name="newpage">
  <option value="' . $n['newpage'] . '">Current - Do Not Change</option>
  <option value="1">Yes</option>
  <option value="0">No</option>
</select>
</div>
  <div class="form-group">
    <label for="order">Order</label>
    <input type="text" class="form-control" id="order" name="order" value="'.$n['ordernav'].'">
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
    'Navbar Items  <p class="pull-right"><a href="#" data-toggle="modal" data-target="#newnav" class="btn btn-default btn-xs">New Item</a></p>',
    $notice .
    'You can manage the custom navbar items here..',
    '<table class="table table-hover">
         <thead>
           <tr>
              <th style="width:20%">Title</th>
              <th style="width:40%">URL</th>
              <th style="width:20%">New Page</th>
              <th style="width:20%">Controls</th>
            </tr>
         </thead>
         <tbody>
           ' . $nav . '
        </tbody>
       </table>

<div class="modal fade bs-example-modal-sm" id="newnav" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">New Navbar Item</h4>
      </div>
      <div class="modal-body">
        <form method="POST" ACTION="">
        <input type="hidden" name="csrf_token" value="' . $token . '">
        <div class="form-group">
    <label for="title">URL Title</label>
    <input type="text" class="form-control" id="title" name="title" placeholder="Enter a title for your URL">
  </div>
  <div class="form-group">
    <label for="url">URL</label>
    <input type="text" class="form-control" id="url" name="url" placeholder="Enter a URL">
  </div>
  <div class="form-group">
  <label for="newpage">Open URL in new page</label>
  <select class="form-control" id="newpage" name="newpage">
  <option value="1">Yes</option>
  <option value="0">No</option>
</select>
</div>
  <div class="form-group">
    <label for="order">Order</label>
    <input type="text" class="form-control" id="order" name="order" placeholder="Order your URL">
  </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" name="newitem" id="newitem" class="btn btn-primary">Save Nav Item</button>
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
