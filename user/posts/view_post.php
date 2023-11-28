<?php

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT p.*, c.name as category from `post_list` p inner join category_list c on p.category_id = c.id where p.id = '{$_GET['id']}' and p.delete_flag = 0");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }else{
        echo '<script> alert("Post ID is invalid."); location.replace("./?page=posts");</script>';
    }
}else{
    echo '<script> alert("Post ID is required."); location.replace("./?page=posts");</script>';
}
?>
<div class="mx-0 py-5 px-3 mx-ns-4 bg-gradient-primary">
	<h3><b>Post Details</b></h3>
</div>
<style>
	img#cimg{
      max-height: 15em;
      object-fit: scale-down;
    }
</style>
<div class="row justify-content-center" style="margin-top:-2em;">
	<div class="col-lg-10 col-md-11 col-sm-11 col-xs-11">
		<div class="card rounded-0 shadow">
			<div class="card-body">
				<div class="container-fluid">
					<div class="container-fluid">
                        <div class="text-right">
                            <?php if(isset($status) && $status == 1): ?>
                                <small class="badge badge-light border bg-gradient-light px-2 rounded-pill"><i class="fa fa-circle text-success"></i> Published</small>
                            <?php elseif(isset($status) && $status == 2): ?>
                                <small class="badge badge-light border bg-gradient-light px-2 rounded-pill"><i class="fa fa-circle text-muted"></i> Unpublished</small>
                            <?php endif?>
                        </div>
                        <div class="clear-fix my-2"></div>
						<div class="text-center">
                            <img src="<?php echo validate_image(isset($image_path) ? $image_path : '') ?>" alt="" id="cimg" class="img-fluid img-thumbnail rounded-0 p-0 col-lg-6 col-md-8 col-sm-12 col-xs-12 bg-gradient-dark">
                        </div>
                        <div class="clear-fix my-2"></div>
                        <div style="line-height:.5em">
                            <h4 class="font-weight-bolder mb-0"><?= isset($title) ? $title : '' ?></h4>
                            <div class="pl-2"><small class="text-muted"><i class="fa fa-circle mr-1"></i><?= isset($category) ? $category : '' ?></small></div>
                        </div>
                        <hr class="my-1">
                        <div class="clear-fix my-1"></div>
                        <h4 class="font-weight-bolder mb-1">Description</h4>
                        <div class="pl-2"><?= isset($description) ? $description : '' ?></div>
                        <div class="clear-fix my-1"></div>
                        <h4 class="font-weight-bolder mb-1">Location</h4>
                        <div class="pl-2" ><?= isset($location) ? $location : '' ?></div>
                        <div class="clear-fix my-1"></div>
                        <h4 class="font-weight-bolder mb-1">Available Slots</h4>
                        <div class="pl-2"><?= isset($slots) ? number_format($slots) : '' ?></div>
					</div>
				</div>
			</div>
			<div class="card-footer py-1 text-center">
				<a class="btn btn-primary btn-sm bg-gradient-primary rounded-0" href="./?page=posts/manage_post&id=<?= isset($id) ? $id :'' ?>"><i class="fa fa-edit"></i> Edit</a>
				<button class="btn btn-danger btn-sm bg-gradient-danger rounded-0" type="button" id="delete-data"><i class="fa fa-trash"></i> Delete</button>
				<a class="btn btn-light btn-sm bg-gradient-light border rounded-0" href="./?page=posts"><i class="fa fa-angle-left"></i> Back to List</a>
			</div>
		</div>
	</div>
</div>
<script>
	
	$(document).ready(function(){
        $('#delete-data').click(function(){
			_conf("Are you sure to delete this Post permanently?","delete_post",['<?= isset($id) ? $id : '' ?>'])
		})
	})
    function delete_post($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_post",
			method:"POST",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.replace("./?page=posts");
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>