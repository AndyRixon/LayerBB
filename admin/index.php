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
}
echo $ADMIN->template('top');

/*echo '<div class="col-md-12">
        <div class="page-header">
          <h1>Administration Panel</h1>
        </div>
      </div>';*/

      $v=simplexml_load_file("https://api.layerbb.com/api.php?cmd=version");
      if (version_compare(LayerBB_VERSION, $v->versioning[0]->human, '<')) { 
          $alert = $ADMIN->alert('<p>New version found: ' . $v->versioning[0]->human . ' (' . $v->versioning[0]->system . ')<br /><a href=" ' . $v->versioning[0]->link . '" target="_blank" class="btn btn-primary">&raquo; Download Now?</a></p>', 'warning');
      }

if ($LAYER->data['site_enable'] == 0) {
    echo "<div class='alert alert-danger' role='alert'>
  <b>Forum Offline:</b> Your forum has been disabled, this can be changed by going to the <a href='general.php'>general settings</a>.
</div>";
}

if (file_exists('../install')) {
    echo "<div class='alert alert-danger' role='alert'>
  <b>Security Alert:</b> You have not deleted the install directory, this could potentially impact the security of your forum. Please remove the install directory!
</div>";
}
if (file_exists('../update')) {
    echo "<div class='alert alert-danger' role='alert'>
  <b>Security Alert:</b> You have not deleted the update directory, this could potentially impact the security of your forum. Please remove the update directory!
</div>";
}

if (isset($_POST['updateboard'])) {
  $MYSQL->bind('board', clean($_POST['whiteboard']));
  $MYSQL->query("UPDATE `{prefix}generic` SET `whiteboard` = :board WHERE `id` = 1;");
  $alert = $ADMIN->alert('AdminCP Whiteboard has been successfully updated.', 'success');
}
$notice = '';
if (isset($_POST['run'])) {
    try {

        foreach ($_POST as $parent => $child) {
            $_POST[$parent] = clean($child);
        }

        $cmd = '/' . $_POST['command'];

        if (!$cmd) {
            throw new Exception ('Please enter a command.');
        } else {

            if ($handle = opendir('../applications/terminal')) {
                while (false !== ($entry = readdir($handle))) {
                    if ($entry !== "." && $entry !== ".." && $entry !== 'index.html') {
                        require_once('../applications/terminal/' . $entry);
                    }
                }
                closedir($handle);
            }

            list($command) = sscanf($cmd, '/%s');
            /*$MYSQL->where('command_name', $command);
            $query = $MYSQL->get('{prefix}terminal');*/
            $MYSQL->bind('command_name', $command);
            $query = $MYSQL->query('SELECT * FROM {prefix}terminal WHERE command_name = :command_name');

            if (!empty($query)) {
                $list = sscanf($cmd, '/' . $query['0']['command_syntax']);
                $command = 'terminal_' . $query['0']['run_function'];
                $run = call_user_func_array($command, $list);
                $notice .= $run;
            } else {
                throw new Exception ('Command does not exist.');
            }

        }

    } catch (Exception $e) {
        $notice .= $ADMIN->alert(
            $e->getMessage(),
            'danger'
        );
    }
}

echo $ADMIN->box(
    'Dashboard',
    'This forum is powered by LayerBB <strong>' . LayerBB_VERSION . '</strong>.' . @$alert,
    '<table class="table">
         <thead>
           <tr>
             <th>Forum Statistic</th>
              <th>Value</th>
            </tr>
         </thead>
         <tbody>
           <tr>
             <td>Threads</td>
             <td><span class="label label-primary">' . stat_threads() . '</span></td>
           </tr>
          <tr>
             <td>Posts</td>
             <td><span class="label label-primary">' . stat_posts() . '</span></td>
           </tr>
           <tr>
             <td>Users</td>
             <td><span class="label label-primary">' . stat_users() . '</span></td>
           </tr>
        </tbody>
       </table>'
);

$query = $MYSQL->query('SELECT * FROM {prefix}generic WHERE id = 1');
echo $ADMIN->box(
    'Whiteboard',
    '<form name="acpwhiteboard" method="post" action="">
  <textarea name="whiteboard" cols="100" class="form-control" rows="10" id="whiteboard" >' . $query['0']['whiteboard'] . '</textarea><br />
    <center><input name="updateboard" type="submit" class="btn btn-primary" id="updateboard" value="Update"></center>
</form>'
);

echo $ADMIN->box(
    'Terminal',
    $notice .
    '<form action="" method="POST">
         <div class="input-group">
           <span class="input-group-addon">/</span>
           <input type="text" name="command" class="form-control" placeholder="Command" />
           <span class="input-group-btn">
             <input type="submit" name="run" value="Run" class="btn btn-default" />
           </span>
         </div>
       </form>
       <br />
       You can run commands that are available in LayerBB.
       <br />
       <h4>Commands</h4>
       Change User\'s Usergroup: <code>cugroup &lt;username&gt; &lt;usergroup&gt;</code>
       <br />
       Change User\'s Display Group: <code>dugroup &lt;username&gt; &lt;usergroup&gt;</code>
       <br />
       Ban User: <code>ban &lt;username&gt;</code>
       <br />
       Unban User: <code>unban &lt;username&gt;</code>'
);


$getnews=simplexml_load_file("https://api.layerbb.com/api.php?cmd=newsfeed");
$newsreader = '<div class="list-group">';
foreach($getnews as $news) {
  $newsreader .='<a href="#" class="list-group-item"data-toggle="modal" data-target="#More-'.$news->id.'"><h4 class="list-group-item-heading">'.$news->title.'</h4>
    <p class="list-group-item-text">'.$news->short.' ('.$news->time.')</p></a>
    <div class="modal fade" id="More-'.$news->id.'" tabindex="-1" role="dialog" aria-labelledby="More-'.$news->id.'">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="More-'.$news->id.'">'.$news->title.'</h4>
      </div>
      <div class="modal-body">
      <i>Submitted on '.$news->time.'</i>
        '.$news->content.'
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
      </div>
    </div>
  </div>
</div>
    '; 
}
$newsreader .='</div>';

echo $ADMIN->box(
    'LayerBB News Feed <a href="https://api.layerbb.com/api.php?cmd=newsfeed&view=all" target="_blank" class="btn btn-default btn-xs">View Archive</a>',
    'Get all the latest news & updates from LayerBB.',
    $newsreader
);

echo $ADMIN->template('bot');

?>
