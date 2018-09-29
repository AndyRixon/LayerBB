<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//

define('BASEPATH', 'Forum');
require_once('applications/wrapper.php');

if (!$LAYER->perm->check('access_administration')) {
    if($LAYER->data['site_enable'] == 0) {
        redirect(SITE_URL . '/offline.php');
    }
}

if (!$LAYER->sess->isLogged) {
    redirect(SITE_URL);
}//Checks if user has permission to create a thread.

if ($PGET->g('user')) {

    $user = clean($PGET->g('user'));
    $follower = clean($PGET->g('follower'));
    $MYSQL->bind('id', $user);
    $MYSQL->bind('follower', $follower);
    $query = $MYSQL->query('SELECT * FROM {prefix}user_followers WHERE profile_owner = :id AND follower = :follower');
    //if(!$LAYER->sess->data['id']==$user) {
        if (empty($query)) {
            $notice = '';
            $content = '';
            $MYSQL->bind('id', $user);
            $MYSQL->bind('follower', $follower);
            $MYSQL->query("INSERT INTO `{prefix}user_followers` (`id`, `profile_owner`, `follower`) VALUES (NULL, :id, :follower);");
            redirect(SITE_URL . '/members.php/cmd/user/id/' . $user);
        } else {
            redirect(SITE_URL . '/members.php/cmd/user/id/' . $user);
        }
   // } else {
   //     redirect(SITE_URL . '/members.php/cmd/user/id/' . $user);
   // }
}


?>