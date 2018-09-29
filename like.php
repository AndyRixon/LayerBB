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

if ($PGET->g('post')) {

    $post = clean($PGET->g('post'));
    $MYSQL->bind('id', $post);
    $query = $MYSQL->query('SELECT * FROM {prefix}forum_posts WHERE id = :id');
    $MYSQL->bind('u_id', $LAYER->sess->data['id']);
    $MYSQL->bind('p_id', $post);
    $checklike = $MYSQL->query('SELECT * FROM {prefix}likes WHERE p_id = :p_id AND u_id = :u_id');

    if (!empty($query)) {

        $notice = '';
        $content = '';
        if(empty($checklike)) {
            if($query['0']['origin_thread'] > 0){
                $MYSQL->bind('id', $query['0']['origin_thread']);
                $query1 = $MYSQL->query('SELECT * FROM {prefix}forum_posts WHERE id = :id');
                $MYSQL->bind('post', $query['0']['id']);
                $MYSQL->bind('u_id', $LAYER->sess->data['id']);
                $MYSQL->query("INSERT INTO `{prefix}likes` (`id`, `u_id`, `p_id`) VALUES (NULL, :u_id, :post);");
                $LAYER->user->notifyUser(
                                    'like',
                                    $query['0']['post_user'],
                                    true,
                                    array(
                                        'username' => $LAYER->sess->data['username'],
                                        'thread_title' => $query1['0']['post_title'],
                                        'link' => SITE_URL . '/thread.php/' . $query1['0']['title_friendly'] . '.' . $query1['0']['id'] . '#post-' . $post
                                    )
                                );
                redirect(SITE_URL . '/thread.php/' . $query1['0']['title_friendly'] . '.' . $query1['0']['id'] . '#post-' . $post);
            } else {
                $MYSQL->bind('post', $query['0']['id']);
                $MYSQL->bind('u_id', $LAYER->sess->data['id']);
                $MYSQL->query("INSERT INTO `{prefix}likes` (`id`, `u_id`, `p_id`) VALUES (NULL, :u_id, :post);");
                $LAYER->user->notifyUser(
                                    'like',
                                    $query['0']['post_user'],
                                    true,
                                    array(
                                        'username' => $LAYER->sess->data['username'],
                                        'thread_title' => $query['0']['post_title'],
                                        'link' => SITE_URL . '/thread.php/' . $query['0']['title_friendly'] . '.' . $query['0']['id']
                                    )
                                );
                redirect(SITE_URL . '/thread.php/' . $query['0']['title_friendly'] . '.' . $post);
            }
        } else {
            if($query['0']['origin_thread'] > 0) {
                $MYSQL->bind('p_id', $query['0']['origin_thread']);
                $get = $MYSQL->query('SELECT * FROM {prefix}forum_posts WHERE id = :p_id');
                //die('[debug] ' . SITE_URL . '/thread.php/' . $get['0']['title_friendly'] . '.' . $get['0']['id']);
                redirect(SITE_URL . '/thread.php/' . $get['0']['title_friendly'] . '.' . $get['0']['id'] . '#post-' . $post);
            } else {
                //die('[debug] ' . SITE_URL . '/thread.php/' . $query['0']['title_friendly'] . '.' . $query['0']['id']);
                redirect(SITE_URL . '/thread.php/' . $query['0']['title_friendly'] . '.' . $query['0']['id']);
            }
        }

    } else {
        redirect(SITE_URL . '/thread.php/' . $query['0']['title_friendly'] . '.' . $post);
    }
}


?>