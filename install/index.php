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
include 'assets/tpl/header.php'; 
$phpver = PHP_VERSION;
$os = php_uname('s');
if (file_exists("../applications/config.php")) {
    $fileperm = substr(sprintf('%o', fileperms('../applications/config.php')), -3);
} else {
    $fileperm = "Missing";
}
if (file_exists("../applications")) {
    $appperm = substr(sprintf('%o', fileperms('../applications')), -3);
} else {
    $appperm = "Missing";
}
if (extension_loaded('pdo_mysql')) {
    $pdo = 'Installed';
} else {
    $pdo = 'Not Installed';
}
if (extension_loaded('mcrypt')) {
    $mcrypt = 'Installed';
} else {
    $mcrypt = 'Not Installed';
}
if (extension_loaded('mbstring')) {
    $mbstring = 'Installed';
} else {
    $mbstring = 'Not Installed';
}
?>
<div class="row">
  			<div class="col-md-3">
  				<div class="list-group">
	 				<a href="#" class="list-group-item active">Introduction</a>
	  				<a href="#" class="list-group-item">MySQL Information</a>
	  				<a href="#" class="list-group-item">Forum Information</a>
	  				<a href="#" class="list-group-item">Admin Information</a>
	  				<a href="#" class="list-group-item">Installation Complete</a>
				</div>
			</div>
  			<div class="col-md-9">
  				<div class="row">
  					<div class="col-md-12">
  						<div class="progress">
<div class="progress-bar" role="progressbar" aria-valuenow="20" aria-valuemin="0" aria-valuemax="100" style="width: 20%;">
    							20%
  							</div>
						</div>
  					</div>
				</div>
				<div class="row">
  					<div class="col-md-12">
  						<h2>Introduction <small>Welcome to LayerBB Installer</small></h2>
  						<p>Please ensure all system information below is correct.</p>
  						<table class="table table-striped">
  							<tr>
							    <td>&nbsp;</td>
							    <td><h5>Recommended</h5></td>
							    <td><h5>Your System</h5></td>
  							</tr>
							<tr>
							    <td>Operating System</td>
							    <td><span class="label label-default">Linux</span></td>
							    <td><span class="label label-primary"><?php echo $os; ?></span></td>
							</tr>
							<tr>
							    <td>PHP Version</td>
							    <td><span class="label label-default">5.5.0+</span></td>
							    <td><span class="label label-primary"><?php echo $phpver; ?></span></td>
							</tr>
							<tr>
							    <td>PDO PHP Extension</td>
							    <td><span class="label label-default">Installed</span></td>
							    <td><span class="label label-primary"><?php echo $pdo; ?></span></td>
							</tr>
							<tr>
							    <td>MCrypt PHP Extension</td>
							    <td><span class="label label-default">Installed</span></td>
							    <td><span class="label label-primary"><?php echo $mcrypt; ?></span></td>
							</tr>
							                            <tr>
							    <td>Multibyte String Extension</td>
							    <td><span class="label label-default">Installed</span></td>
							    <td><span class="label label-primary"><?php echo $mbstring; ?></span></td>
							</tr>
							<tr>
							    <td>File Permission on config.php</td>
							    <td><span class="label label-default">777</span></td>
							    <td><span class="label label-primary"><?php echo $fileperm; ?></span></td>
							</tr>
                            <tr>
                                <td>File Permission on /applications directory</td>
                                <td><span class="label label-default">777</span></td>
                                <td><span class="label label-primary"><?php echo $appperm; ?></span></td>
                            </tr>
						</table>
  					</div>
				</div>
				<div class="row">
					<?php
						if (file_exists('../applications/config.php')) {
							if($fileperm == '777' && $appperm == '777'){
								echo '<div class="col-md-12" style="text-align: right;">
  						<a href="mysql.php" class="btn btn-primary btn-sm" role="button">Start Installation</a>
  					</div>';
							} else {
								echo '<div class="alert alert-danger" role="alert"><b>Incorrect File Permission:</b> You need to change the file/folder permissions to the same as above to continue.</div>';
							}
						    
						} else {
						    echo '<div class="alert alert-danger" role="alert"><b>File Missing:</b> You need to rename config.php.new to config.php in the applications directory to continue.</div>';
						}
					?>
				</div>
  			</div>
		</div>
  	</div>
<?php include 'assets/tpl/footer.php'; ?>