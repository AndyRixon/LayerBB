<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.0                 //
//===================================//

define('BASEPATH', 'Staff');
require_once('../applications/wrapper.php');

if (!$LAYER->perm->check('access_administration')) {
    redirect(SITE_URL);
}//Checks if user has permission to create a thread.
//require_once('template/top.php');
echo $ADMIN->template('top');
$notice = '';

if ($PGET->g('notice')) {
     switch ($PGET->g('notice')) {
         case "edit_success":
             $notice .= $ADMIN->alert(
                 'General settings have been successfully saved.',
                 'success'
             );
        break;
        case "remove_logo":
            $MYSQL->query('UPDATE {prefix}generic SET logo = "" WHERE id = 1');
            $path = realpath('../uploads');
            unlink($path.'/'.$LAYER->data['logo']);
            redirect(SITE_URL.'/admin/general.php');
        break;
     }
 }

function languagePackages()
{
    global $LAYER;
    $return = '';
    if ($handle = opendir('../applications/languages/')) {
        while (false !== ($entry = readdir($handle))) {
            if ($entry != "." && $entry != ".." && $entry != "index.html") {
                $explode = explode('.php', $entry);
                $checked = ($LAYER->data['site_language'] == $explode['0']) ? ' selected' : '';
                $return .= '<option value="' . $explode['0'] . '"' . $checked . '>' . ucfirst($explode['0']) . '</option>';
            }
        }
        closedir($handle);
    }
    return $return;
}

