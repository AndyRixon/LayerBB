<?php

define('BASEPATH', 'Staff');
require_once('../applications/wrapper.php');

if (!$LAYER->perm->check('access_administration')) {
    redirect(SITE_URL);
}//Checks if user has permission to create a thread.
//require_once('template/top.php');
echo $ADMIN->template('top');

echo '<div class="col-md-12">
        <div class="page-header">
          <h1>Administration Panel</h1>
        </div>
      </div></div>';

      $getversions = file_get_contents('https://www.layerbb.com/checkversion.php');
      $version = explode('|', $getversions);
      if (version_compare(LayerBB_VERSION, $version[0], '<'))
      { 
        $alert = $ADMIN->alert('<p>New version found: ' . $version[0] . '<br /><a href=" ' . $version[1] . '" target="_blank" class="btn btn-primary">&raquo; Download Now?</a></p>', 'warning');
      }


/*$versions = @file_get_contents('https://www.layerbb.com/version_list.php');
if ($versions != '') {
    $versionList = explode("|", $versions);
    foreach ($versionList as $version) {
        if (version_compare(LayerBB_VERSION, $version, '<')) {
            $alert = $ADMIN->alert('<p>New version found: ' . $version . '<br /><a href="https://github.com/AndyRixon/LayerBB/releases" target="_blank">&raquo; Download Now?</a></p>', 'warning');
        }
    }
}*/

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
if (file_exists('../update.php')) {
    echo "<div class='alert alert-danger' role='alert'>
  <b>Security Alert:</b> You have not deleted the update.php file, this could potentially impact the security of your forum. Please remove the update.php file!
</div>";
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
             <td>' . stat_threads() . '</td>
           </tr>
          <tr>
             <td>Posts</td>
             <td>' . stat_posts() . '</td>
           </tr>
           <tr>
             <td>Users</td>
             <td>' . stat_users() . '</td>
           </tr>
        </tbody>
       </table>'
);

$getnews=simplexml_load_file("https://api.layerbb.com/newsapi.php");
$newsreader = '<div class="list-group">';
foreach($getnews as $news) {
  $newsreader .='<a href="#" class="list-group-item"data-toggle="modal" data-target="#More-'.$news->id.'"><h4 class="list-group-item-heading">'.$news->title.'</h4>
    <p class="list-group-item-text">'.$news->short.'</p></a>
    <div class="modal fade" id="More-'.$news->id.'" tabindex="-1" role="dialog" aria-labelledby="More-'.$news->id.'">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="More-'.$news->id.'">'.$news->title.'</h4>
      </div>
      <div class="modal-body">
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
    'LayerBB News Feed',
    'Get all the latest news & updates from LayerBB.',
    $newsreader
);

//require_once('template/bot.php');
echo $ADMIN->template('bot');

?>
