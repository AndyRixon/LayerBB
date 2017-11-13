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
      </div>';

$versions = @file_get_contents('http://layerbb.com/version_list.php');
if ($versions != '') {
    $versionList = explode("|", $versions);
    foreach ($versionList as $version) {
        if (version_compare(LayerBB_VERSION, $version, '<')) {
            $alert = $ADMIN->alert('<p>New version found: ' . $version . '<br /><a href="' . SITE_URL . '/admin/update.php?doUpdate=true&step=1">&raquo; Download Now?</a></p>', 'warning');
        }
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

echo $ADMIN->box(
    'Github and Updates',
    'Fork LayerBB on Github <a href="https://github.com/Codetana/LayerBB">here</a>.<br />
       To keep up with the updates on LayerBB, you can fork/watch the LayerBB Github repository or visit our website at <a href="http://LayerBB.com">LayerBB.Com</a> regularly!'
);

//require_once('template/bot.php');
echo $ADMIN->template('bot');

?>
