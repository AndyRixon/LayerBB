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

if (!$LAYER->perm->check('reply_thread')) {
    redirect(SITE_URL);
}//Checks if user has permission to create a thread.
$LAYER->tpl->getTpl('page');

if ($PGET->g('post')) {

    $post_id = clean($PGET->g('post'));
    $MYSQL->bind('id', $post_id);
    $query = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE id = :id");

    if (!empty($query)) {

        if ($LAYER->perm->check('access_moderation')) {
        } elseif ($query['0']['post_user'] !== $LAYER->sess->data['id']) {
            redirect(SITE_URL);
        }

        $node = node($query['0']['origin_node']);

        $breadcrumbs = $LAYER->tpl->entity(
            'breadcrumbs_before',
            array(
                'link',
                'name'
            ),
            array(
                SITE_URL . '/forum.php',
                $LANG['bb']['forum']
            )
        );
        if ($node['node_type'] == 2) {

            $parent_node = node($node['parent_node']);
            $ori_cat = category($parent_node['in_category']);

            $breadcrumbs .= $LAYER->tpl->entity(
                'breadcrumbs_before',
                array(
                    'link',
                    'name'
                ),
                array(
                    SITE_URL . '/node.php/' . $parent_node['name_friendly'] . '.' . $parent_node['id'],
                    $parent_node['node_name']
                )
            );

            $breadcrumbs .= $LAYER->tpl->entity(
                'breadcrumbs_before',
                array(
                    'link',
                    'name'
                ),
                array(
                    '#',
                    $ori_cat['category_title']
                )
            );

            $breadcrumbs .= $LAYER->tpl->entity(
                'breadcrumbs_before',
                array(
                    'link',
                    'name'
                ),
                array(
                    SITE_URL . '/node.php/' . $node['name_friendly'] . '.' . $node['id'],
                    $node['node_name']
                )
            );

        } elseif ($node['node_type'] == 1) {

            $ori_cat = category($node['in_category']);

            $breadcrumbs .= $LAYER->tpl->entity(
                'breadcrumbs_before',
                array(
                    'links',
                    'name'
                ),
                array(
                    '#',
                    $ori_cat['category_title']
                )
            );

            $breadcrumbs .= $LAYER->tpl->entity(
                'breadcrumbs_before',
                array(
                    'link',
                    'name'
                ),
                array(
                    SITE_URL . '/node.php/' . $node['name_friendly'] . '.' . $node['id'],
                    $node['node_name']
                )
            );
        }

        if ($query['0']['post_type'] == 1) {
            $breadcrumbs .= $LAYER->tpl->entity(
                'breadcrumbs_before',
                array(
                    'link',
                    'name'
                ),
                array(
                    SITE_URL . '/thread.php/' . $query['0']['title_friendly'] . '.' . $query['0']['id'],
                    $query['0']['post_title']
                )
            );
        } elseif ($query['0']['post_type'] == 2) {
            $t = thread($query['0']['origin_thread']);
            $breadcrumbs .= $LAYER->tpl->entity(
                'breadcrumbs_before',
                array(
                    'link',
                    'name'
                ),
                array(
                    SITE_URL . '/thread.php/' . $t['title_friendly'] . '.' . $t['id'],
                    $t['post_title']
                )
            );
        }

        $breadcrumbs .= $LAYER->tpl->entity(
            'breadcrumbs_current',
            'name',
            $LANG['bb']['edit_post_breadcrumb']
        );

        $breadcrumb = $LAYER->tpl->entity(
            'breadcrumbs',
            'bread',
            $breadcrumbs
        );

        $notice = '';
        $origin_thread = '';
        $friendly_url = '';
        if ($query['0']['post_type'] == "1") {
            $page_title = $query['0']['post_title'];
            $thread_id = $query['0']['id'];
            $input_type = 'text';
        } else {
            $thread = thread($query['0']['origin_thread']);
            $page_title = $thread['post_title'];
            $thread_id = $thread['id'];
            $input_type = 'hidden';
        }

        if (isset($_POST['edit'])) {
            try {

                NoCSRF::check('csrf_token', $_POST, true, 60*10, true);

                $con = emoji_to_text($_POST['content']);
                $thread_title = clean($_POST['title']);


                if (!$con) {
                    throw new Exception ($LANG['global_form_process']['all_fields_required']);
                } else {

                    $friendly_url = title_friendly($thread_title);
                    $origin_thread .= $friendly_url . '.' . $thread_id;

                    if ($query['0']['post_type'] == "1") {

                        $MYSQL->bindMore(
                            array(
                                'post_title' => $thread_title,
                                'title_friendly' => $friendly_url,
                                'post_content' => $con,
                                'id' => $post_id
                            )
                        );
                        $u_query = $MYSQL->query("UPDATE {prefix}forum_posts SET post_title = :post_title, title_friendly = :title_friendly, post_content = :post_content WHERE id = :id");
                    } else {
                        $MYSQL->bindMore(
                            array(
                                'post_content' => $con,
                                'id' => $post_id
                            )
                        );
                        $u_query = $MYSQL->query("UPDATE {prefix}forum_posts SET post_content = :post_content WHERE id = :id");
                    }
                    if ($u_query > 0) {
                        redirect(SITE_URL . '/thread.php/' . $origin_thread);
                    } else {
                        throw new Exception ($LANG['global_form_process']['error_updating_post']);
                    }

                }

            } catch (Exception $e) {
                $notice .= $LAYER->tpl->entity(
                    'danger_notice',
                    'content',
                    $e->getMessage()
                );
            }
        }

        define('CSRF_TOKEN', NoCSRF::generate('csrf_token'));
        $content = $breadcrumb .
            '<form id="LAYER_form" action="" method="POST">
                        ' . $FORM->build('hidden', '', 'csrf_token', array('value' => CSRF_TOKEN)) . '
                        <input id="title" name="title" type="' . $input_type . '" value="' . $page_title . '" /><br />
                        <textarea id="editor" name="content" style="width:100%;height:300px;max-width:100%;min-width:100%;">' . $query['0']['post_content'] . '</textarea>
                        <br />
                        ' . $FORM->build('submit', '', 'edit', array('value' => $LANG['bb']['form']['edit_post'])) . '
                      </form>';

        $LAYER->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $LANG['bb']['edit_post_in'] . ' ' . $page_title,
                $notice . $content
            )
        );

    } else {
        redirect(SITE_URL);
    }

} else {
    redirect(SITE_URL);
}

echo $LAYER->tpl->output();

?>
