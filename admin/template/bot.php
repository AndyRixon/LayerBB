<?php
//===================================//
// LayerBB Project                   //
//-----------------------------------//
// Website: https://www.layerbb.com  //
// Email: info@layerbb.com           //
// Build Series: 1.1                 //
//===================================//

  global $ADMIN, $LAYER;
?>
<div style="clear: both;"></div>
</section>
 </div>
  <!-- /.content-wrapper -->
  <footer class="main-footer">
    <div class="pull-right hidden-xs">
      <b>Version</b> <?php echo LayerBB_VERSION; ?>
    </div>
    <strong>Copyright &copy; 2019 <a href="https://www.layerbb.com">LayerBB</a>.</strong> All rights
    reserved.
  </footer>
 </div>
<!-- ./wrapper -->

<!-- jQuery 3 -->
<script src="<?php echo SITE_URL; ?>/public/admin/bower_components/jquery/dist/jquery.min.js"></script>
<!-- jQuery UI 1.11.4 -->
<script src="<?php echo SITE_URL; ?>/public/admin/bower_components/jquery-ui/jquery-ui.min.js"></script>
<!-- Resolve conflict in jQuery UI tooltip with Bootstrap tooltip -->
<script>
  $.widget.bridge('uibutton', $.ui.button);
</script>
<!-- Bootstrap 3.3.7 -->
<script src="<?php echo SITE_URL; ?>/public/admin/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- Morris.js charts -->
<script src="<?php echo SITE_URL; ?>/public/admin/bower_components/raphael/raphael.min.js"></script>
<script src="<?php echo SITE_URL; ?>/public/admin/bower_components/morris.js/morris.min.js"></script>
<!-- Sparkline -->
<script src=<?php echo SITE_URL; ?>/public/admin/"bower_components/jquery-sparkline/dist/jquery.sparkline.min.js"></script>
<!-- jvectormap -->
<script src="<?php echo SITE_URL; ?>/public/admin/plugins/jvectormap/jquery-jvectormap-1.2.2.min.js"></script>
<script src="<?php echo SITE_URL; ?>/public/admin/plugins/jvectormap/jquery-jvectormap-world-mill-en.js"></script>
<!-- jQuery Knob Chart -->
<script src="<?php echo SITE_URL; ?>/public/admin/bower_components/jquery-knob/dist/jquery.knob.min.js"></script>
<!-- daterangepicker -->
<script src="<?php echo SITE_URL; ?>/public/admin/bower_components/moment/min/moment.min.js"></script>
<script src="<?php echo SITE_URL; ?>/public/admin/bower_components/bootstrap-daterangepicker/daterangepicker.js"></script>
<!-- datepicker -->
<script src="<?php echo SITE_URL; ?>/public/admin/bower_components/bootstrap-datepicker/dist/js/bootstrap-datepicker.min.js"></script>
<!-- Bootstrap WYSIHTML5 -->
<script src="<?php echo SITE_URL; ?>/public/admin/plugins/bootstrap-wysihtml5/bootstrap3-wysihtml5.all.min.js"></script>
<!-- Slimscroll -->
<script src="<?php echo SITE_URL; ?>/public/admin/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="<?php echo SITE_URL; ?>/public/admin/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="<?php echo SITE_URL; ?>/public/admin/dist/js/adminlte.min.js"></script>
<!-- AdminLTE dashboard demo (This is only for demo purposes) -->
<script src="<?php echo SITE_URL; ?>/public/admin/dist/js/pages/dashboard.js"></script>
<!-- AdminLTE for demo purposes -->
<script src="<?php echo SITE_URL; ?>/public/admin/dist/js/demo.js"></script>
</body>
</html>