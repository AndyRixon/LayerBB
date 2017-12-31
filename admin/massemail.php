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
                $username = $s['user_email'];
                $emailcontent = clean($_POST['content']);

                if (!$subject) {
                    throw new Exception ('All fields are required!');
                } else {
                    try {
                        $query = $MYSQL->query('SELECT * FROM {prefix}users');
                        $return = '';
                        $sitename = $LAYER->data['site_name'];
                        $siteemail = $LAYER->data['site_email'];
                        foreach ($query as $s) {
                            $datejoined = date('l jS \of F Y', $s['date_joined']);
                            $replace = array( 
                                '%siteurl%' => SITE_URL, 
                                '%sitename%' => $LAYER->data['site_name'],
                                '%username%' => $s['username'],
                                '%datejoined%' => $datejoined,
                                '%profileurl%' => SITE_URL . '/members.php/cmd/user/id/' . $s['id']
                            ); 

                            $emailreplace = strtr($emailcontent,$replace); 
                            $content = $emailreplace;
                            $headers = 'From: '.$sitename.' <'.$siteemail.'>' . "\r\n" .
                                       'Reply-To: '.$siteemail . "\r\n" .
                                       'X-Sender: '.$siteemail . "\r\n" .
                                       'X-Mailer: PHP/' . phpversion();
                            //echo $headers;
                            mail($s['user_email'], $subject, $content, $headers);
                        }
                        redirect(SITE_URL . '/admin/massemail.php/notice/sent');
                    } catch (mysqli_sql_exception $e) {
                        throw new Exception ('Error sending email. Please try again.');
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
            '<div class="row">
  <div class="col-md-8"><form action="" method="POST">
                <input type="hidden" name="csrf_token" value="' . $token . '">
                <label for="subject">Subject</label>
                <input type="text" name="subject" id="subject" value="" class="form-control" />
                <label for="content">Email Content</label>
                <textarea id="editor" name="content" class="form-control" style="min-height:250px;"></textarea><br />
                <div class="alert alert-info" role="alert"><b>Please Note:</b> HTML Tags do not work, line breaks and urls are automatically converted!</div>
                <input type="submit" name="send" value="Send Email" class="btn btn-default" />
            </form></div>
  <div class="col-md-4"><div class="panel panel-default">
  <div class="panel-heading">
    <h3 class="panel-title">Available Shortcodes</h3>
  </div>
  <div class="panel-body">
  <li class="list-group-item">
    <span class="badge">%siteurl%</span>
    Site URL<br />
    <small>Displays the site URL.</small>
  </li>
  <li class="list-group-item">
    <span class="badge">%sitename%</span>
    Site Name<br />
    <small>Displays the site name.</small>
  </li>
  <li class="list-group-item">
    <span class="badge">%username%</span>
    Username<br />
    <small>Displays the members username.</small>
  </li>
  <li class="list-group-item">
    <span class="badge">%profileurl%</span>
    Profile URL<br />
    <small>Displays the members profile URL.</small>
  </li>
  <li class="list-group-item">
    <span class="badge">%datejoined%</span>
    Date Joined<br />
    <small>Displays when the member joined.</small>
  </li>
  </div>
</div></div>
</div>',
            '',
            '12'
        );


//require_once('template/bot.php');
echo $ADMIN->template('bot');
?>