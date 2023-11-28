<?php

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `post_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="mx-0 py-5 px-3 mx-ns-4 bg-gradient-primary">
	<h3><b><?= isset($id) ? "Update Post Details" : "Create New Post" ?></b></h3>
</div>
<style>
	img#cimg{
      max-height: 15em;
      width: 100%;
      object-fit: scale-down;
    }
</style>
<div class="row justify-content-center" style="margin-top:-2em;">
	<div class="col-lg-10 col-md-11 col-sm-11 col-xs-11">
		<div class="card rounded-0 shadow">
			<div class="card-body">
				<div class="container-fluid">
					<div class="container-fluid">
						<form action="" id="post-form">
							<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
							<div class="form-group mb-3">
								<label for="category_id" class="control-label">Category</label>
								<select class="form-control form-control-sm rounded-0" id="category_id" name="category_id" required="required">
									<option <?= !isset($category_id) ? 'selected' : '' ?> value="" disabled="disabled"></option>
									<?php 
									$qry = $conn->query("SELECT * FROM `category_list` where delete_flag = 0 and `status` = 1 ".(isset($category_id) && $category_id > 0 ? " or id = '{$category_id}' " : '')." ");
									while($row= $qry->fetch_assoc()):
									?>
									<option value="<?= $row['id'] ?>" <?= isset($category_id) && $category_id == $row['id'] ? "selected" : '' ?>><?= $row['name'] ?></option>
									<?php endwhile; ?>
								</select>
							</div>
							<div class="form-group mb-3">
								<label for="title" class="control-label">Title</label>
								<input type="text" class="form-control form-control-sm rounded-0" id="title" name="title" value="<?= isset($title) ? $title : '' ?>" required="required">
							</div>
							<div class="form-group mb-3">
								<label for="description" class="control-label">Description</label>
								<textarea rows="3" class="form-control form-control-sm rounded-0" id="description" name="description" required="required"><?= isset($description) ? $description : '' ?></textarea>
							</div>
							<div class="form-group mb-3">
								<label for="location" class="control-label">Location</label>
								<textarea rows="3" class="form-control form-control-sm rounded-0" id="location" name="location" required="required"><?= isset($location) ? $location : '' ?></textarea>
							</div>
							<div class="form-group p-0 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
								<label for="slots" class="control-label">Available Slots</label>
								<input type="number" min="0" class="form-control form-control-sm rounded-0 text-right" id="slots" name="slots" value="<?= isset($slots) ? $slots : 0 ?>" required="required">
							</div>
							<div class="form-group p-0 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
								<label for="status" class="control-label">Status</label>
								<select name="status" id="status" class="form-control form-control-sm rounded-0" required="required">
									<option value="1" <?= isset($status) && $status == 1 ? 'selected' : '' ?>>Published</option>
									<option value="2" <?= isset($status) && $status == 2 ? 'selected' : '' ?>>Unpublished</option>
								</select>
							</div>
							<div class="form-group p-0 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
								<label for="customFile" class="control-label">Image Thumbnail</label>
								<div class="custom-file custom-file-sm">
									<input type="file" class="custom-file-input rounded-0" id="customFile" name="img" onchange="displayImg(this,$(this))" accept="image/png, image/jpeg">
									<label class="custom-file-label rounded-0" for="customFile">Choose file</label>
								</div>
							</div>
							<div class="form-group p-0 col-lg-6 col-md-6 col-sm-12 col-xs-12 mb-3">
								<img src="<?php echo validate_image(isset($image_path) ? $image_path : '') ?>" alt="" id="cimg" class="img-fluid img-thumbnail rounded-0">
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="card-footer py-1 text-center">
				<button class="btn btn-primary btn-sm bg-gradient-primary rounded-0" form="post-form"><i class="fa fa-save"></i> Save</button>
				<a class="btn btn-light btn-sm bg-gradient-light border rounded-0" href="./?page=posts"><i class="fa fa-angle-left"></i> Cancel</a>
			</div>
		</div>
	</div>
</div>
<script>
	function displayImg(input,_this) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$('#cimg').attr('src', e.target.result);
				_this.siblings('label').text(input.files[0].name)
			}

			reader.readAsDataURL(input.files[0]);
		}else{
			$('#cimg').attr('src', "<?php echo validate_image(isset($image_path) ? $image_path : '') ?>");
			_this.siblings('label').text('Choose File')
		}
	}
	$(document).ready(function(){
		$('#category_id').select2({
			placeholder:'Please Select Category Here',
			containerCssClass:'form-control form-control-sm rounded-0'
		})
		$('#description').summernote({
			height: "35vh",
			toolbar: [
				[ 'style', [ 'style' ] ],
				[ 'font', [ 'bold', 'italic', 'underline', 'strikethrough', 'superscript', 'subscript', 'clear'] ],
				[ 'fontname', [ 'fontname' ] ],
				[ 'fontsize', [ 'fontsize' ] ],
				[ 'color', [ 'color' ] ],
				[ 'para', [ 'ol', 'ul', 'paragraph', 'height' ] ],
				[ 'table', [ 'table' ] ],
				[ 'view', [ 'undo', 'redo', 'fullscreen', 'codeview', 'help' ] ]
			]
		})
		$('#post-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_post",
				data: new FormData($(this)[0]),
                cache: false,
                contentType: false,
                processData: false,
                method: 'POST',
                type: 'POST',
                dataType: 'json',
				error:err=>{
					console.log(err)
					alert_toast("An error occured",'error');
					end_loader();
				},
				success:function(resp){
					if(typeof resp =='object' && resp.status == 'success'){
						location.href = "./?page=posts/view_post&id="+resp.pid
					}else if(resp.status == 'failed' && !!resp.msg){
                        var el = $('<div>')
                            el.addClass("alert alert-danger err-msg").text(resp.msg)
                            _this.prepend(el)
                            el.show('slow')
                            $("html, body, .modal").scrollTop(0)
                            end_loader()
                    }else{
						alert_toast("An error occured",'error');
						end_loader();
                        console.log(resp)
					}
				}
			})
		})

	})
</script>