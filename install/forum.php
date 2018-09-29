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
//die('1');
require_once('../applications/config.php');
include 'assets/tpl/header.php'; 
?>
    <div class="row">
        <div class="col-md-3">
          <div class="list-group">
          <a href="#" class="list-group-item">Introduction</a>
            <a href="#" class="list-group-item">MySQL Information</a>
            <a href="#" class="list-group-item active">Forum Information</a>
            <a href="#" class="list-group-item">Admin Information</a>
            <a href="#" class="list-group-item">Installation Complete</a>
        </div>
      </div>
        <div class="col-md-9">
          <div class="row">
            <div class="col-md-12">
              <div class="progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="60" aria-valuemin="0" aria-valuemax="100" style="width: 60%;">
                  60%
                </div>
            </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
              <h2>Forum Information</h2>
              <p>It's time to give your forum an identity</p>
              <?php
    if (isset($_POST['submit_forum'])) {
        try {
            $name = $_POST['forumname'];
            $email = $_POST['forumemail'];
            if (!$name or !$email) {
                throw new Exception('All fields are required!');
            } else {
                //$MYSQL = new mysqli(MYSQL_HOST, MYSQL_USERNAME, MYSQL_PASSWORD, MYSQL_DATABASE);

                $dsn = 'mysql:dbname=' . MYSQL_DATABASE . ';host=' . MYSQL_HOST;

                try {
                    $MYSQL = new PDO($dsn, MYSQL_USERNAME, MYSQL_PASSWORD);
                } catch (PDOException $e) {
                    throw new Exception('Connection failed: ' . $e->getMessage());
                }

                $rules = '
- No spamming.
- No racist comments.
- Do not start a political discussion unless permitted.
- No illegal stuff are to be posted on anywhere in the forum.';
                $MYSQL->query("INSERT INTO `" . MYSQL_PREFIX . "generic` (`id`, `site_rules`, `site_name`, `site_theme`, `site_language`, `site_email`) VALUES ('1', '$rules', '$name', '1', 'english', '$email');");
                echo("<script>location.href = 'admin.php';</script>");

            }
        } catch (Exception $e) {
            echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
        }
    }
    ?>
              <form method="POST" action="forum.php">
              <div class="form-group">
              <label for="forumname">Forum Name</label>
                <input type="text" class="form-control" id="forumname" name="forumname" placeholder="Your Forums Name">
                </div>
              <div class="form-group">
              <label for="forumemail">Forum Email Address</label>
              <input type="text" class="form-control" id="forumemail" name="forumemail" placeholder="Your Forums Email Address">
              </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="text-align: right;">
              <input type="submit" name="submit_forum" class="btn btn-primary btn-sm" value="Next Step: Admin Information"/>
            </div>
          </form>
        </div>
        </div>
    </div>
    </div>
<?php include 'assets/tpl/footer.php'; ?>