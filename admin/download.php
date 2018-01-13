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
         case "add_success":
             $notice .= $ADMIN->alert(
                 'New download has been added!',
                 'success'
             );
             break;
         case "edit_success":
             $notice .= $ADMIN->alert(
                 ' download has been successfully edited!',
                 'success'
             );
             break;
     }
 }

/*
 * Delete User
 */
 if ($PGET->g('delete_download')) {
     $d_d = clean($PGET->g('delete_download'));
     /*$MYSQL->where('id', $d_u);
     $query = $MYSQL->get('{prefix}usergroups');*/
     $MYSQL->bind('id', $d_d);
     $query = $MYSQL->query('SELECT * FROM {prefix}downloads WHERE id = :id');

     if (!empty($query)) {

         //$MYSQL->where('id', $d_u);
         $MYSQL->bind('id', $d_d);
         try {
             //$MYSQL->delete('{prefix}usergroups');
             $MYSQL->query('DELETE FROM {prefix}downloads WHERE id = :id');
             $notice .= $ADMIN->alert(
                 'Download <strong>' . $query['0']['title'] . '</strong> has been deleted!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error deleting download.',
                 'danger'
             );
         }

     } else {
         $notice .= $ADMIN->alert(
             'Download item does not exist!',
             'danger'
         );
     }
 }

 if (isset($_POST['savechange'])) {

    NoCSRF::check('csrf_token', $_POST);
     $id = clean($_POST['id']);
     $title = clean($_POST['title']);
     $url = clean($_POST['url']);
     $description = clean($_POST['description']);
     $order = clean($_POST['order']);
     
     $MYSQL->bind('id', $id);
     $MYSQL->bind('title', $title);
     $MYSQL->bind('url', $url);
     $MYSQL->bind('description', $description);
     $MYSQL->bind('order', $order);                
    
     try {
             $MYSQL->query('UPDATE {prefix}downloads SET title = :title,
                                                    url = :url,
                                                    description = :description,
                                                    order_download = :order
                                                    WHERE id = :id');
             $notice .= $ADMIN->alert(
                 'Download edited!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error edited Download.',
                 'danger'
             );
         }
 }

 if (isset($_POST['newitem'])) {

    NoCSRF::check('csrf_token', $_POST);
     $title = clean($_POST['title']);
     $url = clean($_POST['url']);
     $description = clean($_POST['description']);
     $order = clean($_POST['order']);
     
     $MYSQL->bind('title', $title);
     $MYSQL->bind('url', $url);
     $MYSQL->bind('description', $description);
     $MYSQL->bind('order', $order);                
    
     try {
             $MYSQL->query('INSERT INTO `{prefix}downloads` (url, description, title, order_download) VALUES (:url, :description, :title, :order);');
             $notice .= $ADMIN->alert(
                 'Download added!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error adding Download.',
                 'danger'
             );
         }
 }

$query = $MYSQL->query("SELECT * FROM {prefix}downloads");

$token = NoCSRF::generate('csrf_token');
   
$down = '';
foreach ($query as $n) {
    $down .= '<tr>
                        <td>
                          <strong>' . $n['title'] . '</strong>
                        </td>
                        <td>
                          ' . $n['description'] . '
                        </td>
                        <td>
                          ' . $n['url'] . '
                        </td>
                        <td>
                          <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown">
                              Options <span class="caret"></span>
                            </button>
                            <span class="dropdown-arrow dropdown-arrow-inverse"></span>
                            <ul class="dropdown-menu dropdown-inverse" role="menu">
                              <li><a href="#" data-toggle="modal" data-target="#editDownload-' . $n['id'] . '">Edit Item</a></li>
                              <li><a href="' . SITE_URL . '/admin/download.php/delete_download/' . $n['id'] . '">Delete Item</a></li>
                            </ul>
                          </div>
                        </td>
                      </tr>';
    $down .= '<div class="modal fade bs-example-modal-sm" id="editDownload-' . $n['id'] . '" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">Editing <b>'.$n['title'].'</b> Download</h4>
      </div>
      <div class="modal-body">
        <form method="POST" ACTION="">
        <input type="hidden" name="csrf_token" value="' . $token . '">
        <input type="hidden" name="id" value="' . $n['id'] . '">
        <div class="form-group">
    <label for="title">Download Title</label>
    <input type="text" class="form-control" id="title" name="title" value="'.$n['title'].'">
  </div>
  <div class="form-group">
    <label for="description">Download Description</label>
    <input type="text" class="form-control" id="description" name="description"  value="'.$n['description'].'">
  </div>
 <div class="form-group">
    <label for="url">Download URL</label>
    <input type="text" class="form-control" id="url" name="url"  value="'.$n['url'].'">
  </div>
  <div class="form-group">
    <label for="order">Order</label>
    <input type="text" class="form-control" id="order" name="order" value="'.$n['order_download'].'">
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
    'Downloads  <p class="pull-right"><a href="#" data-toggle="modal" data-target="#newitem" class="btn btn-default btn-xs">New Item</a></p>',
    $notice .
    'You can manage downloads here.',
    '<div>

  <!-- Nav tabs -->
  <ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#home" aria-controls="home" role="tab" data-toggle="tab">Download Dashboard</a></li>
    <li role="presentation"><a href="#settings" aria-controls="settings" role="tab" data-toggle="tab">Settings</a></li>
    <li role="presentation"><a href="#downloads" aria-controls="downloads" role="tab" data-toggle="tab">Downloads</a></li>
  </ul>

 <!-- Tab panes -->
  <div class="tab-content">
    <div role="tabpanel" class="tab-pane active" id="home"><h1>Download Dashboard</h1>
      Nothing here yet...
    </div>
    <div role="tabpanel" class="tab-pane" id="settings">
<h1>Download Settings</h1>
      Nothing here yet...
    </div>
    <div role="tabpanel" class="tab-pane" id="downloads"><table class="table table-hover">
         <thead>
           <tr>
              <th style="width:20%">Title</th>
              <th style="width:40%">Description</th>
              <th style="width:20%">URL</th>
              <th style="width:20%">Controls</th>
            </tr>
         </thead>
         <tbody>
           ' . $down . '
        </tbody>
       </table></div>
  </div>

</div>

<div class="modal fade bs-example-modal-sm" id="newitem" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="myModalLabel">New Download</h4>
      </div>
      <div class="modal-body">
        <form method="POST" ACTION="">
        <input type="hidden" name="csrf_token" value="' . $token . '">
        <div class="form-group">
    <label for="title">Download Title</label>
    <input type="text" class="form-control" id="title" name="title" placeholder="Enter a title for your Download">
  </div>
  <div class="form-group">
    <label for="description">Description</label>
    <input type="text" class="form-control" id="description" name="description" placeholder="Enter a Description">
  </div>
  <div class="form-group">
    <label for="url">URL</label>
    <input type="text" class="form-control" id="url" name="url" placeholder="Enter a URL">
  </div>
  <div class="form-group">
    <label for="order">Order</label>
    <input type="text" class="form-control" id="order" name="order" placeholder="Order your Download">
  </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <button type="submit" name="newitem" id="newitem" class="btn btn-primary">Save Download</button>
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
