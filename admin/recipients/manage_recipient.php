<?php

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `recipient_list` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
}
?>
<div class="mx-0 py-5 px-3 mx-ns-4 bg-gradient-primary">
	<h3><b><?= isset($id) ? "Update Recipient Details" : "Create New Recipient" ?></b></h3>
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
						<form action="" id="recipient-form">
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
							<?php if(is_null($_settings->userdata('toll_id'))): ?>
							<div class="form-group mb-3">
								<label for="toll_id" class="control-label">Toll Gate</label>
								<select class="select2 form-control form-control-sm rounded-0" id="toll_id" name="toll_id" required="required">
									<option <?= !isset($toll_id) ? 'selected' : '' ?> value="" disabled="disabled"></option>
									<?php 
									$qry = $conn->query("SELECT * FROM `toll_list` where delete_flag = 0 and `status` = 1 ".(isset($toll_id) && $toll_id > 0 ? " or id = '{$toll_id}' " : '')." ");
									while($row= $qry->fetch_assoc()):
									?>
									<option value="<?= $row['id'] ?>" <?= isset($toll_id) && $toll_id == $row['id'] ? "selected" : '' ?>><?= $row['name'] ?></option>
									<?php endwhile; ?>
								</select>
							</div>
							<?php else: ?>
							<input type="hidden" name ="toll_id" value="<?php echo $_settings->userdata('toll_id') ?>">
							<?php endif ?>
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
								<label for="cost" class="control-label">Cost</label>
								<input type="number" step="any" class="form-control form-control-sm rounded-0 text-right" id="cost" name="cost" value="<?= isset($cost) ? $cost : '' ?>" required="required">
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="card-footer py-1 text-center">
				<button class="btn btn-primary btn-sm bg-gradient-primary rounded-0" form="recipient-form"><i class="fa fa-save"></i> Save</button>
				<a class="btn btn-light btn-sm bg-gradient-light border rounded-0" href="./?page=recipients"><i class="fa fa-angle-left"></i> Cancel</a>
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
		$('#toll_id.select2').select2({
			placeholder:'Please Select Toll Here',
			containerCssClass:'form-control form-control-sm rounded-0'
		})
		$('#recipient-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_recipient",
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
						location.href = "./?page=recipients/view_recipient&id="+resp.rid
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

<div class="row">
    <div class="col-md-12 text-right">
        <strong>Total Cost: </strong>
        <span id="total-cost">0.00</span>
    </div>
</div>

<script>
    // Function to update the total cost
    function updateTotalCost() {
        var totalCost = 0;
        totalCost = parseFloat($('#cost').val()) || 0;
        
        $('#total-cost').text(totalCost.toFixed(2));
    }

    // Call the function when the page is ready
    $(document).ready(function(){
        updateTotalCost();
    });
</script>
