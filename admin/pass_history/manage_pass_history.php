<?php

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT * from `pass_history` where id = '{$_GET['id']}' ");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
    }
	if(isset($pass_id)){
		$pass_qry = $conn->query("SELECT p.*, c.name as `category` FROM `pass_list` p inner join category_list c on p.category_id = c.id where p.id = '{$pass_id}' ");
		if($pass_qry->num_rows > 0){
			foreach($pass_qry->fetch_array() as $k => $v){
				if(!is_numeric($k))
				$pass[$k] = $v;
			}
			if(isset($pass['balance'])){
				$hist = $conn->query("SELECT SUM(`cost`) FROM `pass_history` where pass_id = '{$pass_id}' and id !='{$id}' ")->fetch_array()[0];
				$hist = $hist > 0 ? $hist : 0 ;
				$pass['balance'] = $pass['balance'] - $hist;
			}
		}
	}
}
?>
<div class="mx-0 py-5 px-3 mx-ns-4 bg-gradient-primary">
	<h3><b><?= isset($id) ? "Update Pass History Details" : "Create New Pass History" ?></b></h3>
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
						<form action="" id="pass_history-form">
							<input type="hidden" name ="id" value="<?php echo isset($id) ? $id : '' ?>">
							<?php if(!isset($id)): ?>
							<div class="form-group mb-3">
								<label for="code" class="control-label">Search Pass Code</label>
								<div class="input-group input-group-sm">
									<input type="search" class="form-control form-control-sm rounded-0" id="code" value="<?= isset($code) ? $code : '' ?>" required="required">
									<button class="btn btn-outline-secondary btn-sm rounded-0" type="button" id="search_pass"><i class="fa fa-search"></i></button>
								</div>
							</div>
							<?php endif; ?>
							<div id="pass_details">
								<fieldset>
									<legend>Pass Details</legend>
										<input type="hidden" name="pass_id" value="<?= isset($pass_id) ? $pass_id : '' ?>">
									<dl>
										<dt class="text-muted font-weight-bolder">Pass Code</dt>
										<dd class="pl-4 font-weight-bolder pass_code"><?= isset($pass['code']) ? $pass['code'] : '' ?></dd>
										<dt class="text-muted font-weight-bolder">Owner</dt>
										<dd class="pl-4 font-weight-bolder owner"><?= isset($pass['owner']) ? $pass['owner'] : '' ?></dd>
										<dt class="text-muted font-weight-bolder">Vehicle</dt>
										<dd class="pl-4 font-weight-bolder vehicle"><?= isset($pass['vehicle_name']) ? $pass['vehicle_name'] : '' ?></dd>
										<dt class="text-muted font-weight-bolder">Vehicle Category</dt>
										<dd class="pl-4 font-weight-bolder category"><?= isset($pass['category']) ? $pass['category'] : '' ?></dd>
										<dt class="text-muted font-weight-bolder">Vehicle Reg. No.</dt>
										<dd class="pl-4 font-weight-bolder vehicle_reg_no"><?= isset($pass['vehicle_registration']) ? $pass['vehicle_registration'] : '' ?></dd>
										<dt class="text-muted font-weight-bolder">Balance</dt>
										<dd class="pl-4 font-weight-bolder h2 balance"><?= isset($pass['balance']) ? number_format($pass['balance']) : '0.00' ?></dd>
									</dl>
								</fieldset>
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
								<label for="cost" class="control-label">Cost</label>
								<input type="number" step="any" class="form-control form-control-sm rounded-0 text-right" id="cost" name="cost" value="<?= isset($cost) ? $cost : '' ?>" required="required">
							</div>
						</form>
					</div>
				</div>
			</div>
			<div class="card-footer py-1 text-center">
				<button class="btn btn-primary btn-sm bg-gradient-primary rounded-0" form="pass_history-form"><i class="fa fa-save"></i> Save</button>
				<a class="btn btn-light btn-sm bg-gradient-light border rounded-0" href="./?page=pass_history"><i class="fa fa-angle-left"></i> Cancel</a>
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
		$('#toll_id.select2').select2({
			placeholder:'Please Select Toll Here',
			containerCssClass:'form-control form-control-sm rounded-0'
		})
		$('#code').keypress(function(e){
			if(e.which == 13){
				$('#search_pass').click()
				e.preventDefault()
			}
		})
		$('#search_pass').click(function(){
			var code = $('#code').val()
			var _this = $(this).parent()
			$('.err-msg').remove();
			var el = $('<div>')
			el.addClass("alert alert-danger err-msg my-1 rounded-0")
			el.hide()
			start_loader();
			$.ajax({
				url:_base_url_+'classes/Master.php?f=search_pass_code',
				method:'POST',
				data:{code:code},
				dataType:'json',
				error:err=>{
					console.log(err)
					alert("An error occurred")
					end_loader()
				},
				success:function(resp){
					if(Object.keys(resp).length > 0){
						$('[name="pass_id"]').val(resp.id)
						$('.pass_code').text(resp.code)
						$('.owner').text(resp.owner)
						$('.vehicle').text(resp.vehicle_name)
						$('.category').text(resp.category)
						$('.vehicle_reg_no').text(resp.vehicle_registration)
						$('.balance').text(parseFloat(resp.balance).toLocaleString('en-US'))
					}else{
						el.text('Undefined Code')
						_this.after(el)
						el.show('slow')
					}
					end_loader()
				}
			})
		})
		$('#pass_history-form').submit(function(e){
			e.preventDefault();
            var _this = $(this)
			 $('.err-msg').remove();
			start_loader();
			$.ajax({
				url:_base_url_+"classes/Master.php?f=save_pass_history",
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
						location.href = "./?page=pass_history/view_pass_history&id="+resp.rid
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