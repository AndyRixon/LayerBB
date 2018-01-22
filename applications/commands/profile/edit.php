<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.0                 //
//===================================//

/*
 * Profile edit module for LayerBB.
 */
if (!defined('BASEPATH')) {
    die();
}
if (!$LAYER->sess->isLogged) {
    redirect(SITE_URL . '/404.php');
}//Check if user is logged in.

$page_title = $LANG['bb']['profile']['personal_details'];
$content = '';
$notice = '';

if (isset($_POST['edit'])) {

    try {

        foreach ($_POST as $parent => $child) {
            $_POST[$parent] = clean($child);
        }

        NoCSRF::check('csrf_token', $_POST);
        $email = $_POST['email'];
        $tz = $_POST['timezone'];
        $about = emoji_to_text($_POST['about']);
        $birthday = $_POST['birthday'];
        $location = $_POST['location'];
        $gender = $_POST['gender'];

        if (!$email or !$tz) {
            throw new Exception ($LANG['global_form_process']['all_fields_required']); // Email and Timezone required
        } elseif (!validEmail($email)) {
            throw new Exception ($LANG['global_form_process']['invalid_email']);
        } else {

            $getfields = $MYSQL->query("SELECT * FROM {prefix}profile_fields");
            foreach ($getfields as $field) {
                $fieldid = $field['id'];
                $MYSQL->bind('fieldid', $fieldid);
                $MYSQL->bind('userid', $LAYER->sess->data['id']);
                $getfieldcontent = $MYSQL->query("SELECT * FROM {prefix}profile_field_content WHERE userid = :userid AND fieldid = :fieldid");
                $fieldcount = count($getfieldcontent);
                $field_content = $_POST[$fieldid];
                if ($fieldcount == '0' && $field_content != '') {
                    $MYSQL->bind('fieldid', $fieldid);
                    $MYSQL->bind('userid', $LAYER->sess->data['id']);
                    $MYSQL->bind('content', $field_content);
                    $MYSQL->query("INSERT INTO {prefix}profile_field_content (fieldid, content, userid) VALUES (:fieldid, :content, :userid)");
                } elseif ($fieldcount != '0' && $field_content == '') {
                    $MYSQL->bind('fieldid', $fieldid);
                    $MYSQL->bind('userid', $LAYER->sess->data['id']);
                    $MYSQL->query("DELETE FROM {prefix}profile_field_content WHERE userid = :userid AND fieldid = :fieldid");
                } elseif ($fieldcount != '0' && $field_content != '') {
                    foreach ($getfieldcontent as $fc) {
                        $MYSQL->bind('fieldid', $fieldid);
                        $MYSQL->bind('userid', $LAYER->sess->data['id']);
                        $MYSQL->bind('content', $field_content);
                        $MYSQL->query("UPDATE {prefix}profile_field_content SET content = :content WHERE userid = :userid AND fieldid = :fieldid");
                    }
                }
            }

            if ($email !== $LAYER->sess->data['user_email']) {
                if (!emailTaken($email)) {
                    $MYSQL->bindMore(
                        array(
                            'user_email' => $email,
                            'about_user' => $about,
                            'set_timezone' => $tz,
                            'user_birthday' => $birthday,
                            'location' => $location,
                            'gender' => $gender,
                            'id' => $LAYER->sess->data['id']
                        )
                    );
                    $MYSQL->query("UPDATE {prefix}users SET user_email = :user_email, about_user = :about_user, set_timezone = :set_timezone, user_birthday = :user_birthday, location = :location, gender = :gender WHERE id = :id");
                        $notice .= $LAYER->tpl->entity(
                            'success_notice',
                            'content',
                            $LANG['global_form_process']['save_success']
                        );
                    

                } else {
                    throw new Exception ($LANG['global_form_process']['email_used']);
                }

            } else {
                $MYSQL->bindMore(
                    array(
                        'set_timezone' => $tz,
                        'location' => $location,
                        'gender' => $gender,
                        'user_birthday' => $birthday,
                        'about_user' => $about,
                        'id' => $LAYER->sess->data['id']
                    )
                );

                $MYSQL->query("UPDATE {prefix}users SET set_timezone = :set_timezone, location = :location, gender = :gender, user_birthday = :user_birthday, about_user = :about_user WHERE id = :id");
                    $notice .= $LAYER->tpl->entity(
                        'success_notice',
                        'content',
                        $LANG['global_form_process']['save_success']
                    );
                

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

    $MYSQL->bind('profile_owner', $LAYER->sess->data['id']);
    $getfields = $MYSQL->query("SELECT * FROM {prefix}profile_fields");
        $fields = '<div class="panel panel-default">
  <div class="panel-heading">Additional Profile Fields</div>
  <div class="panel-body">';
        foreach ($getfields as $field) {
            $MYSQL->bind('userid', $LAYER->sess->data['id']);
            $MYSQL->bind('fieldid', $field['id']);
            $getfield = $MYSQL->query("SELECT * FROM {prefix}profile_field_content WHERE userid = :userid AND fieldid = :fieldid");
            $fields .= $FORM->build('text', $field['title'], $field['id'], array('value' => $getfield['0']['content']));
        }
        $fields .= '</div>
</div>';

$timezones = '<select id="timezone" name="timezone">';
foreach (timezones() as $timezone => $code) {
    if ($LAYER->sess->data['set_timezone'] == $code) {
        $timezones .= '<option value="' . $code . '" selected="selected">' . $timezone . '</option>';
    } else {
        $timezones .= '<option value="' . $code . '">' . $timezone . '</option>';
    }
}
$timezones .= '</select>';

$locations = '<select id="location" name="location">';
foreach ($LANG['location'] as $code => $location) {
    if ($LAYER->sess->data['location'] == $code) {
        $locations .= '<option value="' . $code . '" selected="selected">' . $location . '</option>';
    } else {
        $locations .= '<option value="' . $code . '">' . $location . '</option>';
    }
}
$locations .= '</select>';

$gender = '<select id="gender" name="gender">';
if ($LAYER->sess->data['gender'] == 0) {
    $gender .= '<option value="0" selected="selected">' . $LANG['bb']['profile']['not_telling'] . '</option>
                <option value="1">' . $LANG['bb']['profile']['female'] . '</option>
                <option value="2">' . $LANG['bb']['profile']['male'] . '</option>';
} elseif ($LAYER->sess->data['gender'] == 1) {
    $gender .= '<option value="0">' . $LANG['bb']['profile']['not_telling'] . '</option>
                <option value="1" selected="selected">' . $LANG['bb']['profile']['female'] . '</option>
                <option value="2">' . $LANG['bb']['profile']['male'] . '</option>';
} elseif ($LAYER->sess->data['gender'] == 2) {
    $gender .= '<option value="0">' . $LANG['bb']['profile']['not_telling'] . '</option>
                <option value="1">' . $LANG['bb']['profile']['female'] . '</option>
                <option value="2" selected="selected">' . $LANG['bb']['profile']['male'] . '</option>';
}
$gender .= '</select>';

//Breadcrumbs
$LAYER->tpl->addBreadcrumb(
    $LANG['bb']['forum'],
    SITE_URL . '/forum.php'
);
$LAYER->tpl->addBreadcrumb(
    $LANG['bb']['members']['home'],
    SITE_URL . '/conversations.php'
);
$LAYER->tpl->addBreadcrumb(
    $LANG['bb']['profile']['personal_details'],
    '#',
    true
);
$bc = $LAYER->tpl->breadcrumbs();
if (isset($LAYER->sess->data['user_birthday']) && $LAYER->sess->data['user_birthday'] != '0000-00-00') {
    $val_birthday = $LAYER->sess->data['user_birthday'];
} else {
    $val_birthday = 'YYYY-MM-DD';
}

$content .= '<form id="LAYER_form" action="" method="POST">
                 ' . $FORM->build('hidden', '', 'csrf_token', array('value' => CSRF_TOKEN)) . '
                 ' . $FORM->build('text', $LANG['bb']['members']['form_email'], 'email', array('value' => $LAYER->sess->data['user_email'])) . '
                 <label for="gender">' . $LANG['bb']['profile']['gender'] . '</label>
                 ' . $gender . '
                 <label for="timezone">' . $LANG['bb']['profile']['timezone'] . '</label>
                 ' . $timezones . '
                 <br />
                 <label for="location">' . $LANG['bb']['profile']['location'] . '</label>
                 ' . $locations . '
                 <br />
                 ' . $FORM->build('text', $LANG['bb']['members']['birthday'], 'birthday', array('value' => $val_birthday)) . '
                 <label for="editor">' . $LANG['bb']['profile']['about_you'] . '</label><br />
                 <textarea name="about" id="editor" style="min-width:100%;max-width:100%;height:150px;">' . $LAYER->sess->data['about_user'] . '</textarea>
                 <br />
                '.$fields.'
                 <br />
                 ' . $FORM->build('submit', '', 'edit', array('value' => $LANG['bb']['profile']['form_save'])) . '
               </form>';

$content = $bc . $notice . $content;

?>
