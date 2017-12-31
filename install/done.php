<?php 
include 'assets/tpl/header.php'; 
?>
        <div class="row">
        <div class="col-md-3">
          <div class="list-group">
          <a href="#" class="list-group-item">Introduction</a>
            <a href="#" class="list-group-item">MySQL Information</a>
            <a href="#" class="list-group-item">Forum Information</a>
            <a href="#" class="list-group-item">Admin Information</a>
            <a href="#" class="list-group-item active">Installation Complete</a>
        </div>
      </div>
        <div class="col-md-9">
          <div class="row">
            <div class="col-md-12">
              <div class="progress">
                <div class="progress-bar" role="progressbar" aria-valuenow="100" aria-valuemin="0" aria-valuemax="100" style="width: 100%;">
                  100%
                </div>
            </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
              <h2>Installation Complete <small>Your installation was successful.</small></h2>
              <p>Thank you for choosing LayerBB</p>
            <p>We hope that you enjoy using your newly installed forums, below are a few little links and tips that you may find useful.</p>
            <h5><b>Useful Tips</b></h5>
              <ul>
                  <li>Remove the 'install' directory, this will stop others from reinstalling and ruining your forums.</li>
                  <li>Remove the 'update.php' file, this may lead to further security risks.</li>
              </ul>
            <h5><b>Useful Links</b></h5>
              <ul>
                  <li><b>LayerBB</b> - <a href="https://www.layerbb.com">https://www.layerbb.com</a></li>
                  <li><b>LayerBB Wiki</b> - <a href="https://github.com/AndyRixon/LayerBB/wiki">https://github.com/AndyRixon/LayerBB/wiki</a></li>
              </ul>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12" style="text-align: right;">
              <a href="../" class="btn btn-primary btn-sm" role="button">Go to the forum</a>
              <a href="../admin" class="btn btn-primary btn-sm" role="button">Go to the AdminCP</a>
            </div>
        </div>
        </div>
    </div>
    </div>
<?php include 'assets/tpl/footer.php'; ?>