<?php

define('BASEPATH', 'Staff');
require_once('../applications/wrapper.php');

if (!$LAYER->perm->check('access_administration')) {
    redirect(SITE_URL);
}//Checks if user has permission to create a thread.
//require_once('template/top.php');
echo $ADMIN->template('top');
$notice = '';

/*
 * Additional notice.
 */
if ($PGET->g('notice')) {
     switch ($PGET->g('notice')) {
         case "sent":
             $notice .= $ADMIN->alert(
                 'Mass email has been sent successfully!',
                 'success'
             );
             break;
     }
 }
        if (isset($_POST['send'])) {
            try {
                NoCSRF::check('csrf_token', $_POST);

                $subject = clean($_POST['subject']);
                $content = clean($_POST['content']);

                if (!$subject) {
                    throw new Exception ('All fields are required!');
                } else {
                    try {
                        $query = $MYSQL->query('SELECT * FROM {prefix}users');
                        $return = '';
                        $sitename = $LAYER->data['site_name'];
                        $siteemail = $LAYER->data['site_email'];
                        foreach ($query as $s) {
                            $headers = 'From: '.$sitename.' <'.$siteemail.'>' . "\r\n" .
                                       'Reply-To: '.$siteemail . "\r\n" .
                                       'X-Sender: '.$siteemail . "\r\n" .
                                       'X-Mailer: PHP/' . phpversion();
                            //echo $headers;
                            mail($s['user_email'], $subject, $content, $headers);
                        }
                        redirect(SITE_URL . '/admin/massemail.php/notice/sent');
                    } catch (mysqli_sql_exception $e) {
                        throw new Exception ('Error updating node.');
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
            'Send Mass Email',
            $notice .
            '<form action="" method="POST">
                <input type="hidden" name="csrf_token" value="' . $token . '">
                <label for="subject">Subject</label>
                <input type="text" name="subject" id="subject" value="" class="form-control" />
                <label for="content">Email Content</label>
                <textarea name="content" id="content" class="form-control" style="min-height:250px;"></textarea>
                <br /><br />
                <input type="submit" name="send" value="Send Email" class="btn btn-default" />
            </form>',
            '',
            '12'
        );


//require_once('template/bot.php');
echo $ADMIN->template('bot');
?>