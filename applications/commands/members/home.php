<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//

/*
 * Register Module for LayerBB.
 * Everything that you want to display MUST be in the $content variable.
 */
if (!defined('BASEPATH')) {
    die();
}

$page = (!$PGET->g('page')) ? '1' : clean($PGET->g('page'));
//die($page);
$sort = clean($PGET->g('sort'));

$content = '';
$m_cont = '';

//Breadcrumb
$LAYER->tpl->addBreadcrumb(
    $LANG['bb']['forum'],
    SITE_URL . '/forum.php'
);
$LAYER->tpl->addBreadcrumb(
    $LANG['bb']['members']['home'],
    '#',
    true
);
$content .= $LAYER->tpl->breadcrumbs();

foreach (getMembers($page, $sort) as $user) {
    $p_count = $LAYER->user($user['id']);
    $m_cont .= $LAYER->tpl->entity(
        'members_page',
        array(
            'avatar',
            'username',
            'profile_url',
            'date_joined',
            'postcount'
        ),
        array(
            $p_count['user_avatar'],
            $p_count['username_style'],
            SITE_URL . '/members.php/cmd/user/id/' . $user['id'],
            date('M jS, Y', $user['date_joined']),
            $p_count['post_count']
        )
    );
}

$content .= $LAYER->tpl->entity(
    'members_page_head',
    array(
        'members',
        //Sorting
        'sort_date_joined_asc',
        'sort_date_joined_desc',
        'sort_name_asc',
        'sort_name_desc'
    ),
    array(
        $m_cont,
        //Sorting
        SITE_URL . '/members.php/sort/date_joined_asc',
        SITE_URL . '/members.php/sort/date_joined_desc',
        SITE_URL . '/members.php/sort/username_asc',
        SITE_URL . '/members.php/sort/username_desc'
    )
);

$total_pages = ceil(fetchTotalMembers() / 20);

$pag = '';
if ($page != 1 && $total_pages > 1) {
    /*$LAYER->tpl->addPagination(
        '<<',
        ($sort) ? SITE_URL . '/members.php/sort/' . $sort . '/page/' . $i : SITE_URL . '/members.php/page/' . intval($page - 1)
    );*/
    $pag .= $LAYER->tpl->entity(
        'pagination_links',
        array(
            'url',
            'page'
        ),
        array(
            ($sort) ? SITE_URL . '/members.php/sort/' . $sort . '/page/' . $i : SITE_URL . '/members.php/page/' . intval($page - 1),
            '&laquo;'
        )
    );
}
if ($total_pages > 1) {
    $i = '';
    for ($i = 1; $i <= $total_pages; ++$i) {
        if ($i <= 2 || ($i == ($page - 1) && $page > 1) || $i == $page || $i == ($page + 1) || $i >= ($total_pages - 1)) {
            $link = ($sort) ? SITE_URL . '/members.php/sort/' . $sort . '/page/' . $i : SITE_URL . '/members.php/page/' . $i;
            if ($i == $page) {
                $pag .= $LAYER->tpl->entity(
                    'pagination_link_current',
                    'page',
                    $i
                );
            } else {
                $pag .= $LAYER->tpl->entity(
                    'pagination_links',
                    array(
                        'url',
                        'page'
                    ),
                    array(
                        $link,
                        $i
                    )
                );
            }
        } elseif (($i == 3 && $page != 1) || ($i == ($total_pages - 2) && $page != $total_pages)) {
            /*$LAYER->tpl->addPagination(
                '...',
                '#'
            );*/
            $pag .= $LAYER->tpl->entity(
                'pagination_links',
                array(
                    'url',
                    'page'
                ),
                array(
                    '#',
                    '...'
                )
            );
        }
    }
}
if ($page != $total_pages && $total_pages > 1) {
    /*$LAYER->tpl->addPagination(
        '>>',
        ($sort) ? SITE_URL . '/members.php/sort/' . $sort . '/page/' . $i : SITE_URL . '/members.php/page/' . intval($page + 1)
    );*/
    $pag .= $LAYER->tpl->entity(
        'pagination_links',
        array(
            'url',
            'page'
        ),
        array(
            ($sort) ? SITE_URL . '/members.php/sort/' . $sort . '/page/' . $i : SITE_URL . '/members.php/page/' . intval($page + 1),
            '&raquo;'
        )
    );
}
$content .= $LAYER->tpl->entity(
    'pagination',
    'pages',
    $pag
);

?>
