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

if (!$LAYER->perm->check('access_moderation')) {
    redirect(SITE_URL);
}//Checks if user has permission to create a thread.
$LAYER->tpl->getTpl('page');

$content = '';

if ($PGET->g('id')) {
    $MYSQL->bind('id', $PGET->g('id'));
    $query = $MYSQL->query("SELECT * FROM {prefix}users WHERE id = :id");

    if ($PGET->g('id') == $LAYER->sess->data['id']) {
        $content .= $LAYER->tpl->entity(
                    'danger_notice',
                    'content',
                    $LANG['mod']['ban']['self_ban']
                );
        
    } else {
        if (!empty($query)) {

            if ($query['0']['is_banned'] == "0") {
                $MYSQL->bindMore(
                    array(
                        'user_group' => BAN_ID,
                        'id' => $PGET->g('id')
                    )
                );

                if($query['0']['user_group'] == ADMIN_ID) {
                     $content .= $LAYER->tpl->entity(
                                    'danger_notice',
                                    'content',
                                    $LANG['mod']['ban']['admin_ban']
                                );
                } else {
                    if ($MYSQL->query("UPDATE {prefix}users SET is_banned = 1, user_group = :user_group WHERE id = :id") > 0) {
                        $content .= $LAYER->tpl->entity(
                            'success_notice',
                            'content',
                            str_replace(
                                '%url%',
                                SITE_URL . '/members.php/cmd/user/id/' . $query['0']['id'],
                                $LANG['mod']['ban']['ban_success']
                            )
                        );
                    } else {
                        $content .= $LAYER->tpl->entity(
                            'danger_notice',
                            'content',
                            $LANG['mod']['ban']['ban_error']
                        );
                    }
                }

            } else {
                $content .= $LAYER->tpl->entity(
                    'danger_notice',
                    'content',
                    $LANG['mod']['ban']['already_banned']
                );
            }

        } else {
            redirect(SITE_URL);
        }
    }
} else {
    redirect(SITE_URL);
}

$LAYER->tpl->addParam(
    array(
        'page_title',
        'content'
    ),
    array(
        $LANG['mod']['ban']['ban'],
        $content
    )
);

echo $LAYER->tpl->output();

?>