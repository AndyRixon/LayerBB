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

        NoCSRF::check('csrf_token', $_POST, true, 60*10, true);

        $site_name = $_POST['site_name'];
        $board_email = $_POST['board_email'];
        $site_lang = $_POST['default_language'];
        $site_rules = $_POST['board_rules'];
        $enable_reg = (isset($_POST['register_enable'])) ? '1' : '0';
        $post_merge = (isset($_POST['post_merge'])) ? '1' : '0';
        //$flatui_ena = (isset($_POST['flatui_enable'])) ? '1' : '0';
        $number_subs = $_POST['number_subs'];
        $offline_msg = $_POST['offline_msg'];

        $rcap_public = $_POST['rcap_public'];
        $rcap_private = $_POST['rcap_private'];
        $enable_rcap = (isset($_POST['enable_recaptcha'])) ? '2' : '1';
        $site_enable = (isset($_POST['site_enable'])) ? '1' : '0';
        $email_verify = (isset($_POST['email_verify'])) ? '1' : '0';

        $board_signature = $_POST['board_signature'];
        $enable_rtl = (isset($_POST['enable_rtl'])) ? '1' : '0';
        $enable_signatures = (isset($_POST['enable_signatures'])) ? '1' : '0';
        $enable_pcomments = (isset($_POST['enable_pcomments'])) ? '1' : '0';
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
                'recaptcha_public_key' => $rcap_public,
                'recaptcha_private_key' => $rcap_private,
                'captcha_type' => $enable_rcap,
                'site_enable' => $site_enable,
                'offline_msg' => $offline_msg,
                'board_signature' => $board_signature,
                'enable_rtl' => $enable_rtl,
                'email_verify' => $email_verify,
                'logo' => $customlogo,
                'enable_signatures' => $enable_signatures,
                'enable_pcomments' => $enable_pcomments
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
                                                            recaptcha_public_key = :recaptcha_public_key,
                                                            recaptcha_private_key = :recaptcha_private_key,
                                                            captcha_type = :captcha_type,
                                                            site_enable = :site_enable,
                                                            offline_msg = :offline_msg,
                                                            board_signature = :board_signature,
                                                            enable_rtl = :enable_rtl,
                                                            register_email_activate = :email_verify,
                                                            logo = :logo,
                                                            enable_signatures = :enable_signatures,
                                                            enable_pcomments = :enable_pcomments
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
$rtl_check  = ($LAYER->data['enable_rtl'] == 1) ? ' CHECKED' : '';
$site_enable  = ($LAYER->data['site_enable'] == 1) ? ' CHECKED' : '';
$email_verify  = ($LAYER->data['register_email_activate'] == 1) ? ' CHECKED' : '';
$recaptcha_check = ($LAYER->data['captcha_type'] == "2") ? ' CHECKED' : '';
$enable_signatures  = ($LAYER->data['enable_signatures'] == 1) ? ' CHECKED' : '';
$enable_pcomments  = ($LAYER->data['enable_pcomments'] == 1) ? ' CHECKED' : '';
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
    '<div class="nav-tabs-custom"><ul class="nav nav-tabs" role="tablist">
    <li role="presentation" class="active"><a href="#general" aria-controls="general" role="tab" data-toggle="tab">General</a></li>
    <li role="presentation"><a href="#logo" aria-controls="logo" role="tab" data-toggle="tab">Easy Logo Changer</a></li>
    <li role="presentation"><a href="#integration" aria-controls="integration" role="tab" data-toggle="tab">Integration</a></li>
    <li role="presentation"><a href="#email" aria-controls="email" role="tab" data-toggle="tab">Email Settings</a></li>
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
       <input type="checkbox" name="register_enable" value="1" id="reg_enable" ' . $reg_check . ' /> <label for="reg_enable">Enable Registeration</label><br />
       <input type="checkbox" name="post_merge" value="1" id="post_merge" ' . $merge_check . ' /> <label for="post_merge">Merge Posts (<a href="#" title="Merge consecutive posts by the same user." id="tooltip">?</a>)</label><br />
       <input type="checkbox" name="site_enable" value="1" id="site_enable" ' . $site_enable. ' /> <label for="site_enable">Forum Enabled (<a href="#" title="Allows you to enable or disable your forums." id="tooltip">?</a>)</label><br />
       <input type="checkbox" name="email_verify" value="1" id="email_verify" ' . $email_verify. ' /> <label for="email_verify">Email Verification (<a href="#" title="Allows you to enable or disable email verification." id="tooltip">?</a>)</label><br />
       <input type="checkbox" name="enable_signatures" value="1" id="enable_signatures" ' . $enable_signatures. ' /> <label for="enable_signatures">Allow user signatures (<a href="#" title="Allows you to disable user signatures." id="tooltip">?</a>)</label><br />
       <input type="checkbox" name="enable_pcomments" value="1" id="enable_pcomments" ' . $enable_pcomments. ' /> <label for="enable_pcomments">Enable Profile Comments (<a href="#" title="Allows you to disable profile comments." id="tooltip">?</a>)</label><br />
       <br />
       <label for="default_language">Default Languge</label><br />
       <select name="default_language" id="Default_language" class="form-control">
       ' . languagePackages() . '
       </select><br />
       <input type="checkbox" name="enable_rtl" value="1" id="enable_rtl" ' . $rtl_check . ' /> <label for="enable_rtl">Enable RTL (<a href="#" title="Enable Right-to-left for languages that need RTL" id="tooltip">?</a>)</label><br /><br />
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
       <input type="checkbox" name="enable_recaptcha" value="1"' . $recaptcha_check . ' /> Use reCaptcha<br />
    </div>
    <div role="tabpanel" class="tab-pane" id="email">
       <label for="content">Board Signature</label>
       <textarea id="editor" name="board_signature" class="form-control" style="min-height:250px;">' . $LAYER->data['board_signature'] . '</textarea>
       <br /><div class="alert alert-info" role="alert"><b>Please Note:</b> HTML Tags do not work, line breaks and urls are automatically converted!</div>
    </div>

    <div role="tabpanel" class="tab-pane" id="logo">
'.$logo.'
</div>
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
