<?php

/*
 * Core class of LayerBB
 */
if (!defined('BASEPATH')) {
    die();
}

class LayerBB_Captcha
{

    public $error;
    private $captcha_type = 1;
    private $key;

    public function __construct()
    {
        global $LAYER;
        if ($LAYER->data['captcha_type'] == "2") {
            require_once('recaptchalib.php');
            $this->captcha_type = 2;
            $this->key = array(
                'public' => $LAYER->data['recaptcha_public_key'],
                'private' => $LAYER->data['recaptcha_private_key']
            );
        }
    }

    /*
     * @Default Captcha
     * - Returns the text input with the captcha's image tag.
     * @reCaptcha
     * - Returns reCaptcha API call.
     */
    public function display()
    {
        global $LANG;
        if ($this->captcha_type == "1") {
            return '<img src="' . SITE_URL . '/public/img/captcha.php" alt="LayerBB Captcha" /><br /><input type="text" id="LayerBB_captcha" name="LayerBB_captcha" />';
        } else {
            return '<div class="g-recaptcha" data-sitekey="'.$this->key['public'].'"></div>';
            //return recaptcha_get_html($this->key['public'], $this->error);
        }
    }

    /*
     * Verify if the input is the same as the captcha.
     */
    public function verify()
    {
        global $LANG;
        if ($this->captcha_type == "1") {
            $input = md5($_POST['LayerBB_captcha']);
            if ($input !== $_SESSION['LayerBB_Captcha']) {
                throw new Exception ($LANG['global_form_process']['captcha_incorrect']);
            } else {
                return true;
            }
        } else {
            $sender_name = stripslashes($_POST["sender_name"]);
            $sender_email = stripslashes($_POST["sender_email"]);
            $sender_message = stripslashes($_POST["sender_message"]);
            $response = $_POST["g-recaptcha-response"];
            $url = 'https://www.google.com/recaptcha/api/siteverify';
            $data = array(
                'secret' => $this->key['private'],
                'response' => $_POST["g-recaptcha-response"]
            );
            $options = array(
                'http' => array (
                    'method' => 'POST',
                    'content' => http_build_query($data)
                )
            );
            $context  = stream_context_create($options);
            $verify = file_get_contents($url, false, $context);
            $captcha_success=json_decode($verify);
            if ($captcha_success->success==false) {
               throw new Exception ($LANG['global_form_process']['captcha_incorrect']);
            } else if ($captcha_success->success==true) {
                return true;
            }



            /*$resp = recaptcha_check_answer(
                $this->key['private'],
                $_SERVER['REMOTE_ADDR'],
                $_POST['recaptcha_challenge_field'],
                $_POST['recaptcha_response_field']
            );
            if ($resp->is_valid) {
                return true;
            } else {
                throw new Exception ($LANG['global_form_process']['captcha_incorrect']);
            }*/
        }
    }

}

?>