if (isset($_POST['update'])) {
    try {

        foreach ($_POST as $parent => $child) {
            $_POST[$parent] = clean($child);
        }

        NoCSRF::check('csrf_token', $_POST);

        $site_name = $_POST['site_name'];
        $board_email = $_POST['board_email'];
        $site_lang = $_POST['default_language'];
        $site_rules = $_POST['board_rules'];
        $enable_reg = (isset($_POST['register_enable'])) ? '1' : '0';
        $post_merge = (isset($_POST['post_merge'])) ? '1' : '0';
        //$flatui_ena = (isset($_POST['flatui_enable'])) ? '1' : '0';
        $number_subs = $_POST['number_subs'];
        $offline_msg = $_POST['offline_msg'];

        $fb_app_id = $_POST['fb_app_id'];
        $fb_app_sec = $_POST['fb_app_secret'];
        $enable_fb = (isset($_POST['enable_facebook'])) ? '1' : '0';

        $rcap_public = $_POST['rcap_public'];
        $rcap_private = $_POST['rcap_private'];
        $enable_rcap = (isset($_POST['enable_recaptcha'])) ? '2' : '1';
        $site_enable = (isset($_POST['site_enable'])) ? '1' : '0';

        $smtp_port = $_POST['smtp_port'];
        $smtp_user = $_POST['smtp_user'];
        $smtp_pass = $_POST['smtp_pass'];
        $smtp_add = $_POST['smtp_add'];
        if($_FILES["custom_logo"]["name"]=='') {
             $customlogo = $LAYER->data['logo'];
            //die('NO CHANGE: '.$customlogo);
        } else {
            $uploads_dir = realpath('../uploads');
            // print_r($_FILES);
            move_uploaded_file($_FILES["custom_logo"]["tmp_name"], $uploads_dir."/".basename($_FILES["custom_logo"]["name"]));
            $customlogo = basename($_FILES["custom_logo"]["name"]);
            //die('UPLOADING: '.$customlogo);
        }
        $enable_smtp = (isset($_POST['enable_smtp'])) ? '2' : '1';
        if (!$site_name or !$board_email or !$site_lang) {
            throw new Exception ('All fields are required!');
        } else {

            /*$data = array(
                'site_name' => $site_name,
                'site_email' => $board_email,
                'site_rules' => $site_rules,
                'site_language' => $site_lang,
                'register_enable' => $enable_reg,
                'post_merge' => $post_merge,
                'facebook_app_id' => $fb_app_id,
                'facebook_app_secret' => $fb_app_sec,
                'facebook_authenticate' => $enable_fb,
                'recaptcha_public_key' => $rcap_public,
                'recaptcha_private_key' => $rcap_private,
                'captcha_type' => $enable_rcap,
                'mail_type' => $enable_smtp,
                'smtp_address' => $smtp_add,
                'smtp_port' => $smtp_port,
                'smtp_username' => $smtp_user,
                'smtp_password' => $smtp_pass
            );
            $MYSQL->where('id', 1);*/
            $MYSQL->bindMore(array(
                'site_name' => $site_name,
                'site_email' => $board_email,
                'site_rules' => $site_rules,
                'site_language' => $site_lang,
                'register_enable' => $enable_reg,
                'post_merge' => $post_merge,
                'number_subs' => $number_subs,
                'facebook_app_id' => $fb_app_id,
                'facebook_app_secret' => $fb_app_sec,
                'facebook_authenticate' => $enable_fb,
                'recaptcha_public_key' => $rcap_public,
                'recaptcha_private_key' => $rcap_private,
                'captcha_type' => $enable_rcap,
                'mail_type' => $enable_smtp,
                'smtp_address' => $smtp_add,
                'smtp_port' => $smtp_port,
                'smtp_username' => $smtp_user,
                'smtp_password' => $smtp_pass,
                'site_enable' => $site_enable,
                'offline_msg' => $offline_msg,
                'logo' => $customlogo,
               // 'flat_ui_admin' => $flatui_ena
            ));

            try {
                //$MYSQL->update('{prefix}generic', $data);
                $MYSQL->query('UPDATE {prefix}generic SET site_name = :site_name,
                                                            site_email = :site_email,
                                                            site_rules = :site_rules,
                                                            site_language = :site_language,
                                                            register_enable = :register_enable,
                                                            post_merge = :post_merge,
                                                            number_subs = :number_subs,
                                                            facebook_app_id = :facebook_app_id,
                                                            facebook_app_secret = :facebook_app_secret,
                                                            facebook_authenticate = :facebook_authenticate,
                                                            recaptcha_public_key = :recaptcha_public_key,
                                                            recaptcha_private_key = :recaptcha_private_key,
                                                            captcha_type = :captcha_type,
                                                            mail_type = :mail_type,
                                                            smtp_address = :smtp_address,
                                                            smtp_port = :smtp_port,
                                                            smtp_username = :smtp_username,
                                                            smtp_password = :smtp_password,
                                                            site_enable = :site_enable,
                                                            offline_msg = :offline_msg,
                                                            logo = :logo
                                                            WHERE id = 1');
                redirect(SITE_URL . '/admin/general.php/notice/edit_success');
            } catch (mysqli_sql_exception $e) {
                throw new Exception ('Error saving information. Try again later.');
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

echo '<form action="" enctype="multipart/form-data" method="POST">';

$reg_check    = ($LAYER->data['register_enable'] == 1) ? ' CHECKED' : '';
$merge_check  = ($LAYER->data['post_merge'] == 1) ? ' CHECKED' : '';
$site_enable  = ($LAYER->data['site_enable'] == 1) ? ' CHECKED' : '';
$recaptcha_check = ($LAYER->data['captcha_type'] == "2") ? ' CHECKED' : '';
$smtp_check = ($LAYER->data['mail_type'] == 2) ? ' CHECKED' : '';
$fb_check = ($LAYER->data['facebook_authenticate'] == 1) ? ' CHECKED' : '';
if(empty($LAYER->data['logo'])) {
    $logo = '<div class="form-group">
<label for="custom_logo">Easy Logo Changer</label>
<input type="file" name="custom_logo" id="custom_logo" class="form-control">
</label>';
} else {
    $logo = '<img src="'.SITE_URL.'/uploads/'.$LAYER->data['logo'].'" width="230px" height="70px" /><br />
  <a href="'.SITE_URL.'/admin/general.php/notice/remove_logo">Remove Custom Logo</a>';
}
//$flatui_check = ($LAYER->data['flat_ui_admin'])? 'CHECKED' : '';
echo $ADMIN->box(
    'System Settings',
    'You will be able to edit all of the system settings within this section.',
    $notice .
    '<ul class="nav nav-tabs" role="tablist" style="padding-left: 20px; padding-right: 20px;">
    <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">General</a></li>
    <li role="presentation"><a href="#logo" aria-controls="logo" role="tab" data-toggle="tab">Easy Logo Changer</a></li>
    <li role="presentation"><a href="#integration" aria-controls="integration" role="tab" data-toggle="tab">Integration</a></li>
    <li role="presentation"><a href="#email" aria-controls="email" role="tab" data-toggle="tab">SMTP/Email Settings</a></li>
  </ul>
  <div class="tab-content" style="padding: 20px;">
    <div role="tabpanel" class="tab-pane active" id="general">
<input type="hidden" name="csrf_token" value="' . $token . '">
       <label for="site_name">Board Name</label>
       <input type="text" class="form-control" name="site_name" id="site_name" value="' . $LAYER->data['site_name'] . '" />
       <label for="board_email">Board Email</label>
       <input type="text" class="form-control" name="board_email" id="board_email" value="' . $LAYER->data['site_email'] . '" />
       <label for="number_subs">Number of shown subforums</label>
       <input type="text" class="form-control" name="number_subs" id="number_subs" value="' . $LAYER->data['number_subs'] . '" />
       <input type="checkbox" name="register_enable" value="1" id="reg_enable" ' . $reg_check . ' /> <label for="reg_enable">Enable Register</label><br />
       <input type="checkbox" name="post_merge" value="1" id="post_merge" ' . $merge_check . ' /> <label for="post_merge">Merge Posts (<a href="#" title="Merge consecutive posts by the same user." id="tooltip">?</a>)</label><br />
       <input type="checkbox" name="site_enable" value="1" id="site_enable" ' . $site_enable. ' /> <label for="site_enable">Forum Enabled (<a href="#" title="Allows you to enable or diable your forums." id="tooltip">?</a>)</label><br />
       <br />
       <label for="default_language">Default Languge</label><br />
       <select name="default_language" id="Default_language" class="form-control">
       ' . languagePackages() . '
       </select><br /><br />
       <label for="board_rules">Board Rules</label>
       <span id="helpBlock" class="help-block">HTML tags will be converted into ascii codes. Hyperlinks are not supported!</span>
       <textarea name="board_rules" class="form-control" style="min-height:250px;">' . $LAYER->data['site_rules'] . '</textarea><br />
       <label for="offline_msg">Offline Message</label>
       <span id="helpBlock" class="help-block">HTML tags will be converted into ascii codes.</span>
       <textarea name="offline_msg" class="form-control" style="min-height:250px;">' . $LAYER->data['offline_msg'] . '</textarea>
    </div>
    <div role="tabpanel" class="tab-pane" id="integration">
<label for="rcap_public">reCaptcha Public Key</label>
       <input type="text" name="rcap_public" id="rcap_public" class="form-control" value="' . $LAYER->data['recaptcha_public_key'] . '" />
       <label for="rcap_private">reCaptcha Private Key</label>
       <input type="text" name="rcap_private" id="rcap_private" class="form-control" value="' . $LAYER->data['recaptcha_private_key'] . '" />
       <input type="checkbox" name="enable_recaptcha" value="1"' . $recaptcha_check . ' /> Use reCaptcha<br /><br />
       <label for="fb_app_id">Facebook App ID</label>
       <input type="text" name="fb_app_id" id="fb_app_id" class="form-control" value="' . $LAYER->data['facebook_app_id'] . '" />
       <label for="fb_app_secret">Facebook App Secret</label>
       <input type="text" name="fb_app_secret" id="fb_app_secret" class="form-control" value="' . $LAYER->data['facebook_app_secret'] . '" />
       <input type="checkbox" name="enable_facebook" value="1"' . $fb_check . ' /> Enable Facebook Authentication
    </div>
    <div role="tabpanel" class="tab-pane" id="email">
<label for="smtp_add">SMTP Address</label>
       <input type="text" name="smtp_add" id="smtp_add" class="form-control" value="' . $LAYER->data['smtp_address'] . '" />
       <label for="smtp_user">SMTP Username</label>
       <input type="text" name="smtp_user" id="smtp_user" class="form-control" value="' . $LAYER->data['smtp_username'] . '" />
       <label for="smtp_pass">SMTP Password</label>
       <input type="text" name="smtp_pass" id="smtp_pass" class="form-control" value="' . $LAYER->data['smtp_password'] . '" />
       <label for="smtp_port">SMTP Port</label>
       <input type="text" name="smtp_port" id="smtp_port" class="form-control" value="' . $LAYER->data['smtp_port'] . '" />
       <input type="checkbox" name="enable_smtp" value="1"' . $smtp_check . ' /> Send email using SMTP.
    </div>

    <div role="tabpanel" class="tab-pane" id="logo">
'.$logo.'
</div>
    </div>
  </div><br />
<center><input type="submit" name="update" class="btn btn-default" value="Save Settings" /></center><br />',
'12'
);

echo '</form>';

//require_once('template/bot.php');
echo $ADMIN->template('bot');

?>
