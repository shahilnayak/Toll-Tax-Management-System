<?php

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `pass_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="mx-0 py-5 px-3 mx-ns-4 bg-gradient-primary">
	<h3><b><?= isset($id) ? "Update Pass Details" : "Create New Pass" ?></b></h3>
</div>
<style>
	img#cimg{
      max-height: 15em;
      width: 100%;
      object-fit: scale-down;
    }
</style>
<div class="row justify-content-center" style="margin-top:-2em;">
	<div class="col-lg-6 col-md-8 col-sm-11 col-xs-11">
		<div class="card rounded-0 shadow">
			<div class="card-body">
				<div class="container-fluid">
					<div class="container-fluid">
						<form action="" id="pass-form">
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
								<label for="vehicle_name" class="control-label">Vehicle</label>
								<input type="text" class="form-control form-control-sm rounded-0" id="vehicle_name" name="vehicle_name" value="<?= isset($vehicle_name) ? $vehicle_name : '' ?>" required="required">
							</div>
							<div class="form-group mb-3">
								<label for="vehicle_registration" class="control-label">Vehicle Reg. No.</label>
								<input type="text" class="form-control form-control-sm rounded-0" id="vehicle_registration" name="vehicle_registration" value="<?= isset($vehicle_registration) ? $vehicle_registration : '' ?>" required="required">
							</div>
							<div class="form-group mb-3">
								<label for="owner" class="control-label">Owner/Driver Fullname</label>
								<input type="text" class="form-control form-control-sm rounded-0" id="owner" name="owner" value="<?= isset($owner) ? $owner : '' ?>" required="required">
							</div>
							<div class="form-group mb-3">
								<label for="balance" class="control-label">Balance</label>
								<input type="number" step="any" class="form-control form-control-sm rounded-0 text-right" id="balance" name="balance" value="<?= isset($balance) ? $balance : '' ?>" required="required">
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="card-footer py-1 text-center">
				<button class="btn btn-primary btn-sm bg-gradient-primary rounded-0" form="pass-form"><i class="fa fa-save"></i> Save</button>
				<a class="btn btn-light btn-sm bg-gradient-light border rounded-0" href="./?page=passes"><i class="fa fa-angle-left"></i> Cancel</a>
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
		$('#pass-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_pass",
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
						location.href = "./?page=passes/view_pass&id="+resp.rid
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