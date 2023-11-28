<h1>Welcome to <?php echo $_settings->info('name') ?> - Management Site</h1>
<hr>
<style>
  #system-banner{
    width:100%;
    max-height:35em;
    object-fit:cover;
    object-position: center center;
  }
</style>
<?php if($_settings->userdata('type') == 1): ?>
<div class="row">
  <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-primary elevation-1"><i class="fas fa-th-list"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Categories</span>
        <span class="info-box-number">
          <?php 
            $categories = $conn->query("SELECT * FROM category_list where delete_flag = 0")->num_rows;
            echo format_num($categories);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-light elevation-1"><i class="fas fa-road"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Toll Gates</span>
        <span class="info-box-number">
          <?php 
            $tolls = $conn->query("SELECT * FROM toll_list where delete_flag = 0")->num_rows;
            echo format_num($tolls);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-navy elevation-1"><i class="fas fa-credit-card"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Passes</span>
        <span class="info-box-number">
          <?php 
            $passes = $conn->query("SELECT * FROM `pass_list`")->num_rows;
            echo format_num($passes);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-stream"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Passes History Today</span>
        <span class="info-box-number">
          <?php 
            $history = $conn->query("SELECT * FROM pass_history where date(date_created) = '".date('Y-m-d')."'")->num_rows;
            echo format_num($history);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-ticket-alt"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Recipient Today</span>
        <span class="info-box-number">
          <?php 
            $recipient = $conn->query("SELECT * FROM recipient_list where date(date_created) = '".date('Y-m-d')."'")->num_rows;
            echo format_num($recipient);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-dark elevation-1"><i class="fas fa-users"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">System Users</span>
        <span class="info-box-number">
          <?php 
            $user = $conn->query("SELECT * FROM `users` ")->num_rows;
            echo format_num($user);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
</div>
<?php else: ?>
<div class="row">
<div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-navy elevation-1"><i class="fas fa-credit-card"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Passes</span>
        <span class="info-box-number">
          <?php 
            $passes = $conn->query("SELECT * FROM `pass_list`")->num_rows;
            echo format_num($passes);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-stream"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Passes History Today</span>
        <span class="info-box-number">
          <?php 
            $history = $conn->query("SELECT * FROM pass_history where date(date_created) = '".date('Y-m-d')."' and user_id = '{$_settings->userdata('id')}' ")->num_rows;
            echo format_num($history);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
  <div class="col-lg-4 col-md-4 col-sm-12 col-xs-12">
    <div class="info-box">
      <span class="info-box-icon bg-gradient-warning elevation-1"><i class="fas fa-ticket-alt"></i></span>
      <div class="info-box-content">
        <span class="info-box-text">Recipient Today</span>
        <span class="info-box-number">
          <?php 
            $recipient = $conn->query("SELECT * FROM recipient_list where date(date_created) = '".date('Y-m-d')."' and user_id = '{$_settings->userdata('id')}' ")->num_rows;
            echo format_num($recipient);
          ?>
          <?php ?>
        </span>
      </div>
      <!-- /.info-box-content -->
    </div>
    <!-- /.info-box -->
  </div>
  <!-- /.col -->
</div>
<?php endif; ?> 
<hr>
<center>
  <img src="<?= validate_image($_settings->info('cover')) ?>" class="img-fluid img-thumbnail" id="system-banner" alt="<?= $_settings->info('name') ?>">
</center>