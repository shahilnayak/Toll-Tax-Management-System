<h1>Welcome to <?php echo $_settings->info('name') ?></h1>
<hr>
<div class="row">
          <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-gradient-navy elevation-1"><i class="fas fa-blog"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Published Posts</span>
                <span class="info-box-number">
                  <?php 
                    $posts = $conn->query("SELECT * FROM post_list where delete_flag = 0 and `status` = 1 and user_id = '{$_settings->userdata('id')}'")->num_rows;
                    echo format_num($posts);
                  ?>
                  <?php ?>
                </span>
              </div>
              <!-- /.info-box-content -->
            </div>
            <!-- /.info-box -->
          </div>
          <!-- /.col -->
          <div class="col-lg-6 col-md-6 col-sm-12 col-xs-12">
            <div class="info-box">
              <span class="info-box-icon bg-gradient-secondary elevation-1"><i class="fas fa-blog"></i></span>
              <div class="info-box-content">
                <span class="info-box-text">Unpublished Posts</span>
                <span class="info-box-number">
                  <?php 
                    $posts = $conn->query("SELECT * FROM post_list where delete_flag = 0 and `status` = 2 and user_id = '{$_settings->userdata('id')}'")->num_rows;
                    echo format_num($posts);
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