<?php include 'assets/tpl/header.php'; 

?>
<div class="row">
  			<div class="col-md-3">
  				<div class="list-group">
	 				<a href="#" class="list-group-item">Introduction</a>
	  				<a href="#" class="list-group-item active">MySQL Information</a>
	  				<a href="#" class="list-group-item">Forum Information</a>
	  				<a href="#" class="list-group-item">Admin Information</a>
	  				<a href="#" class="list-group-item">Installation Complete</a>
				</div>
			</div>
  			<div class="col-md-9">
  				<div class="row">
  					<div class="col-md-12">
  						<div class="progress">
<div class="progress-bar" role="progressbar" aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width: 40%;">
    							40%
  							</div>
						</div>
  					</div>
				</div>
				<div class="row">
  					<div class="col-md-12">
  						<h2>MYSQL Information</h2>
  						<p>Please fill out your mysql connection information in the fields below.</p>
  						<?php
              ob_start();
if (isset($_POST['submit_mysql'])) {
    try {
        $mysql_host = $_POST['mysqlhost'];//MySQL Host.
        $mysql_username = $_POST['mysqlusername'];//MySQL Username
        $mysql_password = (!$_POST['mysqlpassword']) ? '' : $_POST['mysqlpassword'];//MySQL Password
        $mysql_database = $_POST['mysqldatabase'];//MySQL Database
        $mysql_prefix = $_POST['prefix'];//MySQL Prefix.

        $request = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
        $site_url = explode('/install/', $request);
        $site_url = $site_url[0];

        if (!$mysql_host or !$mysql_username or !$mysql_database or !$mysql_prefix) {//Check if all values are there.
            throw new Exception('All fields are required!');//If not, error.
            /* }elseif(!$conn = @mysqli_connect($mysql_host, $mysql_username, $mysql_password)){//Checks if MySQL connection could be established.
                 throw new Exception('MySQL Server connection could not be established.');//If not, error.
             }elseif(!@mysqli_select_db($conn, $mysql_database)){//Checks for connection to database.
                 throw new Exception('MySQL Database connection could not be established.');//If not, error.*/
        } else {

            $time = time();

            /*
             * Placing correct values into configuration file.
             */
            $config = file_get_contents('config.php');
            $config = str_replace('%mysql_host%', $mysql_host, $config);
            $config = str_replace('%mysql_username%', $mysql_username, $config);
            $config = str_replace('%mysql_password%', $mysql_password, $config);
            $config = str_replace('%mysql_database%', $mysql_database, $config);
            $config = str_replace('%mysql_prefix%', $mysql_prefix, $config);
            $config = str_replace('%site_url%', $site_url, $config);
            //file_put_contents('../applications/config.php', $config);

            //Write contents into the config.php file in the applications directory.
            $fh = fopen('../applications/config.php', 'w');
            fwrite($fh, $config);
            fclose($fh);

            //$MYSQL = new mysqli($mysql_host, $mysql_username, $mysql_password, $mysql_database);
            $dsn = 'mysql:dbname=' . $mysql_database . ';host=' . $mysql_host;

            try {
                $MYSQL = new PDO($dsn, $mysql_username, $mysql_password);
            } catch (PDOException $e) {
                throw new Exception('Connection failed: ' . $e->getMessage());
            }
            $MYSQL->query("DROP TABLE IF EXISTS `" . $mysql_prefix . "extensions`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "forum_category`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "forum_node`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "forum_posts`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "labels`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "generic`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "messages`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "permissions`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "reports`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "sessions`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "terminal`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "usergroups`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "users`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "user_comments`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "user_visitors`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "password_reset_requests`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "notifications`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "thread_tracking`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "countries`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "themes`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "poll`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "poll_answers`;
                           DROP TABLE IF EXISTS `" . $mysql_prefix . "poll_votes`;
                           ");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "extensions` (`id` int(11) NOT NULL AUTO_INCREMENT,`extension_name` varchar(255) NOT NULL,`extension_folder` varchar(255) NOT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");

            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "forum_category` (`id` int(11) NOT NULL AUTO_INCREMENT, `category_title` varchar(255) NOT NULL, `category_desc` varchar(255) NOT NULL, `category_place` int(11) NOT NULL DEFAULT '0', `allowed_usergroups` varchar(255) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            $MYSQL->query("INSERT INTO `" . $mysql_prefix . "forum_category` (`id`, `category_title`, `category_desc`, `category_place`, `allowed_usergroups`) VALUES (1, 'First Category', 'First category on this forum!', 0, '0,1,3,4');");

            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "forum_node` (`id` int(11) NOT NULL AUTO_INCREMENT, `node_name` varchar(255) NOT NULL, `name_friendly` varchar(255) NOT NULL, `node_desc` varchar(255) NOT NULL, `in_category` int(11) NOT NULL DEFAULT '0', `node_type` int(11) NOT NULL DEFAULT '1', `parent_node` int(11) NOT NULL DEFAULT '0', `node_locked` int(11) NOT NULL DEFAULT '0', `node_place` int(11) NOT NULL DEFAULT '0', `allowed_usergroups` varchar(255) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            $MYSQL->query("INSERT INTO `" . $mysql_prefix . "forum_node` (`id`, `node_name`, `name_friendly`, `node_desc`, `in_category`, `node_locked`, `node_place`, `allowed_usergroups`) VALUES (1, 'First Node', 'first_node', 'The first node on this forum', 1, 0, 0, '0,1,3,4');");

            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "forum_posts` (`id` int(11) NOT NULL AUTO_INCREMENT, `post_title` varchar(255) NOT NULL DEFAULT '', `title_friendly` varchar(255) NOT NULL, `post_content` text NOT NULL, `post_tags` varchar(255) NOT NULL, `post_time` int(11) NOT NULL, `post_user` int(11) NOT NULL, `origin_thread` int(11) NOT NULL DEFAULT '0', `origin_node` int(11) NOT NULL DEFAULT '0', `post_type` int(11) NOT NULL, `post_sticky` int(11) NOT NULL DEFAULT '0', `post_locked` int(11) NOT NULL DEFAULT '0', `last_updated` int(11) NOT NULL DEFAULT '0', `watchers` text NOT NULL, `label` int(11) NOT NULL, `views` INT(11) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            $MYSQL->query("ALTER TABLE `" . $mysql_prefix . "forum_posts` ADD FULLTEXT search(post_title, post_content);");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "labels` (`id` INT(11) NOT NULL AUTO_INCREMENT, `node_id` INT(11) NOT NULL, `label` VARCHAR(1000) NOT NULL, PRIMARY KEY (`id`))ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "generic` (`id` int(11) NOT NULL AUTO_INCREMENT, `site_rules` text NOT NULL, `site_name` varchar(255) NOT NULL, `site_theme` int(11) NOT NULL, `site_language` varchar(255) NOT NULL, `site_email` varchar(255) NOT NULL, `register_enable` int(11) NOT NULL DEFAULT '1', `register_email_activate` int(11) NOT NULL DEFAULT '0', `facebook_authenticate` int(11) NOT NULL DEFAULT '0', `facebook_app_id` varchar(255) NOT NULL DEFAULT '0', `facebook_app_secret` varchar(255) NOT NULL DEFAULT '0', `captcha_type` int(11) NOT NULL DEFAULT '1', `recaptcha_public_key` varchar(255) NOT NULL DEFAULT '0', `recaptcha_private_key` varchar(255) NOT NULL DEFAULT '0', `mail_type` int(11) NOT NULL DEFAULT '1', `smtp_address` varchar(255) NOT NULL DEFAULT '0', `smtp_port` int(11) NOT NULL DEFAULT '0', `smtp_username` varchar(255) NOT NULL DEFAULT '0', `smtp_password` varchar(255) NOT NULL DEFAULT '0', `post_merge` int(1) NOT NULL DEFAULT '1', `flat_ui_admin` INT NOT NULL DEFAULT '0', `number_subs` INT(3) DEFAULT 3  NOT NULL, `site_enable` INT(1) DEFAULT 1  NOT NULL, `offline_msg` LONGTEXT NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "messages` (`id` int(11) NOT NULL AUTO_INCREMENT,`message_title` varchar(255) NOT NULL,`message_content` text NOT NULL,`message_time` int(11) NOT NULL,`origin_message` int(11) NOT NULL DEFAULT '0',`message_sender` int(11) NOT NULL,`message_receiver` int(11) NOT NULL,`message_type` int(11) NOT NULL DEFAULT '1',`receiver_viewed` int(11) NOT NULL DEFAULT '0', `sender_deleted` BOOL NOT NULL DEFAULT '0', `receiver_deleted` BOOL NOT NULL DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "permissions` (`id` int(11) NOT NULL AUTO_INCREMENT,`permission_name` varchar(255) NOT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            $MYSQL->query("INSERT INTO `" . $mysql_prefix . "permissions` (`id`, `permission_name`) VALUES (1, 'view_forum'),(2, 'create_thread'),(3, 'reply_thread'),(4, 'access_moderation'),(5, 'access_administration');");

            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "reports` (`id` int(11) NOT NULL AUTO_INCREMENT,`report_reason` varchar(255) NOT NULL,`reported_by` int(11) NOT NULL,`reported_user` int(11) NOT NULL DEFAULT '0',`reported_post` int(11) NOT NULL DEFAULT '0',`reported_time` int(11) NOT NULL,`report_close` int(11) NOT NULL DEFAULT '0',PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "sessions` (`id` int(11) NOT NULL AUTO_INCREMENT,`session_id` varchar(255) NOT NULL,`logged_user` int(11) NOT NULL,`session_type` int(11) NOT NULL DEFAULT '1',`session_time` int(11) NOT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "terminal` (`id` int(11) NOT NULL AUTO_INCREMENT,`command_name` varchar(255) NOT NULL,`command_syntax` varchar(255) NOT NULL,`run_function` varchar(255) NOT NULL,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            $MYSQL->query("INSERT INTO `" . $mysql_prefix . "terminal` (`id`, `command_name`, `command_syntax`, `run_function`) VALUES (1, 'cugroup', 'cugroup %s %s', 'cugroup'),(2, 'ban', 'ban %s', 'ban'),(3, 'unban', 'unban %s', 'unban'),(4, 'dugroup', 'dugroup %s %s', 'dugroup');");

            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "usergroups` (`id` int(11) NOT NULL AUTO_INCREMENT, `group_name` varchar(255) NOT NULL, `group_style` varchar(255) NOT NULL DEFAULT '%username%', `group_permissions` varchar(255) NOT NULL DEFAULT '0', `is_staff` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            $MYSQL->query("INSERT INTO `" . $mysql_prefix . "usergroups` (`id`, `group_name`, `group_style`, `group_permissions`, `is_staff`) VALUES (1, 'User', '<span>%username%</span>', '1,2,3', '0'),(2, 'Banned', '<span>%username% (Banned)</span>', '0', '0'),(3, 'Moderator', '<span style=\"color:#3a5892;\"><strong>%username%</strong></span>', '1,2,3,4', '1'),(4, 'Administrator', '<span style=\"color:#762727;\"><strong>%username%</strong></span>', '*', '1');");

            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "users` (`id` int(11) NULL AUTO_INCREMENT, `username` varchar(255) NULL, `user_password` varchar(255) NULL, `user_email` varchar(255) NULL,`user_message` varchar(255) NULL DEFAULT 'User', `user_avatar` varchar(255) NULL DEFAULT 'default.png', `avatar_type` int(11) NULL DEFAULT '0', `user_signature` varchar(255) NULL,`about_user` LONGTEXT NULL, `location` varchar (2) NULL DEFAULT '--', `gender` int(1) DEFAULT '0', `date_joined` int(11) NULL,`user_birthday` date NULL, `additional_permissions` varchar(255) NULL DEFAULT '0',  `user_group` int(11) NULL DEFAULT '1', `display_group` INT NULL, `chosen_theme` int(11) NULL DEFAULT '0', `set_timezone` varchar(255) NULL DEFAULT 'US/Central', `user_disabled` int(11) NULL DEFAULT '0', `is_banned` int(11) NULL DEFAULT '0', `unban_time` int(11) NULL, `ban_reason` varchar(255) NULL DEFAULT 'None', `facebook_id` varchar(100) NULL DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "user_comments` (`id` int(11) NOT NULL AUTO_INCREMENT, `profile_owner` int(11) NOT NULL, `writer` int(11) NOT NULL, `comment` text NOT NULL, `timestamp` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "user_visitors` (`id` int(11) NOT NULL AUTO_INCREMENT,`profile_owner` int(11) NOT NULL,`visitor` int(11) NOT NULL,`timestamp` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "password_reset_requests` (`id` int(11) NOT NULL AUTO_INCREMENT,`user` int(11) NOT NULL,`reset_token` varchar(255) NOT NULL UNIQUE,`request_time` int(11) NOT NULL,`active` tinyint(1) NOT NULL DEFAULT 1,PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "notifications` (`id` int(11) NOT NULL AUTO_INCREMENT, `notice_content` varchar(255) NOT NULL, `notice_link` varchar(255) NOT NULL DEFAULT '0', `user` int(11) NOT NULL DEFAULT '0', `time_received` int(11) NOT NULL DEFAULT '0', `viewed` int(11) NOT NULL DEFAULT '0', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "thread_tracking` (`user_id` INT(11) NOT NULL, `thread_id` INT(11) NOT NULL, `last_visit` INT(11) NOT NULL, PRIMARY KEY (`user_id`, `thread_id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "countries` (`id` int(11) NOT NULL AUTO_INCREMENT, `iso` varchar(2) NOT NULL, `language` varchar(255) NOT NULL DEFAULT 'english', PRIMARY KEY (`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "themes` (`id` int(11) NOT NULL AUTO_INCREMENT, `theme_name` varchar(255) NOT NULL, `theme_version` varchar(255) NOT NULL DEFAULT '1', `theme_json_data` LONGTEXT NOT NULL, PRIMARY KEY(`id`)) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;");

            $sand = file_get_contents('assets/theme-json/sand.json');

            $stmt = $MYSQL->prepare("INSERT INTO " . $mysql_prefix . "themes (`theme_name`, `theme_version`, `theme_json_data`) VALUES ('Sand', '1.0', :sand);");
            $stmt->bindParam(':sand', $sand);
            $stmt->execute();
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "poll` (`id` int(11) NOT NULL AUTO_INCREMENT, `question` varchar(255) NOT NULL, `thread_id` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "poll_answers` (`id` int(11) NOT NULL AUTO_INCREMENT, `poll_id` int(11) NOT NULL, `answer` varchar(255) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");
            $MYSQL->query("CREATE TABLE IF NOT EXISTS `" . $mysql_prefix . "poll_votes` (`id` int(11) NOT NULL AUTO_INCREMENT, `poll_id` int(11) NOT NULL, `answer_id` int(11) NOT NULL, `user_id` int(11) NOT NULL, PRIMARY KEY (`id`)) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;");

            $MYSQL->query("INSERT INTO `" . $mysql_prefix . "countries` (`iso`, `language`) VALUES ('AD', 'english'),
                                                                                                     ('AE', 'english'),
                                                                                                     ('AF', 'english'),
                                                                                                     ('AG', 'english'),
                                                                                                     ('AI', 'english'),
                                                                                                     ('AL', 'english'),
                                                                                                     ('AM', 'english'),
                                                                                                     ('AO', 'english'),
                                                                                                     ('AQ', 'english'),
                                                                                                     ('AR', 'english'),
                                                                                                     ('AS', 'english'),
                                                                                                     ('AT', 'german'),
                                                                                                     ('AU', 'english'),
                                                                                                     ('AW', 'english'),
                                                                                                     ('AX', 'english'),
                                                                                                     ('AZ', 'english'),
                                                                                                     ('BA', 'english'),
                                                                                                     ('BB', 'english'),
                                                                                                     ('BD', 'english'),
                                                                                                     ('BE', 'english'),
                                                                                                     ('BF', 'english'),
                                                                                                     ('BG', 'english'),
                                                                                                     ('BH', 'english'),
                                                                                                     ('BI', 'english'),
                                                                                                     ('BJ', 'english'),
                                                                                                     ('BL', 'english'),
                                                                                                     ('BM', 'english'),
                                                                                                     ('BN', 'english'),
                                                                                                     ('BO', 'english'),
                                                                                                     ('BQ', 'english'),
                                                                                                     ('BR', 'english'),
                                                                                                     ('BS', 'english'),
                                                                                                     ('BT', 'english'),
                                                                                                     ('BV', 'english'),
                                                                                                     ('BW', 'english'),
                                                                                                     ('BY', 'english'),
                                                                                                     ('BZ', 'english'),
                                                                                                     ('CA', 'english'),
                                                                                                     ('CC', 'english'),
                                                                                                     ('CD', 'english'),
                                                                                                     ('CF', 'english'),
                                                                                                     ('CG', 'english'),
                                                                                                     ('CH', 'english'),
                                                                                                     ('CI', 'english'),
                                                                                                     ('CK', 'english'),
                                                                                                     ('CL', 'english'),
                                                                                                     ('CM', 'english'),
                                                                                                     ('CN', 'english'),
                                                                                                     ('CO', 'english'),
                                                                                                     ('CR', 'english'),
                                                                                                     ('CU', 'english'),
                                                                                                     ('CV', 'english'),
                                                                                                     ('CW', 'english'),
                                                                                                     ('CX', 'english'),
                                                                                                     ('CY', 'english'),
                                                                                                     ('CZ', 'english'),
                                                                                                     ('DE', 'german'),
                                                                                                     ('DJ', 'english'),
                                                                                                     ('DK', 'english'),
                                                                                                     ('DM', 'english'),
                                                                                                     ('DO', 'english'),
                                                                                                     ('DZ', 'english'),
                                                                                                     ('EC', 'english'),
                                                                                                     ('EE', 'english'),
                                                                                                     ('EG', 'english'),
                                                                                                     ('EH', 'english'),
                                                                                                     ('ER', 'english'),
                                                                                                     ('ES', 'english'),
                                                                                                     ('ET', 'english'),
                                                                                                     ('FI', 'english'),
                                                                                                     ('FJ', 'english'),
                                                                                                     ('FK', 'english'),
                                                                                                     ('FM', 'english'),
                                                                                                     ('FO', 'english'),
                                                                                                     ('FR', 'english'),
                                                                                                     ('GA', 'english'),
                                                                                                     ('GB', 'english'),
                                                                                                     ('GD', 'english'),
                                                                                                     ('GE', 'english'),
                                                                                                     ('GF', 'english'),
                                                                                                     ('GG', 'english'),
                                                                                                     ('GH', 'english'),
                                                                                                     ('GI', 'english'),
                                                                                                     ('GL', 'english'),
                                                                                                     ('GM', 'english'),
                                                                                                     ('GN', 'english'),
                                                                                                     ('GP', 'english'),
                                                                                                     ('GQ', 'english'),
                                                                                                     ('GR', 'english'),
                                                                                                     ('GS', 'english'),
                                                                                                     ('GT', 'english'),
                                                                                                     ('GU', 'english'),
                                                                                                     ('GW', 'english'),
                                                                                                     ('GY', 'english'),
                                                                                                     ('HK', 'english'),
                                                                                                     ('HM', 'english'),
                                                                                                     ('HN', 'english'),
                                                                                                     ('HR', 'english'),
                                                                                                     ('HT', 'english'),
                                                                                                     ('HU', 'english'),
                                                                                                     ('ID', 'english'),
                                                                                                     ('IE', 'english'),
                                                                                                     ('IL', 'english'),
                                                                                                     ('IM', 'english'),
                                                                                                     ('IN', 'english'),
                                                                                                     ('IO', 'english'),
                                                                                                     ('IQ', 'english'),
                                                                                                     ('IR', 'english'),
                                                                                                     ('IS', 'english'),
                                                                                                     ('IT', 'english'),
                                                                                                     ('JE', 'english'),
                                                                                                     ('JM', 'english'),
                                                                                                     ('JO', 'english'),
                                                                                                     ('JP', 'english'),
                                                                                                     ('KE', 'english'),
                                                                                                     ('KG', 'english'),
                                                                                                     ('KH', 'english'),
                                                                                                     ('KI', 'english'),
                                                                                                     ('KM', 'english'),
                                                                                                     ('KN', 'english'),
                                                                                                     ('KP', 'english'),
                                                                                                     ('KR', 'english'),
                                                                                                     ('KW', 'english'),
                                                                                                     ('KY', 'english'),
                                                                                                     ('KZ', 'english'),
                                                                                                     ('LA', 'english'),
                                                                                                     ('LB', 'english'),
                                                                                                     ('LC', 'english'),
                                                                                                     ('LI', 'english'),
                                                                                                     ('LK', 'english'),
                                                                                                     ('LR', 'english'),
                                                                                                     ('LS', 'english'),
                                                                                                     ('LT', 'english'),
                                                                                                     ('LU', 'english'),
                                                                                                     ('LV', 'english'),
                                                                                                     ('LY', 'english'),
                                                                                                     ('MA', 'english'),
                                                                                                     ('MC', 'english'),
                                                                                                     ('MD', 'english'),
                                                                                                     ('ME', 'english'),
                                                                                                     ('MF', 'english'),
                                                                                                     ('MG', 'english'),
                                                                                                     ('MH', 'english'),
                                                                                                     ('MK', 'english'),
                                                                                                     ('ML', 'english'),
                                                                                                     ('MM', 'english'),
                                                                                                     ('MN', 'english'),
                                                                                                     ('MO', 'english'),
                                                                                                     ('MP', 'english'),
                                                                                                     ('MQ', 'english'),
                                                                                                     ('MR', 'english'),
                                                                                                     ('MS', 'english'),
                                                                                                     ('MT', 'english'),
                                                                                                     ('MU', 'english'),
                                                                                                     ('MV', 'english'),
                                                                                                     ('MW', 'english'),
                                                                                                     ('MX', 'english'),
                                                                                                     ('MY', 'english'),
                                                                                                     ('MZ', 'english'),
                                                                                                     ('NA', 'english'),
                                                                                                     ('NC', 'english'),
                                                                                                     ('NE', 'english'),
                                                                                                     ('NF', 'english'),
                                                                                                     ('NG', 'english'),
                                                                                                     ('NI', 'english'),
                                                                                                     ('NL', 'english'),
                                                                                                     ('NO', 'english'),
                                                                                                     ('NP', 'english'),
                                                                                                     ('NR', 'english'),
                                                                                                     ('NU', 'english'),
                                                                                                     ('NZ', 'english'),
                                                                                                     ('OM', 'english'),
                                                                                                     ('PA', 'english'),
                                                                                                     ('PE', 'english'),
                                                                                                     ('PF', 'english'),
                                                                                                     ('PG', 'english'),
                                                                                                     ('PH', 'english'),
                                                                                                     ('PK', 'english'),
                                                                                                     ('PL', 'english'),
                                                                                                     ('PM', 'english'),
                                                                                                     ('PN', 'english'),
                                                                                                     ('PR', 'english'),
                                                                                                     ('PS', 'english'),
                                                                                                     ('PT', 'english'),
                                                                                                     ('PW', 'english'),
                                                                                                     ('PY', 'english'),
                                                                                                     ('QA', 'english'),
                                                                                                     ('RE', 'english'),
                                                                                                     ('RO', 'english'),
                                                                                                     ('RS', 'english'),
                                                                                                     ('RU', 'english'),
                                                                                                     ('RW', 'english'),
                                                                                                     ('SA', 'english'),
                                                                                                     ('SB', 'english'),
                                                                                                     ('SC', 'english'),
                                                                                                     ('SD', 'english'),
                                                                                                     ('SE', 'english'),
                                                                                                     ('SG', 'english'),
                                                                                                     ('SH', 'english'),
                                                                                                     ('SI', 'english'),
                                                                                                     ('SJ', 'english'),
                                                                                                     ('SK', 'english'),
                                                                                                     ('SL', 'english'),
                                                                                                     ('SM', 'english'),
                                                                                                     ('SN', 'english'),
                                                                                                     ('SO', 'english'),
                                                                                                     ('SR', 'english'),
                                                                                                     ('SS', 'english'),
                                                                                                     ('ST', 'english'),
                                                                                                     ('SV', 'english'),
                                                                                                     ('SX', 'english'),
                                                                                                     ('SY', 'english'),
                                                                                                     ('SZ', 'english'),
                                                                                                     ('TC', 'english'),
                                                                                                     ('TD', 'english'),
                                                                                                     ('TF', 'english'),
                                                                                                     ('TG', 'english'),
                                                                                                     ('TH', 'english'),
                                                                                                     ('TJ', 'english'),
                                                                                                     ('TK', 'english'),
                                                                                                     ('TL', 'english'),
                                                                                                     ('TM', 'english'),
                                                                                                     ('TN', 'english'),
                                                                                                     ('TO', 'english'),
                                                                                                     ('TR', 'english'),
                                                                                                     ('TT', 'english'),
                                                                                                     ('TV', 'english'),
                                                                                                     ('TW', 'english'),
                                                                                                     ('TZ', 'english'),
                                                                                                     ('UA', 'english'),
                                                                                                     ('UG', 'english'),
                                                                                                     ('UM', 'english'),
                                                                                                     ('US', 'english'),
                                                                                                     ('UY', 'english'),
                                                                                                     ('UZ', 'english'),
                                                                                                     ('VA', 'english'),
                                                                                                     ('VC', 'english'),
                                                                                                     ('VE', 'english'),
                                                                                                     ('VG', 'english'),
                                                                                                     ('VI', 'english'),
                                                                                                     ('VN', 'english'),
                                                                                                     ('VU', 'english'),
                                                                                                     ('WF', 'english'),
                                                                                                     ('WS', 'english'),
                                                                                                     ('YE', 'english'),
                                                                                                     ('YT', 'english'),
                                                                                                     ('ZA', 'english'),
                                                                                                     ('ZM', 'english'),
                                                                                                     ('ZW', 'english');");
            echo("<script>location.href = 'forum.php';</script>");
        }

    } catch (Exception $e) {
        echo '<div class="alert alert-danger">' . $e->getMessage() . '</div>';
    }
}
?>
  						<form method="POST" action="mysql.php">
							<div class="form-group">
 							<label for="mysqlhost">MYSQL Hostname</label>
    						<input type="text" class="form-control" id="mysqlhost" name="mysqlhost" value="localhost">
  							</div>
							<div class="form-group">
							<label for="mysqlusername">MYSQL Username</label>
							<input type="text" class="form-control" id="mysqlusername" name="mysqlusername" placeholder="MySQL Username">
							</div>
							<div class="form-group">
 							<label for="mysqlpassword">MYSQL Password</label>
    						<input type="password" class="form-control" id="mysqlpassword" name="mysqlpassword" placeholder="MySQL Password">
  							</div>
							<div class="form-group">
							<label for="mysqldatabase">MYSQL Database</label>
							<input type="text" class="form-control" id="mysqldatabase" name="mysqldatabase" placeholder="MySQL Database">
							</div>
							<div class="form-group">
							<label for="prefix">Database Prefix</label>
							<input type="text" class="form-control" id="prefix" name="prefix" value="layerbb_">
							</div>
  					</div>
				</div>
				<div class="row">
  					<div class="col-md-12" style="text-align: right;">
  						<input type="submit" name="submit_mysql" class="btn btn-primary btn-sm" value="Next Step: Forum Information"/>
  					</div>
  				</form>
				</div>
  			</div>
		</div>
  	</div>
<?php include 'assets/tpl/footer.php'; ?>