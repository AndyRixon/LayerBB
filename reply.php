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
$notice = '';
$content = '';

function alt_csrf()
{
    $return = sha1(sha1(uniqid() . time() . uniqid()));
    $_SESSION['LAYER_alt_csrf'] = $return;
    return $return;
}

if ($PGET->s(true)) {

    $thread = $PGET->s(true);
    $MYSQL->bindMore(
        array(
            'id' => $thread['id'],
            'title_friendly' => $thread['value']
        )
    );
    $query = $MYSQL->query("SELECT * FROM
                            {prefix}forum_posts
                            WHERE
                            post_type = 1
                            and
                            id = :id
                            AND
                            title_friendly = :title_friendly");

    if (!empty($query)) {
        $node = node($query['0']['origin_node']);

        $allowed = explode(',', $node['allowed_usergroups']);

        if (!in_array($LAYER->sess->data['user_group'], $allowed)) {
            redirect(SITE_URL . '/404.php');
        }

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
                    SITE_URL . '/node.php/' . $node['name_friendly'] . '.' . $node['id'],
                    $node['node_name']
                )
            );

        } elseif ($node['node_type'] == 1) {

            $ori_cat = category($node['in_category']);

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
        }
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
        $breadcrumbs .= $LAYER->tpl->entity(
            'breadcrumbs_current',
            'name',
            $LANG['bb']['new_reply_breadcrumb']
        );

        $breadcrumbs = $LAYER->tpl->entity(
            'breadcrumbs',
            'bread',
            $breadcrumbs
        );

        $q_query = false;
        if ($PGET->g('quote')) {
            $MYSQL->bind('id', $PGET->g('quote'));
            $q_query = $MYSQL->query('SELECT * FROM {prefix}forum_posts WHERE id = :id');
        }

        if (isset($_POST['reply'])) {
            try {

                if (!empty($q_query)) {
                    if ($_POST['csrf_token'] !== $_SESSION['LAYER_alt_csrf']) {
                        throw new Exception ('Invalid CSRF token!');
                    }
                } else {
                    NoCSRF::check('csrf_token', $_POST, true, 60*10, true);
                }

                $cont = emoji_to_text($_POST['content']);
                $cont = (!empty($q_query)) ? '[quote]' . $PGET->g('quote') . '[/quote]' . $cont : $cont;
                $cont = preg_replace('#\\[quote\\]Post ID: (.*?)\\[/quote\\]#uis', '[quote]\\1[/quote]', $cont);

                $MYSQL->bind('post_user', $LAYER->sess->data['id']);
                $c_query = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE post_user = :post_user ORDER BY post_time DESC LIMIT 1");

                if (!$cont) {
                    throw new Exception ($LANG['global_form_process']['all_fields_required']);
                } elseif ($c_query['0']['post_content'] == $cont) {
                    throw new Exception ($LANG['global_form_process']['different_message_previous']);
                } else {
                    $origin = thread($thread['id']);
                    /*
                     * Notify the watchers of the thread.
                     */
                    $watchers = array_filter(explode(',', $query['0']['watchers']));
                    if (!empty($watchers)) {
                        foreach ($watchers as $watcher) {
                            $user = $LAYER->user($watcher);
                            if (!empty($user) && $user['id'] !== $LAYER->sess->data['id']) {
                                $LAYER->user->notifyUser(
                                    'reply',
                                    $user['id'],
                                    true,
                                    array(
                                        'username' => $LAYER->sess->data['username'],
                                        'thread_title' => $query['0']['post_title'],
                                        'link' => SITE_URL . '/thread.php/' . $origin['title_friendly'] . '.' . $origin['id']
                                    )
                                );
                            }
                        }
                    }

                    /*
                     * Mentions
                     */
                    preg_match_all('/@(\w+)/', $cont, $mentions);
                    $mentions = array_filter(array_unique($mentions['1']));
                    if (!empty($mentions)) {
                        foreach ($mentions as $mention) {
                            $user = $LAYER->user($mention);
                            $LAYER->user->notifyUser(
                                'mention',
                                $user['id'],
                                true,
                                array(
                                    'username' => $LAYER->sess->data['username'],
                                    'link' => SITE_URL . '/thread.php/' . $origin['title_friendly'] . '.' . $origin['id']
                                )
                            );
                        }
                    }
                    $time = time();

                    //Double Posting
                    $o_data = '1';
                    $o_query = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE origin_thread = {$thread['id']} ORDER BY post_time DESC LIMIT 1");
                    $p_query = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE origin_thread = {$thread['id']} ORDER BY post_time DESC");
                    if ($o_query && $o_query['0']['post_user'] == $LAYER->sess->data['id'] && $LAYER->data['post_merge'] == 1) {
                        $t_cont = $o_query['0']['post_content'] . '
                                    ' . $LANG['flat']['merge_post'] . '
                                    ' . $cont;

                        $MYSQL->bind('id', $o_query['0']['id']);
                        $MYSQL->bind('post_content', $t_cont);

                        try {
                            $MYSQL->query('UPDATE {prefix}forum_posts SET post_content = :post_content WHERE id = :id');

                            $MYSQL->bind('id', $thread['id']);
                            $MYSQL->bind('last_updated', $time);
                            try {
                                $MYSQL->query('UPDATE {prefix}forum_posts SET last_updated = :last_updated WHERE id = :id');
                                redirect(SITE_URL . '/thread.php/' . $origin['title_friendly'] . '.' . $origin['id']);
                            } catch (mysqli_sql_exception $e) {
                                redirect(SITE_URL . '/thread.php/' . $origin['title_friendly'] . '.' . $origin['id']);
                            }
                        } catch (mysqli_sql_exception $e) {
                            throw new Exception ($LANG['global_form_process']['error_replying_thread']);
                        }

                    } else {
                        $MYSQL->bindMore(
                            array(
                                'post_content' => $cont,
                                'post_time' => $time,
                                'post_user' => $LAYER->sess->data['id'],
                                'origin_node' => $origin['origin_node'],
                                'origin_thread' => $thread['id'],
                                'post_type' => '2'
                            )
                        );
                        $MYSQL->query("INSERT INTO {prefix}forum_posts (post_content, post_time, post_user, origin_node, origin_thread, post_type) VALUES (:post_content, :post_time, :post_user, :origin_node, :origin_thread, :post_type)");
                        $MYSQL->bindMore(
                            array(
                                'last_updated' => $time,
                                'id' => $thread['id']
                            )
                        );

                        $page = '';
                        if (count($p_query) / POST_RESULTS_PER_PAGE > 1) {
                            $page .= '/page/' . ceil(count($p_query) / POST_RESULTS_PER_PAGE);
                        }
                        $MYSQL->query("UPDATE {prefix}forum_posts SET last_updated = :last_updated WHERE id = :id");
                        redirect(SITE_URL . '/thread.php/' . $origin['title_friendly'] . '.' . $origin['id'] . $page);

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

        if (!empty($q_query)) {
            define('CSRF_TOKEN', alt_csrf());
        } else {
            define('CSRF_TOKEN', NoCSRF::generate('csrf_token'));
        }

        $quote_user = (!empty($q_query)) ? $LAYER->user($q_query['0']['post_user']) : '';
        $quote_post = (!empty($q_query)) ? $LAYER->tpl->entity(
            'quote_post',
            array(
                'quoted_post_content',
                'quoted_post_user'
            ),
            array(
                $LAYER->lib_parse->parse($q_query['0']['post_content']),
                $quote_user['username']
            )
        ) : '';

        $content = $LAYER->tpl->entity(
            'reply_thread_page',
            array(
                'breadcrumbs',
                'quote_post',
                'form_id',
                'csrf_input',
                'create_thread_form_action',
                'editor_id',
                'textarea_name',
                'submit_name',
                'thread_url'
            ),
            array(
                $breadcrumbs,
                $quote_post,
                'LAYER_form',
                '<input type="hidden" name="csrf_token" value="' . CSRF_TOKEN . '" />',
                '',
                'editor',
                'content',
                'reply',
                SITE_URL . '/thread.php/' . $query['0']['title_friendly'] . '.' . $query['0']['id']
            )
        );


        $LAYER->tpl->addParam(
            array(
                'page_title',
                'content'
            ),
            array(
                $LANG['bb']['new_reply_in'] . ' ' . $query['0']['post_title'],
                $notice . $content
            )
        );

    } else {
        redirect(SITE_URL . '/404.php');
    }

} else {
    redirect(SITE_URL . '/404.php');
}

echo $LAYER->tpl->output();

?>
