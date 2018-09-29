<?php 
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//
if(file_exists("../applications/install.lock")){
    echo "<div style='padding:10px; background: #ECD5D8; color: #BC2A4D; border:1px solid #BC2A4D;'>The installer is currently locked, you will need to remove /applications/install.lock to continue!</div>";
    exit();
}
define('BASEPATH', '1');
require_once('../applications/config.php');
require_once('../applications/functions.php');
include 'assets/tpl/header.php'; 
?>
    <div class="row">
        <div class="col-md-3">
          <div class="list-group">
          <a href="#" class="list-group-item">Introduction</a>
            <a href="#" class="list-group-item">MySQL Information</a>
            <a href="#" class="list-group-item">Forum Information</a>
            <a href="#" class="list-group-item active">Admin Information</a>
            <a href="#" class="list-group-item">Installation Complete</a>
        </div>
      </div>
        <div class="col-md-9">
          <div class="row">
            <div class="col-md-12">
              <div class="progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="80" aria-valuemin="0" aria-valuemax="100" style="width: 80%;">
                  80%
                </div>
            </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
              <h2>Admin Information</h2>
              <p>Create your first administrator account</p>
               <?php
    if (isset($_POST['submit_admin'])) {
        try {

            foreach ($_POST as $parent => $child) {
                $_POST[$parent] = htmlentities($child);
            }

            $username = $_POST['username'];
            $password = encrypt($_POST['password']);
            $email = $_POST['email'];
            $date = time();

            if (!$username or !$email or !$password) {
                throw new Exception('All fields are required!');
            } else {
                $dsn = 'mysql:dbname=' . MYSQL_DATABASE . ';host=' . MYSQL_HOST;

                try {
                    $MYSQL = new PDO($dsn, MYSQL_USERNAME, MYSQL_PASSWORD);
                } catch (PDOException $e) {
                    throw new Exception('Connection failed: ' . $e->getMessage());
                }
                
                //die("INSERT INTO `" . MYSQL_PREFIX . "users` (`username`, `user_password`, `user_email`, `date_joined`, `user_group`) VALUES (".$username.", ".$password.", ".$email.", ".$date.", " . ADMIN_ID . ");");
                $MYSQL->query("INSERT INTO `" . MYSQL_PREFIX . "users` (`username`, `user_password`, `user_email`, `date_joined`, `user_group`) VALUES ('".$username."', '".$password."', '".$email."', '".$date."', '" . ADMIN_ID . "');");
                $website = SITE_URL;
                $name = $username;
                $url = 'https://api.layerbb.com/api.php?cmd=install';
                $myvars = 'url=' . $website . '&name=' . $name;
                $ch = curl_init( $url );
                curl_setopt( $ch, CURLOPT_POST, 1);
                curl_setopt( $ch, CURLOPT_POSTFIELDS, $myvars);
                curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, 1);
                curl_setopt( $ch, CURLOPT_HEADER, 0);
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, 1);
                $response = curl_exec( $ch ); 

               echo("<script>location.href = 'done.php';</script>");

            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
        }
    }
    ?>
              <form method="POST" action="admin.php">
              <div class="form-group">
              <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" placeholder="Your administrator username">
                </div>
              <div class="form-group">
              <label for="password">Password</label>
              <input type="password" class="form-control" id="password" name="password" placeholder="Your desired password">
              </div>
              <div class="form-group">
              <label for="email">Email Address</label>
                <input type="text" class="form-control" id="email" name="email" placeholder="Your email address">
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="text-align: right;">
              <input type="hidden" name="submit_adin" value=""/>
              <input type="submit" name="submit_admin" class="btn btn-primary btn-sm" value="Complete Installation"/>
            </div>
        </div>
        </div>
    </div>
    </div>
<?php include 'assets/tpl/footer.php'; ?>