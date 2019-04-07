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
}//Checks if user has permission to create a thread.
//require_once('template/top.php');
echo $ADMIN->template('top');
$notice = '';

function allowed_usergroups()
{
    global $LAYER, $MYSQL;
    $return = null;
    $query = $MYSQL->query('SELECT * FROM {prefix}usergroups');
    //$return = '<input type="checkbox" name="allowed_ug[]" value="0"'.$guestcheck.' /> Guest<br />';
    foreach ($query as $u) {
        if ($u['id']) {
            $return .= '<input type="checkbox" name="allowed_ug[]" value="' . $u['id'] . '" CHECKED /> ' . $u['group_name'] . '<br />';
        }
    }
    return $return;
}

if (isset($_POST['create'])) {
    try {

        /*foreach( $_POST as $parent => $child ) {
            $_POST[$parent] = clean($child);
        }*/

        NoCSRF::check('csrf_token', $_POST, true, 60*10, true);

        $title = clean($_POST['cat_title']);
        $desc = (!$_POST['cat_desc']) ? '' : clean($_POST['cat_desc']);

        foreach ($_POST['allowed_ug'] as $ug) {
            $_POST['allowed_ug'][] = clean($ug);
        }

        $all_u = (isset($_POST['allowed_ug'])) ? implode(',', $_POST['allowed_ug']) : '0';

        if (!$title) {
            throw new Exception ('All fields are required!');
        } else {

            /*$data = array(
                'category_title' => $title,
                'category_desc' => $desc,
                'allowed_usergroups' => $all_u
            );*/
            $MYSQL->bindMore(array(
                'category_title' => $title,
                'category_desc' => $desc,
                'allowed_usergroups' => $all_u
            ));

            try {
                //$MYSQL->insert('{prefix}forum_category', $data);
                $MYSQL->query('INSERT INTO {prefix}forum_category (category_title, category_desc, allowed_usergroups) VALUES (:category_title, :category_desc, :allowed_usergroups)');
                redirect(SITE_URL . '/admin/manage_category.php/notice/create_success');
            } catch (mysqli_sql_exception $e) {
                throw new Exception ('Error creating forum category.');
            }

        }

    } catch (Exception $e) {
        $notice .= $ADMIN->alert(
            $e->getMessage(),
            'danger'
        );
    }
}

$token = NoCSRF::generate('csrf_token');

echo $ADMIN->box(
    'Create Category <p class="pull-right"><a href="' . SITE_URL . '/admin/manage_category.php" class="btn btn-default btn-xs">Back</a></p>',
    $notice .
    '<form action="" method="POST">
         <input type="hidden" name="csrf_token" value="' . $token . '">
         <label for="cat_title">Title</label>
         <input type="text" name="cat_title" id="cat_title" class="form-control" />
         <label for="cat_desc">Description</label>
         <textarea name="cat_desc" id="cat_desc" class="form-control"></textarea>
         <br />
         <label for="allowed_usergroups">Allowed Usergroups</label>
         <br />
         ' . allowed_usergroups() . '
         <br />
         <input type="submit" name="create" value="Create Category" class="btn btn-default" />
       </form>',
    '',
    '12'
);

//require_once('template/bot.php');
echo $ADMIN->template('bot');
?>
