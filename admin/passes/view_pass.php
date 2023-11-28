<?php

if(isset($_GET['id']) && $_GET['id'] > 0){
    $qry = $conn->query("SELECT p.*, c.name as category from `pass_list` p inner join category_list c on p.category_id = c.id where p.id = '{$_GET['id']}'");
    if($qry->num_rows > 0){
        foreach($qry->fetch_assoc() as $k => $v){
            $$k=$v;
        }
		if(isset($user_id) && !is_null($user_id)){
			$user = $conn->query("SELECT concat(lastname, ', ', firstname, ' ', coalesce(middlename,'')) as `name` FROM `users` where id = '{$user_id}'");
			if($user->num_rows > 0){
				$user_name = $user->fetch_array()['name'];
			}
		}
		if(isset($id)){
			$hist = $conn->query("SELECT SUM(`cost`) FROM `pass_history` where pass_id = '{$id}' ")->fetch_array()[0];
			$hist = $hist > 0 ? $hist : 0 ;
		}
    }else{
        echo '<script> alert("Pass ID is invalid."); location.replace("./?page=passes");</script>';
    }
}else{
    echo '<script> alert("Pass ID is required."); location.replace("./?page=passes");</script>';
}
?>
<div class="mx-0 py-5 px-3 mx-ns-4 bg-gradient-primary">
	<h3><b>Pass Details</b></h3>
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
				<div class="container-fluid" id="printout">
					<div class="row w-100">
						<div class="col-4 border py-2 m-0 font-weight-bolder text-muted">DateTime Created</div>
						<div class="col-8 border py-2 m-0 font-weight-bolder"><?= isset($date_created) ? date("F d, Y h:i A", strtotime($date_created)) : '' ?></div>
						<div class="col-4 border py-2 m-0 font-weight-bolder text-muted">Pass Code</div>
						<div class="col-8 border py-2 m-0 font-weight-bolder"><?= isset($code) ? $code : '' ?></div>
						<div class="col-4 border py-2 m-0 font-weight-bolder text-muted">Owner/Driver</div>
						<div class="col-8 border py-2 m-0 font-weight-bolder"><?= isset($owner) ? $owner : '' ?></div>
						<div class="col-4 border py-2 m-0 font-weight-bolder text-muted">Vehicle Name</div>
						<div class="col-8 border py-2 m-0 font-weight-bolder"><?= isset($vehicle_name) ? $vehicle_name : '' ?></div>
						<div class="col-4 border py-2 m-0 font-weight-bolder text-muted">Vehicle Category</div>
						<div class="col-8 border py-2 m-0 font-weight-bolder"><?= isset($category) ? $category : '' ?></div>
						<div class="col-4 border py-2 m-0 font-weight-bolder text-muted">Vehicle Reg. No.</div>
						<div class="col-8 border py-2 m-0 font-weight-bolder"><?= isset($vehicle_registration) ? $vehicle_registration : "" ?></div>
						<div class="col-4 border py-2 m-0 font-weight-bolder text-muted">Starting Balance</div>
						<div class="col-8 border py-2 m-0 font-weight-bolder"><?= isset($balance) ? number_format($balance,2) : '0.00' ?></div>
						<div class="col-4 border py-2 m-0 font-weight-bolder text-muted">Current Balance</div>
						<div class="col-8 border py-2 m-0 font-weight-bolder"><?= isset($balance) && isset($hist) ? number_format($balance - $hist,2) : '0.00' ?></div>
						<div class="col-4 border py-2 m-0 font-weight-bolder text-muted">Processed By</div>
						<div class="col-8 border py-2 m-0 font-weight-bolder"><?= isset($user_name) ? ucwords($user_name) : "N/A" ?></div>
					</div>
				</div>
			</div>
			<div class="card-footer py-1 text-center">
				<button class="btn btn-light btn-sm bg-gradient-light border rounded-0" type="button" id="print-data"><i class="fa fa-print"></i> Print</button>
				<a class="btn btn-primary btn-sm bg-gradient-primary rounded-0" href="./?page=passes/manage_pass&id=<?= isset($id) ? $id :'' ?>"><i class="fa fa-edit"></i> Edit</a>
				<button class="btn btn-danger btn-sm bg-gradient-danger rounded-0" type="button" id="delete-data"><i class="fa fa-trash"></i> Delete</button>
				<a class="btn btn-light btn-sm bg-gradient-light border rounded-0" href="./?page=passes"><i class="fa fa-angle-left"></i> Back to List</a>
			</div>
		</div>
	</div>
</div>
<noscript id="print-header">
	<div>
		<div class="d-flex w-100 align-items-center">
			<div class="col-2 text-center">
				<img src="<?= validate_image($_settings->info('logo'))?>" class="img-fluid img-thumbnail rounded-circle" style="height:5em;width:5em;object-fit:cover;object-position:center center" alt="">
			</div>
			<div class="col-8 text-center">
				<div style="line-height:.7em">
					<h4 class="text-center"><?= $_settings->info('name') ?></h4>
					<h3 class="text-center">Toll Pass Details</h3>
				</div>
			</div>
		</div>
		<hr>
	</div>
</noscript>
<script>
	
	$(document).ready(function(){
        $('#delete-data').click(function(){
			_conf("Are you sure to delete this pass permanently?","delete_pass",['<?= isset($id) ? $id : '' ?>'])
		})
		$('#print-data').click(function(){
			var h = $('head').clone()
			var ph = $($('noscript#print-header').html()).clone()
			var p = $('#printout').clone()
			h.find('title').text("pass Details - Print View")
			// console.log(ph[0].outerHTML)
			start_loader()
			var nw = window.open('', '_blank','width=1000,height=800')
				nw.document.querySelector('head').innerHTML = h.html()
				nw.document.querySelector('body').innerHTML = ph[0].outerHTML + p[0].outerHTML
				nw.document.close()
				setTimeout(() => {
					nw.print()
					setTimeout(() => {
						nw.close()
						end_loader()
					}, 300);
				}, (300));
		})
	})
    function delete_pass($id){
		start_loader();
		$.ajax({
			url:_base_url_+"classes/Master.php?f=delete_pass",
			method:"pass",
			data:{id: $id},
			dataType:"json",
			error:err=>{
				console.log(err)
				alert_toast("An error occured.",'error');
				end_loader();
			},
			success:function(resp){
				if(typeof resp== 'object' && resp.status == 'success'){
					location.replace("./?page=passes");
				}else{
					alert_toast("An error occured.",'error');
					end_loader();
				}
			}
		})
	}
</script>