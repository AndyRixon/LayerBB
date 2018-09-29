<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//

/*
 * Forum class of LayerBB
 */
if (!defined('BASEPATH')) {
    die();
}

class LAYER_Forum
{

    public $parser;

    public function listings()
    {
        global $MYSQL, $LAYER;

        $return = '';
        $query = $MYSQL->query("SELECT * FROM
                                  {prefix}forum_category
                                  ORDER BY
                                  category_place
                                  ASC");
        foreach ($query as $list) {
            $allowed = explode(',', $list['allowed_usergroups']);
            if (in_array($LAYER->sess->data['user_group'], $allowed)) {
                $return .= $LAYER->tpl->entity(
                    'forum_listings_category',
                    array(
                        'category_name',
                        'category_desc',
                        'category_forums'
                    ),
                    array(
                        $list['category_title'],
                        $list['category_desc'],
                        $this->forums($list['id'])
                    )
                );
            }
        }
        return $return;
    }

    /*
     * Forums
     */
    private function forums($category)
    {
        global $MYSQL, $LAYER;

        $return = '';
        $MYSQL->bind('in_category', $category);
        $query = $MYSQL->query("SELECT * FROM {prefix}forum_node WHERE in_category = :in_category AND node_type = 1 ORDER BY node_place ASC");
        foreach ($query as $node) {
            $allowed = explode(',', $node['allowed_usergroups']);
            if (in_array($LAYER->sess->data['user_group'], $allowed)) {
                $MYSQL->bind('parent_node', $node['id']);
                $sub = $MYSQL->query("SELECT * FROM {prefix}forum_node WHERE node_type = 2 AND parent_node = :parent_node ORDER BY node_place asc");
                $subs = array();
                foreach ($sub as $suf) {
                    $allowed = explode(',', $suf['allowed_usergroups']);
                    if (in_array($LAYER->sess->data['user_group'], $allowed)) {
                        $subs[] = '<a href="' . SITE_URL . '/node.php/' . $suf['name_friendly'] . '.' . $suf['id'] . '">' . $suf['node_name'] . '</a>';
                    }
                }
                $subs = (!empty($subs)) ? implode(', ', array_slice($subs, 0, $LAYER->data['number_subs'])) : 'None';

                // New posts in node?
                $MYSQL->bind('parent_node', $node['id']);
                $sub_nodes = $MYSQL->query("SELECT id FROM {prefix}forum_node WHERE node_type = 2 AND parent_node = :parent_node");
                $nodes = array();
                array_push($nodes, $node['id']);
                if (!empty($sub_nodes)) {
                    foreach ($sub_nodes as $sub_node) {
                        array_push($nodes, $sub_node['id']);
                    }
                }

                foreach ($nodes as $node_id) {
                    $MYSQL->bind('origin_node', $node_id);
                    $posts = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE origin_node = :origin_node AND post_type = 1 ORDER BY post_time DESC");
                    $node_status = 'read';
                    if (!empty($posts)) {
                        foreach ($posts as $post) {
                            $status = $LAYER->node->thread_new_posts($post['id']);
                            if ($status == 'unread') {
                                $node_status = 'unread';
                                break;
                            }

                        }
                    }
                }


                $return .= $LAYER->tpl->entity(
                    'forum_listings_node',
                    array(
                        'node_name',
                        'node_desc',
                        'latest_post',
                        'sub_forums',
                        'status'
                    ),
                    array(
                        '<a href="' . SITE_URL . '/node.php/' . $node['name_friendly'] . '.' . $node['id'] . '">' . $node['node_name'] . '</a>',
                        $node['node_desc'],
                        $this->latestPost($node['id']),
                        $subs,
                        $node_status
                    )
                );
            }
        }
        return $return;
    }

    /*
     * Sub-Forums
     */
    public function subForums($parent_forum)
    {
        global $MYSQL, $LAYER;

        $return = '';
        $MYSQL->bind('parent_node', $parent_forum);
        $query = $MYSQL->query("SELECT * FROM {prefix}forum_node WHERE parent_node = :parent_node AND node_type = 2 ORDER BY node_place ASC");
        foreach ($query as $node) {
            $allowed = explode(',', $node['allowed_usergroups']);
            if (in_array($LAYER->sess->data['user_group'], $allowed)) {
                $return .= $LAYER->tpl->entity(
                    'forum_listings_node_sub_forums_posts',
                    array(
                        'node_name',
                        'node_desc',
                        'latest_post'
                    ),
                    array(
                        '<a href="' . SITE_URL . '/node.php/' . $node['name_friendly'] . '.' . $node['id'] . '">' . $node['node_name'] . '</a>',
                        $node['node_desc'],
                        $this->latestSubForumPost($node['id'])
                    )
                );
            }
        }

        $return = $LAYER->tpl->entity(
            'forum_listings_node_sub_forums',
            'nodes',
            $return
        );
        return $return;
    }

    /*
     * Latest Threads
     */
    private function latestPost($forum)
    {
        global $MYSQL, $LAYER;

        $MYSQL->bind('parent_node', $forum);
        $query = $MYSQL->query("SELECT * FROM {prefix}forum_node WHERE node_type = 2 AND parent_node = :parent_node");
        $where = 'NULL';
        foreach ($query as $wh) {
            $where .= ',:' . $wh['id'];
            $MYSQL->bind($wh['id'], $wh['id']);
        }
        $where .= ',:forum';
        $MYSQL->bind('forum', $forum);

        $return = '';
        $query = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE origin_node IN (" . $where . ") ORDER BY post_time DESC LIMIT 1");

        if (!empty($query)) {

            foreach ($query as $post) {
                $user = $LAYER->user($post['post_user']);

                if ($post['post_type'] == "1") {
                    $latest = (strlen($post['post_title']) > 24) ? '<a href="' . SITE_URL . '/thread.php/' . $post['title_friendly'] . '.' . $post['id'] . '" title="' . $post['post_title'] . '">' . substr($post['post_title'], 0, 24) . '...' . '</a>' : '<a href="' . SITE_URL . '/thread.php/' . $post['title_friendly'] . '.' . $post['id'] . '">' . $post['post_title'] . '</a>';
                } elseif ($post['post_type'] == "2") {
                    $MYSQL->bind('origin_thread', $post['origin_thread']);
                    $q = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE origin_thread = :origin_thread");

                    $q = (count($q) / POST_RESULTS_PER_PAGE);
                    $page = ($q > 1) ? '/page/' . ceil($q) . '/' : '';

                    $p = thread($post['origin_thread']);
                    $latest = (strlen($p['post_title']) > 24) ? '<a href="' . SITE_URL . '/thread.php/' . $p['title_friendly'] . '.' . $p['id'] . $page . '#post-' . $post['id'] . '" title="' . $p['post_title'] . '">' . substr($p['post_title'], 0, 24) . '...' . '</a>' : '<a href="' . SITE_URL . '/thread.php/' . $p['title_friendly'] . '.' . $p['id'] . $page . '#post-' . $post['id'] . '">' . $p['post_title'] . '</a>';
                }

                $post_time = simplify_time($post['post_time'], @$LAYER->sess->data['location']);
                /** Output */
                $return .= $LAYER->tpl->entity(
                    'forum_listings_node_latest',
                    array(
                        'user_avatar',
                        'latest_post',
                        'post_user',
                        'post_time'
                    ),
                    array(
                        $user['user_avatar'],
                        $latest,
                        '<a href="' . SITE_URL . '/members.php/cmd/user/id/' . $user['id'] . '">' . $user['username'] . '</a>',
                        '<span title="' . $post_time['tooltip'] . '">' . $post_time['time'] . '</span>'
                    )
                );
            }

        } else {
            $return .= 'None';
        }
        return $return;
    }

    private function latestSubForumPost($forum)
    {
        global $MYSQL, $LAYER;

        $return = '';
        $MYSQL->bind('origin_node', $forum);
        $query = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE origin_node = :origin_node ORDER BY post_time DESC LIMIT 1");
        if (!empty($query)) {

            foreach ($query as $post) {
                $user = $LAYER->user($post['post_user']);

                if ($post['post_type'] == "1") {
                    $latest = (strlen($post['post_title']) > 24) ? '<a href="' . SITE_URL . '/thread.php/' . $post['title_friendly'] . '.' . $post['id'] . '" title="' . $post['post_title'] . '">' . substr($post['post_title'], 0, 24) . '...' . '</a>' : '<a href="' . SITE_URL . '/thread.php/' . $post['title_friendly'] . '.' . $post['id'] . '">' . $post['post_title'] . '</a>';
                } elseif ($post['post_type'] == "2") {
                    $MYSQL->bind('origin_thread', $post['origin_thread']);
                    $q = $MYSQL->query("SELECT * FROM {prefix}forum_posts WHERE origin_thread = :origin_thread");

                    $q = (count($q) / POST_RESULTS_PER_PAGE);
                    $page = ($q > 1) ? '/page/' . ceil($q) . '/' : '';

                    $p = thread($post['origin_thread']);
                    $latest = (strlen($p['post_title']) > 24) ? '<a href="' . SITE_URL . '/thread.php/' . $p['title_friendly'] . '.' . $p['id'] . $page . '#post-' . $post['id'] . '" title="' . $p['post_title'] . '">' . substr($p['post_title'], 0, 24) . '...' . '</a>' : '<a href="' . SITE_URL . '/thread.php/' . $p['title_friendly'] . '.' . $p['id'] . $page . '#post-' . $post['id'] . '">' . $p['post_title'] . '</a>';
                }

                $post_time = simplify_time($post['post_time'], @$LAYER->sess->data['location']);
                /** Output */
                $return .= $LAYER->tpl->entity(
                    'forum_listings_node_sub_forums_latest',
                    array(
                        'user_avatar',
                        'latest_post',
                        'post_user',
                        'post_time'
                    ),
                    array(
                        $user['user_avatar'],
                        $latest,
                        '<a href="' . SITE_URL . '/members.php/cmd/user/id/' . $user['id'] . '">' . $user['username'] . '</a>',
                        '<span title="' . $post_time['tooltip'] . '">' . $post_time['time'] . '</span>'
                    )
                );
            }

        } else {
            $return .= 'None';
        }
        return $return;
    }

}

?>
