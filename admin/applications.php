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

if ($PGET->g('activate')) {
     $id = clean($PGET->g('activate'));
     /*$MYSQL->where('id', $d_u);
     $query = $MYSQL->get('{prefix}usergroups');*/
     $MYSQL->bind('id', $id);
     $query = $MYSQL->query('SELECT * FROM {prefix}apps WHERE id = :id');

     if (!empty($query)) {

         //$MYSQL->where('id', $d_u);
         $MYSQL->bind('id', $id);
         try {
             //$MYSQL->delete('{prefix}usergroups');
             $MYSQL->query('UPDATE {prefix}apps SET active = 1 WHERE id = :id');
             $notice .= $ADMIN->alert(
                 'Application <strong>' . $query['0']['title'] . '</strong> has been activated!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error activating application.',
                 'danger'
             );
         }

     } else {
         $notice .= $ADMIN->alert(
             'Application does not exist!',
             'danger'
         );
     }
 }

 if ($PGET->g('deactivate')) {
     $id = clean($PGET->g('deactivate'));
     /*$MYSQL->where('id', $d_u);
     $query = $MYSQL->get('{prefix}usergroups');*/
     $MYSQL->bind('id', $id);
     $query = $MYSQL->query('SELECT * FROM {prefix}apps WHERE id = :id');

     if (!empty($query)) {

         //$MYSQL->where('id', $d_u);
         $MYSQL->bind('id', $id);
         try {
             //$MYSQL->delete('{prefix}usergroups');
             $MYSQL->query('UPDATE {prefix}apps SET active = 0 WHERE id = :id');
             $notice .= $ADMIN->alert(
                 'Application <strong>' . $query['0']['title'] . '</strong> has been deactivated!',
                 'success'
             );
         } catch (mysqli_sql_exception $e) {
             $notice .= $ADMIN->alert(
                 'Error deactivating application.',
                 'danger'
             );
         }

     } else {
         $notice .= $ADMIN->alert(
             'Application does not exist!',
             'danger'
         );
     }
 }

$query = $MYSQL->query("SELECT * FROM {prefix}apps");

$token = NoCSRF::generate('csrf_token');
$apps = '';
foreach ($query as $a) {
    if($a['active']==1){
        $active = '<ul class="dropdown-menu">
    <li><a href="' . SITE_URL . '/admin/applications.php/deactivate/' . $a['id'] . '">Deactivate</a></li>
    <li><a href="' . SITE_URL . '/admin/' . $a['appid'] . '.php">Settings</a></li>
  </ul>';
    } else {
        $active = '<ul class="dropdown-menu">
    <li><a href="' . SITE_URL . '/admin/applications.php/activate/' . $a['id'] . '">Activate</a></li>
  </ul>';
    }
    if($a['active']==1){
        $activated = '<p class="text-success pull-right"><b>Activated</b></p>';
    } else {
        $activated = '<p class="text-danger pull-right"><b>Deactivated</b></p>';
    }
    $apps .= '<tr>
    <td><b>'.$a['title'].' ('.$a['version'].')</b>'.$activated.'
    <p class="text-muted"><i>By '.$a['author'].'</i></p>
    <p class="text-muted">'.$a['short'].'</p></td>
    <td><div class="btn-group">
  <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
    Options <span class="caret"></span>
  </button>
  '.$active.'
</div></td>
  </tr>';
}

echo $ADMIN->box(
    'Applications',
    $notice .
    'You can manage your applications here',
    '<table class="table table-striped">
<thead>
  <tr>
    <th width="85%" scope="col">Application Information</th>
    <th width="15%" scope="col">Options</th>
  </tr>
  </thead>
         <tbody>
           ' . $apps . '
        </tbody>
       </table>',
    '12'
);

//require_once('template/bot.php');
echo $ADMIN->template('bot');
?>
